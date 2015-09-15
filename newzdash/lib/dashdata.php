<?php
use newznab\db\Settings;

class DashData
{

	public function time_elapsed($secs)
	{
		if ($secs == 0) {
			return "now";
		}

		$bit = array(
			'y' => $secs / 31556926 % 12,
			'w' => $secs / 604800 % 52,
			'd' => $secs / 86400 % 7,
			'h' => $secs / 3600 % 24,
			'm' => $secs / 60 % 60,
			's' => $secs % 60
		);

		foreach ($bit as $k => $v) {
			if ($v > 0) {
				$ret[] = $v . $k;
			}
		}

		$strtext = join(' ', $ret) . " ago";

		return $strtext;
	}

	/**
	 * getLastGroupUpdate
	 */
	public function getLastGroupUpdate()
	{
		$sql = sprintf("select name,last_updated,NOW(),unix_timestamp(NOW())-unix_timestamp(last_updated) as age from groups order by last_updated desc limit 0,5");
		$pdo = new Settings();
		$data = $pdo->queryOneRow($sql);
		$age_of_package = DashData::time_elapsed($data['age']);


		printf('<span class="icon32 icon-blue icon-clock"></span>
			<div>Last Group Update</div>
			<div>%s</div>', $age_of_package
		);
	}

	/**
	 * getLastBinaryAdded
	 */
	public function getLastBinaryAdded()
	{
		/*
		$sql=sprintf("select relname,dateadded from binaries order by dateadded desc limit 0,5");
		$pdo = new Settings();
		$data = $pdo->queryOneRow($sql);
		*/
		$sql = sprintf("select relname,dateadded,NOW(),unix_timestamp(NOW())-unix_timestamp(dateadded) as age from binaries order by dateadded desc limit 0,5");
		$pdo = new Settings();
		$data = $pdo->queryOneRow($sql);
		$age_of_package = DashData::time_elapsed($data['age']);
		printf('<span class="icon32 icon-blue icon-clock"></span>
			<div>Last Binary Added</div>
			<div>%s</div>', $age_of_package
		);
	}

	/**
	 * getLastReleaseCreated
	 */
	public function getLastReleaseCreated()
	{
		$sql = sprintf("select name,adddate,unix_timestamp(NOW())-unix_timestamp(adddate) as age from releases order by adddate desc limit 0,5");
		$pdo = new Settings();
		$data = $pdo->queryOneRow($sql);
		$age_of_package = DashData::time_elapsed($data['age']);

		printf('<span class="icon32 icon-blue icon-clock"></span>
			<div>Last Release Created</div>
			<div>%s</div>', $age_of_package
		);
	}


	/**
	 * getRegexInfo
	 */
	public function getNewzDashInfo()
	{

		if (file_exists('.git/HEAD')) {
			$stringfromfile = file('.git/HEAD', FILE_USE_INCLUDE_PATH);

			$stringfromfile = $stringfromfile[0]; //get the string from the array

			$explodedstring = explode("/", $stringfromfile); //seperate out by the "/" in the string

			$branchname = $explodedstring[2]; //get the one that is always the branch name
			$branchname = trim($branchname);

			if (file_exists(".git/refs/heads/" . $branchname)) {
				$gitversion = file_get_contents(".git/refs/heads/" . $branchname);
			} else {
				$gitversion = "unknown";
			}

			printf('<span class="icon32 icon-blue icon-gear"></span>
			    <div>NewzDash Branch: %s</div>
			    <div>Revision: %s</div>', $branchname, substr($gitversion, 0, 10) . "..."
			);
		} else {
			printf('<span class="icon32 icon-blue icon-gear"></span>
			    <div>NewzDash Branch: %s</div>
			    <div>Revision: %s</div>', "unknown", "unknown"
			);

		}
	}

	/**
	 * getDatabaseInfo
	 */
	public function getDatabaseAndRegexInfo()
	{
		$sql = sprintf("select * from settings where setting = 'sqlpatch'");
		$pdo = new Settings();
		$data = $pdo->queryOneRow($sql);
		// $version = $data['value'];
		// now, we want just the numbers as the version is stored as '#Rev: number $'
		preg_match('/[0-9]+/', $data['value'], $version);

		$sql = sprintf("SELECT * FROM settings WHERE setting = 'latestregexrevision'");
		$pdo = new Settings();
		$data = $pdo->queryOneRow($sql);

		printf('<span class="icon32 icon-blue icon-gear"></span>
			<div>Database Version: %s</div>
			<div>Regex Version: %s</div>', $version[0], $data['value']
		);


	}

	/**
	 * getSubversionInfo
	 */
	public function getSubversionLatestFromRss()
	{

		# cache the rss feed so we don't hammer the server
		# and a slight delay in knowing a new release is available is not that bad
		if (xcache_isset("newznabrss")) {
			$xml_source = xcache_get("newznabrss");
		} else {
			$xml_source = file_get_contents('http://newznab.com/plussvnrss.xml');
			# store it for 15 minutes
			xcache_set("newznabrss", $xml_source, 60 * 15);
		}

		$x = simplexml_load_string($xml_source);

		if (count($x) == 0)
			return "";

		$rev = $x->channel->item[0]->title;
		preg_match('/[0-9]+/', $rev, $latest);

		return $latest[0];

	}

	/**
	 * getSubversionInfo
	 */
	public function getSubversionInfo()
	{

		if (extension_loaded('svn')) {
			#svn_auth_set_parameter( SVN_AUTH_PARAM_DEFAULT_USERNAME, SVN_USERNAME );
			#svn_auth_set_parameter( SVN_AUTH_PARAM_DEFAULT_PASSWORD, SVN_PASSWORD );
			$svn_stat = svn_status(realpath(NEWZNAB_HOME), SVN_NON_RECURSIVE | SVN_ALL);
			$current_version = sprintf("%s", $svn_stat[0]["revision"]);


			#$svn_info=svn_info(realpath(NEWZNAB_HOME), SVN_SHOW_UPDATES);
			#$latest_version=sprintf("%s", $svn_info[0]["last_changed_rev"]);
			$latest_version = DashData::getSubversionLatestFromRss();


			if ($current_version === $latest_version) {
				$version_string = sprintf("Running latest version (%s)", $current_version);
				$notification_string = "";
			} else {
				$version_string = sprintf("Running %s, Latest available is %s", $current_version, $latest_version);
				$updates_available = intval($latest_version) - intval($current_version);
				# $notification_string=sprintf('<span class="notification red">%d</span>', $updates_available);
				$notification_string = sprintf('<span class="notification red">!</span>');
			}

			printf('<span class="icon32 icon-blue icon-gear"></span>
			    <div>Newznab-tmux SVN Revision</div>
			    <div>%s</div>
			    %s', $version_string, $notification_string
			);
		} else {
			printf('<span class="icon32 icon-blue icon-gear"></span>
			    <div>Newznab-tmux SVN Revision</div>
			    <div>%s</div>', "php subversion module is not installed"
			);
		}
	}


	/**
	 * count of releases
	 */
	public function getReleaseCount()
	{
		$r = new Releases;
		$total_releases = $r->getCount();

		printf('<span class="icon32 icon-blue icon-star-on"></span>
			<div>Total Releases</div>
			<div>%s</div>', $total_releases
		);
	}

	public function getActiveGroupCount()
	{
		$g = new Groups;
		$active_groups = $g->getCount("", true);

		printf('<span class="icon32 icon-blue icon-comment"></span>
			<div>Active Groups</div>
			<div>%s</div>', $active_groups
		);
	}

	public function getPendingProcessingCount()
	{
		$pdo = new Settings();

		$sql_query = sprintf('select count(*) as todo from releases where (bookinfoid IS NULL and categoryid IN (7010, 7020, 7040, 7060)) OR
		(consoleinfoid IS NULL and categoryid in ( select id from category where parentid = 1000 )) OR
		(imdbid IS NULL and categoryid in ( select id from category where parentid = 2000 )) OR
		(musicinfoid IS NULL and categoryid in ( select id from category where parentid = 3000 )) OR
		(rageid = -1 and categoryid in ( select id from category where parentid = 5000 )) OR
		(xxxinfo_id = 0 and categoryid in (select id from category where parentid = 6000)) OR
		(gamesinfo_id = 0 and categoryid = 4050)');

		$data = $pdo->query($sql_query);
		$total = $data[0]['todo'];

		printf('<span class="icon32 icon-blue icon-star-off"></span>
			<div>Pending Processing</div>
			<div>%s</div>', $total
		);
	}
}