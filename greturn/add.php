<?php
header('Access-Control-Allow-Origin: *');
include_once "../../fungsi.php";
include_once "../../lakonsecurity.php";
ini_set('max_execution_time',   300);
    $database    = include('../varLakon.php');
    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];

    
    $conn = BukaKoneksi($us, $ps, $ip, $db);
    $user   = cekget('user');
    $modul = cekget('modul');
    $rc_code = cekget('rc_code');
    //$param = getLakonSecurity($user,$modul,'im','dp2');

    $sqldel = "delete from t_rt where rt_code in
                (
                select a.rt_code from t_rt a 
                LEFT JOIN t_rt_detail b on a.rt_code = b.rt_code
                where b.rt_code is null and a.useradded = 'riyani' and status = 'OPEN'
                )";
    $result_del= sqlsrv_query($conn, $sqldel);
    if($result_del){
        $sql = "select hd+years+months as id,cast(id as int) + 1 as urut from
                (
                select 'RT'as hd,substring(convert(varchar(5),YEAR(GETDATE())),3,2) as years 
                ,case when len(convert(varchar(3),MONTH(GETDATE()))) = 1 then '0'+convert(varchar(3),MONTH(GETDATE()))
                else convert(varchar(3),MONTH(GETDATE())) end as months
                ,max(cast(substring(rt_code,7,3) as int )) id
                from t_rt where CONVERT(varchar(6),dateadded,112) = CONVERT(varchar(6),GETDATE(),112)
                )data";
    }
    $datas = array();
    $result_1 = sqlsrv_query( $conn, $sql );
    $row = sqlsrv_fetch_array( $result_1, SQLSRV_FETCH_ASSOC);
    $kode = "";
    if (strlen( $row['urut']) == 1){
        $kode = $row['id'].'00'.$row['urut'];
    }elseif(strlen( $row['urut']) == 2){
        $kode = $row['id'].'0'.$row['urut'];
    }elseif(strlen( $row['urut']) == 3){
        $kode = $row['id'].$row['urut'];
    }else{
        $kode =  $row['id'].'001';
    }
    $insert = " insert into t_rt
                select top 1 '$kode','','','','OPEN',null,null,null,'$rc_code',null,null,'$user',GETDATE(),null,null
                from t_rc ";
    $result = sqlsrv_query($conn, $insert);
    if($result){
        $json = array("status" => 1, "respone" => $kode);
    }else{
        $json = array("status" => 0, "respone" => null);
    }
    echo json_encode($json);
?>