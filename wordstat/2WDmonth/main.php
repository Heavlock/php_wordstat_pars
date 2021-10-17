<?php
	http://test.ft-tour.ru/tools/2WDmonth/main.php
	session_start();
	global $dbhost,$dbname,$dbusername, $dbpassword, $connect;
	global $with_variants,$need_limit, $limit;
	global $region, $arr_region, $name_region, $region_name, $with_variants, $need_limit;
	global $wordstat;
	require_once('functions.php');
	//----------------------------------------------------------------------
	send_header('utf-8');
	if (file_exists('stop.txt')) unlink('stop.txt');
	if (file_exists('debug.log')) unlink('debug.log');
	if (file_exists('cookies.txt')) unlink('cookies.txt');

	
//	if (isset($_REQUEST) && count($_REQUEST)>0) print_arr('$_REQUEST',$_REQUEST);

?>
<script src="/wordstat/2WDmonth/jquery.js"></script>
<center><H1>Статистика Wordstat</H1></center>
<script>
(function( $ ) {
	$(document).ready(function () 
	{
		var data = {};
		$('input[name="go"]').on('click touchend',function(){
			data = {
				action	: 'send-file',
				body	: $('textarea[name="keywords"]').val()
			};	
			send(data);
		});
	});	
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
							break;
					};
				};
			});
		}	
	
})(jQuery);
</script>
	<form action="<?php $_SERVER['REQUEST_URI']?>" method="post" name="FORM_GET" enctype="multipart/form-data">

<?php

    $text = '';
	if (file_exists('input.txt'))
	{
		$fname='input.txt';
		$text = file_get_contents($fname);
	};
?>
	<table>
		<tr><td>
			<LABEL>Поисковые запросы (списком каждый с новой строки):</LABEL></td>
		<td>
		</tr>
		<BR>
		<tr><td><TEXTAREA name="keywords" rows="10" cols="100"><?php echo $text; ?></TEXTAREA></td><td></td></tr>
		<table colspan=3>
		<br>
<style type="text/css">.myselect {  width:250px;}  .myselect option{ width:280px;}</style> 
		  <tr><td>&nbsp;&nbsp;&nbsp;Месяц:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select class="myselect" size="1"  name="month">
<?php
	$num_month =  get_value('month');
	$monthes = file('monthes.inc'); 
	$month_names = array();
	for($i=0;$i<count($monthes);$i++) {
		$monthes[$i]= trimall($monthes[$i],'');
		if (strpos($monthes[$i],'<option'))
		{
			$pos = strpos($monthes[$i],'"')+1;
			$pos1 = strpos($monthes[$i],'"',$pos);
			$num = substr($monthes[$i],$pos,$pos1-$pos);
			$pos11 = strpos($monthes[$i],'>')+1;
			$pos12 = strpos($monthes[$i],'<',$pos11);
			$month_names[$num]  = substr($monthes[$i],$pos11,$pos12-$pos11);
				if ($num==$num_month)  {
					$s = substr($monthes[$i],0,$pos1+1).' selected'.substr($monthes[$i],$pos1+1, strlen($monthes[$i])-$pos1-1);
					$monthes[$i] = $s;
			};
		};
	};
	for($i=0;$i<count($monthes);$i++) echo $monthes[$i];
?>
	</select></td></tr><tr><td>&nbsp;&nbsp;Год:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<select class="myselect" size="1"  name="year">
<?php
	$num_year =  get_value('year');
	$years = file('years.inc'); 
	$year_names = array();
	for($i=0;$i<count($years);$i++) {
		$years[$i]= trimall($years[$i],'');
		if (strpos($years[$i],'<option'))
		{
			$pos = strpos($years[$i],'"')+1;
			$pos1 = strpos($years[$i],'"',$pos);
			$num = substr($years[$i],$pos,$pos1-$pos);
			$pos11 = strpos($years[$i],'>')+1;
			$pos12 = strpos($years[$i],'<',$pos11);
			$year_names[$num]  = substr($years[$i],$pos11,$pos12-$pos11);
			if ($num==$num_year)  {
				$s = substr($years[$i],0,$pos1+1).' selected'.substr($years[$i],$pos1+1, strlen($years[$i])-$pos1-1);
				$years[$i] = $s;
			};
		};
	};
for($i=0;$i<count($years);$i++) echo $years[$i];
?>
</select>
	</td></tr>
	<tr><td>&nbsp;&nbsp;Регион (1):&nbsp;&nbsp;
		<select class="myselect" size="1"  name="region1">
<?php $select = file('select.inc'); 
	$region_names = array();
	for($i=0;$i<count($select);$i++) {
		$select[$i]= trimall($select[$i],'');
		if (strpos($select[$i],'<option'))
		{
			$pos = strpos($select[$i],'"')+1;
			$pos1 = strpos($select[$i],'"',$pos);
			$num = substr($select[$i],$pos,$pos1-$pos);
			$pos11 = strpos($select[$i],'>')+1;
			$pos12 = strpos($select[$i],'<',$pos11);
			$region_names[$num]  = substr($select[$i],$pos11,$pos12-$pos11);
			if ($num==$arr_region[1])  {
				$s = substr($select[$i],0,$pos1+1).' selected'.substr($select[$i],$pos1+1, strlen($select[$i])-$pos1-1);
				$select[$i] = $s;
			};
		};
	};
	for($i=0;$i<count($select);$i++) echo $select[$i];
?>
</select>
	</td></td>
		  <tr><td>

&nbsp;&nbsp;Регион (2):&nbsp;&nbsp;
<select class="myselect" size="1"  name="region2">
<?php $select = file('select.inc'); 
for($i=0;$i<count($select);$i++) {
  $select[$i]= trimall($select[$i],'');
  if (strpos($select[$i],'<option'))
  {
	$pos = strpos($select[$i],'"')+1;
	$pos1 = strpos($select[$i],'"',$pos);
	$num = substr($select[$i],$pos,$pos1-$pos);
	if ($num==$arr_region[2])  {
	  $s = substr($select[$i],0,$pos1+1).' selected'.substr($select[$i],$pos1+1, strlen($select[$i])-$pos1-1);
	  $select[$i] = $s;
//	  break;
	};
  };
};
for($i=0;$i<count($select);$i++) echo $select[$i];
?>
</select>
		  </td></tr>
		  <tr><td>

&nbsp;&nbsp;Регион (3):&nbsp;&nbsp;
<select class="myselect" size="1"  name="region3">
<?php $select = file('select.inc'); 
for($i=0;$i<count($select);$i++) {
  $select[$i]= trimall($select[$i],'');
  if (strpos($select[$i],'<option'))
  {
	$pos = strpos($select[$i],'"')+1;
	$pos1 = strpos($select[$i],'"',$pos);
	$num = substr($select[$i],$pos,$pos1-$pos);
	if ($num==$arr_region[3])  {
	  $s = substr($select[$i],0,$pos1+1).' selected'.substr($select[$i],$pos1+1, strlen($select[$i])-$pos1-1);
	  $select[$i] = $s;
//	  break;
	};
  };
};
for($i=0;$i<count($select);$i++) echo $select[$i];
?>
</select>
		  </td></tr>
		  <tr><td>

&nbsp;&nbsp;Регион (4):&nbsp;&nbsp;
<select class="myselect" size="1"  name="region4">
<?php $select = file('select.inc'); 
for($i=0;$i<count($select);$i++) {
  $select[$i]= trimall($select[$i],'');
  if (strpos($select[$i],'<option'))
  {
	$pos = strpos($select[$i],'"')+1;
	$pos1 = strpos($select[$i],'"',$pos);
	$num = substr($select[$i],$pos,$pos1-$pos);
	if ($num==$arr_region[4])  {
	  $s = substr($select[$i],0,$pos1+1).' selected'.substr($select[$i],$pos1+1, strlen($select[$i])-$pos1-1);
	  $select[$i] = $s;
//	  break;
	};
  };
};
for($i=0;$i<count($select);$i++) echo $select[$i];
?>
</select></p>
		  </td></tr>
		  </table>

<br>
<hr>
<table>
<tr><td>
<input type="submit"   name="go" value="Запустить">
</td><td>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit"   name="readonly" value="Просмотреть ранее напарсенное">
</tr>
</table>

</form>

<?php
	global $dbhost,$dbname,$dbusername, $dbpassword, $connect;
	global $with_variants, $need_limit, $limit;
	global $count_region;
	global $region, $arr_region, $name_region, $region_name, $with_variants, $need_limit;
	global $wordstat;

	$count_region = 1;
	if (isset($_REQUEST['go'])){ 
decho('go !!!');

		@unlink('debug.log');
		@unlink('cookies.txt');
		
		$global_time = time();
		$_SESSION['time'] 	= time();
		$_SESSION['numpage'] 	= 0;
		$_SESSION['numkeyword']	= 0;
		unset($_SESSION['csrf_token']);
		redirect('wordstat.php');
	}
