<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    include_once "../../fungsi.php";
    include_once "../../lakonsecurity.php";
    ini_set('max_execution_time',   300);
    $username   = cekget('username');
    $groupname  = cekget('groupname');
    $startdate  = cekget('startdate');
    $enddate    = cekget('enddate');

    if ($startdate=="") {
      $startdate = date("Y-m-d");
    }
    if ($enddate=="") {
      $enddate = date("Y-m-d");
    }
    // $startdate = "2018-02-01";
    // $enddate   = "2018-03-31";

    $database   = include('../varLakon.php');
    $ip         = $database['host'];
    $db         = $database['db'];
    $us         = $database['user'];
    $ps         = $database['pass'];

    $totalrc  = 0;
    $outp0 = array();
    $outp0 = getJson($ip, $db, $us, $ps);



    $json     = array("draw" => 0, "recordsTotal" => $totalrc, "recordsFiltered" => $totalrc, "data" => $outp0);
    // $json = array("data" => $outp0);
	  echo json_encode($json);

    function getJson($ip, $db, $us, $ps) {
        $no   = 0;
        $out  = array();
        $user = $GLOBALS['username'];
        $sdate= $GLOBALS['startdate'];
        $edate= $GLOBALS['enddate'];
        $gname= $GLOBALS['groupname'];
        $conn = BukaKoneksi($us, $ps, $ip, $db);
        $vend = getVendorLakon($gname, $user, "", "t_do");

        if ($gname=="1" || $gname=="3" ) {
            $status = " ( (0=0) and status not in ('NEW') ) ";
        } else {
            $status = " (status in ('POST', 'CLOSE') and status not in ('NEW') ) ";
        };
        // switch use gr per branch
        if($user == "dewi"){
          $sql  = "Select branch_id, branchname, rc_code, po_code, do_code, status, convert(varchar(10), postingdate, 20) as postingdate,
                  vendor_code, vendorname, remarks,useradded as users
                  from t_rc
                  where status <> 'BLOCK' and branch_id = 'S011' and postingdate between '$sdate' and '$edate' and
                  $vend and $status
                  order by rc_code desc";
        }else{
          $sql  = "Select branch_id, branchname, rc_code, po_code, do_code, status, convert(varchar(10), postingdate, 20) as postingdate,
                 vendor_code, vendorname, remarks,useradded as users
                 from t_rc
                 where status <> 'BLOCK' and branch_id in ('S011','S012','S014','S013','S021','S022','S023','S024') and postingdate between '$sdate' and '$edate' and
                 $vend and $status
                 order by rc_code desc";
        }
        

        $result = sqlsrv_query( $conn, $sql );
        if ($result) {
          $out = array();
          while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            $no++;
            extract($row);
            $out[] = array("branch_id" => $branch_id, "branchname" => $branchname,
                           "rc_code" => $rc_code, "po_code" => $po_code, "do_code" => $do_code, "status" => $status,
                           "postingdate" => $postingdate, "vendor_code" => $vendor_code,
                           "vendorname" => $vendorname, "remarks" => $remarks, "users" => $users);
          }
        } else {
          $out[] = array("branch_id" => "", "branchname" => "",
                         "rc_code" => "", "po_code" => "", "do_code" => "", "status" => "",
                         "postingdate" => "", "vendor_code" => "",
                         "vendorname" => "", "remarks" => "");
        }
        // echo $sql;
        sqlsrv_close($conn);
        $GLOBALS['totalrc'] = $no;
        return $out;
    }


?>
