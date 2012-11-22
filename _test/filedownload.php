<?php
/// GLOBAL SETTINGS - CHANGE THEM !
$token = '81270fbe1b3c31c1c71eae38475b0eec';
$domainname = 'http://localhost/moodle';

/// DOWNLOAD PARAMETERS
//Note: The service associated to the user token must allow "file download" !
//      in the administration, edit the service to check the setting (click "advanced" button on the edit page).

//Normally you retrieve the file download url from calling the web service core_course_get_contents()
//However to be quick to demonstrate the download call,
//you are going to retrieve the file download url manually:
//1- In Moodle, create a forum with an attachement
//2- look at the attachement link url, and copy everything after http://YOURMOODLE/pluginfile.php
//   into the above variable
$relativepath = '/22/mod_resource/content/1/p146-roessling.pdf'; //CHANGE THIS !

//CHANGE THIS ! This is where you will store the file.
//Don't forget to allow 'write permission' on the folder for your web server.
$path = '/home/yoshiaki/p146-roessling.pdf';

/// DOWNLOAD IMAGE - Moodle 2.2 and later
$url  = $domainname . '/webservice/pluginfile.php' . $relativepath; //NOTE: normally you should get this download url from your previous call of core_course_get_contents()
$tokenurl = $url . '?token=' . $token; //NOTE: in your client/app don't forget to attach the token to your download url
$fp = fopen($path, 'w');
$ch = curl_init($tokenurl);
curl_setopt($ch, CURLOPT_FILE, $fp);
$data = curl_exec($ch);
curl_close($ch);
fclose($fp);