<?php
require_once 'mysql_helper.php';
require_once 'functions.php';
require_once 'start_session.php';

if (!$user) {
    http_response_code(403);
} else {
    $title = 'YetiCave - Добавление лота';
    $con = connect_db();
    $categories = get_categories($con);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $lot = $_POST;

        $required_fields = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date'];
        $photo_field = 'lot-photo';
        $price_fields = ['lot-rate', 'lot-step'];
        $date_field = 'lot-date';

        $min_price = 1;

        $date_format = 'd.m.Y'; // ДД.ММ.ГГГГ
        $db_date_format = '%d.%m.%Y'; // ДД.ММ.ГГГГ

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

            insert_lot($con, $user, $lot, $photo_folder, $photo_name, $db_date_format);

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
}
