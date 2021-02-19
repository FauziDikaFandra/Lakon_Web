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
    $adm    = cekget('adm');
    $dtm1   = cekget('dtm1');
    $dtm2   = cekget('dtm2');

    $sql = "select a.*,convert(varchar(10),date,120)dts ,description, im.article_code , type as typ from m_adjustment a
              inner join item_master im on a.plu = im.plu where admin = '$adm' and convert(varchar(10),date,120) between '$dtm1' and '$dtm2'";

    $datas = array();
    $result = sqlsrv_query( $conn, $sql );
    if ($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $datas[] = array("dts" => $dts, "article_code" => $article_code ,"plu" => $PLU, "desc" => $description, "qty" => $qty, "type" => $typ, "to" => $to, "from" => $from, "user" => $user );
        }
        $json = array("status" => 1, "respone" => $datas);
    }else{
        $json = array("status" => 0, "respone" => "Error");
    }

    echo json_encode($json);
    
?>