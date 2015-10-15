<?php

require_once './config.php';

use newznab\controllers\AdminPage;
use newznab\controllers\Users;

$page = new AdminPage();

$users = new Users();

$page->title = "User Role List";

//get the user roles
$userroles = $users->getRoles();

$page->smarty->assign('userroles',$userroles);

$page->content = $page->smarty->fetch('role-list.tpl');
$page->render();

