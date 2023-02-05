<?php

declare(strict_types=1);

namespace PhpLightning;

final class LnAddress
{
    private const DEFAULT_BACKEND = 'lnbits';

    /** @var int 100 Minimum in msat (sat/1000) */
    private const DEFAULT_MIN_SENDABLE = 100_000;

    /** @var int 10 000 000 Max in msat (sat/1000) */
    private const DEFAULT_MAX_SENDABLE = 10_000_000_000;

    private HttpApiInterface $httpApi;
    private ConfigInterface $config;

    public function __construct(HttpApiInterface $httpApi, ConfigInterface $config)
    {
        $this->httpApi = $httpApi;
        $this->config = $config;
    }

    /**
     * @param array{
     *   lnbits: array{
     *     api_endpoint: string,
     *     api_key?: string,
     *   }
     * } $backendOptions
     */
    public function generateInvoice(int $amount, string $backend = self::DEFAULT_BACKEND, array $backendOptions = []): void
    {
// automatically define the ln address based on filename & host, this shouldn't be changed
        $username = str_replace('.php', '', basename(__FILE__));
        $ln_address = $username . '@' . $this->config->getHttpHost();

// Modify the description if you want to custom it
// This will be the description on the wallet that pays your ln address
        $description = 'Pay to ' . $ln_address;

// Success payment message, this is the confirmation message that the person who paid will see once your ln address has received sats
        $success_msg = 'Payment received!';

        $minSendable = self::DEFAULT_MIN_SENDABLE;
        $maxSendable = self::DEFAULT_MAX_SENDABLE;

// Modify the following line with the path to the picture you want to display, if you don't want to show a picture, leave an empty string
// Beware that a heavy picture will make the wallet fails to execute lightning address process! 136536 bytes maximum for base64 encoded picture data
        $image_file = '';

// From this line, except if you know what you're doing, you don't need to change anything.

// Comment feature not yet implemented, future use
        $allow_comment = false;
        $max_comment_length = 0;

// requestinvoice($backend, $backend_options, $amount, $metadata, $lnaddr, $comment_allowed, $comment)
// This function handles flows with the backend

        if (!empty($image_file)) {
            $img_metadata = ',["image/jpeg;base64","' . base64_encode($this->httpApi->get($image_file)) . '"]';
        } else {
            $img_metadata = '';
        }

        $metadata = '[["text/plain","' . $description . '"],["text/identifier","' . $ln_address . '"]' . $img_metadata . ']';

// payRequest json data, spec : https://github.com/lnurl/luds/blob/luds/06.md
        $data = [
            "callback" => 'https://' . $this->config->getHttpHost() . $this->config->getRequestUri(),
            "maxSendable" => $maxSendable,
            "minSendable" => $minSendable,
            "metadata" => $metadata,
            "tag" => 'payRequest',
            "commentAllowed" => $allow_comment ? $max_comment_length : 0
        ];

        if ($amount === 0) {
            print(json_encode($data, JSON_UNESCAPED_SLASHES));
        } else {
            if ($amount < $minSendable || $amount > $maxSendable) {
                $resp_payload = [];
                $resp_payload['status'] = 'ERROR';
                $resp_payload['reason'] = 'Amount is not between minimum and maximum sendable amount';
            } else {
                $resp_payload = [];
                $backend_data = json_decode(
                    $this->requestinvoice(
                        $backend,
                        $backendOptions[$backend] ?? [],
                        $amount / 1000,
                        $metadata,
                        $ln_address
                    ),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                );
                if ($backend_data['status'] === 'OK') {
                    $resp_payload['pr'] = $backend_data['pr'];
                    $resp_payload['status'] = 'OK';
                    $resp_payload['successAction'] = ['tag' => 'message', 'message' => $success_msg];
                    $resp_payload['routes'] = [];
                    $resp_payload['disposable'] = false;
                } else {
                    $resp_payload['status'] = $backend_data['status'];
                    $resp_payload['reason'] = $backend_data['reason'];
                }
            }
            print(json_encode($resp_payload));
        }
    }

    private function requestinvoice(
        $backend,
        $backend_options,
        $amount,
        $metadata,
        $lnaddr,
        $comment_allowed = false,
        $comment = null
    ) {
        if ($backend === self::DEFAULT_BACKEND) {
            $http_method = 'POST';
            $api_route = '/api/v1/payments';

            $http_body = [];
            $http_body['out'] = false;
            $http_body['amount'] = $amount;
            $http_body['unhashed_description'] = bin2hex($metadata);
            //$http_body['description_hash'] = hash('sha256', $metadata);

            $http_req = [
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Length: " . strlen(json_encode($http_body))
                        . "\r\nContent-Type: application/json\r\nX-Api-Key: " . $backend_options["api_key"] . "\r\n",
                    'content' => json_encode($http_body)
                ]
            ];

            $req_context = stream_context_create($http_req);
            $req_result = $this->httpApi->get($backend_options['api_endpoint'] . $api_route, $req_context);

            if ($req_result === null) {
                return (json_encode(['status' => 'ERROR', 'reason' => 'Backend is unreachable']));
            } else {
                $json_response = json_decode($req_result, true, 512, JSON_THROW_ON_ERROR);
                return (json_encode(['status' => 'OK', 'pr' => $json_response['payment_request']]));
            }
        }
        // backend handled
        return (json_encode(['status' => 'ERROR', 'reason' => 'Backend is unreachable']));
    }
}