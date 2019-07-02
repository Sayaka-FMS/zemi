<?php
// 送信された名前とメッセージの表示
session_start();
if ( isset( $_SESSION[ 'username' ] ) ) {
  $name = $_SESSION[ 'username' ];
  $userID = $_SESSION['userID'];
  $group_name = $_SESSION['group_name'];
  $group_ID = $_SESSION['group_ID'];
} else {
  header("Location:login.php");
}
$dsn = 'mysql:host=localhost;dbname=test;charset=utf8mb4';
$username = 'root';
$password = '';
date_default_timezone_set( 'Asia/Tokyo' );
$time = date( "Y-m-d H:i:s" );
$pdo = new PDO( $dsn, $username, $password );
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
// デフォルトのフェッチモードを連想配列形式に設定
// (毎回PDO::FETCH_ASSOCを指定する必要が無くなる)
$pdo->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
?>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <link type="text/css" rel="stylesheet" href="pop_display.css">
  <title>旅行チャット</title>
</head>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" />
<script src="http://code.jquery.com/jquery-3.4.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery.random.js"></script>
<script>
var conn = new WebSocket('ws://localhost:8080');
var multi_login_count = 0;
$(function(){
  conn.onmessage = function(e) {
    var receive_data = {}
    receive_data = JSON.parse(e.data);
    console.log(receive_data);
  };
  $('.selectable .pop_things').draggable({
    start: function(e,ui){
    },
    drag:function(e,ui){
    },
    stop: function(e, ui) {
      var param ={};
      param["top"] = ui.offset.top;
      param["left"] = ui.offset.left;
      var id = $(this).attr('id');
      //console.log(this);
      param["mes"] = id;
      console.log(ui);
      conn.send(JSON.stringify(param));
      console.log(' top: ' + ui.offset.top + ' left: ' + ui.offset.left);
    }
  });
  $('.selectable').selectable({
    // selected: onSelected,
    // unselected: onUnselected
  });
});

<?php
//chatデータ表示
try {
  $stmt = $pdo->query("SELECT count(favo) from trip_chat WHERE group_id = '$group_ID'AND favo='1'");
  $favo_things = $stmt->fetchColumn();
} catch ( Exception $e ) {
  echo $e->getMessage() . PHP_EOL;
}
try {
  $stmt = $pdo->query( "SELECT * FROM trip_chat WHERE group_id = '$group_ID'" );
  $i=0;
  foreach ( $stmt as $value ) {
    if($value['favo']==1){
      $favo[$i] = $value[ 'message' ];
      $favo_number[$i] = $value[ 'id' ];
      //var_dump($favo[$i]);
      $i++;
    }
  }
} catch ( Exception $e ) {
  echo $e->getMessage() . PHP_EOL;
}
?>
</script>
<body>
  <div class="selectable">
    <?php
    for($i=0;$i<$favo_things;$i++){
      echo '<div class="pop_things" id="pop_thing_'.$favo_number[$i].'">'.$favo[$i].'</div>';
    }
    ?>
  </div>
</body>
</html>

<?php
//　実験　3-4人の仲良しグループを集める。ディスカッション時間決める。時間を決めないで、どちらが決めやすかったか。
//　LINE、Googleドキュメント比較。仮定、どうしても会えない人用、集まれない人用。
?>
