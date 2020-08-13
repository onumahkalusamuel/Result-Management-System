<?php $pagetitle = 'Class Category'; ?>
<?php include_once(dirname(dirname(__FILE__) ). '/Header.php');?>
    <div class="form-container">
        <div class="section-title" style="">Add New Class Category</div>
        <form action="" method="POST">
            <input hidden name="ID" id="ID"/>
            <div class="form-group">
                <label for="classCategoryTitle">Class Category Title: *</label>
                <input type="text" class="form-input" name="classCategoryTitle" id="classCategoryTitle" required />
            </div>
            <div class="form-group">
                <label for="gradingID">Grading: *</label>
                <select style="width:100%" class="form-input" name="gradingID" id="gradingID" required>
                    <option value="0">Please Select</option>
                    <?php foreach($grading as $g): ?>
                        <option value="<?=$g['ID'];?>"><?=$g['gradingTitle'];?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="form-group">
                <button name="create" id="submitButton" class="delete">Submit</button>
            </div>
        </form>
        <br>
    </div>
    <div class="list-container">
        <div class="section-title">
            Existing Class Categories
        </div>
        <div class="existing">
            <div class="list" style="display:<?=(isset($_GET['page']) ? 'block': 'block');?>;">
                <table class="list-table" cellpadding="5" style="width:98%">
                    <thead>
                        <tr class="form-input" style="height:30px; text-transform:uppercase">
                            <th>S/N</th>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Grading Title</th>
                            <th style="width:10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $x = 1; foreach($classCategory as $cC):?>
                        <tr class="<?=($x%2==0?'form-input':'')?>">
                            <td><?=$x;?></td>
                            <td><?="{$cC['classCategoryTitle']}"?></td>
                            <td><?="{$cC['classCategorySlug']}"?></td>
                            <td><?="{$cC['gradingTitle']}"?></td>
                            <td>
                                <form action=""  style="width:auto; display:inline-block" method="POST" onsubmit="return false">
                                    <input type="text" name="delete" value="<?=$cC['ID'];?>" hidden>
                                    <button class="delete" onclick="confirm('Are you sure?') ? this.parentElement.submit() : null">Delete</button>
                                </form>
                                <button 
                                    class="update" 
                                    onclick="update(`<?=$cC['ID'];?>`, `<?=$cC['classCategoryTitle'];?>`, `<?=$cC['gradingID'];?>`);" 
                                    style="width:auto; display:inline-block"
                                >update</button>
                            </td>
                        </tr>
                        <?php $x++; endforeach; ?>
                    </tbody>
                </table>
                <br>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function update(...args) {

            if(!confirm("Are you sure you want to edit this record?")) {
                return false;
            }

            const [ID, classCategoryTitle, gradingID] = args;
            
            document.querySelector("#ID").value = ID;
            
            document.querySelector("#classCategoryTitle").value = classCategoryTitle;
            document.querySelector("#classCategoryTitle").setAttribute("autofocus", "autofocus");
            document.querySelector("#gradingID").value = gradingID;

            document.querySelector("#submitButton").setAttribute("class", "update");
            document.querySelector("#submitButton").setAttribute("name", "update");

            document.querySelector("#submitButton").textContent = "Update";

            window.location = "#top";
        }
    </script>
<?php include_once(dirname(dirname(__FILE__) ). '/Footer.php'); ?>
