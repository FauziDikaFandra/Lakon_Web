<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    include_once "../../fungsi.php";
    ini_set('max_execution_time',   300);
    $username   = cekget('username');
    $groupname  = cekget('groupname');
    $tgl        = cekget('tgl');
    $plu        = cekget('plu');

    $database   = include('../varLakon.php');
    $ip         = $database['host'];
    $db         = $database['db'];
    $us         = $database['user'];
    $ps         = $database['pass'];

    $conn       = BukaKoneksi($us, $ps, $ip, $db);
    if($username == "S013"){
        $sql = "exec dbo.fillStokReceipt_smb '$tgl', '$plu'";
	}elseif($username == "S024"){
        $sql = "exec dbo.fillStokReceipt_TokoPedia '$tgl', '$plu'";
	}elseif($username == "S023"){
        $sql = "exec dbo.fillStokReceipt_Shopee '$tgl', '$plu'";
    }elseif($username == "S014"){
        $sql = "exec dbo.fillStokReceipt_sms '$tgl', '$plu'";
    }elseif($username == "S012"){
        $sql = "exec dbo.fillStokReceipt '$tgl', '$plu'";
    }elseif($username == "S011"){
        $sql = "exec dbo.fillStokReceipt_bali'$tgl', '$plu'";
    }
    // echo "SQL : " . $sql . "\n";
    $result     = sqlsrv_query( $conn, $sql );

    sqlsrv_close($conn);

    $json       = array("status" => "1", "tgl" => $tgl, "plu" => $plu );
	  echo json_encode($json);

?>
