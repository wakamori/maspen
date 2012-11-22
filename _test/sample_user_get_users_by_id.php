<?php

$domainname = 'http://localhost/moodle';
$token = "317c09d8efced2ef42df8589943cf03e";
$function = 'core_user_get_users_by_id';

// REST RETURNED VALUES FORMAT
$restformat = 'json'; //Also possible in Moodle 2.2 and later: 'json'
                     //Setting it to 'json' will fail all calls on earlier Moodle version

//$params = array('userids'=>array(2));
$params = array('userids'=>array(2));

$serverurl = "$domainname/webservice/rest/server.php";
$serverurl.= "?wstoken=$token";
$serverurl.= "&wsfunction=$function";

header('Content-Type: text/plain');
require_once('./curl.php');
$curl = new curl;
//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
$data = $curl->post($serverurl . $restformat, $params);
var_dump ($data);

//xml
//http://localhost/moodle/webservice/rest/server.php?wstoken=abdc20cca8483f511e69e4ce5a7f8f71&wsfunction=core_user_get_users_by_id&userids[0]=2

//json
//http://localhost/moodle/webservice/rest/server.php?wstoken=abdc20cca8483f511e69e4ce5a7f8f71&wsfunction=core_user_get_users_by_id&userids[0]=2&moodlewsrestformat=json

//simpleserver.php?wsusername=admin&wspassword=@Index1206