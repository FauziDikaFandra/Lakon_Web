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
    $modul  = cekget('modul');
    $head = cekget('head');
    $group = cekget('group');
    $brand = cekget('brand');
    $date1 = cekget('date1');
    $date2 = cekget('date2');
    $branch = cekget('branch');
    $doc = cekget("doc");

    $filter = "";
    if($doc != "undefined"){
        if(strlen($doc) == 0){
            $filter = "";
        }else{
            $filter = " and a.vendor_code = '$doc' ";
        }
    };
    //$param = getLakonSecurity($user,$modul,'im','dp2');
    if($head == "header"){
        if($group == "2"){
            $sql = "select a.rt_code, a.rc_code, a.po_code, isnull(c.name,'-')name, a.vendor_code,isnull(convert(varchar(10),receiptdate,120),'-') receiptdate,isnull(a.remarks,'-')remarks, status, a.useradded, sum(qty)qtyAll from t_rt a
                inner join t_rt_detail b on a.rt_code = b.rt_code
                inner join m_vendor c on c.vendor_code = b.supplier_code 
                where status = 'POST' and c.vendor_code = '$user' $filter
                group by a.rt_code, a.rc_code, a.po_code,  a.vendor_code,c.name, receiptdate,a.remarks, status, a.useradded ,a.dateadded
                order by vendor_code desc";
        }else{
            // and d.brand = '$brand'
            $sql = "select a.rt_code, a.rc_code, a.po_code, isnull(c.name,'-')name, a.vendor_code,isnull(convert(varchar(10),postingdate,120),'-') receiptdate,isnull(a.remarks,'-')remarks
                    , status, a.useradded, sum(isnull(qty,0))qtyAll from t_rt a
                    left join t_rt_detail b on a.rt_code = b.rt_code
                    left join m_vendor c on c.vendor_code = b.supplier_code
                    left join Item_Master d on d.PLU = b.plu
                    where CONVERT(varchar(10),a.dateadded,120) between '$date1' and '$date2' $filter
                    group by a.rt_code, a.rc_code, a.po_code, a.vendor_code, c.name, postingdate,a.remarks, status, a.useradded,a.dateadded
                    order by vendor_code desc";
        }
        
    }else{
        $sql = "";
    }
    
    $datas = array();
    $result = sqlsrv_query( $conn, $sql );
    if($result){
        if($head == "header"){
            while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $datas[] = array("rt_code" => $rt_code,"rc_code" => $rc_code, "po_code" => $po_code, "name" => $name, "terima" => $receiptdate
                            ,"remarks" => $remarks, "status" => $status, "useradded" => $useradded, "qtyAll" => $qtyAll,"vendor_code" =>$vendor_code);
        }
        }else{

        }
        
        $json = array("status" => 1, "respone" => $datas);
    }else{
        $json = array("status" => 0, "respone" => null);
    }
    // echo $sql;
    echo json_encode($json);
?>