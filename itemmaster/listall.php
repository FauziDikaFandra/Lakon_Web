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
    $desc   = cekget('desc');
    $sql  = "Select top 5 im.plu, im.long_description,im.brand, ik.substitute, isnull(ik.price,0)price, t.last_stok
            from item_master im
            left join item_katalog ik on im.article_code=ik.article_code and im.supplier_code=ik.cardcode
            inner join t_stok t on t.plu = im.plu
            where im.burui not in ('NMD92ZZZ9','NMD31ZZZ9','NMD96ZZZ9','NMD98ZZZ9')
            and Right(im.burui,1) = '1'
            and (im.long_description like '%$desc%' or im.plu like '$desc%')
            and im.PLU NOT IN ('9000013100002','9000063500005')
            and (im.description <> 'TIDAK AKTIF')
            AND CONVERT(VARCHAR(6),date,112) = CONVERT(VARCHAR(6),GETDATE(),112)
            order by im.long_description";

    $datas = array();
    $result = sqlsrv_query( $conn, $sql );
    if ($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $datas[] = array("plu" => $plu, "long_description" => $long_description
            , "substitute" => $substitute, "price" => (int)$price, "last_stok"=> $last_stok
            , "brand" => $brand);
        }
        $json = array("status" => 1, "respone" => $datas);
    }else{
        $json = array("status" => 0, "respone" => "Error");
    }
    echo $sql;
    echo json_encode($json);

?>