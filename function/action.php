<?php
header('Access-Control-Allow-Origin: *');
include_once "../../fungsi.php";
include_once "../../lakonsecurity.php";
// header("Content-type: application/vnd-ms-excel");
// Mendefinisikan nama file ekspor "hasil-export.xls"
//header("Content-Disposition: attachment; filename=tutorialweb-export.xls");
 

    ini_set('max_execution_time',   300);
    $database    = include('../varLakon.php');
    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];

    $conn = BukaKoneksi($us, $ps, $ip, $db);
    $date   = cekget('date');
    $brand  = cekget('brand');
    $plu  = cekget('plu');
    $user = cekget('user');
    $group =  cekget('group');
    // header("Content-Disposition: attachment; filename=$brand.xls");
    $brand = str_replace("^","&",$brand);
    $brand = str_replace("\'","'",$brand); 
    $brand = str_replace("'","''",$brand);
    $brand = str_replace("'","#",$brand);
    $filter_brand = "";
    $filter_plu = "";
    if($group == "2"){
        if($brand == "" or $brand == "undefined" or $brand == "all"){
            $filter_brand = " and Supplier_Code = #$user# ";
        }else{
            $filter_brand = " and b.Brand = @brand ";
        }

        if($plu == "" or $plu == "undefined" ){
            $filter_plu = "";
        }else{
            $filter_plu = " and b.Long_Description = #$plu# ";
        };
    }else{
        if($brand == "" or $brand == "undefined"){
            $filter_brand = "";
        }elseif($brand == "all"){
            $filter_brand = " and Supplier_Code = #$user# ";
        }else{
            $filter_brand = " and b.Brand = @brand ";
        };

        if($plu == "" or $plu == "undefined" ){
            $filter_plu = "";
        }else{
            $filter_plu = " and a.Long_Description = #$plu# ";
        };
    }
    
    
    $sql = "declare @dtm varchar(50)
            declare @brand varchar(50)
            declare @firstdate DATE
            declare @date datetime
            set @dtm = #$date#
            set @brand = #$brand#
            set @date = convert(datetime,@dtm,120)
            set @firstdate =  DATEADD(mm, DATEDIFF(mm, 0, @date) - 0, 0)

            select brand as Brand,b.Long_Description,a.plu,a.first_stok,b.Current_Price as harga,isnull(x1.qty,0) GR,0 as refund
            ,cast(ISNULL(x2.sales,0) as int) sales, isnull(x3.[out],0) + isnull(x4.[qty],0) as [out],isnull(x3.[in],0) as [in]
            ,(a.first_stok + isnull(x1.qty,0)+ isnull(x3.[in],0)) - (cast(ISNULL(x2.sales,0) as int) + isnull(x3.[out],0) + isnull(x4.qty,0)) as last_stok
            ,b.article_code,i.substitute,@firstdate tgl1,@dtm tgl2 from t_stok a
            inner join Item_Master b on a.plu = b.PLU
            left join 
                (select plu, SUM(qty) as qty from t_rc a
                inner join t_rc_detail b on a.rc_code = b.rc_code 
                where a.status = #POST# and b.brand = @brand and a.branch_id = #S012#
                and convert(varchar(8),a.postingdate,112) between CONVERT(varchar(8),@firstdate,112) 
                and CONVERT(varchar(8),@date,112)
                group by plu)x1 on x1.plu = a.plu
            left join 
                (select PLU,SUM(b.quantity)sales,max(current_price)harga from t_Sales_Header a
                inner join t_Sales_Details b on a.DocNum = b.DocNum
                inner join Item_Master c on c.PLU = b.CodeBars
                where SUBSTRING(a.Reference,5,1) not in (#43#) and a.store = #S012# and 
                CONVERT(varchar(8),docDate,112) 
                between CONVERT(varchar(8),@firstdate,112) 
                and CONVERT(varchar(8),@date,112) and 
                c.Brand = @brand group by PLU)x2 on x2.PLU = a.plu
            left join item_katalog i on i.plu = a.plu
            left join (select b.plu,sum(case when b.type = #in# then qty else 0 end) as [in]
                ,sum(case when b.type = #out# then qty else 0 end) as [out]
                from t_adjustment a
                inner join t_adjustment_details b on a.adj_kode = b.adj_kode
                where a.status = #POST# and CONVERT(varchar(8),a.posting_date,112) between CONVERT(varchar(8),@firstdate,112)
                and CONVERT(varchar(8),@date,112) 
                group by plu,b.type)x3 on x3.plu = a.plu
            left join (select plu,sum(qty)qty from t_rt a
                inner join t_rt_detail b on a.rt_code = b.rt_code
                where a.status = #POST# and CONVERT(varchar(8),postingdate,112) 
                between CONVERT(varchar(8),@firstdate,112)
                and CONVERT(varchar(8),@date,112) 
                and b.brand = @brand and b.remarks not like #%S011%#
                group by plu)x4 on x4.plu = a.plu
            where CONVERT(varchar(6),[date],112) = CONVERT(varchar(6),@date,112)
            $filter_brand $filter_plu ";   
        $hasil = sqlsrv_query( $conn, "exec sp_report '$sql','$user',101,''" );
        // echo "exec sp_report '$sql','$user',11";
        if($hasil){
            $out = array("respone" => "1");
            echo json_encode($out);
        }else{
            $out = array("respone" => "0");
            echo json_encode($out);
        }
    // echo "insert into query_rpt select $sql,$user,getdate()";
    //$conn = BukaKoneksi($us, $ps, $ip, $db);
    //$moduls   = cekget('moduls');
    // echo $brand;
//   include 'export_stok.php';

?>