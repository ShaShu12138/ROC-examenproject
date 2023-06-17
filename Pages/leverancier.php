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
        .deliver-time > p,input{
            display: inline;
        }
    </style>
</head>
<body>
    <div>
        <a href="./Index.html">👈Back to menu</a>
    </div>
    <div class="deliver-time">
        <p>Time of next deliver: </p>
        <input type="date" name="party" min="2023-06-01" max="2017-04-30" />
    </div>
    <p><?php echo date('Y-m-d');?></p>
</body>
</html>