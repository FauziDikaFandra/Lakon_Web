<?php
    header('Access-Control-Allow-Origin: *');
    include_once "../fpdf17/fpdf.php";
    include_once "../../fungsi.php";
    include_once "../../lakonsecurity.php";
    ini_set('max_execution_time',   300);
    $database    = include('../varLakon.php');
    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];

    $conn = BukaKoneksi($us, $ps, $ip, $db);
    $dt = "select * from query_rpt where page = 3";
    $result = sqlsrv_query( $conn, $dt );
    if($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            $sql_ = $row['query'];
        }
    }
    if(isset($sql_)){
        $sql =  str_replace("#","'",$sql_); 
    }else{
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
    $result1 = sqlsrv_query( $conn,$sql );
    //==============Hasil Query Sales========
    $datalist = [];
    $brand = "";
    $tgl1 = "";
    $po = "";
    $do = "";
    $brand = "";
    $vendor = "";
    $podate = "";
    $suppcode = "";
    if ($result1)
    {
        while( $row = sqlsrv_fetch_array( $result1, SQLSRV_FETCH_ASSOC) ) {
            $datalist[]  = $row;
            $vendor = $row["name"];
            $tgl1   = $row["postdate"];
            $po     = $row["po_code"];
            $do     = $row["do_code"];
            $brand  = $row["brand"];
            $suppcode = $row["vendor_code"];
            $podate = $row["podate"];
        }
    }
    $length = count($datalist);
    $pdf = new FPDF('P','mm',array(210,297)); //L For Landscape / P For Portrait
    $pdf->AddPage();
    $pdf->SetX(5);
    $pdf->SetFont('Arial','B',13);
    $pdf->Image('../lakon.png',6,10,-200);
    // $pdf->Cell(40,8,$vendor);
    $pdf->Ln(3);
    $pdf->SetFont('Arial','B',10);
    $pdf->SetX(5);
    $pdf->Cell(40,5,"");
    $pdf->Ln();
$Y_Fields_Name_position_heade = 10;   
$pdf->SetFont('Arial','B',9.5);
$pdf->SetX(5);
$pdf->Cell(40,5,$vendor,5);
$pdf->SetX(85);
$pdf->SetFont('Arial','BU',9.5);
$pdf->Cell(40,5,'DELIVERY ORDER',5);
$pdf->SetX(145);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(35,5,'Deliver To',13);
$pdf->Ln(4);
$pdf->SetX(5);
$pdf->Cell(30,5,$suppcode,5);
$pdf->SetX(90);
$pdf->Cell(30,5,$do,5);

$pdf->Ln(1);
$pdf->SetX(145);
$pdf->SetFont('Arial','',8);
$pdf->Cell(40,4,'MAL KELAPA GADING 5',5);
$pdf->Ln();
$pdf->SetX(5);
$pdf->SetX(145);
$pdf->SetFont('Arial','',8);
$pdf->Cell(40,4,'Lt.1 Unit 1F-45 Sentra Kelapa Gading,',5);
$pdf->Ln();
$pdf->SetX(145);
$pdf->Cell(40,4,'Jl. Bulevar Kelapa Gading Blok M',5);

// $pdf->Ln();
$pdf->SetX(145);
$pdf->SetX(5);
$pdf->Cell(40,3,'PO Number',5);
$pdf->SetX(30);
$pdf->Cell(40,3,':  '.$po,5);
$pdf->Ln();
$pdf->SetX(5);
$pdf->Cell(40,5,'Document Date ',5);
$pdf->SetX(30);
$pdf->Cell(40,5,':  '.$tgl1,5);
$pdf->SetX(145);
// $pdf->Cell(40,5,'Jakarta 14240, Indonesia',5);
$Y_Fields_Name_position = 40;

$pdf->SetFillColor(110,180,230);
$pdf->SetFont('Arial','',8);
$pdf->SetY($Y_Fields_Name_position);
$pdf->SetX(5);
$pdf->Cell(9,6,'NO',1,0,'C',1);
$pdf->SetX(14);
$pdf->Cell(25,6,'EAN/PLU',1,0,'C',1);
$pdf->SetX(39);
$pdf->Cell(25,6,'Product No',1,0,'C',1);
$pdf->SetX(64);
$pdf->Cell(20,6,'Article',1,0,'C',1);
$pdf->SetX(84);
$pdf->Cell(70,6,'Description',1,0,'C',1);
$pdf->SetX(154);
$pdf->Cell(18,6,'Price',1,0,'C',1);
$pdf->SetX(172);
$pdf->Cell(13,6,'QTY',1,0,'C',1);
$pdf->SetX(185.0);
$pdf->Cell(14,6,'Note',1,0,'C',1);
$pdf->Ln();

$qtyall = "0";
$nm = 1;
foreach($datalist as $data){
    $qtyall = $qtyall + $data["qty"];
    $pdf->SetX(8);
    $pdf->Cell(15,4,$nm,5);
    $pdf->SetX(15);
    $pdf->Cell(35,4,$data["plu"],5);
    $pdf->SetX(40);
    $pdf->Cell(20,4,$data["substitute"],5);
    $pdf->SetX(66);
    $pdf->Cell(35,4,$data["article_code"],5);
    $pdf->SetX(85);
    $pdf->Cell(20,4,$data["long_description"],5);
    $pdf->SetX(158);
    $pdf->Cell(35,4,rupiah($data["Current_Price"]),5);
    $pdf->SetX(177);
    $pdf->Cell(35,4,$data["qty"],5);
$pdf->Ln();
$nm = $nm + 1;
}
    $pdf->SetFillColor(110,180,230);
    $pdf->SetX(5);
    $pdf->Cell(194,6,"    TOTAL",0,0,'',1);
    $pdf->SetX(176.5);
    $pdf->Cell(160,6,$qtyall,5);
    $qtybrand = "0";
    $pdf->Ln();
    $pdf->Ln();
    $pdf->SetX(10);
    $pdf->Cell(194,8,"Supplier,",0);
    $pdf->SetX(155);
    $pdf->Cell(194,8,"LAKON STORE",0);
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
    $pdf->SetX(10);
    $pdf->Cell(194,8,$vendor,0);
$pdf->Output();
$result = sqlsrv_query( $conn, "delete from query_rpt where page = 3" );

?>