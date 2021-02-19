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
            ,qty              
            from t_do_detail a              
            inner join m_branch b on a.branch_id = b.branch_id              
            where do_code = #$do#";
        $result = sqlsrv_query( $conn, "exec dbo.sp_report '$sql','$user',2,''" );
    $out = "";
    if($result){
        $out = array("status" => "1");
        echo json_encode($out);
    }else{
        $out = array("status" => "0");
        echo json_encode($out);
    }
    // echo "insert into query_rpt select '$sql','$user',getdate(),4";
    //$conn = BukaKoneksi($us, $ps, $ip, $db);
    //$moduls   = cekget('moduls');
  include 'export_listDO.php';

?>