<?php
header('Access-Control-Allow-Origin: *');
include_once "../../fungsi.php";
include_once "../../lakonsecurity.php";
    ini_set('max_execution_time',   300);
    if(isset($_GET['branch'])){
        $branch = $_GET['branch'];
    };
    if($branch == "LAKON BALI"){
        $database = include('../varLakon_bali.php');
    }else{
        $database    = include('../varLakon.php');
    }
    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];
    $date1 = cekget('date1');
    $date2 = cekget('date2');
    $date3 = cekget('date3');
    $date4 = cekget('date4');
    $selisih = cekget('selisih');
    $max = cekget('max');
    $min = cekget('min');
    if($selisih >= 0){
        $selisih = "+$selisih";
    }
    $max_ = " where a.id between $min and $max";
    $conn = BukaKoneksi($us, $ps, $ip, $db);
    $sql = "select  a.id,floor(isnull(b.sales,0)/1000000)sales1,floor(ISNULL(c.sales,0)/1000000)sales2 
            ,datename(month,convert(date,'$date1'))bulan1,datename(month,convert(date,'$date3'))bulan2 from dates a
            left join 
            (select datename(DAY,a.transaction_date)period,SUM(b.Net_Price) as sales from [POS_SERVER_HISTORY].[dbo].Sales_Transactions a 
            inner join [POS_SERVER_HISTORY].[dbo].Sales_Transaction_Details b 
            on a.Transaction_Number = b.Transaction_Number
            where a.Status = '00' and CONVERT(varchar(10),a.transaction_date,120) 
            between '$date1' and '$date2'
            group by datename(DAY,a.transaction_date)) b on a.id = b.period
            left join
            (select datename(DAY,a.transaction_date)period,SUM(b.Net_Price) as sales from [POS_SERVER_HISTORY].[dbo].Sales_Transactions a 
            inner join [POS_SERVER_HISTORY].[dbo].Sales_Transaction_Details b 
            on a.Transaction_Number = b.Transaction_Number
            where a.Status = '00' and CONVERT(varchar(10),a.transaction_date,120) 
            between '$date3' and '$date4'
            group by datename(DAY,a.transaction_date)) c on c.period $selisih = a.id
            $max_
            order by a.id asc";
        $datas = array();
        $result = sqlsrv_query( $conn, $sql );
        if ($result){
            while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
                extract($row);
                $datas[] = array("id" => $id,"sales1" => $sales1,"sales2" => $sales2,"bulan1" =>$bulan1, "bulan2" => $bulan2);
            }
            $json = array("status" => 1, "respone" => $datas);
        }else{
            $json = array("status" => 0, "respone" => "Error");
        }
        // echo $sql; 
    echo json_encode($json);

?>