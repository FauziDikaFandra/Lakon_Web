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
    $user   = cekget('user');
    $date1  = cekget('date1');
    $date2  = cekget('date2');
    $group  = cekget('group');
    $brand  = cekget('brand');
    $parday = date("Y-m-d");
    $brand = str_replace("^","&",$brand); 
    $brand = str_replace("\'","'",$brand); 
    $brand = str_replace("'","''",$brand);
    $filter = "";
    if ($brand == "undefined" || $brand == "" || $brand == "all"){
        $ilter = "";
    }else{
        $ilter = " and brand = '$brand' ";
    }

    if(!empty($user)){
        if ($group  == "2"){
            
            $filter = "";
            if($brand == "undefined" || $brand == "" || $brand == "all"){
                $filter = " and supplier_code = '$user' ";
            }else{
                $filter = " and brand = '$brand' ";
            }

            
            $sql = "Declare @store varchar(4) Declare @Date1 datetime Declare @Date2 datetime 
                    Set @Date1 = '$date1' 
                    Set @Date2 = '$date2' 
                    select convert(varchar(10),transaction_date,120) as periode ,b.plu
                    ,c.long_description as item_description, cast(SUM(b.qty) as int)qty,b.price,SUM(b.Net_Price) Net_Price 
                    , SUM(b.Discount_Amount) as disc, sum(b.Net_Price)as netSales, max(k.substitute )substitute 
                    from sales_transactions a inner join 
                    sales_transaction_details b on a.transaction_number = b.transaction_number 
                    inner join item_master c on c.plu = b.plu 
                    left join item_katalog k on k.plu = b.plu
                    where 
                    (CONVERT(varchar(10), a.Transaction_Date, 120) 
                    between @Date1 and @Date2) and 
                    (a.Status = '00') 
                    --and substring(a.Transaction_Number,5,1) <> '3'  
                    and c.burui not in ('NMD92ZZZ9','NMD98ZZZ9','NMD31ZZZ9','NMD96ZZZ9') 
                    and c.PLU NOT IN('9000013100002','9000063500005') 
                     $filter 
                    GROUP BY convert(varchar(10),transaction_date,120),b.plu,c.long_description
                    ,b.price
                     
                     union 

                     select convert(varchar(10),transaction_date,120) as periode ,b.plu
                    ,c.long_description as item_description, cast(SUM(b.qty) as int)qty,b.price,SUM(b.Net_Price) Net_Price 
                    , SUM(b.Discount_Amount) as disc, sum(b.Net_Price)as netSales, max(k.substitute )substitute 
                    from [POS_SERVER_HISTORY].[dbo].sales_transactions a inner join 
                    [POS_SERVER_HISTORY].[dbo].sales_transaction_details b on a.transaction_number = b.transaction_number 
                    inner join item_master c on c.plu = b.plu 
                    left join item_katalog k on k.plu = b.plu
                    where 
                    (CONVERT(varchar(10), a.Transaction_Date, 120) 
                    between @Date1 and @Date2) and 
                    (a.Status = '00') 
                    --and substring(a.Transaction_Number,5,1) <> '3'  
                    and c.burui not in ('NMD92ZZZ9','NMD98ZZZ9','NMD31ZZZ9','NMD96ZZZ9') 
                    and c.PLU NOT IN('9000013100002','9000063500005') 
                     $filter
                    GROUP BY convert(varchar(10),transaction_date,120),b.plu,c.long_description
                    ,b.price";
        }else{
            $sql = "Declare @store varchar(4) Declare @Date1 datetime Declare @Date2 datetime 
                    Set @Date1 = '$date1' 
                    Set @Date2 = '$date2' 

                    select convert(varchar(10),transaction_date,120) as periode ,b.plu
                    ,c.long_description as item_description, cast(SUM(b.qty) as int)qty,b.price,SUM( b.Net_Price) Net_Price 
                    , SUM(b.Discount_Amount) as disc, sum(b.Net_Price)as netSales, max(k.substitute )substitute  
                    from sales_transactions a inner join 
                    sales_transaction_details b on a.transaction_number = b.transaction_number 
                    inner join item_master c on c.plu = b.plu 
                    left join item_katalog k on k.plu = b.plu
                    where 
                    (CONVERT(varchar(10), a.Transaction_Date, 120) 
                    between @Date1 and @Date2) and 
                    (a.Status = '00') 
                    and c.burui not in ('NMD92ZZZ9','NMD98ZZZ9','NMD31ZZZ9','NMD96ZZZ9') 
                    and c.PLU NOT IN('9000013100002','9000063500005') $ilter
                    GROUP BY convert(varchar(10),transaction_date,120),b.plu,c.long_description 
                    ,b.price
                     union 
                    select convert(varchar(10),transaction_date,120) as periode ,b.plu
                    ,c.long_description as item_description, cast(SUM(b.qty) as int)qty,b.price,SUM(b.Net_Price) Net_Price 
                    , SUM(b.Discount_Amount) as disc, sum(b.Net_Price)as netSales, max(k.substitute )substitute 
                    from [POS_SERVER_HISTORY].[dbo].sales_transactions a inner join 
                    [POS_SERVER_HISTORY].[dbo].sales_transaction_details b on a.transaction_number = b.transaction_number 
                    inner join item_master c on c.plu = b.plu 
                    left join item_katalog k on k.plu = b.plu
                    where 
                    (CONVERT(varchar(10), a.Transaction_Date, 120) 
                    between @Date1 and @Date2) and 
                    (a.Status = '00') 
                    and c.burui not in ('NMD92ZZZ9','NMD98ZZZ9','NMD31ZZZ9','NMD96ZZZ9') 
                    and c.PLU NOT IN('9000013100002','9000063500005') $ilter
                    GROUP BY convert(varchar(10),transaction_date,120),b.plu,c.long_description 
                    ,b.price, b.Discount_Amount, b.Net_Price";
        }

        
        $datas = array();
        $result = sqlsrv_query( $conn, $sql );
        if ($result){
            while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
                extract($row);
                $datas[] = array("periode" => $periode,"plu" => $plu, "item_description" => $item_description
                                    ,"qty" => $qty, "price" => $price, "netSales" => $netSales
                                    ,"disc" => $disc, "Net_Price" => $Net_Price);
            }
            $json = array("status" => 1, "respone" => $datas);
        }else{
            $json = array("status" => 0, "respone" => "Error");
        }
    }
    // echo $sql;
    echo json_encode($json);

?>