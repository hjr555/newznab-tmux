<?php

require_once './config.php';

use newznab\controllers\AdminPage;
use newznab\controllers\Groups;

$page = new AdminPage();

// set the current action
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';

switch($action)
{
	case 'submit':
		if (isset($_POST['groupfilter']) && !empty($_POST['groupfilter'])) {
			$groups = new Groups;
			$msgs = $groups->addBulk($_POST['groupfilter'], $_POST['active'], $_POST['backfill']);
		}
		break;
	default:
		$msgs = '';
		break;
}

$page->smarty->assign('groupmsglist',$msgs);
$page->smarty->assign('yesno_ids', array(1,0));
$page->smarty->assign('yesno_names', array( 'Yes', 'No'));

$page->title = "Bulk Add Newsgroups";
$page->content = $page->smarty->fetch('group-bulk.tpl');
$page->render();
