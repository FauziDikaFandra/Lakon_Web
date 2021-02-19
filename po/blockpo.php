<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    include_once "../../fungsi.php";
    include_once "../../lakonsecurity.php";
    ini_set('max_execution_time',   300);
    $database    = include('../varLakon.php');
    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];
    $po   = cekget('po');

    $conn = BukaKoneksi($us, $ps, $ip, $db);
    $sql1 = "select * from t_rc where po_code = '$po'";
    $result1 = sqlsrv_query( $conn, $sql1 );
    $stat_po;
    while( $row1 = sqlsrv_fetch_array( $result1, SQLSRV_FETCH_ASSOC) ) {
        extract($row1);
        $stat_po = $status;
    }

    if($stat_po == "POST"){
        $json = array("status" => 0, "respone" => "Sudah Proses GR");
    }else{
        $sql = "exec block_po '$po'";
        $result = sqlsrv_query( $conn, $sql );
        if($result){
            $json = array("status" => 1, "respone" => "Berhasil");
        }else{
            $json = array("status" => 0, "respone" => "Gagal");
        }
    }
    echo json_encode($json);
?>