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
    $rt   = cekget('rt');
    $plu   = cekget('plu');
    $qty  = cekget('qty');
    $rmk = cekget('rmk');

    if(!empty($plu)){
        $cekplu = "select count(*)dt from t_rt_detail where rt_code = '$rt' and plu = '$plu'";
        $result = sqlsrv_query($conn, $cekplu);
        if($result){
            $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC);
            if($row['dt'] == 1){
                $sql1 = "update t_rt_detail set [qty] = $qty, remarks = '$rmk' where plu = '$plu' and rt_code = '$rt'";
                // $sql2 = "update t_stok set [out] = [out] + $qty, last_stok = last_stok - $qty where plu = '$plu' and CONVERT(varchar(6),date,112) = CONVERT(varchar(6),getdate(),112)";
            }else{
                $sql1 = "insert into t_rt_detail select '$rt','','','',PLU,Article_Code,Class,Burui,DP2,Supplier_Code,Description,Long_Description,Brand
                            ,$qty,'$user',GETDATE(),'','','$rmk'
                            from Item_Master where plu = '$plu'";
                // $sql2 = "update t_stok set out = out + $qty, last_stok = last_stok - $qty where plu = '$plu' and CONVERT(varchar(6),date,112) = CONVERT(varchar(6),getdate(),112)";
            }
            $result1 = sqlsrv_query($conn, $sql1);
            //echo  $sql1;
            //if($result1 ){
                $list = "select b.substitute as katalog,rt_code,a.plu,brand,long_description,a.article_code,a.supplier_code,qty from t_rt_detail a
                        left join item_katalog b on a.plu = b.plu
                        where a.rt_code = '$rt'";
                $list_result = sqlsrv_query($conn, $list);
                if($list_result){
                    while( $row = sqlsrv_fetch_array( $list_result, SQLSRV_FETCH_ASSOC) ) {
                        extract($row);
                        $datas[] = array("katalog" => $katalog,"rt_code" => $rt_code, "plu" => $plu, "brand" => $brand
                            ,"long_description" => $long_description,"article_code" => $article_code, "qty" => $qty
                            ,"supplier_code" => $supplier_code);
                    }
                }
                $json = array("status" => 1, "respone" => $datas);
            //  }
            // $json = array("status" => 1, "respone" => "Gagal");
        } 
    }
    echo json_encode($json);
?>