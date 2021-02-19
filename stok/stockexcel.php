<?php
header("Content-type: application/octet-stream");
header("Pragma: no-cache");
header("Expires: 0");
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
    $dt = "select * from query_rpt where page = 101";
    $result = sqlsrv_query( $conn, $dt );
    if($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            $sql_ = $row['query'];
        }
    }

    
    if(isset($sql_)){
        $sql =  str_replace("#","'",$sql_); 
    }else{
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
    // $sql =  str_replace("#","'",$sql_); 
    $result1 = sqlsrv_query( $conn,$sql );
    //==============Hasil Query Sales========
    $sales = [];
    $brand = "";
    $tgl1 = "";
    $tgl2 = "";
    $vendor = "";
    if ($result1)
    {
        while( $row = sqlsrv_fetch_array( $result1, SQLSRV_FETCH_ASSOC) ) {
            $sales[]  = $row;
            $brand = $row["Brand"];
            $tgl1 = $row["tgl1"];
            $tgl2 = $row["tgl2"];
            // $vendor = $row["name"];
        }
    }
    header("Content-Disposition: attachment; filename=".$vendor."Inventory Report.xls");//ganti nama sesuai keperluan
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>SALES REPORT</title>
<style type="text/css">
    table{
        width:100%;         
        border: 1px solid black;
    }

    table, th {
        border: 1px solid black;
        padding: 3px;

    }

    table, th, td {
    border: 1px solid black;
    padding: 3px;
    }
</style>
</head>
<body>
   
    <table id="example" class="table table-bordered" style="font-size: 90%" >
        <thead style="background-color: #eee;color: #333;text-align: center; ">    
            <tr>
                <th >Ean/PLU</th>
                <th >Product No</th>
                <th >Description</th>
                <th style = "width:30px; text-align : center " >Price</th>
                <th style = "width:50px; text-align : center ">BOM</th>
                <th style = "width:35px; text-align : center ">Receipt</th>
                <th >Sales</th>                                        
                <th >Adjust</th>
                <th >last Stock</th>
            </tr>
            <?php
                foreach($sales as $data){
            ?>
            <tr>
                <td style = "width:110px; text-align : left "><?php echo "'".$data["plu"] ?></td>                       
                <td style = "width:100px; text-align : left "><?php echo $data["substitute"] ?></td>                       
                <td style = "width:350px; text-align : left "><?php echo $data["Long_Description"] ?></td>                       
                <td style = "width:160px; text-align : left "><?php echo rupiah($data["harga"]) ?></td>                       
                <td style = "width:30px; text-align : center " ><?php echo $data["first_stok"] ?></td>                       
                <td style = "width:80px; text-align : right "><?php echo $data["GR"] ?></td>                       
                <td style = "width:45px; text-align : center "><?php echo $data["sales"] ?></td>                       
                <td style = "width:80px; text-align : right "><?php echo $data["in"] - $data["out"] ?></td>                       
                <td style = "width:80px; text-align : right "><?php echo $data["last_stok"] ?></td>                       
            </tr> 
            <?php
                }
            ?>
        </thead>
    </table>
</body>
</html> 