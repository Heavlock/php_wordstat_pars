<?php
	// скрипт формат выдачи Яндекс Вордстат
	// http://127.0.0.1/wordstat/2WDmonth/main.php
	// http://127.0.0.1/wordstat/2WDmonth/wordstat.php?word=вода
	// https://status-kvo42.ru/wordstat/2WDmonth/wordstat.php
	// https://status-kvo42.ru/wordstat/2WDmonth/main.php
	// https://status-kvo42.ru/tools/adminer.php
	
	// http://test.ft-tour.ru/wordstat/2WDmonth/wordstat.php
	// http://test.ft-tour.ru/wordstat/2WDmonth/main.php
	// http://test.ft-tour.ru/wordstat/2WDmonth/main.php
	require_once('functions.php');

	function __decode($response, $browserUserAgent, $cookieValue) 
	{
		$key = substr($browserUserAgent,0, 25).($cookieValue || '');//.eval($response['key']);
		$encryptData = $response['data'];
		$decryptData = '';
		for($i=0;$i< strlen($encryptData);$i++) 
		{
			$charCode = substr($encryptData,$i,1) ^ substr($key,$i % strlen($key),1);
			$decryptData.= $charCode;
		}
		return $decryptData;
	};
	
	require_once('functions.php');
	require_once('curl.php');
	
	session_start();
	global $dbhost,$dbname,$dbusername, $dbpassword, $connect;
	global $count_region,$monthes_names;
	global $region, $arr_region, $name_region, $region_name, $with_variants, $need_limit;
	global $wordstat;

	$fuid01	= '';
	if (file_exists('cookies.txt')):
	$file	= file_get_contents('cookies.txt');
	if (file_exists('cookies.txt'))
	{	
		$arr	= explode("\x0A",$file);
		$names 	= '';
		foreach($arr as $a)
		{
			$_arr = explode("\x09",$a);
			if (!isset($_arr[5])) continue;
			if ($_arr[5]!=='fuid01') continue;
			$fuid01	= $_arr[6];
			break;
		}
	};
	endif;
	$_SESSION['fuid01'] = $fuid01;
	$debug = true;

	if (isset($_REQUEST['action']))
	{
		$action = strtolower($_REQUEST['action']);
		addtolog('$action='.$action);
		switch($action)
		{
			case 'send-file':
				$file	= 'input.txt';
				file_put_contents($file,$_REQUEST['body']);
				die(json_encode(['result'=>'ok','action'=>$action]));
				break;
			case 'send':
				$p = $_SESSION['wordstat'];
				$key = $_REQUEST['key'];
		
				$p = __decode($p, $_SERVER['HTTP_USER_AGENT'], $fuid01);
				die(json_encode(['result'=>'ok','action'=>$action,'key'=>$key,'fuid01'=>$fuid01,'p'=>$p]));
			
			case 'send-data':
				$data 	= $_REQUEST['buf'];
				$data1	= urldecode($data);
				$data1	= json_decode($data1,true);
				addtolog('send-data $data='.print_r($data1,1));
				
				$row	= [
					'root'		=> $_SESSION['keyword'],
					'numpage'	=> $_SESSION['numpage'],
					'phrases'	=> serialize($data1['content']['includingPhrases']['items']),
					'phrases1'	=> serialize($data1['content']['phrasesAssociations']['items'])
				];	
				addtolog('$row='.print_r($row,1));
				insert_row($row,'wp_wordstat');
				die(json_encode(['result'=>'ok','action'=>$action,'data'=>$data1]));
		}
	}	
	send_header('utf-8');

	$monthes = array('0','01','02','03','04','05','06','07','08','09','10','11','12');
	$monthes_names = array(	'Январь','Февраль','Март','Апрель','Май','Июнь',
					'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
	
	set_time_limit(0);
	$keywords = array();
	$keywords = file('input.txt');
	if (strlen($keywords[count($keywords)-1])<4) unset($keywords[count($keywords)-1]);
	$maxcount = count($keywords);

	for ($num=0; $num<count($keywords); $num++) {
		$s = $keywords[$num];
		$s = str_ireplace("\r", ' ', $s);
		$s = str_ireplace("\n", ' ', $s);
		$s = str_ireplace('+', ' ', $s);
		$s = str_ireplace('  ', ' ', $s);
		$keywords[$num] = trimspaces($s);
	};

	$proxy	= array();
	if (file_exists('proxy.txt')) $proxy  = file('proxy.txt');

	$connect = mysqli_connect($dbhost, $dbusername, $dbpassword, $dbname)
						or die("Can't connect to MYSQL database");
	mysqli_select_db( $connect, $dbname );

	$cookies_arr = array ();
	$begtime = date("d-m-y H:i:s",time());
	$begtime = time();

	$global_time = $_SESSION['global_time'];
	$time = time() - $global_time;

	$count_region =1;
	$arr_region 	= array();
	$region_name 	= array();
	for($i=1;$i<5;$i++)  $arr_region[$i]  = get_value('region'.$i);
	for($i=1;$i<5;$i++)  $region_name[$i] = get_value('region_name'.$i);
	$region = $arr_region[$count_region];
?>
<html><head>
<script src="/wordstat/2WDmonth/jquery.js"></script>
</head><body>
<script>
	var milisec=0;
	var flag = "<?php echo $flag_run; ?>";
	var s="<?php echo $time; ?>";
	var seconds=s*1;
	function display(){
		if (milisec>=9){
			milisec=0;
			seconds+=1;
		} else
			milisec+=1;
		$('input[name="counter"]').val(seconds);
		$('input[name="word"]').val(<?= $_SESSION['numkeyword'] ?>);
		$('input[name="page"]').val(<?= $_SESSION['numpage'] ?>);
		setTimeout("display()",100);
	};
	display();
</script>

<table border="0">
  <tr>
  <td align="center">
	<form name="progress">
	  <font face="Arial"><strong>Время работы:</strong></font>
	  <input type="text" size="8" name="counter">
	  <font face="Arial"><strong>сек</strong></font>
&nbsp;&nbsp;&nbsp;&nbsp;
	  <font face="Arial"><strong>Отработано</strong></font>
	  <input type="text" size="8" name="word" value="<?= $_SESSION['numkeyword'] ?>">
	  <font face="Arial"><strong>из</strong></font>
	  <input type="text" size="8" name="maxword" value="<?php echo $maxcount ?>">
&nbsp;&nbsp;&nbsp;&nbsp;
	  <input type="text" size="8" name="page" value="<?php echo $_SESSION['numpage'] ?>">
&nbsp;&nbsp;&nbsp;&nbsp;
	  <input type="text" size="8" name="region" value="<?php echo $count_region ?>">
&nbsp;&nbsp;&nbsp;&nbsp;
	  <label><strong>wordstat.yandex.ru</strong></label>
	</form>
  </td>
  </tr>
</table>
<div id="log" style="width:100%;boreder:1px solid #0000;">Процесс<br></div>
<?php
	$referer='wordstat.yandex.ru';
	$info = array();
	if (isset($_REQUEST['region'])) $count_region = $_REQUEST['region']; else
									$count_region =1;

	if (!isset($_SESSION['numkeyword'])) $_SESSION['numkeyword'] = 0;
	$info = array();
	$key = trimall($keywords[$_SESSION['numkeyword']]);
	getInfoStatMonth($key);
	
	$_SESSION['numpage']++;
	if ($_SESSION['numpage']>=40) 
	{	
		$_SESSION['numpage'] = 0;
		$_SESSION['numkeyword']++;
		if ($_SESSION['numkeyword'] >= count($keywords)) die();
	};
	
	$global_time = time() - $global_time;
	$_SESSION['global_time'] = $global_time;
addtolog('numpage='.$_SESSION['numpage'].' numkeyword='.$_SESSION['numkeyword']);
	if (file_exists('stop.txt')) die();
	redirect('wordstat.php');
die('!!!');
	//-----------------------------------------------------------------
	function wordstat_captcha()
	{
		global $referer,$cookies_arr,$location;
        $p = get_page_curl('https://mc.yandex.ru/resource/watch.js');
        $p = get_page_curl('https://direct.yandex.ru/js-c/direct-simple.js');
        $p = get_page_curl('https://mc.yandex.ru/watch/292098');
        $p = get_page_curl('https://kiks.yandex.ru/su/');
        $p = get_page_curl($location);
        $p = get_page_curl('https://kiks.yandex.ru/system/fc06.html');
		return true;
	}
	//-----------------------------------------------------------------
	function getInfoStatMonth($keyword)
	{
		global $proxy,$all_useragents,$numproxy,$headers,$cookies,$socks;
		global $referer,$user_agent,$all_useragents,$cookies_arr,$location,$outheaders;

		$login		= 'direct3-356418-t54h';
		$password	= '0RglSz7KcxmDsDOpbEAc31337';
		
		$login		= 'direct4-356419-zr9x';
		$password	= 'u1JyVSEzWQQJ5VVtQsUs31337';

		$login		= 'direct2-356417-tu86';			// 2
		$password	= 'CUKtkhdV24N5PCtLaneT31337';
		
		$login		= 'direct-356416-o3vj';
		$password	= 'cTMkbks8oFuYPh65cyQY31337';
		
		if (strlen($keyword)<3) return false;
		addtolog("getInfoStatMonth=======================word=".$keyword);

		$outheaders					= set_outheaders();

		if (!isset($_SESSION['csrf_token'])):
		$url = 'https://passport.yandex.ru/auth/welcome?origin=home_yandexid&retpath=https%3A%2F%2Fyandex.ru&backpath=https%3A%2F%2Fyandex.ru';
		$p = get_page_curl($url);
		$pos = strpos($p,'name="csrf_token"');
		addtolog('strpos(csrf_token)='.$pos);
		if ($pos === false) die();
		$pos = strpos($p,'value="',$pos)+7;
		$pos1= strpos($p,'"',$pos);
		$csrf_token = substr($p,$pos,$pos1-$pos);
		$csrf_token = str_replace(':','%3A',$csrf_token);
		$_SESSION['csrf_token'] = $csrf_token;
		

//		$url = 'https://passport.yandex.ru/registration-validations/accounts/input-login';
		$count = 0;

		if (file_exists('stop.txt')) die();
		addtolog('-----------------------------------------------------------------------------------count='.$count);
		$url = 'https://passport.yandex.ru/auth/add?retpath=https%3A%2F%2Fpassport.yandex.ru%2Fprofile&noreturn=1';
		$p = get_page_curl($url);
//		$outheaders['Cookie'] = set_cookies();

		$pos = strpos($p,'name="csrf_token"');
		addtolog('strpos(csrf_token)='.$pos);
		if ($pos === false) 
		{	
			$csrf_token = $_SESSION['csrf_token'];
		} else {	
			$pos = strpos($p,'value="',$pos)+7;
			$pos1= strpos($p,'"',$pos);
			$csrf_token = substr($p,$pos,$pos1-$pos);
			$csrf_token = str_replace(':','%3A',$csrf_token);
			$_SESSION['csrf_token'] = $csrf_token;
		};	

		$outheaders['Content-Type']	= 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
		$outheaders['X-Requested-With']	= 'X-Requested-With: XMLHttpRequest';		
		unset($outheaders['Upgrade-Insecure-Requests']);
		$outheaders['Accept'] = 'Accept: application/json, text/javascript, */*; q=0.01';
		
		$pos = strpos($p,'process_uuid=');
		if ($pos === false) 
		{
			addtolog('strpos($p,process_uuid=)=false');
			return false;
		}	
		$pos = strpos($p,'=',$pos)+1;
		$pos1= strpos($p,'"',$pos);
		$process_uuid	= substr($p,$pos,$pos1-$pos);
		$outheaders['Accept-Encoding'] 	= 'gzip, deflate, br';
		$outheaders['Referer'] 			= 'Referer: https://passport.yandex.ru/auth';
		$url = 'https://passport.yandex.ru/registration-validations/auth/multi_step/start';

		addtolog('$csrf_token='.$csrf_token);
		$data = '';
		if ($csrf_token!='') $data.= "csrf_token=$csrf_token";
		$data.= "&login=$login&process_uuid=$process_uuid";	
		$p = get_page_curl($url,1,$data);
		$p = json_decode($p,true);
//		$csrf_token = $p['csrf_token'];
		
//		unset($outheaders['Cookie']+);
		$outheaders['Referer'] = 'Referer: '.$url;
		$track_id 	= $p['track_id'];
		$track_id 	= str_replace(':','%3A',$track_id);
		$url = 'https://passport.yandex.ru/registration-validations/auth/multi_step/commit_password';
		$data	= "csrf_token=$csrf_token&track_id=$track_id&password=$password&retpath=https%3A%2A%2Apassport.yandex.ru%2Aprofile";
		$p = get_page_curl($url,1,$data);

		$url = 'https://wordstat.yandex.ru';
		$p = get_page_curl($url);
		$cookies = set_cookies();
		endif;
		
		$num = 0;
//		while(1)
//		{	
		if (file_exists('stop.txt')) die();
		while(1)	
		{	
//			$skeywords = str_replace(' ','+',trimall($keyword));
			$skeywords = urlencode(trimall($keyword));
			$outheaders['Content-Type']	= 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
			$outheaders['X-Requested-With']	= 'X-Requested-With: XMLHttpRequest';		
			unset($outheaders['Upgrade-Insecure-Requests']);
			$outheaders['Accept'] = 'Accept: application/json, text/javascript, */*; q=0.01';
			$data= [
				'db' 		=> '', 
				'filter'	=> 'all', 
				'map'		=> 'world', 
				'page'		=> $_SESSION['numpage']+1, 
				'page_type'	=> 'words', 
				'period'	=> 'monthly', 
				'regions'	=> '', 
				'sort'		=> 'cnt', 
				'type'		=> 'list', 
				'words'		=> $skeywords
			];
			$_SESSION['data'] 	= $data;
			$_SESSION['keyword']= trimall($keyword);

//			$data = http_build_query($data);

			if (file_exists('stop.txt')) die();
			$url = 'https://wordstat.yandex.ru/stat/words';
			$outheaders['Accept-Encoding']	= 'Accept-Encoding: br';
			$outheaders['Referer'] = 'Referer: '.$url;
			$outheaders['X-Retpath-Y'] = "X-Retpath-Y: https://wordstat.yandex.ru/#!/?words=$skeywords";
			$pbase = get_page_curl($url,1,$data);
			if (strpos($pbase,'"captcha"')===false) break;
			wordstat_captcha();
		};
		
		$pos = strpos($pbase,'{');
		if ($pos>10) $pbase = substr($pbase,$pos);
		$p = json_decode($pbase,true);
		if ($p['need_login']=='1') die();
		
		$names	= explode(' ',$p['key']);
		$pos	= strrpos($p['key'],';',-1);
		$p1		= substr($p['key'],0,$pos+1);
		$fun	= substr($p['key'],$pos+1);
		$p['key'] = $p1.'console.log('.$fun.')';
		$_SESSION['wordstat'] = $p;
	?>
	<input type="hidden" id="bufid" value="">
	<style>#results,#results1{float:left;margin-left:20px;padding:10px;}</style>
	<div id="results"></div>
	<div id="results1"></div>
	<script>
		var useragent 	= window.navigator.userAgent;
		function decode(response, browserUserAgent, cookieValue) {
			var key = [
				browserUserAgent.substr(0, 25), 
				(cookieValue || ''),
				eval(response.key)
			].join('');
			var encryptData = response.data;
			var decryptData = '';
			for (var i = 0; i < encryptData.length; i++) {
				var charCode = encryptData.charCodeAt(i) ^ key.charCodeAt(i % key.length);
				decryptData = decryptData + String.fromCharCode(charCode);
			}
			return decryptData;
		}		
		
		var fuid01 		= "<?= $_SESSION['fuid01'] ?>";
		
		function send(data) 
		{
			console.log('data=',data);
			$.post('/wordstat/2WDmonth/wordstat.php', data, function(resp)
			{
				$pos = resp.indexOf('{');
				if ($pos!==false) resp = resp.substring($pos);
				console.log('resp=',resp);
				$json = JSON.parse(resp);
				console.log('$json=',$json);
				if ($json.result=='ok')
				{
					switch($json.action)
					{
						case 'send-data':
							var arr = JSON.parse($json.data);
					console.log('arr=',arr);
							var table = '<table style="border:1px solid #000000;"><thead style="border:1px solid #000000;"><tr><th>№</th><th>Фраза</th><th>Показов</th></tr></thead></tbody>'
							for(var i=0;i<arr.content.includingPhrases.items.length;i++)
							{
								table+= '<tr><td>'+i+'</td><td>'+arr.content.includingPhrases.items[i].phrase+'</td><td>'+arr.content.includingPhrases.items[i].number+'</td></tr>';
							}	
							table+= '</tbody></table>';

							var table1 = '<table style="border:1px solid #000000;"><thead style="border:1px solid #000000;"><tr><th>№</th><th>Фраза</th><th>Показов</th></tr></thead></tbody>'
							for(var i=0;i<arr.content.phrasesAssociations.items.length;i++)
							{
								table1+= '<tr><td>'+i+'</td><td>'+arr.content.phrasesAssociations.items[i].phrase+'</td><td>'+arr.content.phrasesAssociations.items[i].number+'</td></tr>';
							}	
							table1+= '</tbody></table>';
							
							$('#results').html(table);
							$('#results1').html(table1);
							break;
					};
				};
			});
		}	
		var resp= <?= $pbase ?>;
		if (resp.data != undefined)
		{	
			var buf	= decode(resp, useragent, fuid01);
			console.log('buf='+buf);
			if (buf.indexOf('%7B%22content%22%3A%7B%22elem%22%3A%22text%22')==-1)
			{	
				data = { action: 'send-data', keyword: '<?= $keyword ?>', page: <?= $_SESSION['numpage'] ?>, buf: buf};
				send(data);
			}	
		}
	</script>
<?php
		if (strpos($pbase,'"need_login"')===true) die();
	}
