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
    $brand  = cekget('brand');
    $plu  = cekget('plu');

    $filter_brand = "";
    $filter_plu = "";
    if($brand == "" or $brand == "undefined"){
        $filter_brand = "";
    }elseif($brand == "all"){
        $filter_brand = " ";
    }else{
       $filter_brand = " and b.Brand = '$brand'";
    };

    if($plu == "" or $plu == "undefined" ){
        $filter_plu = "";
    }else{
        $filter_plu = " and b.plu = '$plu' ";
    };

    // echo $plu;

    $brand = str_replace("^","&",$brand);
    
    $sql = "select a.plu,b.Article_Code,b.Brand,b.Long_Description,''spp,replace(convert(varchar,cast(b.Current_Price as money),1),'.00','') Current_Price,last_stok,''img from t_stok_online a
            inner join Item_Master b on a.plu = b.PLU 
            where CONVERT(varchar(6),date,112) = CONVERT(varchar(6),GETDATE(),112) 
            and description <> 'TIDAK AKTIF'
            $filter_brand $filter_plu
            ";
    $datas = array();
    $result = sqlsrv_query( $conn, $sql );
    if ($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $datas[] = array("plu" => $plu,"article_code" => $Article_Code, "brand" => $Brand
                                ,"description" => $Long_Description, "supplier" => $spp, "price" => $Current_Price,"stok" => $last_stok);
        }
        $json = array("status" => 1, "respone" => $datas);
    }else{
        $json = array("status" => 0, "respone" => "Error");
    }
    // echo $sql ;
    echo json_encode($json);

?>