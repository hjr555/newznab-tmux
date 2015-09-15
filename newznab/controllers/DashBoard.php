<?php
use newznab\db\Settings;


/**
 * Class DashBoard
 */
class DashBoard
{
	/**
	 * @var newznab\db\Settings
	 */
	public $pdo;

	/**
	 * @var
	 */
	private $options;

	/**
	 * @param array $options
	 */
	public function __construct(array $options = [])
	{
		$defaults = [
			'Settings' => null,
		];
		$options += $defaults;
		$this->pdo = ($options['Settings'] instanceof Settings ? $options['Settings'] : new Settings());
		$this->options = $options;
	}

	/**
	 * @param $secs
	 *
	 * @return string
	 */
	public function time_elapsed($secs)
	{

		$d = [];
		$d[0] = [1, "sec"];
		$d[1] = [60, "min"];
		$d[2] = [3600, "hr"];
		$d[3] = [86400, "day"];
		$d[4] = [31104000, "yr"];

		$w = [];

		$return = '';
		$now = time();
		$diff = ($now - ($secs >= $now ? $secs - 1 : $secs));
		$secondsLeft = $diff;

		for ($i = 4; $i > -1; $i--) {
			$w[$i] = intval($secondsLeft / $d[$i][0]);
			$secondsLeft -= ($w[$i] * $d[$i][0]);
			if ($w[$i] != 0) {
				$return .= $w[$i] . " " . $d[$i][1] . (($w[$i] > 1) ? 's' : '') . " ";
			}
		}

		return $return;
	}

	/**
	 * getLastGroupUpdate
	 */
	public function getLastGroupUpdate()
	{
		$data = $this->pdo->queryOneRow(sprintf('SELECT UNIX_TIMESTAMP(last_updated) AS age FROM groups ORDER BY age DESC LIMIT 1'));
		$age = DashBoard::time_elapsed($data['age']);

		return $age;
	}

	/**
	 * getLastReleaseCreated
	 */
	public function getLastReleaseCreated()
	{
		$data = $this->pdo->queryOneRow(sprintf('SELECT UNIX_TIMESTAMP(adddate) AS age FROM releases ORDER BY id DESC LIMIT 1'));
		$age = DashBoard::time_elapsed($data['age']);

		return $age;
	}

	/**
	 * getGitInfo
	 */
	public function getGitInfo()
	{
		if (file_exists(NN_ROOT . '.git/HEAD')) {
			$stringfromfile = file(NN_ROOT . '.git/HEAD', FILE_USE_INCLUDE_PATH);

			$stringfromfile = $stringfromfile[0]; //get the string from the array

			$explodedstring = explode("/", $stringfromfile); //separate out by the "/" in the string

			$branchname = $explodedstring[2]; //get the one that is always the branch name
			$branchname = trim($branchname);

			if (file_exists(NN_ROOT . '.git/refs/heads/' . $branchname)) {
				$gitversion = file_get_contents(NN_ROOT . '.git/refs/heads/' . $branchname);
			} else {
				$gitversion = "unknown";
			}
		}
	}

	/**
	 * getDatabaseInfo
	 */
	public function getDatabaseInfo()
	{
		$data = $this->pdo->queryOneRow(sprintf("SELECT * FROM settings WHERE setting = 'sqlpatch'"));

		return $data['value'];
	}

	/**
	 * count of releases
	 */
	public function getReleaseCount()
	{
		$releases = new Releases;
		$total = $releases->getCount();

		return $total;
	}

	/**
	 * count of releases
	 */
	public function getNewestRelease()
	{
		$newest = $this->pdo->queryOneRow(sprintf('SELECT searchname AS newestrelname FROM releases ORDER BY id DESC LIMIT 1'));

		return $newest['newestrelname'];

	}

	public function getActiveGroupCount()
	{
		$g = new Groups;
		$active_groups = $g->getCount("", true);

		return $active_groups;
	}

	public function getPendingProcessingCount()
	{
		$data = $this->pdo->query(sprintf('select count(*) as todo from releases where (bookinfoid IS NULL AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0 and categoryid IN (7010, 7020, 7040, 7060)) OR
		(consoleinfoid IS NULL AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0 and categoryid in ( select id from category where parentid = 1000 AND categoryid != 1050)) OR
		(imdbid IS NULL AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0 and categoryid in ( select id from category where parentid = 2000 AND categoryid != 2020)) OR
		(musicinfoid IS NULL AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0 and categoryid in ( select id from category where parentid = 3000 AND categoryid != 3050)) OR
		(rageid = -1 AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0 and categoryid in ( select id from category where parentid = 5000  AND categoryid != 5050)) OR
		(xxxinfo_id = 0 AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0 and categoryid in (select id from category where parentid = 6000 AND categoryid != 6070)) OR
		(gamesinfo_id = 0 AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0 and categoryid = 4050)'
		)
		);
		$total = $data[0]['todo'];

		return $total;
	}


	public function buildReleaseTable()
	{
		if (!defined('SHOW_RPC') || SHOW_RPC != 'checked') {
			return;
		} else {
			$category = new Category;
			# get all the active categories
			$allCategories = $category->get(true);

			foreach ($allCategories as $cat) {
				$res = $this->pdo->queryOneRow('select count(id) as num from releases where categoryid = %s', $cat['id']);
				if ($res['num'] > 0) {
					return['count' => $res['num'], 'category_title' => $cat['title']];
				}
			}
		}
	}

	public function buildGroupTable()
	{
		if (!defined('SHOW_RPG') || SHOW_RPG != 'checked') {
			return;
		} else {
			$group = new Groups;
			# get all the groups
			$allGroups = $group->getAll();

			foreach ($allGroups as $group) {
				$res = $this->pdo->queryOneRow(sprintf('SELECT COUNT(id) AS num FROM releases WHERE groupid = %s', $group['id']));
				if ($res["num"] > 0) {
					return ['count' => $res['num'], '$group_name' => $group['name']];
				}
			}
		}
	}

	public function buildPendingTable()
	{
		if (!defined('SHOW_PROCESSING') || SHOW_PROCESSING != 'checked') {
			return;
		}

		//amount of books left to do
		$book_query = "select count(*) as todo from releases
					  		where (bookinfoid IS NULL AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0)
							and categoryid IN (7010, 7020, 7040, 7060);";
		//amount of console left to do
		$console_query = "SELECT count(*) as todo from releases
							where (consoleinfoid IS NULL AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0)
							and categoryid in ( select id from category where parentid = 1000 AND categoryid != 1050);";
		//amount of movies left to do
		$movies_query = "SELECT count(*) as todo from releases
							where (imdbid IS NULL AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0)
							and categoryid in ( select id from category where parentid = 2000 AND categoryid != 2020);";
		//amount of music left to do
		$music_query = "SELECT count(*) as todo from releases
							where (musicinfoid IS NULL AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0)
							and categoryid in ( select id from category where parentid = 3000 AND categoryid != 3050);";
		//amount of pc games left to do
		$pcgames_query = "SELECT count(*) as todo from releases
							where (gamesinfo_id = 0 AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0)
							and categoryid = 4050;";
		//amount of tv left to do
		$tvrage_query = "SELECT count(*) as todo from releases
							where (rageid = -1 AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0)
							and categoryid in ( select id from category where parentid = 5000  AND categoryid != 5050);";
		//amount of XXX left to do
		$xxx_query = "SELECT count(*) as todo from releases
							where (xxxinfo_id = 0 AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0)
							and categoryid in (select id from category where parentid = 6000 AND categoryid != 6070);";

		# books
		$books = $this->pdo->queryOneRow($book_query);
		echo $books["todo"];
		# games
		$games = $this->pdo->queryOneRow($pcgames_query);
		echo $games["todo"];
		# console
		$res = $this->pdo->queryOneRow($console_query);
		echo $res["todo"];
		# movies
		$res = $this->pdo->queryOneRow($movies_query);
		echo $res["todo"];
		# music
		$res = $this->pdo->queryOneRow($music_query);
		echo $res["todo"];
		# tv
		$res = $this->pdo->queryOneRow($tvrage_query);
		echo $res["todo"];
		# XXX
		$res = $this->pdo->queryOneRow($xxx_query);
		echo $res["todo"];

	}
}