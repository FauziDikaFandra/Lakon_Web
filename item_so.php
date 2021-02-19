<?php
header('Access-Control-Allow-Origin: *');
include_once "../fungsi.php";
include_once "../lakonsecurity.php";
    ini_set('max_execution_time',   300);
    $database    = include('varLakon.php');
    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];
    
    $conn = BukaKoneksi($us, $ps, $ip, $db);
    $brands = cekget('brands');
    $brands = str_replace('\\','',$brands);

    if ($brands == ""){
        $sql = "select top 16000
        c.article_code,a.plu,substring(b.epc,1,13) as EPC,long_description,current_Price,burui,Brand,last_stok as stock 
        from t_stok a
        inner join epc_temp b on a.plu = b.plu
        inner join item_master c on c.plu = a.plu
        where convert(varchar(6),date,112) = convert(varchar(6),getdate(),112)
        and branch_id = 'S012' and Brand <> 'NO BRAND'
        and c.Description <> 'TIDAK AKTIF'
        order by a.plu ";
    }else{
        $sql = "select
        c.article_code,a.plu,substring(b.epc,1,13) as EPC,long_description,current_Price,burui,Brand,last_stok as stock 
        from t_stok a
        inner join epc_temp b on a.plu = b.plu
        inner join item_master c on c.plu = a.plu
        where convert(varchar(6),date,112) = convert(varchar(6),getdate(),112)
        and branch_id = 'S012' and Brand <> 'NO BRAND'
        and c.Description <> 'TIDAK AKTIF'
        and c.brand in ($brands)
        order by a.plu ";
    }
    

    // echo $sql;

    $result = sqlsrv_query( $conn, $sql );
    $data = Array();
    if($result){
        while($row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ){
            extract($row);
            $data[] = array("article_code"=> $article_code,"PLU"=> $plu ,"EPC" => $EPC, "long_description" => $long_description, "current_price" => $current_Price,"burui" =>$burui, "brand" => $Brand, "stock" => $stock);
        }
    $json = array("status"=>"1","Desc"=>"Berhasil","pesan"=>$data);
    }else{
        $json = array("status"=>"0","Desc"=>"Gagal","pesan"=>null);
    }
    // echo $sql;
    echo json_encode($json);
?>