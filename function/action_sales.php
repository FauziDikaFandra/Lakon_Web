<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
include_once "../../fungsi.php";
include_once "../../lakonsecurity.php";
// header("Content-type: application/vnd-ms-excel");
// Mendefinisikan nama file ekspor "hasil-export.xls"
// header("Content-Disposition: attachment; filename=tutorialweb-export.xls");
 
// Tambahkan table
    ini_set('max_execution_time',   300);
    $database    = include('../varLakon.php');
    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];

    $conn = BukaKoneksi($us, $ps, $ip, $db);
    $date1   = cekget('date1');
    $date2   = cekget('date2');
    $brand   = cekget('brand');
    $group   = cekget('group');
    $user    = cekget('user');
    $filter  = "";
    // if($brand == "Light"){
    //     $brand = "Light & salt";
    // }
    $brand = str_replace("^","&",$brand);
    $brand = str_replace("\'","'",$brand); 
    $brand = str_replace("'","''",$brand);
    $brand = str_replace("'","#",$brand);
    if($group == "2"){
       if($brand == "undefined" || $brand == "all"){
            $br = "select distinct brand from Item_Master where Supplier_Code = '$user'";
            // $result1 = sqlsrv_query( $conn, $br );
            // $brd = "";
            // if($result1){
            //     while( $row = sqlsrv_fetch_array( $result1, SQLSRV_FETCH_ASSOC) ) {
            //         $brd = $row["brand"];
            //     }
            //     $filter = " and Brand in (#$brd#)";
            // }
            $filter = " and supplier_code = #$user# ";
            
        }else{
            $filter = "and Brand in (#$brand#) ";
        } 
    }else{
        if($brand == "undefined"){
            $filter = "";
        }else{
            $filter = "and Brand in (#$brand#) ";
        }
    }
    
    $sql = "Declare @store varchar(4) Declare @Date1 datetime   Declare @Date2 datetime     
            Set @Date1 = #$date1#     
            Set @Date2 = #$date2#
            select ROW_NUMBER() OVER (Order by brand) AS nomer,
                   ROW_NUMBER() OVER(PARTITION BY brand ORDER BY brand ASC) AS id_brand,
                   ROW_NUMBER() OVER(PARTITION BY brand,periode ORDER BY brand ASC) AS id_period,* from
            ( 
            select * from
            (     
            select 
            convert(varchar(10),transaction_date,120) as periode ,b.plu  
            ,c.Long_description as item_description,   SUM(cast(b.qty as int))qty,b.price,SUM((cast(b.qty as int)* b.price)) netSales     
            ,SUM(b.Discount_Amount) as disc, sum(b.Net_Price)as Net_Price ,Brand, convert(varchar(10),@date1,120) as tgl1, 
            convert(varchar(10),@date2,120) as tgl2 ,v.name, MAX(substitute)prod from sales_transactions a inner join     
            sales_transaction_details b on a.transaction_number = b.transaction_number     
            inner join item_master c on c.plu = b.plu  inner join m_vendor v on v.vendor_code = c.Supplier_Code 
            left join item_katalog t on t.plu = b.PLU where     
            (CONVERT(varchar(10), a.Transaction_Date, 120)     between @Date1 and @Date2) and   (a.Status = #00#)     
            and c.burui not in (#NMD92ZZZ9#,#NMD98ZZZ9#,#NMD31ZZZ9#,#NMD96ZZZ9#)    
            --and substring(a.Transaction_Number,5,1) <> #3#   
            and c.PLU NOT IN(#9000013100002#,#9000063500005#) $filter
            GROUP BY convert(varchar(10),transaction_date,120),b.plu,c.Long_description     
            ,b.qty,b.price, Brand,v.name
            )xz 
            union
            select * from
            (     
            select 
            convert(varchar(10),transaction_date,120) as periode ,b.plu  
            ,c.Long_description as item_description,   SUM(cast(b.qty as int))qty,b.price,SUM((cast(b.qty as int)* b.price)) netSales     
            ,SUM(b.Discount_Amount) as disc, sum(b.Net_Price)as Net_Price ,Brand, convert(varchar(10),@date1,120) as tgl1, 
            convert(varchar(10),@date2,120) as tgl2 ,v.name, MAX(substitute)prod from [POS_SERVER_HISTORY].[dbo].sales_transactions a inner join     
            [POS_SERVER_HISTORY].[dbo].sales_transaction_details b on a.transaction_number = b.transaction_number     
            inner join item_master c on c.plu = b.plu  inner join m_vendor v on v.vendor_code = c.Supplier_Code 
            left join item_katalog t on t.plu = b.PLU where      
            (CONVERT(varchar(10), a.Transaction_Date, 120)     between @Date1 and @Date2) and   (a.Status = #00#)     
            and c.burui not in (#NMD92ZZZ9#,#NMD98ZZZ9#,#NMD31ZZZ9#,#NMD96ZZZ9#)   
            --and substring(a.Transaction_Number,5,1) <> #3#  
            and c.PLU NOT IN(#9000013100002#,#9000063500005#) $filter
            GROUP BY convert(varchar(10),transaction_date,120),b.plu,c.Long_description     
            ,b.qty,b.price, Brand,v.name
            )xz 
            ) data
            order by periode asc";
        $result = sqlsrv_query( $conn, "exec sp_report '$sql','$user',8,''" );
        $out = "";
        if($result){
         $out = array("status" => "1");
         echo json_encode($out);
        }else{
            $out = array("status" => "0");
         echo json_encode($out);
        }
    // echo "insert into query_rpt select '$sql','$user',getdate(),2";
    //$conn = BukaKoneksi($us, $ps, $ip, $db);

?>