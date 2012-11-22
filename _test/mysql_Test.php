<?php
$link = mysql_connect('localhost', 'yoshiaki', 'k04190403');
if (!$link) {
	die('接続失敗です。'.mysql_error());
}

print('<p>接続に成功しました。</p>');

$db_selected = mysql_select_db('moodle', $link);
if (!$db_selected){
	die('データベース選択失敗です。'.mysql_error());
}

print('<p>データベースを選択しました。</p>');

mysql_set_charset('utf8');
$name = 'Test Course';
$result = mysql_query("SELECT id FROM mdl_course WHERE fullname='$name' OR shortname='$name'");
if (!$result) {
	die('クエリーが失敗しました。'.mysql_error());
}

$row = mysql_fetch_assoc($result);
var_dump($row);
$instance = $row['id'];

$close_flag = mysql_close($link);

if ($close_flag){
	print('<p>切断に成功しました。</p>');
}



$name = "test";
$link = mysql_connect('localhost', 'yoshiaki', 'k04190403');
if (!$link) {
	throw new moodle_exception('Faild to connect to a MySQL Server.'.mysql_error());
}

$db_selected = mysql_select_db('moodle', $link);
if (!$db_selected){
	throw new moodle_exception('Faild to select a Database.'.mysql_error());
}

mysql_set_charset('utf8');
$result = mysql_query("SELECT id FROM mdl_course WHERE fullname='$name' OR shortname='$name'");
if (!$result) {
	throw new moodle_exception('Faild to send a MySQL query.'.mysql_error());
}

$row = mysql_fetch_assoc($result);

$close_flag = mysql_close($link);

if (!$close_flag) {
	throw new moodle_exception('Faild to close MySQL connection.'.mysql_error());
}

echo $row['id'];