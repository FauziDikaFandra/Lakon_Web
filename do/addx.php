<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    include_once "../../fungsi.php";
    ini_set('max_execution_time',   300);
    $username   = cekget('username');
    $id         = "";
    $postingdate= date("Y-m-d");
    //echo $postingdate . "\n";
    $database   = include('../varLakon.php');
    $ip         = $database['host'];
    $db         = $database['db'];
    $us         = $database['user'];
    $ps         = $database['pass'];
    $conn       = BukaKoneksi($us, $ps, $ip, $db);

    $sql = "select max(po_code) as po_code from t_po where month(postingdate)=month('$postingdate') and year(postingdate)=year('$postingdate')";
    $result = sqlsrv_query( $conn, $sql, array(), array( "Scrollable" => 'static' ));
    if ($result) {
        $row_count = sqlsrv_num_rows( $result );
        if ($row_count <=0 ) {
            $id   = "";
            $json = array("status" => 0, "po_code" => "");
        } else {
            $id   = "";
            $outp = array();
            while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
                extract($row);
                $x  = (int)substr($po_code, 6, 3) + 1;
                if ($x <= 9) {
                    $s = "00". $x;
                } elseif ( $x >= 10 && $x <= 99 ) {
                    $s = "0". $x;
                } else {
                    $s = $x;
                }
                $id = "PO" . substr($postingdate, 2, 2) . substr($postingdate, 5, 2) . $s;
            }
            $json = array("status" => 1, "po_code" => $id);
        }
    } else {
        $id   = "";
        $json = array("status" => 0, "po_code" => "");
    }

    if ($id<>"") {
        sqlsrv_query( $conn, "insert into t_po (po_code, status, postingdate,
                              useradded, dateadded) values ('$id', 'NEW', '$postingdate',
                              '$username', GETDATE() )" );
    }
	  echo json_encode($json);
?>
