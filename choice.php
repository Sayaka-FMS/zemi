<?php
session_start();
$dsn = 'mysql:host=localhost;dbname=test;charset=utf8mb4';
$username = 'root';
$password = '';
date_default_timezone_set( 'Asia/Tokyo' );
$pdo = new PDO( $dsn, $username, $password );
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
// デフォルトのフェッチモードを連想配列形式に設定
// (毎回PDO::FETCH_ASSOCを指定する必要が無くなる)
$pdo->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
if(isset( $_SESSION[ 'username' ] )){
	$userID = $_SESSION['userID'];


	if ( empty( $_POST["new_group_ID"] ) ) {
		echo "グループIDを入力してください";
	} else if ( empty( $_POST[ "new_group_password" ] ) ) {
		echo "パスワードを入力してください";
	}else {
		try {
			$new_group_ID = $_POST["new_group_ID"];
			$new_group_name = $_POST["new_group_name"];
			$new_group_password = $_POST["new_group_password"];
			$time = date( "Y-m-d H:i:s" );
			$stmt2 = $pdo->prepare( "INSERT INTO group_info (group_name,group_password,group_number,time) VALUES (?,?,?,?)" );
			$stmt2->execute( array($new_group_name,$new_group_password,$new_group_ID,$time) );
			//ここにユーザアカウントのグループアカウント登録を行う
		} catch ( Exception $e ) {
			echo $e->getMessage() . PHP_EOL;
		}
		$_SESSION['group_name'] = $new_group_name;
		$_SESSION['group_ID'] = $new_group_ID;
		header("Location:trip_chat.php");
	}


	if ( empty( $_POST["group_ID"] ) ) {
		echo "グループIDを入力してください";
	} else if ( empty( $_POST[ "group_password" ] ) ) {
		echo "パスワードを入力してください";
	}else {
		try {
			$group_ID = $_POST["group_ID"];
			$stmt = $pdo->prepare( "SELECT * FROM group_info where group_number = ?" );
			$stmt->execute( array($group_ID) );
			$group_sanshou = $stmt->fetch();
			if(!$group_sanshou){
				echo "指定されたグループIDが存在しません</br>";
			}else{
				if($group_sanshou['group_password']!=$_POST['group_password']){
					echo "パスワードが間違っています";
				}else{
					//ここにユーザアカウントのグループアカウント登録を行う（もし登録がまだだった場合・登録している場合の2パターン）
					$_SESSION['group_name'] = $group_sanshou['group_name'];
					$_SESSION['group_ID'] = $group_sanshou['group_ID'];
					header("Location:trip_chat.php");
				}
			}

		} catch ( Exception $e ) {
			echo $e->getMessage() . PHP_EOL;
		}
	}
}else {
	header("Location:login.php");
}

?>
<html lang="ja">
<head>
	<meta charset="utf-8">
</head>
<body>
	<p>現在所属しているグループ</p>
  <?php
	if(@isset( $userID )){
	try {
	 $stmt = $pdo->prepare( "SELECT * FROM user_group_info where user_id = ?" );
 	 $stmt->execute( array($userID) );
 	 $stmt->fetch();
	 foreach($stmt as $value){
		 ?>
		 <form method="post" name="group_login" action="trip_chat.php">
		 <a href="javascript:group_login.submit()"><?=$value['group_name'] ?></a></br>
		 <input type="hidden" name="group_ID" value=<?=$value['group_ID'] ?>>
		 </form>
		 <?php
	 }
	} catch ( Exception $e ) {
		echo $e->getMessage() . PHP_EOL;
	}
  }
	?>
	<form method="post" name="new_group_login" action="choice.php">
	<a href="javascript:new_group_login.submit()">新しいグループにログイン</a></br>
	<input type="hidden" name="exit_group" value="ok">
  </form>
	<?php
	if(isset($_POST['exit_group'])){
		?>
   <form method="post" action="choice.php">
		<b>グループID</b>
 	 	<input name="group_ID" type="text" size="20">
 	 	</br>
 	 	<b>パスワード</b>
 	 	<input name="group_password" type="password" size="20">
    </br>
    <input type="submit" name="login" value="ログイン">
	 </form>
		<?php
	}
	?>
	<form method="post" name="new_group_making" action="choice.php">
		<input type="hidden" name="new_group" value="ok">
		<a href="javascript:new_group_making.submit()">新しくグループを作成する</a>
	</form>
	<?php
	if(isset($_POST['new_group'])){
		?>
	 <form method="post" name="new_group_making_1" action="choice.php">
		<b>NEWグループID</b>
		<input name="new_group_ID" type="text" size="20">
		</br>
		<b>NEWグループ名</b>
		<input name="new_group_name" type="text" size="20">
		</br>
		<b>NEWパスワード</b>
		<input name="new_group_password" type="password" size="20">
		</br>
		<input type="submit" name="login" value="ログイン">
	 </form>
		<?php
	}
	?>
</body>
</html>
