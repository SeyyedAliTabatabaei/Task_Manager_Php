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
if(empty($_REQUEST['username']) or empty($_REQUEST['password']) or empty($_REQUEST['type']) or empty($_REQUEST['reqKey'])){
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'ورودی نامعتبر'], JSON_PRETTY_PRINT);
    $MySQLi->close();
    die();
}
$username = $_REQUEST['username'];
$password = $_REQUEST['password'];
$type = $_REQUEST['type'];
$reqKey = $_REQUEST['reqKey'];


//          check user availability in DB          //
@$request_sender_user = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `user` WHERE `key` = '{$reqKey}' LIMIT 1"));
if(!$request_sender_user){
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'درخواست اعتبار سنجی نشد.'], JSON_PRETTY_PRINT);
    $MySQLi->close();
    die();
}


//          check forbidden requests          //
//          no one can register owner | users cannot register anyone | admins cannot register admins          //
if($type == 'owner' or $request_sender_user['type'] === 'user' or ($request_sender_user['type'] === 'admin' and $type === 'admin')){
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'درخواست اعتبار سنجی نشد.'], JSON_PRETTY_PRINT);
    $MySQLi->close();
    die();
}


//          check username availablity          //
@$requested_user = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `user` WHERE `username` = '{$username}' LIMIT 1"));
if($requested_user){
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'یوزرنیمی که قصد ایجاد آن را دارید از قبل وجود داشت'], JSON_PRETTY_PRINT);
    $MySQLi->close();
    die();
}


//          show respons          //
$key = genKey();
$password = md5($password);
$MySQLi->query("INSERT INTO `user` VALUES ('{$username}', '{$password}', '{$type}', '{$key}')");
@$getData = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `user` WHERE `username` = '{$username}' LIMIT 1"));
http_response_code(200);
echo json_encode(['status' => 'ok', 'result' => $getData], JSON_PRETTY_PRINT);
$MySQLi->close();
die();
?>