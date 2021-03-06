<?php
require_once 'functions.php';
require_once 'start_session.php';

if (!$user) {
    http_response_code(403);
    die();
}

$title = 'YetiCave - Добавление лота';
$con = connect_db();
$categories = get_categories($con);

$required_fields = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date'];
$field_length = [
    'lot-name' => 255,
    'message' => 1000,
    'lot-rate' => 8,
    'lot-step' => 8
];
$photo_field = 'lot-photo';
$date_field = 'lot-date';
$category_field = 'category';
$price_fields = ['lot-rate', 'lot-step'];


$min_price = 1;

$max_year = 2038;
$date_format = 'd.m.Y'; // ДД.ММ.ГГГГ
$db_date_format = '%d.%m.%Y'; // ДД.ММ.ГГГГ

$max_photo_size = 200000;
$allowed_photo_mime = ['image/png', 'image/jpeg'];
$is_photo_required = true;
$photo_folder = 'upload/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lot = $_POST;
    $files = $_FILES;

    $errors = [];
    $errors += check_required_text_fields($lot, $required_fields);
    $errors += check_field_length($lot, $field_length);
    $errors += check_file($files, $photo_field, $allowed_photo_mime, $max_photo_size, $is_photo_required);
    $errors += check_date($lot, $date_field, $date_format, $max_year);
    $errors += check_category($con, $lot, $category_field);

    // Проверка полей с ценой и шагом ставки
    $price_check_options = [
        'options' => [
            'min_range' => $min_price
        ]
    ];

    foreach ($price_fields as $field) {
        $errors += check_special_value(
            $lot,
            $field,
            FILTER_VALIDATE_INT,
            'Значение должно быть целым числом больше нуля',
            $price_check_options);
    }

    // Вывод ошибок, если они есть, иначе отправка формы
    if (count($errors)) {
        $page_content = include_template('add.php', [
            'categories' => $categories,
            'lot' => $lot,
            'errors' => $errors]);
    } else {
        $photo_name = save_file($files, $photo_field, $photo_folder);
        $db_photo_path = $photo_folder . $photo_name;

        add_lot($con, $user, $lot, $db_photo_path, $db_date_format);

        $new_id = mysqli_insert_id($con);

        header('Location: lot.php?id=' . $new_id);
    }
} else {
    $page_content = include_template('add.php', ['categories' => $categories]);
}

$content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $title,
    'user' => $user,
    'categories' => $categories]);

print($content);
