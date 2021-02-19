<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    include_once "../fungsi.php";
    ini_set('max_execution_time',   300);
    $database    = include('varLakon.php');
    $groupname   = cekget('groupname');

    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];

    $outp0 = array();

    $outp0 = getJsonModule($ip, $db, $us, $ps);

    // $json = array("status" => "1",
    //               "modules" => $outp0);
    $json = array("modules" => $outp0);
	  echo json_encode($json);

    function getJsonModule($ip, $db, $us, $ps) {
        $no   = 1;
        $out  = array();
        $user = $GLOBALS['groupname'];
        $conn = BukaKoneksi($us, $ps, $ip, $db);

        $sql  = "Select um.groupname, um.moduleid, m.modulename, m.icon, m.link
                 from s_user_module um
                 left join s_module m on um.moduleid=m.moduleid
                 where groupname='$user' order by moduleid";
        // echo "SQL Brand : " . $sql . "\n";
        $result = sqlsrv_query( $conn, $sql );
        if ($result) {
          $out = array();
          while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $out[] = array("moduleid" => $moduleid, "modulename" => $modulename, "icon" => $icon, "link" => $link);
            $no++;
          }
        } else {
          $out[] = array("moduleid" => "", "modulename" => "", "icon" => "", "link" => "");
        }
        sqlsrv_close($conn);
        return $out;
    }


?>
