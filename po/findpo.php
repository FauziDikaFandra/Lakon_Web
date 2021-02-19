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
        $sql  = "Select a.branch_id, b.name as  branchname, po_code, status, convert(varchar(10), postingdate, 20) as postingdate,
                 convert(varchar(10), documentdate, 20) as documentdate,
                 convert(varchar(10), deliverydate, 20) as deliverydate,
                 vendor_code, vendorname, remarks, grandtotal
                 from t_po a
                 inner join m_branch b on a.branch_id = b.branch_id
                 where po_code='$id'";
        // echo "SQL : " . $sql . "\n";
        $result = sqlsrv_query( $conn, $sql );
        if ($result) {
          $out = array();
          while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $out[] = array("branch_id" => $branch_id, "branchname" => $branchname,
                           "po_code" => $po_code, "status" => $status, "postingdate" => $postingdate,
                           "documentdate" => $documentdate, "deliverydate" => $deliverydate,
                           "vendor_code" => $vendor_code, "vendorname" => $vendorname,
                           "remarks" => $remarks, "grandtotal" => $grandtotal);
            $no++;
          }
        } else {
          $out[] = array("branch_id" => $branch_id, "branchname" => $branchname,
                         "po_code" => "", "status" => "", "postingdate" => "",
                         "documentdate" => "", "deliverydate" => "",
                         "vendor_code" => "", "vendorname" => "",
                         "remarks" => "", "grandtotal" => 0);
        }
        sqlsrv_close($conn);
        return $out;
    }


?>
