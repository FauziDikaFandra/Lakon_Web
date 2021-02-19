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
    $table2     = cekget('table2');
    $primary2   = cekget('primary2');

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

        $table2= $GLOBALS['table2'];
        $prim2 = $GLOBALS['primary2'];

        $conn = BukaKoneksi($us, $ps, $ip, $db);
        $out = array();
        if ($table2=="") {
          $sql = "select top 1 po_code from t_po where 0=1";  
        } else {
          $sql = "select top 1 $prim2 from $table2 where $prim='$id' order by $prim2 desc ";
        }
        $result = sqlsrv_query( $conn, $sql, array(), array( "Scrollable" => 'static' ));
        if ($result) {
            $row_count = sqlsrv_num_rows( $result );
            if ($row_count <=0 ) {
                $out[] = array("status" => "1", "pesan" => "");
                $sql  = "update $table set status='OPEN' where $prim='$id' ";
                sqlsrv_query( $conn, $sql );
            } else {
                while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
                    extract($row);
                    $out[] = array("status" => "0", "pesan" => "TIDAK BISA CANCEL POSTING, Sudah Dilanjutkan ke Dokumen Lain");
                }
            }
        } else {
            $out[] = array("status" => "0", "pesan" => "ERROR CANCEL POSTING");
        }
        sqlsrv_close($conn);

        return $out;
    }


?>
