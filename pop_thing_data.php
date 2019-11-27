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
if(isset($_POST['ses'])){
  $i = 0;
  foreach ($_POST['ses'] as $value) {
    $_SESSION['ses'][$i] = $value;
    $i++;
  }
}
if(isset($_POST['vote'])){
  $i = 0;
  foreach ($_POST['vote'] as $value) {
    $_SESSION['vote'][$i] = $value;
    $i++;
  }
}

if ( isset( $_POST['id'] ) ) {
  try {
    $stmt_1 = $pdo->query( "SELECT count(*) FROM group_pop_display_info WHERE pop_id = '$id'" );
    $pop_thing_count = $stmt_1->fetchColumn();
    if($pop_thing_count==0){
      $stmt = $pdo->prepare( "INSERT INTO group_pop_display_info(user_id,group_id,pop_id,pop_top,pop_left) VALUES (?, ?, ?, ?, ?)" );
      $stmt->execute( array( $userID,$group_ID,$id,$top,$left) );
    }else{
      $stmt_1 = $pdo->query( "SELECT * FROM group_pop_display_info WHERE pop_id = '$id'" );
      foreach ( $stmt_1 as $value ) {
        if($value['pop_top']==$top&&$value['pop_left']==$left){
        }else{
          $stmt = $pdo->query("DELETE FROM group_pop_display_info WHERE pop_id = '$id'");
          $stmt = $pdo->prepare( "INSERT INTO group_pop_display_info(user_id,group_id,pop_id,pop_top,pop_left) VALUES (?, ?, ?, ?, ?)" );
          $stmt->execute( array( $userID,$group_ID,$id,$top,$left) );
        }
      }
    }//header('Location:trip_chat.php',true,303);
  } catch ( Exception $e ) {
    echo $e->getMessage() . PHP_EOL;
  }
} else {

}

?>
