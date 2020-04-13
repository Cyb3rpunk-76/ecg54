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
	    "SELECT LPAD(net_model_id, 8, '0') as net_model_id, " .
		GetSQLFieldForXMLReturn('net_model_id') . ", " .
		GetSQLFieldForXMLReturn('net_model_name') . " " .
	    "FROM network_device_models
	     ORDER BY
		   net_model_id ASC";
	echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><LOOKUP>' . "\n";
    foreach($dbh->query($sql_devices) as $row) {
		echo '<LOOKUPDATA>';
		echo '<ORDER>' . $row[net_model_id] . '</ORDER>';
		echo '<ID>' . $row[net_model_id] . '</ID>';
		echo '<DESC>' . $row[net_model_name] . '</DESC>';
		echo '</LOOKUPDATA>';
    }
	echo "\n" . '</LOOKUP>';
    $dbh = null;
} catch (PDOException $e) {
/*    print "Error!: " . $e->getMessage() . "<br/>"; */
    exit(1);
}
?>