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
    //$user   = cekget('user');

    $kode = cekget('kode');
    $plu  = cekget('plu');
    $qty  = cekget('qty');
    $typ  = cekget('typ');
    $rmk  = cekget('rmk');

    IF($typ == "2"){
        $typ = "in";
    }elseif($typ == "3"){
        $typ = "out";
    }
    $sql = "insert into t_adjustment_details 
            select '$kode','$plu','$qty','','','$typ',GETDATE(),'$rmk'";
    $datas = array();
    $result = sqlsrv_query( $conn, $sql );
    if ($result){
        $json = array("status" => 1, "respone" => "BERHASIL");
    }else{
        $json = array("status" => 0, "respone" => "Error");
    }
    // echo $sql;
    echo json_encode($json);
    
?>