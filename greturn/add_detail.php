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

    $request        = json_decode($postdata);
    $user           = trim(addslashes($request->user));
    $artikel        = trim(addslashes($request->artikel));
    $rc_code        = trim(addslashes($request->rc_code));
    $rt_code        = trim(addslashes($request->rt_code));
    $qty            = trim(addslashes($request->qty));
    $remark         = trim(addslashes($request->remark));
    $plu            = trim(addslashes($request->plu));
    $method         = trim(addslashes($request->method));

    if(!empty($rt_code)){
        if($method == "add"){
            $sql1 = "insert into t_rt_detail select '$rt_code','','','',PLU,Article_Code,Class,Burui,DP2,Supplier_Code,Description,Long_Description,Brand
                    ,$qty,'$user',GETDATE(),'','',''
                    from Item_Master where plu = '1000730700002'":
            //$sql2 = "update t_stok set out = out + $qty, receipt = receipt - $qty, last_stok = last_stok - $qty where plu = '$plu' and CONVERT(varchar(6),date,112) = CONVERT(varchar(6),getdate(),112)";
            sqlsrv_query($conn, $sql1);
            //sqlsrv_query($conn, $sql2);
            $json = array("status" => 1, "method" => $method ,"data" => $rt_code, "sql1" => $sql1, "sql2" => "sql2");
        }elseif($method == "edit"){
            $sql1 = "update t_rt_detail set qty = '$qty', remarks = '$remark', useradded = '$user', dateedited = GETDATE() where rt_code = '$rt_code' and article_code = '$artikel' and rc_code = '$rc_code' ";
            $sql2 = "";
            sqlsrv_query($conn, $sql1);
            $json = array("status" => 1, "method" => $method ,"data" => $rt_code, "sql1" => $sql1, "sql2" => $sql2);
        }
        
        
    }
    echo json_encode($json);
?>