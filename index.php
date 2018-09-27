<?php
date_default_timezone_set("Europe/Moscow");

require_once 'functions.php';

$host = '127.0.0.1';
$user = 'root';
$password = '';
$database = 'yeticave';

$con = mysqli_connect($host, $user, $password, $database);
mysqli_set_charset($con, 'utf-8');

// Запрос лотов
$sql = 'SELECT
  l.name,
  l.start_price,
  l.img_path,
  MAX(b.bet)   AS current_price,
  COUNT(b.bet) AS bet_counter,
  c.name       AS category
FROM lots l
  JOIN categories c ON l.category = c.id
  LEFT JOIN bets b ON l.id = b.lot
WHERE l.expiration_date > now()
GROUP BY l.id
ORDER BY l.creation_date DESC';
$result = mysqli_query($con, $sql);
$lots = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Запрос категорий для футера
$sql = 'SELECT name FROM categories';
$result = mysqli_query($con, $sql);
$categories_footer = mysqli_fetch_all($result, MYSQLI_ASSOC);

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
