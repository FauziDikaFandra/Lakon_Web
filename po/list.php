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

    // $json = array("status" => "1",
    //               "modules" => $outp0);

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
        $vend = getVendorLakon($gname, $user, "", "t_po");

        $top = " ";
        if ($gname=="2") {
            if ($edate == "2050-12-31"){
              $top = "top 10 ";
            }
            
            $status = " (status in ('POST', 'CLOSE') and status not in ('NEW') ) ";
            $filter = "";
        } elseif ($gname == "1") {
            $status = " ( (0=0) and status not in ('NEW') ) ";
            $filter = " ";
        }else{
          $filter = "";
          $status = " ( (0=0) and status not in ('NEW') ) ";
        }
        $sql  = "Select $top m.name as branch_id, m.name as branchname, a.po_code, status, convert(varchar(10), postingdate, 20) as postingdate,
                 vendorname, remarks, sum(qty) as grandtotal
                 from t_po a left join t_po_detail b on a.po_code = b.po_code
                 inner join m_branch m on m.branch_id = a.branch_id
                 where a.status <> 'BLOCK' and postingdate between '$sdate' and '$edate' and
                 $vend and $status $filter
                 group by a.branch_id, m.name, a.po_code, status, 
                 convert(varchar(10), postingdate, 20),
                 vendorname, remarks,a.dateadded order by a.dateadded desc";
        // echo "SQL : " . $sql . "\n";
        $result = sqlsrv_query( $conn, $sql );
        if ($result) {
          $out = array();
          while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            $no++;
            extract($row);
            $out[] = array("branch_id" => $branch_id, "branchname" => $branchname,
                           "po_code" => $po_code, "status" => $status, "postingdate" => $postingdate,
                           "vendorname" => $vendorname, "remarks" => $remarks, "grandtotal" => (int)$grandtotal);
          }
        } else {
          $out[] = array("branch_id" => "", "branchname" => "",
                         "po_code" => "", "status" => "", "postingdate" => "",
                         "vendorname" => "", "remarks" => "", "grandtotal" => 0);
        }
        sqlsrv_close($conn);
        $GLOBALS['totalrc'] = $no;

        // echo $sql;
        return $out;
    }


?>
