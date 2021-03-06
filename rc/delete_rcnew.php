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

    $sql        = "select po_code, do_code from t_rc where rc_code='$id' ";
    $po_code    = "";
    $do_code    = "";
    $result     = sqlsrv_query( $conn, $sql );
    if ($result) {
      while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_NUMERIC) ) {
        // $out = array("status" => 1, "value" => (int)$row[0], "sql" => $sql);
        $po_code    = $row[0];
        $do_code    = $row[1];
      }
    } else {
        $po_code    = "";
        $do_code    = "";
    }

    sqlsrv_query( $conn, "delete from t_rc_detail where rc_code in (select rc_code from t_rc where rc_code='$id' and status='NEW') " );
    sqlsrv_query( $conn, "delete from t_rc        where rc_code='$id' and status='NEW'" );

    if ($po_code != "") {
      $sql    = "exec dbo.fillQTYPO '$po_code'";
      $result = sqlsrv_query( $conn, $sql );
    }
    if ($do_code != "") {
      $sql    = "exec dbo.fillStatusDO '$do_code'";
      $result = sqlsrv_query( $conn, $sql );
    }
    $json = array("status" => "1");
	  echo json_encode($json);
?>
