<?php
/**
 * @author 小哲
 * @link https://www.locpp.com
 * @date 2021年2月9日10:43:01
 * @msg LOC新帖通知Server酱版
 */
require 'vendor/autoload.php';
use Yurun\Util\HttpRequest;
use QL\QueryList;


/**
 * 配置信息
 */
$sckey = '你的SCKEY';

/**
 * 获取源代码
 */
$ip = rand_ip();
$header = array(
    'CLIENT-IP: '.$ip,
    'X-FORWARDED-FOR: '.$ip
);
$http = HttpRequest::newSession();
$http->referer('https://hostloc.com/');
$http->userAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.104 Safari/537.36');
$http->headers($header);
$response = $http->get('https://hostloc.com/forum.php?mod=forumdisplay&fid=45&filter=author&orderby=dateline&d='.time());
$content = $response->body();

/**
 * 提取源代码内容
 */
$rules = array(
    'text' => array('.new>.xst:eq(0)','text'),
    'id' => array('.new>.showcontent:eq(0)','id')
);
$data = QueryList::html($content)->rules($rules)->queryData();
$data['id'] = str_replace("content_","",$data['id']);

/**
 * 判断是否重复
 */
$file = fopen("hostloc.txt","r");
$filedata=fread($file,filesize("hostloc.txt"));
fclose($file);
if($filedata==$data['id']){exit('error');}

/**
 * 通知并写入文件
 */
$requestBody = array(
    'text' => 'LOC新帖:'.$data['text'],
    'desp'  => 'https://hostloc.com/thread-'.$data['id'].'-1-1.html'
);
$http->post('https://sc.ftqq.com/'.$sckey.'.send',$requestBody);
myfile($data['id']);
exit('success');

/**
 * 写入内容
 */
function myfile($data){
    $myfile = fopen("hostloc.txt", "w") or die("Unable to open file!");
    fwrite($myfile, $data);
    fclose($myfile); 
}

/**
 * 随机生成IP
 */
function rand_ip(){
    $ip_long = array(
        array('607649792', '608174079'), //36.56.0.0-36.63.255.255
        array('975044608', '977272831'), //58.30.0.0-58.63.255.255
        array('999751680', '999784447'), //59.151.0.0-59.151.127.255
        array('1019346944', '1019478015'), //60.194.0.0-60.195.255.255
        array('1038614528', '1039007743'), //61.232.0.0-61.237.255.255
        array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255
        array('1947009024', '1947074559'), //116.13.0.0-116.13.255.255
        array('1987051520', '1988034559'), //118.112.0.0-118.126.255.255
        array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255
        array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255
        array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255
        array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255
        array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255
        array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255
        array('-569376768', '-564133889'), //222.16.0.0-222.95.255.255
    );
    $rand_key = mt_rand(0, 14);
    $huoduan_ip= long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
    return $huoduan_ip;
}
?>