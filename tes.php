<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    include_once "../fungsi.php";
    include_once "../lakonsecurity.php";
    ini_set('max_execution_time',   300);

    $groupname   = cekget('groupname');
    $moduleid    = cekget('moduleid');
    $username    = cekget('username');

    $database    = include('varLakon.php');
    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];
    


    $outp0 = getLakonSecurity($username, $moduleid, 'im', 'dp2');
    echo $outp0;
    // $outp0 = getJsonModule($ip, $db, $us, $ps);
    // $json = array("data" => $outp0);
	  // echo json_encode($json);



?>
