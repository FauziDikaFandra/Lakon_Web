<?php
    header('Access-Control-Allow-Origin: *');

    // header("Content-Type': 'application/x-www-form-urlencoded");
    //$postdata = file_get_contents("php://input");

    // $request = json_decode($postdata);
    // $email = $request->email;
    // $pass = $request->pass;
    // echo $email; //this will go back under "data" of

    // header("Access-Control-Allow-Origin: *");
    // header("Content-Type: application/json; charset=UTF-8");
    include_once "../../fungsi.php";
    ini_set('max_execution_time',   300);
    $database   = include('../varLakon.php');
    $ip         = $database['host'];
    $db         = $database['db'];
    $us         = $database['user'];
    $ps         = $database['pass'];
    $conn       = BukaKoneksi($us, $ps, $ip, $db);

    // $username   = cekget('username');
    // $groupname  = cekget('groupname');
    // $po_code    = cekget('po_code');
    // $plu        = cekget('plu');
    // $qty        = cekget('qty');
    // $price      = cekget('price');
    // $discountpct= cekget('discountpct');
    // $gross      = cekget('gross');
    // $discountamt= cekget('discountamt');
    // $subtotal   = cekget('subtotal');

    $postdata     = file_get_contents("php://input");
    $request      = json_decode($postdata);
    $username     = trim(addslashes($request->username));
    $groupname    = trim(addslashes($request->groupname));
    $branch_id    = trim(addslashes($request->branch_id));
    $branchname   = trim(addslashes($request->branchname));
    $do_code      = trim(addslashes($request->do_code));
    $po_code      = trim(addslashes($request->po_code));
    $substitute   = trim(addslashes($request->sub));
    $plu          = trim(addslashes($request->plu));
    $qty          = trim(addslashes($request->qty));

    if ( !empty($do_code) && !empty($plu) ){
          $sql = "select do_code from t_do_detail where do_code='$do_code' and plu='$plu' ";
          // echo $sql . "\n";
          $result = sqlsrv_query( $conn, $sql, array(), array( "Scrollable" => 'static' ));
          if ($result) {
              $row_count = sqlsrv_num_rows( $result );
              if ($row_count <=0 ) {
                  //insert
                  $sql = "insert into t_do_detail ( branch_id, branchname, do_code, po_code, substitute, ltrim(rtrim(plu)) as plu,
                          ltrim(rtrim(article_code)) as article_code, ltrim(rtrim(class)) as class,
                          ltrim(rtrim(burui)) as burui, ltrim(rtrim(dp2)) as dp2,
                          ltrim(rtrim(supplier_code)) as supplier_code, ltrim(rtrim(description)) as description,
                          ltrim(rtrim(long_description)) as long_description, ltrim(rtrim(brand)) as brand,
                          qty, useradded, dateadded )
                          select '$branch_id' as branch_id, '$branchname' as branchname,
                                 '$do_code' as do_code, '$po_code' as po_code, '$substitute' as substitute,
                                 plu, article_code, class, burui,
                                 dp2, supplier_code, description, long_description, brand,
                                 '$qty', '$username', GETDATE()
                          from item_master
                          where plu='$plu' ";
                  sqlsrv_query($conn, $sql);
                  $json = array("status" => 1, "do_code" => $do_code, "po_code" => $po_code, "sql" => $sql);

                  $sql    = "exec dbo.fillQTYPO '$po_code'";
                  $result = sqlsrv_query( $conn, $sql );
              } else {
                  //Update
                  $sql = "update a set
                               a.article_code=ltrim(rtrim(b.article_code)), a.class=ltrim(rtrim(b.class)),
                               a.burui=ltrim(rtrim(b.burui)), a.dp2=ltrim(rtrim(b.dp2)),
                               a.supplier_code=ltrim(rtrim(b.supplier_code)), a.description=ltrim(rtrim(b.description)),
                               a.long_description=ltrim(rtrim(b.long_description)), a.brand=ltrim(rtrim(b.brand)),
                               qty='$qty',
                               useredited='$username',
                               dateedited=GETDATE(),
                               branch_id='$branch_id',
                               branchname='$branchname',
                               substitute='$substitute',
                               po_code='$po_code'
                               from t_do_detail a
                               left join item_master b on ltrim(rtrim(a.plu))=ltrim(rtrim(b.plu))
                               where a.do_code='$do_code' and a.plu='$plu' ";
                  sqlsrv_query($conn, $sql);
                  $json = array("status" => 1, "do_code" => $do_code, "po_code" => $po_code, "sql" => $sql);

                  $sql    = "exec dbo.fillQTYPO_DO '$po_code'";
                  $result = sqlsrv_query( $conn, $sql );
              }
          } else {
              $json = array("status" => 0, "do_code" => $do_code, "po_code" => $po_code, "sql" => "Query Gagal Dijalankan");
          }
    } else {
        $json = array("status" => 0, "do_code" => $do_code, "po_code" => $po_code, "sql" => "DO Code & PLU Kosong");
    }
    sqlsrv_close($conn);
    echo json_encode($json);
?>
