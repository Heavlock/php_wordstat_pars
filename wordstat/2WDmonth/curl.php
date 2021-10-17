<?php 
	global $outheaders,$RetCode;
	//--------------------------------------------------------------------------
	function get_page_curl( $url, $post=0, $data=null, $header = 1, $user='', $password='' )
	{
	
		global $outheaders,$RetCode,$location;
		global $cookies_arr;
		addtolog('$url='.$url);
		addtolog('$outheaders='.print_r($outheaders,1));
		addtolog('$data='.print_r($data,1));
		addtolog('$post='.$post.' $header='.$header);
		if (!isset($cookies_arr)) $cookies_arr = array();
		$arr 	= explode('/',$url);
		$location = '';
		$process = curl_init($url);
		if( ($post==1) )
		{
//			if (is_array($data))	addtolog('$data(1)='.print_r($data,1));
//			else 					addtolog('$data(2)='.$data);
			curl_setopt($process, CURLOPT_POSTFIELDS, $data);
			curl_setopt($process, CURLOPT_POST, 1);
		}
		curl_setopt($process, CURLOPT_URL, $url);
		curl_setopt($process, CURLOPT_HEADER, $header);
		curl_setopt($process, CURLOPT_HTTPHEADER, $outheaders);
		curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
		// игнорируем сертификаты при работе с SSL
		curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($process, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($process, CURLOPT_CONNECTTIMEOUT, 10);
		
		curl_setopt($process, CURLOPT_COOKIEJAR, 	'cookies.txt');		
		curl_setopt($process, CURLOPT_COOKIEFILE, 	'cookies.txt');

		$proxy 		= '45.89.19.9';
		$proxy_port = '4324';
		$login		= 'bEvnkX';
		$password 	= 'gXx3vZ8Fbv';
/*		
		curl_setopt($process, CURLOPT_HTTPPROXYTUNNEL, 1);
		curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);    
		curl_setopt($ch, CURLOPT_PROXYPORT, $proxy_port);
		curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTPS');
		curl_setopt($ch, CURLOPT_PROXY, $proxy);
		curl_setopt($ch, CURLOPT_PROXYUSERPWD, $login . ":" . $password);
*/		
		$return = curl_exec($process);
		$curl_ch_info = curl_getinfo($process);
		addtolog('$curl_ch_info='.print_r($curl_ch_info,1));
		
//		addtolog('$return='.$return);
		$RetCode = $curl_ch_info['http_code'];

		$pos  = strpos($return,"<!DOCTYPE");
		if ($pos===false) $pos  = strpos($return,"<!doctype");
		if ($pos===false) $pos  = strpos($return,"<html");
		if ($pos>0) $pos  = strpos($return,"\n\r");
		if ($pos===false) { 
			if (strpos($return,'JFIF')==6 ) { $headers = ''; $pos = 0; } else
			if (strpos($return,'GIF'))  { $headers = ''; $pos = strpos($return,'GIF'); } else
			{ $headers = $return; $pos = strlen($return); };
		} else  
			if ($pos>0) { $headers = substr($return, 0   , $pos-1);} else
			{ $headers = ''; $pos = 0; };

		if (strpos($return,'?PNG')>0)  $pos = strpos($return,'?PNG');
		if($count = preg_match_all("~Set-Cookie:\s*([^=]+)=([^\s;]+)~si", $headers, $matches))
		{
			$new_cookies_arr = array();
			for ($i=0; $i<$count; $i++){
				$cookies_arrmatches[1][$i] = $matches[2][$i];
				$new_cookies_arr[$matches[1][$i]] = $matches[2][$i];
			};
			$cookies_arr = array_merge( $cookies_arr, $new_cookies_arr);
		};
//decho('$count='.$count);
/*
print_arr('$matches',$matches);
print_arr('$headers',$headers);
print_arr('$cookies_arr(get_page_curl)',$cookies_arr);
*/
		$pos = strpos($headers, "location:");
		if($pos>0) {
			$pos = strpos($headers, " ", $pos+1);
			$location = substr($headers, $pos+1, strlen($headers)-$pos-1);
			$pos = strpos($location, "\r");
			if ($pos>0) $location = substr($location, 0, $pos);
		};
		if (strlen($curl_ch_info['redirect_url'])>5) $location = $curl_ch_info['redirect_url'];
		
		curl_close($process);

		$pos = strpos($return, "<?xml ");
		if ($pos > 1) {
			$return = substr($return, $pos, strlen($return) - $pos);
		};
//		if ($header)
//		{
			$pos	= strpos( $return, "\x0D\x0A\x0D\x0A")+4;
			$return	= substr( $return, $pos );
//		}
		addtolog('$return='.$return);
		return $return;
	}
	//--------------------------------------------------------------------------
	function set_cookies($names=array())
	{
		global $proxy,$need_proxy,$all_useragents,$numproxy, $cookies,$headers;
		global $cookies_arr;
		$cookies = '';
		if (isset($cookies_arr)){
			foreach($cookies_arr as $key =>$value) {
				if ((count($names)==0) || in_array($key,$names)) {
					if (strpos($value,'deleted')===false) 
					if ($key!='0')	$cookies .= $key.'='.$value.';';
				};
			}
		}
		$s = '';
		foreach ($names as $key => $value){
			if ($key!='0') $s.= '/'.$key.':'.$value;
		};
		$s = '';
		if (count($cookies_arr)>0)
		foreach ($cookies_arr as $key => $value){
			if ($key!='0') $s.= '/'.$key.':'.$value;
		};
		$pos = strlen($cookies);
		$cookies = substr($cookies,0,$pos-1);
		return $cookies;
	}
	//--------------------------------------------------------------------------
	function set_outheaders() 
	{
		$h['Accept'] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9';
//		$h['Accept-Encoding'] 	= 'Accept-Encoding: br';
		$h['Accept-Language'] 	= 'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7';
//		$h['Cache-Control'] 	= 'Cache-Control: max-age=0';
//		$h['Connection'] 		= 'Connection: keep-alive';
		
//		$h['Origin'] 			= 'Origin: https://passport.yandex.ru';
/*		
		$h['sec-ch-ua']			= 'sec-ch-ua: " Not;A Brand";v="99", "Opera";v="79", "Chromium";v="93"';
		$h['sec-ch-ua-mobile']	= 'sec-ch-ua-mobile: ?0';
		$h['sec-ch-ua-platform']= 'sec-ch-ua-platform: "Windows"';
		$h['Sec-Fetch-Dest']	= 'Sec-Fetch-Dest: empty';
		$h['Sec-Fetch-Mode']	= 'Sec-Fetch-Mode: cors';
		$h['Sec-Fetch-Site'] = 'Sec-Fetch-Site: same-origin';
*/		
		$h['Upgrade-Insecure-Requests'] = 'Upgrade-Insecure-Requests: 1';
		$h['User-Agent'] 		= 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.82 Safari/537.36 OPR/79.0.4143.50';
		return $h;
		
		$h['Accept']			= 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9';
		$h['Accept-Encoding']	= 'Accept-Encoding: br';
		$h['Accept-Language']	= 'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7';
		$h['Cache-Control']		= 'Cache-Control: no-cache';
		$h['Connection']		= 'Connection: keep-alive';
		$h['Pragma']			= 'Pragma: no-cache';
		$h['sec-ch-ua']			= "sec-ch-ua: ' Not;A Brand';v='99', 'Opera';v='79', 'Chromium';v='93'";
		$h['sec-ch-ua-mobile']	= 'sec-ch-ua-mobile: ?0';
		$h['sec-ch-ua-platform']= 'sec-ch-ua-platform: "Windows"';
		$h['Sec-Fetch-Dest']	= 'Sec-Fetch-Dest: document';
		$h['Sec-Fetch-Mode']	= 'Sec-Fetch-Mode: navigate';
		$h['Sec-Fetch-Site']	= 'Sec-Fetch-Site: none';
		$h['Sec-Fetch-User']	= 'Sec-Fetch-User: ?1';
		$h['Upgrade-Insecure-Requests']	= 'Upgrade-Insecure-Requests: 1';
		return $h;
	}
