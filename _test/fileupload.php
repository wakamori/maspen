<?php
// This file is NOT a part of Moodle - http://moodle.org/
//
// This client for Moodle 2 is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//

/**
 * HTTP file upload/download client for Moodle 2.1/Moodle2.2 and higher
 * 
 * THIS DOES NOT CALL A WEB SERVICE FUNCTION BUT DEMONSTRATE HOW TO UPLOAD A FILE
 * IN USER PRIVATE FILE, AND TO DOWNLOAD A FILE.
 *
 * @author Jerome Mouneyrac
 */

/// GLOBAL SETTINGS - CHANGE THEM !
$token = "1d0f4e17485d848fa1be951921458169";
$domainname = 'http://localhost/moodle';

/* ========================================================================= */

/// UPLOAD PARAMETERS
//Note: check "Maximum uploaded file size" in your Moodle "Site Policies".
$imagepath = "/home/yoshiaki/uploadtest.txt"; //CHANGE THIS !
$filepath = "/"; //put the file to the root of your private file area. //OPTIONAL

/// UPLOAD IMAGE - Moodle 2.1 and later
$params = array('file_box' => "@".$imagepath,'filepath' => $filepath, 'token' => $token);
echo $params;
$ch = curl_init();
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
curl_setopt($ch, CURLOPT_URL, $domainname . '/webservice/upload.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params); 
$response = curl_exec($ch);
print_r($response);
//[{"component":"user","contextid":"5","userid":"2","filearea":"private","filename":"test.txt","filepath":"\/","itemid":0,"license":"allrightsreserved","author":"Kanemura Yoshiaki","source":""}]
