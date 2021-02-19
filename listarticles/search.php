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

    
    $conn   = BukaKoneksi($us, $ps, $ip, $db);
    $user   = cekget('user');
    $art    = cekget('art');
    $brand  = cekget('brand');
    $brand = str_replace("^","&",$brand);
    $brand = str_replace("'"," '",$brand);
    $brand = str_replace("Nicole\ 's Natural","Nicole''s Natural",$brand);
    if(!empty($user)){
        if(!empty($user) && empty($brand) && empty($art)){
            if (is_numeric(substr($user,1,1))){
                $sql = "select distinct brand as article_code from item_master as im 
                    inner join m_vendor b on im.Supplier_Code = b.vendor_code
                    inner join t_stok x on x.plu = im.plu
                    where description <> 'TIDAK AKTIF' and im.Supplier_Code = '$user'
                   
                    order by brand";
            }else{
                $sql = "select distinct brand as article_code from item_master as im 
                    inner join m_vendor b on im.Supplier_Code = b.vendor_code
                    inner join t_stok x on x.plu = im.plu
                    where description <> 'TIDAK AKTIF'  order by brand";
            }
            
        }elseif ($art == "undefined" && $brand <> "undefined"){
            $filter1 = " ";
            $filter2 = " and Brand = '$brand'";  
            //disini
            $sql = "select  im.plu,im.burui,supplier_code, im.article_code,Long_Description, Brand,Current_Price, b.name, sum(last_stok) as  last_stok
                    , substitute , 0 as purch from item_master as im inner join m_vendor b on im.Supplier_Code = b.vendor_code 
                    inner join t_stok x on x.plu = im.plu and convert(varchar(6),date,112) = convert(varchar(6),getdate(),112)
                    left join item_katalog y on y.plu = im.plu
                    where description <> 'TIDAK AKTIF' and brand = '$brand' 
                    group by im.plu,im.burui,supplier_code, im.article_code,Long_Description
                    , Brand,Current_Price, b.name, substitute order by article_code asc";
        }elseif ($art <> "undefined" && $brand <> "undefined") {
            $filter1 = " and (im.plu like '$art%' or im.article_code like '$art%')";
            $filter2 = " and Brand = '$brand'";
            $sql = "select  im.plu,im.burui,supplier_code, im.article_code,Long_Description, Brand,Current_Price, b.name, sum(last_stok) as last_stok 
                    , substitute , 0 as purch from item_master as im inner join m_vendor b on im.Supplier_Code = b.vendor_code 
                    inner join t_stok x on x.plu = im.plu and convert(varchar(6),date,112) = convert(varchar(6),getdate(),112)
                    left join item_katalog y on y.plu = im.plu
                    where description <> 'TIDAK AKTIF' $filter1 $filter2 
                    
                    group by im.plu,im.burui,supplier_code, im.article_code,Long_Description
                    , Brand,Current_Price, b.name, substitute";
        }elseif($art <> "undefined" && $brand == "undefined"){
            $filter1 = " and (im.plu like '$art%' or im.article_code like '$art%')";
            $filter2 = "";
            $sql = "select  im.plu,im.burui,supplier_code, im.article_code,Long_Description, Brand,Current_Price, b.name, sum(last_stok) as last_stok 
                    , substitute , 0 as purch from item_master as im left join m_vendor b on im.Supplier_Code = b.vendor_code 
                    inner join t_stok x on x.plu = im.plu and convert(varchar(6),date,112) = convert(varchar(6),getdate(),112)
                    left join item_katalog y on y.plu = im.plu
                    where description <> 'TIDAK AKTIF' $filter1 $filter2 
                    group by im.plu,im.burui,supplier_code, im.article_code,Long_Description
                    , Brand,Current_Price, b.name, substitute";
        } 
        
        $datas = array();
        $result = sqlsrv_query( $conn, $sql );
        if ($result){
            if(!empty($user) && empty($brand) && empty($art)){
                while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
                        extract($row);
                        $datas[] = array("article_code" => $article_code);
                }
            }else{
                while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
                    extract($row);
                    $datas[] = array("plu" => $plu,"article_code" => $article_code, "Long_Description" => $Long_Description
                                    ,"Brand" => $Brand, "Current_Price" => $Current_Price, "name" => $name
                                    ,"burui" => $burui, "supplier_code" => $supplier_code, "last_stok" => $last_stok
                                    ,"substitute" => $substitute, "purch" => $purch);
                }
            }

            $json = array("status" => 1, "respone" => $datas);
        }else{
            $json = array("status" => 0, "respone" => "Error");
        }
    }else{
        // $sql = "select distinct TOP 20 article_code from item_master as im 
        //             inner join m_vendor b on im.Supplier_Code = b.vendor_code
        //             inner join t_stok x on x.plu = im.plu
        //             where description <> 'TIDAK AKTIF'";
        $sql = "select distinct article_code from item_master a
                left join t_stok b on a.plu = b.plu
                where description <> 'TIDAK AKTIF'
                group by long_description, a.plu, article_code";
                    $datas = array();
        $result = sqlsrv_query( $conn, $sql );
        if ($result){
            while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
                extract($row);
                $datas[] = array("article_code" => $article_code);
            }
            $json = array("status" => 1, "respone" => $datas);
        }
    }
    // echo $sql;
    // echo $brand;
    echo json_encode($json);

?>