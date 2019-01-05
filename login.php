<?php
$dsn = 'mysql:host=localhost;dbname=test;charset=utf8mb4';
$db_username = 'root';
$db_password = '';
if ( empty( $_POST[ "username" ] ) ) {
	echo "ユーザ名を入力してください";
} else if ( empty( $_POST[ "password" ] ) ) {
	echo "パスワードを入力してください";
} else {
	$pdo = new PDO( $dsn, $db_username, $db_password );
	$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	// デフォルトのフェッチモードを連想配列形式に設定
	// (毎回PDO::FETCH_ASSOCを指定する必要が無くなる)
	$pdo->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
	try {
		$username = $_POST["username"];
		$stmt = $pdo->prepare( "SELECT * FROM user_info where user_name = ?" );
		$stmt->execute( array($username) );
		$user_sanshou = $stmt->fetch();
		if(!$user_sanshou){
			echo "指定されたユーザネームが存在しません</br>";
			echo "<a href='new_account.php'>新しいアカウントを作成</a>";
		}else{
      if($user_sanshou['password']!=$_POST['password']){
				echo "パスワードが間違っています";
			}else{
				header("Location:trip_chat.php");
			}
		}

	} catch ( Exception $e ) {
		echo $e->getMessage() . PHP_EOL;
	}
}
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>ログイン</title>
</head>
<p>ユーザログイン画面</p>
<form method="post" action="login.php">
	<b>ユーザ名</b>
	<input name="username" type="text" size="20">
	</br>
	<b>パスワード</b>
	<input name="password" type="text" size="20">
	</br>
	<input type="submit" name="login" value="ログイン">
</form>

<body>
</body>
</html>
