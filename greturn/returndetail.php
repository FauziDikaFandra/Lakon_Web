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
    $rt_code = cekget('rt_code');
    $sql = "select status ,b.substitute as katalog,a.rt_code,a.plu,brand,long_description,a.article_code
            ,a.supplier_code,qty , c.useradded as usr,convert(varchar(10),postingdate,120) as post, d.name as vendor
            ,c.vendor_code
            from t_rt_detail a
            left join item_katalog b on a.plu = b.plu
            inner join t_rt c on c.rt_code = a.rt_code
            inner join m_vendor d on d.Vendor_Code = a.supplier_code
            where a.rt_code  = '$rt_code'";
    $datas = array();
    $result = sqlsrv_query( $conn, $sql );
    if($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $datas[] = array( "status"=>  $status ,"katalog" => $katalog,"rt_code" => $rt_code, "plu" => $plu, "brand" => $brand
                ,"long_description" => $long_description,"article_code" => $article_code, "qty" => $qty
                ,"supplier_code" => $supplier_code, "usr" => $usr,"post"=>$post,"vendor"=>$vendor,"doc"=>$vendor_code);
        }
        $json = array("status" => 1, "respone" => $datas);
    }else{
        $json = array("status" => 0, "respone" => null);
    }

    // echo $sql;
    echo json_encode($json);
?>