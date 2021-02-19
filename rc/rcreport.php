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
    $dt = "select * from query_rpt where page = 4";
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
    $users = "";
    $suppcode = "";
    $gr   = "";
    $podate =  "";
    if ($result1)
    {
        while( $row = sqlsrv_fetch_array( $result1, SQLSRV_FETCH_ASSOC) ) {
            $datalist[]  = $row;
            $vendor = $row["name"];
            $tgl1   = $row["receiptdate"];
            $po     = $row["po_code"];
            $brand     = $row["Brand"];
            $users  = $row["useradded"];
            $suppcode = $row["supplier_code"];
            $do     = $row["do_code"];
            $gr     = $row["rc_code"];
            $podate = $row["podate"];
        }
    }
    $length = count($datalist);

$pdf = new FPDF('P','mm',array(210,297)); //L For Landscape / P For Portrait
$pdf->AddPage();
$pdf->SetX(5);
$pdf->SetFont('Arial','',9);
$pdf->Image('../lakon.png',6,10,-200);
$pdf->Ln(6);
$pdf->SetX(5);
$pdf->Cell(40,8,'Plaza Summarecon Lt.5 ');
$pdf->SetX(90);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(40,8,'GOOD RECEIPT');
$pdf->SetFont('Arial','',9);
$pdf->SetX(145);
    $pdf->Cell(40,5,'No. DO',5);
    $pdf->SetX(165);
    $pdf->Cell(40,5,':',5);
    $pdf->SetX(170);
    $pdf->Cell(40,5,$do,5);
$pdf->Ln(5);
$pdf->SetX(5);

$pdf->Cell(40,5,'Jl. Perintis Kemerdekaan No.42 ',5);
$pdf->SetX(94);
$pdf->Cell(40,8,$gr);
$pdf->SetX(145);
    $pdf->SetX(145);
    $pdf->Cell(40,5,'No. PO',5);
    $pdf->SetX(165);
    $pdf->Cell(40,5,':',5);
    $pdf->SetX(170);
    $pdf->Cell(40,5,$po,5);
$pdf->Ln(3.5);
$pdf->SetX(5);
$pdf->Cell(40,5,'Pulomas, Jakarta Timur',5);
$pdf->SetFont('Arial','',9.5);
$pdf->Ln(9);
    $pdf->SetFont('Arial','',9);
    $pdf->SetX(5);
    $pdf->Cell(40,5,'Supplier Code',5);
    $pdf->SetX(35);
    $pdf->Cell(40,5,':',5);
    $pdf->SetX(38);
    $pdf->Cell(40,5,$suppcode,5);
$pdf->Ln();
    $pdf->SetFont('Arial','',9);
    $pdf->SetX(5);
    $pdf->Cell(40,5,'Supplier',5);
    $pdf->SetX(35);
    $pdf->Cell(40,5,':',5);
    $pdf->SetX(38);
    $pdf->Cell(40,5,$vendor,5);
$pdf->Ln();
$Y_Fields_Name_position = 45;
$pdf->SetFillColor(110,180,230);
$pdf->SetFont('Arial','',8);
$pdf->SetY($Y_Fields_Name_position);
$pdf->SetX(5);
$pdf->Cell(15,5,'NO',1,0,'C',1);
$pdf->SetX(20.1);
$pdf->Cell(33,5,'EAN/PLU',1,0,'C',1);
$pdf->SetX(52.3);
$pdf->Cell(25,5,'Product No',1,0,'C',1);
$pdf->SetX(77);
$pdf->Cell(26,5,'Article',1,0,'C',1);
$pdf->SetX(103.0);
$pdf->Cell(74,5,'Description',1,0,'C',1);
$pdf->SetX(177);
$pdf->Cell(20,5,'QTY',1,0,'C',1);
$pdf->Ln();

$qtyall = "0";
$nm = 1;
foreach($datalist as $data){
    $qtyall = $qtyall + $data["qtyrec"];
    $pdf->SetX(10);
    $pdf->Cell(15,5,$nm,5);
    $pdf->SetX(23);
    $pdf->Cell(35,5,$data["plu"],5);
    $pdf->SetX(77);
    $pdf->Cell(20,5,$data["substitute"],5);
    $pdf->SetX(82);
    $pdf->Cell(35,5,$data["article_code"],5);
    $pdf->SetX(105);
    $pdf->Cell(20,5,$data["descr"],5);
    $pdf->SetX(185);
    $pdf->Cell(35,5,$data["qtyrec"],5);
$pdf->Ln();
$nm = $nm + 1;
}
    $pdf->SetFillColor(110,180,230);
    $pdf->SetX(5);
    $pdf->Cell(194,5,"    TOTAL",0,0,'',1);
    $pdf->SetX(185);
    $pdf->Cell(171,5,$qtyall,5);
    $qtybrand = "0";
    $pdf->Ln();
    $pdf->Ln();
    $pdf->SetX(10);
$pdf->Output();
$result = sqlsrv_query( $conn, "delete from query_rpt where page = 4" );
?>