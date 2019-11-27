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
    if(receive_data["mes"]!=null){
      $("#"+receive_data["mes"]).css({'top':receive_data["top"],'left':receive_data["left"]});
      if(receive_data["drag"] !=1){
        save_popthing.push([receive_data["mes"],receive_data["top"],receive_data["left"],receive_data["offset_top"],receive_data["offset_left"]]);
      }
    };
    if(receive_data["mouseX"]!=null){
      $("#pointer").css({'background-color':"red",'top':receive_data["mouseY"],'left':receive_data["mouseX"]});
      document.getElementById("pointer").innerText = receive_data["userID"];
    }
    if(receive_data["pop_vote_id"]!=null){
      pop_data_vote(receive_data["pop_vote_id"],receive_data["pop_vote_add"],1);
    }
    if(receive_data["message"]!=null){
      append_message = receive_data["name"] +":" + receive_data["message"];
      alert(append_message+"  "+"と送信しました");
    }
    if(receive_data["save"]!=null){
      alert(receive_data["name"]+"がポップデータを保存しました");
    }
    if(receive_data["save_plan"]!=null){
      alert(receive_data["name"]+"が旅行プランを保存しました");
    }
  };
  $(this).mousemove(function(e){
    var param_2 ={};
    param_2['mouseX']=e.clientX;
    param_2['mouseY']=e.clientY;
    param_2['userID']='<?php echo $userID;?>';
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
      param["mes"] = id;
      param["drag"] = 1;
      conn.send(JSON.stringify(param));
    },
    stop: function(e, ui) {
      var param ={};
      param["top"] = ui.position.top;
      param["left"] = ui.position.left;
      param["offset_top"] = ui.offset.top;
      param["offset_left"] = ui.offset.left;
      var id = $(this).attr('id');
      param["mes"] = id;
      param["drag"] = 0;
      conn.send(JSON.stringify(param));
      save_popthing.push([id,ui.position.top,ui.position.left,ui.offset.top,ui.offset.left]);
      //save_popthing= [[id,ui.position.top,ui.position.left]];
      $.ajax({
        type: "POST",
        url: "pop_thing_data.php",
        data: {
          ses:save_popthing,
        }
      });
    }
  });
  $('.selectable').selectable({
    // selected: onSelected,
    // unselected: onUnselected
  });
});
function save(){
  for(var i=0;i < save_popthing.length;i++){
    $.ajax({
      type: "POST",
      url: "pop_thing_data.php",
      data: {
        id:save_popthing[i][0],
        top:save_popthing[i][3],
        left:save_popthing[i][4],
      },
      //Ajax通信が成功した場合に呼び出されるメソッド
      success: function(data, dataType){
      //デバッグ用 アラートとコンソール
      },
      error: function(XMLHttpRequest, textStatus, errorThrown){
        alert('Error : ' + errorThrown);
        $("#XMLHttpRequest").html("XMLHttpRequest : " + XMLHttpRequest.status);
        $("#textStatus").html("textStatus : " + textStatus);
        $("#errorThrown").html("errorThrown : " + errorThrown);
      }
    });
  }
  for(var i=0;i < Object.keys(vote).length;i++){
    $.ajax({
      type: "POST",
      url: "trip_data.php",
      data: {
        id:favo[i][1],
        vote:vote[i]
      },
      //Ajax通信が成功した場合に呼び出されるメソッド
      success: function(data, dataType){
        //   //デバッグ用 アラートとコンソール
      },
      error: function(XMLHttpRequest, textStatus, errorThrown){
        alert('Error : ' + errorThrown);
        $("#XMLHttpRequest").html("XMLHttpRequest : " + XMLHttpRequest.status);
        $("#textStatus").html("textStatus : " + textStatus);
        $("#errorThrown").html("errorThrown : " + errorThrown);
      }
    });
  }
  var param={};
  param["name"] = '<?php echo $name;?>';
  param['save'] = 1;
  conn.send(JSON.stringify(param));
}
<?php
//chatデータ表示
 $favo_json = 0;
try {
  $stmt = $pdo->query("SELECT count(favo) from trip_chat WHERE group_id = '$group_ID'AND favo='1'");
  $favo_things = $stmt->fetchColumn();
  $stmt = $pdo->query( "SELECT * FROM trip_chat WHERE group_id = '$group_ID'" );
  $i=0;
  foreach ( $stmt as $value ) {
    if($value['favo']==1){
      $favo[$i][0] = $value['message'];
      $favo[$i][1] = $value['id'];
      $favo[$i][2] = $value['vote'];
      $i++;
    }
  }
  $favo_json = json_encode($favo);
  $stmt = $pdo->query("SELECT count(*) from group_pop_display_info WHERE group_id = '$group_ID'");
  $pop_things_count = $stmt->fetchColumn();
  $pop_thing_json = 0;
  if($pop_things_count!=0){
    $stmt = $pdo->query("SELECT * from group_pop_display_info WHERE group_id = '$group_ID'");
    $l =0;
    foreach ( $stmt as $value ) {
      $pop_things_position[$l] = array($value['pop_id'],$value['pop_top'],$value['pop_left']);
      $l++;
    }
    $pop_thing_json = json_encode($pop_things_position);
  }
} catch ( Exception $e ) {
  echo $e->getMessage() . PHP_EOL;
}
?>
var vote = {};
var favo = {};
var favo_length = 0;
$(function(){
  var receive_pop_data = {};
  receive_pop_data = <?php echo $pop_thing_json;?>;
  receive_pop_data_length = Object.keys(receive_pop_data).length;
  for(var i=0;i < receive_pop_data_length;i++){
    $("#"+receive_pop_data[i][0]).css({'position':'absolute','top':receive_pop_data[i][1],'left':receive_pop_data[i][2]});
  }
  favo = <?php echo $favo_json;?>;
  favo_length = Object.keys(favo).length;
  for(var i=0;i < <?=$favo_things?>;i++){
    vote[i] = favo[i][2];
  }
  <?php
  if(isset($_SESSION['ses'])){
    for($i=0;$i < count($_SESSION['ses']);$i++){
      ?>
        $('#<?=$_SESSION['ses'][$i][0]?>').css({'position':'absolute','top':<?=$_SESSION['ses'][$i][3]?>,'left':<?=$_SESSION['ses'][$i][4]?>});
        <?php
    }
  }
  ?>
  <?php
  if(isset($_SESSION['vote'])){
    for($i=0;$i<count($_SESSION['vote']);$i++){
      ?>
      $('#pop_voting_'+favo[<?=$i?>][1]).text(<?=$_SESSION['vote'][$i]?>);
      <?php
    }
  }
  ?>
});

function pop_data_vote(val,val2,val3){
  if(val2==0){
    vote[val]--;
  }
  if(val2==1){
    vote[val]++;
  }
  $('#pop_voting_'+favo[val][1]).text(vote[val]);
  if(val3 == 0){
    var param={};
    param['pop_vote_id'] = val;
    param['pop_vote_add'] = val2;
    conn.send(JSON.stringify(param));
    $.ajax({
      type: "POST",
      url: "pop_thing_data.php",
      data: {
        vote:vote
      },
    });
  }
}

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
      <a href="trip_planning.php">旅行プラン検討へ</a>
      <button id="pop_things_save" onclick="save()">保存</button>
    </div>
  </div>
</div>
<body>
  <div class="selectable">
    <?php
    for($i=0;$i<$favo_things;$i++){
      echo '<div class="pop_things" id="pop_thing_'.$favo[$i][1].'">'.$favo[$i][0].'<input type=button id="pop_voting" value= "-" onclick="pop_data_vote('.$i.',0,0)"><div id="pop_voting_'.$favo[$i][1].'">'.$favo[$i][2].'</div><input type=button id="pop_voting" value= "+" onclick="pop_data_vote('.$i.',1,0)"></div>';
    }
    ?>
  </div>
  <div id="pointer"></div>
</body>
</html>

<?php
//　実験　3-4人の仲良しグループを集める。ディスカッション時間決める。時間を決めないで、どちらが決めやすかったか。
//　LINE、Googleドキュメント比較。仮定、どうしても会えない人用、集まれない人用。
?>
