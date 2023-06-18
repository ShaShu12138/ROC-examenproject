<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magazijnmedewerker</title>
    <style>
        a{
            color: black;
        }
        table {
            font-family: verdana,arial,sans-serif;
            font-size:11px;
            color:#333333;
            border-width: 1px;
            border-color: #666666;
            border-collapse: collapse;
        }
        th {
            border-width: 1px;
            padding: 8px;
            border-style: solid;
            border-color: #666666;
            background-color: #dedede;
        }
        td {
            border-width: 1px;
            padding: 8px;
            border-style: solid;
            border-color: #666666;
            background-color: #ffffff;
        }
        td:hover{
            background-color: #ffff66;
            transition: 0.2s;
        }
        .inhoud{
            width:95vw;
            margin: 50px auto;
            display: flex;
        }
        .container-left{
            width:50%;
            margin: 0;
        }
        .container-right{
            width: 45%;
            margin: 0 0 0 auto;
        }
    </style>
</head>
<body>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projectschema";
    
// 创建连接
$conn = mysqli_connect($servername, $username, $password, $dbname);
// 检查连接
if (!$conn) {
    die("连接失败: " . mysqli_connect_error());
}
//表中只展示未送达的项目
$DeliverSQL = "SELECT idDeliverProduct, companyName, productName, productType, productEAN, productQuantity, productShelfLife, deliveryTime, delivered FROM deliverproduct WHERE delivered = 0 ORDER BY deliveryTime";
$DeliverResult = mysqli_query($conn, $DeliverSQL);
$productTypeResult = mysqli_query($conn, "SELECT idProductType, productTypeName FROM producttype");
$WarehouseSQL = "SELECT idDeliveredProduct, companyName, productName, productType, productEAN, productQuantity, productShelfLife FROM warehouseinventory WHERE productQuantity > 0 ORDER BY productShelfLife";
$WarehouseResult = mysqli_query($conn, $WarehouseSQL);

//Delivered按钮
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["deliveredId"])) {
        // 获取提交的 deliveredId
        $deliveredId = $_POST["deliveredId"];

        // 更新 deliverproduct 表格的 delivered 字段
        $updateSQL = "UPDATE deliverproduct SET delivered = 1 WHERE idDeliverProduct = $deliveredId";
        mysqli_query($conn, $updateSQL);

        // 获取相关信息
        $selectSQL = "SELECT companyName, productName, productType, productEAN, productQuantity, productShelfLife FROM deliverproduct WHERE idDeliverProduct = $deliveredId";
        $selectedResult = mysqli_query($conn, $selectSQL);
        $selectedRow = mysqli_fetch_assoc($selectedResult);
        $companyName = $selectedRow["companyName"];
        $productName = $selectedRow["productName"];
        $productType = $selectedRow["productType"];
        $productEAN = $selectedRow["productEAN"];
        $productQuantity = $selectedRow["productQuantity"];
        $productShelfLife = $selectedRow["productShelfLife"];

        // 插入到 warehouseinventory 表格中
        $insertSQL = "INSERT INTO warehouseinventory (companyName, productName, productType, productEAN, productQuantity, productShelfLife) VALUES ('$companyName', '$productName', $productType, '$productEAN', $productQuantity, '$productShelfLife')";
        if (mysqli_query($conn, $insertSQL)) {
            // 删除成功后重定向到当前页面，实现刷新效果
            header("Location: " . $_SERVER["PHP_SELF"]);
            exit();
        } else {
            echo "删除数据失败：" . mysqli_error($conn);
        }
    }
    if (isset($_POST["deleteId"])) {
        // 获取提交的 deleteId
        $deleteId = $_POST["deleteId"];

        // 更新 deliverproduct 表格的 delivered 字段
        $updateSQL = "UPDATE warehouseinventory SET productQuantity = 0 WHERE idDeliveredProduct = $deleteId";
        if (mysqli_query($conn, $updateSQL)) {
            // 删除成功后重定向到当前页面，实现刷新效果
            header("Location: " . $_SERVER["PHP_SELF"]);
            exit();
        } else {
            echo "删除数据失败：" . mysqli_error($conn);
        }

    }
}
?>
    <div>
        <a href="./Index.html">👈Back to menu</a>
    </div>
    <div class="inhoud">
        <div class="container-left">
            <table>
                <tr>
                    <th>Company name</th>
                    <th>Product name</th>
                    <th>Product type</th>
                    <th>Product EAN number</th>
                    <th>Product quantity</th>
                    <th>Product shelf life</th>
                    <th>Delivery time</th>
                    <th></th>
                </tr>
                <?php
                while($row = mysqli_fetch_assoc($DeliverResult)) { ?>
                    <tr>
                        <td><?= $row["companyName"] ?></td>
                        <td><?= $row["productName"] ?></td>
                        <td><?php 
                        mysqli_data_seek($productTypeResult, 0); // 重新定位结果集的指针到开头
                        while($PTrow = mysqli_fetch_assoc($productTypeResult)) {
                            if ($PTrow["idProductType"] == $row["productType"]) {
                                echo $PTrow["productTypeName"];
                            }
                        } ?></td>
                        <td><?= $row["productEAN"]; ?></td>
                        <td><?= $row["productQuantity"]; ?></td>
                        <td><?= $row["productShelfLife"]; ?></td>
                        <td><?= $row["deliveryTime"]; ?></td>
                        <td>
                            <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                                <input type="hidden" name="deliveredId" value="<?= $row["idDeliverProduct"] ?>">
                                <button type="submit">Delivered</button>
                            </form>
                        </td>
                    </tr>
                <?php ;} ?>
            </table>
        </div>
        <div class="container-right">
            <table>
                <tr>
                    <th>Company name</th>
                    <th>Product name</th>
                    <th>Product type</th>
                    <th>Product EAN number</th>
                    <th>Product quantity</th>
                    <th>Product shelf life</th>
                    <th></th>
                </tr>
                <?php
                while($row = mysqli_fetch_assoc($WarehouseResult)) { ?>
                    <tr>
                        <td><?= $row["companyName"] ?></td>
                        <td><?= $row["productName"] ?></td>
                        <td><?php 
                        mysqli_data_seek($productTypeResult, 0); // 重新定位结果集的指针到开头
                        while($PTrow = mysqli_fetch_assoc($productTypeResult)) {
                            if ($PTrow["idProductType"] == $row["productType"]) {
                                echo $PTrow["productTypeName"];
                            }
                        } ?></td>
                        <td><?= $row["productEAN"]; ?></td>
                        <td><?= $row["productQuantity"]; ?></td>
                        <td  <?php if (strtotime($row["productShelfLife"]) < strtotime(date("Y-m-d"))) { echo 'style="color: red;"'; } ?>>
                            <?= $row["productShelfLife"]; ?>
                        </td>
                        <td style="width:10%">
                            <?php if (strtotime($row["productShelfLife"]) < strtotime(date("Y-m-d"))) {?>
                            <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                                <input type="hidden" name="deleteId" value="<?= $row["idDeliveredProduct"] ?>">
                                <button type="submit">Delete</button>
                            </form>
                            <?php } ?>
                        </td>
                    </tr>
                <?php ;} ?>
            </table>
        </div>
    </div>
</body>
</html>