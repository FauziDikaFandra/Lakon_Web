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
    $rt_code    = cekget('rt_code');
    $param      = cekget('param');
    $rmk        = cekget('rmk');

    $sql = "";
    if($param == "post"){
        $sql = "update t_rt set status = 'POST', remarks = '$rmk',postingdate = getdate() where rt_code = '$rt_code'";
        $sql2 = "update a set [out] = [out] + b.qty , last_stok = last_stok - b.qty
                from t_stok a inner join t_rt_detail b
                on a.plu = b.plu where b.rt_code = '$rt_code'
                and CONVERT(varchar(6),date,112) = CONVERT(varchar(6),GETDATE(),112)";
        
    }elseif($param == "delete"){
        $sql = "delete from t_rt where rt_code = '$rt_code'; delete from t_rt_detail where rt_code = '$rt_code'";
    }else{
        $sql = "update a set receiptdate = convert(varchar(10),getdate(),120), remarks = '$param' from t_rt a where rt_code = '$rt_code '";
    }

    $datas = array();
    if($param == "post" || $param == "POST"){
        $sqlcek = "select status from t_rt where rt_code = '$rt_code'";
        $hasilcek = sqlsrv_query($conn,$sqlcek);
        $rowcek = sqlsrv_fetch_array( $hasilcek, SQLSRV_FETCH_ASSOC);
        if($rowcek['status'] != "POST"){
            $result = sqlsrv_query( $conn, $sql );
            $result2 = sqlsrv_query( $conn, $sql2 );
            $datas[] = array("status" => "Berhasil");
        }else{
            $datas[] = array("status" => "Gagal");
            $result = sqlsrv_query( $conn, "" );
        }
    }elseif($param == "delete" || $param == "DELETE"){
        $sqlcek = "select status from t_rt where rt_code = '$rt_code'";
        $hasilcek = sqlsrv_query($conn,$sqlcek);
        $rowcek = sqlsrv_fetch_array( $hasilcek, SQLSRV_FETCH_ASSOC);
        if($rowcek['status'] != "POST"){
            $result = sqlsrv_query( $conn, $sql );
            $datas[] = array("status" => "Berhasil");
        }else{
            $datas[] = array("status" => "Gagal");
            $result = sqlsrv_query( $conn, "" );
        }
        
    }else{
        $result = sqlsrv_query( $conn, $sql );
    }

    if($result){
        $json = array("status" => 1, "respone" =>  $datas);
    }else{
        $data[] = array("status" => "Gagal");
        $json = array("status" => 0, "respone" =>  $datas);
    }
    // echo $sql;
    echo json_encode($json);
?>