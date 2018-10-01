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
    $max_img_size = 200000;
    $allowed_img_mime = ['image/png', 'image/jpeg'];

    $img_folder = 'img/';

    $errors = [];

    // Проверка заполненности полей
    foreach ($required_fields as $field) {
        if (empty($lot[$field])) {
            $errors[$field] = 'Заполните это поле';
        }
    }

    // Проверка изображения
    $photo_tmp_name = '';
    if ($_FILES[$photo_field]['error'] == UPLOAD_ERR_NO_FILE) {
        $errors[$photo_field] = 'Загрузите файл с изображением';
    } else {
        $photo_size = $_FILES[$photo_field]['size'];
        $photo_tmp_name = $_FILES[$photo_field]['tmp_name'];

        if ($photo_size > $max_img_size) {
            $errors[$photo_field] = 'Максимальный размер файла 200Кб';
        }

        $is_correct_mime = false;
        foreach ($allowed_img_mime as $mime) {
            if (mime_content_type($photo_tmp_name) == $mime) {
                $is_correct_mime = true;
            }
        }

        if (!$is_correct_mime) {
            $errors[$photo_field] = 'Файл должен иметь расширение .jpg, .jpeg или .png';
        }
    }

    // Проверка поля с ценой и шагом ставки
    $price_check_options = [
        'options' => [
            'min_range' => $min_price
        ]
    ];

    foreach ($price_fields as $field) {
        if (!filter_var($lot[$field], FILTER_VALIDATE_INT, $price_check_options)) {
            $errors[$field] = 'Значение должно быть целым числом больше нуля';
        }
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
        // Сохранение изображения
        $photo_path = __DIR__ . '/' . $img_folder;
        $photo_name = $_FILES[$photo_field]['name'];
        move_uploaded_file($photo_tmp_name, $photo_path . $photo_name);

        // Сохраняем данные в БД
        $sql = "INSERT INTO lots (name, description, img_path, start_price, bet_step, creation_date, expiration_date, author, category)
                VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?)";

        $stmt = db_get_prepare_stmt($con, $sql, [
            $lot['lot-name'],
            $lot['message'],
            $img_folder . $photo_name,
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
