<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    include_once "../../fungsi.php";
    include_once "../../lakonsecurity.php";
    ini_set('max_execution_time',   300);
    $database   = include('../varLakon.php');
    $username   = cekget('username');
    $groupname  = cekget('groupname');

    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];

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
        $gname= $GLOBALS['groupname'];
        $conn = BukaKoneksi($us, $ps, $ip, $db);
        $vend = getVendorLakon($gname, $user, "", "m_vendor");

        //$sy   = getLakonSecurity($us, $moduleid, 'im', 'dp2');

        $sql  = "Select vendor_code, name
                 from m_vendor
                 where (name <> '' or name is not null) and
                 $vend
                 order by name";
        // echo "SQL : " . $sql . "\n";
        $result = sqlsrv_query( $conn, $sql );
        if ($result) {
          $out = array();
          while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $out[] = array("vendor_code" => $vendor_code, "name" => $name);
            $no++;
          }
        } else {
          $out[] = array("vendor_code" => "", "name" => "");
        }
        sqlsrv_close($conn);
        return $out;
    }


?>
