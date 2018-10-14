<?php
require_once 'mysql_helper.php';
require_once 'functions.php';
require_once 'start_session.php';

if ($user) {
    header('Location: index.php');
}

$title = 'YetiCave - Вход';
$con = connect_db();
$categories = get_categories($con);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST;

    $required_fields = ['email', 'password'];

    $errors = [];
    $errors += check_required_text_fields($login, $required_fields);

    // Проверка существования учётной записи
    if (empty($errors)) {
        $res = get_password_result($con, $login['email']);
        if (mysqli_num_rows($res) === 0) {
            $errors['email'] = 'Пользователя с таким email не существует';
        }
    }

    // Проверка соответствия пароля
    if (empty($errors)) {
        $res = get_password_result($con, $login['email']);
        $password = mysqli_fetch_assoc($res)['password'];

        if (!password_verify($login['password'], $password)) {
            $errors['password'] = 'Неверный пароль';
        }
    }

    // Вывод ошибок, если они есть, иначе открытие сессии
    if (count($errors)) {
        $page_content = include_template('login.php', [
            'categories' => $categories,
            'login' => $login,
            'errors' => $errors]);
    } else {
        $_SESSION['user_id'] = get_id($con, $login['email']);
        header('Location: index.php');
    }

} else {
    $page_content = include_template('login.php', ['categories' => $categories]);
}

$content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $title,
    'user' => $user,
    'categories' => $categories]);

print($content);

