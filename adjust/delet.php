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
    $status_ = "";
    $sqlcek = "select * from t_adjustment where adj_kode = '$kode'";
    $resultcek = sqlsrv_query( $conn, $sqlcek );
    while( $row = sqlsrv_fetch_array( $resultcek, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $status_ = $status;
    }
    if($status_ == "OPEN"){
        $sql = "exec del_adj '$kode','$plu'";
        $datas = array();
        $result = sqlsrv_query( $conn, $sql );
        if ($result){
            $json = array("status" => 1, "respone" => "BERHASIL");        
        }else{
            $json = array("status" => 0, "respone" => "Error");
        }
    }else{
        $json = array("status" => 0, "respone" => "Error");
    }
    
    // echo $sql;
    echo json_encode($json);
    
?>