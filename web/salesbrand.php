<?php
header('Access-Control-Allow-Origin: *');
include_once "../../fungsi.php";
include_once "../../lakonsecurity.php";
ini_set('max_execution_time',   300);
    $dt = new DateTime();
    $jam = $dt->format('H')+6;
    $menit = $dt->format('i');
$brand = cekget('brand');
$period1 = cekget('period1');
$period2 = cekget('period2');
$brand = str_replace("^","&",$brand);
$branch = "";
$tambahan ="";
$tambahanDBO ="";
$t=date('Y-m-d');

    if(isset($_GET['brach'])){
        $branch = $_GET['brach'];
    };

    if($branch == "LAKON BALI"){
        if($t != $period1){
            $database    = include('../varLakon.php');
            $tambahan = "_bali";
            $tambahanDBO = "[POS_SERVER_HISTORY].[dbo].";
        }else{
            if(($jam >= 21)){
                $database    = include('../varLakon.php');
                $tambahan = "_bali";
                $tambahanDBO = "[POS_SERVER_HISTORY].[dbo].";
            }else{
                $database    = include('../varLakon_bali.php');
            }
        }
    }else{
        $database    = include('../varLakon.php');
    }
    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];
    $conn = BukaKoneksi($us, $ps, $ip, $db);

    
    $sql = "declare @date datetime
            declare @dtm varchar(12)
            declare @firstdate datetime
            set @date = convert(datetime,'$period1',120)
            set @firstdate =  DATEADD(mm, DATEDIFF(mm, 0, @date) - 0, 0)
            select * from
            (
            select CONVERT(varchar(10),transaction_date,120) as period,count(distinct a.Transaction_Number)trx, floor(SUM(b.Net_Price)/1.1)sales 
            from ".$tambahanDBO."Sales_Transactions".$tambahan." a
            inner join ".$tambahanDBO."Sales_Transaction_Details".$tambahan." b on a.Transaction_Number = b.Transaction_Number
            inner join Item_Master c on c.PLU = b.PLU
            where a.Status = '00' and c.Brand <> 'No Brand'
            and CONVERT(varchar(10),transaction_date,120) between convert(varchar(10),@firstdate,120) and '$period1'
            and Brand = '$brand'
            group by CONVERT(varchar(10),transaction_date,120)
            union
            select CONVERT(varchar(10),transaction_date,120) as period,count(distinct a.Transaction_Number)trx, floor(SUM(b.Net_Price)/1.1)sales 
            from [POS_SERVER_HISTORY].[dbo].Sales_Transactions".$tambahan." a
            inner join [POS_SERVER_HISTORY].[dbo].Sales_Transaction_Details".$tambahan." b on a.Transaction_Number = b.Transaction_Number
            inner join [POS_SERVER_HISTORY].[dbo].Item_Master c on c.PLU = b.PLU
            where a.Status = '00' and c.Brand <> 'No Brand'
            and CONVERT(varchar(10),transaction_date,120) between convert(varchar(10),@firstdate,120) and '$period1'
            and Brand = '$brand'
            group by CONVERT(varchar(10),transaction_date,120)
            )dt order by CONVERT(date,period) asc";
        $datas = array();
        $result = sqlsrv_query( $conn, $sql );
        if ($result){
            while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
                extract($row);
                $datas[] = array("period" => $period,"trx"=>$trx,"sales" => $sales);
            }
            $json = array("status" => 1, "respone" => $datas);
        }else{
            $json = array("status" => 0, "respone" => "Error");
        }
    // echo $sql;
    echo json_encode($json);
?>