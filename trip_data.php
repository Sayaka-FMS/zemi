<?php
// 送信された名前とメッセージの表示
session_start();
if ( isset( $_SESSION[ 'username' ] ) ) {
  $name = $_SESSION[ 'username' ];
  $userID = $_SESSION['userID'];
  $group_ID = $_SESSION['group_ID'];
}
$name = $_POST['name'];
$userID = $_POST['userID'];

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

if ( isset( $_POST['message'] ) ) {
  //$message = $_POST[ 'message' ];
  //chatデータ入れる
  $message = $_POST['message'];
  if($_POST['favo']==1){
    $favo = 1;
  }else{
    $favo = 0;
  }
  try {
    $stmt = $pdo->prepare( "INSERT INTO trip_chat(userID,name,message,group_id,favo) VALUES (?, ?, ?,?,?)" );
    $stmt->execute( array( $userID, $name,$message,$group_ID,$favo ) );
    //header('Location:trip_chat.php',true,303);
  } catch ( Exception $e ) {
    echo $e->getMessage() . PHP_EOL;
  }
}

if(isset( $_POST['id'] )){
  try {
    $id = $_POST['id'];
    $vote = $_POST['vote'];
    $stmt = $pdo->query( "UPDATE trip_chat SET vote='$vote' WHERE id = '$id'" );
    //header('Location:trip_chat.php',true,303);
  } catch ( Exception $e ) {
    echo $e->getMessage() . PHP_EOL;
  }

}

?>
