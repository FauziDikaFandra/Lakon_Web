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
    $article   = cekget('article');
   // $param = getLakonSecurity($user,$modul,'im','dp2');

    $sql = "select long_description, a.plu, article_code from item_master a
            left join t_stok b on a.plu = b.plu
            where a.article_code = '$article'
            group by long_description, a.plu, article_code";
    $datas = array();
    $result = sqlsrv_query( $conn, $sql );
    if($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $datas[] = array("Long_Description" => $long_description, "plu" => $plu);
        }
        $json = array("status" => 1, "respone" => $datas);
    }else{
        $json = array("status" => 0, "respone" => null);
    }

    echo json_encode($json);
?>