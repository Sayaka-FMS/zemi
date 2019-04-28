<?php
session_start();
if(isset($_SESSION["id"])){
   }else{
        header("Location: login.php");
        exit;
   }
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
              <meta charset="UTF-8">
			  <script src="jquery-3.2.1.min.js" type="text/javascript"></script>
			  　　　<script src="CMP.js" type="text/javascript"></script>
			  <link rel="stylesheet" href="gurunabi style.css">
       <p> ようこそ<?php echo $_SESSION['name']?> さん<p>

	   <div style="display:inline-flex">
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
<h2>ツイッターで検索</h2>
<link rel="stylesheet" href="tweet-serch.css">
	   <!--	<a href='select.php'>選択画面に戻る</a>
		<a href='logout.php'>ログアウトする</a>-->
     <form action="tweet-serch.php" method="get">
    <input type="text" name="serch" value =<?php if(isset($_SESSION['serch'])){print $_SESSION['serch'];}?>>
    <input class="button" type="submit" method="get"value="検索">
<?php echo "<p><a href='tweet.php'>つぶやく</a></p>";
?>
    </form>

     </br><form action=favo_eturan_twitter.php method=get>
			<input type="hidden" name="id" value="<?php print $_SESSION["id"];?>">
			<input class='button2' type=submit border=0 name=favo_eturan_twitter value="お気に入り確認">
		</form>
<script src="jquery-3.2.1.min.js" type="text/javascript"></script>
 　　　<script src="CMP.js" type="text/javascript"></script>

    </head>
<body id="body">

</html>


<?php

if ( isset( $_GET[ "favo" ] ) ) {
	if ( isset( $_SESSION[ 'id' ] ) )$id = $_SESSION[ 'id' ];
	if ( isset( $_GET[ 'icon_url' ] ) )$icon_url = $_GET[ 'icon_url' ];
	if ( isset( $_GET[ 'screen_name' ] ) )$screen_name = $_GET[ 'screen_name' ];
	if ( isset( $_GET[ 'updated' ] ) )$updated = $_GET[ 'updated' ];
	if ( isset( $_GET[ 'tweet_id' ] ) )$tweet_id = $_GET[ 'tweet_id' ];
	if ( isset( $_GET[ 'text' ] ) )$text = $_GET[ 'text' ];
	if ( isset( $_GET[ 'url' ] ) )$url = $_GET[ 'url' ];
	try {
		$pdo = new PDO( 'sqlite:favo.db' );

		// SQL実行時にもエラーの代わりに例外を投げるように設定
		// (毎回if文を書く必要がなくなる)
		$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

		// デフォルトのフェッチモードを連想配列形式に設定
		// (毎回PDO::FETCH_ASSOCを指定する必要が無くなる)
		$pdo->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
		$stmt = $pdo->prepare( "INSERT INTO favo_id_twitter VALUES (?, ?, ?, ?, ?, ?, ?)" );
		$stmt->execute( array( $id, $icon_url,$screen_name, $updated, $tweet_id, $text , $url));


	} catch ( Exception $e ) {

		echo $e->getMessage() . PHP_EOL;
	}
}


//session_start();
require 'TwistOAuth.phar'; //ライブラリ読み込み

/////////APIキー///////////////////////////////////////////////////////////////
/**/$consumer_key = 'V7G53Rh0zB4P3zKZshRlV3IY4';                            /**/ 
/**/$consumer_secret = 'Oi0PQ0zEgHM1JzmK4jGp3MKKZUurMjHKubxWBwjwXP39nVvctA';/**/
/*$access_token = '4894994366-t2kDFv3bc6UGK8vuSppRjUKPkc0kfWViujwro2Q';   /**/
/*$access_token_secret = 'sR919UjyFM3oGFFJ4CiA2y8ivJeNdVHTJir2urHHH2VTu'; /**/
////////////////////////////////////////////////////////////////////////////////


//$connection = new TwistOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
$connection = new TwistOAuth($consumer_key, $consumer_secret, $_SESSION['token'], $_SESSION['token_secret']);
date_default_timezone_set("Asia/Tokyo");
if(isset($_GET["serch"])||isset($_SESSION["serch"])){
if(isset($_GET["serch"])){
 // print $_GET["serch"];
$_SESSION['serch']=$_GET["serch"];
}
// キーワードによるツイート検索
$tweets_params = ['q' =>$_SESSION['serch'] ];
$tweets = $connection->get('search/tweets', $tweets_params)->statuses;

foreach ($tweets as $value) {
    $text = htmlspecialchars($value->text, ENT_QUOTES, 'UTF-8', false);
    // 検索キーワードをマーキング
    $keywords = preg_split('/,|\sOR\s/', $tweets_params['q']); //配列化
    foreach ($keywords as $key) {
        $text = str_ireplace($key, '<span class="keyword">'.$key.'</span>', $text);
    }
    // ツイート表示のHTML生成
    disp_tweet($value, $text);
}
}
function disp_tweet($value, $text){
    $icon_url = $value->user->profile_image_url;
    $screen_name = $value->user->screen_name;
    $updated = date('Y/m/d H:i', strtotime($value->created_at));
    $tweet_id = $value->id_str;
    $url = 'https://twitter.com/' . $screen_name . '/status/' . $tweet_id;

	echo '<table border="1">';
    echo '<div class="tweetbox">' . PHP_EOL;
    echo '<tr>';
    echo '<td>';
    echo '<div class="thumb">' . '<img alt="" src="' . $icon_url . '">' . '</div>' . PHP_EOL;
    echo '<div class="meta"><a target="_blank" href="' . $url . '">' . $updated . '</a>' . '<br>@' . $screen_name .'</div>' . PHP_EOL;
    echo '</td>';
    echo '<td>';
    echo '<div class="tweet">' . $text . '</div>' . PHP_EOL;
    echo '</td>';
    echo '</tr>';
	echo '</div>' . PHP_EOL;
	echo '<br>';
echo '<form action=tweet-serch.php method=get>';
echo '<input type="hidden" name="id" value="<?php print $id;?>">';
echo '<input type="hidden" name="icon_url" value="<?php print $icon_url;?>">';
echo '<input type="hidden" name="screen_name" value="<?php print $screen_name;?>">';
echo '<input type="hidden" name="updated" value="<?php print $updated;?>">';
echo '<input type="hidden" name="tweet_id" value="<?php print $tweet_id;?>">';
echo '<input type="hidden" name="text" value="<?php print $text;?>">';
echo '<input type="hidden" name="url" value="<?php print $url;?>">';
echo '<input class="button3" type="submit" name="favo" value="お気に入り登録">';
echo '</form>';
}
	?>
<!--
		<form action=tweet-serch.php method=get>
		<input type="hidden" name="id" value="<?php print $id;?>">
		<input type="hidden" name="icon_url" value="<?php print $icon_url;?>">
		<input type="hidden" name="screen_name" value="<?php print $screen_name;?>">
		<input type="hidden" name="updated" value="<?php print $updated;?>">
		<input type="hidden" name="tweet_id" value="<?php print $tweet_id;?>">
		<input type="hidden" name="text" value="<?php print $text;?>">
		<input type="hidden" name="url" value="<?php print $url;?>">
		<input type="submit" name="favo" value="お気に入り登録">
	</form>
-->
<?php

//}

?>
