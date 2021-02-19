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

    $sql = "select x.* from
            (
            select a.po_code,CONVERT(varchar(10),postingdate,120)tglbuat,b.brand,vendorname,SUM(b.qty)qty, a.useradded 
            ,postingdate,a.remarks  from t_po a inner join t_po_detail b on a.po_code = b.po_code
            where a.branch_id = 'S012' and a.status not in ('OPEN','BLOCK')
            and CONVERT(varchar(10),postingdate,120) between  '$tgl1' and '$tgl2'  $filter
            group by a.po_code,CONVERT(varchar(10),postingdate,120),b.brand,vendorname, a.useradded,postingdate
            ,a.remarks
            )x left join t_rc y on x.po_code = y.po_code
            where y.po_code is null 
            order by postingdate desc";
    $datas = array();
    $result = sqlsrv_query( $conn, $sql );
    if($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $datas[] = array("po_code" => $po_code,"tglbuat" => $tglbuat, "brand" => $brand, "vendorname" => $vendorname, "qty" => $qty
                            ,"user" => $useradded,"remark" => $remarks);
        }
        $json = array("status" => 1, "respone" => $datas);
        
    }else{
        $json = array("status" => 0, "respone" => null);
    }
    
    // echo $sql;
    echo json_encode($json);
?>  