<?php
    header('Access-Control-Allow-Origin: *');
    // header("Content-Type': 'application/x-www-form-urlencoded");
    //$postdata = file_get_contents("php://input");

    // $request = json_decode($postdata);
    // $email = $request->email;
    // $pass = $request->pass;
    // echo $email; //this will go back under "data" of

    // header("Access-Control-Allow-Origin: *");
    // header("Content-Type: application/json; charset=UTF-8");
    include_once "../../fungsi.php";
    ini_set('max_execution_time',   300);
    $database   = include('../varLakon.php');
    $ip         = $database['host'];
    $db         = $database['db'];
    $us         = $database['user'];
    $ps         = $database['pass'];

    $conn = BukaKoneksi($us, $ps, $ip, $db);
    $postdata     = file_get_contents("php://input");

    $request      = json_decode($postdata);
    $vendor_code  = trim(addslashes($request->vendor_code));
    $vendorname   = trim(addslashes($request->vendorname));
    // $vendorname   = trim($request->vendorname);
    $branch_id    = trim(addslashes($request->branch_id));
    $branchname   = trim(addslashes($request->branchname));
    $po_code      = trim(addslashes($request->po_code));
    $remarks      = trim(addslashes($request->remarks));
    $status       = trim(addslashes($request->status));
    $postingdate  = trim(addslashes($request->postingdate));
    $documentdate = trim(addslashes($request->documentdate));
    $deliverydate = trim(addslashes($request->deliverydate));
    $username     = trim(addslashes($request->username));
    $action       = strtolower(trim(addslashes($request->action)));

    if (strtolower($status)=="new") {
      $status = "OPEN";
    }
    if ( !empty($po_code) ){
          // $sql = "select name from m_vendor where vendor_code='$vendor_code' ";
          // $result = sqlsrv_query( $conn, $sql, array(), array( "Scrollable" => 'static' ));
          // if ($result) {
          //     $row_count = sqlsrv_num_rows( $result );
          //     if ($row_count <=0 ) {
          //         $vendorname = "";
          //     } else {
          //         $vendorname = "";
          //         $outp = array();
          //         while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
          //             extract($row);
          //             $vendorname = $name;
          //         }
          //     }
          // } else {
          //     $vendorname = "";
          // }
          $vendorname   = str_replace("\'", "''", $vendorname);
          // $vendorname   = str_replace("\", "", $vendorname);
          $sql          = "update t_po set status='$status',
                           branch_id='$branch_id',
                           branchname='$branchname',
                           postingdate='$postingdate',
                           deliverydate='$deliverydate',
                           documentdate='$documentdate',
                           vendor_code='$vendor_code',
                           vendorname='$vendorname',
                           remarks='$remarks',
                           useredited='$username',
                           dateedited=GETDATE()
                           where po_code='$po_code' ";
          sqlsrv_query($conn, $sql);
        $json = array("status" => 1, "data" => $po_code, "sql" => $sql);
    } else {
        $json = array("status" => 0,"data" => "", "sql" => "");
    }

    echo json_encode($json);
?>
