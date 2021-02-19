<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    include_once "../../fungsi.php";
    ini_set('max_execution_time',   300);
    $username   = cekget('username');
    $groupname  = cekget('groupname');
    $po_code    = cekget('po_code');

    $database   = include('../varLakon.php');
    $ip         = $database['host'];
    $db         = $database['db'];
    $us         = $database['user'];
    $ps         = $database['pass'];

    $sts    = "";
    $conn   = BukaKoneksi($us, $ps, $ip, $db);
    $sql    = "exec dbo.fillQTYPO '$po_code'";
    $result = sqlsrv_query( $conn, $sql );
    // if( $result === false )
    // {
    //     echo "Error in executing statement 3.\n";
    //     die( print_r( sqlsrv_errors(), true));
    // }
    $sql    = "select status from t_po where po_code='$po_code' ";
    $result = sqlsrv_query( $conn, $sql );
    if ($result) {
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
          extract($row);
          $sts = $status;
        }
    } else {
        $sts    = "";
    }

    $sisado = 0;
    $sql    = "select sum(sisa_do) as sisa_do from t_po_detail where po_code='$po_code' group by po_code";
    $result = sqlsrv_query( $conn, $sql );
    if ($result) {
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_NUMERIC) ) {
          $sisado = (int)$row[0];
        }

    } else {
        $sisado = 0;
    }

    $sql1       = "";
    $sql2       = "";
    $do_code    = "";
    if (strtolower($sts) =="post") {
        if ($sisado==0) {
            $sts    = "kosong";
        } else {
            $sql    = "select dbo.createDOByPO('$po_code') as nomor";
            $result = sqlsrv_query( $conn, $sql );
            if ($result) {
                $do_code = "";
                while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
                  extract($row);
                  $do_code = $nomor;
                }

                if ($do_code != "") {
                  $sql1 = "insert into t_do (branch_id, branchname, do_code, po_code, status, postingdate, deliverydate,
                          documentdate, vendor_code, vendorname, remarks, useradded, dateadded)
                          select branch_id, branchname, '$do_code' as do_code, po_code, 'OPEN', convert(varchar(10), getdate(), 20),
                          convert(varchar(10), getdate(), 20), convert(varchar(10), getdate(), 20),
                          vendor_code, vendorname, remarks, '$username', convert(varchar(10), getdate(), 20)
                          from t_po
                          where po_code='$po_code'";
                  sqlsrv_query( $conn, $sql1 );

                  $sql2 = "insert into t_do_detail (branch_id, branchname, do_code, po_code, substitute, plu, article_code,
                          class, burui, dp2, supplier_code, description, long_description, brand, qty,
                          useradded, dateadded)
                          select pd.branch_id, pd.branchname, '$do_code' as do_code, pd.po_code, pd.substitute, pd.plu, pd.article_code,
                          pd.class, pd.burui, pd.dp2, pd.supplier_code, pd.description, pd.long_description, pd.brand,
                          pd.qty - coalesce(dd.qty_do, 0) as qty_do,
                          '$username', convert(varchar(10), getdate(), 20)
                          from t_po_detail pd
                          left join (
                          	select branch_id, plu, po_code, sum(qty) as qty_do
                          	from t_do_detail
                          	where po_code='$po_code'
                          	group by branch_id, plu, po_code
                          ) dd on pd.branch_id=dd.branch_id and pd.plu=dd.plu and pd.po_code=dd.po_code
                          where pd.po_code='$po_code'";
                  sqlsrv_query( $conn, $sql2 );

                  $sql    = "exec dbo.fillQTYPO '$po_code'";
                  $result = sqlsrv_query( $conn, $sql );
                }
            } else {
                $sts    = "";
            }
        }
    }
    sqlsrv_close($conn);
    $out = array("status" => $sts, "po_code" => $po_code, "do_code" => $do_code, "sql1" => $sql1, "sql2" => $sql2);
    echo json_encode($out);
?>
