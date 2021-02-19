<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    include_once "../../fungsi.php";
    include_once "../../lakonsecurity.php";
    ini_set('max_execution_time',   300);
    $database   = include('../varLakon.php');
    $username   = cekget('username');
    $groupname  = cekget('groupname');

    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];
    $conn = BukaKoneksi($us, $ps, $ip, $db);

    $sql  = "Select branch_id, name
            from m_branch where showed = 1";
    $result = sqlsrv_query( $conn, $sql);
    $data = Array();
    if($result){
        while($row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ){
            extract($row);
            $data[] = array("branch_id"=> $branch_id,"name"=> $name);
        }
        $json = array("status"=>"1","Desc"=>"Berhasil","data"=>$data);
    }
    echo json_encode($json);
?>