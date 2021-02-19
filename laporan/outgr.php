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
    $tgl1 = cekget('tgl1');
    $tgl2 = cekget('tgl2');
    $po   = cekget('po');

    $filter = "";
    if($po != "undefined"){
        if(strlen($po) > 0){
            $filter = " and a.po_code = '$po' ";
        }else{
            $filter = "";
        }
    }

    $sql = "select x.*,y.qtypo,userpo from
            (select a.po_code,a.rc_code,convert(varchar(10),c.postingdate,120)PO_date,CONVERT(varchar(10),a.postingdate,120)GR_date
            ,b.brand,SUM(b.qty)qty,a.useradded from t_rc a
            inner join t_rc_detail b on a.rc_code = b.rc_code
            inner join t_po c on c.po_code = a.po_code
            where a.status = 'POST'
            and CONVERT(varchar(10),a.postingdate,120) between '$tgl1' and '$tgl2'
            $filter
            and a.branch_id = 'S012'
            group by a.po_code,a.rc_code,convert(varchar(10),c.postingdate,120),CONVERT(varchar(10),a.postingdate,120)
            ,b.brand,a.useradded
            )x inner join 
            (select a.po_code,a.useradded userpo,SUM(b.qty)qtypo from t_po a
            inner join t_po_detail b on a.po_code = b.po_code group by a.po_code,a.useradded)y
            on x.po_code = y.po_code
            where x.qty <> y.qtypo";
    $datas = array();
    $result = sqlsrv_query( $conn, $sql );
    if($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $datas[] = array("po_code" => $po_code,"rc_code" => $rc_code, "po_date" => $PO_date, "gr_date" => $GR_date, "brand" => $brand
                            ,"qty" => $qty,"qtypo" => $qtypo,"usergr" => $useradded, "userpo" =>$userpo);
        }
        $json = array("status" => 1, "respone" => $datas);
        
    }else{
        $json = array("status" => 0, "respone" => null);
    }
    
    // echo $sql;
    echo json_encode($json);
?>  