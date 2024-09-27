<?php

include '../../config.php';
include '../../functions.php';


$MySQLi = new mysqli('localhost',$DB['username'],$DB['password'],$DB['dbname']);
$MySQLi->query("SET NAMES 'utf8'");
$MySQLi->set_charset('utf8mb4');
if ($MySQLi->connect_error){
    echo 'Connection failed: ' . $MySQLi->connect_error;
    $MySQLi->close();
    die;
}


//          post method denied          //
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(304);
    echo json_encode(['status' => 'error', 'message' => 'درخواست شما باید از طریق POST ارسال شود.'], JSON_PRETTY_PRINT);
    $MySQLi->close();
    die();
}


//          check all parameters          //
if(empty($_REQUEST['username']) or empty($_REQUEST['password'])){
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'ورودی نامعتبر'], JSON_PRETTY_PRINT);
    $MySQLi->close();
    die();
}
$username = $_REQUEST['username'];
$password = md5($_REQUEST['password']);


//          check user availability in DB          //
@$request_sender_user = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `user` WHERE `username` = '{$username}' LIMIT 1"));
if(!$request_sender_user){
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'کاربر موردنظر یافت نشد.'], JSON_PRETTY_PRINT);
    $MySQLi->close();
    die();
}


//          check password validation in DB          //
@$isOK = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `user` WHERE `username` = '{$username}' AND `password` = '{$password}' LIMIT 1"));
if(!$isOK){
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'رمز عبور اشتباه است.'], JSON_PRETTY_PRINT);
    $MySQLi->close();
    die();
}


//          show respons          //
$key = genKey();
$MySQLi->query("UPDATE `user` SET `key` = '{$key}' WHERE `username` = '{$username}' LIMIT 1");
@$getData = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `user` WHERE `username` = '{$username}' LIMIT 1"));
http_response_code(200);
echo json_encode(['status' => 'ok', 'result' => $getData], JSON_PRETTY_PRINT);
$MySQLi->close();
die();
?>