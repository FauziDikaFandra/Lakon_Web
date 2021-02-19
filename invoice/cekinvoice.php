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

    $user = cekget('user');
    $group = cekget('group');
    $tgl = cekget('tgl');
    $bulan = cekget('bulan');
    $tahun = cekget('tahun');
    // echo $tgl;
    $date = date_parse($bulan);
    $bulan  =  $date['month'];
    // echo $bulan;
    $sql = "select * from invoice";
    $brands = "";
    $result = sqlsrv_query( $conn, $sql );
    if($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            $datalist[]  = $row;
            $tgl_ = $row["batastgl"];
            $bulan_ = $row["batasbulan"];
            $tahun_ = $row["batastahun"];
        }
        $tanggal = date('d');
        
        // $bulan = date('m');
        // $tahun = date('Y');
        // echo $bulan."==".$bulan_;
        if($bulan <= $bulan_){
            // echo $bulan_.''.$tgl;
            if($tgl <= $tgl_){
                $json = array("hasil" => "1" );
            }else{
                $json = array("hasil" => "0" );
            }
        }else{
           $json = array("hasil" => "0" ); 
        }
    }else{
        $json = array("hasil" => "0" );
    }
    // echo $brands;
    

    echo json_encode($json);
?>
