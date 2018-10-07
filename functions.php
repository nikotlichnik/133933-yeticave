<?php

/**
 * Возвращает соединение к базе данных
 * @return mysqli
 */
function connect_db() {
    $host = '127.0.0.1';
    $user = 'root';
    $password = '';
    $database = 'yeticave';

    $con = mysqli_connect($host, $user, $password, $database);
    mysqli_set_charset($con, 'utf-8');

    return $con;
}

/**
 * Получает из БД ассоциативный массив с категориями для меню
 * @param mysqli $con
 * @return array|null
 */
function get_categories($con) {
    $sql = 'SELECT id, name FROM categories';
    $result = mysqli_query($con, $sql);

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Получает из БД ассоциативный массив со списком лотов
 * @param $con
 * @return array|null
 */
function get_lots($con) {
    $sql = 'SELECT
              l.id,
              l.name,
              l.start_price,
              l.img_path,
              l.expiration_date,
              MAX(b.bet)   AS current_price,
              COUNT(b.bet) AS bet_counter,
              c.name       AS category
            FROM lots l
              JOIN categories c ON l.category = c.id
              LEFT JOIN bets b ON l.id = b.lot
            WHERE l.expiration_date > now()
            GROUP BY l.id
            ORDER BY l.creation_date DESC
            LIMIT 9';
    $result = mysqli_query($con, $sql);

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Получает данные о лоте по id
 * @param mysqli $con
 * @param int $id Идентификатор лота
 * @return array|null
 */
function get_lot($con, $id) {
    $safe_id = mysqli_real_escape_string($con, $id);
    $sql = "SELECT
              l.name,
              l.start_price,
              l.img_path,
              l.description,
              l.bet_step,
              l.expiration_date,
              MAX(b.bet)   AS current_price,
              c.name       AS category
            FROM lots l
              JOIN categories c ON l.category = c.id
              LEFT JOIN bets b ON l.id = b.lot
            WHERE l.id = $safe_id
            GROUP BY l.id";
    $result = mysqli_query($con, $sql);

    return mysqli_fetch_assoc($result);
}

/**
 * Функция-шаблонизатор
 * @param string $name
 * @param array $data
 * @return string
 */
function include_template($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

    if (!file_exists($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require_once $name;

    $result = ob_get_clean();

    return $result;
}

/**
 * Форматирует цену по разрядам
 * @param float $price Цена, которую необходимо отформатировать
 * @return string Строка в формате "XXX XXX ₽"
 */
function format_price($price) {
    $price = ceil($price);

    $formatted_price = number_format($price, 0, '', ' ');
    $formatted_price .= ' ₽';

    return $formatted_price;
}

/**
 * Проверяет соответствие даты указанному формату и наличие разницы во времени
 * @param string $user_date
 * @param string $format Формат даты, переданной в $user_date
 * @return bool
 */
function validate_date($user_date, $format) {
    $date = DateTime::createFromFormat($format, $user_date);
    if (!$date) {
        return false;
    }

    // Проверяем соответствие формату
    if ($date->format($format) !== $user_date) {
        return false;
    }

    // Проверяем наличие разницы во времени
    $date_now = new DateTime('now');
    if ($date < $date_now) {
        return false;
    }

    return true;
}

/**
 * Преобразует дату из формата пользователя к формату для записи в БД
 * @param string $user_date
 * @param string $format Формат даты, переданной в $user_date
 * @return string
 */
function get_db_timestamp($user_date, $format) {
    $db_format = 'Y-m-d H:i:s';
    $date = DateTime::createFromFormat('!' . $format, $user_date); // ! для того, чтобы время было 00:00:00
    return $date->format($db_format);
}

/**
 * Возвращает значение таймера для лота
 * @param $date_finish
 * @return string
 */
function get_timer($date_finish) {
    $date_now = new DateTime('now');
    $date_end = new DateTime($date_finish);
    $timer = '0 д 00:00:00';

    if ($date_end < $date_now) {
        return $timer;
    }

    $dates_diff = $date_end->diff($date_now);
    $timer = $dates_diff->format('%d д %H:%I:%S');

    return $timer;
}

/**
 * @param array $form_data Ассоциативный массив с данными формы
 * @param array $required_fields Массив с именами обязательных полей
 * @return array Ассоциативный массив с ошибками
 */
function check_required_text_fields($form_data, $required_fields) {
    $errors = [];

    foreach ($required_fields as $field) {
        if (empty($form_data[$field])) {
            $errors[$field] = 'Заполните это поле';
        }
    }

    return $errors;
}

/**
 * @param array $file Ассоциативный массив $_FILES['name']
 * @param string $field Имя поля ввода файла
 * @param array $allowed_mime Допустимые MIME типы для файла
 * @param int $max_file_size Максимальный размер файла в байтах
 * @param bool $is_required Обязательность поля ввода
 * @return array
 */
function check_file($file, $field, $allowed_mime, $max_file_size, $is_required) {
    $error = [];

    if ($is_required or $file['name']) {
        if ($file['error'] == UPLOAD_ERR_NO_FILE) {
            $error[$field] = 'Загрузите файл с изображением';
        } else {
            $file_size = $file['size'];
            $file_tmp_name = $file['tmp_name'];

            if ($file_size > $max_file_size) {
                $error[$field] = 'Максимальный размер файла 200Кб';
            }

            $is_correct_mime = false;
            foreach ($allowed_mime as $mime) {
                if (mime_content_type($file_tmp_name) == $mime) {
                    $is_correct_mime = true;
                }
            }

            if (!$is_correct_mime) {
                $error[$field] = 'Файл должен иметь расширение .jpg, .jpeg или .png';
            }
        }
    }

    return $error;
}

/**
 * Валидирует переданное значение указанным фильтром
 * @param string|int $value
 * @param string $field_name
 * @param int $filter
 * @param string $error_text
 * @param array $options
 * @return array
 */
function check_special_value($value, $field_name, $filter, $error_text, $options = []) {
    $error = [];

    if (!filter_var($value, $filter, $options)) {
        $error[$field_name] = $error_text;
    }

    return $error;
}

/**
 * Проверяет, есть ли указанный email в БД
 * @param mysqli $con
 * @param string $email
 * @return array
 */
function check_unique_email($con, $email) {
    $error = [];
    $safe_email = mysqli_real_escape_string($con, $email);

    $sql = "SELECT id FROM users WHERE email = '$safe_email'";
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result)) {
        $error['email'] = 'Введённый email уже используется';
    }

    return $error;
}

/**
 * Сохраняет файл из формы и возврящает сгенерированное имя
 * @param array $file Ассоциативный массив $_FILES['name']
 * @param string $folder Строка в формате "foldername/"
 * @return string
 */
function save_file($file, $folder) {
    $file_path = __DIR__ . '/' . $folder;
    $file_name_parts = explode('.', $file['name']);
    $file_extension = end($file_name_parts);
    $file_name = uniqid() . '.' . $file_extension;
    move_uploaded_file($file['tmp_name'], $file_path . $file_name);

    return $file_name;
}
