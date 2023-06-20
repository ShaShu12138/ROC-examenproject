<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vrijwilliger</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
    <script>
        if(Cookies.get('PackageDate') == undefined){
            Cookies.set('PackageDate', "0" , { expires: 1 });
            location.reload();
        }
        if(Cookies.get('PackageClient') == undefined){
            Cookies.set('PackageClient', "0" , { expires: 1 });
            location.reload();
        }
        
    </script>
</head>
<body class="vrijwilliger-body">
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
if($_COOKIE["PackageClient"] || $_COOKIE["PackageDate"]){
$CookieClient = $_COOKIE["PackageClient"];
$CookieDate = $_COOKIE["PackageDate"];
}else{
$CookieClient = "0";
$CookieDate = "0";
}
$PackageSQL = "SELECT * FROM packageproduct WHERE idClient = '$CookieClient' AND pickupTime = '$CookieDate'" ;
$PackageResult = mysqli_query($conn, $PackageSQL);

    //Package按钮
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["packageID"])) {
        // 获取所需的数据
        $DeliveredProductID = $_POST["packageID"];
        $packageInput = $_POST["packageInput"];

        //更新仓库数据
        $WarehouseSQL = "SELECT companyName, productName, productType, productEAN, productQuantity, productShelfLife FROM warehouseinventory WHERE idDeliveredProduct = $DeliveredProductID";
        $WarehouseResult = mysqli_query($conn, $WarehouseSQL);
        $WarehouseRow = mysqli_fetch_assoc($WarehouseResult);
        $companyName = $WarehouseRow["companyName"];
        $productName = $WarehouseRow["productName"];
        $productType = $WarehouseRow["productType"];
        $productEAN = $WarehouseRow["productEAN"];
        $productShelfLife = $WarehouseRow["productShelfLife"];
        $OldQuantity = $WarehouseRow["productQuantity"];
        $NewQuantity = $OldQuantity - $packageInput;
        $UpdateWarehouseSQL = "UPDATE warehouseinventory SET productQuantity = $NewQuantity WHERE idDeliveredProduct = $DeliveredProductID";
        mysqli_query($conn, $UpdateWarehouseSQL);

        //上传包裹数据
        $insertPackageSQL = "INSERT INTO packageproduct (idClient, companyName, productName, productType, productEAN, productQuantity, productShelfLife, pickupTime) VALUES ('$CookieClient', '$companyName', '$productName', $productType, '$productEAN', $packageInput, '$productShelfLife', '$CookieDate')";
        if (mysqli_query($conn, $insertPackageSQL)) {
            // 删除成功后重定向到当前页面，实现刷新效果
            header("Location: " . $_SERVER["PHP_SELF"]);
            exit();
        } else {
            echo "删除数据失败：" . mysqli_error($conn);
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
    <div class="header">
        <?php if($_COOKIE["PackageClient"] || $_COOKIE["PackageDate"]){ ?>
        <div class="klant-zoek">
            <p>You are now packing for&ensp;</p>
            <p>
                <?php
                mysqli_data_seek($ClientResult, 0); // 重新定位结果集的指针到开头
                while ($PTrow = mysqli_fetch_assoc($ClientResult)) {
                    if ($PTrow["idClient"] == $_COOKIE["PackageClient"]) {
                        echo $PTrow["contactName"];
                        echo (" for ");
                        echo $_COOKIE["PackageDate"];
                        echo (". His/Her family have ");
                        echo $PTrow["adultsNumber"];
                        echo (" adults, ");
                        echo $PTrow["kidsNumber"];
                        echo (" Kids and ");
                        echo $PTrow["babyNumber"];
                        echo (" baby.&ensp;");
                    }
                }
                ?>
            </p>
            <button onclick="CleanUser()">Change client!</button>
        </div>
        <?php }else{ ?>
        <div class="klant-zoek">
            <p>Packing for&ensp;</p>
            <select name="clientSelect" id="clientSelect">
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
            <button onclick="ChooseUser()">Package!</button>
        </div>
        <?php } ?>
    </div>
    <?php if($_COOKIE["PackageClient"] || $_COOKIE["PackageDate"]){ ?>
    <div class="vrijwilliger-inhoud" >
        <div class="container-left">
            <table class = "vrijwilliger-table">
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
                        <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                            <td><input type="text" name="packageInput" class="packageInput"></td>
                            <td><?= $row["productShelfLife"]; ?></td>
                            <td style="width:10%">
                            <input type="hidden" name="packageID" value="<?= $row["idDeliveredProduct"] ?>">
                            <button type="submit" >Package</button>
                            </td>
                        </form>
                    </tr>
                <?php ;} ?>
            </table>
        </div>
        <div class="container-right">
            <table  class = "vrijwilliger-table">
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
                            if ($PTrow["idProductType"] == $row["ProductType"]) {
                                echo $PTrow["productTypeName"];
                            }
                        } ?></td>
                        <td><?= $row["productEAN"]; ?></td>
                        <td><?= $row["productQuantity"]; ?></td>
                        <td><?= $row["productShelfLife"]; ?></td>
                    </tr>
                <?php ;} ?>
            </table>
        </div>
    </div>
    <?php } ?>
</body>
<script>
function validateFridayDate() {
    var inputDate = document.getElementById("friday-date").value;
    var date = new Date(inputDate);

    // 获取选择日期的星期几（0-6，0代表星期日，6代表星期六）
    var dayOfWeek = date.getDay();  
    // 检查是否选择的是周五（4代表星期五）
    if (dayOfWeek !== 5) {
      Swal.fire('You can only chose Friday!!');
      document.getElementById("friday-date").value = "";
    }
}
function ChooseUser(){
    var client = document.getElementById("clientSelect").value;
    var date = document.getElementById("friday-date").value;
    if(client != "none" && date){
        Cookies.set('PackageDate', date, { expires: 1 });
        Cookies.set('PackageClient', client, { expires: 1 });
        location.reload();
    }
}
function CleanUser(){
    Cookies.set('PackageDate', "0" , { expires: 1 });
    Cookies.set('PackageClient', "0" , { expires: 1 });
    location.reload();
}
function Package(){

}
</script>
</html>