<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>Login form</title>
    <script src="jquery-3.2.1.min.js" type="text/javascript"></script>
 　　　<script src="CMP.js" type="text/javascript"></script>
  </head>
  <body id=body_select>
  <?php
  session_start();
	  	print "<p id=icon_display>ようこそ".$_SESSION['name']."さん</p>";
	  ?>
    <p id=p_select> 検索方法を選んでください。</p>
    <form action="CMP.php" method="get">
		 <input  id="button_select_1" type="submit" value="ホットペッパーで検索"> 
    </form>
<!--<p><a href="CMP.php">ホットペッパーで検索</a></p>-->
<!--<input type="button" value="ホットペッパーで検索" href="CMP.php">-->
</br>
<form action="kuse.php" method="get">
    <input id="button_select_2" type="submit" value="ぐるなびで検索">
    </form>
    </br>
    <form action="tweet-serch.php" method="get">
    <input id="button_select_3" type="submit" value="ツイッターで検索">
    </form>
<!--<p><a href="kuse.php">ぐるなびで検索</a></p></br>-->
 <!--  <p><a href="tweet-serch.php">Twitterで検</a></p>-->
     </br>
    <form action="new_plans.php" method="get">
    <input id="button_select_4" type="submit" value="カレンダーを確認">
</form>
    
    
  </body>
</html>