<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magazijnmedewerker</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" type="text/css" href="./CSS/index.css">
</head>
<body class="magazijn-body">
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projectschema";
    
// Maak verbinding
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Controleer de verbinding
if (!$conn) {
    die("连接失败: " . mysqli_connect_error());
}
//Alleen niet-geleverde producten weergeven
$DeliverSQL = "SELECT idDeliverProduct, companyName, productName, productType, productEAN, productQuantity, productShelfLife, deliveryTime, delivered FROM deliverproduct WHERE delivered = 0 ORDER BY deliveryTime";
$DeliverResult = mysqli_query($conn, $DeliverSQL);
$productTypeResult = mysqli_query($conn, "SELECT idProductType, productTypeName FROM producttype");
$WarehouseSQL = "SELECT idDeliveredProduct, companyName, productName, productType, productEAN, productQuantity, productShelfLife FROM warehouseinventory WHERE productQuantity > 0 ORDER BY productShelfLife";
$WarehouseResult = mysqli_query($conn, $WarehouseSQL);

//Delivered knop
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["deliveredId"])) {
        // Verkrijg de ingediende 'deliveredId'
        $deliveredId = $_POST["deliveredId"];

        // Update de 'delivered' kolom van de 'deliverproduct' tabel
        $updateSQL = "UPDATE deliverproduct SET delivered = 1 WHERE idDeliverProduct = $deliveredId";
        mysqli_query($conn, $updateSQL);

        // Haal gerelateerde informatie op
        $selectSQL = "SELECT companyName, productName, productType, productEAN, productQuantity, productShelfLife FROM deliverproduct WHERE idDeliverProduct = $deliveredId";
        $selectedResult = mysqli_query($conn, $selectSQL);
        $selectedRow = mysqli_fetch_assoc($selectedResult);
        $companyName = $selectedRow["companyName"];
        $productName = $selectedRow["productName"];
        $productType = $selectedRow["productType"];
        $productEAN = $selectedRow["productEAN"];
        $productQuantity = $selectedRow["productQuantity"];
        $productShelfLife = $selectedRow["productShelfLife"];

        // Voeg toe aan de 'warehouseinventory' tabel
        $insertSQL = "INSERT INTO warehouseinventory (companyName, productName, productType, productEAN, productQuantity, productShelfLife) VALUES ('$companyName', '$productName', $productType, '$productEAN', $productQuantity, '$productShelfLife')";
        if (mysqli_query($conn, $insertSQL)) {
            echo("
            <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Het product is succesvol afgeleverd',
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
    }
    if (isset($_POST["deleteId"])) {
        // Verkrijg de ingediende 'deleteId'
        $deleteId = $_POST["deleteId"];

        // Update de 'productQuantity' kolom van de 'warehouseinventory' tabel
        $updateSQL = "UPDATE warehouseinventory SET productQuantity = 0 WHERE idDeliveredProduct = $deleteId";
        if (mysqli_query($conn, $updateSQL)) {
            echo("
            <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Vervallen producten zijn verwijderd',
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
    <div class="inhoud">
        <div class="container-left">
            <table class="magazijn-table">
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
                        mysqli_data_seek($productTypeResult, 0); // Reset de resultaatpointer naar het begin
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
            <table class="magazijn-table">
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
                        mysqli_data_seek($productTypeResult, 0); // Reset de resultaatpointer naar het begin
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