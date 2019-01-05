<?php
// 送信された名前とメッセージの表示
if ( isset( $_POST[ 'name' ] ) ) {
	$name = $_POST[ 'name' ];
	if ( $name == "" ) {
		$name = "noname";
	}
} else {
	$name = "";
}
if ( isset( $_POST[ 'message' ] ) ) {
	$message = $_POST[ 'message' ];
} else {
	$message = "";
}
$dsn = 'mysql:host=localhost;dbname=test;charset=utf8mb4';
$username = 'root';
$password = '';
$id = 1;
date_default_timezone_set( 'Asia/Tokyo' );
$time = date( "Y-m-d H:i:s" );
$pdo = new PDO( $dsn, $username, $password );
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
// デフォルトのフェッチモードを連想配列形式に設定
// (毎回PDO::FETCH_ASSOCを指定する必要が無くなる)
$pdo->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
//chatデータ入れる
try {
	$stmt = $pdo->prepare( "INSERT INTO trip_chat VALUES (?, ?, ?, ?)" );
	$stmt->execute( array( $id, $name, $message, $time ) );
} catch ( Exception $e ) {
	echo $e->getMessage() . PHP_EOL;
}
?>

<!-- chat.php -->

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<title>1行メッセージ</title>
</head>

<body>

	<h1>メッセージ</h1>

	<form method="post" action="trip_chat.php">
		<div>
			<b>おなまえ</b>
			<input name="name" type="text" size="20" maxlength="10">
		</div>
		<div>
			<b>コメント</b>
			<input name="message" type="text" size="100" maxlength="50" required>
		</div>
		<button name="submit" type="submit">送信</button>
	</form>
	<?php
	//chatデータ表示
	try {
		$stmt = $pdo->query( "SELECT * FROM trip_chat" );
		foreach ( $stmt as $value ) {
			echo $value[ 'name' ] . "「 " . $value[ 'message' ] . " 」</br>";
		};
	} catch ( Exception $e ) {
		echo $e->getMessage() . PHP_EOL;
	}
	?>
</body>
</html>
