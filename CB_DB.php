<?php

/**
* CB_DB.php
*
* @copyright  2004-2007 CYBRiDGE
* @license    CYBRiDGE 1.0
*/

CLASS CB_DB{

  function CB_DB(){
    /*  global $GLADIUS_DB_ROOT;
    $GLADIUS_DB_ROOT = VAR_DIR . '/data/';*/
    $result_2 = $this->adodb = ADONewConnection(DB_TYPE);
    //  var_dump($this->adodb = ADONewConnection(DB_TYPE));
    //echo DB_PASS;
    $result = $this->adodb->Connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    //var_dump ($result);
    //        $this->adodb->debug = 1;
    if(!$result){
      if(DB_TYPE == "gradius" && @mkdir($GLADIUS_DB_ROOT.DB_NAME) && @mkdir(VAR_DIR.'/data/'.DB_NAME)){
        return $this->CB_DB();
      }
      $this->error("接続エラー");
    }
  }
  function Execute($sql){
    $sql = preg_replace("/\"/is","'",$sql);
    $query = explode(";",$sql);
    if(!is_array($query)){
      $query = array($sql);
    }
    foreach($query AS $val){
      if(trim($val)){
        $data[] = $this->adodb->execute($val.";");
      }
    }
    return $data;
  }


  function GetAll($sql){
    return $this->adodb->GetAll($sql);
  }

  function GetRow($sql){
    $rows = $this->GetAll($sql);
    return $rows[0];
  }

  function Insert($table,$data){
    foreach($data as $key=>$val){
      if(preg_match("/^_/is",$key)){
        unset($data[$key]);
      }
    }

    if(DB_TYPE == "mysql"){
      foreach($data as $key=>$val){
        $data[$key] = preg_replace(array("/\"/is","/'/is"),array("’","’"),$data[$key]);
        $data[$key] = preg_replace("/\\\/is","",$data[$key]);
      }
    }

    foreach($data as $key=>$val){
      $fields[] = $key;
      $values[] = "'{$val}'";
    }
    $sql = "INSERT INTO {$table} (".implode(",", $fields).") VALUES (".implode(",", $values).")";
    @$this->Execute($sql);

    $tables = $this->GetAll('SHOW TABLES');
    foreach($tables as $value){
      if(empty($value["table"])){
        $value["table"]="";
      }
      if(preg_match("/{$table}/is",$value["table"])){
        return $value["top_insert_id"];
      }
    }
    // 2009.11.05 EDIT ST
  /*        var_dump($this->adodb->connectionId);
    if(@mysql_insert_id($this->adodb->connectionId) != ""){
      return mysql_insert_id($this->adodb->connectionId);
    }*/
    // 2009.11.05 EDIT ED
  }

  function Update($table,$data,$where){
    foreach($data as $key=>$val){
      $values[] = "{$key} = '{$val}'";
    }
    $sql = "UPDATE {$table} SET ".implode(",", $values)." WHERE {$where}";
    @$this->Execute($sql);

    // 2012.07.30 TEST ST
    /*
    $log_file = "C:\Program Files\Apache Group\Apache\htdocs\opentask_all\webapp\class\update.log";
    $fp = fopen($log_file, "a");
    fputs($fp, $sql."\n");
    fclose($fp);
    */
    // 2012.07.30 TEST ED

  }

  function Delete($table,$where){
    $sql = "DELETE FROM {$table} WHERE {$where}";
    $this->Execute($sql);
  }

  function Debug($flag = 1){
    $this->adodb->debug = $flag;
  }

  function Error($str){
    die($str);
  }
}
