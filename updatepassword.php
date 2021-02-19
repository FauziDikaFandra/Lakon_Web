<?php
header('Access-Control-Allow-Origin: *');
include_once "../fungsi.php";
include_once "../lakonsecurity.php";
    ini_set('max_execution_time',   300);
    $database    = include('varLakon.php');
    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];
    $conn = BukaKoneksi($us, $ps, $ip, $db);

    $user = cekget('user');
    $pass = cekget('pass');
    $group = cekget('group');
    if($group == "2"){
        $sql = "update m_vendor set password = '$pass' where vendor_code = '$user'";
    }else{
        $sql   = "update s_user set password = '$pass' where username = '$user'";
    }
    
    $brands = "";
    $result = sqlsrv_query( $conn, $sql );
    if($result){
        $json = array("hasil" => "1" );
    }else{
        $json = array("hasil" => "2" );
    }
    // echo $sql;
    echo json_encode($json);
?>
