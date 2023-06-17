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
            font-size:20px;
        }
        .deliver-time{
            width: 400px;
            display: block;
            margin:50px auto;
        }
        .deliver-buttons{
            width: 500px;
            display: flex;
            margin:50px auto;
            justify-content : space-between;
        }
        .Add-button, .Delete-button{
            width:150px;
            height:30px;
        }
        .Upload-button{
            width:150px;
            height:50px;
            font-size:20px;
            position: fixed;
            top:80%;
            left: 50%;
            transform: translate(-50%,100%);
        }
    </style>
</head>
<body>
    <div>
        <a href="./Index.html">👈Back to menu</a>
    </div>
    <div class="deliver-time">
        <p>Time of next deliver: </p>
        <input type="date" name="deliver-date" value='<?= date('Y-m-d');?>' min="<?= date('Y-m-d');?>" />
    </div>
    <div id="product-list">

    </div>
    <div class="deliver-buttons">
        <button class="Add-button" onclick="AddProject()">+ Add a product</button>
        <button class="Delete-button" onclick="DeleteProject()">- Delete a product</button>
    </div>
    <button class="Upload-button">Submit</button>
</body>
<script>
    function AddProject(){
        //A big div
        let div = document.createElement("div");
        let place = document.querySelector("#product-list");
        place.appendChild(div);
        let divEl = document.querySelector("#product-list").lastChild;
        divEl.classList.add("product");
        //Product Name
        let span = document.createElement("span");
        let t = document.createTextNode("Product Name");
        span.appendChild(t);
        divEl.appendChild(span);
        //Product Name input
        let input = document.createElement("input");
        divEl.appendChild(input);

    }
</script>
</html>