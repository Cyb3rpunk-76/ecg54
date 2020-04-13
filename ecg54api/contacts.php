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
	    "SELECT LPAD(cont_id, 8, '0') as cont_id, " .
		GetSQLFieldForXMLReturn('cont_id') . ", " .
		GetSQLFieldForXMLReturn('cont_name') . ", " .
		GetSQLFieldForXMLReturn('cont_shortname') . ", " .
		GetSQLFieldForXMLReturn('cont_email') . ", " .
		GetSQLFieldForXMLReturn('cont_phone') . ", " .
		GetSQLFieldForXMLReturn('cont_nickname') . ", " .
		GetSQLFieldForXMLReturn('cont_telegramnick') . ", " .
		GetSQLFieldForXMLReturn('cont_admin') . ", " .
		GetSQLFieldForXMLReturn('cont_needreceivealerts') . ", " .
		GetSQLFieldForXMLReturn('cont_url_web') . " " .
	    "FROM contacts
	     ORDER BY
		   1 ASC";
	echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><CONTACTS>' . "\n";
    foreach($dbh->query($sql_devices) as $row) {
		echo '<CONTACT>';
		echo '<cont_order>' . $row[cont_id] . '</cont_order>';
		echo '<cont_id>' . $row[cont_id] . '</cont_id>';
		echo '<cont_name>' . $row[cont_name] . '</cont_name>';
		echo '<cont_shortname>' . $row[cont_shortname] . '</cont_shortname>';
		echo '<cont_email>' . $row[cont_email] . '</cont_email>';
		echo '<cont_phone>' . $row[cont_phone] . '</cont_phone>';
		echo '<cont_nickname>' . $row[cont_nickname] . '</cont_nickname>';
		echo '<cont_telegramnick>' . $row[cont_telegramnick] . '</cont_telegramnick>';
		echo '<cont_admin>' . $row[cont_admin] . '</cont_admin>';
		echo '<cont_needreceivealerts>' . $row[cont_needreceivealerts] . '</cont_needreceivealerts>';
		echo '<cont_url_web>' . $row[cont_url_web] . '</cont_url_web>';
		echo '</CONTACT>';
    }
	echo "\n" . '</CONTACTS>';
    $dbh = null;
} catch (PDOException $e) {
/*    print "Error!: " . $e->getMessage() . "<br/>"; */
    exit(1);
}
?>