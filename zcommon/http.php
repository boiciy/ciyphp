<?php
/* =================================================================================
 * 版权声明：保留开源作者及版权声明前提下，开源代码可进行修改及用于任何商业用途。
 * 开源作者：众产国际产业公会  http://ciy.cn/code
 * 版本：0.1.0
====================================================================================*/
/**
//初始化
//指定cookiefile保存路径，由curl统一控制cookie，不填则自行控制。遇到JS更新Cookie的网站，需自行控制。
$http = new http($cookiefile = '');
//GET请求资源
$http->request($url);
//POST请求资源
$post = 'id=123&cn=aab';//该项如果填写则为POST方式，否则为GET方式;如需上传文件,需在文件路径前加上@符号
$http->request($url,$post);
//获取网页数据
$data = $http->get_data();

//设置cookie。
$cookie = $http->set_cookie($name,$value,$domain,$path,$expires);
//批量设置cookie。
$cookie = $http->set_cookiebyurl($url,$cookie);
//获取cookie。url必填，name不填时返回拼接cookie
$cookie = $http->get_cookie($url,$name);

//设置来源
$http->set_referer($referer);
//设置Header头
$http->set_headeronce($key,$value);//当次请求有效
$http->set_header('X-Requested-With','XMLHttpRequest');//永久有效
//设置浏览器UserAgent
$http->set_useragent($useragent);
//设置超时时间(秒)
$http->set_timeout($sec);
//设置代理服务器信息
$http->set_proxy($host, $port, $user, $pass);


//获取网页响应状态码
$statcode = $http->get_statcode();
//获取原始未处理的响应数据
$response = $http->get_response();
//获取连接资源的信息（返回数组），如平均上传速度 、平均下载速度 、请求的URL等
$response_info = $http->get_info();
//获取网页响应Header
$header = $http->get_header($key);
 */
class http {
    public $id;//扩展数据1
    public $extdata;//扩展数据2
    public $obj;//扩展数据3
    private $response;
    private $response_header;
    private $response_data;
    private $response_info;
    private $timeout = 30;
    public $maxredirect = 3;
    private $cookiefile = '';//设置此项，cookie相关函数均失效，用libcurl的cookie文件统一控制。
    private $cookiecontainer = array();
    private $useragent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Maxthon/4.9.3.1000 Chrome/39.0.2146.0 Safari/537.36';
    private $request_referer = '';
    private $request_headeronce = array();
    private $request_headerfixed = array();
    private $request_proxy = array();
    private $request_method;

    public function __construct($cookiefile = '') {
        //能有效提高POST大于1M数据时的请求速度
        $this->set_header('Expect');
        $this->cookiefile = $cookiefile;
    }
    public function set_cookie($name,$value=null,$domain='',$path='/',$expires=2114352000) {
        $cook = array();
        if(is_array($name))
            $cook = $name;
        else
        {
            $cook['name'] = $name;
            $cook['value'] = $value;
            $cook['domain'] = $domain;
            $cook['path'] = $path;
            $cook['expires'] = $expires;
            if($value === null)
                $cook['value'] = 'deleted';
            if($expires<time())
                $cook['value'] = 'deleted';
        }
        if(empty($cook['domain']))
            return;
        $cookiei = -1;
        foreach($this->cookiecontainer as $k=>$c)
        {
            if($c['name'] == $cook['name'])
            {
                if($this->_verifycookie($c,$cook['domain'],$cook['path']))
                    $cookiei = $k;
            }
        }
        if($cookiei < 0)
        {
            if($cook['value'] != 'deleted')
                $this->cookiecontainer[] = $cook;
        }
        else
        {
            if($cook['value'] == 'deleted')
                array_splice($this->cookiecontainer,$cookiei,1);
            else
                $this->cookiecontainer[$cookiei] = $cook;
        }
    }
    public function set_cookiebyurl($url,$cookie){
        $purl = parse_url($url);
        $doamin = $purl['host'];
        $cookieinfo = explode(';', $cookie);
        foreach($cookieinfo as $cinfo)
        {
            $ind = strpos($cinfo,'=');
            if($ind === false)
                continue;
            $val = substr($cinfo,$ind + 1);
            $key = trim(substr($cinfo,0,$ind));
            $this->set_cookie($key,$val,$doamin);
        }
    }
    public function set_useragent($useragent = '') {
        $this->useragent = $useragent;
    }
    public function set_referer($referer) {
        $this->request_referer = $referer;
    }
    public function set_header($k, $v = '') {
        if (!empty($k))
            $this->request_headerfixed[] = $k . ':' . $v;
        else
            $this->request_headerfixed = array();
    }
    public function set_headeronce($k, $v = '') {
        if (!empty($k))
            $this->request_headeronce[] = $k . ':' . $v;
        else
            $this->request_headeronce = array();
    }
    public function set_timeout($sec) {
        if ($sec > ini_get('max_execution_time'))
            @set_time_limit($sec);
        $this->timeout = $sec;
    }
    public function set_proxy($host, $port = '', $user = '', $pass = '') {
        $this->request_proxy = array('host' => $host, 'port' => $port, 'user' => $user, 'pass' => $pass);
    }
    public function get_response() {
        return $this->response;
    }
    public function get_header($key = null) {
        if ($key == null) {
            return $this->response_header;
        }
        if (!isset($this->response_header[$key])) {
            return '';
        }
        return $this->response_header[$key];
    }
    public function get_cookie($url = null, $name = null) {
        if(empty($url))
            return $this->cookiecontainer;
        $purl = parse_url($url);
        $doamin = $purl['host'];
        if(isset($purl['path']))
            $path = $purl['path'];
        else
            $path = '/';
        if($name === null)
        {
            $cook = '';
            foreach($this->cookiecontainer as $k=>$c)
            {
                if($this->_verifycookie($c,$doamin,$path))
                    $cook .= $c['name'].'='.$c['value'].'; ';
            }
            if($cook == '')
                return '';
            $cook = substr($cook,0,-2);
            return $cook;
        }
        foreach($this->cookiecontainer as $k=>$c)
        {
            if($this->_verifycookie($c,$doamin,$path))
            {
                if($name == $c['name'])
                    return $c['value'];
            }
        }
        return '';
    }
    public function get_data() {
        $encode = $this->get_header('Content-Encoding');
        if($encode == 'gzip'){
            $flags = ord(substr($this->response_data, 3, 1));
            $headerlen = 10;
            $extralen = 0;
            $filenamelen = 0;
            if ($flags & 4) {
                $extralen = unpack('v' ,substr($this->response_data, 10, 2));
                $extralen = $extralen[1];
                $headerlen += 2 + $extralen;
            }
            if ($flags & 8)
                $headerlen = strpos($this->response_data, chr(0), $headerlen) + 1;
            if ($flags & 16)
                $headerlen = strpos($this->response_data, chr(0), $headerlen) + 1;
            if ($flags & 2)
                $headerlen += 2;
            $unpacked = @gzinflate(substr($this->response_data, $headerlen));
            if ($unpacked === FALSE)
                  $unpacked = $this->response_data;
            return $unpacked;
        }
        return $this->response_data;
    }
    public function get_charset() {
        $content_type = $this->get_header('Content-Type');
        if ($content_type) {
            preg_match('/charset=(.+)/i', $content_type, $matches);
            if (isset($matches[1])) {
                return strtoupper(trim($matches[1]));
            }
        }
        return '';
    }
    public function get_info() {
        return $this->response_info;
    }
    public function get_statcode() {
        return (int)$this->response_info['http_code'];
    }
    public function request($url, $postdata = '') {
        for($i=0;$i<$this->maxredirect;$i++)
        {
            $ch = $this->_http_request($url, $postdata);
            if (empty($postdata)) {
                $this->request_method = 'GET';
            } else {
                $this->request_method = 'POST';
            }
            $this->response = curl_exec($ch);
            if ($this->response === false) {
                $this->response_info['http_code'] = 204;
                $this->response_data = 'URL：' . $url . ' cURL resource: ' . (string) $ch . '; cURL error: ' . curl_error($ch) . ' (' . curl_errno($ch) . ')';
                return false;
            }
            $this->_process_response($ch);
            curl_close($ch);
            $httpcode = (int)$this->response_info['http_code'];
            if($httpcode>=300 && $httpcode<400)
            {
                if($this->response_info['redirect_url'])
                {
                    $url = $this->response_info['redirect_url'];
                    $postdata = '';
                    continue;
                }
            }
            $this->request_headeronce = array();
            if($httpcode == 200)
                return true;
            return false;
        }
        return false;
    }
    private function _verifycookie($cookie,$domain,$path){
        $sel = false;
        if($cookie['domain'][0] == '.')
        {
            $d = '.'.$domain;
            $cc = substr($d,strlen($d)-strlen($cookie['domain']));
            if($cc == $cookie['domain'])
                $sel = true;
        }
        else
        {
            if($domain == $cookie['domain'])
                $sel = true;
        }
        if($sel)
        {
            if(substr($path,0,strlen($cookie['path'])) == $cookie['path'])
                return true;
        }
        return false;
    }
    private function _process_response($ch = null) {
        if (is_resource($ch)) {
            $this->response_info = curl_getinfo($ch);
            $content_size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
            if ($content_size > 0) {
                $this->response_header = substr($this->response, 0, -$content_size);
                $this->response_data = substr($this->response, -$content_size);
            } else {
                $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                $this->response_header = substr($this->response, 0, $header_size);
                $this->response_data = substr($this->response, $header_size);
            }
            //分解响应头部信息
            $this->response_header = explode("\r\n\r\n", trim($this->response_header),2);
            $this->response_header = array_pop($this->response_header);
            $this->response_header = explode("\r\n", $this->response_header);
            array_shift($this->response_header); //开头为状态
            //分割数组
            $purl = null;
            $header_assoc = array();
            foreach ($this->response_header as $header) {
                $kv = explode(': ', $header, 2);
                if (strtolower($kv[0]) == 'set-cookie') {
                    $header_assoc['Set-Cookie'][] = $kv[1];
                    if(empty($this->cookiefile))
                    {
                        $cookieinfo = explode(';', $kv[1]);
                        //list($name, $value) = explode('=', array_shift($cookieinfo), 2);
                        $cookname = array_shift($cookieinfo);
                        $ind = strpos($cookname,'=');
                        if($ind === false)
                            continue;
                        $cook = array();
                        $cook['value'] = substr($cookname,$ind + 1);
                        $cook['name'] = trim(substr($cookname,0,$ind));
                        foreach($cookieinfo as $key)
                        {
                            $ind = strpos($key,'=');
                            if($ind !== false)
                            {
                                $val = substr($key,$ind + 1);
                                $key = strtolower(trim(substr($key,0,$ind)));
                            }
                            if($key == 'expires')
                            {
                                $cook['expires'] = strtotime($val);
                                if($cook['expires']<time())
                                    $cook['value'] = 'deleted';
                            }
                            else if($key == 'path')
                                $cook['path'] = $val;
                            else if($key == 'domain')
                                $cook['domain'] = $val;
                        }
                        if($purl === null)
                        {
                            $purl = parse_url($this->response_info['url']);
                            $doamin = $purl['host'];
                            if(isset($purl['path']))
                                $path = $purl['path'];
                            else
                                $path = '/';
                        }
                        if(!isset($cook['expires']))
                            $cook['expires'] = 2114352000;
                        if(!isset($cook['domain']))
                            $cook['domain'] = $doamin;
                        if(!isset($cook['path']))
                            $cook['path'] = '/';
                        $this->set_cookie($cook);
                    }
                    continue;
                }
                $header_assoc[$kv[0]] = $kv[1];
            }
            $this->response_header = $header_assoc;
        }
        return false;
    }
    private function _http_request($url, $post_data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        //强制使用IPV4协议解析域名，否则在支持IPV6的环境下请求会异常慢
        @curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        if ($post_data) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }
        if(empty($this->cookiefile))
        {
            $purl = parse_url($url);
            $doamin = $purl['host'];
            if(isset($purl['path']))
                $path = $purl['path'];
            else
                $path = '/';
            $cook = '';
            $cookdel = array();
            foreach($this->cookiecontainer as $k=>$c)
            {
                if($c['expires']<time())
                    $cookdel[] = $k;
            }
            $cookdel = array_reverse($cookdel);
            foreach($cookdel as $k)
                array_splice($this->cookiecontainer,$k,1);
            foreach($this->cookiecontainer as $k=>$c)
            {
                if($this->_verifycookie($c,$doamin,$path))
                    $cook .= $c['name'].'='.$c['value'].'; ';
            }
            if($cook != '')
            {
                $cook = substr($cook,0,-2);
                curl_setopt($ch, CURLOPT_COOKIE, $cook);
            }
        }
        else
        {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookiefile);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiefile);
        }
        
        if (!empty($this->request_referer)) {
            curl_setopt($ch, CURLOPT_REFERER, $this->request_referer);
        }
        $headers = array_merge($this->request_headeronce,$this->request_headerfixed);
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if ($this->request_proxy) {
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
            $host = $this->request_proxy['host'];
            $host .= ($this->request_proxy['port']) ? ':' . $this->request_proxy['port'] : '';
            curl_setopt($ch, CURLOPT_PROXY, $host);
            if (isset($this->request_proxy['user']) && isset($this->request_proxy['pass'])) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->request_proxy['user'] . ':' . $this->request_proxy['pass']);
            }
        }
        return $ch;
    }
}
