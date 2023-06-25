<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vrijwilliger</title>
    <link rel="stylesheet" type="text/css" href="./CSS/index.css">
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
    
// verbinding maken
$conn = mysqli_connect($servername, $username, $password, $dbname);
// controleer verbinding
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

    //Package knop
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["packageID"])) {
        //haal de vereiste gegevens op
        $DeliveredProductID = $_POST["packageID"];
        $packageInput = $_POST["packageInput"];

        if($packageInput){
        //update magazijngegevens
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

        //pakketgegevens uploaden
        $insertPackageSQL = "INSERT INTO packageproduct (idClient, companyName, productName, productType, productEAN, productQuantity, productShelfLife, pickupTime) VALUES ('$CookieClient', '$companyName', '$productName', $productType, '$productEAN', $packageInput, '$productShelfLife', '$CookieDate')";
        if (mysqli_query($conn, $insertPackageSQL)) {
            echo("
            <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'De geselecteerde items zijn succesvol verpakt',
                showConfirmButton: false,
                timer: 1500
              }).then(function() {
                setTimeout(function() {
                  window.location.href = '" . $_SERVER["PHP_SELF"] . "'; 
                }, 50); // Delay 50ms
              });
            </script>");
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
    <div class="header">
        <?php if($_COOKIE["PackageClient"] || $_COOKIE["PackageDate"]){ ?>
        <div class="klant-zoek">
            <p>
                <?php
                mysqli_data_seek($ClientResult, 0); // Reset de resultaatpointer naar het begin
                while ($PTrow = mysqli_fetch_assoc($ClientResult)) {
                    if ($PTrow["idClient"] == $_COOKIE["PackageClient"]) {
                        echo ("You are now packing for ");
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
                        echo ("<br>");
                        echo ("Tips: ");
                        echo $PTrow["information"];
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
                mysqli_data_seek($ClientResult, 0); // Reset de resultaatpointer naar het begin
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
                        mysqli_data_seek($productTypeResult, 0); //Reset de resultaatpointer naar het begin
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
                        mysqli_data_seek($productTypeResult, 0); //Reset de resultaatpointer naar het begin
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

    // Krijg de dag van de week van de geselecteerde datum (0-6, 0 voor zondag, 6 voor zaterdag)
    var dayOfWeek = date.getDay();  
    // Controleer of vrijdag is geselecteerd (5 voor vrijdag)
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
    }else{
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: 'U moet zowel de datum als de gebruiker invullen',
          showConfirmButton: false,
          timer: 1500
        })
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