<?php
header('Access-Control-Allow-Origin: *');
include_once "../../fungsi.php";
include_once "../../lakonsecurity.php";
    ini_set('max_execution_time',   300);
    if(isset($_GET['branch'])){
        $branch = $_GET['branch'];
    };
    if($branch == "LAKON BALI"){
        $database = include('../varLakon_bali.php');
    }else{
        $database    = include('../varLakon.php');
    }

    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];
    $tipe = cekget('tipe');
    $brand = cekget('brand');
    $brand = str_replace("^","&",$brand);
    $conn = BukaKoneksi($us, $ps, $ip, $db);
    if($tipe == "1"){
      $sql = "select ROW_NUMBER() OVER(PARTITION BY brand ORDER BY brand ASC) AS id,
            Brand,Long_Description,b.Current_Price,a.last_stok from t_stok a
            inner join Item_Master b on a.plu = b .PLU
            where CONVERT(varchar(6),date,112) = CONVERT(varchar(6),getdate(),112)
            and b.brand = '$brand'";
    }else{
      $sql = "select ROW_NUMBER() OVER(PARTITION BY brand ORDER BY brand ASC) AS id,
            Brand,Long_Description,b.Current_Price,a.last_stok from t_stok a
            inner join Item_Master b on a.plu = b .PLU
            where CONVERT(varchar(6),date,112) = CONVERT(varchar(6),getdate(),112)
            and a.plu = '$brand'";
    }
        $datas = array();
        $result = sqlsrv_query( $conn, $sql );
        if ($result){
            while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
                extract($row);
                $datas[] = array("id" => $id,"brand" => $brand, "desc" => $Long_Description,"harga"=>$Current_Price, "stok"=>$last_stok);
            }
            $json = array("status" => 1, "respone" => $datas);
        }else{
            $json = array("status" => 0, "respone" => "Error");
        }
        // echo $sql;
    echo json_encode($json);

?>