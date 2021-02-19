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
    $ket = cekget('ket');
    // echo $kode;
    $hasil = "";
    $sql = "select isnull(b.adj_kode,'0')code from t_adjustment a 
            left join t_adjustment_details b on a.adj_kode = b.adj_kode
            where a.adj_kode = '$kode' and a.status <> 'POST'";
    $datas = array();
    $result = sqlsrv_query( $conn, $sql );
    if ($result){
         while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
         }
        if($code == "0"){
                $json = array("status" => 0, "respone" => "Detail kosong");        
        }else if($code == $kode){
            $sqlexec = "exec adjustment_ '$kode','$ket'";
            $resultakhir = sqlsrv_query( $conn, $sqlexec );
            if($resultakhir){
                $json = array("status" => 1, "respone" => "BERHASIL");
            }
        }
    }else{
        $json = array("status" => 0, "respone" => "Error");
    }
    // echo $sql;
    echo json_encode($json);
    
?>