<?php
header('Access-Control-Allow-Origin: *');
include_once "../../fungsi.php";
include_once "../../lakonsecurity.php";
    ini_set('max_execution_time',   300);
    $database    = include('../varLakon.php');
    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];

    
    $conn = BukaKoneksi($us, $ps, $ip, $db);
    $do = cekget('do');
    // $sql = "select a.do_code , case when b.do_code is null then 'gakada' else 'ada' end status from t_do a
    //         left join t_rc b on a.do_code = b.do_code where a.do_code = '$do'";

    $sql = "select do_code, 
            case when status = 'POST' then 'ada' when status = 'close' then 'ada' else 'gakada' end status  
            from t_do where do_code = '$do'";

    $datas = array();
    $result = sqlsrv_query( $conn, $sql );
    if ($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $datas[] = array("do" => $do_code, "status" => $status);
        }
        $json = array("status" => 1, "respone" => $datas);
    }else{
        $json = array("status" => 0, "respone" => "Error");
    }
    // echo $sql;
    echo json_encode($json);

?>