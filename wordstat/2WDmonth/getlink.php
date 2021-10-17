<?php
class CGetLink
{
	var $url;
	var $view_all;	
	var $nav_str;
	var $curr_page;
	
	function CGetLink($request, $all = false, $page = false)
	{	
		$this->url = "http://yandex.ru/yandsearch?";
		$this->url .= 'numdoc=50';
		$this->url .= '&text=' . $_REQUEST['REQUEST'];
                if ($_REQUEST['rstr']!="") $this->url .= '&lr=' . $_REQUEST['rstr'];
		//$this->url .= '&rstr_manually=true';
		if ($all) $this->url .= '&rd=0';

		$this->view_all = $all;
		$this->curr_page = $page; 
	}

	function savereport($fname,$str)
	{
      		$file= fopen ($fname,"a+");
      		fputs($file, $str);
      		fclose ( $file );
	}

	
	function get_html($url)
	{
		if ($hfile = fopen($url, "r"))
		{
			$html = '';
			while (!feof($hfile)) 
				$html .= fread($hfile, 8192);
			fclose($hfile);
				
			return $html;
		}
		
		return false;
	}
	
	function get_page_link($html)
	{
		$tpl_link_tag = "'<span class=\"ei\"[^>]*?>.*?href=\"([^\s]{1,})\".*?</span>'si";
		$tpl_link_tag = "'<span class=\"ei\">(.*?)</span>'si";
	
		if (preg_match_all($tpl_link_tag, $html, $ar_links))
			return $ar_links[1];
		else
			return false;
	}
	
	function get_num_links($html)
	{
		$tpl_title_tag = "'<title[^>]*?>.*?([0-9]{2,}).*?</title>'si";
		
		if (preg_match($tpl_title_tag, $html, $title))
		{
			if ( ($title[1]>1000) || preg_match("/РјР»РЅ/",$title[0]))
				$num_links = 1000;
			else
				$num_links = $title[1];
		}
		return $num_links;
	}
	
	function get_nav_end($html)
	{
		$tpl_nav_tag = "'<div class=\"dk\"[^>]*?>.*?</div>'si";
		$tpl_nav_item = "'>([0-9]{1,})</[ab]{1,}>'si";
		
		if (preg_match($tpl_nav_tag, $html, $nav_str))
		{
			preg_match_all($tpl_nav_item, $nav_str[0], $ar_nav);
			echo "<br>------------->".$ar_nav[1][count($ar_nav[1])-1];
			return $ar_nav[1][count($ar_nav[1])-1];
		}	
		return false;
	}
	
	function set_nav_string($html)
	{
		$tpl_nav_tag = "'<div class=\"dk\"[^>]*?>.*?</div>'si";
		$tpl_nav_item = "'>([0-9]{1,})</[ab]{1,}>'si";
		
		if (preg_match($tpl_nav_tag, $html, $nav_str))
		{
			preg_match_all($tpl_nav_item, $nav_str[0], $ar_nav);
			
			$this->nav_str = '<p>';
			
			if ($this->curr_page > 0)
				$this->nav_str .= '<a href="JavaScript:onClickNavPage(' . ($this->curr_page-1) . ');">предыдущая</a>&nbsp;';
			else
				$this->nav_str .= 'предыдущая&nbsp;';
				
			foreach ($ar_nav[1] as $p)
			{
				if ($p != ($this->curr_page+1))
					$this->nav_str .= '<a href="JavaScript:onClickNavPage(' . ($p-1) . ');">' . $p . '</a>&nbsp;';
				else
					$this->nav_str .= '<b>' . $p . '</b>&nbsp;';
			}
			
			if ( ($p-$this->curr_page) > 1)
				$this->nav_str .= '<a href="JavaScript:onClickNavPage(' . ($this->curr_page+1) . ');">следующая</a>';
			else
				$this->nav_str .= 'следующая&nbsp;';
				
			$this->nav_str .= '</p>';
		}
	}
	

	function get_links()
	{	
		$p = ($this->curr_page!==false) ? $this->curr_page : 0;
		if ( !($html = $this->get_html($this->url . '&p=' . $p)) )
		{
			$result = '<p class="error">Невозможно получить страницу.</p>';
		}
		else
		{
//		echo mb_convert_encoding($html, "windows-1251", "UTF-8");
//		$this->savereport("1.html",$html);
		}
		
		return $result;
	}
        function file_get_contents_curl($url) {
		$headerstrings = array();
		$headerstrings['User-Agent'] = 'Mozilla/5.0 (Windows; U; Windows NT 5.'.rand(0,2).'; en-US; rv:1.'.rand(2,9).'.'.rand(0,4).'.'.rand(1,9).') Gecko/2007'.rand(10,12).rand(10,30).' Firefox/2.0.'.rand(0,1).'.'.rand(1,9);
		$headerstrings['Accept-Charset'] = rand(0,1) ? 'en-gb,en;q=0.'.rand(3,8) : 'en-us,en;q=0.'.rand(3,8);
		$headerstrings['Accept-Language'] = 'en-us,en;q=0.'.rand(4,6);
		$setHeaders = 	'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5'."\r\n".
						'Accept-Charset: '.$headerstrings['Accept-Charset']."\r\n".
						'Accept-Language: '.$headerstrings['Accept-Language']."\r\n".
						'User-Agent: '.$headerstrings['User-Agent']."\r\n";
          	$ch = curl_init();	
		curl_setopt($ch, CURLOPT_HEADER, 0);
//		curl_setopt($ch, CURLOPT_HEADER, $setHeaders);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_URL, $url);
		$s = $this->proxy[$this->numproxy];
		echo "<br>proxy=".$s;
		echo "<br>";
//              $this->needproxy = true; 
//              if ($this->needproxy == true)
                curl_setopt_array($ch,array(CURLOPT_PROXY=>$this->proxy[$this->numproxy],CURLOPT_PROXYTYPE=>CURLPROXY_SOCKS4,CURLOPT_HTTPPROXYTUNNEL => true));
		$data = curl_exec($ch);	
		curl_close($ch);
                $this->numproxy++;
                $this->numproxy = rand(0,count($this->proxy)-1);
                if ($this->numproxy>=count($this->proxy)) $this->numproxy = 0; 
		return $data;
	}

}
