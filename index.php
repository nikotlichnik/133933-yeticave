<?php
date_default_timezone_set("Europe/Moscow");

require_once 'functions.php';
require_once 'start_session.php';

$con = connect_db();

$title = 'YetiCave - Главная страница.';

$categories = get_categories($con);

$page_content = include_template('index.php', ['lots' => get_lots($con), 'categories' => $categories]);
$content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $title,
    'user' => $user,
    'categories' => $categories]);

print($content);
