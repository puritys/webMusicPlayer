<?

//$w=new MyCurl();

class MyCurl{
	var $header='';
    public $isCurl = false;
    public function __construct($isCurl=true) {
        $this->isCurl = $isCurl;
    }

	function isUrlExist($url,$setting = array(),$param='') {/*{{{*/
        $setting['headerOnly'] = true;
        if ($this->isCurl) {
            $res = $this->curl($url, 'GET', '', $setting);
            $httpCode = $res['info']['http_code'];
            //echo "\n code= ".$httpCode;
            if (!$httpCode || $httpCode == "404") {
                return false;
            }
            return true;
        }

		$url=str_replace('/file.php/','',$url);
		$downfile=str_replace(" ","%20",$url);//替换空格之类，可以根据实际情况进行替换
		$downfile=str_replace("http://","",$downfile);//去掉http://
		$urlarr=explode("/",$downfile);//以"/"分解出域名
		$domain=$urlarr[0];//域名
		$getfile=str_replace($urlarr[0],'',$downfile);//得出header中的GET部分
		if(!eregi("[/]",$getfile)){
			return false;
		}
		$fp = fsockopen($domain,80, $errno, $errstr, 10);
		$out = "";
	    $out .= "GET ".$getfile." HTTP/1.1\r\n";
	    $out .= "Host: ".$domain."\r\n";
		$out .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-TW; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3 \r\n";
		$out .= "Connection: close\r\n";
		$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$out .= "Content-length: " . strlen($params) . "\r\n". "\r\n";
		if(!$fp){return false;}
		fputs($fp, $out);
		$k='';
	    while (!feof($fp)){
		  $k.=fgets($fp, 1024);
	    }
		fclose($fp);
		$response=split("\r\n\r\n",$k); 
		//echo $response[0].'<br />';
		$this->header=$response[0];
		//get HTTP
		if(!eregi("200 OK",$response[0])){
			return false;
		}
		else{
			return true;
		}
		
		//get server
		$re='/Server:([^>]+)Last-Modified/';
		preg_match($re,$response[0],$tx);
		$server=$tx[1];
		
		return array("server"=>$server);
		
		
		$fp = fsockopen($domain,80, $errno, $errstr, 10);
		
	}/*}}}*/
	
	function fetch ($type='GET',$url,$setting='',$param='') {/*{{{*/
        if ($this->isCurl) {
            $res = $this->curl($url, $type, $param, $setting);
            $httpCode = $res['info']['http_code'];
            if ($httpCode == "301" || $httpCode == "302") {
                return $res;
            }
            if (isset($setting['getHeader']) && $setting['getHeader'] == true) {
                return $res;
            }
            //echo "\n code= ".$httpCode;
            return $res['response'];
        }


		$url=str_replace('/file.php/','',$url);
		$downfile=str_replace(" ","%20",$url);
		$downfile=str_replace("http://","",$downfile);
		$urlarr=explode("/",$downfile);
		$domain=$urlarr[0];//域名
		$getfile=str_replace($urlarr[0],'',$downfile);
		
		if(!eregi("[/]",$getfile)){
			return false;
		}
		$params='';
		if(is_array($param)){
			foreach ($param as $key=>$value) {
				if ($flag!=0) {
					$params .= "&";
					$flag = 1;
				}
				$params.= $key."="; $params.= urlencode($value);
				$flag = 1;
			}
		}
		else{
			$params=$param;
		}
        if (mb_substr($url, 0, 5) == 'https') {
	    	$fp = fsockopen($domain, 443, $errno, $errstr, 10);
        } else { 
	    	$fp = fsockopen($domain, 80, $errno, $errstr, 10);
        }
		if ($fp){
			   $out = "";
			   $out .= "".strtoupper($type)." ".$getfile." HTTP/1.1\r\n";
			   $out .= "Host: ".$domain."\r\n";
			   $out .= "Accept: text/html\r\n";
			   $out .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-TW; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3 \r\n";
			   //$out .= "Client-IP: 202.101.201.11"."\r\n";
			   //$out .= "X-Forwarded-For: 202.101.201.11"."\r\n";
			   if( isset($setting['cookie']) && $setting['cookie'])
					$out .= "Cookie: ".$setting['cookie']."\r\n";
			   if( isset($setting['Referer']) &&  $setting['Referer'])
					$out .= "Referer: ".$setting['Referer']."\r\n";
			
			   $out .= "Connection: close\r\n";
			   $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
			   $out .= "Content-length: " . strlen($params) . "\r\n";
			   $out .= "\r\n";
			   $out .= ($params) . "\r\n";
			 
			   fputs($fp, $out);
			   $k='';
			   while (!feof($fp)){
				  $k.=fgets($fp, 1024);
			   }
		}
		//echo $out.'<br /><hr />';
		$tx = split("\r\n\r\n",$k);
		for($i=2;$i<count($tx);$i++){
			$tx[1].=$tx[$i];
		}
		return array("header"=>$tx[0],"content"=>$tx[1]);
		
	}/*}}}*/

    public function curl ($url, $method="GET", $param="", $setting="") {/*{{{*/
        $curl = curl_init();
        $port = 80;
        $cookie = "";
        if (mb_substr($url, 0, 5) == "https") {
            $port = 443;
        }

        if (preg_match('/:([0-9]+)\//', $url, $res)) {
            $port = $res[1];
        }
        $url = preg_replace('/[\s]/', '%20', $url);
        curl_setopt($curl, CURLOPT_PORT , $port); 
        curl_setopt($curl, CURLOPT_VERBOSE, 0); 
        curl_setopt($curl, CURLOPT_HEADER, 1); 
        //curl_setopt($curl, CURLOPT_SSLVERSION, 3); 
        //curl_setopt($curl, CURLOPT_SSLCERT, getcwd() . "/client.pem"); 
        //curl_setopt($curl, CURLOPT_SSLKEY, getcwd() . "/keyout.pem"); 
        //curl_setopt($curl, CURLOPT_CAINFO, getcwd() . "/ca.pem"); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        if (isset($setting['timeout'])) {
            curl_setopt($curl , CURLOPT_CONNECTTIMEOUT, $setting['timeout']);
        }
        if (isset($setting['autoRedirect']) && $setting['autoRedirect']) {
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);// auto redirect
        }

        if (isset($setting['headerOnly']) && $setting['headerOnly']) {
            curl_setopt($curl, CURLOPT_NOBODY, true);// auto redirect
        }


        if (isset($setting['cookie']) && $setting['cookie']) {
            if (is_array($setting['cookie'])) {
                foreach ($setting['cookie'] as $key => $val) {
                    if ($cookie) {
                        $cookie .= "; ";
                    }
                    $cookie .= "$key=$val";
                }
            } else {
                $cookie = $setting['cookie'];
            }
            //$cookieFile = "tmpCookie";
            //file_put_contents($cookieFile, $setting['cookie']);
            //curl_setopt($curl, CURLOPT_COOKIEFILE, $cookieFile);
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        } 

        $data = "";
        if (is_array($param)) {
            foreach ($param as $key => $val) {
                $data .= "$key=$val&";
            }
        }

        if (strtolower($method) == "post") {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data); 
        } else {
            if (preg_match('/\?/', $url)) {
                $url .= '&'.$data;
            } else {
                $url .= '?'.$data;
            }
        }

        curl_setopt($curl, CURLOPT_URL, $url);

        if (isset($setting['header']) && $setting['header']) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $setting['header']); 
        }

        $tuData = curl_exec($curl);
//print_r($tuData); 
        if(!curl_errno($curl)){ 
            $info = curl_getinfo($curl); 
            //echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url']; 
        } else { 
            echo 'Curl error: ' . curl_error($curl); 
        }
        $c = preg_split('/\n\r/', $tuData);
        $header = $c[0];
        unset($c[0]);
        $tuData = implode('\n\r', $c);
        $setCookie = $this->getCookie($header);

        return array("response"=>$tuData, "info"=> $info, "header"=> $header, "setCookie" => $setCookie);
    } /*}}}*/


	public function download ($url, $saveToPath, $method="GET", $param="", $setting="") {
        if (empty($saveToPath)) {
            throw new Exception("Missing param saveToPath");
        }
        $url = $this->convertUrlToRightFormat($url);
        $info = $this->getInfoFromUrl($url);
        $baseCurl = curl_init();
        curl_setopt($baseCurl, CURLOPT_PORT , $info['port']); 
        curl_setopt($baseCurl, CURLOPT_VERBOSE, 0); 
        curl_setopt($baseCurl, CURLOPT_HEADER, 1); 
        curl_setopt($baseCurl, CURLOPT_SSL_VERIFYPEER, 1); 
        curl_setopt($baseCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($baseCurl, CURLOPT_URL, $url);

        $curl = $baseCurl;
//        curl_setopt($curl, CURLOPT_RANGE, 'bytes=0-'); 
        curl_setopt($curl, CURLOPT_NOBODY, 1);
        $tuData = curl_exec($curl);
        $resHeader = $this->parseHeader($tuData);

        $endBytes = $resHeader['Content-Length'] - 1;
        $fromBytes = 0;
        $segment = 200000;
        $toBytes = $segment - 1;

        unlink($saveToPath);

        while ($fromBytes <= $endBytes) {
            if ($toBytes > $endBytes) {
                $toBytes = $endBytes;
            }
            $curl = $baseCurl;
            curl_setopt($curl, CURLOPT_RANGE, "$fromBytes-$toBytes"); 
            curl_setopt($curl, CURLOPT_NOBODY, 0);
            curl_setopt($curl, CURLOPT_HEADER, 0); 
            curl_setopt($curl, CURLOPT_BINARYTRANSFER, 1);
            $tuData = curl_exec($curl);
            $fromBytes += $segment;
            $toBytes += $segment;
            if(!curl_errno($curl)){ 
                $info = curl_getinfo($curl); 
            } else { 
                echo 'Curl error: ' . curl_error($curl); 
            }
            file_put_contents($saveToPath, $tuData, FILE_APPEND);
        }
 

    }

	function getCookie($header){/*{{{*/
		$re='/Set-Cookie:[\s]?([\s]*[^:=]+=[^:;]+[;]?[\s]?)/i';
		//$re='/Set-Cookie:[\s]?([\S]+[\s]){1,2}/i';
		preg_match_all($re,$header,$k);
		$n=sizeof($k[1]);
		$cookie = array();
        $REG = "/([^=]+)=([^;]+)/";
		for($i=0;$i<$n;$i++){
            preg_match($REG, $k[1][$i], $res);
			$cookie[$res[1]] = $res[2];
		}
		return $cookie;
	}/*}}}*/

    function addCookie ($cookie, $addCookie) {/*{{{*/
        $newCookie = $addCookie;
        if (is_string($addCookie)) {
            $newCookie = array();
            $ck = explode(';', $addCookie);
            $n = count($ck);
            for ($i = 0; $i < $n; $i++) {
                preg_match('/([^=]+)=([^;]+)/', $ck, $res);
                $key = $res[1];
                $value = $res[2];
                $newCookie[$key] = $value;
            }
        }

        foreach ($newCookie as $key=> $val) {
            $cookie[$key] = $val;
        }
        return $cookie;

    }/*}}}*/

    public function convertUrlToRightFormat($url) {
        $url = preg_replace('/[\s]/', '%20', $url);
        return $url;
    }
    
    public function getInfoFromUrl($url) {
        $port = 80;
        $cookie = "";
        if (mb_substr($url, 0, 5) == "https") {
            $port = 443;
        }

        if (preg_match('/:([0-9]+)\//', $url, $res)) {
            $port = $res[1];
        }

        return array(
            "port" => $port,
        );
    }

    public function parseHeader($header) {
        $RegExp = '/^([^:]+):[\s]*([^\n\r]+)/';
        $headers = preg_split('/[\n\r]+/', $header);
        $retval = array();
        foreach ($headers as $h ) {
            if (preg_match($RegExp, $h, $res)) {
                $retval[$res[1]] = $res[2];
            }
        }
        return $retval;
    }

}
/*
foreach ($argv as $key=>$value) {
	if ($flag!=0) {
		$params .= "&";
		$flag = 1;
	}
	$params.= $key."="; $params.= urlencode($value);
	$flag = 1;
}
$params = 'username=george&password=i81bpz';
$length = strlen($params);

if ($fp){
   $out = "";
   $out .= "POST ".$getfile." HTTP/1.1\r\n";
   $out .= "Host: ".$domain."\r\n";
   $out .= "Accept: text/html\r\n";
   $out .= "Client-IP: 202.101.201.11"."\r\n";
   $out .= "X-Forwarded-For: 202.101.201.11"."\r\n";
   $out .= "Cookie: PHPSESSID=sk7blcojiup8uk1k6asbe5p0u3\r\n";
   $out .= "Referer: ".$domain."\r\n";
   $out .= "Connection: close\r\n";
   $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
   $out .= "Content-length: " . strlen($params) . "\r\n";
   $out .= "\r\n";
   $out .= ($params) . "\r\n";
   fputs($fp, $out);
   while (!feof($fp)){
      $k.=fgets($fp, 1024);
   }
}
echo  $k;
fclose($fp);*/
?>
