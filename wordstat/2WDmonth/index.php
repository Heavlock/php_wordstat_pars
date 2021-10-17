<?php
	session_start();
	$path = getcwd();  if (strpos($path, ':') > 0) { $path.="\\"; } else { $path.="/";};
//  echo ' ';
//  echo $path;
//  print_r($_SESSION);
	global $connect;
	require_once('config.php');
	require_once('header.php');
	require_once('functions.php');

	if ((isset($_POST['login'])) && (isset($_POST['pass'])))
	{
//    	print_r($_POST); die();
		$login = $_POST['login'];
//    	$pass = md5($_POST['pass']);
		$pass = $_POST['pass'];
		$q   = "SELECT `id`
            FROM `users`
            WHERE `login`='$login' AND `pass`='$pass'";
		$sql = mysqli_query($connect,$q) or die(mysql_error());

		if (mysqli_num_rows($sql) == 1) {
			$row = mysqli_fetch_array($connect,$sql);
			$id = $row['id'];
			$_SESSION['user_id'] = $id;
			if ($_SESSION['user_id']==1) {
				redirect('main.php');
			} else {
				redirect('main.php');
			};
		}
	} else if (isset($_SESSION['user_id'])) {
		if ($_SESSION['user_id'] == '1') {
			$url = 'main.php';
		} else {
			$url ='main.php';
		};
		redirect($url);
		exit;
	};

	if (isset($_POST['page']) && ($_POST['page']=='lost'))
	{ 
		$output = <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html><head>
<meta http-equiv="content-type" content="text/html; charset=windows-1251">
<meta http-equiv="refresh" content="0; url=index.php">
<link rel="StyleSheet" type="text/css" href="design/style.css">
</head>
<body>
<center><br>
<form name="form1" method="post" action="index.php">
<table border="0" align="center" cellpadding="2" cellspacing="0" class="back2">
      <tr><td><b>Логин&nbsp;&nbsp;</b></td><td><input type="text" name="login"></td></tr>
      <tr><td><b>Пароль&nbsp;&nbsp;</b></td><td><input type="pass" name="pass"></td></tr>
      <tr><td colspan="2"><center>
          <input type="submit" name="Submit" value="Войти" style="border: solid 1px #303030; background-color:c5c5c5; font-size:10pt; height:20px;"><br><br><a href="index.php?page=lost">Забыли пароль?</a>
       </form></center>
</body>
</html>
EOF;
	} else {
		$output = <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html><head>
<meta http-equiv="content-type" content="text/html; charset=windows-1251">
<link rel="StyleSheet" type="text/css" href="design/style.css">
</head><body>
<center><br>
<form name="form1" method="post" action="index.php">
  <table border="0" align="center" cellpadding="2" cellspacing="0">
      <tr><td><b>Логин&nbsp;&nbsp;</b></td>
        <td><input type="text" name="login"></td></tr>
      <tr><td><b>Пароль&nbsp;&nbsp;</b></td><td><input type=password name="pass"></td></tr>
      <tr><td colspan="2"><center>
          <input type="submit" name="Submit" value="Войти" style="border: solid 1px #303030; background-color:c5c5c5; font-size:10pt; height:20px;"><br><br><a href="index.php?page=lost">Забыли пароль?</a>
</form></center></body></html>
EOF;
	}
	@header("HTTP/1.0 200 OK");
	@header("Content-type: text/html;charset=UTF-8");
	@header("Cache-Control: no-cache, must-revalidate, max-age=0");
	@header("Expires: 0");
	@header("Pragma: no-cache");
	print $output;
