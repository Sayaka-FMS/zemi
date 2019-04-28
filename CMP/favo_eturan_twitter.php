<html>
<?php

if ( isset( $_GET[ "sakuzyo" ] ) ) {
	if ( isset( $_GET[ 'icon_url' ] ) )$icon_url = $_GET[ 'icon_url' ];
	if ( isset( $_GET[ 'id' ] ) )$id = $_GET[ 'id' ];
	if ( isset( $_GET[ 'screen_name' ] ) )$screen_name = $_GET[ 'screen_name' ];
	if ( isset( $_GET[ 'updated' ] ) )$updated = $_GET[ 'updated' ];
	if ( isset( $_GET[ 'tweet_id' ] ) )$tweet_id = $_GET[ 'tweet_id' ];
	if ( isset( $_GET[ 'url' ] ) )$url = $_GET[ 'url' ];
	try {
		$pdo = new PDO( 'sqlite:favo.db' );

		// SQL実行時にもエラーの代わりに例外を投げるように設定
		// (毎回if文を書く必要がなくなる)
		$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

		// デフォルトのフェッチモードを連想配列形式に設定 
		// (毎回PDO::FETCH_ASSOCを指定する必要が無くなる)
		$pdo->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
		$pdo->query( "DELETE FROM favo_id_twitter  WHERE id='$id' AND icon_url='$icon_url' AND screen_name = '$screen_name' AND updated ='$updated' " );


	} catch ( Exception $e ) {

		echo $e->getMessage() . PHP_EOL;
	}
}
?>

<head>
	<title> mydate</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="tweet-serch.css">
</head>

<body style="body">
	<table >
		<?php

		if ( isset( $_GET[ 'id' ] ) ) {
			$id = $_GET[ 'id' ];

			$db = new PDO( "sqlite:favo.db" );
			try {
				$pdo = new PDO( 'sqlite:favo.db' );

				// SQL実行時にもエラーの代わりに例外を投げるように設定
				// (毎回if文を書く必要がなくなる)
				$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

				// デフォルトのフェッチモードを連想配列形式に設定 
				// (毎回PDO::FETCH_ASSOCを指定する必要が無くなる)
				$pdo->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
				$result = $db->query( "SELECT *from favo_id_twitter where id='$id'" );

				for ( $i = 0; $row = $result->fetch(); ++$i ) {
					echo "<tr valign=center>";
					echo "<tr>";
					echo "<td >". '<img alt="" src="' . $row['icon_url'] . '">'. "</td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td >" . $row[ 'screen_name' ] . "</td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td >" . $row[ 'updated' ] . "</td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td >" . $row[ 'tweet_id' ] . "</td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td >" . $row[ 'text' ] . "</td>";
					echo "</tr>";
					echo "<tr>";
					$rink=$row[ 'url' ];
					echo "<td ><a href ='$rink'>$rink</a></td>";
					echo "</tr>";
					echo "<tr>";
					?>
		<form action=favo_eturan_twitter.php method=get>
			<input type="hidden" name="id" value="<?php print $id;?>">
			<input type="hidden" name="icon_url" value="<?php print $row[ 'icon_url' ]?>">
			<input type="hidden" name="screen_name" value="<?php print $row[ 'screen_name' ]?>">
			<input type="hidden" name="updated" value="<?php print $row[ 'updated' ]?>">
			<input type="hidden" name="tweet_id" value="<?php print $row[ 'tweet_id' ]?>">
			<input type="hidden" name="url" value="<?php print $row[ 'url' ] ?>">
			</td>
			<td><input class="button4" type=submit border=0 name=sakuzyo value="削除">
			</td>
			</tr>
			<tr>
				<td>
		</form>
		<?php
		}

		} catch ( Exception $e ) {

			echo $e->getMessage() . PHP_EOL;
		}
		}

		?>
		<form action=tweet-serch.php method=get>
		<input class="button" type=submit border=0 name=back value="閲覧ページに戻る">
		</form>

</body>

</html>