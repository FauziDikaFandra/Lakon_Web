<?php
header('Access-Control-Allow-Origin: *');
include_once "../../fungsi.php";
include_once "../../lakonsecurity.php";
    ini_set('max_execution_time',   300);
    $branch = "";
    if(isset($_GET['branch'])){
        $branch = $_GET['branch'];
    };
    if($branch == "LAKON BALI"){
        $tblA = "Sales_Transactions_Bali";
        $tblB = "Sales_Transaction_Details_Bali";
        $tblC = "t_stok_bali";
        $database    = include('../varLakon.php');
    }else{
        $tblA = "Sales_Transactions";
        $tblB = "Sales_Transaction_Details";
        $tblC = "t_stok";
        $database    = include('../varLakon.php');
       
    }
    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];
    $range = cekget('range');
    $conn = BukaKoneksi($us, $ps, $ip, $db);
    // $sql = "declare @dt varchar(10) 
    //         declare @dtm1 datetime 
    //         declare @dtm2 datetime 
    //         declare @range int set @range = $range
    //         set @dtm1 = getdate() set @dtm2 = DATEADD(MONTH,-@range,getdate()) 
    //         select dt.*,floor(SUM(Qty)/@range) as [AVG],max(stok) as stok 
    //         ,case when max(stok) <> 0 and floor(SUM(Qty)/@range) <> 0 then round(max(stok)/floor(SUM(Qty)/@range),1)
    //         when max(stok) <> 0 and floor(SUM(Qty)/@range) = 0 then MAX(stok) 
    //         else 0 end as ito from ( 
    //         select Brand,SUM(b.Qty)qty from Sales_Transactions a 
    //         inner join Sales_Transaction_Details b on a.Transaction_Number = b.Transaction_Number 
    //         inner join Item_Master c on c.PLU = b.PLU where a.Status = '00' and c.Brand <> 'No Brand' 
    //         and CONVERT(varchar(10),a.transaction_date,120) between CONVERT(varchar(10),@dtm2,120) 
    //         and CONVERT(varchar(10),@dtm1,120) group by c.Brand 
    //         union 
    //         select Brand,SUM(b.Qty)qty from [POS_SERVER_HISTORY].[dbo].Sales_Transactions a 
    //         inner join [POS_SERVER_HISTORY].[dbo].Sales_Transaction_Details b on a.Transaction_Number = b.Transaction_Number 
    //         inner join Item_Master c on c.PLU = b.PLU where a.Status = '00' 
    //         and c.Brand <> 'No Brand' and c.Brand <> 'BARCODE DUMMY' and CONVERT(varchar(10),a.transaction_date,120) 
    //         between CONVERT(varchar(10),@dtm2,120) and CONVERT(varchar(10),@dtm1,120) group by c.Brand )dt 
    //         inner join (select Brand,SUM(last_stok)stok from t_stok a 
    //         inner join Item_Master b on a.plu = b.PLU where convert(varchar(6),date,112) = convert(varchar(6),GETDATE(),112) 
    //         group by Brand)dx on dt.Brand = dx.Brand group by dt.Brand,qty order by dt.Brand asc";
    $sql = "declare @dt varchar(10) 
            declare @dtm1 datetime 
            declare @dtm2 datetime 
            declare @range int 
            declare @var1 varchar(9)
            declare @var2 varchar(9)
            set @range = $range
            set @dtm1 = DATEADD(MONTH,-@range,getdate()) 
            set @dtm2 = dateadd(month,-1,getdate()) 
            set @var1 = convert(varchar(6),@dtm1,112)
            set @var2 = convert(varchar(6),@dtm2,112)

            select dt.*,floor(SUM(Qty)/@range) as [AVG],max(stok) as stok 
            ,case when max(stok) <> 0 and floor(SUM(Qty)/@range) <> 0 then round(max(stok)/floor(SUM(Qty)/@range),1)
            when max(stok) <> 0 and floor(SUM(Qty)/@range) = 0 then MAX(stok) 
            else 0 end as ito
            from ( 
            select Brand,SUM(b.Qty)qty from [POS_SERVER_HISTORY].[dbo].$tblA a 
            inner join [POS_SERVER_HISTORY].[dbo].$tblB b on a.Transaction_Number = b.Transaction_Number 
            inner join Item_Master c on c.PLU = b.PLU where a.Status = '00' 
            and c.Brand <> 'No Brand' and c.Brand <> 'BARCODE DUMMY' and CONVERT(varchar(6),a.transaction_date,112) 
            between CONVERT(varchar(6),@var1,112) and CONVERT(varchar(6),@var2,112) 
            group by c.Brand )dt 
            inner join (select Brand,SUM(last_stok)stok from $tblC a 
                        inner join Item_Master b on a.plu = b.PLU where convert(varchar(6),date,112) = convert(varchar(6),GETDATE(),112) 
                        group by Brand)dx on dt.Brand = dx.Brand group by dt.Brand,qty order by dt.Brand asc";
        $datas = array();
        $result = sqlsrv_query( $conn, $sql );
        if ($result){
            while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
                extract($row);
                $datas[] = array("Brand" => $Brand,"sales" => $qty,"avg" => $AVG , "stok" => $stok, "ito" => $ito);
            }
            $json = array("status" => 1, "respone" => $datas);
        }else{
            $json = array("status" => 0, "respone" => "Error");
        }
        // echo $sql;
    echo json_encode($json);

?>