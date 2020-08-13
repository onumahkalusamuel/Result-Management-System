<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=(isset($pagetitle)? $pagetitle : 'Application');?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="<?php \Core\Config::BASEURL;?>favicon.ico" type="image/x-icon">
    <style>
        * {transition: ease 0.5s all;}
        body{ font-family: 'Roboto'; font-size:1.3em; background-color: rgb(200,200,200); text-align: center; margin: auto; transition: smooth; }
        .container {width: 90%; margin: auto; height:695px; box-shadow: 2px 2px 50px; padding: 10px; margin-top: 30px; background-color: white}
        .form-group { width:335px; display:inline-block; margin: 10px 5px; border-bottom: 2px solid rgba(250,150,150); border-width: 4px; padding-bottom: 5px; }
        button { width: 99%; height: 35px; margin: 4px 0; border: none; background: rgb(13, 13, 178);
            box-shadow: 2px 2px 6px black; color: white; text-transform: uppercase; font-size: 0.8em; cursor:pointer }
        .form-input { width:325px; height: 30px; padding: 0 5px; background: rgb(240,240,240); border: none; }
        textarea { height:auto!important; }
        label {display:block; font-size: 0.7em; font-weight:bold; text-transform:uppercase}
        .messages { border: 2px solid #9d1717; font-size: 0.8em; padding:10px; background: rgb(220,220,220); text-align: center; }
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
<?php include_once('Navigation.php');?>
<h4 id="secondHeader" style="padding-left:10px; margin:10px 0; text-align:left">
    <?=(isset($pagetitle)? $pagetitle : 'Application');?>
</h4>
<hr style="margin-left:5px;">
<!-- Page Start -->
<div class="page" id="page">
<div id="top"></div>
<!-- Message Holder -->
<?php if(!empty($message)): ?>
    <div class="messages">
        <strong><u>RESPONSE</u></strong>
        <br>
        <?php foreach($message as $mess): ?>
            <span><?=$mess;?></span><br>
        <?php endforeach;?>
    </div>
    <script type="text/javascript">
    setTimeout(() => {document.querySelector(".messages").style.display = "none";}, 5000);
    </script>
<?php endif; ?>
