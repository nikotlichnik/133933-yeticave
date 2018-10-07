<?php
require_once 'mysql_helper.php';
require_once 'functions.php';
require_once 'temp_user.php';

$title = 'YetiCave - Добавление лота';
$con = connect_db();
$categories = get_categories($con);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lot = $_POST;

    $required_fields = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date'];
    $photo_field = 'lot-photo';
    $price_fields = ['lot-rate', 'lot-step'];
    $date_field = 'lot-date';

    $min_price = 1;

    $date_format = 'd.m.Y'; // ДД.ММ.ГГГГ

    $photo = $_FILES[$photo_field];
    $max_photo_size = 200000;
    $allowed_photo_mime = ['image/png', 'image/jpeg'];
    $is_photo_required = true;
    $photo_folder = 'upload/';

    $errors = [];
    $errors += check_required_text_fields($lot, $required_fields);
    $errors += check_file($photo, $photo_field, $allowed_photo_mime, $max_photo_size, $is_photo_required);

    // Проверка поля с ценой и шагом ставки
    $price_check_options = [
        'options' => [
            'min_range' => $min_price
        ]
    ];

    foreach ($price_fields as $field) {
        $errors += check_special_value(
            $lot[$field],
            $field,
            FILTER_VALIDATE_INT,
            'Значение должно быть целым числом больше нуля',
            $price_check_options);
    }

    // Проверка поля даты
    if (!validate_date($lot[$date_field], $date_format)) {
        $errors[$date_field] = 'Дата должна иметь формат ДД.ММ.ГГГГ и быть больше текущей';
    }

    // Вывод ошибок, если они есть, иначе отправка формы
    if (count($errors)) {
        $page_content = include_template('add.php', [
            'categories' => $categories,
            'lot' => $lot,
            'errors' => $errors]);
    } else {
        $photo_name = save_file($photo, $photo_folder);

        // Сохраняем данные в БД
        $sql = "INSERT INTO lots (name, description, img_path, start_price, bet_step, creation_date, expiration_date, author, category)
                VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?)";

        $stmt = db_get_prepare_stmt($con, $sql, [
            $lot['lot-name'],
            $lot['message'],
            $photo_folder . $photo_name,
            $lot['lot-rate'],
            $lot['lot-step'],
            get_db_timestamp($lot['lot-date'], $date_format),
            1,
            $lot['category']]);

        $res = mysqli_stmt_execute($stmt);

        $new_id = mysqli_insert_id($con);

        header('Location: lot.php?id=' . $new_id);
    }
} else {
    $page_content = include_template('add.php', ['categories' => $categories]);
}

$content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $title,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories]);

print($content);
