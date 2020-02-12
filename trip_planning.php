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
var conn = new WebSocket('ws://192.168.11.22:8080');
var multi_login_count = 0;
var save_popthing = [];
var save_context_join = [];
var add_val = [];
var save_userID = [];
$(function(){
  conn.onmessage = function(e) {
    receive_data = JSON.parse(e.data);
    if(receive_data["mes"]!=null){
      $("#"+receive_data["mes"]).css({'top':receive_data["top"],'left':receive_data["left"]});
      if(receive_data["drag"] !=1){
        save_popthing.push([receive_data["mes"],receive_data["top"],receive_data["left"],receive_data["offset_top"],receive_data["offset_left"]]);
      }
    }
    if(receive_data["message"]!=null){
      alert(receive_data["name"] +"が" + receive_data["message"]+"と送信しました");
    };
    if(receive_data["mouseX"]!=null){
      var result = save_userID.some( function( value ) {
        return value === receive_data["userID"];
      });
      if(result===false){
        var a=$('#display').append($('<div class="pointer" id="pointer'+receive_data["userID"]+'"></div>').html(receive_data["userID"]));
        save_userID.push(receive_data["userID"]);
      }
      $("#pointer"+receive_data["userID"]).css({'top':receive_data["mouseY"],'left':receive_data["mouseX"]});
    };
    if(receive_data['start_date']!=null){
      $("#start_date").val(receive_data['start_date']);
    };
    if(receive_data['finish_date']!=null){
      $("#finish_date").val(receive_data['finish_date']);
    };
    if(receive_data['trip_title']!=null){
      $("#trip_title").val(receive_data['trip_title']);
    };
    if(receive_data['in_trip_day_plan']!=null){
      $("#"+receive_data['in_trip_day_plan_id']).val(receive_data['in_trip_day_plan']);
    };
    if(receive_data['toMin']!=null){
      $("#"+receive_data['toMin_id']).val(receive_data['toMin']);
    };
    if(receive_data['val']!=null){
      trip_day_plan_add(receive_data['val'],1);
    };
    if(receive_data['delete_val']!=null){
      trip_day_plan_delete(receive_data['delete_val'],1);
    };
    if(receive_data['join_val']!=null){
      trip_plan_join(receive_data['join_val'],receive_data['join_val2'],1);
    };
    if(receive_data['change_val']!=null){
      trip_plan_change(receive_data['change_val'],receive_data['change_val2'],1,receive_data['change_val4'],receive_data['change_val5']);
    };
    if(receive_data['display_title']!=null){
      trip_data_display(receive_data['display_title'],1);
    }
    if(receive_data['start']!=null){
      trip_data_decide(1);
    }
    if(receive_data["pop_vote_id"]!=null){
      pop_data_vote(receive_data["pop_vote_id"],receive_data["pop_vote_add"],1);
    }
    if(receive_data["message"]!=null){
      append_message = receive_data["name"] +":" + receive_data["message"];
      alert(append_message+" と送信しました");
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
  <?php
  if(isset($_SESSION['display_title'])){
    ?>
    trip_data_display('<?=@$_SESSION['display_title']?>',1);
    <?php
  }else if(isset($_SESSION['start_date'])){
    ?>
    trip_data_decide(1,1);
    <?php
  }
  if(isset($_SESSION['add_val'])){
    for($i=0;$i < count($_SESSION['add_val']);$i++){
      for($l=0;$l < $_SESSION['add_val'][$i];$l++){
        ?>
        trip_day_plan_add('<?= $i?>',1);
        <?php
      }
    }
  }
  ?>
  <?php
  if(isset($_SESSION['join_data'])){
    for($i=0;$i<count($_SESSION['join_data']);$i=$i+4){
      ?>
      var val = '<?=@$_SESSION['join_data'][$i]?>';
      var val2 = '<?=@$_SESSION['join_data'][$i+1]?>';
      var time_context = '<?=@$_SESSION['join_data'][$i+2]?>';
      var time_context_1 = "'<?=@$_SESSION['join_data'][$i+2]?>'";
      if('<?=@$_SESSION['join_data'][$i+2]?>'==0){
        time_context='00:00';
        time_context_1="'00:00'";
      }
      var context = '<?=@$_SESSION['join_data'][$i+3]?>';
      var context_1 = "'<?=@$_SESSION['join_data'][$i+3]?>'";
      save_context_join.push(val,val2,time_context,context);
      $("#trip_day_plan"+val+val2).html(time_context+"　"+context+'<input id=trip_plan type="button" value="変更" onclick="trip_plan_change('+val+','+val2+',0,'+time_context_1+','+context_1+')">');
      $.ajax({
        type: "POST",
        url: "trip_planning_data.php",
        data: {
          join_data:save_context_join
        },
      });
      <?php
    }
  }
  ?>
  console.log(save_context_join);
});
function trip_plan_save(){
  var save_context=[];
  var save_time_context=[];
  var a = add_val.length;
  for(var i=0;i<add_val.length;i++){
    for(var i_1=0;i_1<=add_val[i];i_1++){
      var data = ""+i+i_1;
      if($("#in_trip_day_plan"+data).val()!=null&&$("#toMin"+data).val()!=null){
        trip_plan_join(i,i_1,0);
      }
      var trip_data_0 = $("#trip_day_plan"+data).text();
      var trip_data = trip_data_0.split(' ');
      var trip_day_0 = $("#days_"+i).text();
      var trip_day = trip_day_0.replace(trip_data_0,"");
      if(trip_data.length > 2){
        for(var n=1;n<trip_data.length-1;n++){
          trip_data[1] = trip_data[1]+' '+trip_data[n+1];
        }
      }
      var title = $('#output').text();
      $.ajax({
        type: "POST",
        url: "trip_planning_data.php",
        data: {
          trip_day:trip_day,
          title:title,
          trip_day_id:data,
          trip_time:trip_data[0],
          trip_context:trip_data[1]
        },
        //Ajax通信が成功した場合に呼び出されるメソッド
        success: function(data, dataType){
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
          alert('Error : ' + errorThrown);
          $("#XMLHttpRequest").html("XMLHttpRequest : " + XMLHttpRequest.status);
          $("#textStatus").html("textStatus : " + textStatus);
          $("#errorThrown").html("errorThrown : " + errorThrown);
        }
      });
    };
  };
  var param={};
  param["name"] = '<?php echo $name;?>';
  param['save_plan'] = 1;
  conn.send(JSON.stringify(param));
};

function save(){
  for(var i=0;i < save_popthing.length;i++){
    $.ajax({
      type: "POST",
      url: "pop_thing_data.php",
      data: {
        id:save_popthing[i][0],
        top:save_popthing[i][3],
        left:save_popthing[i][4]
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
  // popデータ
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
  // tripデータ
  $stmt = $pdo->query("SELECT DISTINCT title from trip_plan_info WHERE group_id = '$group_ID'");
  $i = 0;
  foreach ( $stmt as $value ) {
    $title_0[$i] = $value['title'];
    $i++;
  }
  $h = 0;
  $trip_data_file = array();
  for($n=0; $n < $i ;$n++){
    $title = $title_0[$n];
    $stmt = $pdo->query("SELECT * from trip_plan_info WHERE group_id = '$group_ID' AND title = '$title'");
    foreach ( $stmt as $value ) {
      $trip_data_file[$h][0] = $value['title'];
      $trip_data_file[$h][1] = $value['trip_day'];
      $trip_data_file[$h][2] = $value['trip_day_id'];
      $trip_data_file[$h][3] = $value['trip_time'];
      $trip_data_file[$h][4] = $value['trip_context'];
      $h++;
    }
  }
  $trip_data_json = json_encode($trip_data_file);
} catch ( Exception $e ) {
  echo $e->getMessage() . PHP_EOL;
}
?>

function trip_day_plan_add(val,val3){
  var val2 = add_val[val]+1;
  $('#days_'+val).append($('<div id="trip_day_plan'+val+val2+'"></div>').html('<input class="toMin" id="toMin'+val+val2+'" type="time" size="2" maxlength="2"> <input class="in_trip_day_plan" id="in_trip_day_plan'+val+val2+'" type="text" placeholder="予定記入"><input id=trip_plan type="button" value="登録" onclick="trip_plan_join('+val+','+val2+',0)">'));
  if(val3==0){
    var param={};
    param['val'] = val;
    param['val2'] = add_val[val];
    //param['trip_title']=val;
    conn.send(JSON.stringify(param));
  }
  add_val[val]++;
  $.ajax({
    type: "POST",
    url: "trip_planning_data.php",
    data: {
      add_val:add_val
    }
  });
};

function trip_day_plan_delete(val,val3){
  var val2 = add_val[val];
  $("#trip_day_plan"+val+val2).remove();
  if(val3==0){
    var param={};
    param['delete_val'] = val;
    param['delete_val2'] = add_val[val];
    //param['trip_title']=val;
    conn.send(JSON.stringify(param));
  }
  add_val[val]--;
  $.ajax({
    type: "POST",
    url: "trip_planning_data.php",
    data: {
      add_val:add_val
    }
  });
};

function trip_plan_join(val,val2,val3){
  var context = $("#in_trip_day_plan"+val+val2).val();
  var time_context = $("#toMin"+val+val2).val();
  context_1 = "'"+context+"'";
  time_context_1 = "'"+time_context+"'";
  if(time_context==0){
    time_context='00:00';
  };
  $("#trip_day_plan"+val+val2).html(time_context+" "+context+'<input id=trip_plan type="button" value="変更" onclick="trip_plan_change('+val+','+val2+',0,'+time_context_1+','+context_1+')">');
   save_context_join.push(val,val2,time_context,context);
    console.log(save_context_join);
   $.ajax({
     type: "POST",
     url: "trip_planning_data.php",
     data: {
       join_data:save_context_join
     },
   });
  if(val3==0){
    var param={};
    param['join_val'] = val;
    param['join_val2'] = val2;
    conn.send(JSON.stringify(param));
  }
};
function trip_plan_change(val,val2,val3,val4,val5){
  $("#trip_day_plan"+val+val2).html('<input class="toMin" id="toMin'+val+val2+'" type="time" size="2" maxlength="2"><input class="in_trip_day_plan" id="in_trip_day_plan'+val+val2+'" type="text"><input id=trip_plan type="button" value="登録" onclick="trip_plan_join('+val+','+val2+',0)">');
  $("#in_trip_day_plan"+val+val2).val(val5);
  $("#toMin"+val+val2).val(val4);
  <?php
  if(@isset($_SESSION['join_data'])){
    for($i=0;$i<count($_SESSION['join_data']);$i=$i++){
      ?>

      if(val=='<?=$_SESSION['join_data'][$i]?>'&&val2=='<?=$_SESSION['join_data'][$i+1]?>'){
        <?php
         unset($_SESSION['join_data'][$i]);
         unset($_SESSION['join_data'][$i+1]);
         unset($_SESSION['join_data'][$i+2]);
         unset($_SESSION['join_data'][$i+3]);
         $_SESSION['join_data']=array_values($_SESSION['join_data']);
        ?>
        save_context_join.splice(<?=$i?>,4);
        console.log(save_context_join);
      }
      <?php
    }
  }
  ?>
  if(val3==0){
    var param={};
    param['change_val'] = val;
    param['change_val2'] = val2;
    param['change_val4'] = val4;
    param['change_val5'] = val5;
    //param['trip_title']=val;
    conn.send(JSON.stringify(param));
  }
  $.ajax({
    type: "POST",
    url: "trip_planning_data.php",
    async: false,
    data: {
      join_data:save_context_join,
    },
    success: function(data, dataType){
      //  デバッグ用 アラートとコンソール
    },
    error: function(XMLHttpRequest, textStatus, errorThrown){
      alert('Error : ' + errorThrown);
      $("#XMLHttpRequest").html("XMLHttpRequest : " + XMLHttpRequest.status);
      $("#textStatus").html("textStatus : " + textStatus);
      $("#errorThrown").html("errorThrown : " + errorThrown);
    }
  });
};

function trip_data_display(val,val1){
  var receive_trip_data = {};
  var sort_trip_data = [];
  sort_trip_data = <?php echo $trip_data_json;?>;
  receive_trip_data_length = Object.keys(sort_trip_data).length;
  var length_sort = 0;
  var title_save = {};
  var data_num = 0;
  var add = 1;
  var start_day = sort_trip_data[0][1];
  <?php
  if(isset($_SESSION['length_sort'])){
    for($i=0;$i < $_SESSION['length_sort'];$i++){
      ?>
      $('#days_'+<?= $i?>).remove();
      <?php
    }
  }

  ?>
  for(var i=0;i < receive_trip_data_length;i++){
    if(sort_trip_data[i][0]==val){
      $('#output').html(sort_trip_data[i][0]);
      if(sort_trip_data[i][1] == start_day){
        var str = sort_trip_data[i][2].split('');
        if(str[1]==null){
          str[1] = "0";
        }
        $('#trip_date_data').append($('<div class="days_date" id="days_'+str[0]+'"></div>').html(sort_trip_data[i][1]));
        add_val[str[0]] = add;
        $('#days_'+str[0]).append($('<input id=trip_plan_add type="button" value="追加" onclick="trip_day_plan_add('+str[0]+',0)">'));
        $('#days_'+str[0]).append($('<input id=trip_plan_delete type="button" value="削除" onclick="trip_day_plan_delete('+str[0]+',0)">'));
        $('#days_'+str[0]).append($('<div id="trip_day_plan'+str[0]+str[1]+'"></div>'));
        var time_context = "'"+sort_trip_data[i][3]+"'";
        var context = "'"+sort_trip_data[i][4]+"'";
        $("#trip_day_plan"+str[0]+str[1]).html(sort_trip_data[i][3]+" "+sort_trip_data[i][4]+'<input id=trip_plan type="button" value="変更" onclick="trip_plan_change('+str[0]+','+str[1]+',0,'+time_context+','+context+')">');
        add++;
        add_val[str[0]] = str[1];
        length_sort++;
      }else{
        start_day = new Date(start_day);
        var get_month = start_day.getMonth()+1;
        start_day.setDate(start_day.getDate()+1);
        var get_month =  start_day.getMonth()+1;
        start_day = start_day.getFullYear() + "-" +  get_month +"-"+ start_day.getDate();
        i--;
        add = 1;
      }
    }
  }
  if(val1 == 0){
    var param={};
    param['display_title'] = val;
    conn.send(JSON.stringify(param));
    $.ajax({
      type: "POST",
      url: "trip_planning_data.php",
      data: {
        display_title:val,
        display_length:length_sort
      }
    });
  }
   $('.trip_plan_decide').hide();
};

function trip_data_decide(val,val1){
  <?php
  if(isset($_SESSION['length_sort'])){
    for($i=0;$i < $_SESSION['length_sort'];$i++){
      ?>
      $('#days_'+<?= $i?>).remove();
      <?php
    }
  }
  ?>
  if(val1==1){
    var start_date_1 = 0;
    var start_date = 0;
    var finish_date = 0;
    <?php
    if(isset($_SESSION['start_date'])){
      ?>
      var start_date = <?=@$_SESSION['start_date']?>;
      var finish_date = <?=@$_SESSION['finish_date']?>;
      <?php
    }
    ?>
    start_date_1 = new Date(start_date);
    $('#output').html('<?=@$_SESSION['trip_title']?>');
    var output = $('#output').html('<?=@$_SESSION['trip_title']?>');
    var date_diff = Math.floor((finish_date-start_date)/1000/60/60/24);
  }else{
    var start_date_1 = new Date($("#start_date").val());
    var start_date = Date.parse($("#start_date").val());
    var finish_date = Date.parse($("#finish_date").val());
    $('#output').html($("#trip_title").val());
    var output = $('#output').html($("#trip_title").val());
    var date_diff = 0;
    date_diff = '<?=@$_SESSION['date_diff']?>';
    for(var i=0;i <= date_diff;i++){
      $('#days_'+i).remove();
    }
  }
  var date_diff = Math.floor((finish_date-start_date)/1000/60/60/24);
  for(var i=0;i <= date_diff;i++){
    var get_month = start_date_1.getMonth()+1;
    var date = start_date_1.getFullYear() + "-" +  get_month +"-"+ start_date_1.getDate();
    $('#trip_date_data').append($('<div class="days_date" id="days_'+i+'"></div>').html(date));
    add_val[i] = 0;
    $('#days_'+i).append($('<input id=trip_plan_add type="button" value="追加" onclick="trip_day_plan_add('+i+',0)">'));
    $('#days_'+i).append($('<input id=trip_plan_delete type="button" value="削除" onclick="trip_day_plan_delete('+i+',0)">'));
    $('#days_'+i).append($('<div id="trip_day_plan'+i+0+'"></div>').html('<div id="trip_day_plan'+i+0+'"></div>').html('<input class="toMin" id="toMin'+i+0+'" type="time" size="2" maxlength="2"><input class="in_trip_day_plan" id="in_trip_day_plan'+i+0+'" type="text" placeholder="予定記入"> <input id=trip_plan type="button" value="登録" onclick="trip_plan_join('+i+',0,0)">'));
    start_date_1.setDate(start_date_1.getDate()+1);
  }
  if(val == 0){
    var param={};
    param['start'] = start_date_1;
    param['finish'] = finish_date;
    param['title'] = output;
    conn.send(JSON.stringify(param));
    $.ajax({
      type: "POST",
      url: "trip_planning_data.php",
      data: {
        start_date:start_date,
        finish_date:finish_date,
        trip_title:$("#trip_title").val(),
        date_diff:date_diff
      }
    });
  }
    $('.trip_plan_decide').hide();
};

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
      save_popthing.push(['<?=$_SESSION['ses'][$i][0]?>','<?=$_SESSION['ses'][$i][1]?>','<?=$_SESSION['ses'][$i][2]?>','<?=$_SESSION['ses'][$i][3]?>','<?=$_SESSION['ses'][$i][4]?>']);
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
  //ここまでpop_displayのコード
  $("#trip_title").keyup(function(){
    var param={};
    var val = $(this).val();
    param['trip_title']=val;
    conn.send(JSON.stringify(param));
  });
  $("#start_date").change(function(){
    var param={};
    var val = $(this).val();
    param['start_date']=val;
    conn.send(JSON.stringify(param));
  });
  $("#finish_date").change(function(){
    var param={};
    var val = $(this).val();
    param['finish_date']=val;
    conn.send(JSON.stringify(param));
  });
  $(document).on('change',"[id^=toMin]",function(){
    var param={};
    var val = $(this).val();
    var id = $(this).attr('id');
    param['toMin']=val;
    param['toMin_id']=id;
    conn.send(JSON.stringify(param));
  });
  $(document).on('keyup change',"[id^=in_trip_day_plan]",function(){
    var param={};
    var val = $(this).val();
    var id = $(this).attr('id');
    param['in_trip_day_plan']=val;
    param['in_trip_day_plan_id']=id;
    conn.send(JSON.stringify(param));
  });
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

window.onbeforeunload = function () {
    $.ajax({
      type: "POST",
      url: "pop_thing_data.php",
      async: false,
      data: {
        ses:save_popthing,
      },
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
    return true;
}

</script>
<div id="bms_chat_header">
  <div id="bms_chat_user_status">
    <div id="bms_status_icon">🛫</div>
    <div id ="bms_chat_user_name">
      <?php
      echo $group_name.'　　　';
      ?>
      <a href="logout.php">ログアウト</a>
      <a href="choice.php">グループ切り替え</a>
      <a href="trip_chat.php">チャットシステムへ</a>
      <a href="pop_display.php">みんなで相談画面へ</a>
      <button id="pop_things_save" onclick="save()">保存</button>
    </div>
  </div>
</div>
<body id=display>
  <div id=plan_css>
    <div class="split">
      <div class="split-item pop_display">
        <div class="selectable">
          <?php
          for($i=0;$i<$favo_things;$i++){
            echo '<div class="pop_things" id="pop_thing_'.$favo[$i][1].'">'.$favo[$i][0].'</br><input type=button id="pop_voting" value= "+" onclick="pop_data_vote('.$i.',1,0)"><div id="pop_voting_'.$favo[$i][1].'">'.$favo[$i][2].'</div><input type=button id="pop_voting" value= "-" onclick="pop_data_vote('.$i.',0,0)"></div>';
          }
          ?>
        </div>
      </div>
    </div>
    <div class="split-item trip_plan">
      <div class="trip_plan_decide">
      </br>  </br>
      <p id=save_plan_t>保存されているプラン</p>
      <?php
      if(count($title_0) > 0){
        for($i=0;$i<count($title_0);$i++){
          $title = '"'.$title_0[$i].'"';
          ?>
          <div><input class=trip_data_d type="button" onclick='trip_data_display(<?= $title?>,0)' value=<?= $title?>></div>

          <?php
        }
      }
      ?>
    </br>
  </br>
      <form id="trip_data" action="trip_planning.php" method="post">
        <div><input class="text" id="start_date" name="start_date" type="date"></div><p>~</p><div><input class="text" id="finish_date" name="finish_date" type="date"></div>
        <div><input class="text" id="trip_title" name="trip_title" type="text" placeholder="タイトル"></div>
        <input type="hidden" id="trip" name="trip" value="1">
        <div><input class="plan_sub" id=trip_main_data type="button" value="送信" onclick="trip_data_decide(0,0)"></div>
      </form>
    </div>
    <div id=trip_plan_sub>
      <div id=trip_title_output><span id="output"></span><input id=trip_plans type="button" value="保存" onclick="trip_plan_save()"></div>
    </br>
      <div id="trip_date_data"></div>
    </div>
    </div>
  </div>
</body>
</html>

<?php
//　実験　3-4人の仲良しグループを集める。ディスカッション時間決める。時間を決めないで、どちらが決めやすかったか。
//　LINE、Googleドキュメント比較。仮定、どうしても会えない人用、集まれない人用。
//　研究目的を明確化する→それによりプランが決めやすくなる
//登録してから、画面を移行すると登録したほうのやつに登録したデータが表示されない
?>
