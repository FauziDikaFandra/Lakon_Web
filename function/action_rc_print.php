<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");
include_once "../../fungsi.php";
include_once "../../lakonsecurity.php";
// header("Content-type: application/vnd-ms-excel");
// Mendefinisikan nama file ekspor "hasil-export.xls"
// header("Content-Disposition: attachment; filename=tutorialweb-export.xls");
 
// Tambahkan table
    ini_set('max_execution_time',   300);
    $database    = include('../varLakon.php');
    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];

    $conn = BukaKoneksi($us, $ps, $ip, $db);
    $rc   = cekget('rc');
    $user = cekget('user');

    
    $sql = "select ROW_NUMBER() OVER (ORDER BY (SELECT 1)) AS nomer,a.rc_code,a.po_code, i.substitute, b.article_code, b.long_description descr, b.qty as qtyrec, c.qty_do
            , b.do_code,qty_rt,a.do_code,c.qty as qtyPO,qty_rc as reciv, convert(varchar(10),postingdate,120) as receiptdate, c.supplier_code, d.name, a.useradded, m.Brand 
            , convert(varchar(10),c.dateadded,120) as podate,b.plu 
            from t_rc a
            inner join t_rc_detail b on a.rc_code = b.rc_code
            inner join t_po_detail c on c.po_code = a.po_code and c.plu = b.plu
            inner join m_vendor d on d.vendor_code = c.supplier_code
            inner join item_master m on m.Article_Code = b.article_code
            left join item_katalog i on i.plu = b.plu
            where a.rc_code = #$rc# order by b.article_code asc ";
        // sqlsrv_query( $conn, "delete from query_rpt where page = 6" );
        // $result = sqlsrv_query( $conn, "insert into query_rpt select '$sql','$user',getdate(),6" );
        $result = sqlsrv_query( $conn, "exec sp_report '$sql','$user',4,''" );
    $out = "";
    if($result){
        $out = array("respone" => "1");
        echo json_encode($out);
    }else{
        $out = array("respone" => "0");
        echo json_encode($out);
    }
    // echo "insert into query_rpt select '$sql','$user',getdate(),4";
    //$conn = BukaKoneksi($us, $ps, $ip, $db);
    //$moduls   = cekget('moduls');
    
//   include 'export_listDO.php';

?>