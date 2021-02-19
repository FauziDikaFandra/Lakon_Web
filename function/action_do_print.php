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
    $do   = cekget('do');
    $user = cekget('user');

    
    $sql = "select b.name,a.brand,a.po_code,a.article_code,a.plu,a.long_description 
            ,cast(qty as int) qty,a.do_code, c.name, convert(varchar(10),d.postingdate,120)postdate
            ,i.substitute,c.vendor_code,convert(varchar(10),d.postingdate,120)podate,m.Current_Price from t_do_detail a
            inner join t_po d on d.po_code = a.po_code
            inner join m_branch b on a.branch_id = b.branch_id
            inner join m_vendor c on c.vendor_code = a.supplier_code
            LEFT join item_katalog i on i.plu = a.plu
            inner join Item_Master m on m.PLU = a.plu
            where do_code = #$do# order by a.article_code asc";
        // sqlsrv_query( $conn, "delete from query_rpt where page = 4" );
        $result = sqlsrv_query( $conn, "exec dbo.sp_report '$sql','$user',3,''" );
    if($result){
        $json = array("status" => 1, "respone" => "berhasil");
        echo json_encode($json);
    }else{
        $json = array("status" => 0, "respone" => "gagal");
        echo json_encode($json);
    }
    // echo "insert into query_rpt select '$sql','$user',getdate(),4";
    //$conn = BukaKoneksi($us, $ps, $ip, $db);
    //$moduls   = cekget('moduls');
    
//   include 'export_listDO.php';

?>