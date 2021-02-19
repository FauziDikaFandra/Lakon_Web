<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    include_once "../../fungsi.php";
    ini_set('max_execution_time',   300);
    $username   = cekget('username');
    $groupname  = cekget('groupname');
    $do_code    = cekget('do_code');

    $database   = include('../varLakon.php');
    $ip         = $database['host'];
    $db         = $database['db'];
    $us         = $database['user'];
    $ps         = $database['pass'];

    $sts    = "";
    $po_code= "";
    $conn   = BukaKoneksi($us, $ps, $ip, $db);

    // if( $result === false )
    // {
    //     echo "Error in executing statement 3.\n";
    //     die( print_r( sqlsrv_errors(), true));
    // }
    $sql    = "select status, po_code as code from t_do where do_code='$do_code' ";
    $result = sqlsrv_query( $conn, $sql );
    if ($result) {
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
          extract($row);
          $sts    = $status;
          $po_code= $code;
        }
    } else {
        $sts    = "";
        $po_code= "";
    }

    if ($po_code != "") {
      $sql    = "exec dbo.fillQTYPO '$po_code'";
      $result = sqlsrv_query( $conn, $sql );
    }

    $sisarc = 0;
    $sql    = "select sum(sisa_rc) as sisa_rc from t_po_detail where po_code='$po_code' group by po_code";
    $result = sqlsrv_query( $conn, $sql );
    if ($result) {
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_NUMERIC) ) {
          $sisarc = (int)$row[0];
        }

    } else {
        $sisarc = 0;
    }

    $sql1       = "";
    $sql2       = "";
    $rc_code    = "";
    if (strtolower($sts) =="post") {
        if ($sisarc==0) {
            $sts    = "kosong";
        } else {
            $sql    = "select dbo.createRCByDO('$do_code') as nomor";
            $result = sqlsrv_query( $conn, $sql );
            if ($result) {
                $rc_code = "";
                while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
                  extract($row);
                  $rc_code = $nomor;
                }

                if ($rc_code != "") {
                  $sql1 = "insert into t_rc (branch_id, branchname, rc_code,  po_code, do_code, status, postingdate, receiptdate,
                          documentdate, vendor_code, vendorname, remarks, useradded, dateadded)
                          select branch_id, branchname, '$rc_code' as rc_code, po_code, do_code, 'OPEN', convert(varchar(10), getdate(), 20),
                          convert(varchar(10), getdate(), 20), convert(varchar(10), getdate(), 20),
                          vendor_code, vendorname, remarks, '$username', convert(varchar(10), getdate(), 20)
                          from t_do
                          where do_code='$do_code'";
                  sqlsrv_query( $conn, $sql1 );

                  $sql2 = "insert into t_rc_detail (branch_id, branchname, rc_code, po_code, do_code, substitute, plu, article_code,
                          class, burui, dp2, supplier_code, description, long_description, brand, qty,
                          useradded, dateadded)
                          select pd.branch_id, pd.branchname, '$rc_code' as rc_code, pd.po_code, pd.do_code, pd.substitute, pd.plu, pd.article_code,
                          pd.class, pd.burui, pd.dp2, pd.supplier_code, pd.description, pd.long_description, pd.brand,
                          pd.qty, '$username', convert(varchar(10), getdate(), 20)
                          from t_do_detail pd
                          where pd.do_code='$do_code'";
                  sqlsrv_query( $conn, $sql2 );

                  if ($po_code != "") {
                    $sql    = "exec dbo.fillQTYPO '$po_code'";
                    $result = sqlsrv_query( $conn, $sql );
                  }
                  if ($do_code != "") {
                    $sql    = "exec dbo.fillStatusDO '$do_code'";
                    $result = sqlsrv_query( $conn, $sql );
                  }

                }
            } else {
                $sts    = "";
            }
        }
    }
    sqlsrv_close($conn);
    $out = array("status" => $sts, "rc_code" => $rc_code, "po_code" => $po_code, "do_code" => $do_code,
                 "sql1" => $sql1, "sql2" => $sql2);
    echo json_encode($out);
?>
