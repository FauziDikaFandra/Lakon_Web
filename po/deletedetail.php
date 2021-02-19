<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    include_once "../../fungsi.php";
    ini_set('max_execution_time',   300);
    $username   = cekget('username');
    $groupname  = cekget('groupname');
    $po_code    = cekget('po_code');
    $plu        = cekget('plu');

    $database   = include('../varLakon.php');
    $ip         = $database['host'];
    $db         = $database['db'];
    $us         = $database['user'];
    $ps         = $database['pass'];

    $conn = BukaKoneksi($us, $ps, $ip, $db);
    sqlsrv_query( $conn, "delete from t_po_detail where po_code='$po_code' and plu='$plu' " );

    $json = array("status" => "1");
	  echo json_encode($json);
?>
