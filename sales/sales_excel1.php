<?php
header("Content-type: application/octet-stream");
header("Pragma: no-cache");
header("Expires: 0");
header('Access-Control-Allow-Origin: *');
include_once "../../fungsi.php";
include_once "../../lakonsecurity.php";
$usr = cekget('user');
ini_set('max_execution_time',   300);
    $database    = include('../varLakon.php');
    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];
    $conn = BukaKoneksi($us, $ps, $ip, $db);
    $dt = "select * from query_rpt where page = 8 and users = '$usr'";
    $result = sqlsrv_query( $conn, $dt );
    if($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            $sql_ = $row['query'];
        }
    }
    $sql =  str_replace("#","'",$sql_); 
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
            $vendor = $row["name"];
        }
    }
    header("Content-Disposition: attachment; filename=".$vendor." Sales Report.xls");//ganti nama sesuai keperluan
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
    <h2 style = "text-align : center">SALES REPORT</h2>
    <h3 style = "text-align : center"><?php echo $vendor; ?></h3><br>
    Period :  <?php echo $tgl1; ?>  To  <?php echo $tgl2; ?></center>
    <table id="example" class="table table-bordered" style="font-size: 90%" >
        <thead style="background-color: #eee;color: #333;text-align: center; ">    
            <tr>
                <th >Date</th>
                <th >Ean/PLU</th>
                <th >Product No</th>
                <th >Description</th>
                <th style = "width:30px; text-align : center " >QTY</th>
                <th style = "width:50px; text-align : center ">Price</th>
                <th style = "width:35px; text-align : center ">Disc(%)</th>
                <th >Sales</th>                                        
            </tr>
            <?php
                foreach($sales as $data)
                { 
            ?>
            <tr>
                <td style = "width:90px; text-align : left "><?php echo $data["periode"] ?></td>                       
                <td style = "width:115px; text-align : left "><?php echo "'".$data["plu"] ?></td>                       
                <td style = "width:90px; text-align : left "><?php echo $data["prod"] ?></td>                       
                <td style = "width:220px; text-align : left "><?php echo $data["item_description"] ?></td>                       
                <td style = "width:30px; text-align : center " ><?php echo $data["qty"] ?></td>                       
                <td style = "width:80px; text-align : right "><?php echo $data["price"] ?></td>                       
                <td style = "width:45px; text-align : center "><?php echo $data["disc"] ?></td>                       
                <td style = "width:80px; text-align : right "><?php echo $data["Net_Price"] ?></td>                       
            </tr> 
            <?php
                }
            ?>
        </thead>
    </table>
</body>
</html> 