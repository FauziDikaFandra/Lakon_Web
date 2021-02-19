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
    $po   = cekget('po');
    // $param = getLakonSecurity($user,$modul,'im','dp2');

    // $sql = "select convert(varchar(10),receiptdate,120) as dates, rc_code
    //         , po_code, do_code, vendor_code,Vendorname, useradded,branchName from t_rc where status = 'POST'
    //         and po_code = '$po'";
    if($modul == '10'){
    $sql = "select '' as dates,'' as rc_code, '' as po_code 
            ,brand,Long_description as supplier_code,a.plu,name as vendorname,'' as qty
            ,last_stok as last_stok,'' as rmk
            from item_master a
            inner join m_vendor b on a.supplier_code = b.vendor_code
            inner join t_stok c on c.plu = a.plu and convert(varchar(6),date,112) =  convert(varchar(6),getdate(),112) 
            where a.plu = '$po'";
    }else{
    $sql = "select '' dates, '' rc_code,'' po_code,c.brand,
            c.Long_Description as supplier_code,c.plu,vendorname,sum(b.qty)qty,max(last_stok)last_stok
            ,max(x.remarks) rmk from t_rc a
            inner join t_rc_detail b on a.rc_code = b.rc_code
            inner join Item_Master c on c.PLU = b.plu
            left join t_rt_detail x on x.plu = c.plu and x.rt_code = '$modul'
            left join t_stok d on d.plu = b.plu and CONVERT(varchar(6),d.date,112) = CONVERT(varchar(6),GETDATE(),112)
            where a.status = 'POST' and a.branch_id= 'S012'
            and b.plu = '$po'
            group by c.brand,
            c.Long_Description ,c.PLU,vendorname";
    }
    $datas = array();
    $result = sqlsrv_query( $conn, $sql );
    if($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $datas[] = array("dates" => $dates,"rc_code" => $rc_code, "po_code" => $po_code, "supplier_code" => $supplier_code
                            ,"plu" => $plu,"vendorname" => $vendorname, "qty" => $qty, "last_stok" => $last_stok
                            ,"brand" => $brand,"rmk" => $rmk);
        }
        $json = array("status" => 1, "respone" => $datas);
    }else{
        $json = array("status" => 0, "respone" => null);
    }
    // echo $sql;
    echo json_encode($json);
?>