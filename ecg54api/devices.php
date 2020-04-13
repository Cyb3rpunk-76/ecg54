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
function GetSQLFieldForXMLReturn($alias,$fieldname) {
	return ( "case when (Trim(coalesce(" . $alias . "." . $fieldname . ",'')) = '') then '-' ELSE " . $alias . "." . $fieldname . " END AS " . $fieldname );
}

header("Content-Type: text/xml");
try {
    $dbh = new PDO('mysql:host=' . $cfg_ecg54['database']['hostname'] . ';dbname=' . $cfg_ecg54['database']['database'], $cfg_ecg54['database']['username'], $cfg_ecg54['database']['password']);
	$sql_devices = 
	    "SELECT CONCAT(LPAD(nkd.net_area_id, 3, '0'), LPAD(nkd.net_device_id, 8, '0')) as dev_order, " .
		GetSQLFieldForXMLReturn('nkd','net_device_id') . ", " .
		"CONCAT(na.net_area, ' ( ', na.net_area_name, ' )') AS net_area, " .
		GetSQLFieldForXMLReturn('nkd','net_device_macadd') . ", " .
		GetSQLFieldForXMLReturn('nkd','net_device_ipadd') . ", " .
		GetSQLFieldForXMLReturn('nkd','net_device_knowinfo') . ", " .
		GetSQLFieldForXMLReturn('nkd','net_device_knowaddinfo') . ", " .
		GetSQLFieldForXMLReturn('nkd','net_device_hostname') . ", " .
		GetSQLFieldForXMLReturn('nkd','net_device_added') . ", " .
		GetSQLFieldForXMLReturn('nkd','net_device_alertsend') . ", " .
		GetSQLFieldForXMLReturn('nkl','net_devicetype_id') . ", " .
		GetSQLFieldForXMLReturn('nkl','net_mac_type') . ", " .
		GetSQLFieldForXMLReturn('nkl','net_method') . ", " .
		GetSQLFieldForXMLReturn('nkl','net_model_id') . ", " .
		GetSQLFieldForXMLReturn('nkl','cont_id') . " " .
	    "FROM network_knowdevices nkd
		INNER JOIN network_areas na ON (na.net_id = nkd.net_area_id)
		LEFT OUTER JOIN network_knowledgebase nkl ON (nkl.net_mac_info = nkd.net_device_macadd)
		ORDER BY na.net_id ASC, nkd.net_device_id ASC";
	echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><DEVICES>' . "\n";
    foreach($dbh->query($sql_devices) as $row) {
		echo '<DEVICE>';
		echo '<net_device_id>' . $row[net_device_id] . '</net_device_id>';
		echo '<net_area>' . $row[net_area] . '</net_area>';
		echo '<net_device_macadd>' . $row[net_device_macadd] . '</net_device_macadd>';
		echo '<net_device_ipadd>' . $row[net_device_ipadd] . '</net_device_ipadd>';
		echo '<net_device_knowinfo>' . $row[net_device_knowinfo] . '</net_device_knowinfo>';
		echo '<net_device_knowaddinfo>' . $row[net_device_knowaddinfo] . '</net_device_knowaddinfo>';
		echo '<net_device_hostname>' . $row[net_device_hostname] . '</net_device_hostname>';
		echo '<net_device_added>' . $row[net_device_added] . '</net_device_added>';
		echo '<net_device_alertsend>' . $row[net_device_alertsend] . '</net_device_alertsend>';
		echo '<net_devicetype_id>' . $row[net_devicetype_id] . '</net_devicetype_id>';
		echo '<net_mac_type>' . $row[net_mac_type] . '</net_mac_type>';
		echo '<net_method>' . $row[net_method] . '</net_method>';
		echo '<net_model_id>' . $row[net_model_id] . '</net_model_id>';
		echo '<cont_id>' . $row[cont_id] . '</cont_id>';
		echo '</DEVICE>';
    }
	echo "\n" . '</DEVICES>';
    $dbh = null;
} catch (PDOException $e) {
/*    print "Error!: " . $e->getMessage() . "<br/>"; */
    exit(1);
}
?>