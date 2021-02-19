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

    $groupname   = cekget('groupname');
    $username    = cekget('username');

    $conn = BukaKoneksi($us, $ps, $ip, $db);
    $parday = date("Y-m-d");

    if($groupname == "2"){
        $filter = " where vendor_code = '$username'";
    }else{
        $filter = "";
    }

    $sql = "select name as article_code from m_vendor $filter";
    
    $datas = array();
    $result = sqlsrv_query( $conn, $sql );
    if ($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $datas[] = array("article_code" => $article_code);
        }
        $json = array("status" => 1, "respone" => $datas);
    }else{
        $json = array("status" => 0, "respone" => "Error");
    }

    // echo $sql;
    echo json_encode($json);

?>