<html>
<?php

if ( isset( $_GET[ "sakuzyo" ] ) ) {
	if ( isset( $_GET[ 'name' ] ) )$name = $_GET[ 'name' ];
	if ( isset( $_GET[ 'id' ] ) )$id = $_GET[ 'id' ];
	if ( isset( $_GET[ 'station_name' ] ) )$station_name = $_GET[ 'station_name' ];
	if ( isset( $_GET[ 'address' ] ) )$address = $_GET[ 'address' ];
	if ( isset( $_GET[ 'access' ] ) )$access = $_GET[ 'access' ];
	if ( isset( $_GET[ 'open' ] ) )$open = $_GET[ 'open' ];
	if ( isset( $_GET[ 'close' ] ) )$close = $_GET[ 'close' ];
	if ( isset( $_GET[ 'rink' ] ) )$rink = $_GET[ 'rink' ];
	if ( isset( $_GET[ 'img' ] ) )$img = $_GET[ 'img' ];
	try {
		$pdo = new PDO( 'sqlite:favo.db' );

		// SQL実行時にもエラーの代わりに例外を投げるように設定
		// (毎回if文を書く必要がなくなる)
		$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

		// デフォルトのフェッチモードを連想配列形式に設定 
		// (毎回PDO::FETCH_ASSOCを指定する必要が無くなる)
		$pdo->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
		$pdo->query( "DELETE FROM favo_id  WHERE id='$id' AND name='$name' AND station_name = '$station_name' AND address ='$address' " );


	} catch ( Exception $e ) {

		echo $e->getMessage() . PHP_EOL;
	}
}
?>

<head>
	<title> mydate</title>
	<meta charset="UTF-8">
  	 <script src="jquery-3.2.1.min.js" type="text/javascript"></script>
 　　<script src="CMP.js" type="text/javascript"></script>
   	 <link rel="stylesheet" href="gurunabi style.css">
    <style type="text/css">
	.tbl_01 th {

    width: 25%;

    text-align: left;

    padding: 10px 5px 5px 25px;

    border: 1px solid #e3e3e3;

    font-weight: normal;

    vertical-align: middle;

    background-color: #f6f6f6;

}

.tbl_01 td {

    vertical-align: middle;

    background: #FFF;

    padding: 10px 5px 5px 25px;

    border: 1px solid #e3e3e3;

}
	</style>
</head>

<body id=body>

		
			<?php
		if(isset($_GET['favo_eturan'])){
			?>
		<form action=CMP.php method=get>
		<input type=submit border=0 name=back value="閲覧ページに戻る">
		</form>
		<?php
		}else if(isset($_GET['favo_eturan_grunabi'])){
			?>
		<form action=kuse.php method=get>
		<input type=submit border=0 name=back value="閲覧ページに戻る">
		<?php
		}
	

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
				$result = $db->query( "SELECT *from favo_id where id='$id'" );

				for ( $i = 0; $row = $result->fetch(); ++$i ) {
					echo "<table class=tbl_01>";
					echo "<tr valign=center>";
					echo "<tr>";
					echo "<td >" . $row[ 'name' ] . "</td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td >" . $row[ 'station_name' ] . "</td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td >" . $row[ 'address' ] . "</td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td >" . $row[ 'access' ] . "</td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td >" . $row[ 'open' ] . "</td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td >" . $row[ 'close' ] . "</td>";
					echo "</tr>";
					echo "<tr>";
					$rink=$row[ 'rink' ];
					echo "<td ><a href ='$rink'>$rink</a></td>";
					echo "</tr>";
					echo "<tr>";
					$img = $row[ 'img' ];
					echo "<td ><img src='$img'></td>";
					echo "</tr>";
					?>
		<form action=favo_eturan.php method=get>
			<input type="hidden" name="id" value="<?php print $id;?>">
			<input type="hidden" name="name" value="<?php print $row[ 'name' ]?>">
			<input type="hidden" name="station_name" value="<?php print $row[ 'station_name' ]?>">
			<input type="hidden" name="address" value="<?php print $row[ 'address' ]?>">
			<input type="hidden" name="access" value="<?php print $row[ 'access' ]?>">
			<input type="hidden" name="open" value="<?php print $row[ 'open' ] ?>">
			<input type="hidden" name="close" value="<?php print $row[ 'close' ]?>">
			<input type="hidden" name="rink" value="<?php print $row[ 'rink' ]?>">
			<input type="hidden" name="img" value="<?php print $row[ 'img' ]?>">
			</td>
			<td><input type=submit border=0 name=sakuzyo value="削除">
			</td>
			</tr>
			</table>
		</form>

		<?php
		}

		} catch ( Exception $e ) {

			echo $e->getMessage() . PHP_EOL;
		}
		}

		?>

</body>

</html>