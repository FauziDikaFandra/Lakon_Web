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
    $rt   = cekget('rt');
    $plu   = cekget('plu');

    if(!empty($plu)){
        $sql1 = "delete from t_rt_detail where plu = '$plu' and rt_code = '$rt'";
        $result1 = sqlsrv_query($conn, $sql1);
        if($result1){
             $json = array("status" => 1, "respone" => "1");
        }else{
            $json = array("status" => 1, "respone" => "2");
        }
    }else{
        $json = array("status" => 1, "respone" => "0");
    }
    // echo $sql1;
    echo json_encode($json);
?>