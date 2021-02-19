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
    $user   = cekget('user');
    $date1  = cekget('date1');
    $date2  = cekget('date2');
    $group  = cekget('group');
    if(!empty($user)){
        $sql1  = "delete from report_param";
        $result1 = sqlsrv_query( $conn, $sql1 );
        if ($result1){
            $sql  = "insert into report_param select '$user', '$date1','$date2', getdate(),$group";
            $result = sqlsrv_query( $conn, $sql );
            if($result){
                $json = array("status" => 1, "respone" => "oke");
            }else{
                 $json = array("status" => 0, "respone" => "Error");
            }
            
        }else{
            $json = array("status" => 0, "respone" => "Error");
        }
    }
    // echo $sql;
    echo json_encode($json);

?>