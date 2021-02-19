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

    $usr  = cekget('user');
    
    $conn = BukaKoneksi($us, $ps, $ip, $db);
    $dt = "select * from query_rpt where page = 8 and users = '$usr'";

    // echo $dt;
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
    $sales = [];
    $brand = "";
    $tgl1 = "";
    $tgl2 = "";
    $vendor = "";
    if ($result1)
    {
        while( $row = sqlsrv_fetch_array( $result1, SQLSRV_FETCH_ASSOC) ) {
            $sales[]  = $row;
            $brand = $row["Brand"];
            $tgl1 = $row["tgl1"];
            $tgl2 = $row["tgl2"];
            $vendor = $row["name"];
        }
    }
    $length = count($sales);

    $pdf = new FPDF('L','mm',array(210,297)); //L For Landscape / P For Portrait
    $pdf->AddPage();
    $pdf->SetFont('Arial','BU',13);
    // $pdf->SetFont('','U');
    $pdf->Cell(5);
    $pdf->Image('../lakon.png',17,10,-150);
    $pdf->Cell(95,22,'SALES REPORT',0,0,'L');
    $pdf->Ln();
    $pdf->Cell(20);
$Y_Fields_Name_position_heade = 10;   
$pdf->SetFont('Arial','',10);
$pdf->SetX(15);
$pdf->Cell(40,3,'Brand');
$pdf->SetX(25);
$pdf->Cell(40,3,':');
$pdf->SetX(35);
$pdf->Cell(35,3,$brand,5);
$pdf->Ln(1);
$pdf->SetX(15);
$pdf->Cell(37,8,'Period',5);
$pdf->SetX(25);
$pdf->Cell(40,8,':');
$pdf->SetX(35);
$pdf->Cell(35,8,$tgl1,5);
$pdf->SetX(55);
$pdf->Cell(40,8,'s/d',5);
$pdf->SetX(63);
$pdf->Cell(40,8,$tgl2,5);
$pdf->Ln();

$Y_Fields_Name_position = 40;

$pdf->SetFillColor(110,180,230);
$pdf->SetFont('Arial','',10);
$pdf->SetY($Y_Fields_Name_position);
$pdf->SetX(15);
$pdf->Cell(35,5,'Period',1,0,'C',1);
$pdf->SetX(23+27);
$pdf->Cell(35,5,'EAN/PLU',1,0,'C',1);
$pdf->SetX(58+27);
$pdf->Cell(33,5,'Product No',1,0,'C',1);
$pdf->SetX(90+27);
$pdf->Cell(60,5,'Description',1,0,'C',1);
$pdf->SetX(148+27);
$pdf->Cell(20,5,'Qty',1,0,'C',1);
$pdf->SetX(168+27);
$pdf->Cell(29,5,'Price',1,0,'C',1);
$pdf->SetX(197+27);
$pdf->Cell(20,5,'Disc(%)',1,0,'C',1);
$pdf->SetX(217+27);
$pdf->Cell(31,5,'Sales',1,0,'C',1);
// $pdf->SetX(241);
// $pdf->Cell(35,8,'Sales',1,0,'C',1);
$pdf->Ln();

$qtytgl = "0";
$qtybrand = "0";
$qtyall = "0";

$salestgl = "0";
$salesbrand = "0";
$salesall = "0";

$disctgl = "0";
$discbrand ="0";
$discall = "0";

$grosstgl = "0";
$grossbrand ="0";
$grossall ="0";
$tglDumy = $tgl2;
$qty_ = "0";
// dummy
$id_ = "0";
$qty_ = 0;
$sls_ = 0;
$disc_ = 0;
foreach($sales as $data){
    $pdf->SetFont('Arial','',8);
    // untuk qty
    $salestgl = $salestgl + $data["netSales"];
    $qtytgl = $qtytgl + $data["qty"];
    $disctgl = $disctgl + $data["disc"];
    
    $discall = $discall + $data["disc"];
    $qtyall = $qtyall + $data["qty"];
    $grossall = $grossall + $data["netSales"];
    // //untuk disc
    if(($data["nomer"] !="1") && ($data["id_period"] =="1"))
    {
        $qty_ = "0";
        $sls_ = "0";
        $disc_ = "0";
        $pdf->SetFillColor(182, 145, 107);
        $pdf->SetX(15);
        $pdf->Cell(260,5,"Total Sales / Date   ",0,0,'',1);
        $pdf->SetX(157+25);
        $pdf->Cell(271,5,(int)$qtytgl - $data["qty"],5);
        $pdf->SetX(195+25);
        $pdf->SetX(203+25);
        $pdf->Cell(40,5,rupiah($disctgl),5);
        $pdf->SetX(226+25);
        $pdf->Cell(40,5,rupiah($salestgl - $data["netSales"]),5);
        
        $qtytgl =  $data["qty"];
        $disctgl = "0";
        $salestgl = $data["netSales"];
       
        $pdf->Ln();
    }
    $qty_ = $qty_ + $data["qty"];
    $sls_ = $sls_ + $data["netSales"];
    $disc_ = $disc_ + $data["disc"];

    $pdf->SetX(0+25);
    $pdf->Cell(35,4,$data["periode"],5);
    $pdf->SetX(28+25);
    $pdf->Cell(33,4,$data["plu"],5);

    $pdf->SetX(64+25);
    $pdf->Cell(55,4,$data["prod"],5);

    $pdf->SetX(95+25);
    $pdf->Cell(55,4,$data["item_description"],5);
    $pdf->SetX(157+25);
    $pdf->Cell(35,4,$data["qty"],5);
    $pdf->SetX(178+25);
    $pdf->Cell(20,4,rupiah($data["price"]+0),5);
    $pdf->SetX(195+25);
    $pdf->SetX(203+25);
    $pdf->Cell(25,4,rupiah($data["disc"]),5);
    $pdf->SetX(226+25);
    $pdf->Cell(40,4,rupiah($data["Net_Price"]),5);
    $pdf->Ln();
    if(($data["nomer"] == $length)){
        $pdf->SetFillColor(182, 145, 107);
        $pdf->SetX(15);
        $pdf->Cell(260,5,"Total Sales / Date   ",0,0,'',1);
        $pdf->SetX(157+25);
        $pdf->Cell(271,5,(int)$qty_,5);
        $pdf->SetX(195+25);
        $pdf->SetX(203+25);
        $pdf->Cell(40,5,rupiah($disctgl),5);
        $pdf->SetX(226+25);
        $pdf->Cell(40,5,rupiah($sls_),5);
        
        $qtytgl = "0";
        $disctgl = "0";
        $salestgl = $data["netSales"];
       
        $pdf->Ln();
    }
}
    $pdf->SetFillColor(110,180,230);
    $pdf->SetX(15);
    $pdf->Cell(260,5,"SUMMARY SALES",0,0,'',1);
    $pdf->SetX(157+25);
    $pdf->Cell(171,5,$qtyall,5);
    $pdf->SetX(170);
    $pdf->SetX(203+25);
    $pdf->Cell(40,5,rupiah($discall),5);
    $pdf->SetX(226+25);
    $pdf->Cell(40,5,rupiah($grossall),5);
    $qtybrand = "0";
    $pdf->Ln();
$pdf->Output();
$result = sqlsrv_query( $conn, "delete from query_rpt where page = 8" );
?>