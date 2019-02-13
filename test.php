<html>
<head>
	<title>PHP TEST</title>
</head>

<body>

	<?php

	$link = mysqli_connect( "localhost", "root", "" );
	if ( !$link ) {
		die( '接続失敗です。' );
	}

	print( '<p>接続に成功しました。</p>' );

	$db_selected = mysqli_select_db( $link, 'test' );
	if ( !$db_selected ) {
		die( 'データベース選択失敗です。' );
	}

	print( '<p>データベースを選択しました。</p>' );

	$link->set_charset( "utf8" );

	$result = $link->query( "SELECT * FROM ryokou_db" );
	if ( !$result ) {
		die( 'クエリーが失敗しました。' );
	}

	foreach ( $result as $row ) {
		// データベースのフィールド名で出力
		var_dump( $row );

		// 改行を入れる
		echo '<br>';
	}

	$dsn = 'mysql:host=localhost;dbname=test;charset=utf8mb4';
	$username = 'root';
	$password = '';

	try {
		$pdo = new PDO($dsn,$username,$password);

		// SQL実行時にもエラーの代わりに例外を投げるように設定
		// (毎回if文を書く必要がなくなる)
		$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

		// デフォルトのフェッチモードを連想配列形式に設定 
		// (毎回PDO::FETCH_ASSOCを指定する必要が無くなる)
		$pdo->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
		$stmt = $pdo->prepare( "INSERT INTO user_ryokou_info VALUES (?, ?, ?, ?, ?)" );
		$user_id = 1;
		$ryokou_n = 1;
		$ryokou_name = "TOKYO";
		$ryokou_basho = "スカイツリー";
		$motimono = "";
		$stmt->execute( array( $user_id, $ryokou_n, $ryokou_name, $ryokou_basho, $motimono ) );


	} catch ( Exception $e ) {

		echo $e->getMessage() . PHP_EOL;
	}



	$close_flag = mysqli_close( $link );

	if ( $close_flag ) {
		print( '<p>切断に成功しました。</p>' );
	}

	?>
</body>
</html>