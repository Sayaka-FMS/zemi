<?php
session_start();
$dsn = 'mysql:host=localhost;dbname=test;charset=utf8mb4';
$db_username = 'root';
$db_password = '';
$pdo = new PDO( $dsn, $db_username, $db_password );
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
// デフォルトのフェッチモードを連想配列形式に設定
// (毎回PDO::FETCH_ASSOCを指定する必要が無くなる)
$pdo->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
if(isset($_POST['username'])&&isset($_POST['password'])&&isset($_POST['userID'])){
  try {
    $stmt = $pdo->prepare("INSERT INTO user_info_2 (id,userID,username,password) VALUES (NULL,?,?,?)");
    $userID = $_POST['userID'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt->execute( array($userID,$username,$password) );
  }catch ( Exception $e ) {
    echo $e->getMessage() . PHP_EOL;
  }
 header("Location:login.php");
}
?>
<html>
<head>
	<meta charset="utf-8">
	<title>アカウント登録</title>
</head>
<p>アカウント登録画面</p>
<form method="post" action="new_account.php">
  <b>ユーザID</b>
	<input name="userID" type="text" size="20">
	</br>
	<b>ユーザ名</b>
	<input name="username" type="text" size="20">
	</br>
	<b>パスワード</b>
	<input name="password" type="text" size="20">
	</br>
  <input name="urltoken" type="hidden"  value="<?=$_GET['urltoken']?>" size="20">
	<input type="submit" name="get_account" value="登録">
</form>

<body>
</body>
</html>
