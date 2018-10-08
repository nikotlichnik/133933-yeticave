<?php
date_default_timezone_set("Europe/Moscow");

require_once 'functions.php';

$con = connect_db();

session_start();
$user = [];
if (isset($_SESSION['user_id'])) {
    $user = get_user_info($con, $_SESSION['user_id']);
}

$con = connect_db();

$title = 'YetiCave - Главная страница.';

$categories = ['Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное'];

$page_content = include_template('index.php', ['lots' => get_lots($con), 'categories' => $categories]);
$content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $title,
    'user' => $user,
    'categories' => get_categories($con)]);

print($content);
