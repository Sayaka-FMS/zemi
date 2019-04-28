<?php 

// カレンダーの年月をタイムスタンプを使って指定 
if (isset($_GET["date"]) && $_GET["date"] != "") { 
  $date_timestamp = $_GET["date"]; 
} else { 
  $date_timestamp = time(); 
} 

$month = date("m", $date_timestamp); 
$year = date("Y", $date_timestamp); 

$first_date = mktime(0, 0, 0, $month, 1, $year);  
$last_date = mktime(0, 0, 0, $month + 1, 0, $year);  

// 最初の日と最後の日の｢日にち」の部分だけ数字で取り出す。  
$first_day = date("j", $first_date);  
$last_day = date("j", $last_date);  

// 全ての日の曜日を得る。  
for($day = $first_day; $day <= $last_day; $day++) {  
  $day_timestamp = mktime(0, 0, 0, $month, $day, $year);  
  $week[$day] = date("w", $day_timestamp);  
}  

?>  
<html>  
<head>  
<title>カレンダー</title>  
<style type="text/css"> 
 a:link {color: #3366FF; background-color: transparent; 
 text-decoration: none; font-weight: bold;} 
 a:visited {color: #2B318F; background-color: transparent; 
 text-decoration: none; font-weight: bold;} 
 a:hover {color: #00BFFF; background-color: transparent; 
 text-decoration: underline;} 
 body {color: #333333; background-color: #FFFFFF;} 
 table {border: 1px solid #CCCCCC; border-collapse: collapse; 
 margin-bottom: 1em;} 
 td {border: 1px solid #CCCCCC; height: 5em; width: 5em;  
     vertical-align: middle; padding-left: 1em; padding-top: 2px;  
     padding-right: 1em; padding-bottom: 2px;} 
 th {border: 1px solid #CCCCCC; color: #333333;  
     background-color: #F0F0F0; padding: 5px;} 
</style> 
</head>  
<body>  
<table border="1">  
  <tr> 
    <th colspan="2"><a href="calendar.php?date= 
<?php print (strtotime("-1 month", $first_date)); ?>">前月</a></th> 
    <th colspan="3"><?php print (date("Y", $date_timestamp) . "年" . date("n", $date_timestamp) . "月"); ?></th> 
    <th colspan="2"><a href="calendar.php?date= 
<?php print (strtotime("+1 month", $first_date)); ?>">次月</a></th> 
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
     
    for ($day = $first_day; $day <= $last_day; $day++) { 
      if ($week[$day] == 0) { 
        print ("</tr>\n<tr>\n"); 
		  ?>
		 <form name="form1" method="post" action="new_plans.php">
		<input type="hidden" name="id" value="<?php print $id;?>">
		<input type="hidden" name="year" value="<?php print date("Y", $date_timestamp) ;?>">
		<input type="hidden" name="month" value="<?php print date("n", $date_timestamp);?>">
		<input type="hidden" name="day" value="<?php print $day;?>">   
     <?php
      } 
      print ("<td><a href='new_plans.php'>".$day."</a></td>\n"); 
	  print  "</form>";
    }  
     // カレンダーの最後の空白部分  
    for ($i = $week[$last_day] + 1; $i < 7; $i++) {  
      print ("<td></td>\n");  
    }  
    ?>  
  </tr>  
</table>  
</body> 
</html>