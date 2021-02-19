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
    $group = cekget('group');
    $brand = cekget('brand');
    $id = cekget('id');
    $brand = str_replace("^","&",$brand);
    $filter = "";
    if($brand == "undefined" || $brand == "all"){
        $filter = "supplier_code = '$id'";
    }else{
        $filter = "brand = '$brand'";
    }

    $sql1 = "select Long_Description as dsc from Item_Master where $filter ";
    $datas= "";
    $result = sqlsrv_query( $conn, $sql1 );
    if($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $datas[] = array("dsc" => $dsc);
        }
        $json = array("status" => 1, "respone" => $datas);
    }else{
        $json = array("status" => 0, "respone" => null);
    }  

    echo json_encode($json);
?>