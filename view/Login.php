<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        body{ font-family: 'Roboto'; font-size:1.3em; background-color: rgb(200,200,200); text-align: center; margin: auto; transition: smooth; }
        .container {width: 90%; margin: auto; height:695px; box-shadow: 2px 2px 50px; padding: 10px; margin-top: 30px; background-color: white}
        .form-group { width:335px; display:inline-block; margin: 10px 5px; border-bottom: 2px solid rgba(250,150,150); border-width: 4px; padding-bottom: 5px; }
        button { width: 99%; height: 35px; margin: 4px 0; border: none; background: rgb(13, 13, 178);
            box-shadow: 2px 2px 6px black; color: white; text-transform: uppercase; font-size: 0.8em; cursor:pointer }
        .form-input { width:325px; height: 30px; padding: 0 5px; background: rgb(240,240,240); border: none; }
        textarea { height:auto!important; }
        label {display:block; font-size: 0.7em; font-weight:bold; text-transform:uppercase}
        .messages { border: 2px solid #9d1717; font-size: 0.8em; padding:10px; background: rgb(220,220,220); text-align: center; width: 325px; margin: auto }
        .list { font-size:0.7em; }
        button.delete { background-color: #9d1717; }
        button.update { color: #9d1717; background-color: #fff}
        button.currentpage { background-color: white; color: rgb(13, 13, 178);}
        .controls button{ width: 11%!important; }
        .controls { width: 100% }
        .controls a {  margin:1%; text-decoration:none; }
        nav {list-style:none; width: 98%; background-color: #9d1717; font-weight: bolder; text-transform: uppercase; padding: 10px 1%; font-size: 0.8em; }
        nav li {display:inline; margin:1%;}
        nav li a, .link {text-decoration: none; color: rgba(250,150,150); }
        nav li a.active { color: white; }
        .page {width: 100%; height: 550px; overflow:scroll; text-align: left; scrollbar-width: thin;}
        .section-title { text-transform: uppercase; padding-left:5px; margin: 15px 0; text-decoration: underline }
    </style>
</head>
<body>
<div class="container">
<h4 style="padding-left:10px; margin:10px 0; text-transform:uppercase">RESULT MANAGEMENT SYSTEM</h4>
<hr style="margin-left:5px;">
<!-- Page Start -->
<div class="page">
    <div class="form-container" style="padding-top: 80px; margin:auto; text-align:center">
        <form method="POST" action="./login">
        <u>LOG IN TO ACCESS APPLICATION</u><br><br>
            <div class="form-group" >
                <label for="userName">Username:</label>
                <input tabindex=1 type="text" class="form-input" name="userName" required />
            </div> <br>
            <div class="form-group" >
                <label for="password">Password:</label>
                <input type="password" class="form-input" name="password" required />
            </div> <br>
            <div class="form-group">
                <input hidden name="login"/>
                <button name="create" class="delete">Login</button>
            </div>
        </form>
        <br>
        <?php if(!empty($message)): ?>
            <div class="messages">
                <strong><u>RESPONSE</u></strong>
                <br>
                <?php foreach($message as $mess): ?>
                    <span><?=$mess;?></span><br>
                <?php endforeach;?>
            </div>
        <?php endif; ?>
    </div>
<!-- Page End -->
</div>
<hr>
    <span style="background-color: #9d1717; width:100%; display:block;font-weight:bold; text-transform:uppercase; font-size: 0.7em; padding:3px 0;">Designed by: <a class="link" href="https://fb.me/kalukalukalu">Contact</a></span>
</div> 
</body>
</html>