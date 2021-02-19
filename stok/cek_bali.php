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
    $date   = cekget('date');
    $brand  = cekget('brand');
    $plu  = cekget('plu');
    $user = cekget('user');

    $filter_brand = "";
    $filter_plu = "";
    if($brand == "" or $brand == "undefined"){
        $filter_brand = "";
    }elseif($brand == "all"){
        $filter_brand = " and Supplier_Code = '$user' ";
    }else{
       $filter_brand = " and b.Brand = @brand";
    };

    if($plu == "" or $plu == "undefined" ){
        $filter_plu = "";
    }else{
        $filter_plu = " and b.Long_Description = '$plu' ";
    };
    $brand = str_replace("^","&",$brand);
    $brand = str_replace("\'","'",$brand); 
    $brand = str_replace("'","''",$brand);
    $sql = "declare @dtm varchar(50)
            declare @brand varchar(50)
            declare @firstdate DATE
            declare @date datetime
            set @dtm = '$date'
            set @brand = '$brand'
            set @date = convert(datetime,@dtm,120)
            set @firstdate =  DATEADD(mm, DATEDIFF(mm, 0, @date) - 0, 0)
            select 
            brand,b.Long_Description,a.plu,a.first_stok,b.Current_Price harga,isnull(isnull(x1.qty,x5.qty_direct),0) GR,0 as refund
            ,cast(ISNULL(x2.sales,0) as int) sales, isnull(x3.[out],0) + isnull(isnull(x4.qty,x6.qty_dir_retur),0) as [out],isnull(x3.[in],0) as [in]
            ,(a.first_stok + isnull(isnull(x1.qty,x5.qty_direct),0)+ isnull(x3.[in],0)) - (cast(ISNULL(x2.sales,0) as int) + isnull(isnull(x3.[out],x6.qty_dir_retur),0) + isnull(x4.qty,0)) as last_stok
            ,b.article_code,i.substitute
            from t_stok_bali a
            inner join Item_Master b on a.plu = b.PLU
            left join 
                    (select plu, SUM(qty) as qty from t_rc a
                    inner join t_rc_detail b on a.rc_code = b.rc_code 
                    where a.status = 'POST' and b.brand = @brand and a.branch_id = 'S011'
                    and convert(varchar(8),a.postingdate,112) between CONVERT(varchar(8),@firstdate,112) 
                    and CONVERT(varchar(8),@date,112)
                    group by plu)x1 on x1.plu = a.plu
            left join 
                    (select PLU,SUM(b.quantity)sales,max(current_price)harga from t_Sales_Header a
                    inner join t_Sales_Details b on a.DocNum = b.DocNum
                    inner join Item_Master c on c.PLU = b.CodeBars
                    where SUBSTRING(a.Reference,5,1) not in ('394','393','391','392','395','396') 
                    and CONVERT(varchar(6),a.docdate,112) > '201804'
                    and a.store = 'S011' and 
                    CONVERT(varchar(8),docDate,112) 
                    between CONVERT(varchar(8),@firstdate,112) 
                    and CONVERT(varchar(8),@date,112) and 
                    c.Brand = @brand group by PLU)x2 on x2.PLU = a.plu
            left join item_katalog i on i.plu = a.plu
            left join (select b.plu,sum(case when b.type = 'in' then qty else 0 end) as [IN]
                    ,sum(case when b.type = 'out' then qty else 0 end) as [OUT]
                    from t_adjustment a
                    inner join t_adjustment_details b on a.adj_kode = b.adj_kode
                    where a.status = 'POST' and CONVERT(varchar(8),a.posting_date,112) between CONVERT(varchar(8),@firstdate,112)
                    and CONVERT(varchar(8),@date,112) and a.whs_code = 'S011'
                    group by plu,b.type)x3 on x3.plu = a.plu
            left join (select plu,sum(qty) qty from t_rt a
                    inner join t_rt_detail b on a.rt_code = b.rt_code
                    where a.status = 'POST' and CONVERT(varchar(8),postingdate,112) 
                    between CONVERT(varchar(8),@firstdate,112)
                    and CONVERT(varchar(8),@date,112) 
                    and b.brand = @brand and b.remarks like '%S011%'
                    group by plu)x4 on x4.plu = a.plu
            left join(select plu,sum(b.qty)qty_direct from t_rc_direct a -- GR direct (x5)
                    left join t_rc_direct_dtl b on a.code = b.ID
                    where a.brand = @brand and a.branch= 'S011'
                    and convert(varchar(8),a.tgl,112) between CONVERT(varchar(8),@firstdate,112) 
                    and CONVERT(varchar(8),@date,112)
                    group by plu)x5 on x5.plu = a.plu
            left join (select convert(varchar,plu) as plu,article_code,sum(b.qty)qty_dir_retur from t_rt_direct a -- Good Return direct (x6)
                    inner join t_rt_direct_dtl b on a.code = b.ID
                    where a.brand = @brand and a.branch = 'S011'
                    and convert(varchar(8),a.tgl,112) between CONVERT(varchar(8),@firstdate,112) 
                    and CONVERT(varchar(8),@date,112) 
                    group by plu,article_code)x6 on x6.plu = b.PLU
            where CONVERT(varchar(6),[date],112) = CONVERT(varchar(6),@date,112)
            and b.Description <> 'TIDAK AKTIF'
            $filter_brand $filter_plu ";
        $datas = array();
        $result = sqlsrv_query( $conn, $sql );
        if ($result){
            while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
                extract($row);
                $datas[] = array("brand" => $brand,"plu" => $plu, "Long_Description" => $Long_Description
                                 ,"plu" => $plu, "first_stok" => $first_stok, "GR" => $GR,"harga" => $harga
                                 ,"sales" => $sales, "refund" => $refund, "out" => $out, "in" => $in
                                 , "last_stok" => $last_stok,"article_code" => $article_code, "substitute" => $substitute);
            }
            $json = array("status" => 1, "respone" => $datas);
        }else{
            $json = array("status" => 0, "respone" => "Error");
        }
    // echo $sql ;
    echo json_encode($json);

?>