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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST;

    $required_fields = ['email', 'password', 'name', 'message'];
    $email_field = 'email';
    $avatar_field = 'avatar';
    $files = $_FILES;

    $allowed_img_mime = ['image/png', 'image/jpeg'];
    $max_avatar_size = 200000;
    $avatar_folder = 'upload/';
    $is_avatar_required = false;

    $errors = [];
    $errors += check_required_text_fields($user, $required_fields);
    $errors += check_file($files, $avatar_field, $allowed_img_mime, $max_avatar_size, $is_avatar_required);
    $errors += check_unique_email($con, $user, $email_field);
    $errors += check_special_value($user, $email_field, FILTER_VALIDATE_EMAIL, 'Введите корректный email');

    // Вывод ошибок, если они есть, иначе отправка формы
    if (count($errors)) {
        $page_content = include_template('sign-up.php', [
            'categories' => $categories,
            'user' => $user,
            'errors' => $errors]);
    } else {
        // Сохранение изображения
        $db_avatar_path = null;

        if ($files['name']) {
            $avatar_name = save_file($files, $avatar_field, $avatar_folder);
            $db_avatar_path = $avatar_folder . $avatar_name;
        }

        add_user($con, $user, $db_avatar_path);

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
