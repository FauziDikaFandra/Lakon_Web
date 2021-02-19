<?php
    // return [
    //     'host' => '192.168.6.10',
    //     'db'   => 'POS_SERVER',
    //     'user' => 'sass',
    //     'pass' => 'star'
    // ];


    $serverName = "210.211.16.106"; //serverName\instanceName
    $connectionInfo = array( "Database"=>"HRD", "UID"=>"sa", "PWD"=>"star123456");
    $conn = sqlsrv_connect( $serverName, $connectionInfo);
    if( $conn ) {
        echo "Connection established.<br />";
    }else{
        echo "Connection could not be established.<br />";
        die( print_r( sqlsrv_errors(), true));
    }
    
    $q = "select top 1 * from TBL_PARAM";
    $result = sqlsrv_query( $conn, $q );
    if($result){
         while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
             echo $row["paramname"];
         }
    }
    
    echo "ssss";
    // echo "ssss";


?>