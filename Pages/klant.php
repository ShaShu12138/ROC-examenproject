<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .project-container{
            border-collapse: collapse;
            width: 90%;
            border-collapse: collapse;
            margin: 85px;
            font-size: 0.9em;
            font-family: sans-serif;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
        }
        th ,td{
            padding: 8px;
            text-align: center;
        }
        .project-items {
            background-color: #009879;
            color: #ffffff;
        }

        .project-inhoud  .projects {
        border-bottom: 1px solid #dddddd;
        }
        .project-inhoud .projects:nth-of-type(even) {
        background-color: #f3f3f3;
        }

        .project-inhoud  .projects:last-of-type {
        border-bottom: 2px solid #009879;
        }

        .project-inhoud .projects.active-row {
        font-weight: bold;
        color: #009879;
        }

        .index-link > a{
            display: block;
            color: black;
            /*text-decoration:none;*/
            margin-bottom: 5px;
            margin-top: 5px;
        }
        .header > p{
            position: absolute;
            left: 50%;
            transform: translate(-50%,-60%);
            font-size: 400px;
        }
    </style>
</head>
<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "projectschema";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("You DB connection has been failed!: " . $conn->connect_error);
    }
?>
<body>
    <div>
        <a href="./Index.html">👈Back to menu</a>
    </div>
    <?php
    $sql = "SELECT productName, productType, productEAN, productQuantity, productShelfLife FROM deliverproduct";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table class='project-container'><tr class='project-items'><th>Project Naam</th><th>Type</th><th>EAN Nummer</th><th>Product Hoeveelheid</th><th>Shelf Life</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr class='projects'><td>" . $row["productName"] . "</td><td>" . $row["productType"] . "</td><td>" . $row["productEAN"] . "</td><td>" . $row["productQuantity"] . "</td><td>" . $row["productShelfLife"] . "</td></tr>";
        }
    
        echo "</table>";
    } else {
        echo "Kan niks vinden.";
    }

    $conn->close();
    ?>

</body>
</html>