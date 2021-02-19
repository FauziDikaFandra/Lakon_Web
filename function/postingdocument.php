<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    include_once "../../fungsi.php";
    ini_set('max_execution_time',   300);
    $username   = cekget('username');
    $groupname  = cekget('groupname');
    $id         = cekget('id');
    $table      = cekget('table');
    $primary    = cekget('primary');

    $database   = include('../varLakon.php');
    $ip         = $database['host'];
    $db         = $database['db'];
    $us         = $database['user'];
    $ps         = $database['pass'];

    $outp0      = getJson($ip, $db, $us, $ps);
    $json       = array("data" => $outp0);
	  echo json_encode($json);

    function getJson($ip, $db, $us, $ps) {
        $no   = 1;
        $out  = array();
        $user = $GLOBALS['username'];
        $group= $GLOBALS['groupname'];
        $id   = $GLOBALS['id'];
        $table= $GLOBALS['table'];
        $prim = $GLOBALS['primary'];

        $conn = BukaKoneksi($us, $ps, $ip, $db);
        $sql  = "update $table set status='POST', postingdate=convert(varchar(10), getdate(), 20) where $prim='$id' ";

        // echo "SQL : " . $sql . "\n";
        sqlsrv_query( $conn, $sql );
        $out = array();
        $out[] = array("status" => "1", "sql" => $sql);
        sqlsrv_close($conn);
        return $out;
    }


?>
