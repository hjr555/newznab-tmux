<?php

if (!$page->users->isLoggedIn())
	$page->show403();

$dashdata = new DashData;





$dashdata->getReleaseCount();
$dashdata->getActiveGroupCount();
$dashdata->getPendingProcessingCount();
$dashdata->getLastGroupUpdate();
$dashdata->getLastReleaseCreated();
$dashdata->getDatabaseInfo();
$dashdata->getNewestRelease();
$dashdata->getGitInfo();