<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klant</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
    <script>
        if(Cookies.get('CheckDate') == undefined){
            Cookies.set('CheckDate', "0" , { expires: 1 });
            location.reload();
        }
        if(Cookies.get('CheckClient') == undefined){
            Cookies.set('CheckClient', "0" , { expires: 1 });
            location.reload();
        }
        
    </script>
</head>
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

if($_COOKIE["CheckClient"] || $_COOKIE["CheckDate"]){
    $CookieClient = $_COOKIE["CheckClient"];
    $CookieDate = $_COOKIE["CheckDate"];
    }else{
    $CookieClient = "0";
    $CookieDate = "0";
}
$ClientSQL = "SELECT * FROM client";
$ClientResult = mysqli_query($conn, $ClientSQL);
$PackageSQL = "SELECT * FROM packageproduct WHERE idClient = '$CookieClient' AND pickupTime = '$CookieDate'" ;
$PackageResult = mysqli_query($conn, $PackageSQL);
$productTypeResult = mysqli_query($conn, "SELECT idProductType, productTypeName FROM producttype");
?>
<body class="klant-body">
    <nav class="index-link">
            <a href="./Index.html">Home</a>
            <a href="./leverancier.php">Leverancier</a>
            <a href="./magazijnmedewerker.php">Magazijnmedewerker</a>
            <a href="./vrijwilliger.php">Vrijwilliger</a>
            <a href="./klant.php">Klant</a>
            <div id="indicator"></div>
    </nav>
    <div class="klant-header">
        <?php if($_COOKIE["CheckClient"] || $_COOKIE["CheckDate"]){ ?>
        <div class="klant-zoek">
            <p>You are now checking for&ensp;</p>
            <p>
                <?php
                mysqli_data_seek($ClientResult, 0); // 重新定位结果集的指针到开头
                while ($PTrow = mysqli_fetch_assoc($ClientResult)) {
                    if ($PTrow["idClient"] == $_COOKIE["CheckClient"]) {
                        echo $PTrow["contactName"];
                        echo (" for ");
                        echo $_COOKIE["CheckDate"];
                        echo (". &ensp; ");
                    }
                }
                ?>
            </p>
            <button onclick="CleanUser()">Change client / Change date!</button>
        </div>
        <?php }else{ ?>
        <div class="klant-zoek">
            <p>Checking for&ensp;</p>
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
            <input type="date" id="friday-date" onchange="validateFridayDate()">
            <p>&ensp;(Only Friday!)&ensp;</p>
            <button onclick="ChooseUser()">Check!</button>
        </div>
        <?php } ?>
    </div>
    <?php if($_COOKIE["CheckClient"] || $_COOKIE["CheckDate"]){ ?>
    <div class="klant-inhoud">
    <table class="klant-table">
        <tr>
            <th>Company name</th>
            <th>Product name</th>
            <th>Product type</th>
            <th>Product EAN number</th>
            <th>Product quantity</th>
            <th>Product shelf life</th>
        </tr>
        <?php
        if (mysqli_num_rows($PackageResult) === 0) {
            // 结果集为空，显示 "无结果" 的消息
            echo '<tr><td colspan="6" class="no-results">No Result!</td></tr>';
        } else {
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
            <?php }
        }
        ?>
    </table>
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
        Cookies.set('CheckDate', date, { expires: 1 });
        Cookies.set('CheckClient', client, { expires: 1 });
        location.reload();
    }
}
function CleanUser(){
    Cookies.set('CheckDate', "0" , { expires: 1 });
    Cookies.set('CheckClient', "0" , { expires: 1 });
    location.reload();
}
</script>

</html>