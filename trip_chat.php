

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
var conn = new WebSocket('ws://localhost:8080');
var multi_login_count = 0;
//接続できた
conn.onopen = function(e) {
  console.log(e);
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
  console.log(e.data);
  var receive_data = {}
  receive_data = JSON.parse(e.data)
  append_message = receive_data["name"] +":" + receive_data["message"];
  $("#message_box").append(append_message);
};
//メッセージを送る
//メッセージを送る
function send() {
  var param = {}
  param["name"] = '<?php echo $name;?>';
  param["userID"] = '<?php echo $userID;?>';
  param["message"] = $('#bms_send_message').val();
  conn.send(JSON.stringify(param));
  //チャット欄にメッセージ追加
  //Ajax通信メソッド
  //type : HTTP通信の種類(POSTとかGETとか)
  //url  : リクエスト送信先のURL
  //data : サーバに送信する値
  $.ajax({
    type: "POST",
    url: "trip_data.php",
    data: {
      message:$('#bms_send_message').val()
    },
    //Ajax通信が成功した場合に呼び出されるメソッド
    success: function(data, dataType){
      //デバッグ用 アラートとコンソール
      alert(param);
      console.log(param);

      //出力する部分
      $('#result').html(data);
    },
    error: function(XMLHttpRequest, textStatus, errorThrown){
      alert('Error : ' + errorThrown);
      $("#XMLHttpRequest").html("XMLHttpRequest : " + XMLHttpRequest.status);
      $("#textStatus").html("textStatus : " + textStatus);
      $("#errorThrown").html("errorThrown : " + errorThrown);
    }
  });

};
</script>

<body id="your_container">
  <div id="bms_messages_container">
    <div id="bms_chat_header">
      <div id="bms_chat_user_status">
        <div id="bms_status_icon">●</div>
        <div id ="bms_chat_user_name">
          <?php
          echo $name.'さん';
          ?>
          <a href="logout.php">ログアウト</a>
          <a href="choice.php">グループ切り替え</a>
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
              echo  "<div id='bms_messege_p_right'>" .$value[ 'name' ] ." ". $value[ 'message' ] ."</div>";
            }else{
              echo  "<div id='bms_messege_p_left'>" .$value[ 'name' ] ." ". $value[ 'message' ] ."</div>";
            }
          };
        } catch ( Exception $e ) {
          echo $e->getMessage() . PHP_EOL;
        }
        ?>
      </div>
    </div>
  </div>
  <div id="bms_send">
    <form method="post" action="trip_chat.php">
      <textarea  id="bms_send_message" name="message"></textarea>
      <button name="submit" id="bms_send_btn" type="submit">送信</button>
      <button name="submit" id="favo_btn" type="submit">♡</button>
    </form>
  </div>
</body>
</html>
