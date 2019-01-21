<?php
session_start();
$dsn = 'mysql:host=localhost;dbname=test;charset=utf8mb4';
$username = 'root';
$password = '';
$pdo = new PDO( $dsn, $username, $password );
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
// デフォルトのフェッチモードを連想配列形式に設定
// (毎回PDO::FETCH_ASSOCを指定する必要が無くなる)
$pdo->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
if(isset( $_SESSION[ 'username' ] )){
	if(isset($_POST['new_group'])){
		$urltoken = hash('sha256',uniqid(rand(),1));
		$userID = $_SESSION['userID'];
		$url = "http://localhost/zemi/login.php"."?urltoken=".$urltoken;
		try {
			$stmt = $pdo->prepare( "INSERT INTO pre_group_link (url_token,user_data) VALUES (?, ?)" );
			$stmt->execute( array($urltoken,$userID) );
		} catch ( Exception $e ) {
			echo $e->getMessage() . PHP_EOL;
		}
	}
}else {
	header("Location:login.php");
}

?>
<html lang="ja">
<head>
	<meta charset="utf-8">
</head>
<body>
	<a href="trip_chat.php">グループ画面へ</a></br>
	<form method="post" name="new_group_making" action="choice.php">
		<input type="hidden" name="new_group" value="ok">
		<a href="javascript:new_group_making.submit()">新しくグループを作成する</a>
	</form>
	<?php
	if(isset($_POST['new_group'])){
		echo "<a href=".$url.">".$url."</a>";
	}
	?>
</body>
</html>
