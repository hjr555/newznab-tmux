<?php

use newznab\UserMovies;
use newznab\Category;


if (!$page->users->isLoggedIn()) {
	$page->show403();
}

$um = new UserMovies(['Settings' => $page->settings]);
if (isset($_REQUEST["del"])) {
	$um->delMovie($page->users->currentUserId(), $_REQUEST["del"]);
}

$cat = new Category(['Settings' => $page->settings]);
$tmpcats = $cat->getChildren(Category::MOVIE_ROOT);
$categories = [];
foreach ($tmpcats as $c) {
	$categories[$c['id']] = $c['title'];
}

$movies = $um->getMovies($page->users->currentUserId());
$results = [];
foreach ($movies as $mov => $m) {
	$movcats = explode('|', $m['categoryid']);
	if (is_array($movcats) && sizeof($movcats) > 0) {
		$catarr = [];
		foreach ($movcats as $movcat) {
			if (!empty($movcat)) {
				$catarr[] = $categories[$movcat];
			}
		}
		$m['categoryNames'] = implode(', ', $catarr);
	} else {
		$m['categoryNames'] = '';
	}

	$results[$mov] = $m;
}
$page->smarty->assign('movies', $results);

$page->title = "Edit My Movies";
$page->meta_title = "Edit My Movies";
$page->meta_keywords = "couch,potato,movie,add";
$page->meta_description = "Manage Your Movies";

$page->content = $page->smarty->fetch('mymoviesedit.tpl');
$page->render();
