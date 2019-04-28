<?php
function h($str) { return htmlspecialchars($str, ENT_QUOTES, "UTF-8"); }

session_start();
$key = 'd10523f81e0f1887';
$count = '10';
$ok = 1;

//$db = new PDO( "sqlite:favo.sqlite" );

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

if ( isset( $_GET[ 'LargeServiceAreaCD' ] ) )$_SESSION[ 'large_service_area' ] = $_GET[ 'LargeServiceAreaCD' ];
if ( isset( $_GET[ 'LargeAreaCD' ] ) )$_SESSION[ 'large_area' ] = $_GET[ 'LargeAreaCD' ];
if ( isset( $_GET[ 'MiddleAreaCD' ] ) )$_SESSION[ 'middle_area' ] = $_GET[ 'MiddleAreaCD' ];
if ( isset( $_GET[ 'SmallAreaCD' ] ) )$_SESSION[ 'small_area' ] = $_GET[ 'SmallAreaCD' ];
if ( isset( $_GET[ 'keyword' ] ) )$_SESSION[ 'keyword' ] = $_GET[ 'keyword' ];

if ( isset( $_GET[ 'reset' ] ) ) {
	$_SESSION = array();
	session_destroy();
}

$start = 1;
$format = 'xml';

if ( isset( $_SESSION[ 'large_service_area' ] ) ) {
	$url = 'http://webservice.recruit.co.jp/hotpepper/gourmet/v1/?key=' . $key . '&count=' . $count . '&large_service_area=' . $_SESSION[ 'large_service_area' ] . '&start=' . $start . '&format=' . $format;
	if ( isset( $_SESSION[ 'keyword' ] ) ) {
		$url = 'http://webservice.recruit.co.jp/hotpepper/gourmet/v1/?key=' . $key . '&count=' . $count . '&large_service_area=' . $_SESSION[ 'large_service_area' ] . '&keyword=' . $_SESSION[ 'keyword' ] . '&start=' . $start . '&format=' . $format;
	}
	$large_area_choise = 'http://webservice.recruit.co.jp/hotpepper/large_area/v1?key=' . $key . '&large_service_area=' . $_SESSION[ 'large_service_area' ];
	$xml_large = simplexml_load_file( $large_area_choise );
	if ( isset( $_SESSION[ 'lsa' ] ) ) {
		if ( $_SESSION[ 'lsa' ] != $_SESSION[ 'large_service_area' ] ) {
			unset( $_SESSION[ 'large_area' ] );
			unset( $_SESSION[ 'middle_area' ] );
			unset( $_SESSION[ 'small_area' ] );
		}
		} else if ( isset( $_SESSION[ 'la' ] ) ) {
			if ( $_SESSION[ 'la' ] != $_SESSION[ 'large_area' ] ) {
				unset( $_SESSION[ 'middle_area' ] );
				unset( $_SESSION[ 'small_area' ] );
			} 
	}else if ( isset( $_SESSION[ 'ma' ] ) ) {
				if ( $_SESSION[ 'ma' ] != $_SESSION[ 'middle_area' ] ) {
					unset( $_SESSION[ 'small_area' ] );
				}
			}
	
	if ( isset( $_SESSION[ 'large_area' ] ) ) {
		$url = 'http://webservice.recruit.co.jp/hotpepper/gourmet/v1/?key=' . $key . '&count=' . $count . '&large_area=' . $_SESSION[ 'large_area' ] . '&start=' . $start . '&format=' . $format;
		if ( isset( $_SESSION[ 'keyword' ] ) ) {
			$url = 'http://webservice.recruit.co.jp/hotpepper/gourmet/v1/?key=' . $key . '&count=' . $count . '&large_area=' . $_SESSION[ 'large_area' ] . '&keyword=' . $_SESSION[ 'keyword' ] . '&start=' . $start . '&format=' . $format;
		}
		$middle_area_choise = 'http://webservice.recruit.co.jp/hotpepper/middle_area/v1?key=' . $key . '&large_area=' . $_SESSION[ 'large_area' ];
		$xml_middle = simplexml_load_file( $middle_area_choise );
	}

	if ( isset( $_SESSION[ 'middle_area' ] ) ) {
		$url = 'http://webservice.recruit.co.jp/hotpepper/gourmet/v1/?key=' . $key . '&count=' . $count . '&middle_area=' . $_SESSION[ 'middle_area' ] . '&start=' . $start . '&format=' . $format;
		if ( isset( $_SESSION[ 'keyword' ] ) ) {
			$url = 'http://webservice.recruit.co.jp/hotpepper/gourmet/v1/?key=' . $key . '&count=' . $count . '&middle_area=' . $_SESSION[ 'middle_area' ] . '&keyword=' . $_SESSION[ 'keyword' ] . '&start=' . $start . '&format=' . $format;
		}
		$small_area_choise = 'http://webservice.recruit.co.jp/hotpepper/small_area/v1?key=' . $key . '&middle_area=' . $_SESSION[ 'middle_area' ];
		$xml_small = simplexml_load_file( $small_area_choise );
	}

	if ( isset( $_SESSION[ 'small_area' ] ) ) {
		$url = 'http://webservice.recruit.co.jp/hotpepper/gourmet/v1/?key=' . $key . '&count=' . $count . '&small_area=' . $_SESSION[ 'small_area' ] . '&start=' . $start . '&format=' . $format;
		if ( isset( $_SESSION[ 'keyword' ] ) ) {
			$url = 'http://webservice.recruit.co.jp/hotpepper/gourmet/v1/?key=' . $key . '&count=' . $count . '&small_area=' . $_SESSION[ 'small_area' ] . '&keyword=' . $_SESSION[ 'keyword' ] . '&start=' . $start . '&format=' . $format;
		}
		$small_area_choise = 'http://webservice.recruit.co.jp/hotpepper/small_area/v1?key=' . $key . '&middle_area=' . $_SESSION[ 'middle_area' ];
		$xml_small = simplexml_load_file( $small_area_choise );
	}

	$_SESSION[ 'lsa' ] = $_SESSION[ 'large_service_area' ];
	if ( isset( $_SESSION[ 'large_area' ] ) )$_SESSION[ 'la' ] = $_SESSION[ 'large_area' ];
	if ( isset( $_SESSION[ 'middle_area' ] ) )$_SESSION[ 'ma' ] = $_SESSION[ 'middle_area' ];
	$xml = simplexml_load_file( $url );
	$total_count = $xml->results_available;

} else if ( isset( $_SESSION[ 'keyword' ] ) ) {
	$url = 'http://webservice.recruit.co.jp/hotpepper/gourmet/v1/?key=' . $key . '&count=' . $count . '&keyword=' . $_SESSION[ 'keyword' ] . '&start=' . $start . '&format=' . $format;
	$xml = simplexml_load_file( $url );
	$total_count = $xml->results_available;

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


<!DOCTYPE html PUBLIC "-//W3C//DTD HT
ML 4.01 Transitional//EN">
<html>

<head>
	<title>Google Maps V3</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<script src="jquery-3.2.1.min.js" type="text/javascript"></script>
 　　　<script src="CMP.js" type="text/javascript"></script>
	 <link rel="stylesheet" href="gurunabi style.css">
	
</head>

<body id="body">
<ul>
<li id="kensaku">
	<h2>検索</h2>
	<form action=CMP.php method=get>
		<select name="LargeServiceAreaCD" size="10" style="width: 102px;" onchange="submit(this.form)">
			<option value="SS40">北海道</option>
			<option value="SS50">東北</option>
			<option value="SS60">北陸・甲信越</option>
			<option value="SS10">関東</option>
			<option value="SS20">関西</option>
			<option value="SS30">東海</option>
			<option value="SS70">中国</option>
			<option value="SS80">四国</option>
			<option value="SS90">九州</option>
		</select>
		<select name="LargeAreaCD" size="10" style="width: 102px;" onchange="submit(this.form)">
			<?php
			if ( isset( $_SESSION[ 'large_service_area' ] ) ) {
				foreach ( $xml_large->large_area as $rest ) {
					foreach ( $rest->large_service_area as $rest_next ) {
						if ( ( $rest_next->code ) == $_SESSION[ 'large_service_area' ] ) {
							print "<option value='" . $rest->code . "'>" . $rest->name . "</option>";
						}
					}
				}

			}
			?>
		</select>
		<select name="MiddleAreaCD" size="10" style="width: 102px;" onchange="submit(this.form)">
			<?php
			if ( isset( $_SESSION[ 'large_area' ] ) ) {
				foreach ( $xml_middle->middle_area as $rest ) {
					foreach ( $rest->large_area as $rest_next ) {
						if ( ( $rest_next->code ) == $_SESSION[ 'large_area' ] ) {
							print "<option value='" . $rest->code . "'>" . $rest->name . "</option>";
						}
					}
				}

			}
			?>
		</select>
		<select name="SmallAreaCD" size="10" style="width: 102px;" onchange="submit(this.form)">
			<?php
			if ( isset( $_SESSION[ 'middle_area' ] ) ) {
				foreach ( $xml_small->small_area as $rest ) {
					foreach ( $rest->middle_area as $rest_next ) {
						if ( ( $rest_next->code ) == $_SESSION[ 'middle_area' ] ) {
							print "<option value='" . $rest->code . "'>" . $rest->name . "</option>";
						}
					}
				}

			}
			?>
		</select>
		<form action=CMP.php method=get>
			<input id="button" type=submit border=0 name=reset value="リセット">
		</form>
		<form action=favo_eturan.php method=get>
			<input type="hidden" name="id" value="<?php print $id;?>">
			<input id="fav-button" type=submit border=0 name=favo_eturan value="お気に入り確認">
		</form>
		</li>

       <li id=keyword>
		<h2>キーワード検索</h2>
		<form action=CMP.php method=get>
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
	if ( isset( $_SESSION[ 'large_service_area' ] ) || isset( $_SESSION[ 'large_area' ] ) || isset( $_SESSION[ 'middle_area' ] ) || isset( $_SESSION[ 'small_area' ] ) || isset( $_SESSION[ 'keyword' ] ) ) {
		if ( !$xml->shop ) {
			echo 'We can not find!!';
		} else {
			foreach ( $xml->shop as $rest ) {
				echo '<tr>';
				echo '<td>';
				
	echo '<table class=tbl_01>';
	echo '</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td>';
	echo $rest->name;
	echo '</td>';
	echo '</tr>';
	echo '<tr class="map">';
	echo '<td>';
	echo "最寄り駅：" . $rest->station_name;
	echo '</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td>';
	echo "住所：" . $rest->address;
	echo '</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td>';
	echo "アクセス：" . $rest->access;
	echo '</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td>';
	echo "営業時間：" . $rest->open . "</br>";
	echo "定休日：" . $rest->close;
	echo '</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td>';
	$rink = $rest->urls->pc;
	echo "<a href ='$rink'>" . $rink . "</a>";
	echo '</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td>';
	$img = $rest->photo->pc->m;
	//$imghyouzi = file_get_contents($img);
	echo "<img src='$img'>";
	echo '</td>';
	echo '</tr>';
	echo '</table>';
				
	?>
	<form action=CMP.php method=get>
		<input type="hidden" name="id" value="<?php print $id;?>">
		<input type="hidden" name="name" value="<?php print $rest->name;?>">
		<input type="hidden" name="station_name" value="<?php print $rest->station_name;?>">
		<input type="hidden" name="address" value="<?php print $rest->address;?>">
		<input type="hidden" name="access" value="<?php print $rest->access;?>">
		<input type="hidden" name="open" value="<?php print $rest->open;?>">
		<input type="hidden" name="close" value="<?php print $rest->close;?>">
		<input type="hidden" name="rink" value="<?php print $rest->urls->pc;?>">
		<input type="hidden" name="img" value="<?php print $rest->photo->pc->m;?>">
		<input type="submit" class = "favo_button" name="favo" value="♡">
		
	</form>
	<form action=new_plans.php method=get>
		<input type="hidden" name="id" value="<?php print $id;?>">
		<input type="hidden" name="name" value="<?php print $rest->name;?>">
		<input type="hidden" name="station_name" value="<?php print $rest->station_name;?>">
		<input type="hidden" name="url" value="<?php print $rest->urls->pc;?>">
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