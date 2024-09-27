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
if(empty($_REQUEST['title']) or empty($_REQUEST['id']) or empty($_REQUEST['reqKey'])){
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'ورودی نامعتبر'], JSON_PRETTY_PRINT);
    $MySQLi->close();
    die();
}
$title = $_REQUEST['title'];
$id = $_REQUEST['id'];
$reqKey = $_REQUEST['reqKey'];


//          check user availability in DB          //
@$request_sender_user = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `user` WHERE `key` = '{$reqKey}' LIMIT 1"));
if(!$request_sender_user){
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'درخواست اعتبار سنجی نشد.'], JSON_PRETTY_PRINT);
    $MySQLi->close();
    die();
}


//          check category availability          //
@$getCategory = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `category` WHERE `id` = '{$id}' LIMIT 1"));
if(!$getCategory){
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'دسته بندی موردنظر یافت نشد.'], JSON_PRETTY_PRINT);
    $MySQLi->close();
    die();
}


//          check only owner or category owner can delete          //
if($getCategory['owner'] !== $request_sender_user['username'] and $request_sender_user['type'] !== "owner"){
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'درخواست اعتبار سنجی نشد.'], JSON_PRETTY_PRINT);
    $MySQLi->close();
    die();
}


//          show respons          //
$MySQLi->query("UPDATE `category` SET `title` = '{$title}' WHERE `id` = '{$id}' LIMIT 1");
@$getData = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `category` WHERE `id` = '{$id}' LIMIT 1"));
http_response_code(200);
echo json_encode(['status' => 'ok', 'result' => $getData], JSON_PRETTY_PRINT);
$MySQLi->close();
die();
?>