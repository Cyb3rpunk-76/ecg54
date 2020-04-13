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
	    "SELECT " .
		GetSQLFieldForXMLReturn('net_area') . ", " .
		GetSQLFieldForXMLReturn('net_device_last_check') . ", " .
		GetSQLFieldForXMLReturn('net_device_ipadd') . ", " .
		GetSQLFieldForXMLReturn('net_device_status') . ", " .
		GetSQLFieldForXMLReturn('net_device_macadd') . ", " .
		GetSQLFieldForXMLReturn('device_owner') . ", " .
		GetSQLFieldForXMLReturn('device_type') . ", " .
		GetSQLFieldForXMLReturn('net_method') . ", " .
		GetSQLFieldForXMLReturn('net_model') . ", " .
		GetSQLFieldForXMLReturn('net_vendor') . " " .
	    "FROM network 
	     ORDER BY
		   net_area ASC,
		   net_device_status DESC,
		   net_device_last_check DESC,
		   net_device_ipadd ASC";
	echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><NETWORKDEVICES>' . "\n";
    foreach($dbh->query($sql_devices) as $row) {
		echo '<DEVICE>';
		echo '<net_area>' . $row[net_area] . '</net_area>';
		echo '<net_device_last_check>' . $row[net_device_last_check] . '</net_device_last_check>';
		echo '<net_device_ipadd>' . $row[net_device_ipadd] . '</net_device_ipadd>';
		echo '<net_device_status>' . $row[net_device_status] . '</net_device_status>';
		echo '<net_device_macadd>' . $row[net_device_macadd] . '</net_device_macadd>';
		echo '<device_owner>' . $row[device_owner] . '</device_owner>';
		echo '<device_type>' . $row[device_type] . '</device_type>';
		echo '<net_method>' . $row[net_method] . '</net_method>';
		echo '<net_model>' . $row[net_model] . '</net_model>';
		echo '<net_vendor>' . $row[net_vendor] . '</net_vendor>';
		echo '</DEVICE>';
    }
	echo "\n" . '</NETWORKDEVICES>';
    $dbh = null;
} catch (PDOException $e) {
/*    print "Error!: " . $e->getMessage() . "<br/>"; */
    exit(1);
}
?>