<?php

function h($str) { return htmlspecialchars($str, ENT_QUOTES, "UTF-8"); }

session_start();
$keyid = 'd9586180e48e50c8159b2d69fcde1498';
$hit_per_page = '10'; //何件表示するか

if(isset($_SESSION["id"])){
   print "ようこそ".h($_SESSION["name"])."さん";
	$id = $_SESSION["id"];
	print '</br>';
		//echo "<li id= select_back >";
		print 	'<div style="display:inline-flex">';
		?>
		

	<form action="CMP.php" method="get">
		<input id="button_select_1_plan" type="submit" value="ホットペッパーで検索">
	</form>
	<span style="margin-right: 5em;"></span>
	<!--<p><a href="CMP.php">ホットペッパーで検索</a></p>-->
	<!--<input type="button" value="ホットペッパーで検索" href="CMP.php">-->
	
	<form action="kuse.php" method="get">
		<input id="button_select_2_plan" type="submit" value="ぐるなびで検索">
	</form>
	<span style="margin-right: 5em;"></span>
	<form action="tweet-serch.php" method="get">
		<input id="button_select_3_plan" type="submit" value="ツイッターで検索">
	</form>
	<!--<p><a href="kuse.php">ぐるなびで検索</a></p></br>-->
	<!--  <p><a href="tweet-serch.php">Twitterで検</a></p>-->
	<span style="margin-right: 5em;"></span>
	<form action="new_plans.php" method="get">
		<input id="button_select_4_plan" type="submit" value="カレンダーを確認">
	</form>
	<span style="margin-right: 5em;"></span>
	<form action="logout.php" method="get">
		<input id="button_select_5_plan" type="submit" value="ログアウト">
	</form>
</div>
<?php
   }else{
        header("Location: login.php");
        exit;
   }

if ( isset( $_GET[ 'LargeServiceAreaCD' ] ) )$_SESSION[ 'area' ] = $_GET[ 'LargeServiceAreaCD' ];
if ( isset( $_GET[ 'LargeAreaCD' ] ) )$_SESSION[ 'pref' ] = $_GET[ 'LargeAreaCD' ];
if ( isset( $_GET[ 'MiddleAreaCD' ] ) )$_SESSION[ 'areacode_l' ] = $_GET[ 'MiddleAreaCD' ];
if ( isset( $_GET[ 'SmallAreaCD' ] ) )$_SESSION[ 'areacode_m' ] = $_GET[ 'SmallAreaCD' ];
if ( isset( $_GET[ 'freeword' ] ) )$_SESSION[ 'freeword' ] = $_GET[ 'freeword' ];


if ( isset( $_GET[ 'reset' ] ) ) {
	$_SESSION = array();
	session_destroy();
}


$format = 'xml';
if ( isset( $_SESSION[ 'area' ] ) ) {
	$url = 'http://api.gnavi.co.jp/RestSearchAPI/20150630/?keyid=' . $keyid . '&hit_per_page=' . $hit_per_page . '&area=' . $_SESSION[ 'area' ] . '&format=' . $format;
	if(isset( $_SESSION['freeword'] )){
		$url = 'http://api.gnavi.co.jp/RestSearchAPI/20150630/?keyid=' . $keyid . '&hit_per_page=' . $hit_per_page .'&area=' . $_SESSION[ 'area' ] . '&freeword=' . $_SESSION['freeword']   . '&format=' . $format;
	}
	$large_area_choise = 'https://api.gnavi.co.jp/master/PrefSearchAPI/20150630/?keyid=' . $keyid ;
	$xml_large = simplexml_load_file( $large_area_choise );

	if ( isset( $_SESSION[ 'pref' ] ) ) {
		$url = 'http://api.gnavi.co.jp/RestSearchAPI/20150630/?keyid=' . $keyid . '&hit_per_page=' . $hit_per_page . '&pref=' . $_SESSION[ 'pref' ] . '&format=' . $format;
	if(isset( $_SESSION['freeword'] )){
		$url = 'http://api.gnavi.co.jp/RestSearchAPI/20150630/?keyid=' . $keyid . '&hit_per_page=' . $hit_per_page . '&pref=' . $_SESSION[ 'pref' ] . '&freeword=' . $_SESSION['freeword']   . '&format=' . $format;
		}
		$middle_area_choise = 'https://api.gnavi.co.jp/master/GAreaLargeSearchAPI/20150630/?keyid=' . $keyid ;
		$xml_middle = simplexml_load_file( $middle_area_choise );
	}
	if ( isset( $_SESSION[ 'lsa' ] ) ) {
		if ( $_SESSION[ 'lsa' ] != $_SESSION[ 'area' ] ) {
			$_SESSION[ 'pref' ] = array();
		}
	}

	if ( isset( $_GET[ 'MiddleAreaCD' ] ) ) {
		$url = 'http://api.gnavi.co.jp/RestSearchAPI/20150630/?keyid=' . $keyid . '&hit_per_page=' . $hit_per_page . '&areacode_l=' . $_SESSION[ 'areacode_l' ] . '&format=' . $format;
		if(isset( $_SESSION['freeword'] )){
		$url = 'http://api.gnavi.co.jp/RestSearchAPI/20150630/?keyid=' . $keyid . '&hit_per_page=' . $hit_per_page . '&areacode_l=' . $_SESSION[ 'areacode_l' ]. '&freeword=' . $_SESSION['freeword']   . '&format=' . $format;
		}
		$small_area_choise = 'https://api.gnavi.co.jp/master/GAreaMiddleSearchAPI/20150630/?keyid=' . $keyid ;
		$xml_small = simplexml_load_file( $small_area_choise );
	}
	if ( isset( $_SESSION[ 'lsa' ] ) ) {
		if ( $_SESSION[ 'lsa' ] != $_SESSION[ 'area' ] ) {
			$_SESSION[ 'areacode_l' ] = array();
		}
	}

	if ( isset( $_GET[ 'SmallAreaCD' ] ) ) {
		$url = 'http://api.gnavi.co.jp/RestSearchAPI/20150630/?keyid=' . $keyid . '&hit_per_page=' . $hit_per_page . '&areacode_m=' . $_SESSION[ 'areacode_m' ] . '&format=' . $format;
		if(isset( $_SESSION['freeword'] )){
		$url = 'http://api.gnavi.co.jp/RestSearchAPI/20150630/?keyid=' . $keyid . '&hit_per_page=' . $hit_per_page . '&areacode_m=' . $_SESSION[ 'areacode_m' ] . '&freeword=' . $_SESSION['freeword']   . '&format=' . $format;
		}
		$small_area_choise = 'https://api.gnavi.co.jp/master/GAreaMiddleSearchAPI/20150630/?keyid=' . $keyid ;
		$xml_small = simplexml_load_file( $small_area_choise );
	}
	$_SESSION[ 'lsa' ] = $_SESSION[ 'area' ];
	$xml = simplexml_load_file( $url );
	$total_count = $xml->total_hit_count;

}else if ( isset( $_SESSION['freeword'] ) ) {
	$url = 'http://api.gnavi.co.jp/RestSearchAPI/20150630/?keyid=' . $keyid . '&hit_per_page=' . $hit_per_page . '&freeword=' . $_SESSION['freeword']  . '&format=' . $format;
	$xml = simplexml_load_file( $url );
	$total_count = $xml->total_hit_count;

}

if ( isset( $_GET[ "favo" ] ) ) {
	if ( isset( $_GET[ 'name' ] ) )$name = $_GET[ 'name' ];
	if ( isset( $_SESSION[ 'id' ] ) )$id = $_SESSION[ 'id' ];
	if ( isset( $_GET[ 'station_name' ] ) )$station_name = $_GET[ 'station_name' ];
	if ( isset( $_GET[ 'address' ] ) )$address = $_GET[ 'address' ];
	if ( isset( $_GET[ 'access' ] ) )$access = $_GET[ 'access' ];
	if ( isset( $_GET[ 'open' ] ) )$close = $_GET[ 'open' ];
	if ( isset( $_GET[ 'close' ] ) )$open = $_GET[ 'close' ];
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
		$stmt = $pdo->prepare( "INSERT INTO favo_id VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)" );
		$stmt->execute( array( $id, $name, $station_name, $address, $access, $open, $close, $rink, $img ));		   


	} catch ( Exception $e ) {

		echo $e->getMessage() . PHP_EOL;
	}
}

?>





<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
	<title>Google Maps V3</title>
	<script src="jquery-3.2.1.min.js" type="text/javascript"></script>
 　　　<script src="CMP.js" type="text/javascript"></script>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	 <link rel="stylesheet" href="gurunabi style.css">
</head>

<body id="body">
<ul>
<li id="kensaku">

	<h2>検索</h2>
	<form action=kuse.php method=get>
		<select name="LargeServiceAreaCD" size="10" style="width: 102px;" onchange="submit(this.form)">
			<option value="AREA150">北海道</option>
			<option value="AREA160">東北</option>
			<option value="AREA110">関東</option>
			<option value="AREA170">北陸</option>
			<option value="AREA130">中部</option>
			<option value="AREA120">関西</option>
			<option value="AREA180">中国</option>
			<option value="AREA190">四国</option>
			<option value="AREA140">九州</option>
			<option value="AREA200">沖縄</option>
		</select>
		<select name="LargeAreaCD" size="10" style="width: 102px;" onchange="submit(this.form)">
			<?php
			if ( isset( $_SESSION[ 'area' ] ) ) {
				foreach ( $xml_large->pref as $rest ) {
					foreach ( $rest->area_code as $rest_next ) {
						if ( ( $rest_next ) == $_SESSION[ 'area' ] ) {
							print "<option value='".$rest->pref_code."'>" . $rest->pref_name . "</option>";
						}
					}
				}

			}
			?>
		</select>
		<select name="MiddleAreaCD" size="10" style="width: 102px;" onchange="submit(this.form)">
			<?php
			if ( isset( $_SESSION[ 'pref' ] ) ) {
				foreach ( $xml_middle->garea_large as $rest ) {
					foreach ( $rest->pref as $rest_next ) {
						if ( ( $rest_next->pref_code ) == $_SESSION[ 'pref' ] ) {
							print "<option value='" . $rest->areacode_l . "'>" . $rest->areaname_l . "</option>";
						}
					}
				}

			}
			?>
		</select>
		<select name="SmallAreaCD" size="10" style="width: 102px;" onchange="submit(this.form)">
			<?php
			if ( isset( $_SESSION[ 'areacode_l' ] ) ) {
				foreach ( $xml_small->garea_middle as $rest ) {
					foreach ( $rest->garea_large as $rest_next ) {
						if ( ( $rest_next->areacode_l ) == $_SESSION[ 'areacode_l' ] ) {
							print "<option value='" . $rest->areacode_m . "'>" . $rest->areaname_m . "</option>";
						}
					}
				}

			}
			?>
		</select>
		<form action=kuse.php method=get>
			<input id="button" type=submit border=0 name=reset value="リセット">
		</form>
		<form action=favo_eturan.php method=get>
			<input type="hidden" name="id" value="<?php print $id;?>">
			<input id="fav-button" type=submit border=0 name=favo_eturan value="お気に入り確認">
		</form>
		
		</li>

       <li id=keyword>

		<h2>キーワード検索</h2>
		<form action=kuse.php method=get>
			<table border=0 cellpadding=0 cellspacing=0>
				<tr>
					<td>キーワード:</td>
					<td><input type=text size=20 name=freeword value =<?php
							   if(isset($_SESSION['freeword'])){
								   print $_SESSION['freeword'];
							   }?>>
					</td>
				</tr>
				<tr>
					<td> </td>
					<td><input type=submit border=0 value="検索">
					</td>
				</tr>
			</table>
		</form>
	

	</form>
	</li>
	</ul>
    <ul>
	<li>

	<?php
	if ( isset( $_SESSION[ 'area' ] ) || isset( $_SESSION[ 'pref' ] ) || isset( $_SESSION[ 'areacode_l' ] ) || isset( $_SESSION[ 'areacode_m' ] )|| isset( $_SESSION[ 'freeword' ] ) ) {
		if ( !$xml->rest ) {
			echo 'We can not find!!';
		} else {
			foreach ( $xml->rest as $rest ) {
				$access_1= $rest->access->line.$rest->access->station.' 徒歩'.$rest->access->walk.'分';
				?>

			<?php
				echo '<table class=tbl_01>';
				echo '<tr>';
				echo '<td>';
				echo $rest->name;
				echo '</td>';
				echo '</tr>';
				echo '<tr class="map">';
				echo '<td>';
				echo "最寄り駅：".$rest->access->station;
				echo '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<td>';
				echo "住所：".$rest->address;
				echo '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<td>';
				echo "アクセス：".$rest->access->line.$rest->access->station.' 徒歩'.$rest->access->walk.'分';
				echo '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<td>';
				echo "営業時間：".$rest->opentime."</br>";
				echo "定休日：".$rest->holiday;
				echo '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<td>';
				$rink = $rest->url;
				echo "<a href ='$rink'>".$rink."</a>";
				echo '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<td>';
				$img = $rest->image_url->shop_image1;
				//$imghyouzi = file_get_contents($img);
				echo "<img src='$img'>";
				echo '</td>';
				echo '</tr>';
				echo '</table>';
				?>
				
					<form action=kuse.php method=get>
		<input type="hidden" name="id" value="<?php print $id;?>">
		<input type="hidden" name="name" value="<?php print $rest->name;?>">
		<input type="hidden" name="station_name" value="<?php print $rest->access->station;?>">
		<input type="hidden" name="address" value="<?php print $rest->address;?>">
		<input type="hidden" name="access" value="<?php print $access_1;?>">
		<input type="hidden" name="open" value="<?php print $rest->opentime;?>">
		<input type="hidden" name="close" value="<?php print $rest->holiday;?>">
		<input type="hidden" name="rink" value="<?php print $rest->url;?>">
		<input type="hidden" name="img" value="<?php print $rest->image_url->shop_image1;?>">
	    <input type="submit" class = "favo_button" name="favo" value="♡">
	</form>
		
	<form action=new_plans.php method=get>
		<input type="hidden" name="id" value="<?php print $id;?>">
		<input type="hidden" name="name" value="<?php print $rest->name;?>">
		<input type="hidden" name="station_name" value="<?php print $rest->access->station;?>">
		<input type="hidden" name="url" value="<?php print $rest->url;?>">
         <input type="submit" class = "add_to_calendar" name="new_plans_add" value="Add to calendar">
	</form>

<?php
			}
		}
	}
	?>
		
</li>
</ul>

</body>

</html>
