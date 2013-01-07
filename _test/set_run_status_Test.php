<?php
$token = '08785ff27bbf462a64cca1fee185255f';
$domainname = 'http://localhost/maspen';
$user  = 3;
$module= 2;
$code  = 453;
$error = 325;

if(0){
	$token = '863941fa304ba6566e5c392515286aa3';
	$domainname = 'http://konoha.ubicg.ynu.ac.jp/maspen';
	$id = 2;
}
$functionname = 'local_exfunctions_set_run_status';

$restformat = 'json';

$params = array('user'=> $user, 'module'=>$module, 'code'=>$code, 'error'=>$error);

$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;

require_once('./curl.php');
$curl = new curl;
//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
$resp = $curl->post($serverurl . $restformat, $params);

echo "<pre>";
print_r($resp);
echo "\n\n\n";
var_dump(json_decode($resp));
echo "</pre>";