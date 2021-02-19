<?php
header('Access-Control-Allow-Origin: *');
include_once "../../fungsi.php";
include_once "../../lakonsecurity.php";
    ini_set('max_execution_time',   300);
    $database    = include('../varLakon.php');
    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];

    
    $conn = BukaKoneksi($us, $ps, $ip, $db);
    $user = cekget('user');
    $po = cekget('po');
    if(!empty($user)){
        $sql = "exec close_PO '$po','$user'";

        $datas = array();
        $result = sqlsrv_query( $conn, $sql );
        if ($result){
            $json = array("status" => 1, "respone" => "Oke");
        }else{
            $json = array("status" => 0, "respone" => "Error");
        }
    }
    // echo $sql;
    echo json_encode($json);

?>