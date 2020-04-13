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
	$paridx = 0;
    $dbh = new PDO('mysql:host=' . $cfg_ecg54['database']['hostname'] . ';dbname=' . $cfg_ecg54['database']['database'], $cfg_ecg54['database']['username'], $cfg_ecg54['database']['password']);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($_GET['cont_id']==-1) {
        $stmt = $dbh->prepare('INSERT INTO contacts (cont_name, cont_shortname, cont_email, cont_phone, cont_nickname, cont_telegramnick, cont_admin, cont_needreceivealerts, cont_url_web) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) ');		
	} else {
	    $stmt = $dbh->prepare('UPDATE contacts SET cont_name=?, cont_shortname=?, cont_email=?, cont_phone=?, cont_nickname=?, cont_telegramnick=?, cont_admin=?, cont_needreceivealerts=?, cont_url_web=? WHERE cont_id=? ');
	}
	$stmt->bindParam( ++$paridx, $_GET['cont_name'], PDO::PARAM_STR);
	$stmt->bindParam( ++$paridx, $_GET['cont_shortname'], PDO::PARAM_STR);
	$stmt->bindParam( ++$paridx, $_GET['cont_email'], PDO::PARAM_STR);
	$stmt->bindParam( ++$paridx, $_GET['cont_phone'], PDO::PARAM_STR);
	$stmt->bindParam( ++$paridx, $_GET['cont_nickname'], PDO::PARAM_STR);
	$stmt->bindParam( ++$paridx, $_GET['cont_telegramnick'], PDO::PARAM_STR);
	$stmt->bindParam( ++$paridx, $_GET['cont_admin'], PDO::PARAM_INT);
	$stmt->bindParam( ++$paridx, $_GET['cont_needreceivealerts'], PDO::PARAM_INT);
	$stmt->bindParam( ++$paridx, $_GET['cont_url_web'], PDO::PARAM_STR);
	if (!($_GET['cont_id']==-1)) { $stmt->bindParam( ++$paridx, $_GET['cont_id'], PDO::PARAM_INT); }	
	$stmt->execute();
/*	print_r ($_GET) . ' <br> <br> ';
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