<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    include_once "../../fungsi.php";
    ini_set('max_execution_time',   300);
    $username   = cekget('username');
    $groupname  = cekget('groupname');
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
        $sql  = "Select ltrim(rtrim(plu)) as plu,b.branch_id
                 from t_rc_detail a
                 inner join t_rc	b on a.rc_code = b.rc_code
                 where a.rc_code='$id'
                 order by plu";
        // echo "SQL : " . $sql . "\n";
        $result = sqlsrv_query( $conn, $sql );
        if ($result) {
          $out = array();
          while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $out[] = array("plu" => $plu,"branch_id" => $branch_id);
            $no++;
          }
        } else {
          $out[] = array("plu" => "","branch_id" => "");
        }
        sqlsrv_close($conn);
        return $out;
    }


?>
