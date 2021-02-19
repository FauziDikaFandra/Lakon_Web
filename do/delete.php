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

    $sql        = "select po_code from t_do where do_code='$id' ";
    $po_code    = "";
    $result     = sqlsrv_query( $conn, $sql );
    if ($result) {
      while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_NUMERIC) ) {
        // $out = array("status" => 1, "value" => (int)$row[0], "sql" => $sql);
        $po_code    = $row[0];
      }
    } else {
        $po_code    = "";
    }

    sqlsrv_query( $conn, "delete from t_do_detail where do_code='$id'" );
    sqlsrv_query( $conn, "delete from t_do        where do_code='$id'" );

    if ($po_code != "") {
      $sql    = "exec dbo.fillQTYPO '$po_code'";
      $result = sqlsrv_query( $conn, $sql );
    }

    $json = array("status" => "1");
	  echo json_encode($json);
?>
