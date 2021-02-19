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
    $plu            = trim(addslashes($request->plu));
    $article        = trim(addslashes($request->article));
    $qty            = trim(addslashes($request->qty));
    $user           = trim(addslashes($request->user));
    $typ            = trim(addslashes($request->typ));
    $whs            = trim(addslashes($request->whs));
    $from           = trim(addslashes($request->from));
    $to             = trim(addslashes($request->to));
    $remark         = trim(addslashes($request->remark));
    $adm            = trim(addslashes($request->adm));

    if(!empty($plu)){
        $sql = "insert into m_adjustment
                select getdate(), '$plu','$article',$qty,'$user','$typ','$whs','$from','$to','$remark','$adm'";
        $resultAdd = sqlsrv_query($conn, $sql);
        if($resultAdd){
            if ($adm == '1'){
                $exc = "exec adjust '$typ',$qty,'$plu'";
                $resultUpd = sqlsrv_query($conn, $exc);
            }else{
                $exc = "exec adjustment '$typ',$qty,'$plu'";
                $resultUpd = sqlsrv_query($conn, $exc);
            }
            
        }

        $json = array("status" => 1, "data" => $plu, "sql" => $sql);
        
    }
    echo json_encode($json);
?>