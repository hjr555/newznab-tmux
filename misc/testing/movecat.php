<?php

//This script moves releases from one category to another

require_once realpath(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'indexer.php');

use newznab\db\Settings;
use newznab\Releases;

$releases = new Releases();
$db = new Settings();

//
// [1] move mp3 files from other misc into audio mp3
//
/*
$sql = "update releases inner join (
select distinct rf.releaseID
from release_files rf
inner join releases r on r.ID = rf.releaseID
where rf.name like '%.mp3'
and r.categoryID like '7%') x on x.releaseID = releases.ID
set releases.categoryID = 3010;";
*/



$db->exec($sql);
