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
    $desc   = cekget('desc');
    $sql  = "select a.plu,b.Long_Description,b.Current_Price as price,a.last_stok,b.brand
            ,sales,refund,receipt,(transferIn + [in])-([out]+transferOut) as adj
            from t_stok a
            inner join Item_Master b on a.plu = b.PLU
            where b.Description <> 'TIDAK AKTIF'
            and CONVERT(VARCHAR(6),date,112) = CONVERT(VARCHAR(6),GETDATE(),112)
            and (long_description like '%$desc%' or a.plu like '%$desc%')";

    $datas = array();
    $result = sqlsrv_query( $conn, $sql );
    if ($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $datas[] = array("plu" => $plu, "Long_Description" => $Long_Description
            ,"price" => (int)$price, "last_stok"=> $last_stok, "brand"=> $brand, "sales" => $sales, "refund" => $refund
            ,"receipt" => $receipt, "adj" => $adj);
        }
        $json = array("status" => 1, "respone" => $datas);
    }else{
        $json = array("status" => 0, "respone" => "Error");
    }
    // echo $sql;
    echo json_encode($json);

?>