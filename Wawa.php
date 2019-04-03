<?php
class Wawa{
    protected $id, $devid;
    const URL = "http://35.198.207.23:7910/vouchers";
    public function __construct($id, $devid){
        $this->id = $id;
        $this->devid = $devid;
    }
    protected function curl($code, $url = self::URL){
        $chArray = [
            CURLOPT_HEADER => 1,
            CURLOPT_HTTPHEADER => [
                "Host: 35.198.207.23:7910",
                "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                "Referer: http://35.198.207.23:7910/vouchers?id=".$this->id,
                "Cookie: laravel_session=9bf1185510f20cd12d2c5c6c57dd6c0e7d5c4d80"
            ],
            CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:66.0) Gecko/20100101 Firefox/66.0",
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => http_build_query(["code" => $code, "id" => $this->id]),
        ];
        $ch = curl_init($url);
        curl_setopt_array($ch, $chArray);
        $result = curl_exec($ch);
        list($header, $body) = explode("\r\n\r\n", $result, 2);
        $getValid = strpos($body, "Success");
        if($getValid !== false){
            preg_match('/<p class="note">([^!]+)/', $body, $msg);
            return [true, $msg[1]];
        }else{
            preg_match('/<p class="error">([^<]+)/', $body, $msg);
            return [false, $msg[1]];
        }
        curl_close($ch);
    }
    protected function generateCode($length = 6){
        $container = "";
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        for($i = 0; $i < $length; $i++){
            $container .= $str[rand(0, strlen($str) - 1)];
        }
        return trim("T3".$container);
    }
    public function saveData(){
        $generateCode = $this->generateCode();
        $curl = $this->curl($generateCode);
        if($curl[0] === true){
            $fopen = fopen("valid.txt", "a");
            fwrite($fopen, $generateCode.PHP_EOL);
            fclose($fopen);
            return [true, $curl[1]." => ".$generateCode];
        }else{
            return [false, $curl[1]." => ".$generateCode];
        }
    }
}