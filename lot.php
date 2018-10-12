<?php
require_once 'functions.php';
require_once 'mysql_helper.php';
require_once 'start_session.php';

$categories = get_categories($con);

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $lot_id = $_GET['id'];

    $con = connect_db();
    $lot = get_lot($con, $lot_id);

    $errors = [];
    // Если пользователь залогинен и отправлена форма
    if ($user and $_SERVER['REQUEST_METHOD'] == 'POST') {
        $bet = $_POST;
        $required_fields = ['cost'];
        $errors += check_required_text_fields($bet, $required_fields);

        // Проверка поля с ценой и шагом ставки
        $bet_check_options = [
            'options' => [
                'min_range' => $lot['min_bet']
            ]
        ];

        $errors += check_special_value(
            $bet['cost'],
            'cost',
            FILTER_VALIDATE_INT,
            'Значение должно быть больше или равно минимальной ставке',
            $bet_check_options);

        if (!$errors){
            $sql = "INSERT INTO bets (bet, author, lot) VALUES (?, ?, ?)";
            $stmt = db_get_prepare_stmt($con, $sql, [
                $bet['cost'],
                $user['id'],
                $lot_id
            ]);
            mysqli_stmt_execute($stmt);

            $lot = get_lot($con, $lot_id);
        }
    }

    if ($lot) {
        $title = 'YetiCave - ' . $lot['name'];

        $is_allowed_to_bet = is_allowed_to_bet($con, $user, $lot);
        $bets = get_bets($con, $lot_id);

        $page_content = include_template('lot.php', [
            'lot' => $lot,
            'user' => $user,
            'bet' => $bet ?? '',
            'bets' => $bets,
            'errors' => $errors,
            'is_allowed_to_bet' => $is_allowed_to_bet,
            'categories' => $categories]);

        $content = include_template('layout.php', [
            'content' => $page_content,
            'title' => $title,
            'user' => $user,
            'categories' => $categories]);

        print($content);
    } else {
        http_response_code(404);
    }
} else {
    http_response_code(404);
}

