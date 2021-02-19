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

        $sql  = "Select branch_id, name
                 from m_branch
                 where (name <> '' or name is not null) and
                 $vend
                 order by branch_id";
        // echo "SQL : " . $sql . "\n";
        $result = sqlsrv_query( $conn, $sql );
        if ($result) {
          $out = array();
          while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $out[] = array("branch_id" => $branch_id, "name" => $name);
            $no++;
          }
        } else {
          $out[] = array("branch_id" => "", "name" => "");
        }
        sqlsrv_close($conn);
        return $out;
    }


?>
