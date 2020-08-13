<?php $pagetitle = 'Subjects'; ?>
<?php include_once(dirname(dirname(__FILE__) ). '/Header.php');?>
    <div class="form-container">
        <div class="section-title" style="">Add New Subject</div>
        <form action="" method="POST">
            <input hidden name="ID" id="ID"/>
            <div class="form-group">
                <label for="subjectTitle">Subject Title: *</label>
                <input tabindex=1 autofocus type="text" class="form-input" name="subjectTitle" id="subjectTitle" required />
            </div>
            <div class="form-group">
                <button name="create"  id="submitButton" class="delete">Submit</button>
            </div>
        </form>
        <br>
    </div>
    <div class="list-container">
        <div class="section-title">
            Existing Subjects
        </div>
        <div class="existing">
            <div class="list" style="display:<?=(isset($_GET['page']) ? 'block': 'block');?>;">
                <table class="list-table" cellpadding="5" style="width:98%">
                    <thead>
                        <tr class="form-input" style="height:30px; text-transform:uppercase">
                            <th>S/N</th>
                            <th>Title</th>
                            <th>Slug</th>
                            <th style="width:10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $x = 1; foreach($subjects as $subject):?>
                        <tr class="<?=($x%2==0?'form-input':'')?>">
                            <td><?=$x;?></td>
                            <td><?="{$subject['subjectTitle']}"?></td>
                            <td><?="{$subject['subjectSlug']}"?></td>
                            <td>
                                <form action=""  style="width:auto; display:inline-block" method="POST" onsubmit="return false">
                                    <input type="text" name="delete" value="<?=$subject['ID'];?>" hidden>
                                    <button class="delete" onclick="confirm('Are you sure?') ? this.parentElement.submit() : null">Delete</button>
                                </form>
                                <button 
                                    class="update" 
                                    onclick="update(`<?=$subject['ID'];?>`, `<?=$subject['subjectTitle'];?>`);" 
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

            const [ID, subjectTitle] = args;
            
            document.querySelector("#ID").value = ID;
            
            document.querySelector("#subjectTitle").value = subjectTitle;
            document.querySelector("#subjectTitle").setAttribute("autofocus", "on");

            document.querySelector("#submitButton").setAttribute("class", "update");
            document.querySelector("#submitButton").setAttribute("name", "update");

            document.querySelector("#submitButton").textContent = "Update";

            window.location = "#top";
        }
    </script>
<?php include_once(dirname(dirname(__FILE__) ). '/Footer.php'); ?>
