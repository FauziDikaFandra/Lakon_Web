<?php
header('Access-Control-Allow-Origin: *');
include_once "../../fungsi.php";
include_once "../../lakonsecurity.php";
    ini_set('max_execution_time',   300);
    $dt = new DateTime();
    $jam = $dt->format('H')+6;
    $menit = $dt->format('i');
    $branch = "";
    $tambahan ="";
    $tambahanDBO ="";
    $nobrand = " and c.Brand <> 'No Brand'";
    $period = cekget('period');
    $t=date('Y-m-d');
    
    
    if(isset($_GET['branch'])){
        $branch = $_GET['branch'];
    };

    if($branch == "LAKON MKG"){
        $database    = include('../varLakon.php');
    }else{
        $nobrand = "";

        if(($jam >= 21)){
            $database    = include('../varLakon.php');
            $tambahan = "_bali";
            $tambahanDBO = "[POS_SERVER_HISTORY].[dbo].";
        }else{
            if($t != $period){
                // echo $t;
                $database    = include('../varLakon.php');
                $tambahan = "_bali";
                $tambahanDBO = "[POS_SERVER_HISTORY].[dbo].";
            }else{
                // $database    = include('../varLakon_bali.php');
                $database    = include('../varLakon.php');
                $tambahan = "_bali";
                $tambahanDBO = "[POS_SERVER_HISTORY].[dbo].";
            }
            
        }
        
    }
    
    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];
    $conn = BukaKoneksi($us, $ps, $ip, $db);
    
    
    
    $sql = "select * from
            (
            select top 20 c.dp2,c.Brand as brand,cast(sum(qty) as int)trx,COUNT(distinct a.Transaction_Number)trx1, floor(cast(SUM(b.Net_Price) as decimal(12,2))/1.1) sales 
            from ".$tambahanDBO."Sales_Transactions$tambahan a
            inner join ".$tambahanDBO."Sales_Transaction_Details$tambahan b on a.Transaction_Number = b.Transaction_Number
            inner join Item_Master c on c.PLU = b.PLU
            where a.Status = '00' $nobrand
            and CONVERT(varchar(10),transaction_date,120) = '$period'
            group by c.dp2,c.Brand
            order by SUM(b.Net_Price) desc
            union
            select top 20 c.dp2,c.Brand as brand,cast(sum(qty) as int) trx,COUNT(distinct a.Transaction_Number)trx1, floor(cast(SUM(b.Net_Price) as decimal(12,2))/1.1)  sales 
            from [POS_SERVER_HISTORY].[dbo].Sales_Transactions$tambahan a
            inner join [POS_SERVER_HISTORY].[dbo].Sales_Transaction_Details$tambahan b on a.Transaction_Number = b.Transaction_Number
            inner join [POS_SERVER_HISTORY].[dbo].Item_Master c on c.PLU = b.PLU
            where a.Status = '00' $nobrand
            and CONVERT(varchar(10),transaction_date,120) = '$period'
            group by c.dp2,c.Brand
            order by SUM(b.Net_Price) desc
            )dt order by sales desc";
            
    $sql1 = "select jam.jam,isnull(trx,0)trx,floor(ISNULL(sales,0)/1.1) sales from jam
            left join
            (
            select * from
            (
            select LEFT(a.Transaction_Time,2)+':00' as Jam,count(distinct a.transaction_number)trx,SUM(b.Net_Price)sales
            from ".$tambahanDBO."Sales_Transactions$tambahan a
            inner join ".$tambahanDBO."Sales_Transaction_Details$tambahan b on a.Transaction_Number = b.Transaction_Number
            inner join Item_Master c on c.PLU = b.PLU
            where a.Status = '00' $nobrand
            and CONVERT(varchar(10),transaction_date,120) = '$period'
            group by LEFT(a.Transaction_Time,2)
            union
            select LEFT(a.Transaction_Time,2)+':00' as Jam,count(distinct a.transaction_number)trx,SUM(b.Net_Price)sales
            from [POS_SERVER_HISTORY].[dbo].Sales_Transactions$tambahan a
            inner join [POS_SERVER_HISTORY].[dbo].Sales_Transaction_Details$tambahan b on a.Transaction_Number = b.Transaction_Number
            inner join [POS_SERVER_HISTORY].[dbo].Item_Master c on c.PLU = b.PLU
            where a.Status = '00' $nobrand
            and CONVERT(varchar(10),transaction_date,120) = '$period'
            group by LEFT(a.Transaction_Time,2)
            )data 
            )z on z.Jam = jam.jam 
            where jam.jam not in ('08:00')
            order by CAST(  LEFT(jam.jam,2) AS int) asc";

    // }
    
        $datas = array();
        $result = sqlsrv_query( $conn, $sql );
        $result1 = sqlsrv_query( $conn, $sql1 );
        if ($result){
            while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
                extract($row);
                $datas[] = array("sbu"=>$dp2,"brand" => $brand,"trx"=>$trx,"sales" => $sales, "trx1" =>$trx1);
            }
            while( $row1 = sqlsrv_fetch_array( $result1, SQLSRV_FETCH_ASSOC) ) {
                extract($row1);
                $datas1[] = array("jam" => $jam,"trx"=>$trx,"sales" => $sales);
            }
            $json = array("status" => 1, "respone" => $datas,"respone1"=> $datas1);
        }else{
            $json = array("status" => 0, "respone" => "Error");
        }
    // echo $sql1;
    echo json_encode($json);
?>