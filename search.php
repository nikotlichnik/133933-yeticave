<?php
require_once 'functions.php';
require_once 'start_session.php';

$con = connect_db();
$lots = [];

if (isset($_GET['search'])) {
    $user_search = $_GET['search'];
    $safe_search = mysqli_real_escape_string($con, $user_search);
    $sql = "SELECT l.id,
              l.name,
              l.start_price,
              l.img_path,
              l.expiration_date,
              MAX(b.bet)   AS current_price,
              COUNT(b.bet) AS bet_counter,
              c.name       AS category
            FROM lots l
              JOIN categories c ON l.category = c.id
              LEFT JOIN bets b ON l.id = b.lot
            WHERE MATCH(l.name, l.description) AGAINST ('$safe_search')
            GROUP BY l.id
            ORDER BY l.creation_date DESC";
    $res = mysqli_query($con, $sql);
    $lots = mysqli_fetch_all($res, MYSQLI_ASSOC);
}

$title = 'YetiCave - Поиск.';

$categories = get_categories($con);

$page_content = include_template('search.php', ['lots' => $lots, 'user_search' => $user_search]);
$content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $title,
    'user' => $user,
    'categories' => get_categories($con)]);

print($content);
