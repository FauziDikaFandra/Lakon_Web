<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    include_once "../../fungsi.php";
    include_once "../../lakonsecurity.php";
    ini_set('max_execution_time',   300);
    $database   = include('../varLakon.php');
    $username   = cekget('username');
    $groupname  = cekget('groupname');
    $vendor_code= cekget('vendor_code');

    $table      = cekget('table');
    $column     = cekget('column');
    $value      = cekget('value');

    $long_description  = cekget('long_description');

    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];

    $outp0 = array();

    $outp0 = getJson($ip, $db, $us, $ps);

    // $json = array("status" => "1",
    //               "modules" => $outp0);
    //echo "tes\n";
    $json = array("data" => $outp0);
    //echo $outp0;
    // $json = array("data" => $outp0);
	  echo json_encode($json);
    //echo "tes2\n";

    function getJson($ip, $db, $us, $ps) {
        $no   = 1;
        $out  = array();
        $user = $GLOBALS['username'];
        $gname= $GLOBALS['groupname'];
        $ldesc= $GLOBALS['long_description'];
        $vcode= $GLOBALS['vendor_code'];

        $tbl  = $GLOBALS['table'];
        $clm  = $GLOBALS['column'];
        $vlu  = $GLOBALS['value'];

        $conn = BukaKoneksi($us, $ps, $ip, $db);
        $vend = getVendorLakon($gname, $user, "", "item_master");

        if ($tbl=="") {
            $sy = " (0=0) ";
        } else {
            $sy = " ( im.plu in (select plu from $tbl where $clm='$vlu') ) ";
        }

        $sql  = "Select top 25 im.plu, im.long_description, ik.substitute, isnull(im.Current_Price,0)price
                 from item_master im
                 left join item_katalog ik on im.article_code=ik.article_code and im.supplier_code=ik.cardcode
                 where im.burui not in ('NMD92ZZZ9','NMD31ZZZ9','NMD96ZZZ9','NMD98ZZZ9')
                 --and Right(im.burui,1) = '1'
                 and (im.long_description like '%$ldesc%' or im.plu like '$ldesc%')
                 and im.PLU NOT IN ('9000013100002','9000063500005')
                 and (im.description <> 'TIDAK AKTIF') and
                 im.supplier_code='$vcode' and
                 $vend and $sy
                 order by im.long_description";
        // echo "SQL : " . $sql . "\n";
        $result = sqlsrv_query( $conn, $sql );
        if ($result) {
          $out = array();
          // echo "tes\n";
          while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            // echo $long_description;
            $out[] = array("plu" => $plu, "long_description" => $long_description, "substitute" => $substitute, "price" => (int)$price);
            $no++;
          }
        } else {
          $out[] = array("plu" => "", "long_description" => "", "substitute" => "", "price"=>0);
        }
        sqlsrv_close($conn);
        // echo $sql;
        return $out;
    }
?>
