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
    $plu   = cekget('plu');
    $action = cekget('action');
    $sql  = "";
    if($action == "add"){
        $sql = "insert into t_stok_online
                select *,GETDATE() from t_stok where plu = '$plu' 
                and CONVERT(varchar(6),date,112) = CONVERT(varchar(6),GETDATE(),112)";
    }else if($action == "del"){
        $sql = "delete from t_stok_online where plu = '$plu' and CONVERT(varchar(6),date,112) = CONVERT(varchar(6),GETDATE(),112)";
    }
    $datas = array();
    $result = sqlsrv_query( $conn, $sql );
    if ($result){
        $sel = "select *,(transferIn + [in])-([out]+transferOut) as adj,b.brand as brands,replace(convert(varchar,Current_Price,1),'.00','') harga 
        from t_stok_online a inner join item_master b on a.plu = b.plu
         where CONVERT(varchar(6),createdate,112) = CONVERT(varchar(6),GETDATE(),112) order by createdate desc";
        $result_sel = sqlsrv_query($conn, $sel);
        if($result_sel){
            while( $row = sqlsrv_fetch_array( $result_sel, SQLSRV_FETCH_ASSOC) ) {
                extract($row);
                $datas[] = array("plu" => $plu, "Long_Description" => $Long_Description
                ,"price" => $harga, "last_stok"=> $last_stok, "brands"=> $brands, "sales" => $sales, "refund" => $refund
                ,"receipt" => $receipt, "adj" => $adj);
            }
        }
        $json = array("status" => 1, "respone" => $datas);
        // $json = array("status" => 1, "respone" => "ok");
    }else{
        $json = array("status" => 0, "respone" => "Error");
    }
    // echo $sql;
    echo json_encode($json);

?>