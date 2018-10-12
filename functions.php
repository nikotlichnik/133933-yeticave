<?php
require_once 'mysql_helper.php';

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
              l.id,
              l.name,
              l.start_price,
              l.img_path,
              l.description,
              l.bet_step,
              l.expiration_date,
              l.author,
              MAX(b.bet)   AS current_price,
              c.name       AS category
            FROM lots l
              JOIN categories c ON l.category = c.id
              LEFT JOIN bets b ON l.id = b.lot
            WHERE l.id = $safe_id
            GROUP BY l.id";
    $result = mysqli_query($con, $sql);
    $lot = mysqli_fetch_assoc($result);

    $lot['current_price'] = $lot['current_price'] ?? $lot['start_price'];
    $lot['min_bet'] = $lot['current_price'] + $lot['bet_step'];

    return $lot;
}

/**
 * @param mysqli $con
 * @param string $user_search_query
 * @return int Число результатов по поисковому запросу
 */
function count_search_results($con, $user_search_query) {
    $safe_search = mysqli_real_escape_string($con, $user_search_query);
    $sql = "SELECT COUNT(id) as counter
            FROM lots
            WHERE MATCH(name, description) AGAINST ('$safe_search')";
    $res = mysqli_query($con, $sql);

    return mysqli_fetch_assoc($res)['counter'];
}

/**
 * Выполняет поисковый запрос пользователя
 * @param mysqli $con
 * @param string $user_search_query
 * @param $limit
 * @param $offset
 * @return array|null Ассоциативный массив с объявлениями
 */
function search_lots($con, $user_search_query, $limit, $offset) {
    $safe_search = mysqli_real_escape_string($con, $user_search_query);
    $sql = "SELECT l.id,
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
            WHERE MATCH(l.name, l.description) AGAINST ('$safe_search')
            GROUP BY l.id
            LIMIT $limit
            OFFSET $offset";
    $res = mysqli_query($con, $sql);

    return mysqli_fetch_all($res, MYSQLI_ASSOC);
}

/**
 * Проверяет делал ли пользователь ставку для лота
 * @param mysqli $con
 * @param int $user_id
 * @param int $lot_id
 * @return bool|mysqli_result
 *
 */
function is_already_bet($con, $user_id, $lot_id) {
    $safe_id = mysqli_real_escape_string($con, $lot_id);
    $sql = "SELECT EXISTS(SELECT id FROM bets WHERE author=$user_id AND lot=$safe_id)";
    $res = mysqli_query($con, $sql);

    return mysqli_fetch_array($res)[0];
}

/**
 * Проверяет не истекло ли время лота
 * @param mysqli $con
 * @param int $lot_id
 * @return bool
 */
function is_lot_expired($con, $lot_id) {
    $safe_id = mysqli_real_escape_string($con, $lot_id);
    $sql = "SELECT expiration_date FROM lots WHERE id = $safe_id";
    $res = mysqli_query($con, $sql);
    $expiration_date = mysqli_fetch_assoc($res)['expiration_date'];

    $date_lot = new DateTime($expiration_date);
    $date_now = new DateTime('now');

    return $date_now > $date_lot;
}

/**
 * Проверяет, доступно ли добавление ставки лоту
 * @param mysqli $con
 * @param array $user
 * @param array $lot
 * @return bool
 */
function is_allowed_to_bet($con, $user, $lot) {
    if ($user) {
        $is_already_bet = is_already_bet($con, $user['id'], $lot['id']);
        $is_lot_expired = is_lot_expired($con, $lot['id']);
        $is_user_author = $lot['author'] === $user['id'];

        return !$is_already_bet and !$is_lot_expired and !$is_user_author;
    }

    return false;
}

/**
 * Получает ставки для лота
 * @param mysqli $con
 * @param int $lot_id
 * @return array|null
 */
function get_bets($con, $lot_id) {
    $safe_id = mysqli_real_escape_string($con, $lot_id);
    $sql = "SELECT b.bet, b.date, u.name
            FROM bets b
            JOIN users u ON b.author = u.id
            WHERE b.lot = $safe_id
            ORDER BY b.date DESC";
    $res = mysqli_query($con, $sql);
    $bets = mysqli_fetch_all($res, MYSQLI_ASSOC);

    return $bets;
}

/**
 * Возвращает относительную дату для ставки
 * @param string $date
 * @return string
 */
function format_bet_date($date) {
    $bet_date = new DateTime($date);
    $now_date = new DateTime('now');

    $date_diff = $bet_date->diff($now_date);
    $days = $date_diff->d;
    $hours = $date_diff->h;
    $minutes = $date_diff->i;

    if ($days > 0) {
        $formatted_date = $bet_date->format('d.m.y в H:i');
    } elseif ($hours > 0) {
        $formatted_date = $hours . ' ' . make_plural(['час', 'часа', 'часов'], $hours) . ' назад';
    } elseif ($minutes > 0) {
        $formatted_date = $minutes . ' ' . make_plural(['минуту', 'минуты', 'минут'], $minutes) . ' назад';
    } else {
        $formatted_date = 'Только что';
    }

    return $formatted_date;
}

/**
 * Возвращает нужную форму слова для числительного (например 'день', 'дня' или 'дней')
 * @param array $options Массив из трёх словоформ [ед.число, ед.число род.падеж, мн.число род.падеж]
 * @param int $number
 * @return mixed
 */
function make_plural($options, $number) {
    $word = $options[2];
    $remainder = $number % 10;

    if ($remainder === 1 && $number !== 11) {
        $word = $options[0];
    }

    if (($remainder === 2 && $number !== 12) ||
        ($remainder === 3 && $number !== 13) ||
        ($remainder === 4 && $number !== 14)) {
        $word = $options[1];
    }

    return $word;
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
 * Возвращает значение таймера для лота
 * @param $date_finish
 * @return string
 */
function get_timer($date_finish) {
    $date_now = new DateTime('now');
    $date_end = new DateTime($date_finish);
    $timer = '00:00:00';

    if ($date_end < $date_now) {
        return $timer;
    }

    $dates_diff = $date_end->diff($date_now);
    $years = $dates_diff->y;
    $months = $dates_diff->m;
    $days = $dates_diff->d;

    $years_str = $years ? $years . ' ' . make_plural(['год', 'года', 'лет'], $years) . ' ' : '';
    $months_str = $months ? $months . ' ' . make_plural(['месяц', 'месяца', 'месяцев'], $months) . ' ' : '';
    $days_str = $days ? $days . ' ' . make_plural(['день', 'дня', 'дней '], $days) : '';

    $timer = $dates_diff->format($years_str . $months_str . $days_str . ' %H:%I:%S');

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
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            $error[$field] = 'Загрузите файл с изображением';
        } else {
            $file_size = $file['size'];
            $file_tmp_name = $file['tmp_name'];

            if ($file_size > $max_file_size) {
                $error[$field] = 'Максимальный размер файла 200Кб';
            }

            $is_correct_mime = false;
            foreach ($allowed_mime as $mime) {
                if (mime_content_type($file_tmp_name) === $mime) {
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

/**
 * Возвращает результат запроса на пароль по email
 * @param mysqli $con
 * @param string $email
 * @return bool|mysqli_result
 */
function get_password_result($con, $email) {
    $safe_email = mysqli_real_escape_string($con, $email);
    $sql = "SELECT password FROM users WHERE email ='$safe_email'";

    return mysqli_query($con, $sql);
}

/**
 * Возвращает id пользователя для указанного email
 * @param mysqli $con
 * @param string $email
 * @return int
 */
function get_id($con, $email) {
    $safe_email = mysqli_real_escape_string($con, $email);
    $sql = "SELECT id FROM users WHERE email ='$safe_email'";
    $res = mysqli_query($con, $sql);
    $id = mysqli_fetch_assoc($res)['id'];

    return $id;
}

/**
 * @param mysqli $con
 * @param int $id Идентификатор пользователя
 * @return array Данные о пользователе
 */
function get_user_info($con, $id) {
    $sql = "SELECT email, name, avatar_path FROM users WHERE id = $id";
    $res = mysqli_query($con, $sql);
    $user = mysqli_fetch_assoc($res);

    return [
        'id' => $id,
        'email' => $user['email'],
        'name' => $user['name'],
        'avatar' => $user['avatar_path']
    ];
}

/**
 * Возвращает строку с параметрами GET запроса для страницы поиска
 * @param string $search_query Поисковый запрос пользователя
 * @param int $page Номер страницы
 * @return string
 */
function get_href_search_attr($search_query, $page) {
    if ($page) {
        return 'href="?' . http_build_query(['search' => htmlspecialchars($search_query), 'page' => $page]) . '"';
    }

    return '';
}

/**
 * Добавляет лот в БД
 * @param mysqli $con
 * @param array $user Ассоциативный массив с данными о пользователе
 * @param array $lot Ассоциативный массив с данными из формы о лоте
 * @param string $photo_folder Имя папки с изображением
 * @param string $photo_name Имя изображения
 * @param string $db_date_format Формат даты $lot['lot-date'] для функции STR_TO_DATE()
 */
function add_lot($con, $user, $lot, $photo_folder, $photo_name, $db_date_format){
    $sql = "INSERT INTO lots (name, description, img_path, start_price, bet_step, expiration_date, author, category)
                VALUES (?, ?, ?, ?, ?, STR_TO_DATE(?, '$db_date_format'), ?, ?)";

    $stmt = db_get_prepare_stmt($con, $sql, [
        $lot['lot-name'],
        $lot['message'],
        $photo_folder . $photo_name,
        $lot['lot-rate'],
        $lot['lot-step'],
        $lot['lot-date'],
        $user['id'],
        $lot['category']]);

    mysqli_stmt_execute($stmt);
}

/**
 * Добавляет ставку в БД
 * @param mysqli $con
 * @param array $user Ассоциативный массив с данными о пользователе
 * @param array $bet Ассоциативный массив с данными из формы о ставке
 * @param int $lot_id Идентификатор лота
 */
function add_bet($con, $user, $bet, $lot_id){
    $sql = "INSERT INTO bets (bet, author, lot) VALUES (?, ?, ?)";
    $stmt = db_get_prepare_stmt($con, $sql, [
        $bet['cost'],
        $user['id'],
        $lot_id
    ]);

    mysqli_stmt_execute($stmt);
}
