<?php
    header('Access-Control-Allow-Origin: *');
    include_once "../../fungsi.php";
    ini_set('max_execution_time',   300);
    $database   = include('../varLakon.php');
    $ip         = $database['host'];
    $db         = $database['db'];
    $us         = $database['user'];
    $ps         = $database['pass'];

    $conn = BukaKoneksi($us, $ps, $ip, $db);
    $postdata     = file_get_contents("php://input");

    $request      = json_decode($postdata);
    $user            = trim(addslashes($request->user));
    // $article        = trim(addslashes($request->article));
    // $qty            = trim(addslashes($request->qty));
    // $user           = trim(addslashes($request->user));
    // $typ            = trim(addslashes($request->typ));
    // $whs            = trim(addslashes($request->whs));
    // $from           = trim(addslashes($request->from));
    // $to             = trim(addslashes($request->to));
    // $remark         = trim(addslashes($request->remark));
    // $adm            = trim(addslashes($request->adm));

    if(!empty($user)){
        $kode_adj = "";
        $kode = "select dbo.adj_kode() as kode";
        $result_kode = sqlsrv_query( $conn, $kode );
        while( $row = sqlsrv_fetch_array( $result_kode, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            $kode_adj = $kode;
        }
        $sql = "insert into t_adjustment
                select GETDATE(),'$kode_adj',0,'$user','S012','','OPEN',null,null";
        $resultAdd = sqlsrv_query($conn, $sql);
        if($resultAdd){
            $json = array("status" => 1, "data" => $kode_adj, "sql" => $sql);
        }else{
            $json = array("status" => 0, "data" =>  $kode_adj, "sql" => $sql);
        }
    }
    echo json_encode($json);
?>