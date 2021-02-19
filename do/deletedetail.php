<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    include_once "../../fungsi.php";
    ini_set('max_execution_time',   300);
    $username   = cekget('username');
    $groupname  = cekget('groupname');
    $do_code    = cekget('do_code');
    $plu        = cekget('plu');

    $database   = include('../varLakon.php');
    $ip         = $database['host'];
    $db         = $database['db'];
    $us         = $database['user'];
    $ps         = $database['pass'];

    $conn = BukaKoneksi($us, $ps, $ip, $db);

    $sql        = "select po_code from t_do where do_code='$do_code' ";
    $po_code    = "";
    $result     = sqlsrv_query( $conn, $sql );
    if ($result) {
      while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_NUMERIC) ) {
        // $out = array("status" => 1, "value" => (int)$row[0], "sql" => $sql);
        $po_code    = $row[0];
      }
    } else {
        $po_code    = "";
    }

    sqlsrv_query( $conn, "exec delete_do '$do_code','$plu' " );

    if ($po_code != "") {
      $sql    = "exec dbo.fillQTYPO '$po_code'";
      $result = sqlsrv_query( $conn, $sql );

      $listdo = "Select branch_id, branchname, do_code, po_code, substitute, plu, article_code, class, burui, dp2, supplier_code,
                description, long_description, brand, qty
                from t_do_detail
                where do_code='$do_code'
                order by long_description";
      
      // echo $listdo;
      $result_list = sqlsrv_query( $conn, $listdo );
        if ($result_list){
            while( $row = sqlsrv_fetch_array( $result_list, SQLSRV_FETCH_ASSOC) ) {
                extract($row);
                $datas[] = array("branch_id" => $branch_id, "branchname" => $branchname,
                                "do_code" => $do_code, "po_code" => $po_code, "substitute" => $substitute,
                                "plu" => $plu, "article_code" => $article_code,
                                "class" => $class, "burui" => $burui, "dp2" => $dp2,
                                "supplier_code" => $supplier_code, "description" => $description,
                                "long_description" => $long_description, "brand" => $brand,
                                "qty" => (int)$qty);
            }
            $json = array("status" => 1, "data" => $datas);
        }else{
          $datas = null;
        }
    }else{
      $datas = null;
    }

    $json = array("status" => "1", "data" => $datas);
	  echo json_encode($json);
?>
