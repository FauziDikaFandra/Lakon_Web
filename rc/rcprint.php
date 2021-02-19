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
    $rc = cekget('rc');
    //$param = getLakonSecurity($user,$modul,'im','dp2');
    if(!empty($user)){
        $sql = "select ROW_NUMBER() OVER (ORDER BY (SELECT 1)) AS nomer,a.rc_code,a.po_code, b.substitute, b.article_code, b.long_description descr, b.qty as qtyrec, c.qty_do
                , qty_rt,a.do_code,c.qty as qtyPO,qty_rc as reciv, convert(varchar(10),receiptdate,120) as receiptdate, c.supplier_code, d.name, a.useradded, m.Brand 
                , convert(varchar(10),c.dateadded,120) as podate
                from t_rc a
                inner join t_rc_detail b on a.rc_code = b.rc_code
                inner join t_po_detail c on c.po_code = a.po_code and c.plu = b.plu
                inner join m_vendor d on d.vendor_code = c.supplier_code
                inner join item_master m on m.Article_Code = b.article_code
                where a.rc_code = '$rc'";
                // echo $sql;
        $datas = array();
        $result = sqlsrv_query( $conn, $sql );
        if ($result){
            while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
                extract($row);
                $datas[] = array("nomer"=> $nomer,"po_code" => $po_code, "substitute"=>$substitute, "article_code" => $article_code, "qtyrec"=>$qtyrec
                           ,"qtydo" => $qty_do, "qtyrt" => $qty_rt, "receiptdate" => $receiptdate, "name" => $name
                           ,"useradded" => $useradded,"Brand"=>$Brand, "descr" => $descr,"qtyPO" => $qtyPO, "reciv" => $reciv
                           ,"do_code" => $do_code, "rc_code" => $rc_code, "podate" => $podate);
            }
            $json = array("status" => 1, "respone" => $datas);
        }else{
            $json = array("status" => 0, "respone" => "Error");
        }
    }
    // echo $sql;
    echo json_encode($json);

?>