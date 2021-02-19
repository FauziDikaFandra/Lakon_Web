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
    //$user   = cekget('user');
    $kode = cekget('kode');
    $plu = cekget('plu');
    $filter = "";
    if($plu != ""){
        $filter = " and a.plu = '$plu' ";
    }

    $sql = "select a.plu,b.Long_Description as desc_,b.Current_Price,a.qty,a.type,c.status,d.last_stok,b.brand,a.remarks from t_adjustment_details a
            inner join Item_Master b on a.plu = b.PLU inner join t_adjustment c on c.adj_kode = a.adj_kode 
            inner join t_stok d on d.plu = b.PLU and CONVERT(varchar(6),d.date,112) = CONVERT(varchar(6),GETDATE(),112)
            where a.adj_kode = '$kode' $filter";
    $datas = array();
    $result = sqlsrv_query( $conn, $sql );
    if ($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $datas[] = array("plu" => $plu ,"long_desc" => $desc_, "status" => $status,"brand"=>$brand
            , "Current_Price" => $Current_Price, "qty" => $qty, "type" => $type, "last_stok" => $last_stok
            , "remarks" => $remarks);
        }
        $json = array("status" => 1, "respone" => $datas);
    }else{
        $json = array("status" => 0, "respone" => "Error");
    }
    // echo $sql;
    echo json_encode($json);
    
?>