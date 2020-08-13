<?php 
if(is_file(dirname(__FILE__) .'/core/Config.php')) {
    echo "<p style='text-align:center; padding-top: 30px'>Previous installation found. Please goto <code><a href='login'>login page</a></code> or remove the <code>Config.php</code> file in <code>core</code> folder before a fresh installation.</p>";
    die();
}

if($_POST) {
    $message = [];
    $HOST = trim($_POST['HOST']);
    $DBNAME = trim($_POST['DBNAME']);
    $USERNAME = trim($_POST['USERNAME']);
    $PASSWORD = trim($_POST['PASSWORD']);
    $BASEURL = trim($_POST['BASEURL']);
    $SUPERADMINUSERNAME = trim($_POST['SUPERADMINUSERNAME']);
    $SUPERADMINPASSWORD = trim($_POST['SUPERADMINPASSWORD']);

    $ConfigSample = file_get_contents(dirname(__FILE__) .'/core/Config.php.bak');

    try {
        $conn = new PDO("mysql:host=" . $HOST . ";dbname=" . $DBNAME, $USERNAME, $PASSWORD);
    } catch(PDOException $exception) {
        $message[] = $exception->getMessage();
    }

    if(empty($message)) {
        // check if there are tables
        $query = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$DBNAME'";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $rowCount = $stmt->fetch()[0];

        if($rowCount > 0) $message[] = "Database is not empty";

        if($rowCount == 0) {
            // create the necessary tables.
            $db = $conn;

            //Bring in all the model
            $models = scandir('model/'); array_shift($models); array_shift($models);

            if(empty($models)) $message[] = "No model files found";

            if(!empty($models)) {

                $createdTablesCount = 0;

                foreach($models as $model){ 
                    if(
                        is_file('model/'.$model) &&
                        pathinfo('model/'.$model, PATHINFO_EXTENSION) == 'php' &&
                        $model !== 'Database.php' 
                    ) {

                        include_once 'model/'.$model;
                                
                        $modelName = "\Model\\" . pathinfo($model, PATHINFO_FILENAME);

                        $modelHandle = new $modelName;
                            
                        $modelHandle->createDBTable();

                        $createdTablesCount++;

                    }
                }

                // check database again
                $query = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$DBNAME'";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $rowCount = $stmt->fetch()[0];
                
                if($rowCount != $createdTablesCount) $message[] = "It seems like some tables were not created. Please cross check database.";

                if($rowCount == $createdTablesCount) {
                    // create the super admin
                    $admin = new \Model\Admin;
                    $admin->lastName = "Super";
                    $admin->firstName = "Admin";
                    $admin->userName = $SUPERADMINUSERNAME;
                    $admin->password = $SUPERADMINPASSWORD;
                    if ( !$admin->create() ) $message[] = "Unable to create Super Admin at the moment. You might need to create one manually or try again later.";

                    // create the config file
                    $Config = sprintf($ConfigSample, $HOST, $DBNAME, $USERNAME, $PASSWORD, $BASEURL);
                    file_put_contents(dirname(__FILE__) .'/core/Config.php', $Config);
                    $message[] = 'Installation complete. Please login to continue';

                }
            }
        }
    }

}?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Page</title>
    <style>
        body{ font-family: 'Roboto'; font-size:1.3em; background-color: rgb(200,200,200); text-align: center; margin: auto; transition: smooth; }
        .container {width: 90%; margin: auto; height:695px; box-shadow: 2px 2px 50px; padding: 10px; margin-top: 30px; background-color: white}
        .form-group { width:600px; display:inline-block; margin: 10px 5px; border-bottom: 2px solid rgba(250,150,150); border-width: 4px; padding-bottom: 5px; }
        button { width: 99%; height: 35px; margin: 4px 0; border: none; background: rgb(13, 13, 178);
            box-shadow: 2px 2px 6px black; color: white; text-transform: uppercase; font-size: 0.8em; cursor:pointer }
        .form-input { width:590px; height: 30px; padding: 0 5px; background: rgb(240,240,240); border: none; }
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
        small {font-size: small; text-transform:uppercase}
    </style>
</head>
<body>
<div class="container">
<h4 style="padding-left:10px; margin:10px 0; text-transform:uppercase">RESULT MANAGEMENT SYSTEM INSTALLER</h4>
<hr style="margin-left:5px;">
<!-- Page Start -->
<div class="page">
    <?php if(!empty($createdTablesCount)): ?>
        <div onclick="window.location = 'login'" style="width: 200px; text-align: center; margin:30px auto 0 auto; cursor:pointer; padding:5px; border: 2px solid red">GOTO LOGIN PAGE</div>
    <?php endif;?>
    <div class="form-container" style="padding-top: 40px; margin:auto; text-align:center">
        <form method="POST" action="">
        <u>PLEASE FILL IN THE DATABASE DETAILS</u><br><br>
        <?php if(!empty($message)): ?>
            <div class="messages">
                <strong><u>RESPONSE</u></strong>
                <br>
                <?php foreach($message as $mess): ?>
                    <span><?=$mess;?></span><br>
                <?php endforeach;?>
            </div>
            <br>
        <?php endif; ?>
            <div class="form-group" >
                <label for="HOST">Database Host:</label>
                <input tabindex=1 type="text" class="form-input" value="localhost" name="HOST" required />
                <small><strong>Note:</strong> if you do not know your host, localhost works fine most of the time.</small>
            </div> <br>
            <div class="form-group" >
                <label for="DBNAME">Database Name:</label>
                <input type="text" class="form-input" value="rms" name="DBNAME" required />
                <small><strong>Note:</strong> this database should not contain any table already.</small>
            </div> <br>
            <div class="form-group" >
                <label for="USERNAME">Database Username:</label>
                <input type="text" class="form-input" value="root" name="USERNAME" required />
            </div> <br>
            <div class="form-group" >
                <label for="PASSWORD">Database Password:</label>
                <input type="text" class="form-input" name="PASSWORD" />
                <small><strong>Note:</strong> if you are on localhost, this value might be empty.</small>
            </div> <br>
            <div class="form-group" >
                <label for="BASEURL">Base URL:</label>
                <input type="text" class="form-input" value="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . str_replace( $_SERVER['DOCUMENT_ROOT'],'', dirname($_SERVER['SCRIPT_FILENAME']) ) . '/';?>" name="BASEURL" />
                <small><strong>Note:</strong> this value is usually correct. Only make adjustments if you're sure it is wrong.</small>
            </div> <br>
            <div class="form-group" >
                <label for="SUPERADMINUSERNAME">Super admin username:</label>
                <input type="text" class="form-input" name="SUPERADMINUSERNAME" required />
                <small><strong>Note:</strong> First user account to be created.</small>
            </div> <br>
            <div class="form-group" >
                <label for="SUPERADMINPASSWORD">Super admin password:</label>
                <input type="text" class="form-input" name="SUPERADMINPASSWORD" required />
                <small><strong>Note:</strong> First user account to be created.</small>
            </div> <br>
            <div class="form-group">
                <button class="delete">install</button>
            </div>
        </form>
        <br>
    </div>
<!-- Page End -->
</div>
<hr>
    <span style="background-color: #9d1717; width:100%; display:block;font-weight:bold; text-transform:uppercase; font-size: 0.7em; padding:3px 0;">Designed by: <a class="link" href="https://fb.me/kalukalukalu">Contact</a></span>
</div> 
</body>
</html>