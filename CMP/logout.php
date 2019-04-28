<html>
<head>
  <!-- 外部ファイル読込 -->
  <link rel="stylesheet" type="text/css" href="example.css">
 
  <!-- HTML内埋め込み -->
 <style type="text/css">
body{
    font-family: "Hiragino Kaku Gothic ProN", Meiryo, sans-serif;
    font-size: 30px;
    line-height: 40px;
    color: #10108f;
    background: #FFD700;
    text-align: left;
    color: white;
    }
    .logout{
    font-size:10px
    }
</style>
    
</head>
<body>
    
</body>
</html>
<?php
session_start();
 
header("Content-type: text/html; charset=utf-8");
 
//セッション変数を全て解除
$_SESSION = array();
 
//セッションクッキーの削除
if (isset($_COOKIE["PHPSESSID"])) {
    setcookie("PHPSESSID", '', time() - 1800, '/');
}
 
//セッションを破棄する
session_destroy();
 
echo "<p>ログアウトしました。</p>";
 
echo "<a href='login_form.php' class=logout>はじめのページへ</a>";
?>