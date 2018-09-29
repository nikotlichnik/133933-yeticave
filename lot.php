<?php
require_once 'functions.php';
require_once 'temp_user.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $lot_id = $_GET['id'];

    $con = connect_db();
    $lot = get_lot($con, $lot_id);

    if ($lot) {
        $title = 'YetiCave - ' . $lot['name'];

        $lot['current_price'] = $lot['current_price'] ?? $lot['start_price'];

        $page_content = include_template('lot.php', ['lot' => $lot]);
        $content = include_template('layout.php', [
            'content' => $page_content,
            'title' => $title,
            'is_auth' => $is_auth,
            'user_name' => $user_name,
            'categories' => get_categories($con)]);

        print($content);
    } else {
        http_response_code(404);
    }
} else {
    http_response_code(404);
}

