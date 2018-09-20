<?php
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
 * @return string
 */
//function get_timer() {
//    $seconds_in_hour = 60 * 60;
//    $seconds_in_minute = 60;
//
//    $now_stamp = time();
//    $end_stamp = strtotime('tomorrow');
//    $diff_stamp = $end_stamp - $now_stamp;
//    $hours = floor($diff_stamp / $seconds_in_hour);
//    $minutes = floor($diff_stamp % $seconds_in_hour / $seconds_in_minute);
//
//    $hours = $hours < 10 ? '0' . $hours : $hours;
//    $minutes = $minutes < 10 ? '0' . $minutes : $minutes;
//    $timer = $hours . ':' . $minutes;
//
//    return $timer." ".$now_stamp;
//}

function get_timer() {
    $now_stamp = time();
    $end_stamp = strtotime('tomorrow');
    $diff_stamp = $end_stamp - $now_stamp;
    $timer = date('H:i', $diff_stamp);

    return $timer;
}
