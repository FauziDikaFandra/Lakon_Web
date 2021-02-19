<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    include_once "../fungsi.php";
    ini_set('max_execution_time',   300);

    $groupname   = cekget('groupname');
    $moduleid    = cekget('moduleid');

    $database    = include('varLakon.php');
    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];

    $outp0 = array();

    $outp0 = getJsonModule($ip, $db, $us, $ps);

    // $json = array("status" => "1",
    //               "modules" => $outp0);
    $json = array("data" => $outp0);
	  echo json_encode($json);

    function getJsonModule($ip, $db, $us, $ps) {
        $no   = 1;
        $out  = array();
        $user = $GLOBALS['groupname'];
        $mdid = $GLOBALS['moduleid'];
        $conn = BukaKoneksi($us, $ps, $ip, $db);

        $sql  = "Select *
                 from s_user_module
                 where groupname='$user' and moduleid='$mdid' ";
        // echo "SQL Brand : " . $sql . "\n";
        $result = sqlsrv_query( $conn, $sql );
        if ($result) {
          $out = array();
          while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $out[] = array("groupname" => $groupname, "moduleid" => $moduleid,
                           "isadd" => $isadd, "isedit" => $isedit, "isdelete" => $isdelete,
                           "isprint" => $isprint, "isdetail" => $isdetail);
            $no++;
          }
        } else {
          $out[] = array("groupname" => 0, "moduleid" => 0,
                         "isadd" => 0, "isedit" => 0, "isdelete" => 0,
                         "isprint" => 0, "isdetail" => 0);
        }
        sqlsrv_close($conn);
        return $out;
    }


?>
