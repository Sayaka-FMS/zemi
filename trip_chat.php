<?php
// 送信された名前とメッセージの表示
session_start();
if ( isset( $_SESSION[ 'username' ] ) ) {
	$name = $_SESSION[ 'username' ];
} else {
	header("Location:login.php");
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

if ( isset( $_POST[ 'message' ] ) ) {
	$message = $_POST[ 'message' ];
	//chatデータ入れる
	try {
		$stmt = $pdo->prepare( "INSERT INTO trip_chat VALUES (?, ?, ?, ?)" );
		$stmt->execute( array( $id, $name, $message, $time ) );
		header('Location:trip_chat.php',true,303);
	} catch ( Exception $e ) {
		echo $e->getMessage() . PHP_EOL;
	}
} else {

}
?>

<!-- chat.php -->

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<link type="text/css" rel="stylesheet" href="trip_chat.css">
	<title>旅行チャット</title>
</head>

<body id="your_container">
  <div id="bms_messages_container">
		<div id="bms_chat_header">
			<div id="bms_chat_user_status">
				 <div id="bms_status_icon">●</div>
				 <div id ="bms_chat_user_name">
			<?php
			echo $name.'さん';
			?>
			<a href="logout.php">ログアウト</a>
			<a href="choice.php">グループ切り替え</a>
		</div>
		</div>
		</div>
		<div id="bms_messages">
		<div id="bms_message">
	<?php
	//chatデータ表示
	try {
		$stmt = $pdo->query( "SELECT * FROM trip_chat" );
		foreach ( $stmt as $value ) {
			if($value['name']==$_SESSION['username']){
				echo  "<div id='bms_messege_p_right'>" .$value[ 'name' ] ." ". $value[ 'message' ] ."</div>";
			}else{
				echo  "<div id='bms_messege_p_left'>" .$value[ 'name' ] ." ". $value[ 'message' ] ."</div>";
			}
		};
	} catch ( Exception $e ) {
		echo $e->getMessage() . PHP_EOL;
	}
	?>
</div>
</div>
</div>
<div id="bms_send">
	<form method="post" action="trip_chat.php">
		<textarea  id="bms_send_message" name="message"></textarea>
		<button name="submit" id="bms_send_btn" type="submit">送信</button>
	</form>
</div>
</body>
</html>
