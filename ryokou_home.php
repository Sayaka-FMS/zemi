<?php

$link = mysqli_connect("localhost","root","");
if (!$link) {
  die('接続失敗です。');
}

$db_selected = mysqli_select_db($link,'test');
if (!$db_selected){
  die('データベース選択失敗です。');
}

$link->set_charset("utf8");

/*$result = $link->query("SELECT * FROM ryokou_db");
if (!$result) {
  die('クエリーが失敗しました。');
}

foreach($result as $row){
  // データベースのフィールド名で出力
  var_dump($row);

  // 改行を入れる
  echo '<br>';
}*/

$sql = $link->prepare("INSERT INTO user_ryokou_info VALUES (?, ?, ?, ?, ?)");
print_r($link->connect_errno);
$user_id = 1;
$ryokou_n = 1;
$ryokou_name = "TOKYO";
$ryokou_basho = "スカイツリー";
$motimono = NULL;

$result_flag = $sql->execute(array($user_id,$ryokou_n,$ryokou_name,$ryokou_basho,$motimono));

if (!$result_flag) {
    die('INSERTクエリーが失敗しました。');
}

?>


<html>
<head>
  <meta charset="UTF-8">
  <title>旅行登録画面</title>
</head>

<body>
  <a href="basho.html">旅行場所を選択する</a>
</br>
</br>
<form action=<?php echo $_SERVER['SCRIPT_NAME'];?> method="post">
<input type="time" name="time">
</br></br>
<input type="text" name="ryokou_basho">
</br></br>
<input type="text" name="motimono">
</br></br>
<input type="text" name="osusume_tour">
</br></br>
<input type="submit" name="touroku" value="登録">
</form>
</body>
</html>
