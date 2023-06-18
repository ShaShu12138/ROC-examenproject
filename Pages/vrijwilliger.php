<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vrijwilliger</title>
    <style>
        a{
            color: black;
            margin:0 200px 0 0;
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
        .header{
            display:flex;
        }
        .packageInput{
            width:70px;
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
    $productTypeResult = mysqli_query($conn, "SELECT idProductType, productTypeName FROM producttype");
    $ClientSQL = "SELECT * FROM client";
    $ClientResult = mysqli_query($conn, $ClientSQL);
    $WarehouseSQL = "SELECT idDeliveredProduct, companyName, productName, productType, productEAN, productQuantity, productShelfLife FROM warehouseinventory WHERE productQuantity > 0 AND productShelfLife > CURDATE() ORDER BY productShelfLife";
    $WarehouseResult = mysqli_query($conn, $WarehouseSQL);
    $PackageSQL = "SELECT * FROM packageproduct" ;
    $PackageResult = mysqli_query($conn, $PackageSQL);

?>
    <div class="header">
        <a href="./Index.html">👈Back to menu</a>
        <p>Packing for&ensp;</p>
        <select name="clientSelect" id="clientSelect" onchange="displayUserData()" >
            <option value="none" selected disabled hidden>Please chose an user</option>
            <?php
            mysqli_data_seek($ClientResult, 0); // 重新定位结果集的指针到开头
            while ($PTrow = mysqli_fetch_assoc($ClientResult)) {
                echo '<option value="' . $PTrow["idClient"] . '">' . $PTrow["contactName"] . '</option>';
            }
            ?>
        </select>
        <p>&ensp;for&ensp;</p>
        <input type="date" id="friday-date" min="<?= date('Y-m-d'); ?>" onchange="validateFridayDate()">
        <p>&ensp;(Only Friday!)&ensp;</p>
        <p id="userDetails">&ensp;</p>
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
                    <th>Package quantity</th>
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
                        <td><input type="text" class="packageInput"></td>
                        <td><?= $row["productShelfLife"]; ?></td>
                        <td style="width:10%">
                            <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                                <input type="hidden" name="packageID" value="<?= $row["idDeliveredProduct"] ?>">
                                <button type="submit" >Package</button>
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
                </tr>
                <?php
                while($row = mysqli_fetch_assoc($PackageResult)) { ?>
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
                        <td><input type="text" class="packageInput"></td>
                        <td><?= $row["productShelfLife"]; ?></td>
                    </tr>
                <?php ;} ?>
            </table>
        </div>
    </div>
</body>
<script>
function displayUserData() {
    var select = document.getElementById("clientSelect");
    var selectedClientId = select.value
    <?php
    mysqli_data_seek($ClientResult, 0); // 重新定位结果集的指针到开头
    while ($PTrow = mysqli_fetch_assoc($ClientResult)) {
        echo 'if (selectedClientId === "' . $PTrow["idClient"] . '") {';
        echo 'document.getElementById("userDetails").innerHTML = "His/Her family have ' . $PTrow["adultsNumber"] . ' adult, ' . $PTrow["kidsNumber"] . ' children and ' . $PTrow["babyNumber"] . ' baby";';
        echo '}';
    }
    ?>
}
function validateFridayDate() {
    var inputDate = document.getElementById("friday-date").value;
    var date = new Date(inputDate);

    // 获取选择日期的星期几（0-6，0代表星期日，6代表星期六）
    var dayOfWeek = date.getDay();  
    // 检查是否选择的是周五（4代表星期五）
    if (dayOfWeek !== 5) {
      alert("You can only chose Friday!!");
      document.getElementById("friday-date").value = "";
    }
}
</script>
</html>