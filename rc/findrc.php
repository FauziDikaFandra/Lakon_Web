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

    $outp0 = array();

    $outp0 = getJson($ip, $db, $us, $ps);

    // $json = array("status" => "1",
    //               "modules" => $outp0);
    $json = array("data" => $outp0);
    // $json = array("data" => $outp0);
	  echo json_encode($json);

    function getJson($ip, $db, $us, $ps) {
        $no   = 1;
        $out  = array();
        $user = $GLOBALS['username'];
        $id   = $GLOBALS['id'];

        $conn = BukaKoneksi($us, $ps, $ip, $db);
        $sql  = "Select branch_id, branchname, rc_code, po_code, do_code, status, convert(varchar(10), postingdate, 20) as postingdate,
                 convert(varchar(10), receiptdate, 20) as receiptdate,
                 convert(varchar(10), documentdate, 20) as documentdate,
                 vendor_code, vendorname, remarks
                 from t_rc
                 where rc_code='$id'";
        // echo "SQL : " . $sql . "\n";
        $result = sqlsrv_query( $conn, $sql );
        if ($result) {
          $out = array();
          while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $out[] = array("branch_id" => $branch_id, "branchname" => $branchname,
                           "rc_code" => $rc_code, "po_code" => $po_code, "do_code" => $do_code,
                           "status" => $status, "postingdate" => $postingdate,
                           "receiptdate" => $receiptdate, "documentdate" => $documentdate,
                           "vendor_code" => $vendor_code, "vendorname" => $vendorname,
                           "remarks" => $remarks);
            $no++;
          }
        } else {
          $out[] = array("branch_id" => "", "branchname" => "", 
                         "rc_code" => "", "po_code" => "", "do_code" => "",
                         "status" => "", "postingdate" => "",
                         "receiptdate" => "", "documentdate" => "",
                         "vendor_code" => "", "vendorname" => "",
                         "remarks" => "");
        }
        // echo $sql;
        sqlsrv_close($conn);
        return $out;
    }


?>
