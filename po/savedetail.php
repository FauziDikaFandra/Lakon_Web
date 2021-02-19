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
    $po_code      = trim(addslashes($request->po_code));
    $sub          = trim(addslashes($request->sub));
    $plu          = trim(addslashes($request->plu));
    $qty          = trim(addslashes($request->qty));
    $price        = trim(addslashes($request->price));
    $discountpct  = trim(addslashes($request->discountpct));
    $gross        = trim(addslashes($request->gross));
    $discountamt  = trim(addslashes($request->discountamt));
    $subtotal     = trim(addslashes($request->subtotal));

    $sql1 = "";
    $sql2 = "";
    if ( !empty($po_code) && !empty($plu) ){
        //   $select = "select * from t_po where po_code = '$po_code'";
        //   $result = sqlsrv_query( $conn, $sql );
        //   $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC);
        //   $branch_id = $row['branch_id'];
        //   $branchname = $row['branchname'];

          $sql = "select po_code from t_po_detail where po_code='$po_code' and plu='$plu' ";
          // echo $sql . "\n";
          $result = sqlsrv_query( $conn, $sql, array(), array( "Scrollable" => 'static' ));
          if ($result) {
              $row_count = sqlsrv_num_rows( $result );
              if ($row_count <=0 ) {
                  //insert
                  $sql1 = "insert into t_po_detail ( branch_id, branchname, po_code, substitute, plu, article_code, class, burui,
                          dp2, supplier_code, description, long_description, brand,
                          qty, price, gross, discountpct, discountamt, subtotal,
                          useradded, dateadded )
                          select '$branch_id' as branch_id, '$branchname' as branchname,
                                 '$po_code' as po_code, '$sub' as substitute, ltrim(rtrim(plu)) as plu,
                                 ltrim(rtrim(article_code)) as article_code, ltrim(rtrim(class)) as class,
                                 ltrim(rtrim(burui)) as burui, ltrim(rtrim(dp2)) as dp2,
                                 ltrim(rtrim(supplier_code)) as supplier_code, ltrim(rtrim(description)) as description,
                                 ltrim(rtrim(long_description)) as long_description, ltrim(rtrim(brand)) as brand,
                                 '$qty', '$price', '$gross', '$discountpct', '$discountamt', '$subtotal',
                                 '$username', GETDATE()
                          from item_master
                          where plu='$plu' ";
                  sqlsrv_query($conn, $sql1);
              } else {
                  //Update
                  $sql1 = "update a set
                               a.article_code=ltrim(rtrim(b.article_code)), a.class=ltrim(rtrim(b.class)),
                               a.burui=ltrim(rtrim(b.burui)), a.dp2=ltrim(rtrim(b.dp2)),
                               a.supplier_code=ltrim(rtrim(b.supplier_code)),
                               a.description=ltrim(rtrim(b.description)),
                               a.long_description=ltrim(rtrim(b.long_description)), a.brand=ltrim(rtrim(b.brand)),
                               qty='$qty',
                               price='$price',
                               gross='$gross',
                               discountpct='$discountpct',
                               discountamt='$discountamt',
                               subtotal='$subtotal',
                               useredited='$username',
                               dateedited=GETDATE(),
                               branch_id='$branch_id',
                               branchname='$branchname',
                               substitute='$sub'
                               from t_po_detail a
                               left join item_master b on ltrim(rtrim(a.plu))=ltrim(rtrim(b.plu))
                               where a.po_code='$po_code' and a.plu='$plu' ";
                  sqlsrv_query($conn, $sql1);
              }
              $sql2 = "update a
                      set a.subtotal=b.subtotal, a.discountamt=b.discountamt,
                      a.tax = (b.subtotal-b.discountamt) * v.isppn * 10 / 100,
                      a.grandtotal= (b.subtotal-b.discountamt) + ( (b.subtotal-b.discountamt) * v.isppn * 10 / 100 )
                      from t_po a
                      left join
                      (
                        select po_code, sum(gross) as subtotal, sum(discountamt) as discountamt, sum(subtotal) as grandtotal
                        from t_po_detail
                        where po_code='$po_code'
                        group by po_code
                      ) b on a.po_code=b.po_code
                      left join m_vendor v on a.vendor_code=v.vendor_code
                      where a.po_code='$po_code'
                      and a.po_code=b.po_code";
              sqlsrv_query($conn, $sql2);
              $json = array("status" => 1, "po_code" => $po_code, "plu" => $plu, "sql1" => $sql1, "sql2" => $sql2 );
          } else {
              $json = array("status" => 0,"data" => "", "sql1" => "", "sql2" => "");
          }
    } else {
        $json = array("status" => 0,"data" => "", "sql1" => "", "sql2" => "");
    }
    sqlsrv_close($conn);
    echo json_encode($json);
?>
