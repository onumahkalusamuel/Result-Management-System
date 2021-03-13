<?php $pagetitle = 'Admin'; ?>
<?php include_once(dirname(dirname(__FILE__) ). '/Header.php');?>
    <div class="form-container" id="formContainer">
        <div class="section-title" style="">Add New Admin</div>
        <form action="" method="POST">
            <input hidden name="ID" id="ID"/>
            <div class="form-group">
                <label for="lastName">Last Name: *</label>
                <input type="text" class="form-input" name="lastName" id="lastName" required />
            </div>
            <div class="form-group">
                <label for="firstName">First Name: *</label>
                <input type="text" class="form-input" name="firstName" id="firstName" required />
            </div>
            <div class="form-group">
                <label for="middleName">Middle Name: </label>
                <input type="text" class="form-input" name="middleName" id="middleName" />
            </div>
            <div class="form-group">
                <label for="userName">Username: *</label>
                <input type="text" class="form-input" name="userName" id="userName" required />
            </div>
            <div class="form-group">
                <label for="password">Password: *</label>
                <input type="text" class="form-input" name="password" id="password" required />
            </div>
            <div class="form-group">
                <button name="create" id="submitButton" class="delete">Submit</button>
            </div>
            <label class="form-group" style="display:none" id="passwordMessage" >Leave password field empty if you do not wish to change the password.</label>
        </form>
        <br>
    </div>
    <div class="list-container">
        <div class="section-title">
            Existing Admin
        </div>
        <div class="existing">
            <div class="list" style="display:<?=(isset($_GET['page']) ? 'block': 'block');?>;">
                <table class="list-table" cellpadding="5" style="width:98%">
                    <thead>
                        <tr class="form-input" style="height:30px; text-transform:uppercase">
                            <th>S/N</th>
                            <th>Last Name</th>
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Username</th>
                            <th style="width:10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $x = 1; foreach($admins as $admin):?>
                        <tr class="<?=($x%2==0?'form-input':'')?>">
                            <td><?=$x;?></td>
                            <td><?="{$admin['lastName']}"?></td>
                            <td><?="{$admin['firstName']}"?></td>
                            <td><?="{$admin['middleName']}"?></td>
                            <td><?=$admin['userName'];?></td>
                            <td>
                                <form action=""  style="width:auto; display:inline-block" method="POST" onsubmit="return false">
                                    <input type="text" name="delete" value="<?=$admin['ID'];?>" hidden>
                                    <button class="delete" onclick="confirm('Are you sure?') ? this.parentElement.submit() : null">Delete</button>
                                </form>
                                <button 
                                    class="update" 
                                    onclick="update(`<?=$admin['ID'];?>`, `<?=$admin['lastName'];?>`, `<?=$admin['firstName'];?>`,`<?=$admin['middleName']?>`, `<?=$admin['userName'];?>`);" 
                                    style="width:auto; display:inline-block"
                                >update</button>
                            </td>
                        </tr>
                        <?php $x++; endforeach; ?>
                    </tbody>
                </table>
                <br>
                <br>
                <div class="controls">
                    <?php if(!empty($paging->first)) :?>
                        <a href="?k=<?=$_GET['k'];?>&page=1"> <button class="" style="margin:auto"> << </button> </a>
                    <?php endif;?>

                    <?php if(!empty($paging->pages)) foreach($paging->pages as $pps): 
                        $page_url = $pps->current_page == "no" ? "?k={$_GET['k']}&page={$pps->page}": "#";
                    ?>
                    <a href='<?=$page_url;?>'>
                        <button 
                            class="<?=($pps->current_page=='yes'? 'currentpage': null)?>" 
                            <?=($pps->current_page=='yes'? 'disabled': null)?>
                        >
                            <?=$pps->page?>
                        </button>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
    function update(...args) {

        if(!confirm("Are you sure you want to edit this record?")) {
            return false;
        }

        const [ID, lastName, firstName, middleName, userName] = args;
        
        document.querySelector("#ID").value = ID;
        
        document.querySelector("#lastName").value = lastName;
        document.querySelector("#lastName").setAttribute("autofocus", "autofocus");

        document.querySelector("#firstName").value = firstName;

        document.querySelector("#middleName").value = middleName;

        document.querySelector("#userName").value = userName;
        document.querySelector("#userName").setAttribute("disabled", "disabled");
        
        document.querySelector("#password").removeAttribute("required");

        document.querySelector("#submitButton").setAttribute("class", "update");
        document.querySelector("#submitButton").setAttribute("name", "update");

        document.querySelector("#submitButton").textContent = "Update";

        document.querySelector("#passwordMessage").style.display = "block";

        window.location = "#page";
    }
    </script>
<?php include_once(dirname(dirname(__FILE__) ). '/Footer.php'); ?>
