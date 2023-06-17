<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leverancier</title>
    <style>
        a{
            color: black;
        }
        table{
            margin:50px auto;
            width:80vw;
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

    $sql = "SELECT companyName, productName, productType, productEAN, productQuantity, productShelfLife,deliveryTime FROM deliverproduct";
    $result = mysqli_query($conn, $sql);
    $productTypeResult = mysqli_query($conn, "SELECT idProductType, productTypeName FROM producttype");
    $productTypeResult2 = mysqli_query($conn, "SELECT idProductType, productTypeName FROM producttype");
    ?>
    <div>
        <a href="./Index.html">👈Back to menu</a>
    </div>
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
        while($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= $row["companyName"] ?></td>
                <td><?= $row["productName"] ?></td>
                <td><?php while($PTrow = mysqli_fetch_assoc($productTypeResult)) {
                    if ($PTrow["idProductType"] == $row["productType"]) {
                        echo $PTrow["productTypeName"];
                    }
                } ?></td>
                <td><?= $row["productEAN"]; ?></td>
                <td><?= $row["productQuantity"]; ?></td>
                <td><?= $row["productShelfLife"]; ?></td>
                <td><?= $row["deliveryTime"]; ?></td>
                <td><button>Delete</button></td>
            </tr>
        <?php ;} ?>

        <tr>
            <td><input type="text"></td>
            <td><input type="text"></td>
            <td>
                <select>
                    <?php while($PTrow2 = mysqli_fetch_assoc($productTypeResult2)) { ?>
                        <option value ="<?= $PTrow2["idProductType"]; ?>"><?= $PTrow2["productTypeName"]; ?></option>
                    <?php } ?>
                </select>
            </td>
            <td><input type="text"></td>
            <td><input type="text"></td>
            <td><input type="date" value='<?= date('Y-m-d');?>' min="<?= date('Y-m-d');?>" ></td>
            <td><input type="date" value='<?= date('Y-m-d');?>' min="<?= date('Y-m-d');?>" ></td>
            <td><button onclick=submit()>Submit</button></td>
        </tr>
    </table>
</body>
<script>
    submit(){
        
    }
</script>
</html>