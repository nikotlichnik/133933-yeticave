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
    $sql = 'SELECT name FROM categories';
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
