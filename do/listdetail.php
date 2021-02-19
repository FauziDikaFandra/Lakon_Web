<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    include_once "../../fungsi.php";
    include_once "../../lakonsecurity.php";
    ini_set('max_execution_time',   300);
    $username   = cekget('username');
    $groupname  = cekget('groupname');
    $id         = cekget('id');

    $database   = include('../varLakon.php');
    $ip         = $database['host'];
    $db         = $database['db'];
    $us         = $database['user'];
    $ps         = $database['pass'];

    $totalrc  = 0;
    $outp0 = array();
    $outp0 = getJson($ip, $db, $us, $ps);

    // $json = array("status" => "1",
    //               "modules" => $outp0);

    $json     = array("draw" => 0, "recordsTotal" => $totalrc, "recordsFiltered" => $totalrc, "data" => $outp0);
    // $json = array("data" => $outp0);
	  echo json_encode($json);

    function getJson($ip, $db, $us, $ps) {
        $no   = 0;
        $out  = array();
        $user = $GLOBALS['username'];
        $gname= $GLOBALS['groupname'];
        $id   = $GLOBALS['id'];

        $conn = BukaKoneksi($us, $ps, $ip, $db);
        $sql  = "Select a.branch_id, branchname, do_code, po_code, substitute, a.plu, a.article_code, a.class, a.burui, b.Current_Price as dp2, a.supplier_code,
                 a.description, a.long_description, a.brand, qty
                 from t_do_detail a
                 inner join Item_Master b on a.plu = b.PLU
                 where do_code='$id'
                 order by long_description";
        // echo "SQL : " . $sql . "\n";
        $result = sqlsrv_query( $conn, $sql );
        if ($result) {
          $out = array();
          while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            $no++;
            extract($row);
            $out[] = array("branch_id" => $branch_id, "branchname" => $branchname,
                           "do_code" => $do_code, "po_code" => $po_code, "substitute" => $substitute,
                           "plu" => $plu, "article_code" => $article_code,
                           "class" => $class, "burui" => $burui, "dp2" => $dp2,
                           "supplier_code" => $supplier_code, "description" => $description,
                           "long_description" => $long_description, "brand" => $brand,
                           "qty" => (int)$qty);
          }
        } else {
          $out[] = array("branch_id" => "", "branchname" => "",
                         "do_code" => "", "po_code" => "", "substitute" => "", "plu" => "", "article_code" => "",
                         "class" => "", "burui" => "", "dp2" => "",
                         "supplier_code" => "", "description" => "",
                         "long_description" => "", "brand" => "",
                         "qty" => 0);
        }

        // echo $sql ;
        sqlsrv_close($conn);
        $GLOBALS['totalrc'] = $no;
        return $out;
    }


?>
