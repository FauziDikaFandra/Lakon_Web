<?php
   // $conn = BukaKoneksi($us, $ps, $ip, $db);
    $serverName = "210.211.16.106"; //serverName\instanceName
    $connectionInfo = array( "Database"=>"HRD", "UID"=>"sa", "PWD"=>"star123456");
    $conn = sqlsrv_connect( $serverName, $connectionInfo);
    if( $conn ) {
        echo "Connection established.<br />";
    }else{
        echo "Connection could not be established.<br />";
        die( print_r( sqlsrv_errors(), true));
    }
?>