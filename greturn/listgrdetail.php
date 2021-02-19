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
    $modul = cekget('modul');
    $rcode = cekget('rcode');
   // $param = getLakonSecurity($user,$modul,'im','dp2');

    $sql = "select rc_code, a.po_code, a.do_code, a.substitute, a.article_code, a.plu,a.plu, a.qty as qty
            ,d.last_stok, a.brand, b.description from t_rc_detail a
            inner join item_master b on a.article_code = b.article_code
            inner join t_po_detail c on c.po_code = a.po_code and c.article_code = a.article_code
            left join t_stok d on d.plu = a.plu and convert(varchar(6),date,112) = convert(varchar(6),getdate(),112)
            where rc_code = '$rcode'";
    $datas = array();
    $result = sqlsrv_query( $conn, $sql );
    if($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $datas[] = array("rc_code" => $rc_code, "po_code" => $po_code
                            ,"do_code" => $do_code, "article_code" => $article_code, "plu" => $plu
                            ,"qty" => $qty, "brand" => $brand, "description" => $description, "last_stok" => $last_stok);
        }
        $json = array("status" => 1, "respone" => $datas);
    }else{
        $json = array("status" => 0, "respone" => null);
    }

    echo json_encode($json);
?>