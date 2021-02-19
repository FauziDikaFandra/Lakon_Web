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
    $po   = cekget('po');
    $sql = "select a.brand,a.article_code,a.plu,a.long_description,a.qty,b.qty as qtypo,b.qty as qtygr from t_po_detail a
            left join t_rc_detail b on a.po_code = b.po_code 
            where a.po_code = '$po'";
    $result = sqlsrv_query($conn,$sql);
    if($result){
        while($row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $datas[] = array( "article_code"=>  $article_code ,"plu" => $plu,"desc" => $long_description, "brand" => $brand,"qtypo" => $qtypo, "qtygr" => $qtygr);
        }
        $json = array("status" => 1, "respone" => $datas);
    }else{
        $json = array("status" => 0, "respone" => null);
    }

    // echo $sql;
    echo json_encode($json);
?>