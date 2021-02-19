<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    include_once "../../fungsi.php";
    ini_set('max_execution_time',   300);
    //$sql        = cekget('sql');
    $sql        = $_GET['sql'];

    $database   = include('../varLakon.php');
    $ip         = $database['host'];
    $db         = $database['db'];
    $us         = $database['user'];
    $ps         = $database['pass'];

    // echo $sql . "\n";
    $out        = array();
    $conn       = BukaKoneksi($us, $ps, $ip, $db);
    $result     = sqlsrv_query( $conn, $sql );
    $out = array("status" => 0, "value" => -2, "sql" => "");
    if ($result) {
      while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_NUMERIC) ) {
        $out = array("status" => 1, "value" => $row[0], "sql" => $sql);
      }
    } else {
        $out = array("status" => 0, "value" => -1, "sql" => "");
    }
    sqlsrv_close($conn);
	  echo json_encode($out);
?>
