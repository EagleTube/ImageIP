<?php

// Get User IP ADDRESS from crossing domain attack

$type = array('jpg'=>'image/jpg','jpeg'=>'image/jpeg','gif'=>'image/gif','png'=>'image/png','svg'=>'image/svg+xml');
function informations($ip)
{
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_URL,$ip);
    $json = curl_exec($ch);
    curl_close($ch);

    if ($json) return $json;
    else return die('HTTP/1.1 500 Internal Server Error');
}
function fetchIP()
{
    if(!empty($_SERVER['HTTP_CLIENT_IP']))
    {
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    }
    else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    }
    $info = json_decode(informations("http://ip-api.com/json/".$ipaddress));
    if($info->status=="fail")
    {
        $headers = getallheaders();
        $proxy = "-- Private/CF Proxied IP Address! --\n\n";
        $agent = "User-Agent : ".$headers['user-agent']."\n";
        $referer = "Referrer : ".$headers['referer']."\n";
        $contents = "IP Address : " . $ipaddress . "\n" . $agent . $referer . $proxy;
        $f = fopen("log.txt","a+");
        fwrite($f,$contents);
        fclose($f);
    }
    else
    {
        $headers = getallheaders();

        $country = "Country : "  . $info -> country ."\n";
        $region = "Region : "  . $info -> regionName ."\n";
        $city = "City : "  . $info -> city ."\n";
        $latitude = "latitude : "  . $info -> lat ."\n";
        $longitude = "Longitude : "  . $info -> lon ."\n";
        $timezone = "TimeZone : "  . $info -> timezone ."\n";
        $isp = "ISP : "  . $info -> isp ."\n\n";

        $agent = "User-Agent : ".$headers['User-Agent']."\n";
        $referer = "Referrer : ".$headers['Referer']."\n";
        $contents = "IP Address : " . $ipaddress . "\n" . $agent . $referer . $country . $region . $city . $latitude . $longitude . $timezone . $isp;
        $f = fopen("log.txt","a+");
        fwrite($f,$contents);
        fclose($f);
    }

}
function ImagePreview($type,$loc)
{
    header('Content-type:'.$type);
    header('Content-length:'.filesize($loc));
    readfile($loc);
    fetchIP();
}
$location = "img/bg.jpg";
$ext = (pathinfo($location,PATHINFO_EXTENSION));

ImagePreview($type[$ext],$location);
