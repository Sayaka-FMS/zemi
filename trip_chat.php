<?php
// 送信された名前とメッセージの表示
session_start();
if(isset($_POST['group_ID'])){
  $group_name = $_POST['group_name'];
  $group_ID = $_POST['group_ID'];
  $_SESSION['group_name']=$_POST['group_name'];
  $_SESSION['group_ID']=$_POST['group_ID'];
}
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

<!-- chat.php -->

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <link type="text/css" rel="stylesheet" href="trip_chat.css">
  <title>旅行チャット</title>
</head>
<script src="http://code.jquery.com/jquery-2.2.4.js"></script>
<script src="https://code.jquery.com/jquery-3.0.0.min.js"></script>
<script>
var conn = new WebSocket('ws://192.168.11.22:8080');
var multi_login_count = 0;
//接続できた
conn.onopen = function(e) {
  console.log(e);
  $("#bms_messages").animate({scrollTop:20000});
};
conn.onerror = function(e) {
  alert("エラー");
};
//接続が切れた
conn.onclose = function(e) {
  alert("接続がきれました");
};
//メッセージを受け取った
conn.onmessage = function(e) {
  var receive_data = {}
  receive_data = JSON.parse(e.data);
  append_message = receive_data["name"] +":" + receive_data["message"];
  console.log(receive_data["favo"]);
  if(receive_data["message"]!=null){
  if(receive_data["favo"]==1){
    var inner_name = $('<div id="bms_messege_p_left_name"></div>').text(receive_data["name"]);
    var inner_mes = $('<div id="bms_messege_p_left_favo"></div>').text(receive_data["message"]);
  }else{
    var inner_name = $('<div id="bms_messege_p_left_name"></div>').text(receive_data["name"]);
    var inner_mes = $('<div id="bms_messege_p_left"></div>').text(receive_data["message"]);
  }
 }
 if(receive_data["message"]!=null){
   append_message = receive_data["name"] +":" + receive_data["message"];
   // alert(append_message+"  "+"と送信しました");
 }
 if(receive_data["save"]!=null){
   alert(receive_data["name"]+"がポップデータを保存しました");
 }
 if(receive_data["save_plan"]!=null){
   alert(receive_data["name"]+"が旅行プランを保存しました");
 }
  var box = $('<div id="box"></div>').html(inner_name);
  $('#chat').append(box);
  var box = $('<div id="box"></div>').html(inner_mes);
  $('#chat').append(box);
  $("#bms_messages").animate({scrollTop:20000});
};
//メッセージを送る
function send(favo) {
  var param = {}
  param["name"] = '<?php echo $name;?>';
  param["userID"] = '<?php echo $userID;?>';
  param["message"] = $('#bms_send_message').val();
  param["favo"] = favo;
//  var reset_favo = document.getElementById("favo_btn");
  //reset_favo.value = "0";
  if(param["favo"]==1){
    $('#chat').append('<div id="bms_messege_p_right_name">'+param["name"]+'</div><div id="bms_messege_p_right_favo">'+param['message']+'</div>');
  }else{
    $('#chat').append('<div id="bms_messege_p_right_name">'+param["name"]+'</div><div id="bms_messege_p_right">'+param['message']+'</div>');
  }
  conn.send(JSON.stringify(param));
  console.log(JSON.stringify(param));
  $("#bms_messages").animate({scrollTop:20000});
  $.ajax({
    type: "POST",
    url: "trip_data.php",
    data: {
      name:param["name"],
      userID:param["userID"],
      message:$('#bms_send_message').val(),
      favo:param["favo"]
    },
    //Ajax通信が成功した場合に呼び出されるメソッド
    success: function(data, dataType){
      //   //デバッグ用 アラートとコンソール
      //   alert(param);
      console.log(param);
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
  var reset_target = document.getElementById("bms_send_message");
  reset_target.value = '';
};
function send_nfavo() {
  send(0);
};
function send_favo() {
  send(1);
};

</script>

<body id="your_container">
  <div id="bms_messages_container">
    <div id="bms_chat_header">
      <div id="bms_chat_user_status">
        <div id="bms_status_icon">🛫</div>
        <div id ="bms_chat_user_name">
          <?php
          echo $group_name.'　　　';
          ?>
          <a href="logout.php">ログアウト</a>
          <a href="choice.php">グループ切り替え</a>
          <a href="pop_display.php">みんなで相談画面へ</a>
          <a href="trip_planning.php">旅行プラン検討へ</a>
        </div>
      </div>
    </div>
    <div id="bms_messages">
      <div id="bms_message">
        <?php
        //chatデータ表示
        try {
          $stmt = $pdo->query( "SELECT * FROM trip_chat WHERE group_id = '$group_ID'" );
          foreach ( $stmt as $value ) {
            if($value['name']==$_SESSION['username']){
              if($value['favo']==1){
                echo  "<div id='bms_messege_p_right_name'>".$value[ 'name' ] ."</div><div id='bms_messege_p_right_favo'>". $value[ 'message' ] ."</div>";
              }else{
                echo  "<div id='bms_messege_p_right_name'>".$value[ 'name' ] ."</div><div id='bms_messege_p_right'>". $value[ 'message' ] ."</div>";
              }
            }else
            if($value['favo']==1){
              echo  "<div id='bms_messege_p_left_name'>".$value[ 'name' ] ."</div><div id='bms_messege_p_left_favo'>" . $value[ 'message' ] ."</div>";
            }else{
              echo  "<div id='bms_messege_p_left_name'>".$value[ 'name' ] ."</div><div id='bms_messege_p_left'>" . $value[ 'message' ] ."</div>";
            }
          }
      } catch ( Exception $e ) {
        echo $e->getMessage() . PHP_EOL;
      }
      ?>
      <div id="chat"></div>
    </div>
  </div>
</div>
<div id="bms_send">
  <textarea  id="bms_send_message" name="message"></textarea>
  <button name="submit" id="bms_send_btn" onclick="send_nfavo()">送信</button>
  <button name="favo" id="favo_btn" onclick="send_favo()">♡</button>
</div>
</body>
</html>
