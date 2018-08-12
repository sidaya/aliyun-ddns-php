<?php

class AliyunAPI {
    
    public $data;
    public $accessKeyId;
    public $accessKeySecret;
    public $url;
    
    public function __construct($actionArray, $url, $KeyId, $KeySecret) {
        $this->url = $url;
        $this->accessKeyId = $KeyId;
        $this->accessKeySecret = $KeySecret;
        
        date_default_timezone_set("GMT");
        
        $this->data = array(
            'Format' => 'json',
            'Version' => '2015-01-09',
            'AccessKeyId' => $this->accessKeyId,
            'SignatureVersion' => '1.0',
            'SignatureMethod' => 'HMAC-SHA1',
            'SignatureNonce'=> uniqid(),
            'Timestamp' => date('Y-m-d\TH:i:s\Z'),
        );
        
        if(is_array($actionArray)) {
            $this->data = array_merge($this->data, $actionArray);
        }
    }
    
    public function percentEncode($str) {
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }
    
    public function computeSignature($parameters, $accessKeySecret) {
        ksort($parameters);
        $canonicalizedQueryString = '';
        foreach($parameters as $key => $value) {
        $canonicalizedQueryString .= '&' . $this->percentEncode($key) . '=' . $this->percentEncode($value);
        }
        $stringToSign = 'GET&%2F&' . $this->percentencode(substr($canonicalizedQueryString, 1));
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $accessKeySecret . '&', true));
        return $signature;
    }
    
    public function callInterface() {
        $this->data['Signature'] = $this->computeSignature($this->data, $this->accessKeySecret);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . http_build_query($this->data));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($ch); 
        return $res;
    }
}

/*
 *
 *    Config 配置
 *
 */

$url = "http://alidns.aliyuncs.com/?";
                                                            

 $ip = $_SERVER["REMOTE_ADDR"]; 					 // 获取本机 IP 地址

$accessKeyId = "你的accessKeyId";					//你的accessKeyId
$accessKeySecret = "你的accessKey秘钥";//你的accessKey秘钥

$arr = Array(
    "Action" => "DescribeDomainRecords",    // 业务类型标识，请勿修改
    "DomainName" => "heysida.cn",          // 要解析的域名
    "RecordID" => "",                       // 记录ID，留空，请勿修改
    "Value" => "",                          // 记录值，留空，请勿修改
    "RR" => "test",                         // 解析主机名，改为你需要的
    "Type" => "A",                          // 记录类型，请勿修改
    "TTL" => 600                            // TTL 生存时间，默认 600
);

/*
 *
 *    Request & Update 请求与域名解析更新
 *
 */

$obj = new AliyunAPI($arr, $url, $accessKeyId, $accessKeySecret);  
$recordList = json_decode($obj->callInterface(), true);
if(!$recordList) {
    echo "Failed get record list!";
    exit;
}
if(isset($recordList["DomainRecords"]["Record"])) {
    foreach($recordList["DomainRecords"]["Record"] as $id => $record) {
        if($record["RR"] == $arr["RR"] && $record["Type"] == "A") {
            $arr["RecordId"] = $record["RecordId"];
            $arr["Action"] = "UpdateDomainRecord";
            
           
            $arr["Value"] = $ip;
            
            if($arr["RecordId"] !== "" && $arr["Value"] !== "") {
                $obj = new AliyunAPI($arr, $url, $accessKeyId, $accessKeySecret);
                $result = json_decode($obj->callInterface(), true);
                
                if(isset($result["RecordId"]) && $result["RecordId"] == $record["RecordId"]) {
                    echo "Successful update domain record,the new ip is:",$ip;
                    exit;
                } else {
                    if(isset($result["Message"])) {
                        echo $result["Message"];
                    } else {
                        print_r($result);
                    }
                }
            } else {
                echo "Failed to get the ip address.";
                exit;
            }
        }
    }
} else {
    echo "Empty record list.";
    exit;
}
