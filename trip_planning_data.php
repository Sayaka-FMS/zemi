<?php
// 送信された名前とメッセージの表示
//header("HTTP/1.1 503 Service Unavailable");
session_start();
if ( isset( $_SESSION[ 'username' ] ) ) {
  $name = $_SESSION[ 'username' ];
  $userID = $_SESSION['userID'];
  $group_ID = $_SESSION['group_ID'];
}
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

if ( isset( $group_ID ) ) {
  try {
    $title = $_POST['title'];
    $stmt_1 = $pdo->query( "SELECT * FROM trip_plan_info WHERE title = '$title'" );
    foreach ( $stmt_1 as $value ) {
     if($value['trip_day']==$_POST['trip_day']&&$value['trip_day_id']==$_POST['trip_day_id']){
       $trip_day = $_POST['trip_day'];
       $trip_day_id = $_POST['trip_day_id'];
      $stmt = $pdo->query("DELETE FROM trip_plan_info WHERE title = '$title' AND trip_day = '$trip_day' AND trip_day_id = '$trip_day_id'");
     }
    }
    $stmt = $pdo->prepare( "INSERT INTO trip_plan_info(group_id,title,trip_day,trip_day_id,trip_time,trip_context) VALUES (?, ?, ?, ?, ?, ?)" );
    $stmt->execute( array( $group_ID,$_POST['title'],$_POST['trip_day'],$_POST['trip_day_id'],$_POST['trip_time'],$_POST['trip_context']) );
  } catch ( Exception $e ) {
    echo $e->getMessage() . PHP_EOL;
  }
} else {

}

?>
