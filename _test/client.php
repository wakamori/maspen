<?php
// This file is NOT a part of Moodle - http://moodle.org/
//
// This client for Moodle 2 is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//

/**
 * REST client for Moodle 2
 * Return JSON or XML format
 *
 * @authorr Jerome Mouneyrac
 */

/// SETUP - NEED TO BE CHANGED
//http://localhost/moodle/webservice/rest/server.php?wstoken=81270fbe1b3c31c1c71eae38475b0eec&wsfunction=local_exfunctions_download&downloadpath=uploadtest.txt&moodlewsrestformat=json
$token = 'cf43f71e96ae4e1ea37b1e2cb3370eae';
$domainname = 'http://localhost/moodle';
$functionname = 'core_user_get_users_by_id';

// REST RETURNED VALUES FORMAT
$restformat = 'json'; //Also possible in Moodle 2.2 and later: 'json'
                     //Setting it to 'json' will fail all calls on earlier Moodle version

//////// moodle_user_create_users ////////

/// PARAMETERS - NEED TO BE CHANGED IF YOU CALL A DIFFERENT FUNCTION
$params = array('userids[0]'=> '3');

/// REST CALL
//header('Content-Type: text/plain');
$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
require_once('./curl.php');
$curl = new curl;
//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
$resp = $curl->post($serverurl . $restformat, $params);
//print_r($resp);
echo "<pre>";
var_dump(json_decode($resp));
echo "</pre>";
/*
cf43f71e96ae4e1ea37b1e2cb3370eae	Admin User	test
e323cbd02efed34ee58d022345187d0d	Admin User	exfunctions
65eff623cec8b141c8e37c214f460b90	Taro Tanaka	exfunctions
b53b5f72165d1c7a99d463f9d93584db	Jiro Suzuki	exfunctions
 */