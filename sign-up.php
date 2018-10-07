<?php
require_once 'mysql_helper.php';
require_once 'functions.php';
require_once 'temp_user.php';

$title = 'YetiCave - Регистрация';
$con = connect_db();
$categories = get_categories($con);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST;

    $required_fields = ['email', 'password', 'name', 'message'];

    $avatar_field = 'avatar';
    $avatar = $_FILES[$avatar_field];

    $allowed_img_mime = ['image/png', 'image/jpeg'];
    $max_avatar_size = 200000;
    $avatar_folder = 'upload/';
    $is_avatar_required = false;

    $errors = [];
    $errors += check_required_text_fields($user, $required_fields);
    $errors += check_file($avatar, $avatar_field, $allowed_img_mime, $max_avatar_size, $is_avatar_required);
    $errors += check_unique_email($con, $user['email']);
    $errors += check_special_value($user['email'], 'email', FILTER_VALIDATE_EMAIL, 'Введите корректный email');

    // Вывод ошибок, если они есть, иначе отправка формы
    if (count($errors)) {
        $page_content = include_template('sign-up.php', [
            'categories' => $categories,
            'user' => $user,
            'errors' => $errors]);
    } else {
        // Сохранение изображения
        $db_avatar_path = null;

        if ($avatar['name']) {
            $avatar_name = save_file($avatar, $avatar_folder);
            $db_avatar_path = $avatar_folder . $avatar_name;
        }

        // Сохраняем данные в БД
        $sql = "INSERT INTO users (registration_date, email, name, password, avatar_path, contacts)
                VALUES (NOW(), ?, ?, ?, ?, ?)";

        $stmt = db_get_prepare_stmt($con, $sql, [
            $user['email'],
            $user['name'],
            password_hash($user['password'], PASSWORD_DEFAULT),
            $db_avatar_path,
            $user['message']]);

        $res = mysqli_stmt_execute($stmt);

        // header('Location: login.php');
        die('Форма отправлена'); // Временно
    }
} else {
    $page_content = include_template('sign-up.php', ['categories' => $categories]);
}

$content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $title,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories]);

print($content);
