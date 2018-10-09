<?php
require_once 'functions.php';

session_start();

$con = connect_db();

$user = [];

if (isset($_SESSION['user_id'])) {
    $user = get_user_info($con, $_SESSION['user_id']);
}
