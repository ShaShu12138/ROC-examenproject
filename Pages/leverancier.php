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
        
    // Maak verbinding met de database
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    // Alleen niet-geleverde producten weergeven in de tabel
    $sql = "SELECT idDeliverProduct, companyName, productName, productType, productEAN, productQuantity, productShelfLife, deliveryTime FROM deliverproduct WHERE delivered = 0";
    $result = mysqli_query($conn, $sql);
    $productTypeResult = mysqli_query($conn, "SELECT idProductType, productTypeName FROM producttype");

    // Verwerk het ingediende formulier
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Controleer of de delete-knop is ingedrukt
        if (isset($_POST["deleteId"])) {
            $deleteId = $_POST["deleteId"];
            
            // SQL-query voor het verwijderen van de productinformatie
            $deleteSql = "DELETE FROM deliverproduct WHERE idDeliverProduct = $deleteId";

            if (mysqli_query($conn, $deleteSql)) {
                echo("
                <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Productinformatie succesvol verwijderd',
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
        } elseif(isset($_POST["submitId"])) {
            // Verwerk de ingediende formuliergegevens
            $companyName = $_POST["companyName"];
            $productName = $_POST["productName"];
            $productType = $_POST["productType"];
            $productEAN = $_POST["productEAN"];
            $productQuantity = $_POST["productQuantity"];
            $productShelfLife = $_POST["productShelfLife"];
            $deliveryTime = $_POST["deliveryTime"];
            if($companyName && $productName && $productType && $productEAN && $productQuantity && $productShelfLife && $deliveryTime){
                // SQL-query voor het invoegen van de gegevens
                $insertSql = "INSERT INTO deliverproduct (companyName, productName, productType, productEAN, productQuantity, productShelfLife, deliveryTime) 
                              VALUES ('$companyName', '$productName', '$productType', '$productEAN', '$productQuantity', '$productShelfLife', '$deliveryTime')";

                if (mysqli_query($conn, $insertSql)) {
                    echo("
                    <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Productinformatie succesvol ingevoerd',
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
                    title: 'Error...',
                    text: 'Gelieve alle gegevens in te vullen!',
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
                mysqli_data_seek($productTypeResult, 0); // Reset de cursor van de resultaatset naar het begin
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
                        mysqli_data_seek($productTypeResult, 0); // Reset de cursor van de resultaatset naar het begin
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
