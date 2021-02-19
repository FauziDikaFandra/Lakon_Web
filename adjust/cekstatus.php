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
    $hasil = "";
    $sql = "select * from t_adjustment where adj_kode = '$kode'";
    $datas = array();
    $result = sqlsrv_query( $conn, $sql );
    if ($result){
         while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
                $json = array("statusx" => $status);
         }
    }else{
        $json = array("statusx" => "Error");
    }
    // echo $sql;
    echo json_encode($json);
    
?>