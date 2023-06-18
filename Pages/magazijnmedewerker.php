<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .inhoud{
            margin: 50px;
        }
        .container-left{
            float: left;
        }
        .container-right{
            float: right;
        }
        .project-container{
            border-collapse: collapse;
            width: 100%;
            border-collapse: collapse;
            font-size: 1em;
            font-family: sans-serif;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
        }
        th ,td{
            padding: 8px;
        }
        .project-items {
            background-color: #2f479e;
            color: #ffffff;
        }

        .projects {
        border-bottom: 1px solid #dddddd;
        }
        .projects:nth-of-type(even) {
        background-color: #f3f3f3;
        }

        .projects:last-of-type {
        border-bottom: 2px solid #737776;
        }

        .projects.active-row {
        font-weight: bold;
        color: #647572;
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
<body>
    <div>
        <a href="./Index.html">👈Back to menu</a>
    </div>
    <div class="inhoud">
    <div class="container-left">
        <table>
            <th>
                <tr>
                    <td>Voorbeeld</td>
                    <td>voorbeeld</td>
                    <td>voorbeeld</td>
                </tr>
            </th>
        </table>
    </div>
    <div class="container-right">
      <table class="project-container">
        <thead>
          <tr class="project-items">
            <td>Project name</td>
            <td>Project type</td>
            <td>EAN-nummer</td>
            <td>Hoeveelheid</td>
            <td>Houdbaarheid</td>
          </tr>
        </thead>
        <tbody class="project-inhoud">
            <tr class="projects">
                <td>xixixi</td>
                <td>hahahahha</td>
                <td>EAN 439028493</td>
                <td>xixixi</td>
                <td>hahahahha</td>
            </tr>
            <tr class="projects">
                <td>xixixi</td>
                <td>xixixi</td>
                <td>hahahahha</td>
                <td>hahahahha</td>
                <td>EAN 439028493</td>
            </tr>
            
        </tbody>
      </table>
    </div>
    </div>
</body>
</html>