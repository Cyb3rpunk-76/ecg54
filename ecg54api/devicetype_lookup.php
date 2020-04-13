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

// Garante que valores nullos ou vazios não saiam no XML, facilita na hora de manipular no frontend
function GetSQLFieldForXMLReturn($fieldname) {
	return ( "case when (Trim(coalesce(" . $fieldname . ",'')) = '') then '-' ELSE " . $fieldname . " END AS " . $fieldname );
}


header("Content-Type: text/xml");
try {
    $dbh = new PDO('mysql:host=' . $cfg_ecg54['database']['hostname'] . ';dbname=' . $cfg_ecg54['database']['database'], $cfg_ecg54['database']['username'], $cfg_ecg54['database']['password']);
	$sql_devices = 
	    "SELECT LPAD(device_type_id, 8, '0') as device_type_id, " .
		GetSQLFieldForXMLReturn('device_type_id') . ", " .
		GetSQLFieldForXMLReturn('device_type_name') . " " .
	    "FROM network_device_types
	     ORDER BY
		   device_type_id ASC";
	echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><LOOKUP>' . "\n";
	echo '<LOOKUPDATA>';
	echo '<ORDER>-1</ORDER>';
	echo '<ID>-1</ID>';
	echo '<DESC>-</DESC>';
	echo '</LOOKUPDATA>';
    foreach($dbh->query($sql_devices) as $row) {
		echo '<LOOKUPDATA>';
		echo '<ORDER>' . $row[device_type_id] . '</ORDER>';
		echo '<ID>' . $row[device_type_id] . '</ID>';
		echo '<DESC>' . $row[device_type_name] . '</DESC>';
		echo '</LOOKUPDATA>';
    }
	echo "\n" . '</LOOKUP>';
    $dbh = null;
} catch (PDOException $e) {
/*    print "Error!: " . $e->getMessage() . "<br/>"; */
    exit(1);
}
?>