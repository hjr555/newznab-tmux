<?php
require_once(WWW_DIR."lib/framework/db.php");
require_once(WWW_DIR."lib/groups.php");
require_once(WWW_DIR."lib/nfo.php");
require_once(WWW_DIR."lib/site.php");
require_once(WWW_DIR . "/lib/ConsoleTools.php");
require_once(WWW_DIR . "/lib/ColorCLI.php");
require_once("nzbcontents.php");
require_once("simple_html_dom.php");
require_once("IRCScraper.php");
require_once("Info.php");
require_once("namefixer.php");

/**
 * Class for inserting names/categories etc from PreDB sources into the DB,
 * also for matching names on files / subjects.
 *
 * Class PreDb
 */
Class PreHash
{
	// Nuke status.
	const PRE_NONUKE  = 0; // Pre is not nuked.
	const PRE_UNNUKED = 1; // Pre was un nuked.
	const PRE_NUKED   = 2; // Pre is nuked.
	const PRE_MODNUKE = 3; // Nuke reason was modified.
	const PRE_RENUKED = 4; // Pre was re nuked.
	const PRE_OLDNUKE = 5; // Pre is nuked for being old.

	/**
	 * @var bool|stdClass
	 */
	protected $site;

	/**
	 * @var bool
	 */
	protected $echooutput;

	/**
	 * @var DB
	 */
	protected $pdo;

	private $dateLimit;

	/**
	 * @param array $options
	 */
	public function __construct(array $options = array())
	{
		$defaults = [
			'Echo'     => false,
			'Settings' => null,
		];
		$options += $defaults;

		$this->echooutput = ($options['Echo'] && NN_ECHOCLI);
		$this->pdo = ($options['Settings'] instanceof DB ? $options['Settings'] : new DB());
	}

	/**
	 * Attempts to match PreDB titles to releases.
	 *
	 * @param $dateLimit
	 */
	public function checkPre($dateLimit = false)
	{
		$this->dateLimit = $dateLimit;

		$consoleTools = new ConsoleTools(['ColorCLI' => $this->pdo->log]);
		$updated = 0;
		$datesql = '';

		if ($this->echooutput) {
			echo $this->pdo->log->header('Querying DB for release search names not matched with PreDB titles.');
		}

		if ($this->dateLimit !== false && is_numeric($this->dateLimit)) {
			$datesql = sprintf('AND DATEDIFF(NOW(), adddate) <= %d', $this->dateLimit);
		}

		$res = $this->pdo->queryDirect(
			sprintf('
							SELECT p.ID AS prehashid, r.ID AS releaseid
							FROM prehash p
							INNER JOIN releases r ON p.title = r.searchname
							WHERE r.prehashid < 1 %s',
				$datesql
			)
		);

		if ($res !== false) {
			$total = $res->rowCount();
			echo $this->pdo->log->primary(number_format($total) . ' releases to match.');

			if ($res instanceof Traversable) {
				foreach ($res as $row) {
					$this->pdo->queryExec(
						sprintf('UPDATE releases SET prehashid = %d WHERE ID = %d', $row['prehashid'], $row['releaseid'])
					);

					if ($this->echooutput) {
						$consoleTools->overWritePrimary(
							'Matching up preDB titles with release searchnames: ' . $consoleTools->percentString( ++$updated, $total)
						);
					}
				}
				if ($this->echooutput) {
					echo PHP_EOL;
				}
			}

			if ($this->echooutput) {
				echo $this->pdo->log->header(
					'Matched ' . number_format(($updated > 0) ? $updated : 0) . ' PreDB titles to release search names.'
				);
			}
		}
	}

	/**
	 * Try to match a single release to a PreDB title when the release is created.
	 *
	 * @param string $cleanerName
	 *
	 * @return array|bool Array with title/ID from PreDB if found, bool False if not found.
	 */
	public function matchPre($cleanerName)
	{
		if ($cleanerName == '') {
			return false;
		}

		$titleCheck = $this->pdo->queryOneRow(
			sprintf('SELECT ID FROM prehash WHERE title = %s LIMIT 1', $this->pdo->escapeString($cleanerName))
		);

		if ($titleCheck !== false) {
			return array(
				'title' => $cleanerName,
				'prehashid' => $titleCheck['ID']
			);
		}

		// Check if clean name matches a PreDB filename.
		$fileCheck = $this->pdo->queryOneRow(
			sprintf('SELECT ID, title FROM prehash WHERE filename = %s LIMIT 1', $this->pdo->escapeString($cleanerName))
		);

		if ($fileCheck !== false) {
			return array(
				'title' => $fileCheck['title'],
				'prehashid' => $fileCheck['ID']
			);
		}

		return false;
	}

	/**
	 * Matches the hashes within the predb table to release files and subjects (names) which are hashed.
	 *
	 * @param $time
	 * @param $echo
	 * @param $cats
	 * @param $namestatus
	 * @param $show
	 *
	 * @return int
	 */
	public function parseTitles($time, $echo, $cats, $namestatus, $show)
	{
		$namefixer = new NameFixer(['Echo' => $this->echooutput, 'ConsoleTools' => $this->pdo->log, 'Settings' => $this->pdo]);
		$consoletools = new ConsoleTools(['ColorCLI' => $this->pdo->log]);
		$updated = $checked = 0;

		$tq = '';
		if ($time == 1) {
			$tq = 'AND r.adddate > (NOW() - INTERVAL 3 HOUR) ORDER BY rf.releaseid, rf.size DESC';
		}
		$ct = '';
		if ($cats == 1) {
			$ct = 'AND r.categoryid IN (1090, 2020, 3050, 6050, 5050, 7050, 8010)';
		}

		if ($this->echooutput) {
			$te = '';
			if ($time == 1) {
				$te = ' in the past 3 hours';
			}
			echo $this->pdo->log->header('Fixing search names' . $te . " using the predb hash.");
		}
		$regex = "AND (r.ishashed = 1 OR rf.ishashed = 1)";

		if ($cats === 3) {
			$query = sprintf('SELECT r.ID AS releaseid, r.name, r.searchname, r.categoryid, r.groupid, '
				. 'dehashstatus, rf.name AS filename FROM releases r '
				. 'LEFT OUTER JOIN releasefiles rf ON r.ID = rf.releaseid '
				. 'WHERE nzbstatus = 1 AND dehashstatus BETWEEN -6 AND 0 AND prehashid = 0 %s', $regex);
		} else {
			$query = sprintf('SELECT r.ID AS releaseid, r.name, r.searchname, r.categoryid, r.groupid, '
				. 'dehashstatus, rf.name AS filename FROM releases r '
				. 'LEFT OUTER JOIN releasefiles rf ON r.ID = rf.releaseid '
				. 'WHERE nzbstatus = 1 AND isrenamed = 0 AND dehashstatus BETWEEN -6 AND 0 %s %s %s', $regex, $ct, $tq);
		}

		$res = $this->pdo->queryDirect($query);
		$total = $res->rowCount();
		echo $this->pdo->log->primary(number_format($total) . " releases to process.");
		if ($res instanceof Traversable) {
			foreach ($res as $row) {
				if (preg_match('/[a-fA-F0-9]{32,40}/i', $row['name'], $matches)) {
					$updated = $updated + $namefixer->matchPredbHash($matches[0], $row, $echo, $namestatus, $this->echooutput, $show);
				} else if (preg_match('/[a-fA-F0-9]{32,40}/i', $row['filename'], $matches)) {
					$updated = $updated + $namefixer->matchPredbHash($matches[0], $row, $echo, $namestatus, $this->echooutput, $show);
				}
				if ($show === 2) {
					$consoletools->overWritePrimary("Renamed Releases: [" . number_format($updated) . "] " . $consoletools->percentString(++$checked, $total));
				}
			}
		}
		if ($echo == 1) {
			echo $this->pdo->log->header("\n" . $updated . " releases have had their names changed out of: " . number_format($checked) . " files.");
		} else {
			echo $this->pdo->log->header("\n" . $updated . " releases could have their names changed. " . number_format($checked) . " files were checked.");
		}

		return $updated;
	}

	/**
	 * Get all PRE's in the DB.
	 *
	 * @param int    $offset  OFFSET
	 * @param int    $offset2 LIMIT
	 * @param string $search  Optional title search.
	 *
	 * @return array The row count and the query results.
	 */
	public function getAll($offset, $offset2, $search = '')
	{
		if ($search !== '') {
			$search = explode(' ', trim($search));
			if (count($search) > 1) {
				$search = "LIKE '%" . implode("%' AND title LIKE '%", $search) . "%'";
			} else {
				$search = "LIKE '%" . $search[0] . "%'";
			}
			$search = 'WHERE title ' . $search;
			$count = $this->pdo->queryOneRow(sprintf('SELECT COUNT(*) AS cnt FROM prehash %s', $search));
			$count = $count['cnt'];
		} else {
			$count = $this->getCount();
		}

		$parr = $this->pdo->query(
			sprintf('
				SELECT p.*, r.guid
				FROM prehash p
				LEFT OUTER JOIN releases r ON p.ID = r.prehashid %s
				ORDER BY p.predate DESC LIMIT %d OFFSET %d',
				$search,
				$offset2,
				$offset
			)
		);
		return array('arr' => $parr, 'count' => $count);
	}

	/**
	 * Get count of all PRE's.
	 *
	 * @return int
	 */
	public function getCount()
	{
		$count = $this->pdo->queryOneRow('SELECT COUNT(*) AS cnt FROM prehash');
		return ($count === false ? 0 : $count['cnt']);
	}

	/**
	 * Get all PRE's for a release.
	 *
	 * @param int $preID
	 *
	 * @return array
	 */
	public function getForRelease($preID)
	{
		return $this->pdo->query(sprintf('SELECT * FROM prehash WHERE ID = %d', $preID));
	}

	/**
	 * Return a single PRE for a release.
	 *
	 * @param int $preID
	 *
	 * @return array
	 */
	public function getOne($preID)
	{
		return $this->pdo->queryOneRow(sprintf('SELECT * FROM prehash WHERE ID = %d', $preID));
	}

}