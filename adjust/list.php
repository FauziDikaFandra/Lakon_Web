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
    //$user   = cekget('user');
    $modul = cekget('modul');
    $adm    = cekget('adm');
    $date1  = cekget('date1');
    $date2  = cekget('date2');
    if($adm == '0'){
        $sql = "select a.adj_kode,CONVERT(varchar(10),a.date,120)tgl,max(name)branch
                ,sum(c.qty)qty,a.typ,[user] ,a.status
                from t_adjustment a
                inner join m_branch b on a.whs_code = b.branch_id
                inner join t_adjustment_details c on a.adj_kode = c.adj_kode
                where CONVERT(varchar(10),a.date,120) between '$date1' and '$date2'
                group by a.adj_kode,CONVERT(varchar(10),a.date,120),a.typ,[user],status";
    }else{
        // $sql = "select a.adj_kode,CONVERT(varchar(10),a.date,120)tgl,max(name)branch
        //         ,sum(c.qty)qty,a.typ,[user] ,a.status
        //         from t_adjustment a
        //         inner join m_branch b on a.whs_code = b.branch_id
        //         inner join t_adjustment_details c on a.adj_kode = c.adj_kode
        //         where CONVERT(varchar(10),a.date,120) between '$date1' and '$date2'
        //         group by a.adj_kode,CONVERT(varchar(10),a.date,120),a.typ,[user],status
        $sql = "select adj_kode,case when name = 'S012' then 'MKG' when name = 'S014' then 'SMS' end as name,isnull(ps,'-')psd,isnull(SUM([IN]),0)masuk
                ,isnull(SUM([OUT]),0)keluar,[USER],[status],dts,xxx,remarks from
                (
                select a.adj_kode,b.branch_id as name,convert(varchar(10),posting_date,120)ps,
                case when type = 'out' then qty end as [IN],
                case when type = 'in' then qty end as [OUT]
                ,a.[user],a.[status],CONVERT(varchar(10),a.date,120)dts
                ,a.date xxx,a.remarks
                from t_adjustment a
                left join t_adjustment_details c on a.adj_kode = c.adj_kode
                inner join m_branch b on a.whs_code = b.branch_id
                where (CONVERT(varchar(8),a.date,112) > '20181001')
                and convert(varchar(10),a.date,120) between '$date1' and '$date2'
                )data group by adj_kode,name,ps,[user],[status],dts,xxx,remarks order by xxx desc";
    }
    $datas = array();
    $result = sqlsrv_query( $conn, $sql );
    if ($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            extract($row);
            if($adm == '0'){
                $datas[] = array("adj_kode" => $adj_kode, "tgl" => $tgl ,"branch" => $branch, "qty" => $qty, "typ" => $typ, "status" => $status, "user" => $user );
            }else{
                $datas[] = array("adj_kode" => $adj_kode, "name" => $name ,"psd" => $psd, "masuk" => $masuk, "keluar" => $keluar, "user" => $USER, "status"=>$status,"dts" => $dts,"xxx"=>$xxx,"remarks" => $remarks );
            }
            
        }
        $json = array("status" => 1, "respone" => $datas);
    }else{
        $json = array("status" => 0, "respone" => "Error");
    }
    // echo $sql;
    echo json_encode($json);
    
?>