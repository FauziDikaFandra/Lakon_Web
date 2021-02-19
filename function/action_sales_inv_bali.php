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
    // $date1   = cekget('date1');
    // $date2   = cekget('date2');
    $bulan     = cekget('bulan');
    $tahun     = cekget('tahun');
    $brand   = cekget('brand');
    $group   = cekget('group');
    $user    = cekget('user');
    $filter  = "";

    $tgl = "01";
    $date = date_parse($bulan);
    $bulan  =  $date['month'];
    if(strlen($bulan) == 1){
        $bulan = "0".$bulan;
    }
    $date1 =  $tahun."-".$bulan."-01";
    $date2 = date("Y-m-t", strtotime($date1));

    // echo $date1."--".$date1
    if($group == "2"){
        $filter = " and Supplier_code in (#$brand#) ";
    }else{
        if($brand == "undefined"){
            $filter = "";
        }else{
            $filter = "and Supplier_code in (#$brand#) ";
        }
    }
    
    // pertama
    // $sql = "Declare @store varchar(4) Declare @Date1 datetime   Declare @Date2 datetime                   
    //         Set @Date1 = #$date1#                  
    //         Set @Date2 = #$date2#       
            
    //         select  convert(varchar(10),transaction_date,120) as periode ,b.plu  ,c.Supplier_Code, c.article_code            
    //         ,c.Long_Description as item_description,   SUM(cast(b.qty as int))qty,b.price,SUM((cast(b.qty as int)* b.price)) netSales                   
    //         , b.Discount_Percentage as disc, b.Net_Price ,Brand, convert(varchar(10),@date1,120) as tgl1,c.current_price as harga,
    //         convert(varchar(10),@date2,120) as tgl2 ,v.name, MAX(substitute)prod,B.Discount_Amount ,sum(b.Net_Price )*(100 - m.margin)/100 as hsl
    //         ,z.isppn,m.margin
    //         from [POS_SERVER_HISTORY].[dbo].sales_transactions a inner join                   
    //         [POS_SERVER_HISTORY].[dbo].sales_transaction_details b on a.transaction_number = b.transaction_number                   
    //         inner join item_master c on c.plu = b.plu  inner join m_vendor v on v.vendor_code = c.Supplier_Code               
    //         left join item_katalog t on t.plu = b.PLU  
    //         inner join m_vendor z on z.vendor_code = c.Supplier_Code     
    //         left join item_marge m on m.plu = b.plu  where    
    //         --CONVERT(varchar(8),efective_date,112) > CONVERT(varchar(8),GETDATE(),112) and        
    //         (CONVERT(varchar(10), a.Transaction_Date, 120)     
    //         between @Date1 and @Date2) and   (a.Status = #00#)                   
    //         and c.burui not in (#NMD92ZZZ9#,#NMD98ZZZ9#,#NMD31ZZZ9#,#NMD96ZZZ9#)                   
    //         and c.PLU NOT IN(#9000013100002#,#9000063500005#) $filter            
    //         GROUP BY convert(varchar(10),transaction_date,120),b.plu,c.article_code,c.Long_Description                   
    //         ,b.qty,b.price, b.Discount_Percentage, b.Net_Price,Brand,v.name,c.Supplier_Code,c.current_price,B.Discount_Amount 
    //         ,m.margin,z.isppn";

    // kedua
    // $sql = "Declare @store varchar(4) Declare @Date1 datetime   Declare @Date2 datetime                   
    //         Set @Date1 = #$date1#                    
    //         Set @Date2 = #$date2# 
            
    //         select convert(varchar(10),DocDate,120)period,d.PLU,c.Supplier_Code,e.name,c.Article_Code 
    //         ,c.Long_Description as item_description,cast(b.Quantity as int) qty,d.Price as price,d.Discount_Amount,b.Margin as margin,#-# prod
    //         ,isppn,b.Price as hsl,convert(varchar(10),@Date1,120) tgl1, convert(varchar(10),@Date2,120) as tgl2 from t_Sales_Header a
    //         inner join t_Sales_Details b on a.DocNum = b.DocNum
    //         inner join Item_Master c on c.PLU = b.CodeBars
    //         inner join [POS_SERVER_HISTORY].dbo.Sales_Transaction_Details d on d.Transaction_Number = a.Reference
    //         and b.CodeBars = d.PLU and d.Seq = b.LineNum
    //         inner join m_vendor e on e.vendor_code = c.Supplier_Code
    //         where CONVERT(varchar(10),docdate,120) between @Date1 and @Date2
    //         $filter  
    //         GROUP BY convert(varchar(10),DocDate,120),d.PLU,c.Supplier_Code,c.Article_Code
    //         ,c.Long_Description,b.Quantity,d.Price,d.Discount_Amount,b.Margin,e.name,isppn
    //         ,b.Price,Seq order by convert(varchar(10),DocDate,120) , Article_Code asc";

    // ketiga
    $sql = "Declare @store varchar(4) Declare @Date1 datetime   Declare @Date2 datetime                   
            Set @Date1 = #$date1#                    
            Set @Date2 = #$date2# 

            select #LAKON BALI# as toko,PLU,Supplier_Code,name,Article_Code,item_description,cast(SUM(qty) as int)qty,price,SUM(Discount_Amount)Discount_Amount
            ,margin,## as prod,isppn,sum(hsl)hsl,convert(varchar(10),@Date1,120) tgl1, convert(varchar(10),@Date2,120) as tgl2
            from
            (
            select convert(varchar(10),DocDate,120)period,d.PLU,c.Supplier_Code,e.name,c.Article_Code 
            ,c.Long_Description as item_description,b.Quantity qty,d.Price,d.Discount_Amount,b.Margin as margin,#-# prod
            ,isppn,b.Price as hsl
            from t_Sales_Header a
            inner join t_Sales_Details b on a.DocNum = b.DocNum
            inner join Item_Master c on c.PLU = b.CodeBars
            inner join [POS_SERVER_HISTORY].dbo.Sales_Transaction_Details_bali d on d.Transaction_Number = a.Reference
            and d.Seq = b.LineNum
            inner join m_vendor e on e.vendor_code = c.Supplier_Code
            where CONVERT(varchar(10),docdate,120) between @Date1 and @Date2
            and a.store = #S011# 
            $filter
            )data
            group by PLU,Supplier_Code,name,Article_Code,item_description,Price
            ,margin,isppn";
        $date = date('Y-m-d');
        $hasil = encrypts($user."-".$date."-lakonstoreindonesia");
        // echo $hasil;
        $result = sqlsrv_query( $conn, "exec sp_report '$sql','$user',100,'$hasil'" );
        $out = "";
        if($result){
         $out = array("status" => "1", "hasil" => $hasil);
         echo json_encode($out);
        }else{
            $out = array("status" => "0", "hasil" => "null");
         echo json_encode($out);
        }
?>