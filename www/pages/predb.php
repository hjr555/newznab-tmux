<?php

use newznab\PreDb;

if (!$page->users->isLoggedIn()) {
	$page->show403();
}

$predb = new PreDb();

$offset = (isset($_REQUEST["offset"]) && ctype_digit($_REQUEST['offset'])) ? $_REQUEST["offset"] : 0;

if (isset($_REQUEST['presearch'])) {
	$lastSearch = $_REQUEST['presearch'];
	$parr = $predb->getAll($offset, ITEMS_PER_PAGE, $_REQUEST['presearch']);
} else {
	$lastSearch = '';
	$parr = $predb->getAll($offset, ITEMS_PER_PAGE);
}

$page->smarty->assign('pagertotalitems', $parr['count']);
$page->smarty->assign('pageroffset', $offset);
$page->smarty->assign('pageritemsperpage', ITEMS_PER_PAGE);
$page->smarty->assign('pagerquerybase', WWW_TOP . "/predb/&amp;offset=");
$page->smarty->assign('pagerquerysuffix', "#results");
$page->smarty->assign('lastSearch', $lastSearch);

$pager = $page->smarty->fetch("pager.tpl");
$page->smarty->assign('pager', $pager);

$page->smarty->assign('results', $parr['arr']);

$page->title = "Browse PreDb";
$page->meta_title = "View PreDb info";
$page->meta_keywords = "view,predb,info,description,details";
$page->meta_description = "View PreDb info";

$page->content = $page->smarty->fetch('predb.tpl');
$page->render();
