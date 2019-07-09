<?php
// 送信された名前とメッセージの表示
session_start();
if ( isset( $_SESSION[ 'username' ] ) ) {
  $name = $_SESSION[ 'username' ];
  $userID = $_SESSION['userID'];
  $group_ID = $_SESSION['group_ID'];
}
$id = $_POST['id'];
$top = $_POST['top'];
$left = $_POST['left'];

$dsn = 'mysql:host=localhost;dbname=test;charset=utf8mb4';
$username = 'root';
$password = '';
date_default_timezone_set( 'Asia/Tokyo' );
$time = date( "Y-m-d H:i:s" );
$pdo = new PDO( $dsn, $username, $password );
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
// デフォルトのフェッチモードを連想配列形式に設定
// (毎回PDO::FETCH_ASSOCを指定する必要が無くなる)
$pdo->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );

if ( isset( $_POST['id'] ) ) {
  try {
    $stmt = $pdo->prepare( "INSERT INTO group_pop_display_info(group_id,pop_id,pop_top,pop_left) VALUES (?, ?, ?, ?)" );
    $stmt->execute( array( $group_ID,$id,$top,$left) );
    //header('Location:trip_chat.php',true,303);
  } catch ( Exception $e ) {
    echo $e->getMessage() . PHP_EOL;
  }
} else {

}

?>
