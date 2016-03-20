<?php
require_once realpath(dirname(dirname(dirname(dirname(dirname(__DIR__))))) . DIRECTORY_SEPARATOR . 'indexer.php');

use newznab\processing\PostProcess;
use newznab\ColorCLI;
use newznab\NNTP;
use newznab\Tmux;


$c = new ColorCLI();
if (!isset($argv[1])) {
	exit($c->error("This script is not intended to be run manually, it is called from postprocess_threaded.py."));
}


$tmux = new Tmux;
$torun = $tmux->get()->post;

$pieces = explode('           =+=            ', $argv[1]);

$postprocess = new PostProcess(['Echo' => true]);
if (isset($pieces[6])) {
	// Create the connection here and pass
	$nntp = new NNTP();
	if  ($nntp->doConnect() === false) {
		exit($c->error("Unable to connect to usenet."));
	}

	$postprocess->processAdditional($nntp, $argv[1]);
	$nntp->doQuit();
} else if (isset($pieces[3])) {
	// Create the connection here and pass
	$nntp = new NNTP();
	if ($nntp->doConnect() === false) {
		exit($c->error("Unable to connect to usenet."));
	}

	$postprocess->processNfos($argv[1], $nntp);
	$nntp->doQuit();

} else if (isset($pieces[2])) {
	$postprocess->processMovies($argv[1]);
	echo '.';
} else if (isset($pieces[1])) {
	$postprocess->processTv($argv[1]);
}
