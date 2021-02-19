<?php
    header("Access-Control-Allow-Origin: *");
    // header("Content-Type: application/json; charset=UTF-8");
    include_once "../fungsi.php";
    ini_set('max_execution_time',   300);

    $groupname   = cekget('groupname');
    $moduleid    = cekget('moduleid');
    $username    = cekget('username');

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
        $gname= $GLOBALS['groupname'];
        $user = $GLOBALS['username'];
        $mdid = $GLOBALS['moduleid'];
        $conn = BukaKoneksi($us, $ps, $ip, $db);

        $sql  = "declare @mn as varchar(max)
                select @mn=menuname from s_user_module where groupname=$gname and moduleid=$mdid
                SELECT * FROM s_menu
                where menuname in ( select * from dbo.F_SplitAsTable( @mn ) )
                and moduleid=$mdid and isactive=1
                order by _order;";
        // echo "SQL Brand : " . $sql . "\n";
        // echo $sql;
        $result = sqlsrv_query( $conn, $sql );
        if ($result) {
          $out = array();
          while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $out[] = array("menuid" => (int)$menuid, "menuname" => $menuname, "icon" => $icon);
            $no++;
          }
        } else {
          $out[] = array("menuid" => 0, "menuname" => "", "icon" => "");
        }
        sqlsrv_close($conn);
        return $out;
    }


?>
