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
  <link type="text/css" rel="stylesheet" href="trip_chat.css">
  <title>旅行チャット</title>
</head>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" />
<script src="http://code.jquery.com/jquery-3.4.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>
<script>
var conn = new WebSocket('ws://localhost:8080');
var multi_login_count = 0;
var save_popthing = [];
$(function(){
  conn.onmessage = function(e) {
    var receive_data = {}
    receive_data = JSON.parse(e.data);
    //console.log(receive_data["mes"]);
    if(receive_data["mes"]!=null){
      $("#"+receive_data["mes"]).css({'top':receive_data["top"],'left':receive_data["left"]});
      if(receive_data["drag"] !=1){
        save_popthing.push([receive_data["mes"],receive_data["top"],receive_data["left"]]);
        console.log(save_popthing);
      }
    };
    if(receive_data["mouseX"]!=null){
      $("#pointer").css({'top':receive_data["mouseY"],'left':receive_data["mouseX"]});
      //console.log('top:'+receive_data["mouseX"],'left:'+receive_data["mouseY"]);
    }
  };
  $(this).mousemove(function(e){
    var param_2 ={};
    param_2['mouseX']=e.clientX;
    param_2['mouseY']=e.clientY;
    conn.send(JSON.stringify(param_2));
  });
  $('.selectable .pop_things').draggable({
    start: function(e,ui){
    },
    drag:function(e,ui){
      var param ={};
      param["top"] = ui.position.top;
      param["left"] = ui.position.left;
      var id = $(this).attr('id');
      //console.log(this);
      param["mes"] = id;
      param["drag"] = 1;
      //console.log(id);
      conn.send(JSON.stringify(param));
      //console.log(' top: ' + ui.position.top + ' left: ' + ui.position.left);
    },
    stop: function(e, ui) {
      var param ={};
      param["top"] = ui.position.top;
      param["left"] = ui.position.left;
      var id = $(this).attr('id');
      //console.log(this);
      param["mes"] = id;
      param["drag"] = 0;
      console.log(id);
      conn.send(JSON.stringify(param));
      console.log(' top: ' + ui.position.top + ' left: ' + ui.position.left);
      save_popthing.push([id,ui.position.top,ui.position.left]);
      //save_popthing= [[id,ui.position.top,ui.position.left]];
      console.log(save_popthing);
    }
  });
  $('.selectable').selectable({
    // selected: onSelected,
    // unselected: onUnselected
  });
});
function save(){
  console.log(save_popthing.length);
  for(var i=0;i < save_popthing.length;i++){
    $.ajax({
      type: "POST",
      url: "pop_thing_data.php",
      data: {
        id:save_popthing[i][0],
        top:save_popthing[i][1],
        left:save_popthing[i][2]
      },
      //Ajax通信が成功した場合に呼び出されるメソッド
      success: function(data, dataType){
        //   //デバッグ用 アラートとコンソール
        //   alert(param);
        console.log("ok");
        //
        //   //出力する部分
        //   $('#result').html(data);
      },
      error: function(XMLHttpRequest, textStatus, errorThrown){
        alert('Error : ' + errorThrown);
        $("#XMLHttpRequest").html("XMLHttpRequest : " + XMLHttpRequest.status);
        $("#textStatus").html("textStatus : " + textStatus);
        $("#errorThrown").html("errorThrown : " + errorThrown);
      }
    });
  }
}
<?php
//chatデータ表示
try {
  $stmt = $pdo->query("SELECT count(favo) from trip_chat WHERE group_id = '$group_ID'AND favo='1'");
  $favo_things = $stmt->fetchColumn();
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
<div id="bms_chat_header">
  <div id="bms_chat_user_status">
    <div id="bms_status_icon">●</div>
    <div id ="bms_chat_user_name">
      <?php
      echo $name.'さん';
      ?>
      <a href="logout.php">ログアウト</a>
      <a href="choice.php">グループ切り替え</a>
      <a href="trip_chat.php">チャットシステムへ</a>
      <button id="pop_things_save" onclick="save()">保存</button>
    </div>
  </div>
</div>
<body>
  <div class="selectable">
    <?php
    for($i=0;$i<$favo_things;$i++){
      echo '<div class="pop_things" id="pop_thing_'.$favo_number[$i].'">'.$favo[$i].'</div>';
    }
    ?>
  </div>
  <div id="pointer">〇</div>
</body>
</html>

<?php
//　実験　3-4人の仲良しグループを集める。ディスカッション時間決める。時間を決めないで、どちらが決めやすかったか。
//　LINE、Googleドキュメント比較。仮定、どうしても会えない人用、集まれない人用。
?>
