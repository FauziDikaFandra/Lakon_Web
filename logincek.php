<?php
header('Access-Control-Allow-Origin: *');
include_once "../fungsi.php";
include_once "../lakonsecurity.php";
    ini_set('max_execution_time',   300);
    $database    = include('varLakon.php');
    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];
    $conn = BukaKoneksi($us, $ps, $ip, $db);

    $user = cekget('user');
    $pass = cekget('pass');
    $group = cekget('group');
    if($group == "2"){
        $sql = "select * from m_vendor where vendor_code = '$user' and password  = '$pass'";
    }else{
        $sql   = "select * from s_user where username = '$user' and password = '$pass'";
    }
    $brands = "";
    $result = sqlsrv_query( $conn, $sql );
    if($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            $datalist[]  = $row;
            if($group == "2"){
                $brands = $row["vendor_code"];
            }else{
                $brands = $row["username"];
            }
        }

        if( $brands == $user){
           $json = array("hasil" => "1" );
        }else{
           $json= array("hasil" => "0" );
        }
    }else{
        $json = array("hasil" => "2" );
    }
    // echo $brands;
    echo json_encode($json);
?>
