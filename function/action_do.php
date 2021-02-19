<?php
header('Access-Control-Allow-Origin: *');
include_once "../../fungsi.php";
include_once "../../lakonsecurity.php";
header("Content-type: application/vnd-ms-excel");
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
    header("Content-Disposition: attachment; filename=$do.xls");

    
    $sql = "select b.name,a.brand,a.po_code,a.article_code,a.plu,a.long_description 
            ,qty
            from t_do_detail a
            inner join m_branch b on a.branch_id = b.branch_id
            where do_code = #$do# ";
        // sqlsrv_query( $conn, "delete from query_rpt where page = 2" );
        sqlsrv_query( $conn, "exec sp_report '$sql','$user',3,''" );

    // echo "insert into query_rpt select '$sql','$user',getdate(),2";
    //$conn = BukaKoneksi($us, $ps, $ip, $db);
    //$moduls   = cekget('moduls');
    
  include 'export_listDO.php';

?>