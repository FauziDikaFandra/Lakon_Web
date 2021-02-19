<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: text/html; charset=utf-8');
    setlocale(LC_ALL, 'fr_CA.utf-8');
    include_once "../../fungsi.php";
    include_once "../../lakonsecurity.php";
    ini_set('max_execution_time',   300);
    $database    = include('../varLakon.php');
    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];

    $plu = cekget('plu');
    $conn   = BukaKoneksi($us, $ps, $ip, $db);
    // $sql = "select   im.plu,im.burui,supplier_code, im.article_code,Long_Description+'_ ' as Long_Description, Brand,Current_Price, b.name, sum(last_stok) as  last_stok
    //         , substitute , 0 as purch from item_master as im inner join m_vendor b on im.Supplier_Code = b.vendor_code 
    //         inner join t_stok x on x.plu = im.plu and convert(varchar(6),date,112) = convert(varchar(6),getdate(),112)
    //         left join item_katalog y on y.plu = im.plu
    //         where description <> 'TIDAK AKTIF' and brand = 'Stella Rossa' and x.plu = '$plu'
    //         group by im.plu,im.burui,supplier_code, im.article_code,Long_Description
    //         , Brand,Current_Price, b.name, substitute";
    //    $sql = "select Long_Description from item_master as im inner join m_vendor b on im.Supplier_Code = b.vendor_code 
    //         inner join t_stok x on x.plu = im.plu and convert(varchar(6),date,112) = convert(varchar(6),getdate(),112)
    //         left join item_katalog y on y.plu = im.plu
    //         where description <> 'TIDAK AKTIF' and brand = 'Stella Rossa' and x.plu = '$plu'
    //         group by im.plu,im.burui,supplier_code, im.article_code,Long_Description
    //         , Brand,Current_Price, b.name, substitute";
    
    $sql = "select top 1 'SSA Abigail Bucket Bag Small Grey' as Long_Description from item_master where brand = 'Stella Rossa' and plu not in ('1002205900009     ','1002206000005     ','1002206200009     ') ";
    
    $datas = array();
    $result = sqlsrv_query( $conn, $sql );
    if ($result){
            while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
                    extract($row);
                     $datas[] = array("Long_Description" => $Long_Description);
                    // $datas[] = array("Long_Description" =>str_replace( chr( 194 ) . chr( 160 ), ' ', $Long_Description ));
            }
            
    }
    $json = array("status" => 1, "respone" => $datas);
    echo json_encode($json);
    // echo $sql;

?>