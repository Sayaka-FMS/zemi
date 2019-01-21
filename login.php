<?php
session_start();
$dsn = 'mysql:host=localhost;dbname=test;charset=utf8mb4';
$db_username = 'root';
$db_password = '';
if(isset($_GET['urltoken'])){
$_SESSION['urltoken'] = $_GET['urltoken'];
}
date_default_timezone_set( 'Asia/Tokyo' );
$time = date( "Y-m-d H:i:s" );
if ( empty( $_POST["username"] ) ) {
	echo "ユーザIDを入力してください";
} else if ( empty( $_POST[ "password" ] ) ) {
	echo "パスワードを入力してください";
}else {
	$pdo = new PDO( $dsn, $db_username, $db_password );
	$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	// デフォルトのフェッチモードを連想配列形式に設定
	// (毎回PDO::FETCH_ASSOCを指定する必要が無くなる)
	$pdo->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
	try {
		$userID = $_POST["username"];
		$stmt = $pdo->prepare( "SELECT * FROM user_info_2 where userID = ?" );
		$stmt->execute( array($userID) );
		$user_sanshou = $stmt->fetch();
		if(!$user_sanshou){
			echo "指定されたユーザIDが存在しません</br>";
			echo "<a href='new_account.php'>新しいアカウントを作成</a>";
		}else{
      if($user_sanshou['password']!=$_POST['password']){
				echo "パスワードが間違っています";
			}else{
				if(isset($_SESSION['urltoken'])){
					try {
					$stmt2 = $pdo->prepare( "INSERT INTO user_group_info (id,url_token,user_id,date) VALUES (NULL,?,?,?)" );
					$stmt2->execute( array($_SESSION['urltoken'],$user_sanshou['userID'],$time) );
					unset($_SESSION['urltoken']);
					} catch ( Exception $e ) {
						echo $e->getMessage() . PHP_EOL;
					}
				}
				$_SESSION['username'] = $user_sanshou['username'];
				$_SESSION['userID'] = $user_sanshou['userID'];
			  header("Location:choice.php");
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
	<b>ユーザID</b>
	<input name="username" type="text" size="20">
	</br>
	<b>パスワード</b>
	<input name="password" type="password" size="20">
	</br>
	<input type="submit" name="login" value="ログイン">
</form>

<body>
</body>
</html>
