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

			$stringfromfile = $stringfromfile[0]; //get the string FROM the array

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
		$data = $this->pdo->query(sprintf('SELECT COUNT(*) AS todo FROM releases WHERE (bookinfoid IS NULL AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0 AND categoryid IN (7010, 7020, 7040, 7060)) OR
		(consoleinfoid IS NULL AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0 AND categoryid IN ( select id FROM category WHERE parentid = 1000 AND categoryid != 1050)) OR
		(imdbid IS NULL AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0 AND categoryid IN ( select id FROM category WHERE parentid = 2000 AND categoryid != 2020)) OR
		(musicinfoid IS NULL AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0 AND categoryid IN ( select id FROM category WHERE parentid = 3000 AND categoryid != 3050)) OR
		(rageid = -1 AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0 AND categoryid IN ( select id FROM category WHERE parentid = 5000  AND categoryid != 5050)) OR
		(xxxinfo_id = 0 AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0 AND categoryid IN (select id FROM category WHERE parentid = 6000 AND categoryid != 6070)) OR
		(gamesinfo_id = 0 AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0 AND categoryid = 4050)'
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
				$res = $this->pdo->queryOneRow('SELECT COUNT(id) AS num FROM releases WHERE categoryid = %s', $cat['id']);
				if ($res['num'] > 0) {
					return ['count' => $res['num'], 'category_title' => $cat['title']];
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
		$book_query = "SELECT COUNT(*) AS todo FROM releases
					  		WHERE (bookinfoid IS NULL AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0)
							AND categoryid IN (7010, 7020, 7040, 7060);";
		//amount of console left to do
		$console_query = "SELECT COUNT(*) AS todo FROM releases
							WHERE (consoleinfoid IS NULL AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0)
							AND categoryid IN ( select id FROM category WHERE parentid = 1000 AND categoryid != 1050);";
		//amount of movies left to do
		$movies_query = "SELECT COUNT(*) AS todo FROM releases
							WHERE (imdbid IS NULL AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0)
							AND categoryid IN ( select id FROM category WHERE parentid = 2000 AND categoryid != 2020);";
		//amount of music left to do
		$music_query = "SELECT COUNT(*) AS todo FROM releases
							WHERE (musicinfoid IS NULL AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0)
							AND categoryid IN ( select id FROM category WHERE parentid = 3000 AND categoryid != 3050);";
		//amount of pc games left to do
		$pcgames_query = "SELECT COUNT(*) AS todo FROM releases
							WHERE (gamesinfo_id = 0 AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0)
							AND categoryid = 4050;";
		//amount of tv left to do
		$tvrage_query = "SELECT COUNT(*) AS todo FROM releases
							WHERE (rageid = -1 AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0)
							AND categoryid IN ( select id FROM category WHERE parentid = 5000  AND categoryid != 5050);";
		//amount of XXX left to do
		$xxx_query = "SELECT COUNT(*) AS todo FROM releases
							WHERE (xxxinfo_id = 0 AND isrenamed = 0 AND proc_pp = 0 AND proc_par2 = 0 AND proc_nfo = 0 AND proc_files = 0 AND proc_sorter = 0)
							AND categoryid IN (select id FROM category WHERE parentid = 6000 AND categoryid != 6070);";

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

	public function buildRecentTable($newznab_cat, $category)
	{
		$category = new Category;
		# get all the child categories
		$allcategories = $category->getChildren($newznab_cat);
		$catarray = [];

		foreach ($allcategories as $cat) {
			array_push($catarray, $cat['id']);
		}

		$catstring = implode(',', $catarray);

		$res = $this->pdo->query(sprintf('SELECT r.searchname AS name,
							r.adddate AS date, r.guid AS guid, c.title AS title
							FROM releases r INNER JOIN category c ON c.id = r.categoryid
							WHERE r.categoryid IN (%s) ORDER BY r.adddate DESC limit 0,50', $catstring
								)
							);
		foreach ($res as $row) {
			$name = $row["name"];
			if (strlen($name) > 50) {
				$name = substr($row["name"], 0, 45);
			}
		}
	}

	public function buildRecentMoviesTable()
	{
		if (defined('SHOW_MOVIES') && SHOW_MOVIES === 'checked') {
			DashBoard::buildRecentTable(Category::CAT_PARENT_MOVIE, "Movies");
		}
	}

	public function buildRecentMusicTable()
	{
		if (defined('SHOW_MUSIC') && SHOW_MUSIC === 'checked') {
			DashBoard::buildRecentTable(Category::CAT_PARENT_MUSIC, "Music");
		}
	}

	public function buildRecentConsoleTable()
	{
		if (defined('SHOW_GAMES') && SHOW_GAMES === 'checked') {
			DashBoard::buildRecentTable(Category::CAT_PARENT_GAME, "Console");
		}
	}

	public function buildRecentTVTable()
	{
		if (defined('SHOW_TV') && SHOW_TV === 'checked') {
			DashBoard::buildRecentTable(Category::CAT_PARENT_TV, "Televison");
		}
	}

	public function buildRecentPCTable()
	{
		if (defined('SHOW_PC') && SHOW_PC === 'checked') {
			DashBoard::buildRecentTable(Category::CAT_PARENT_PC, "PC");
		}
	}

	public function buildRecentXXXTable()
	{
		if (defined('SHOW_XXX') && SHOW_XXX === 'checked') {
			DashBoard::buildRecentTable(Category::CAT_PARENT_XXX, "XXX");
		}
	}
}