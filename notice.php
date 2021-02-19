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
    $group = cekget('group');
    $id = cekget('id');
    $sql1 = "select notice as pesan from notice where isactive = 1 and tipe_notice = 'ALL' and akses = $group";
    // $sql2 = "select notice as pesan from notice where isactive = 1 and tipe_notice = 'ONE' and akses 
    //         = $group and id_user = '$id'";
    // echo $sql2;
    $datas= "";
    $result = sqlsrv_query( $conn, $sql1 );
    if($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $datas = $row["pesan"];
        }
        $json = array("status" => 1, "respone1" => $datas, "respone2" => null);
    }else{
        $json = array("status" => 0, "respone1" => null, "respone2" => null);
    }  
    echo json_encode($json);
?>