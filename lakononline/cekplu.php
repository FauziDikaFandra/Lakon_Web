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

    $plux = "";
    $conn  = BukaKoneksi($us, $ps, $ip, $db);
    $plu   = cekget('plu');
    $sql   = "select * from t_stok_online where plu = '$plu'";
    

    $result = sqlsrv_query($conn, $sql);
    if($result){
        $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC);
        $plux = str_replace(' ', '',$row["plu"]);
        if($plux == $plu){
           $json = array("status" => 0, "respone" => "Plu terdaftar");
        }else{
            $json = array("status" => 1, "respone" => "ok");
        }
    }
        echo json_encode($json);
?>