<!doctype html>
<?php
session_start();
function h($str) { return htmlspecialchars($str, ENT_QUOTES, "UTF-8"); }
	$id = $_SESSION['id'];
	$name = $_SESSION['name'];
	$pdo = new PDO( "sqlite:favo.db" );
	$st = $pdo->prepare( "select *from user_id where id = ?" );
	$st->execute( array( id ) );
	$user_on_db = $st->fetch();
	if ( !$user_on_db ) {
		$st = $pdo->prepare( "INSERT INTO user_id(id,name) VALUES (?, ?)" );
		$st->execute( array( $id , $name ) );
		$result = 	header('Location: CMP.php');
	} else {

		$result = "同一のユーザーネームが存在してます</br><a href='login_form.php'>戻る</a>";
	}

?>
<html>

<head>
	<meta charset="utf-8">
	<title>無題ドキュメント</title>
</head>

<body>
<h2><?php print $result; ?></h2>
</body>

</html>
