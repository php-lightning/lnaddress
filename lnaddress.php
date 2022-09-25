<?php
## Written by Benjamin Phạm-Bachelart
## Feel free to copy, change, redistribute
## CC License BY-NC-SA
header("Content-Type: application/json");

## Todo
# better error handling
# support other backends

$backend = 'lnbits';    // lnbits is the only supported backend for now
$backend_options = array();
$backend_options['lnbits'] = [
        'api_endpoint' => 'http://localhost:5000',  // lnbits endpoint : protocol://host:port
        'api_key' => ''                             // put your lnbits read key here
];


// automatically define the ln address based on filename & host, this shouldn't be changed
$username = str_replace('.php', '', basename(__FILE__));
$ln_address = $username.'@'.$_SERVER['HTTP_HOST'];

// Modify the description if you want to custom it
$description = 'Pay to '.$ln_address;

// min & max amount, in msat (sat/1000)
$minSendable = 100000; // default min sendable : 100 sats minimum
$maxSendable = 10000000000; // default max sendable : 10 000 000 sats max

// Modify the following line with the picture you want to use, if you don't want to show a picture, leave an empty string
$image_file = '';

// From this line, except if you know what you're doing, you don't need to change anything.

// comment feature not yet implemented
$allow_comment = false;
$max_comment_length = 0;

function requestinvoice($backend='lnbits', $backend_options, $amount, $metadata, $lnaddr, $comment_allowed=false, $comment=NULL) {
        if($backend == 'lnbits') {
                $http_method = 'POST';
                $api_route = '/api/v1/payments';

                $http_body = array();
                $http_body['out'] = false;
                $http_body['amount'] = $amount;
                $http_body['unhashed_description'] = bin2hex($metadata);
                //$http_body['description_hash'] = hash('sha256', $metadata);

                $http_req = [
                        'http' => [
                                'method' => 'POST',
                                'header' => "Content-Length: ".strlen(json_encode($http_body))."\r\nContent-Type: application/json\r\nX-Api-Key: ".$backend_options["api_key"]."\r\n",
                                'content' => json_encode($http_body)
                        ]
                ];

                $req_context = stream_context_create($http_req);
                $req_result = file_get_contents($backend_options['api_endpoint'].$api_route, false, $req_context);

                $json_response = json_decode($req_result);

                if($req_result === false) {
                        return(json_encode(['status' => 'ERROR', 'reason' => 'Backend is unreachable']));
                }
                else {
                        return(json_encode(['status' => 'OK', 'pr' => $json_response->payment_request]));
                }
                // backend handled
        }
}

$metadata = '[["text/plain","'.$description.'"],["text/identifier","'.$ln_address.'"]';

if(!empty($image_file)) {
        $metadata .= ',["image/jpeg;base64","'.base64_encode(file_get_contents($image_file)).'"]';
}
$metadata .= ']';

$data = [
        "callback" => 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
        "maxSendable" => $maxSendable,
        "minSendable" => $minSendable,
        "metadata" => $metadata,
        "tag" => 'payRequest',
        "commentAllowed" => $allow_comment?$max_comment_length:0
];

if(!$_GET['amount']) {
        print(json_encode($data, JSON_UNESCAPED_SLASHES));
} else {
        $amount = filter_var($_GET['amount'], FILTER_VALIDATE_INT);
        if($amount < $minSendable || $amount > $maxSendable) {
                $resp_payload = array();
                $resp_payload['status'] = 'ERROR';
                $resp_payload['reason'] = 'Amount is not between minimum and maximum sendable amount';
        } else {
                $resp_payload = array();
                $backend_data = json_decode(requestinvoice($backend='lnbits', $backend_options['lnbits'], $amount/1000, $metadata, $ln_address));
                if($backend_data->status == 'OK') {
                        $resp_payload['pr'] = $backend_data->pr;
                        $resp_payload['status'] = 'OK';
                        $resp_payload['successAction'] = ['tag' => 'message', 'message' => 'Payment received!'];
                        $resp_payload['routes'] = array();
                        $resp_payload['disposable'] = false;
                } else {
                        $resp_payload['status'] = $backend_data->status;
                        $resp_payload['reason'] = $backend_data->reason;
                }
        }
        print(json_encode($resp_payload));
}

?>