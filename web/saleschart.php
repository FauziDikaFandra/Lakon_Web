<?php
header('Access-Control-Allow-Origin: *');
include_once "../../fungsi.php";
include_once "../../lakonsecurity.php";
    ini_set('max_execution_time',   300);
    $database    = include('../varLakon.php');
    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];
    $conn = BukaKoneksi($us, $ps, $ip, $db);
    $sql = "select datename(month,CONVERT(date,periode))period,floor(SUM(netSales)/1000000) sales from
      (
      select convert(varchar(10),transaction_date,120) as periode ,b.plu
      ,c.long_description as item_description, cast(SUM(b.qty) as int)qty,b.price,SUM((cast(b.qty as int)* b.price)) netSales 
      , b.Discount_Percentage as disc, b.Net_Price, max(k.substitute )substitute 
      from sales_transactions a inner join 
      sales_transaction_details b on a.transaction_number = b.transaction_number 
      inner join item_master c on c.plu = b.plu 
      left join item_katalog k on k.plu = b.plu
      where 
      (a.Status = '00') 
      and c.burui not in ('NMD92ZZZ9','NMD98ZZZ9','NMD31ZZZ9','NMD96ZZZ9') 
      and c.PLU NOT IN('9000013100002','9000063500005') 
      GROUP BY convert(varchar(10),transaction_date,120),b.plu,c.long_description
      ,b.price, b.Discount_Percentage, b.Net_Price 
      union 
      select convert(varchar(10),transaction_date,120) as periode ,b.plu
      ,c.long_description as item_description, cast(SUM(b.qty) as int)qty,b.price,SUM((cast(b.qty as int)* b.price)) netSales 
      , b.Discount_Percentage as disc, b.Net_Price, max(k.substitute )substitute 
      from [POS_SERVER_HISTORY].[dbo].sales_transactions a inner join 
      [POS_SERVER_HISTORY].[dbo].sales_transaction_details b on a.transaction_number = b.transaction_number 
      inner join item_master c on c.plu = b.plu 
      left join item_katalog k on k.plu = b.plu
      where 
      (a.Status = '00') 
      and c.burui not in ('NMD92ZZZ9','NMD98ZZZ9','NMD31ZZZ9','NMD96ZZZ9') 
      and c.PLU NOT IN('9000013100002','9000063500005') 
      GROUP BY convert(varchar(10),transaction_date,120),b.plu,c.long_description
      ,b.price, b.Discount_Percentage, b.Net_Price
      )data1
      group by convert(varchar(6),CONVERT(date,periode),112),datename(month,CONVERT(date,periode))
      order by convert(varchar(6),CONVERT(date,periode),112) asc";
        $datas = array();
        $result = sqlsrv_query( $conn, $sql );
        if ($result){
            while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
                extract($row);
                $datas[] = array("period" => $period,"sales" => $sales);
            }
            $json = array("status" => 1, "respone" => $datas);
        }else{
            $json = array("status" => 0, "respone" => "Error");
        }
    echo json_encode($json);

?>