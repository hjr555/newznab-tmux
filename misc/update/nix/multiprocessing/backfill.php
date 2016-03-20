<?php
declare(ticks=1);
require('.do_not_run/require.php');
use newznab\libraries\Forking;
// Check if argument 1 is numeric, which is to limit article count.
(new Forking())->processWorkType(
	'backfill', (isset($argv[1]) && is_numeric($argv[1]) && $argv[1] > 0 ? array(0 => $argv[1]) : array(0 => false))
);