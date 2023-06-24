<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leverancier</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" type="text/css" href="./CSS/index.css">
</head>
<body class="lever-body">
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
    $sql = "SELECT idDeliverProduct, companyName, productName, productType, productEAN, productQuantity, productShelfLife, deliveryTime FROM deliverproduct WHERE delivered = 0";
    $result = mysqli_query($conn, $sql);
    $productTypeResult = mysqli_query($conn, "SELECT idProductType, productTypeName FROM producttype");

    // 处理表单提交
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // 检查是否点击了删除按钮
        if (isset($_POST["deleteId"])) {
            $deleteId = $_POST["deleteId"];
            
            // 执行删除操作的 SQL 语句
            $deleteSql = "DELETE FROM deliverproduct WHERE idDeliverProduct = $deleteId";

            if (mysqli_query($conn, $deleteSql)) {
                // 删除成功后重定向到当前页面，实现刷新效果
                header("Location: " . $_SERVER["PHP_SELF"]);
                exit();
            } else {
                echo "删除数据失败：" . mysqli_error($conn);
            }
        } elseif(isset($_POST["submitId"])) {
            // 处理提交表单的数据
            $companyName = $_POST["companyName"];
            $productName = $_POST["productName"];
            $productType = $_POST["productType"];
            $productEAN = $_POST["productEAN"];
            $productQuantity = $_POST["productQuantity"];
            $productShelfLife = $_POST["productShelfLife"];
            $deliveryTime = $_POST["deliveryTime"];
            if($companyName && $productName && $productType && $productEAN && $productQuantity && $productShelfLife && $deliveryTime){
                // 执行插入数据的 SQL 语句
                $insertSql = "INSERT INTO deliverproduct (companyName, productName, productType, productEAN, productQuantity, productShelfLife, deliveryTime) 
                              VALUES ('$companyName', '$productName', '$productType', '$productEAN', '$productQuantity', '$productShelfLife', '$deliveryTime')";

                if (mysqli_query($conn, $insertSql)) {
                    // 插入成功后重定向到当前页面，实现刷新效果
                    header("Location: " . $_SERVER["PHP_SELF"]);
                    exit();
                } else {
                    echo("
                    <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error...',
                        text: '".mysqli_error($conn)."',
                      })
                    </script>");
                }
            }else{
                echo("
                <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Vul alstublieft alle velden in!',
                  })
                </script>");
            }
        }
    }
    ?>
    <nav class="index-link">
            <a href="./Index.html">Home</a>
            <a href="./leverancier.php">Leverancier</a>
            <a href="./magazijnmedewerker.php">Magazijnmedewerker</a>
            <a href="./vrijwilliger.php">Vrijwilliger</a>
            <a href="./klant.php">Klant</a>
            <div id="indicator"></div>
    </nav>
    <div class="lever-inhoud">
    <table class="tableLever">
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
        while($row = mysqli_fetch_assoc($result)) { ?>
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
                        <input type="hidden" name="deleteId" value="<?= $row["idDeliverProduct"] ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php ;} ?>

        <tr>
            <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                <td><input type="text" name="companyName"></td>
                <td><input type="text" name="productName"></td>
                <td>
                    <select name="productType">
                        <?php
                        mysqli_data_seek($productTypeResult, 0); // 重新定位结果集的指针到开头
                        while ($PTrow = mysqli_fetch_assoc($productTypeResult)) {
                            echo '<option value="' . $PTrow["idProductType"] . '">' . $PTrow["productTypeName"] . '</option>';
                        }
                        ?>
                    </select>
                </td>
                <td><input type="number" name="productEAN" maxlength="13" minlength="13"></td>
                <td><input type="text" name="productQuantity"></td>
                <td><input type="date" name="productShelfLife" value="<?= date('Y-m-d'); ?>" min="<?= date('Y-m-d'); ?>"></td>
                <td><input type="date" name="deliveryTime" value="<?= date('Y-m-d'); ?>" min="<?= date('Y-m-d'); ?>"></td>
                <td><button type="submit" name="submitId">Submit</button></td>
            </form>
        </tr>
    </table>
    </div>
</body>
</html>
