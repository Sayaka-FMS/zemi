<?php
session_start();
if(isset($_POST['ok'])){
  //セッション変数を全て解除
  $_SESSION = array();
  //セッションクッキーの削除
  if (isset($_COOKIE["PHPSESSID"])) {
      setcookie("PHPSESSID", '', time() - 1800, '/');
  }
  //セッションを破棄する
  session_destroy();
  header("Location:login.php");
}


?>

<html>
<head>
</head>
<p>ログアウトしてよろしいですか？</p>
<form action="logout.php" method="post">
<input type="hidden" name="ok" value="1">
<input type="submit" value="はい"></input>
</form>
<button type="button" name="NO" value="いいえ" onclick="history.back()">いいえ</button>
</html>
