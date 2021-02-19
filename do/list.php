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
    $podo       = cekget('podo');
    $brand      = cekget('brand');

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
	  echo json_encode($json);

    function getJson($ip, $db, $us, $ps) {
        $no   = 0;
        $out  = array();
        $user = $GLOBALS['username'];
        $sdate= $GLOBALS['startdate'];
        $edate= $GLOBALS['enddate'];
        $gname= $GLOBALS['groupname'];
        $podo = $GLOBALS['podo'];
        $brand= $GLOBALS['brand'];
        $conn = BukaKoneksi($us, $ps, $ip, $db);
        $filter_ = "";
        $tops    = "";
        if ($gname=="2") {
            if($edate == "2050-12-31"){
              $tops = "top 10 ";
            }
            
            $filter = "";
            $status = " ( (0=0) and a.status not in ('NEW') ) ";
            $vend = getVendorLakon($gname, $user, "", "t_do");
            $vend = str_replace("(", "(a.", $vend);
        } elseif($gname== "1") {
            $filter = "and b.useradded = '$user'";
            $status = " (a.status in ('POST', 'CLOSE') and a.status not in ('NEW') ) ";
            $vend = "(0=0)";
        }else{
            $filter = "";
            $vend = "(0=0)";
            $status = " (a.status in ('POST', 'CLOSE') and a.status not in ('NEW') ) ";
            
            if($podo == "undefined"){
              $podo = "";
            }
            if($brand == "undefined"){
              $brand = "";
            }


            if($podo == "" && $brand <> ""){
              $filter_ = " and a.vendorname like '%$brand%'";
            }elseif($podo <> "" && $brand == ""){
              $filter_ = " and a.po_code = '$podo'";
            }elseif($podo <> "" && $brand <> ""){
              $filter_ = " and a.po_code = '$podo'  and a.vendorname like '%$brand%'";
            }else{
              $filter_ = "";
            }
        }
        $sql  = "Select $tops m.name as branch_id, a.branchname, do_code, a.po_code, a.status, convert(varchar(10), a.postingdate, 20) as postingdate,
                 a.vendor_code, a.vendorname, a.remarks
                 from t_do a
                 inner join t_po b on a.po_code = b.po_code
                 inner join m_branch m on m.branch_id = b.branch_id
                 where a.status <> 'BLOCK' and a.postingdate between '$sdate' and '$edate' and
                 $vend and $status $filter $filter_
                 order by postingdate desc";
        // echo "SQL : " . $sql . "\n";
        $result = sqlsrv_query( $conn, $sql );
        if ($result) {
          $out = array();
          while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            $no++;
            extract($row);
            $out[] = array("branch_id" => $branch_id, "branchname" => $branchname,
                           "do_code" => $do_code, "po_code" => $po_code, "status" => $status,
                           "postingdate" => $postingdate, "vendor_code" => $vendor_code,
                           "vendorname" => $vendorname, "remarks" => $remarks);
          }
        } else {
          $out[] = array("branch_id" => "", "branchname" => "",
                         "do_code" => "", "po_code" => "", "status" => "",
                         "postingdate" => "", "vendor_code" => "",
                         "vendorname" => "", "remarks" => "");
        }
        // echo $sql;
        sqlsrv_close($conn);
        $GLOBALS['totalrc'] = $no;
        return $out;
    }


?>
