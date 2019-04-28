<?php
session_start();
$result = "";

if ( isset( $_SESSION[ 'id' ] ) ) {
	$id = $_SESSION[ 'id' ];

	$pdo = new PDO( "sqlite:favo.db" );
	$st = $pdo->prepare( "select *from user_id where id = ?" );
	$st->execute( array( $id ) );
	$user_on_db = $st->fetch();

	if ( !$user_on_db ) {
		$result = "指定されたユーザーが存在しません";
		?>
        <p><a href="new_account_submit.php">新しいアカウントを作成</a>してよろしいですか</p>
        <?php
	} else {
		$_SESSION[ "id" ] = $id;
		header( "Location: select.php" );
		exit;
	}
}

?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Login success</title>
	<script src="jquery-3.2.1.min.js" type="text/javascript"></script>
 　　　<script src="CMP.js" type="text/javascript"></script>
</head>

<body id="body">
	<h2>
		<?php echo $result; ?>
	</h2>
</body>

</html>
