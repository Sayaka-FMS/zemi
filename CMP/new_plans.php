<?php
session_start();
?>

<html>

<head>
	<title>カレンダー</title>


	<meta charset="utf-8">
	<title>Login form</title>
	<script src="jquery-3.2.1.min.js" type="text/javascript"></script>
    <script src="CMP.js" type="text/javascript"></script>
    <style type="text/css">

		.calendar a:link {
			color: #3366FF;
			background-color: transparent;
			text-decoration: none;
			font-weight: bold;
		}
		
		.calendar a:visited {
			color: #2B318F;
			background-color: transparent;
			text-decoration: none;
			font-weight: bold;
		}
		
		.calendar a:hover {
			color: #00BFFF;
			background-color: transparent;
			text-decoration: underline;
		}
		
		body {
			color: #333333;
			background-color: #FAFAD2;
		}
		
		.calendar table{
			border: 1px solid #CCCCCC;
			border-collapse: collapse;
			margin-bottom: 1em;

		}
		
		.calendar td{
			border: 1px solid #CCCCCC;
			height: 5em;
			width: 5em;
			vertical-align: middle;
			padding-left: 1em;
			padding-top: 2px;
			padding-right: 1em;
			padding-bottom: 2px;
			background-color: white;
			
		}
		
		.calendar th {
			border: 1px solid #CCCCCC;
			color: #333333;
			background-color: #F0F0F0;
			padding: 5px;
		}
		

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

<body >
<ul>
	<?php

	//session_start();
	//「autoload.php」読み込み
	//「twitteroauth/」フォルダは本プログラムと同階層に配置
	require_once dirname( __FILE__ ) . '/twitteroauth-master/autoload.php';
	use Abraham\ TwitterOAuth\ TwitterOAuth;

	//APIキー
	define( "Consumer_Key", "6Rq4Wd89Lbfif1bAIxkpRnKwJ" );
	define( "Consumer_Secret", "1QbeunVuUXh4pxSFBFATYiSFTSuxPMsdUaDiaJ5wtIJksDHV10" );

	$twitter = new TwitterOAuth( Consumer_Key, Consumer_Secret, $_SESSION[ 'token' ], $_SESSION[ 'token_secret' ] );
	if ( isset( $_GET[ "days_tweet" ] ) ) {
		$tweet = $_GET[ "days_tweet" ] . "\n" . $_GET[ "title_tweet" ] . "\n" . $_GET[ "place_tweet" ] . "\n" . $_GET[ "body_tweet" ] . "\n" . $_GET[ "url_tweet" ];
		$result = $twitter->post(
			"statuses/update",
			array( "status" => $tweet ) );
	} else if ( isset( $_GET[ "tweet" ] ) ) {
		$tweet = $_GET[ "tweet" ];
		$result = $twitter->post(
			"statuses/update",
			array( "status" => $tweet ) );
	}

	//if($twitter->getLastHttpCode() == 200) {
	if ( $twitter->getLastHttpCode() == 200 ) {
		// ツイート成功
		print "tweeted\n";
	} else {

	}

	//};



	if ( isset( $_SESSION[ 'id' ] ) ) {

		$id = $_SESSION[ 'id' ];
		$year = date( "Y", time() );
		$month = date( "m", time() );
		$day = date( "d" );

		$_SESSION[ 'month' ] = str_pad( $month, 2, 0, STR_PAD_LEFT );
		$_SESSION[ 'day' ] = str_pad( $day, 2, 0, STR_PAD_LEFT );


		if ( isset( $_GET[ 'year' ] ) ) {
			$_SESSION[ 'year' ] = $_GET[ 'year' ];
			$year = $_SESSION[ 'year' ];
		}
		if ( isset( $_GET[ 'month' ] ) ) {
			$_SESSION[ 'month' ] = $_GET[ 'month' ];
			if ( $_SESSION[ 'month' ] < 10 ) {
				$_SESSION[ 'month' ] = str_pad( $_SESSION[ 'month' ], 2, 0, STR_PAD_LEFT );
				//print str_pad( $_SESSION[ 'month' ], 2, 0, STR_PAD_LEFT );
			}
			$month = $_SESSION[ 'month' ];
			//print $month;
		}
		if ( isset( $_GET[ 'day' ] ) ) {
			//print "ok";
			$_SESSION[ 'day' ] = $_GET[ 'day' ];
			if ( $_SESSION[ 'day' ] < 10 ) {
				//$_SESSION['month'] = print $_SESSION['month'];
				$_SESSION[ 'day' ] = str_pad( $_SESSION[ 'day' ], 2, 0, STR_PAD_LEFT );
			}
			$day = $_SESSION[ 'day' ];
		}
			if ( isset( $_SESSION[ 'id' ] ) && isset( $_GET[ 'title' ] ) && isset( $_GET[ 'date_select' ] ) && isset( $_GET[ 'place' ] ) && isset( $_GET[ 'body' ] ) && isset( $_GET[ 'url' ] ) ) {
		if ( isset( $_GET[ 'date_select' ] ) )$date_select = $_GET[ 'date_select' ];
		if ( isset( $_GET[ 'title' ] ) )$title = $_GET[ 'title' ];
		if ( isset( $_GET[ 'place' ] ) )$place = $_GET[ 'place' ];
		if ( isset( $_GET[ 'body' ] ) )$body = $_GET[ 'body' ];
		if ( isset( $_GET[ 'url' ] ) )$url = $_GET[ 'url' ];


		try {
			$pdo = new PDO( 'sqlite:favo.db' );

			// SQL実行時にもエラーの代わりに例外を投げるように設定
			// (毎回if文を書く必要がなくなる)
			$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

			// デフォルトのフェッチモードを連想配列形式に設定 
			// (毎回PDO::FETCH_ASSOCを指定する必要が無くなる)
			$pdo->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
			$stmt = $pdo->prepare( "INSERT INTO new_plans VALUES (?, ?, ?, ?, ?, ?)" );
			$stmt->execute( array( $id, $date_select, $title, $place, $body, $url ) );


		} catch ( Exception $e ) {

			echo $e->getMessage() . PHP_EOL;
		}

	}


        //print 	'<div style="display:inline-flex">';
		print "ようこそ" . $_SESSION[ 'name' ] . "さん</br></br>";
		 print 	'<div style="display:inline-flex">';
		//echo "<li id= select_back >";
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
	<?php
	//echo "<p><a href='logout.php'>ログアウト</a></p>";
	//echo "</li>";
	echo "</div>";
	echo "</br>";
	echo "</br>";
	echo "</br>";
	
	?>





	<?php 
//$id = $_SESSION['id'];
// カレンダーの年月をタイムスタンプを使って指定 
if(isset($_SESSION['id'])){
if (isset($_GET["date"]) && $_GET["date"] != "") { 
  $_SESSION['date_timestamp'] = $_GET["date"]; 
}else if(isset($_GET["day"])){
} else { 
  $_SESSION['date_timestamp'] = time(); 
} 

$_SESSION['month'] = date("m", $_SESSION['date_timestamp']); 
$_SESSION['year'] = date("Y", $_SESSION['date_timestamp']); 

$first_date = mktime(0, 0, 0, $_SESSION['month'], 1, $_SESSION['year']);  
$last_date = mktime(0, 0, 0, $_SESSION['month'] + 1, 0, $_SESSION['year']);  

// 最初の日と最後の日の｢日にち」の部分だけ数字で取り出す。  
$first_day = date("j", $first_date);  
$last_day = date("j", $last_date);  

// 全ての日の曜日を得る。  
for($_SESSION['day'] = $first_day; $_SESSION['day'] <= $last_day; $_SESSION['day']++) {  
  $day_timestamp = mktime(0, 0, 0, $_SESSION['month'], $_SESSION['day'], $_SESSION['year']);  
  $week[$_SESSION['day']] = date("w", $day_timestamp);  
}  

?>


	</li>
	<li id= calendar_display >
		<table border="1" class=calendar>
			<tr>
				<th colspan="2"><a href="new_plans.php?date= 
<?php print (strtotime(" -1 month ", $first_date)); ?>">前月</a>
				</th>
				<th colspan="3">
					<?php print (date("Y", $_SESSION['date_timestamp']) . "年" . date("n", $_SESSION['date_timestamp']) . "月"); ?>
				</th>
				<th colspan="2"><a href="new_plans.php?date= 
<?php print (strtotime(" +1 month ", $first_date)); ?>">次月</a>
				</th>
			</tr>
			<tr>
				<th>日</th>
				<th>月</th>
				<th>火</th>
				<th>水</th>
				<th>木</th>
				<th>金</th>
				<th>土</th>
			</tr>
			<tr>
				<?php  
    // カレンダーの最初の空白部分  
    for ($i = 0; $i < $week[$first_day]; $i++) {  
      print ("<td></td>\n");  
    }  
     
    for ($_SESSION['day'] = $first_day; $_SESSION['day'] <= $last_day; $_SESSION['day']++) { 
      if ($week[$_SESSION['day']] == 0) { 
        print ("</tr>\n<tr>\n"); 
      }
	  	//print ("<td><a href='' onclick='document.forms.form1.submit();return false;'>".$_SESSION['day']."</a></td>\n");
      //$month_hidden = date("m", $_SESSION['date_timestamp']);
	  ?>
				<form name="form1" method="get" action="new_plans.php">
					<input type="hidden" name="year" value="<?php print date("Y", $_SESSION['date_timestamp']);?>">
					<input type="hidden" name="month" value="<?php print date("m", $_SESSION['date_timestamp']);?>">
					<input type="hidden" name="day" value="<?php print $_SESSION['day'];?>">
					<td> <input type="submit" name="add" value="<?php print $_SESSION['day'];?>">
				</form>
				<?php 
     try {   
		$pdo = new PDO( 'sqlite:favo.db' );
		$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$pdo->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
		 $id = $_SESSION['id'];
		$result = $pdo->query( "SELECT *from new_plans where id='$id'" );

				for ( $i = 0; $row = $result->fetch(); ++$i ) {
					$_SESSION['day']= str_pad( $_SESSION['day'], 2, 0, STR_PAD_LEFT );
                    $_days = $_SESSION['year']."-". $_SESSION['month']."-".$_SESSION['day'];
					//print $_days;
					if($_days==$row['days']){
						?> </br><a href="new_plans.php?days=<?php print $_days;?>">予定あり</a>
				<?php
				}

				}
				} catch ( Exception $e ) {
					echo $e->getMessage() . PHP_EOL;
				}
				?>
				</td>
				<?php 
		
		
	}
     // カレンダーの最後の空白部分  
    for ($i = $week[$last_day] + 1; $i < 7; $i++) {  
      print ("<td></td>\n");  
    }
	echo "</tr></table>";
	echo "</li>";
	
	
	print "<li id = new_plans>";
	print "<form action='new_plans.php' method='get'>";
	print "日付</br>";
	$days_add = $year.'-'.$month.'-'.$day;	
	//print $days_add;	
	print "<input type='date' name='date_select' value='$days_add'></br>";
	?> タイトル </br>
	<input type="text" name="title" <?php if(isset($_GET[ 'name'])){ print "value=".$_GET[ 'name']; } ?>>
	</br>
	場所 </br>
	<input type="text" name="place" <?php if(isset($_GET[ 'station_name'])){ print "value=".$_GET[ 'station_name']; } ?>>
	</br>
	内容（ 誰と　 何時） </br>
	<textarea name="body"></textarea>
	</br>
	URL </br>
	<input type="text" name="url" <?php if(isset($_GET[ 'url'])){ print "value=".$_GET[ 'url']; } ?>>
	</br>
	<input type=submit border=0 value="追加">

	</form>

	<form action='new_plans.php' method='get'>
		<textarea name="tweet"></textarea>
		</br>
		<input type="submit" method="get" value="tweet">
	</form>
	
		<li id=new_plans_display>
	<?php
    		//print "<li id = new_plans_display>";

	
	if ( isset( $_GET[ 'delete' ] ) ) {
		$days_1 = $_GET[ 'days_1' ];
		$title_1 = $_GET[ 'title_1' ];
		try {
			$pdo = new PDO( 'sqlite:favo.db' );
			$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$pdo->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
			$id = $_SESSION[ 'id' ];
			$pdo->query( "DELETE FROM  new_plans  WHERE id='$id' AND days = '$days_1' AND title= '$title_1'" );
		} catch ( Exception $e ) {

			echo $e->getMessage() . PHP_EOL;
		}
	}
	if ( isset( $_GET[ 'days' ] ) ) {
		$_days = $_GET[ 'days' ];
		try {
			$pdo = new PDO( 'sqlite:favo.db' );
			$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$pdo->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
			$id = $_SESSION[ 'id' ];
			$result = $pdo->query( "SELECT *from new_plans where id='$id' and  days = '$_days'" );
			for ( $i = 0; $row = $result->fetch(); ++$i ) {
				echo "<table class='tbl_01'>";
				
				echo "<tr>";
				echo "<th>";
				echo "日にち";				
				echo "</th>";
				//echo "</tr>";
				//echo "<tr>";				
				echo "<td>". $row[ 'days' ] . "</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<th>";
				echo "タイトル";
				echo "</th>";
				//echo "</tr>";
				//echo "<tr>";				
				echo "<td>" . $row[ 'title' ] . "</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<th>";
				echo "場所";
				echo "</th>";
				//echo "</tr>";
				//echo "<tr>";				
				echo "<td >" . $row[ 'place' ] . "</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<th>";
				echo "内容";
				echo "</th>";
				//echo "</tr>";
				//echo "<tr>";
				echo "<td >" . $row[ 'body' ] . "</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<th>";
				echo "URL";
				echo "</th>";
			//	echo "<tr>";
				echo "</td>";
				$rink = $row[ 'url' ];
				echo "<td ><a href ='$rink'>$rink</a></td>";
				echo "</tr>";
				echo "</table>";
				?>
	<form name="sakuzyo" method="get" action="new_plans.php">
		<input type="hidden" name="days_1" value="<?php print $row[ 'days' ] ;?>">
		<input type="hidden" name="title_1" value="<?php print $row[ 'title' ];?>">
		<td> <input type="submit" name="delete" value="削除">
	</form>
	<form action="new_plans.php" method="get">
		<input type="hidden" name="days_tweet" value="<?php print $row[ 'days' ] ;?>">
		<input type="hidden" name="title_tweet" value="<?php print $row[ 'title' ];?>">
		<input type="hidden" name="place_tweet" value="<?php print $row[ 'place' ] ;?>">
		<input type="hidden" name="body_tweet" value="<?php print $row[ 'body' ];?>">
		<input type="hidden" name="url_tweet" value="<?php print $row[ 'url' ];?>">
		<input type="submit" method="get" value="予定をtweet">
	</form>
	<?php
	}

  	 print "</li>";	
	} catch ( Exception $e ) {

		echo $e->getMessage() . PHP_EOL;
	}
  
	}


	
}
	}else{

	header("Location: login.php");
}

    ?>
    
    

</ul>
</body>

</html>