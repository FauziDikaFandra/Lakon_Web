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
    $branch_id    = trim(addslashes($request->branch_id));
    $branchname   = trim(addslashes($request->branchname));
    $rc_code      = trim(addslashes($request->rc_code));
    $po_code      = trim(addslashes($request->po_code));
    $do_code      = trim(addslashes($request->do_code));
    $status       = trim(addslashes($request->status));
    $postingdate  = trim(addslashes($request->postingdate));
    $receiptdate  = trim(addslashes($request->receiptdate));
    $documentdate = trim(addslashes($request->documentdate));
    $vendor_code  = trim(addslashes($request->vendor_code));
    $vendorname   = trim(addslashes($request->vendorname));
    $remarks      = trim(addslashes($request->remarks));

    $username     = trim(addslashes($request->username));
    $action       = strtolower(trim(addslashes($request->action)));

    if (strtolower($status)=="new") {
      $status = "OPEN";
    }
    if ( !empty($do_code) ){
          $vendorname   = str_replace("\'", "''", $vendorname);
          $sql          = "update t_rc set
                           branch_id='$branch_id',
                           branchname='$branchname',
                           po_code='$po_code',
                           do_code='$do_code',
                           status='$status',
                           postingdate='$postingdate',
                           receiptdate='$receiptdate',
                           documentdate='$documentdate',
                           vendor_code='$vendor_code',
                           vendorname='$vendorname',
                           remarks='$remarks',
                           useredited='$username',
                           dateedited=GETDATE()
                           where rc_code='$rc_code' ";
          sqlsrv_query($conn, $sql);
        $json = array("status" => 1, "data" => $rc_code, "sql" => $sql);
    } else {
        $json = array("status" => 0,"data" => "", "sql" => "");
    }

    echo json_encode($json);
?>
