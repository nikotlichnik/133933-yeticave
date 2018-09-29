<?php
date_default_timezone_set("Europe/Moscow");

require_once 'functions.php';

$con = connect_db();

// Запрос лотов
$lots = get_lots($con);

// Запрос категорий для футера
$categories_footer = get_categories($con);

$title = 'YetiCave - Главная страница.';

$user_name = 'Никита';
$is_auth = rand(0, 1);

$user_avatar = 'img/user.jpg';

$categories = ['Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное'];

$page_content = include_template('index.php', ['lots' => $lots, 'categories' => $categories]);
$content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $title,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories_footer]);

print($content);
