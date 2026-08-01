---
description: Run the full quality suite and fix everything that fails
allowed-tools: Bash, Read, Edit, Grep, Glob
---

Run `composer test-all` (php-cs-fixer dry-run, Psalm, PHPStan, PHPUnit, Rector dry-run).

If anything fails:

1. Fix the root cause in the source, not by widening ignores in `phpstan.neon` / `psalm.xml`.
2. Auto-fixable style/Rector diffs: run `composer fix`, then review the diff.
3. PHPStan OOM inside `resultCache.php` = stale cache -> `vendor/bin/phpstan clear-result-cache`.
4. Re-run `composer test-all` until green.

Report: what failed, what you changed, final suite status. Do not commit unless asked.

$ARGUMENTS
