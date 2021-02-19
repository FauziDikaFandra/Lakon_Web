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
    $dt = "select * from query_rpt where page = 11";
    $sql_ = "";
    $sql = "";
    $datas = array();
    $brands = "";
    $result = sqlsrv_query( $conn, $dt );
    if($result){
        while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
            $sql_ = $row['query'];
        }
    }
    //echo str_replace("#","'",$sql_); 
    $sql =  str_replace("#","'",$sql_); 
    $result1 = sqlsrv_query( $conn,$sql );
?>
    <html>
            <head>
                <style>
                    table{
                        BORDER=10 ;
                        BORDERCOLOR=RED
                    }
                </style>
            </head>
            <body>
                <table border = 1 BORDERCOLOR=black>
                    <tr>
                        <td>PLU</td>
                        <td>Product No</td>
                        <td style="width:150px">Description</td>
                        <td>Price</td>
                        <td>BOM</td>
                        <td>Reciept</td>
                        <td>Sales</td>
                        <td>Adjutment</td>
                        <td>Last Stock</td>
                    </tr>
                    <?php
                    if ($result1){
                        while( $row = sqlsrv_fetch_array( $result1, SQLSRV_FETCH_ASSOC) ) {
                            ?>
                            <tr>
                                <td><?php echo $row['plu']?></td>
                                <td><?php echo $row['substitute']?></td>
                                <td style="width:150px"><?php echo $row['Long_Description']?></td>
                                <td><?php echo $row['harga']?></td>
                                <td><?php echo $row['first_stok']?></td>
                                <td><?php echo $row['GR']?></td>
                                <td><?php echo $row['sales']?></td>
                                <td><?php echo $row['out']?></td>
                                <td><?php echo $row['last_stok']?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                <table>
            </body>
    </html>


        
