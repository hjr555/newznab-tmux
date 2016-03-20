<?php
require_once './config.php';
require_once NN_WWW . 'pages/smartyTV.php';

use newznab\Videos;

$page   = new AdminPage();
$tv = new smartyTV(['Settings' => $page->settings]);
$video = new Videos(['Settings' => $page->settings]);

switch ((isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view')) {
	case 'submit':
		//TODO: Use a function that allows overwrites
		//$tv->update($_POST["id"], $_POST["title"],$_POST["summary"], $_POST['countries_id']);

		if (isset($_POST['from']) && !empty($_POST['from'])) {
			header("Location:" . $_POST['from']);
			exit;
		}

		header("Location:" . WWW_TOP . "/show-list.php");
		break;

	case 'view':
	default:
		if (isset($_GET["id"])) {
			$page->title = "TV Show Edit";
			$show = $video->getByVideoID($_GET["id"]);
		}
		break;
}

$page->smarty->assign('show', $show);

$page->title   = "Edit TV Show Data";
$page->content = $page->smarty->fetch('show-edit.tpl');
$page->render();
