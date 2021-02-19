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
    $po   = cekget('po');
    $user = cekget('user');
    $group = cekget('group');

    
    $sql = "select ROW_NUMBER() OVER (ORDER BY (SELECT 1)) AS nomer, a.po_code, convert(varchar(10),a.dateadded,120) as dibuat
            , c.name,b.article_code,b.long_description, b.qty, x.Current_Price price , b.plu,
            convert(varchar(10),a.postingdate,120)postingdate
            ,b.brand,a.useradded as dibuat,i.substitute ,b.discountpct, a.vendor_code,m.name as toko from t_po a
            inner join t_po_detail b on a.po_code = b.po_code
            inner join m_vendor c on c.vendor_code = a.vendor_code
            inner join m_branch m on m.branch_id = a.branch_id 
            left join item_katalog i on i.plu = b.plu
            inner join Item_Master x on x.PLU = b.plu
            where a.po_code = #$po# order by b.article_code asc";
        // sqlsrv_query( $conn, "delete from query_rpt where page = 5" );
        $result = sqlsrv_query( $conn, "exec sp_report '$sql','$user',2,''" );
    $out = "";
    if($result){
        if($group == "2"){
            // $upd = "update t_po set status = 'CLOSE' where po_code = '$po'";
            // $result_upd = sqlsrv_query( $conn, $upd);
            // if($result_upd){
            //     $out = array("status" => "1");
            //     echo json_encode($out);
            // }else{
            //     $out = array("status" => "0");
            //     echo json_encode($out);
            // }
            $out = array("status" => "1");
                echo json_encode($out);
        }else{
            $out = array("status" => "1");
            echo json_encode($out);
        }
        
    }else{
        $out = array("status" => "0");
        echo json_encode($out);
    }
    // echo "insert into query_rpt select '$sql','$user',getdate(),4";
    //$conn = BukaKoneksi($us, $ps, $ip, $db);
    //$moduls   = cekget('moduls');
    
//   include 'export_listDO.php';

?>