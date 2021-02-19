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
    $do = cekget('do');
    // $modul = cekget('modul');
    // $param = getLakonSecurity($user,$modul,'im','dp2');
    if(!empty($do)){
        $sql = "select ROW_NUMBER() OVER (ORDER BY (SELECT 1)) AS nomer,a.do_code, a.po_code,a.branchname as toko, CONVERT(varchar(10),a.deliverydate,120) kirim 
                , brand, substitute as katalog, a.vendorname, a.vendor_code,article_code, long_description, qty, a.useradded as [user], a.status
                , convert(varchar(10),c.postingdate,120) po_date
                from t_do a inner join t_do_detail b
                on a.po_code = b.po_code and a.do_code = b.do_code
                inner join t_po c on c.po_code = a.po_code
                where a.do_code = '$do'";
        $datas = array();
        $result = sqlsrv_query( $conn, $sql );
        if ($result){
            while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
                extract($row);
                $datas[] = array("nomer" => $nomer,"do_code" => $do_code,"po_code" => $po_code,"user" => $user, "toko" => $toko,"article_code" => $article_code
                                ,"long_description" => $long_description, "qty" => $qty, "status" => $status, "kirim" => $kirim
                                ,"brand" => $brand, "katalog" => $katalog, "name" => $vendorname, "vendor_code" => $vendor_code, "po_date" =>$po_date );
            }
            $json = array("status" => 1, "respone" => $datas);
        }else{ 
            $json = array("status" => 0, "respone" => "Error");
        }
    }
    // echo $sql;
    echo json_encode($json);

?>