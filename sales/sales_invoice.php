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

    $key = cekget('key');
    $conn = BukaKoneksi($us, $ps, $ip, $db);

    $key = decrypts($key);
    
    $dt = "select * from query_rpt where page = 100";
    $result = sqlsrv_query( $conn, $dt );
    if($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            $sql_ = $row['query'];
        }
    }
    if(isset($sql_)){
        $sql =  str_replace("#","'",$sql_); 
        $result1 = sqlsrv_query( $conn,$sql );
    }else{
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
    //==============Hasil Query Sales========
    $sales = [];
    $brand = "";
    $tgl1 = "";
    $tgl2 = "";
    $vendor = "";
    $supcode = "";
    $isppn = "";
    $toko = "";
    if ($result1)
    {
        while( $row = sqlsrv_fetch_array( $result1, SQLSRV_FETCH_ASSOC) ) {
            $sales[]  = $row;
            // $brand = $row["Brand"];
            $tgl1 = $row["tgl1"];
            $tgl2 = $row["tgl2"];
            $vendor = $row["name"];
            $supcode = $row["Supplier_Code"];
            $isppn = $row["isppn"];
            $toko = $row["toko"];
        }
    }
    $length = count($sales);
    $pdf = new FPDF('P','mm',array(210,297)); //L For Landscape / P For Portrait
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',10);
    $pdf->SetFont('Arial','B',15);
    $pdf->Cell(110);
    $pdf->Ln();
$Y_Fields_Name_position_header = 10;   
$pdf->SetFont('Arial','',10);
$pdf->SetX(9);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,5,'BILL TO :',55);
$pdf->SetX(140);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(40,5,'I N V O I C E  '.$toko,5);
$pdf->SetX(169);
$pdf->Ln();
$pdf->SetX(9);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(40,5,'PT.STAR MAJU SENTOSA',5);
$pdf->SetFont('Arial','',10);
$pdf->SetX(140);
$pdf->SetFont('Arial','',10);
$pdf->Cell(10,5,'Vendor Code',5);
$pdf->SetX(162);
$date = date('Y-m-d');
$pdf->Cell(40,5,' : '.$supcode,5);
$pdf->Ln();
$pdf->SetX(9);
$pdf->Cell(40,5,'Plaza Summarecon Lt.5',5);
$pdf->SetX(140.5);
//st($a, $b) = explode('/', $vendor."");
//st($a, $b) = explode('/', $supcode."");
//List($a,$b) = $vendor;
//st($a,$b) = $vendor;
//List($a,$b) = str_replace("/", " ",$vendor);
List($a,$b) = explode('/', $vendor."");
//List($a,$b) = explode("/", " ",$vendor);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(40,5,$a,5,'R');
$pdf->SetFont('Arial','',10);
$pdf->Ln();
$pdf->SetX(9);
$pdf->Cell(40,5,'Jl. Perintis Kemerdekaan No. 42',5);
$pdf->SetX(140);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(40,5,'/'.$b,5,'R');
$pdf->SetFont('Arial','',10);
$pdf->Ln();
$pdf->SetX(9);
$pdf->Cell(40,5,'Jakarta 13210',5);
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',10);
$pdf->SetX(9);
// $pdf->Cell(10,5,$vendor,5);
$pdf->Ln();
$pdf->SetX(9);
$pdf->SetFont('Arial','',10);
$pdf->Cell(40,5,'Period of Sales :  '.$tgl1.' to '.$tgl2,5);
// $pdf->Cell(40,5,'*',100);
$pdf->SetX(769);
$Y_Fields_Name_position = 50;

$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',8);
$pdf->SetY($Y_Fields_Name_position);
$pdf->SetX(10);
$pdf->Cell(13,5,'No',1,0,'C',1);
$pdf->SetX(23);
$pdf->Cell(30,5,'Product No',1,0,'C',1);
$pdf->SetX(53);
$pdf->Cell(79,5,'Product Desc',1,0,'C',1);
$pdf->SetX(132);
$pdf->Cell(15,5,'Qty',1,0,'C',1);
$pdf->SetX(147);
$pdf->Cell(20,5,'Unit Price',1,0,'C',1);
$pdf->SetX(167);
$pdf->Cell(24,5,'Total',1,0,'C',1);
$pdf->Ln();
$pdf->SetFont('Arial','',9);
$unitprice = "0";
$subtotal = "0";
$nomer = 1;
foreach($sales as $data){
    $pdf->SetX(14);
    $pdf->Cell(35,5,$nomer,5); //UNTUK NOMOR
    $pdf->SetX(26);
    $pdf->Cell(33,5,$data["prod"],5); //UNTUK KATALOG
    $pdf->SetX(55);
    $pdf->Cell(33,5,$data["item_description"],5); //UNTUK DESKRIPSI
    $pdf->SetX(137);
    $pdf->Cell(35,5,$data["qty"],5); //UNTUK QTY
    $pdf->SetX(153);
    
    $harga = $data["price"] - (($data["margin"]/100)*($data["price"]));
    $harga = abs($harga);
    $pdf->Cell(12,5,rupiah($harga),5,0,'R'); //UNTUK UNIT PRICE
    $pdf->SetX(172);

    $harga_akhir = $data["hsl"] - (($data["margin"]/100)*($data["hsl"]));
    $pdf->Cell(15,5,rupiah(($harga_akhir)),5,0,'R'); // UNTUK TOTAL

    $pdf->Ln();
    $subtotal = $subtotal + ($harga_akhir);
    $nomer = $nomer + 1;
}
    $pdf->SetFillColor(255,255,255);
    $pdf->SetX(10);
    $pdf->Cell(137,5,'Please remmit your payment to our account',1,0,'L',1);
    $pdf->SetX(147);
    $pdf->SetFont('Arial','B',7);
    $pdf->Cell(20,5,'Sub Total',1,0,'C',1);
    $pdf->SetX(201+25);
    $pdf->Cell(30,8,'',1,0,'C',1);
    $pdf->SetX(167);
    if($isppn == 1){
        $pdf->Cell(24,5,'Rp. '.rupiah($subtotal/1.1),1,0,'C',1);
        $a = $subtotal/1.1;
    }else{
        $pdf->Cell(24,5,'Rp. '.rupiah($subtotal),1,0,'C',1);
        $a = $subtotal;
    }
    $c = $subtotal;
    $b = "";
        $pdf->Ln();
    $pdf->SetX(10);
    $pdf->Cell(137,20,'',1,0,'C',1);
    $pdf->SetX(23+7);
    $pdf->SetX(147);
    $pajak = "";
    $bagiPajak = "";
    if($isppn == 1){
        $pajak = "VAT(10%)";
        $bagiPajak = "Rp. ".rupiah($c - ($a));
    }elseif($isppn == 0){
        $pajak = "VAT(0%)";
        // $bagiPajak = 0;
    }
    $pdf->Cell(20,10,$pajak,1,0,'C',1);
    $pdf->SetX(167);
    $pdf->Cell(24,10,$bagiPajak,1,0,'C',1);
    $pdf->Ln();
    $pdf->SetX(147);
    $pdf->Cell(20,10,'TOTAL DUE',1,0,'C',1);
    $pdf->SetX(167);
    $pdf->Cell(24,10,'Rp. '.rupiah($subtotal),1,0,'C',1);
    $pdf->Ln();
    // $pdf->Ln();
    $pdf->SetX(145);
    $pdf->SetFont('Arial','',9);
    $date = date('d-m-Y');
    $pdf->Cell(30,10,'Jakarta,     '.$tgl2);
    // $pdf->Ln();
    $pdf->Ln();
    $pdf->SetX(149);
    // $pdf->Cell(30,5,'Name  ');
    $pdf->Cell(27,20,'Materai',1,0,'C',1);
    $pdf->Ln();
    $pdf->SetX(145);
    $pdf->Cell(30,9,'Name  ');
    $pdf->SetX(145);
    $pdf->Cell(30,19,'Position  ');
$pdf->Output();
$sqldelete = "delete from query_rpt where page = 100";
$result = sqlsrv_query( $conn, $sqldelete);
?>