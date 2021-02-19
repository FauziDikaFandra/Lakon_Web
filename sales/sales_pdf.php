<?php
header('Access-Control-Allow-Origin: *');
include_once "../../fungsi.php";
include_once "../../lakonsecurity.php";
include_once "../fpdf17/fpdf.php";
ini_set('max_execution_time',   300);
    $database    = include('../varLakon.php');
    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];
    
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(80);
    
    $pdf->Cell(30,10,'LAKON SALES REPORT',0,0,'C');
    $pdf->Ln();
    $pdf->Cell(80);
    $pdf->Cell(30,10,'PT. Harfa Sejahtera | www.harfastore.com',0,0,'C');
    $pdf->Ln();
    $pdf->Output();
    // $conn = BukaKoneksi($us, $ps, $ip, $db);
    // $dt = "select * from query_rpt where page = 3";
    // $sql_ = "";
    // $sql = "";
    // $datas = array();
    // $brands = "";
    // $result = sqlsrv_query( $conn, $dt );
    // if($result){
    //     while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
    //         $sql_ = $row['query'];
    //     }
    // }
    // // echo str_replace("#","'",$sql_); 
    // $sql =  str_replace("#","'",$sql_); 
    // $result1 = sqlsrv_query( $conn,$sql );
    // if ($result1){
    //     while( $row = sqlsrv_fetch_array( $result1, SQLSRV_FETCH_ASSOC) ) {
            // $pdf = new FPDF('P','mm',array(210,297)); //L For Landscape / P For Portrait
            // $pdf->AddPage();

            //Menambahkan Gambar
            //$pdf->Image('../foto/logo.png',10,10,-175);

            // $pdf->SetFont('Arial','B',13);
            // $pdf->Cell(80);
            //     $pdf->Cell(30,10,'DATA PERSEDIAAN BARANG',0,0,'C');
            // $pdf->Ln();
            // $pdf->Cell(80);
            // $pdf->Cell(30,10,'PT. Harfa Sejahtera | www.harfastore.com',0,0,'C');
            // $pdf->Ln();
            // echo $row['item_description'];
    //     }
    // }
?>


        
