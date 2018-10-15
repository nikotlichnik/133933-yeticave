<?php
require_once 'mysql_helper.php';
require_once 'functions.php';
require_once 'start_session.php';

if ($user) {
    header('Location: index.php');
}

$title = 'YetiCave - Регистрация';
$con = connect_db();
$categories = get_categories($con);

$required_fields = ['email', 'password', 'name', 'message'];
$field_length = [
    'email' => 255,
    'password' => 255,
    'name' => 128,
    'message' => 1000
];
$email_field = 'email';
$avatar_field = 'avatar';

$allowed_img_mime = ['image/png', 'image/jpeg'];
$max_avatar_size = 200000;
$avatar_folder = 'upload/';
$is_avatar_required = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_form = $_POST;
    $files = $_FILES;

    $errors = [];
    $errors += check_required_text_fields($user_form, $required_fields);
    $errors += check_field_length($user_form, $field_length);
    $errors += check_file($files, $avatar_field, $allowed_img_mime, $max_avatar_size, $is_avatar_required);
    $errors += check_unique_email($con, $user_form, $email_field);
    $errors += check_special_value($user_form, $email_field, FILTER_VALIDATE_EMAIL, 'Введите корректный email');

    // Вывод ошибок, если они есть, иначе отправка формы
    if (count($errors)) {
        $page_content = include_template('sign-up.php', [
            'categories' => $categories,
            'user_form' => $user_form,
            'errors' => $errors]);
    } else {
        // Сохранение изображения
        $db_avatar_path = null;

        if ($files[$avatar_field]['name']) {
            $avatar_name = save_file($files, $avatar_field, $avatar_folder);
            $db_avatar_path = $avatar_folder . $avatar_name;
        }

        add_user($con, $user_form, $db_avatar_path);

        header('Location: login.php');
    }
} else {
    $page_content = include_template('sign-up.php', ['categories' => $categories]);
}

$content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $title,
    'user' => $user,
    'categories' => $categories]);

print($content);
