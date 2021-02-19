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
    $po = cekget('po');
    // $modul = cekget('modul');
    // $param = getLakonSecurity($user,$modul,'im','dp2');


    if(!empty($po)){
        $sql = "select ROW_NUMBER() OVER (ORDER BY (SELECT 1)) AS nomer, a.po_code, convert(varchar(10),a.dateadded,120) as dibuat, c.name,article_code,long_description, b.qty, b.price, convert(varchar(10),a.postingdate,120)postingdate
                ,a.useradded as dibuat, b.discountpct, a.vendor_code,m.name as toko from t_po a
                inner join t_po_detail b on a.po_code = b.po_code
                inner join m_vendor c on c.vendor_code = a.vendor_code
                inner join m_branch m on m.branch_id = a.branch_id 
                where a.po_code = '$po'";
        $datas = array();
        $result = sqlsrv_query( $conn, $sql );
        if ($result){
            while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
                extract($row);
                $datas[] = array("nomer" => $nomer, "po_code" => $po_code,"user" => $user, "article_code" => $article_code
                                ,"long_description" => $long_description, "qty" => $qty, "price" => $price, "postingdate" => $postingdate
                                ,"dibuat" => $dibuat, "discountpct" => $discountpct, "name" => $name, "vendor_code" => $vendor_code,"toko" => $toko );
            }
            $json = array("status" => 1, "respone" => $datas);
        }else{
            $json = array("status" => 0, "respone" => "Error");
        }
    }
    // echo $sql;
    echo json_encode($json);

?>