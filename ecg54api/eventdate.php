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

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

if(! validateDate($_GET["date"], 'd-m-Y'))
{
	exit(1);
}

// Garante que valores nullos ou vazios não saiam no XML, facilita na hora de manipular no frontend
function GetSQLFieldForXMLReturn($fieldname) {
	return ( "case when (Trim(coalesce(" . $fieldname . ",'')) = '') then '-' ELSE " . $fieldname . " END AS " . $fieldname );
}

$timestamp_ini = strtotime($_GET["date"]);
$timestamp_fim = strtotime($_GET["date"] . ' + 1 days - 1 second');

header("Content-Type: text/xml");
try {
    $dbh = new PDO('mysql:host=' . $cfg_ecg54['database']['hostname'] . ';dbname=' . $cfg_ecg54['database']['database'], $cfg_ecg54['database']['username'], $cfg_ecg54['database']['password']);
	$sql_events = 
	    "SELECT " .
		GetSQLFieldForXMLReturn('net_device_ipadd') . ", " .
		GetSQLFieldForXMLReturn('net_device_macadd') . ", " .
		GetSQLFieldForXMLReturn('network_when') . ", " .
		GetSQLFieldForXMLReturn('network_eventtype_desc') . ", " .
		GetSQLFieldForXMLReturn('device_type') . ", " .
		GetSQLFieldForXMLReturn('net_model') . ", " .
		GetSQLFieldForXMLReturn('net_vendor') . ", " .
		GetSQLFieldForXMLReturn('net_method') . ", " .
		GetSQLFieldForXMLReturn('device_owner') . ", " .
		GetSQLFieldForXMLReturn('net_last_check') . " " .
	    "FROM network_activity
		 WHERE network_when BETWEEN :data_ini AND :data_fim
	     ORDER BY
		   network_when ASC";
	$eventsnetwork = $dbh->prepare($sql_events, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $eventsnetwork->execute(array(':data_ini' => date("Y-m-d H:i:s", $timestamp_ini), ':data_fim' => date("Y-m-d H:i:s", $timestamp_fim)));
    $eventos = $eventsnetwork->fetchAll();
	echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><EVENTSNETWORK>' . "\n";
    foreach($eventos as $row) {
		echo '<EVENT>';
		echo '<net_device_ipadd>' . $row[net_device_ipadd] . '</net_device_ipadd>';
		echo '<net_device_macadd>' . $row[net_device_macadd] . '</net_device_macadd>';
		echo '<network_when>' . $row[network_when] . '</network_when>';
		echo '<network_eventtype_desc>' . $row[network_eventtype_desc] . '</network_eventtype_desc>';
		echo '<device_type>' . $row[device_type] . '</device_type>';
		echo '<net_model>' . $row[net_model] . '</net_model>';
		echo '<net_vendor>' . $row[net_vendor] . '</net_vendor>';
		echo '<net_method>' . $row[net_method] . '</net_method>';
		echo '<device_owner>' . $row[device_owner] . '</device_owner>';
		echo '<net_last_check>' . $row[net_last_check] . '</net_last_check>';
		echo '</EVENT>';
    }
	echo "\n" . '</EVENTSNETWORK>';
    $dbh = null;
} catch (PDOException $e) {
/*    print "Error!: " . $e->getMessage() . "<br/>"; */
    exit(1);
}
?>