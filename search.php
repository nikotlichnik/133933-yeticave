<?php
require_once 'functions.php';
require_once 'start_session.php';

$con = connect_db();
$lots = [];
$user_search_query = '';
$lots_per_page = 9;
$number_of_pages = 1;

if (isset($_GET['page'])) {
    $cur_page = (int)$_GET['page'] ?? 1;
} else {
    $cur_page = 1;
}

if (isset($_GET['search'])) {
    $user_search_query = $_GET['search'];

    $number_of_lots = count_search_results($con, $user_search_query);
    $number_of_pages = (int)ceil($number_of_lots / $lots_per_page);
    $offset = $lots_per_page * ($cur_page - 1);

    if ($number_of_lots) {
        $lots = search_lots($con, $user_search_query, $lots_per_page, $offset);
    }
}

$title = 'YetiCave - Поиск';

$categories = get_categories($con);

$page_range = range(1, $number_of_pages);
$previous_page = array_search($cur_page - 1, $page_range) !== false ? $cur_page - 1 : null;
$next_page = array_search($cur_page + 1, $page_range) !== false ? $cur_page + 1 : null;

$page_content = include_template('search.php', [
    'lots' => $lots,
    'user_search_query' => $user_search_query,
    'number_of_pages' => $number_of_pages,
    'cur_page' => $cur_page,
    'previous_page' => $previous_page,
    'next_page' => $next_page,
    'page_range' => $page_range,
    'categories' => $categories]);

$content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $title,
    'user' => $user,
    'categories' => $categories]);

print($content);
