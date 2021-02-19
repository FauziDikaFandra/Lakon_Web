<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    include_once "../../fungsi.php";
    ini_set('max_execution_time',   300);
    $username   = cekget('username');
    $id         = cekget('id');

    $database   = include('../varLakon.php');
    $ip         = $database['host'];
    $db         = $database['db'];
    $us         = $database['user'];
    $ps         = $database['pass'];

    $conn = BukaKoneksi($us, $ps, $ip, $db);
    sqlsrv_query( $conn, "delete from t_po_detail where po_code in (select po_code from t_po where po_code='$id' and status='NEW') " );
    sqlsrv_query( $conn, "delete from t_po        where po_code='$id' and status='NEW'" );

    $json = array("status" => "1");
	  echo json_encode($json);
?>
