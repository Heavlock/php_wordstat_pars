<?php
  error_reporting(E_ALL);
  global $dbhost,$dbname,$dbusername, $dbpassword, $connect;
  
	$dbhost     = 'localhost';
	$dbname     = 'statuskvo_db';
	// 
/*
	$dbname     = 'fttour_test';
    $dbusername = 'fttour_test';
    $dbpassword = '6q1QQ&Rg'; 
*/

	$dbname     = 'aivoices';
    $dbusername = 'aivoices';
    $dbpassword = 'uD1gE1eV1qtJ9b'; 

	if (strpos($_SERVER['SERVER_NAME'],'127.0.0.1')!==false) {
		$dbname     = 'wdmonthes';
		$dbusername = 'root';
		$dbpassword = ''; 
		echo '<br>$dbusername='.$dbusername;
	};
	db_connect($dbname);
	
	$sql = '
CREATE TABLE IF NOT EXISTS `wp_wordstat` (
  `id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `root` varchar(128),
  `phrases` text,
  `phrases1` text,
  `user_id` int(2) default 1,
  `region` int(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
	SQL($sql);
  //--------------------------------------------------------------------------
  function getpath()
  {
    $path = getcwd();  if (strpos($path, ':') > 0) { $path.="\\"; } else { $path.="/";};
    return  $path;
  };
  //--------------------------------------------------------------------------
  function redirect($url)
  {
    addtolog('redirect '.$url);
    $output =
       '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">';
    $output.='<html><head>';
    $output.='<meta http-equiv="content-type" content="text/html; charset=utf-8">';
    $output.='<meta http-equiv="refresh" content="0;';
    $output.=' url='.$url.'">';
    $output.='<link rel="StyleSheet" type="text/css" href="design/style.css">';
    $output.='</head>';
    $output.='<body>';
    $output.='</body></html>';
    @header("HTTP/1.0 200 OK");
    @header("Content-type: text/html;charset=utf-8");
    @header("Cache-Control: no-cache, must-revalidate, max-age=0");
    @header("Expires: 0");
    @header("Pragma: no-cache");
    print $output;
//    addtolog('output='.$output);
    die();
  };

  function rewritereport($name,$str)
  {
    $file = @fopen ($name,"w+");
    if (strlen($str)>0) @fputs($file, $str);
    @fclose ( $file );
  };
  function rewritereportarr($name,$arr)
  {
    $file = @fopen ($name,"w+");
    foreach($arr as $a) {
      if (strlen($a)>0) @fputs($file, trimall($a)."\x0D\x0A");
    }
    @fclose ( $file );
  };
  //============================================================================
//  function trimall($str, $substr = '', $charlist = "\t\n\r\x0B\x97\xAB\xBB")
  function trimall($str, $substr = '', $charlist = "\t\n\r\x0B")
  {
    return str_ireplace(str_split($charlist), $substr, $str);
  };
  //============================================================================
  function trimspaces($str)
  {
  $s = $str;
  while (1) {
    if (strlen($s)>0) {
      if ($s[0] == ' ') { $s = substr($s, 1, strlen($s)-1);  } else { break;};
    } else { break; };
  };
  while (1) {
    if (strlen($s)>0) {
      if ($s[strlen($s)-1] == ' ') { $s = substr($s, 0, strlen($s)-1);  } else { break;};
    } else { break;};
  };
  return $s;
  };
  //--------------------------------------------------------------------------
  function decho($str) {
    echo "<br>".$str; @ob_flush();
  };
  //--------------------------------------------------------------------------
  function detect_utf($s){
    $s=urlencode($s); // в некоторых случа€х - лишн€€ операци€ (закоментируйте)
    $res = 0;
    $j=strlen($s);

    $s2=strtoupper($s);
    $s2=str_replace("%D0",'',$s2);
    $s2=str_replace("%D1",'',$s2);
    $k=strlen($s2);

    $m=1;
    if ($k>0){
      $m=$j/$k;
      if (($m>1.2)&&($m<2.2)){ $res = 1; }
    }
    return $res;
  }
  //----------------------------------------------------------------------
  function rewritefile($name, $val)
  {
    @file_put_contents($name,$val);
//    exec('chmod '.$name.' 0777');
    @file_put_contents($name,$val);
  };
  //----------------------------------------------------------------------
  function print_arr($hdr, $arr = array(), $utf = true, $view=1, $name='debug.log') {
//    return;
    rewritereport(getpath().'report.txt',print_r($arr,true));
    $file = file(getpath().'report.txt');
    if ($view) decho('------------------'.$hdr.'------------------------------------');
    addtolog('------------------'.$hdr.'------------------------------------', $name);
    foreach($file as $str) {
      $str = str_ireplace("\x0A",'',$str);
      $str = str_ireplace("\x0D",'',$str);
//      if ($utf==true) $str = mb_convert_encoding($str,"UTF-8", "windows-1251");
      if ($str<>'') addtolog($str);
      if (is_string($str))
      $str = str_ireplace("\x20",'&nbsp;',$str);
      if (($str<>'') and ($view))  decho($str);
    };
//    @unlink(getpath().'report.txt');
  };
  //--------------------------------------------------------------------------
  function addtolog($str, $name = 'debug.log') {
//    chmod (getpath().'/', 0777);
    if (file_exists($name)) { 
      if (filesize($name)>(10*1024*1024)) { unlink($name);  $file = fopen ($name,"w+"); } else
      $file = fopen ($name,"a+"); 
    } else
     { $file = fopen ($name,"w+");};
    @fputs($file, date("d.m.Y h:i:s",time()).' '.$str);
    fputs($file, "\r");
    fclose ( $file );
  };
  //--------------------------------------------------------------------------
  function addtofile($name, $str) {
    if (file_exists($name)) { $file = fopen ($name,"a+"); } else
                            { $file = fopen ($name,"w+");};
    fputs($file, $str);
    fputs($file, "\n");
    fclose ( $file );
  };
	//--------------------------------------------------------------------------
	function SQL($sql, $view=false, $debug=false) {
		global $dbhost,$dbname,$dbusername, $dbpassword, $connect;
		mysqli_query( $connect, $sql);
		$error = mysqli_error( $connect );
//		if ($view)
//		decho('$sql='.$sql.' error='.$error);
		if ($debug)
		@addtolog('$sql='.$sql.' error='.$error);
		return $error;
	}
	//--------------------------------------------------------------------------
	function db_connect() {
		global $dbhost,$dbname,$dbusername, $dbpassword, $connect;
		@$connect = mysqli_connect($dbhost, $dbusername, $dbpassword, $dbname)
						 or die("Can't connect to MYSQL database	error".mysql_error());
		mysqli_select_db( $connect, $dbname);
		SQL("SET character_set_client='utf8'");
		SQL("SET character_set_connection='utf8'");
		SQL("SET character_set_results='utf8'");
	};
	//----------------------------------------------------------------------
	function get_field( $name, $table, $where ) {
		global $dbhost,$dbname,$dbusername, $dbpassword, $connect;
		$sql	 = 'select `'.$name.'` from `'.$table.'` where '.$where;
		$q	 = mysqli_query( $connect, $sql);
		SQL($sql);
		$row = mysqli_fetch_assoc($q);
		return $row[$name];
	};
	//----------------------------------------------------------------------
	function insert_row($row, $table, $debug=1) {
		global $dbhost,$dbname,$dbusername, $dbpassword, $connect;

		if (strlen(mysqli_stat($connect))<10) db_connect();
		$sval	= '';
		$sql	 = 'insert into `'.$table.'` (';
		foreach($row as $name=>$value) {
			$sql .="`$name`,";
			$sval.="'$value',";
		};
		$sql = substr($sql,0,strlen($sql)-1);
		$sval= substr($sval,0,strlen($sval)-1);
		$sql.= ') values ('.$sval.')';
		
		SQL($sql, 1, $debug);
		
		return true;
	};
	//----------------------------------------------------------------------
	function delete_rows($table, $where='') {
		global $dbhost,$dbname,$dbusername, $dbpassword, $connect;
		$sval	= '';
		$sql	 = 'delete from `'.$table.'`';
		if (strlen($where)>0) $sql.= ' where '.$where;

		SQL($sql,0,1);
		
		return true;
	};

	//----------------------------------------------------------------------
	function get_update_str($row, $table, $where, $debug=0) {
		global $dbhost,$dbname,$dbusername, $dbpassword, $connect;
		$sval	= '';
		$sql	 = 'update `'.$table.'` set ';
		foreach($row as $name=>$value) $sql .="`$name`='$value',";
		$sql = substr($sql,0,strlen($sql)-1);
		$sval= substr($sval,0,strlen($sval)-1);
		if (strlen($where)>0) $sql.= ' where '.$where;
		return $sql;
	};
	//----------------------------------------------------------------------
	function update_row($row, $table, $where, $debug=0) {
		global $dbhost,$dbname,$dbusername, $dbpassword, $connect;
		$sql = get_update_str($row, $table, $where, $debug);
		SQL($sql);
		return true;
	};
	//----------------------------------------------------------------------
	function update_field($name, $value, $table, $where) {
		global $dbhost,$dbname,$dbusername, $dbpassword, $connect;
		$sql	 = 'update `'.$table.'` set `'.$name."`='$value' where ".$where;
		$q	 = mysqli_query( $connect, $sql);
		$row = mysqli_fetch_assoc($q);
		return true;
	};
	//----------------------------------------------------------------------
	function read_table($select, $table, $where = '', $all=1, $order = '', $limit='') {
		global $dbhost,$dbname,$dbusername, $dbpassword, $connect;
		$sql = 'select '.$select.' from `'.$table.'`';
		if (strlen($where)>0) $sql.= ' where '.$where;
		if (strlen($order)>0) $sql.= ' order by '.$order;
		if (strlen($limit)>0) $sql.= ' LIMIT '.$limit;
		@addtolog($dbhost.':'.$dbname.':'.$dbusername.':'.$dbpassword);
		@addtolog('read_table $where='.$where.' $all='.$all.' $order='.$order.' $limit='.$limit);
		
		if (strlen(mysqli_stat($connect))<10) db_connect();
//		decho('mysqli_stat($connect)='.mysqli_stat($connect));
		$q	 = mysqli_query( $connect, $sql);
		$res = 0;
		if ($all) {
			$res = array();
			if ($q)
			while ( $row= mysqli_fetch_assoc($q)) {
				$res[] = $row; 
			};
		} else {
			if ($q)
			if ($row = mysqli_fetch_assoc($q)) {
				if ((strpos($select, '*')===false) && (strpos($select, ',')===false)) $res = $row[$select]; 
				else $res = $row;
			};
		};
		decho('read_table sql='.$sql.' error='.mysqli_error( $connect ).' count='.count($res));
		@addtolog('read_table sql='.$sql.' error='.mysqli_error( $connect ).' count='.count($res));
		return $res;
	};
	//----------------------------------------------------------------------
	function get_row( $table, $where ) {
		global $dbhost,$dbname,$dbusername, $dbpassword, $connect;
		$sql	 = 'select * from `'.$table.'` where '.$where;
		$q	 = mysqli_query( $connect, $sql);
		echo '<br>sql='.$sql.' error='.mysqli_error( $connect );
//		addtolog('sql='.$sql.' error='.mysqli_error( $connect ));
		$row = mysqli_fetch_assoc($q);
		return $row;
	};
	//----------------------------------------------------------------------
	function check_and_save_table($row, $table, $where) {
		$arr = read_table('*', $table, $where);
		if (count($arr)>0) {
			update_row($row, $table, $where);
		} else {
			insert_row($row,$table); 
		};
	};

	function get_value($name)
	{
		global $connect;
		$q = mysqli_query($connect, "select * from `variables` where name='$name'");
		if ($row = mysqli_fetch_array($q)) 
		{
			$var = $row['value'];
		};
		return $var;
	}
	function set_value($name,$value)
	{
		global $connect;
		$q = mysqli_query($connect,"select * from `variables`  where name='$name'");
		if ($row = mysqli_fetch_array($q)) {
			mysqli_query($connect,"update `variables` set value= '$value' where name='$name'");
		} else {
			mysqli_query($connect,"INSERT INTO `variables` ( `name`, `value`) values ( '$name', '$value')");
		};
		return true;
	}
	//----------------------------------------------------------------------
	function send_header($code='UTF-8'){
	echo '<!DOCTYPE html>
<html lang="ru">
	<head><meta http-equiv="Content-Type" content="text/html; charset='.$code.'" />
  ';
	};
