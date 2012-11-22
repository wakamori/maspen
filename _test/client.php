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
$token = '81270fbe1b3c31c1c71eae38475b0eec';
$domainname = 'http://localhost/moodle';
$functionname = 'core_files_get_files';

// REST RETURNED VALUES FORMAT
$restformat = 'json'; //Also possible in Moodle 2.2 and later: 'json'
                     //Setting it to 'json' will fail all calls on earlier Moodle version

//////// moodle_user_create_users ////////

/// PARAMETERS - NEED TO BE CHANGED IF YOU CALL A DIFFERENT FUNCTION
$params = array('contextid' => '15');

/// REST CALL
//header('Content-Type: text/plain');
$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
require_once('./curl.php');
$curl = new curl;
//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
$resp = $curl->post($serverurl . $restformat, $params);
//print_r($resp);
var_dump(json_decode($resp));
/*
81270fbe1b3c31c1c71eae38475b0eec	Admin User	test
7f7704a1be159241b88351144bd5065a	Admin User	exfunctions
2caccd83285cf074ddbfe5ec09baa864	Taro Tanaka	exfunctions
3ad8554dbf72606635ec5ce89dad73f2	Jiro Suzuki	exfunctions
 */