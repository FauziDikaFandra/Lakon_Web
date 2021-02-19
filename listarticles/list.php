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
    if(!empty($user)){
        $sql = "select top 100 im.plu,im.burui,supplier_code, im.article_code,Long_Description, Brand,Current_Price, b.name, last_stok 
                    , substitute, y.price as purch from item_master as im inner join m_vendor b on im.Supplier_Code = b.vendor_code 
                    inner join t_stok x on x.plu = im.plu 
                    left join item_katalog y on y.plu = im.plu
                    where description <> 'TIDAK AKTIF' order by Last_Update desc";

        $datas = array();
        $result = sqlsrv_query( $conn, $sql );
        if ($result){
            while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
                extract($row);
                $datas[] = array("plu" => $plu,"article_code" => $article_code, "Long_Description" => $Long_Description
                                    ,"Brand" => $Brand, "Current_Price" => $Current_Price, "name" => $name
                                    ,"burui" => $burui, "supplier_code" => $supplier_code, "last_stok" => $last_stok
                                    ,"substitute" => $substitute, "purch" => $purch);
            }
            $json = array("status" => 1, "respone" => $datas);
        }else{
            $json = array("status" => 0, "respone" => "Error");
        }
    }
    // echo $sql;
    echo json_encode($json);

?>