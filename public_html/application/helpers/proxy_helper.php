<?php

function detect_proxy()
{
	$CI = & get_instance();
	$is_proxy_header = 0;
	
	if (
			$CI->input->server('HTTP_FORWARDED')			// URI and FQDN of the client proxy
			OR $CI->input->server('HTTP_X_FORWARDED_FOR') 
			OR $CI->input->server('HTTP_PROXY_CONNECTION') 
			OR $CI->input->server('HTTP_XROXY_CONNECTION') // CacheFlow product-specific extension headers (Proxy-Connection related)
			OR $CI->input->server('HTTP_HTTP_PC_REMOTE_ADDR') 
			OR $CI->input->server('HTTP_VIA')				// Proxy information (type, version, etc.)
			OR $CI->input->server('HTTP_X_TM_VIA') 
			OR $CI->input->server('HTTP_USERAGENT_VIA') 
			OR $CI->input->server('HTTP_HTTP_CLIENT_IP') 
			OR $CI->input->server('HTTP_CLIENT_IP')		// The source IP address (TrafficServer NetscapeProxy or appended)
			OR $CI->input->server('HTTP_X_HOST') 
			OR $CI->input->server('HTTP_PROXYITEMID') 
			OR $CI->input->server('HTTP_X_BLUECOAT_VIA') 
			OR $CI->input->server('HTTP_X_LOOP_158_946684808') 
			OR $CI->input->server('HTTP_X_NAI_ID') 
			OR $CI->input->server('HTTP_X_PROXY_ID') 
			OR $CI->input->server('HTTP_MAX_FORWARDS')	// The maximum number of proxies that can be over
			OR $CI->input->server('HTTP_TE')				// Transfer encoding supported by the proxy, etc.
			OR $CI->input->server('HTTP_SP_HOST')			// The source IP address
			OR $CI->input->server('HTTP_XONNECTION')		// CacheFlow product-specific extension headers (Connection related)
			OR $CI->input->server('HTTP_X_CLUSTER_CLIENT_IP')

		) 
	{
		$is_proxy_header = 1;
	}

	foreach ($_SERVER as $k => $v) 
	{
		if (stripos($k, 'HTTP_X_LOOP') !== FALSE)
		{
			$is_proxy_header = 1;
			break;
		}				
	}	

	return $is_proxy_header ? TRUE : FALSE;
}


function detect_proxy_header($server)
{
	$is_proxy_header = 0;
	
	if (
			isset($server['HTTP_FORWARDED'])			// URI and FQDN of the client proxy
			OR isset($server['HTTP_X_FORWARDED_FOR']) 
			OR isset($server['HTTP_PROXY_CONNECTION']) 
			OR isset($server['HTTP_XROXY_CONNECTION']) // CacheFlow product-specific extension headers (Proxy-Connection related)
			OR isset($server['HTTP_HTTP_PC_REMOTE_ADDR']) 
			OR isset($server['HTTP_VIA'])				// Proxy information (type, version, etc.)
			OR isset($server['HTTP_X_TM_VIA']) 
			OR isset($server['HTTP_USERAGENT_VIA']) 
			OR isset($server['HTTP_HTTP_CLIENT_IP']) 
			OR isset($server['HTTP_CLIENT_IP'])		// The source IP address (TrafficServer NetscapeProxy or appended)
			OR isset($server['HTTP_X_HOST']) 
			OR isset($server['HTTP_PROXYITEMID']) 
			OR isset($server['HTTP_X_BLUECOAT_VIA']) 
			OR isset($server['HTTP_X_LOOP_158_946684808']) 
			OR isset($server['HTTP_X_NAI_ID']) 
			OR isset($server['HTTP_X_PROXY_ID']) 
			OR isset($server['HTTP_MAX_FORWARDS'])	// The maximum number of proxies that can be over
			OR isset($server['HTTP_TE'])				// Transfer encoding supported by the proxy, etc.
			OR isset($server['HTTP_SP_HOST'])			// The source IP address
			OR isset($server['HTTP_XONNECTION'])		// CacheFlow product-specific extension headers (Connection related)
			OR isset($server['HTTP_X_CLUSTER_CLIENT_IP'])
		) 
	{
		$is_proxy_header = 1;
	}

	foreach ($server as $k => $v) 
	{
		if (stripos($k, 'HTTP_X_LOOP') !== FALSE)
		{
			$is_proxy_header = 1;
			break;
		}				
	}	

	return $is_proxy_header ? TRUE : FALSE;
}


function get_host($ip)
{
        $ptr= implode(".",array_reverse(explode(".",$ip))).".in-addr.arpa";
        $host = dns_get_record($ptr,DNS_PTR);
        if ($host == null) return $ip;
        else return $host[0]['target'];
		
}

function get_domain($url)
{
	$url = preg_replace("/^http:\/\//", "", $url);
	$url = preg_replace("/^www\./", "", $url);
	return preg_match("/(.+?)(\/|$)/", $url, $match) ? $match[1] : $url;
}

/*
function get_domain($url = NULL)
{
	if (!$url)
	{
		$url = base_url();
	}
	
    $CI =& get_instance();
    return preg_replace("/^[\w]{2,6}:\/\/([\w\d\.\-]+).*$/","$1", $url);
} 
*/



function get_ip($server)
{
	$ip_address = FALSE;
	
	if (! isset($server['HTTP_CLIENT_IP']) AND isset($server['REMOTE_ADDR']))
	{
		$ip_address = $server['REMOTE_ADDR'];
	}
	elseif (isset($server['REMOTE_ADDR']) AND isset($server['HTTP_CLIENT_IP']))
	{
		$ip_address = $server['HTTP_CLIENT_IP'];
	}
	elseif (isset($server['HTTP_CLIENT_IP']))
	{
		$ip_address = $server['HTTP_CLIENT_IP'];
	}
	elseif (isset($server['HTTP_X_FORWARDED_FOR']))
	{
		$ip_address = $server['HTTP_X_FORWARDED_FOR'];
	}

	if ($ip_address === FALSE)
	{
		$ip_address = '0.0.0.0';
		return $ip_address;
	}

	if (strpos($ip_address, ',') !== FALSE)
	{
		$x = explode(',', $ip_address);
		$ip_address = trim(end($x));
	}

	if ( ! valid_ip($ip_address))
	{
		$ip_address = '0.0.0.0';
	}

	return $ip_address;
}

	// --------------------------------------------------------------------

	/**
	* Validate IP Address
	*
	* Updated version suggested by Geert De Deckere
	*
	* @access	public
	* @param	string
	* @return	string
	*/
	function valid_ip($ip)
	{
		$ip_segments = explode('.', $ip);

		// Always 4 segments needed
		if (count($ip_segments) != 4)
		{
			return FALSE;
		}
		// IP can not start with 0
		if ($ip_segments[0][0] == '0')
		{
			return FALSE;
		}
		// Check each segment
		foreach ($ip_segments as $segment)
		{
			// IP segments must be digits and can not be
			// longer than 3 digits or greater then 255
			if ($segment == '' OR preg_match("/[^0-9]/", $segment) OR $segment > 255 OR strlen($segment) > 3)
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	
function GetPageRank($q,$host='toolbarqueries.google.com',$context=NULL) {
$seed = "Mining PageRank is AGAINST GOOGLE'S TERMS OF SERVICE. Yes, I'm talking to you, scammer.";
$result = 0x01020345;
$len = strlen($q);
for ($i=0; $i<$len; $i++) {
$result ^= ord($seed{$i%strlen($seed)}) ^ ord($q{$i});
$result = (($result >> 23) & 0x1ff) | $result << 9;
}
    if (PHP_INT_MAX != 2147483647) { $result = -(~($result & 0xFFFFFFFF) + 1); }
$ch=sprintf('8%x', $result);
$url='http://%s/tbr?client=navclient-auto&ch=%s&features=Rank&q=info:%s';
$url=sprintf($url,$host,$ch,$q);
@$pr=file_get_contents($url,false,$context);
return $pr?substr(strrchr($pr, ':'), 1):false;
}


function genhash ($url) {
	$hash = "Mining PageRank is AGAINST GOOGLE'S TERMS OF SERVICE. Yes, I'm talking to you, scammer.";
	$c = 16909125;
	$length = strlen($url);
	$hashpieces = str_split($hash);
	$urlpieces = str_split($url);
	for ($d = 0; $d < $length; $d++) {
		$c = $c ^ (ord($hashpieces[$d]) ^ ord($urlpieces[$d]));
		$c = (($c >> 23) & 0x1ff) | $c << 9;
 	}
 	$c = -(~($c & 4294967295) + 1);
 	return '8' . dechex($c);
}

function pagerank($url) {
	$googleurl = 'http://toolbarqueries.google.com/tbr?client=navclient-auto&ch=' . genhash($url) . '&features=Rank&q=info:' . urlencode($url);
	if(function_exists('curl_init')) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $googleurl);
		$out = curl_exec($ch);
		curl_close($ch);
	} else {
		$out = file_get_contents($googleurl);
	}
	if(strlen($out) > 0) {
		return trim(substr(strrchr($out, ':'), 1));
	} else {
		return -1;
	}
}
