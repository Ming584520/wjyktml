<?php
//设置忽略是否关闭终端窗口源码分享站出品ymfxz.com
ignore_user_abort(true);
ini_set('max_execution_time', '0');
set_time_limit(0);

echo 'ok:'.date("Y-m-d G:i:s");

//任务执行几秒后终止
define("time_zx",58);

//每停止几秒后执行
define("time_sleep",1);

//每停止几秒后执行  微秒
define("time_usleep",200000);//200000=0.2秒

//每停止几秒后执行  微秒
define("time_usleepss",2000000);//3000000=3秒


if(function_exists('fastcgi_finish_request')) fastcgi_finish_request();

$ztime=(strtotime(date("Y-m-d G:i:s"))+time_zx);
$logname="coller";

while(1) {
    $url="http://{$_SERVER['HTTP_HOST']}/index/api/getdate";
    $nr=mycurl($url);
    mylog($logname,"[".date("Y-m-d G:i:s")."]: ".$url." [{$nr}]");
    usleep(time_usleepss);
    
    $url="http://{$_SERVER['HTTP_HOST']}/index/api/order";
    $nr=mycurl($url);
    mylog($logname,"[".date("Y-m-d G:i:s")."]: ".$url." [{$nr}]");
    usleep(time_usleep);
    
    // $url="http://{$_SERVER['HTTP_HOST']}/index/api/addorder";
    $nr=mycurl($url);
    mylog($logname,"[".date("Y-m-d G:i:s")."]: ".$url." [{$nr}]");
    usleep(time_usleep);
    
    $url="http://{$_SERVER['HTTP_HOST']}/index/api/allotorder";
    $nr=mycurl($url);
    mylog($logname,"[".date("Y-m-d G:i:s")."]: ".$url." [{$nr}]");
    usleep(time_usleep);
    
    if(strtotime(date("Y-m-d G:i:s"))>=$ztime){
        unlink($_SERVER['DOCUMENT_ROOT'].'/log/mylog/'.$logname.'.log');
        break;
    }
}

function mylog($name,$var){
	//设置路径目录信息
	$name=$name?$name:date("Y-m-d");
	$url  = $_SERVER['DOCUMENT_ROOT'].'/log/mylog/'.$name.'.log';
	//取出目录路径中目录(不包括后面的文件)
	$dir_name = dirname($url);
	//如果目录不存在就创建
	if(!file_exists($dir_name)) {
		$res = mkdir($dir_name,0777,true);
		if($res===false){
			return false;
		}
	}
	file_put_contents( $url , $var."\n" , FILE_APPEND | LOCK_EX );
}

function mycurl($url,$params=false,$ispost=0){
    $httpInfo = array();
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
    curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 3 );
    curl_setopt( $ch, CURLOPT_TIMEOUT , 3);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
    if( $ispost )
    {
        curl_setopt( $ch , CURLOPT_POST , true );
        curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
        curl_setopt( $ch , CURLOPT_URL , $url );
    }
    else
    {
        if($params){
            curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
        }else{
            curl_setopt( $ch , CURLOPT_URL , $url);
        }
    }
    $response = curl_exec( $ch );
    if ($response === FALSE) {
        //echo "cURL Error: " . curl_error($ch);
        return false;
    }
    $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
    $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
    curl_close( $ch );
    return $response;
}