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
var receive_data = [];
$(function(){
  conn.onmessage = function(e) {
    receive_data = JSON.parse(e.data);
    //console.log(receive_data["mes"]);
    if(receive_data["mes"]!=null){
      $("#"+receive_data["mes"]).css({'top':receive_data["top"],'left':receive_data["left"]});
      if(receive_data["drag"] !=1){
        save_popthing.push([receive_data["mes"],receive_data["top"],receive_data["left"],receive_data["offset_top"],receive_data["offset_left"]]);
        //console.log(save_popthing);
      }
    };
    if(receive_data["mouseX"]!=null){
      $("#pointer").css({'background-color':"red",'top':receive_data["mouseY"],'left':receive_data["mouseX"]});
      document.getElementById("pointer").innerText = receive_data["userID"];
    };
    if(receive_data['start_date']!=null){
      //console.log(receive_data['start_date']);
      $("#start_date").val(receive_data['start_date']);
    };
    if(receive_data['finish_date']!=null){
      //console.log(receive_data['finish_date']);
      $("#finish_date").val(receive_data['finish_date']);
    };
    if(receive_data['trip_title']!=null){
      //console.log(receive_data['trip_title']);
      $("#trip_title").val(receive_data['trip_title']);
    };
    if(receive_data['in_trip_day_plan']!=null){
      //console.log(receive_data);
      $("#"+receive_data['in_trip_day_plan_id']).val(receive_data['in_trip_day_plan']);
    };
    if(receive_data['toMin']!=null){
      //console.log(receive_data);
      $("#"+receive_data['toMin_id']).val(receive_data['toMin']);
    };
    if(receive_data['val']!=null){
      trip_day_plan_add(receive_data['val'],1);
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
      param["offset_top"] = ui.offset.top;
      param["offset_left"] = ui.offset.left;
      var id = $(this).attr('id');
      //console.log(this);
      param["mes"] = id;
      param["drag"] = 0;
      //console.log(id);
      conn.send(JSON.stringify(param));
      //console.log(' top: ' + ui.position.top + ' left: ' + ui.position.left);
      save_popthing.push([id,ui.position.top,ui.position.left,ui.offset.top,ui.offset.left]);
      //save_popthing= [[id,ui.position.top,ui.position.left]];
      //console.log(save_popthing);
    }
  });
  $('.selectable').selectable({
    // selected: onSelected,
    // unselected: onUnselected
  });
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
          console.log('ok');
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
};

function save(){
  //console.log(save_popthing.length);
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
  for($n=0; $n < $i ;$n++){
    $title = $title_0[$n];
    $stmt = $pdo->query("SELECT * from trip_plan_info WHERE group_id = '$group_ID' AND title = '$title'");
    $trip_data_file = array();
    $h = 0;
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
var add_val = [];
function trip_day_plan_add(val,val3){
  var val2 = add_val[val]+1;
  $('#days_'+val).append($('<div id="trip_day_plan'+val+val2+'"></div>').html('<input id="toMin'+val+val2+'" type="time" size="2" maxlength="2"> <input id="in_trip_day_plan'+val+val2+'" type="text" placeholder="予定記入"><input id=trip_plan type="button" value="登録" onclick="trip_plan_join('+val+','+val2+',0)">'));
  console.log(add_val[val],val,val3);
  if(val3==0){
    var param={};
    param['val'] = val;
    param['val2'] = add_val[val];
    //param['trip_title']=val;
    conn.send(JSON.stringify(param));
  }
  add_val[val]++;
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
  if(val3==0){
    var param={};
    param['join_val'] = val;
    param['join_val2'] = val2;
    //param['trip_title']=val;
    conn.send(JSON.stringify(param));
  }
};
function trip_plan_change(val,val2,val3,val4,val5){
  $("#trip_day_plan"+val+val2).html('<input class="toMin" id="toMin'+val+val2+'" type="time" size="2" maxlength="2"><input class="in_trip_day_plan" id="in_trip_day_plan'+val+val2+'" type="text"><input id=trip_plan type="button" value="登録" onclick="trip_plan_join('+val+','+val2+',0)">');
  $("#in_trip_day_plan"+val+val2).val(val5);
  $("#toMin"+val+val2).val(val4);
  if(val3==0){
    var param={};
    param['change_val'] = val;
    param['change_val2'] = val2;
    param['change_val4'] = val4;
    param['change_val5'] = val5;
    //param['trip_title']=val;
    conn.send(JSON.stringify(param));
  }
};

function trip_data_display(val,val1){
  var receive_trip_data = {};
  var sort_trip_data = [];
  receive_trip_data = <?php echo $trip_data_json;?>;
  sort_trip_data = <?php echo $trip_data_json;?>;
  receive_trip_data_length = Object.keys(receive_trip_data).length;

  var length_sort = 0;
  for(var id_1 = 0;id_1 < receive_trip_data_length;id_1++){
    var length = 0;
    for(var i=0;i < receive_trip_data_length;i++){
      var str = receive_trip_data[i][2].split('');
      if(str[0] == id_1){
        if(str[1]==null){
          str[1] = "0";
        }
        var data_num = Number(str[1])+length_sort;
        sort_trip_data[data_num][0] = receive_trip_data[i][0];
        sort_trip_data[data_num][1] = receive_trip_data[i][1];
        sort_trip_data[data_num][2] = receive_trip_data[i][2];
        sort_trip_data[data_num][3] = receive_trip_data[i][3];
        sort_trip_data[data_num][4] = receive_trip_data[i][4];
        length++;
      }
    }
    length_sort = length;
  }
  console.log(sort_trip_data);
  //$('#output').html(sort_trip_data[0][0]);
  var add = 1;
  for(var i=0;i < receive_trip_data_length;i++){
    if(sort_trip_data[i][1] == start_day){
      var str = sort_trip_data[i][2].split('');
      if(str[1]==null){
        str[1] = "0";
      }
      $('#trip_date_data').append($('<div id="days_'+str[0]+'"></div>').html(sort_trip_data[i][1]));
      add_val[str[0]] = add;
      $('#days_'+str[0]).append($('<input id=trip_plan_add type="button" value="追加" onclick="trip_day_plan_add('+str[0]+','+add+')">'));
      $('#days_'+str[0]).append($('<div id="trip_day_plan'+str[0]+str[1]+'"></div>'));
      var time_context = "'"+sort_trip_data[i][3]+"'";
      var context = "'"+sort_trip_data[i][4]+"'";
      $("#trip_day_plan"+str[0]+str[1]).html(sort_trip_data[i][3]+" "+sort_trip_data[i][4]+'<input id=trip_plan type="button" value="変更" onclick="trip_plan_change('+str[0]+','+str[1]+',0,'+time_context+','+context+')">');
      add++;
    }else{
       var start_day = new Date(start_day);
      // var get_month = start_date_1.getMonth()+1;
       start_day.setDate(start_day.getDate()+1);
       var get_month =  start_day.getMonth()+1;
       start_day = start_day.getFullYear() + "-" +  get_month +"-"+ start_day.getDate();
       i--;
       add = 1;
    }
  }
  if(val1 == 0){
    var param={};
    param['display_title'] = val;
    //param['trip_title']=val;
    conn.send(JSON.stringify(param));
  }
};

$(function(){
  var receive_pop_data = {};
  receive_pop_data = <?php echo $pop_thing_json;?>;
  receive_pop_data_length = Object.keys(receive_pop_data).length;
  for(var i=0;i < receive_pop_data_length;i++){
    $("#"+receive_pop_data[i][0]).css({'position':'absolute','top':receive_pop_data[i][1],'left':receive_pop_data[i][2]});
  }
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
    //[id^=toMin]
    var param={};
    var val = $(this).val();
    var id = $(this).attr('id');
    //console.log(id);
    param['toMin']=val;
    param['toMin_id']=id;
    conn.send(JSON.stringify(param));
  });
  $(document).on('keyup change',"[id^=in_trip_day_plan]",function(){
    var param={};
    var val = $(this).val();
    var id = $(this).attr('id');
    // console.log(id);
    param['in_trip_day_plan']=val;
    param['in_trip_day_plan_id']=id;
    conn.send(JSON.stringify(param));
  });

  <?php
  if((isset($_POST['start_date'])||isset($_POST['finish_date']))&&isset($_POST['trip_title'])){
    ?>
    var start_date_1 = new Date('<?php echo $_POST['start_date'];?>');
    var start_date = Date.parse('<?php echo $_POST['start_date'];?>');
    var finish_date = Date.parse('<?php echo $_POST['finish_date'];?>');
    var date_diff = Math.floor((finish_date-start_date)/1000/60/60/24);
    $('#output').html('<?php echo $_POST['trip_title'];?>');
    for(var i=0;i <= date_diff;i++){
      var get_month = start_date_1.getMonth()+1;
      var date = start_date_1.getFullYear() + "-" +  get_month +"-"+ start_date_1.getDate();
      $('#trip_date_data').append($('<div id="days_'+i+'"></div>').html(date));
      add_val[i] = 0;
      $('#days_'+i).append($('<input id=trip_plan_add type="button" value="追加" onclick="trip_day_plan_add('+i+',0)">'));
      //$('#days_'+i).append($('<div></div>').html('<input id="trip_day_plan_add" type="button"value=予定追加 onclick="trip_day_plan_add('+i+',0)">'));
      $('#days_'+i).append($('<div id="trip_day_plan'+i+0+'"></div>').html('<div id="trip_day_plan'+i+0+'"></div>').html('<input class="toMin" id="toMin'+i+0+'" type="time" size="2" maxlength="2"><input class="in_trip_day_plan" id="in_trip_day_plan'+i+0+'" type="text" placeholder="予定記入"> <input id=trip_plan type="button" value="登録" onclick="trip_plan_join('+i+',0,0)">'));
      start_date_1.setDate(start_date_1.getDate()+1);
    }
    <?php
  }
  ?>
});
</script>
<!-- <div id="bms_chat_header">
<div id="bms_chat_user_status">
<div id="bms_status_icon">●</div>
<div id ="bms_chat_user_name">
<?php
echo $name.'さん';
?>
<a href="logout.php">ログアウト</a>
<a href="choice.php">グループ切り替え</a>
<a href="trip_chat.php">チャットシステムへ</a>
<a href="pop_display.php">みんなで相談画面へ</a>
<button id="pop_things_save" onclick="save()">保存</button>
</div>
</div>
</div> -->
<body>
  <div class="split">
    <div class="split-item pop_display">
      <div class="selectable">
        <?php
        for($i=0;$i<$favo_things;$i++){
          echo '<div class="pop_things" id="pop_thing_'.$favo_number[$i].'">'.$favo[$i].'</div>';
        }
        ?>
      </div>
    </div>
    <div class="split-item trip_plan">
      <?php
      if(count($title_0) > 0){
        for($i=0;$i<count($title_0);$i++){
          $title = '"'.$title_0[$i].'"';
          ?>
          <div><input id=trip_data type="button" onclick='trip_data_display(<?= $title?>,0)' value=<?= $title?>></div>

          <?php
        }
      }
      ?>
      <form id="trip_data" action="trip_planning.php" method="post">
        <div><input id="start_date" name="start_date" type="date"></div><p>~</p><div><input id="finish_date" name="finish_date" type="date"></div>
        <div><input id="trip_title" name="trip_title" type="text" placeholder="タイトル"></div>
        <div><input id=trip_main_data type="submit" value="送信"></div>
      </form>
      <div>旅行タイトル：<span id="output"></span></div>
      <div id="trip_date_data"></div>
      <div><input id=trip_plans type="button" value="保存" onclick="trip_plan_save()"></div>
    </div>
  </div>
  <div id="pointer"></div>
</body>
</html>

<?php
//　実験　3-4人の仲良しグループを集める。ディスカッション時間決める。時間を決めないで、どちらが決めやすかったか。
//　LINE、Googleドキュメント比較。仮定、どうしても会えない人用、集まれない人用。
//　研究目的を明確化する→それによりプランが決めやすくなる
// 新着のチャットコメントを出るようにする
//　プランの日にち・時間を決めるとき、アラートで出すようにする
// 10-24 memo データが保存されない、値はすべて取得されているが、#days＿　で、余計な時間を取得している　php事態にエラーが出ているか確認　
?>
