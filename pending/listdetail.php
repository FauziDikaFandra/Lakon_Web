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

    $user = cekget('user');
    $po   = cekget('po');
    $conn = BukaKoneksi($us, $ps, $ip, $db);
    $sql = "select b.po_code, convert(varchar(10),c.receiptdate,120)tgl ,b.article_code, burui, description , sisa_rc as qty, sisa_do from t_po a inner join t_po_detail b
            on a.po_code = b.po_code and b.sisa_rc <> 0
            inner join t_rc c on c.po_code = a.po_code 
            where a.po_code = '$po' --and a.useradded = '$user'";

    $datas = array();
    $result = sqlsrv_query( $conn, $sql );
    if ($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $datas[] = array("po_code" => $po_code,"article_code" => $article_code, "burui" => $burui
                                ,"description" => $description, "qty" => $qty, "sisa_do" => $sisa_do, "tgl" => $tgl);
        }
        $json = array("status" => 1, "respone" => $datas);
    }else{
        $json = array("status" => 0, "respone" => "Error");
    }
    // echo $sql;
    echo json_encode($json);

?>