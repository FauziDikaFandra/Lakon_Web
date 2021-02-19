<?php
header('Access-Control-Allow-Origin: *');
include_once "../../fungsi.php";
include_once "../../lakonsecurity.php";
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=DataDO.xls");
ini_set('max_execution_time',   300);
    $database    = include('../varLakon.php');
    $ip   = $database['host'];
    $db   = $database['db'];
    $us   = $database['user'];
    $ps   = $database['pass'];

    
    $conn = BukaKoneksi($us, $ps, $ip, $db);
    $dt = "select * from query_rpt where page = 2";
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
                        <td style="width:100px">Brand</td>
                        <td style="width:100px">Article Code</td>
                        <td>PLU</td>
                        <td style="width:150px">Description</td>
                        <td>Qty</td>
                    </tr>
                    <?php
                    if ($result1){
                        while( $row = sqlsrv_fetch_array( $result1, SQLSRV_FETCH_ASSOC) ) {
                            ?>
                            <tr>
                                <td><?php echo $row['brand']?></td>
                                <td style="width:100px"><?php echo $row['article_code']?></td>
                                <td><?php echo $row['plu']?></td>
                                <td style="width:150px"><?php echo $row['long_description']?></td>
                                <td><?php echo $row['qty']?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                <table>
            </body>
    </html>


        
