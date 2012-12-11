<?php
$token = '08785ff27bbf462a64cca1fee185255f';
$domainname = 'http://localhost/moodle';
//$token = 'ea7140698543c4e97ac995e13b21d2ec';
//$domainname = 'http://konoha.ubicg.ynu.ac.jp/maspen';
$functionname = 'local_exfunctions_submit_assignment';

$restformat = 'json';

$params = array('id'=> '2', 'text' => 'Hello, World.', 'userid'=>'3');

$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
require_once('./curl.php');
$curl = new curl;
//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
$resp = $curl->post($serverurl . $restformat, $params);

echo "<pre>";
echo $resp."\n";
var_dump(json_decode($resp));
echo "</pre>";