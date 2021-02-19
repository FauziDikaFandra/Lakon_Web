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
    $sql = "select 'PENDING' as Status, convert(varchar(10),a.postingdate,120)tgl,a.branch_id,a.po_code , a.vendorname, sum(sisa_rc + sisa_rc) as pesanan 
            , sum(sisa_rc)sisa_pending  
            ,a.useradded from t_po a inner join t_po_detail b
            on a.po_code = b.po_code and b.sisa_rc <> 0
            inner join t_rc c on c.po_code = a.po_code
            group by convert(varchar(10),a.postingdate,120),a.branch_id,a.po_code , a.vendorname, a.useradded";

    $datas = array();
    $result = sqlsrv_query( $conn, $sql );
    if ($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $datas[] = array("Status" => $Status,"branch_id" => $branch_id, "po_code" => $po_code
                                ,"vendorname" => $vendorname, "pesanan" => $pesanan, "sisa_pending" => $sisa_pending
                                ,"useradded" => $useradded, "tgl" => $tgl);
        }
        $json = array("status" => 1, "respone" => $datas);
    }else{
        $json = array("status" => 0, "respone" => "Error");
    }
    // echo $sql;
    echo json_encode($json);

?>