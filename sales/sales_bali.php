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
    $brand = str_replace("'","#",$brand);
    // echo $brand;
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
                    
                    select convert(varchar(10),DocDate,120)as periode,b.CodeBars as plu,
                    c.Long_Description as item_description,sum(b.Quantity) as qty,b.price
                    ,b.Price as Net_Price,0 as disc,sum(b.Quantity) * b.Price as netSales,'' as substitute
                    from t_Sales_Header a
                    inner join t_Sales_Details b on a.DocNum = b.DocNum
                    inner join Item_Master c on c.PLU = b.CodeBars
                    where 
                    (CONVERT(varchar(10), a.DocDate, 120) between @Date1 and @Date2) 
                    and a.Store = 'S011'
                    and c.burui not in ('NMD92ZZZ9','NMD98ZZZ9','NMD31ZZZ9','NMD96ZZZ9') 
                    and c.PLU NOT IN('9000013100002','9000063500005') $filter 
                    GROUP BY convert(varchar(10),DocDate,120),b.CodeBars,c.long_description 
                    ,b.price";
        }else{
            $sql = "Declare @store varchar(4) Declare @Date1 datetime Declare @Date2 datetime 
                    Set @Date1 = '$date1' 
                    Set @Date2 = '$date2' 

                    select convert(varchar(10),DocDate,120)as periode,b.CodeBars as plu,
                    c.Long_Description as item_description,sum(b.Quantity) as qty,b.price
                    ,b.Price as Net_Price,0 as disc,sum(b.Quantity) * b.Price as netSales,'' as substitute
                    from t_Sales_Header a
                    inner join t_Sales_Details b on a.DocNum = b.DocNum
                    inner join Item_Master c on c.PLU = b.CodeBars
                    where 
                    (CONVERT(varchar(10), a.DocDate, 120) between @Date1 and @Date2) 
                    and a.Store = 'S011'
                    and c.burui not in ('NMD92ZZZ9','NMD98ZZZ9','NMD31ZZZ9','NMD96ZZZ9') 
                    and c.PLU NOT IN('9000013100002','9000063500005') $filter 
                    GROUP BY convert(varchar(10),DocDate,120),b.CodeBars,c.long_description 
                    ,b.price";
        }

        
        $datas = array();
        $result = sqlsrv_query( $conn, $sql );
        if ($result){
            while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
                extract($row);
                $datas[] = array("periode" => $periode,"plu" => $plu, "item_description" => $item_description
                                    ,"qty" => $qty, "price" => $price, "netSales" => $netSales
                                    ,"disc" => $disc, "Net_Price" => $netSales);
            }
            $json = array("status" => 1, "respone" => $datas);
        }else{
            $json = array("status" => 0, "respone" => "Error");
        }
    }
    // echo $sql;
    echo json_encode($json);

?>