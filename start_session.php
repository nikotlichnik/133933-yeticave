<?php
session_start();

$user = [];

if (isset($_SESSION['user_id'])) {
    $user = get_user_info($con, $_SESSION['user_id']);
}
