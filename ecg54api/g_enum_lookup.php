<?php
define('IN_MYBB', 1);
require_once '../global.php';
require_once MYBB_ROOT.'inc/functions_forumlist.php';
require_once MYBB_ROOT.'inc/class_parser.php';
require_once MYBB_ROOT.'ecg54api/config.php';
$parser = new postParser;
$plugins->run_hooks('index_start');
if($mybb->user['uid'] == 0 || $mybb->usergroup['canusercp'] == 0)
{
	exit(1);
}
$input_field = $_GET["field"];
if("" == trim($input_field))
{
	exit(1);
}
$table=explode(".",$input_field);
header("Content-Type: text/xml");
try {
    $dbh = new PDO('mysql:host=' . $cfg_ecg54['database']['hostname'] . ';dbname=' . $cfg_ecg54['database']['database'], $cfg_ecg54['database']['username'], $cfg_ecg54['database']['password']);
	$sql_enum = "SHOW COLUMNS FROM " . $table[0] . " WHERE Field = '" . $table[1] . "'";
	echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><LOOKUP>' . "\n";
    foreach($dbh->query($sql_enum) as $row) {
		$enuminfo = $row[Type];
    }
	$lookup_data=explode("(",$enuminfo);
	$lookup_data=explode(")",$lookup_data[1]);
	$lookup_data=$lookup_data[0];
	$lookup_data=str_getcsv($lookup_data, ',', "'");
	foreach ($lookup_data as $enum) {
		echo '<LOOKUPDATA>';
		echo '<ORDER>' . $enum . '</ORDER>';
		echo '<ID>' . $enum . '</ID>';
		echo '<DESC>' . $enum . '</DESC>';
		echo '</LOOKUPDATA>';
	}
	echo "\n" . '</LOOKUP>';
    $dbh = null;
} catch (PDOException $e) {
/*    print "Error!: " . $e->getMessage() . "<br/>"; */
    exit(1);
}
?>