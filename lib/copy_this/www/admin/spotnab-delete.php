<?php
require_once("config.php");

$page = new AdminPage();

if (isset($_GET['id']))
{
	$spotnab = new SpotNab();
	$spotnab->deleteSource($_GET['id']);
}

$referrer = $_SERVER['HTTP_REFERER'];
header("Location: " . $referrer);

