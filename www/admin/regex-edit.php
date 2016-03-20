<?php
require_once("config.php");

use newznab\Category;
use newznab\ReleaseRegex;

$page = new AdminPage();
$category = new Category();
$reg = new ReleaseRegex();
$id = 0;

// set the current action
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';

switch($action)
{
	case 'submit':
		if ($_POST["id"] == "")
		{
			$reg->add($_POST);
		}
		else
		{
			$ret = $reg->update($_POST);
		}
		header("Location:".WWW_TOP."/regex-list.php".($_POST["groupname"] != "" ? "?group=".$_POST["groupname"] : ""));
		break;
	case 'addtest':
		if (isset($_GET['regex']) && isset($_GET['groupname'])) {
			$r = array('groupname'=>$_GET['groupname'], 'regex'=>$_GET['regex'], 'ordinal'=>'1', 'status'=>'1');
			$page->smarty->assign('regex', $r);
		}
		break;
	case 'view':
	default:

		$page->title = "Release Regex Add";

		if (isset($_GET["id"]))
		{
			$page->title = "Release Regex Edit";
			$id = $_GET["id"];

			$r = $reg->getByID($id);

		}
		else
		{
			$r = array();
			$r["status"] = 1;
		}
		$page->smarty->assign('regex', $r);

		break;
}

$page->smarty->assign('status_ids', [Category::STATUS_ACTIVE, Category::STATUS_INACTIVE]);
$page->smarty->assign('status_names', ['Yes', 'No']);

$page->smarty->assign('catlist',$category->getForSelect(true));

$page->content = $page->smarty->fetch('regex-edit.tpl');
$page->render();
