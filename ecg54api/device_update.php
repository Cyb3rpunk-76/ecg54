<?php
define('IN_MYBB', 1);
require_once '../global.php';
require_once MYBB_ROOT.'inc/functions_forumlist.php';
require_once MYBB_ROOT.'inc/class_parser.php';
require_once MYBB_ROOT.'ecg54api/config.php';
$parser = new postParser;
if($mybb->user['uid'] == 0 || $mybb->usergroup['canusercp'] == 0)
{
	exit(1);
}
try {
	if ($_GET['cont_id']==-1) {
		exit(1);
	}	
	$paridx = 0;
    $dbh = new PDO('mysql:host=' . $cfg_ecg54['database']['hostname'] . ';dbname=' . $cfg_ecg54['database']['database'], $cfg_ecg54['database']['username'], $cfg_ecg54['database']['password']);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $dbh->prepare('UPDATE network_knowdevices SET net_device_hostname=?, net_device_knowinfo=?, net_device_knowaddinfo=? WHERE net_device_id=? ');
	$stmt->bindParam( ++$paridx, $_GET['net_device_hostname'], PDO::PARAM_STR);
	$stmt->bindParam( ++$paridx, $_GET['net_device_knowinfo'], PDO::PARAM_STR);
	$stmt->bindParam( ++$paridx, $_GET['net_device_knowaddinfo'], PDO::PARAM_STR);
	$stmt->bindParam( ++$paridx, $_GET['net_device_id'], PDO::PARAM_INT);
	$stmt->execute();
    foreach($dbh->query('SELECT net_device_macadd from network_knowdevices WHERE net_device_id=' . $_GET['net_device_id']) as $row) {
		$target_mac = $row[net_device_macadd];
    }
	$paridx = 0;
	if ($_GET['net_devicetype_id']==-1) {
		$stmt = $dbh->prepare('DELETE FROM network_knowledgebase WHERE net_mac_info =? ');
		$stmt->bindParam( ++$paridx, $target_mac, PDO::PARAM_STR);
	} else {
		$stmt = $dbh->prepare('REPLACE INTO network_knowledgebase ( net_mac_info, net_mac_type, cont_id, net_method, net_model_id, net_devicetype_id ) VALUES( ? , ? , ? , ? , ? , ? ) ');
		$stmt->bindParam( ++$paridx, $target_mac, PDO::PARAM_STR);
		$stmt->bindParam( ++$paridx, $_GET['net_mac_type'], PDO::PARAM_STR);
		$stmt->bindParam( ++$paridx, $_GET['cont_id'], PDO::PARAM_INT);
		$stmt->bindParam( ++$paridx, $_GET['net_method'], PDO::PARAM_STR);
		$stmt->bindParam( ++$paridx, $_GET['net_model_id'], PDO::PARAM_INT);
		$stmt->bindParam( ++$paridx, $_GET['net_devicetype_id'], PDO::PARAM_INT);
	}	
	$stmt->execute();
/*	
    print_r ($_GET) . ' <br> <br> ';
	print_r ($stmt) . ' <br> <br> ';
	print $stmt->debugDumpParams() . "<br><br>";
	print "<<br>br>Row count: " . $stmt->RowCount() . "<br>"; */
    $dbh = null;	
} 
catch (PDOException $e) { 
    /* print "Error!: " . $e->getMessage() . "<br>";
    print $stmt->debugDumpParams() . "<br>";
    print $stmt->errorInfo() . "<br>"; */
    exit(1);
}
?>