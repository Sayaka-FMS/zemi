<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="tweet.css" type="text/css">
  ようこそ<?php echo $_SESSION['name']?> さん<br><br><br>
     <form action="tweet.php" method="get">
       <div class="box1">
         <div class="box2">
     <textarea name="tweet" placeholder="いまどうしてる？" maxlength="140"></textarea><br>
    <input type="submit" method="get"value="ツイート"><br>
    <div class="hantei">
    </form>
    </head>
<body>
</body>
</html>


<?php
//session_start();
    //「autoload.php」読み込み
    //「twitteroauth/」フォルダは本プログラムと同階層に配置
    require_once dirname(__FILE__) . '/twitteroauth-master/autoload.php';
    use Abraham\TwitterOAuth\TwitterOAuth;

    //APIキー
    define("Consumer_Key", "6Rq4Wd89Lbfif1bAIxkpRnKwJ");
    define("Consumer_Secret", "1QbeunVuUXh4pxSFBFATYiSFTSuxPMsdUaDiaJ5wtIJksDHV10");

    $twitter = new TwitterOAuth(Consumer_Key, Consumer_Secret, $_SESSION['token'], $_SESSION['token_secret']);
      if(isset($_GET["tweet"])){
    $tweet=$_GET["tweet"];
    $result = $twitter->post(
            "statuses/update",
            array("status" => $tweet)
    );

    //if($twitter->getLastHttpCode() == 200) {
        if($twitter->getLastHttpCode() == 200) {
        // ツイート成功
        print "ツイートしました\n";
    } else {
        // ツイート失敗
        print "ツイートに失敗しました\n";
    }
    }
//}
echo "</div></div></div><br><p><a href='logout.php'>ログアウト</a></p>";
echo "<p><a href='tweet-serch.php'>戻る</a></p>";
?>
