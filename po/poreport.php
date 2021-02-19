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
    $dt = "select * from query_rpt where page = 2";
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
    $vendor = "";
    $vendorcode = "";
    $users = "";
    if ($result1)
    {
        while( $row = sqlsrv_fetch_array( $result1, SQLSRV_FETCH_ASSOC) ) {
            $datalist[]  = $row;
            $vendor = $row["name"];
            $tgl1   = $row["postingdate"];
            $po     = $row["po_code"];
            $brand     = $row["brand"];
            $users  = $row["dibuat"];
            $vendorcode  = $row["vendor_code"];
        }
    }
    $length = count($datalist);
    $pdf = new FPDF('P','mm',array(210,297)); //L For Landscape / P For Portrait
    $pdf->AddPage();
    $pdf->SetX(5);
    // $pdf->SetFont('Arial','B',13);
    // $pdf->Cell(40,8,$vendor);
    $pdf->Image('../lakon.png',6,10,-200);
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->SetX(5);
    $pdf->Cell(40,8,"");
    $pdf->Ln();
$Y_Fields_Name_position_heade = 10;   
// $pdf->SetFont('Arial','B',9);
// $pdf->SetX(5);
// $pdf->Cell(40,2,'Purchase Order  '.$po,5);
// $pdf->SetX(25);
// $pdf->Ln();

$pdf->SetX(5);
// $pdf->SetFont('Arial','',11);
$pdf->Cell(40,5,'Jl. Perintis Kemerdekaan No.42',5);
$pdf->Ln(2.5);
$pdf->SetX(5);
$pdf->Cell(35,9,'Pulomas, Jakarta Timur',3);
$pdf->SetX(80);
$pdf->SetFont('Arial','BU',9.5);
$pdf->SetFont('Arial','BU',9.5);
$pdf->Cell(40,2,'PURCHASE ORDER',5);
$pdf->SetX(145);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(40,5,'Deliver To',5);
$pdf->Ln(5);
$pdf->SetX(5);
$pdf->SetX(86.5);
$pdf->Cell(20,2,$po,2);
// $pdf->SetFont('Arial','B',9);
$pdf->SetFont('Arial','',10);
$pdf->SetX(5);
$pdf->Cell(35,8,'Jakarta 14240, Indonesia',3);
$pdf->SetX(145);
$pdf->Cell(40,5,'MAL KELAPA GADING 5',5);
$pdf->Ln(5);
$pdf->SetFont('Arial','',8);
$pdf->SetX(5);
// $pdf->Cell(40,4,'Jl. Perintis Kemerdekaan No.42 ',5);
$pdf->SetX(145);
$pdf->Cell(40,4,'Lt.1 Unit 1F-45 Sentra Kelapa Gading,',5);
$pdf->Ln(3);
$pdf->SetX(5);
// $pdf->Cell(40,5,'Pulomas, Jakarta Timur',5);
$pdf->SetX(145);
$pdf->Cell(40,5,'Jl. Bulevar Kelapa Gading Blok M',5);
$pdf->Ln(3.5);
$pdf->SetX(5);
// $pdf->Cell(40,5,'Jakarta 13210, Indonesia',5);
$pdf->SetX(5);
$pdf->Cell(40,5,'Supplier ',5);
$pdf->SetX(30);
$pdf->Cell(40,5,':  '.$vendor,5);
$pdf->Ln(4);
$pdf->SetX(5);
$pdf->Cell(40,5,'Supplier Code ',5);
$pdf->SetX(30);
$pdf->Cell(40,5,':  '.$vendorcode,5);
$pdf->Ln(4);
$pdf->SetX(5);
$pdf->Cell(40,5,'Document Date ',5);
$pdf->SetX(30);
$pdf->Cell(40,5,':  '.$tgl1,5);

$Y_Fields_Name_position = 50;

$pdf->SetFillColor(110,180,230);
//Bold Font for Field Name
$pdf->SetFont('Arial','',7);
$pdf->SetY($Y_Fields_Name_position);
$pdf->SetX(5);
$pdf->Cell(15,5,'NO',1,0,'C',1);
$pdf->SetX(20.1);
$pdf->Cell(27,5,'EAN/PLU',1,0,'C',1);
$pdf->SetX(45.3);
$pdf->Cell(25,5,'Product No',1,0,'C',1);
$pdf->SetX(70);
$pdf->Cell(20,5,'Article',1,0,'C',1);
$pdf->SetX(90.0);
$pdf->Cell(68,5,'Description',1,0,'C',1);
$pdf->SetX(158);
$pdf->Cell(20,5,'Price',1,0,'C',1);
$pdf->SetX(177);
$pdf->Cell(15,5,'QTY',1,0,'C',1);
$pdf->Ln();

$qtyall = "0";
$nm = 1;
foreach($datalist as $data){
    $qtyall = $qtyall + $data["qty"];
    $pdf->SetX(10);
    $pdf->Cell(15,4,$nm,5);
    $pdf->SetX(21);
    $pdf->Cell(35,4,$data["plu"],5);
    $pdf->SetX(48);
    $pdf->Cell(20,4,$data["substitute"],5);
    $pdf->SetX(72);
    $pdf->Cell(35,4,$data["article_code"],5);
    $pdf->SetX(91);
    $pdf->Cell(20,4,$data["long_description"],5);
    $pdf->SetX(163);
    $pdf->Cell(20,4,rupiah($data["price"]),5);
    $pdf->SetX(183);
    $pdf->Cell(35,4,$data["qty"],5);
$pdf->Ln();
$nm = $nm + 1;
}
    $pdf->SetFillColor(110,180,230);
    $pdf->SetX(5);
    $pdf->Cell(187,5,"    TOTAL",0,0,'',1);
    $pdf->SetX(183);
    $pdf->Cell(171,5,$qtyall,5);
    $qtybrand = "0";
    $pdf->Ln();
$pdf->Output();
$result = sqlsrv_query( $conn, "delete from query_rpt where page = 2" );
?>