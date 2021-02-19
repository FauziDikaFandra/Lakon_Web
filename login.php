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
    include_once "../fungsi.php";
    ini_set('max_execution_time',   300);
    $database    = include('varLakon.php');
    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];

    $conn = BukaKoneksi($us, $ps, $ip, $db);
    $postdata     = file_get_contents("php://input");

    $request      = json_decode($postdata);
    $user         = addslashes($request->username);
    $passdecr     = addslashes($request->passdecr);
    $passencr     = addslashes($request->passencr);

    if ( !empty($user) && !empty($passdecr) ) {
          $no = 0;
          $sql = "select vendor_code,name, password from m_vendor where vendor_code='$user' and password='$passdecr' ";
          $result = sqlsrv_query( $conn, $sql, array(), array( "Scrollable" => 'static' ));
          if ($result) {
              $row_count = sqlsrv_num_rows( $result );
              if ($row_count <=0 ) {
                  $no = 0;
              } else {
                  $no = 0;
                  while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
                      extract($row);
                      if ( $vendor_code==$user ) {
                          $no = 1;
                      }
                  }
              }
          } else {
              $no = 0;
          }
          $json = array("status" => 0, "data" => "");

          if ($no==1) {
              $out    = array();
              $out[]  = array("username" => $user,"name" => $name, "password" => $passdecr, "groupname" => "2");
              $json   = array("status" => $no, "data" => $out);
          }

          if ($no==0) {
              $sql = "select username, password, groupname, dp2 as sbu from s_user where username='$user' and password='$passdecr' ";
              $result = sqlsrv_query( $conn, $sql, array(), array( "Scrollable" => 'static' ));
              if ($result) {
                  $row_count = sqlsrv_num_rows( $result );
                  if ($row_count <=0 ) {
                      $no = 0;
                  } else {
                      $no = 0;
                      $out = array();
                      while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
                          extract($row);
                          if ( $username==$user ) {
                              $no = 1;
                              $out[]  = array("username" => $user,"name" => $user, "password" => $passdecr, "groupname" => $groupname, "sbu" => $sbu);
                              $json   = array("status" => $no, "data" => $out);
                          }
                      }
                  }
              } else {
                  $no = 0;
              }
          }
    } else {
        $json = array("status" => 0,"data" => "");
    }

    echo json_encode($json);
?>
