<?php $pagetitle = 'Students'; ?>
<?php include_once(dirname(dirname(__FILE__) ). '/Header.php');?>
    <div class="form-container" id="formContainer">
        <div class="section-title" style="">Add New Student</div>
        <form action="" method="POST">
            <input hidden name="ID" id="ID"/>
            <div class="form-group">
                <label for="lastName">Last Name: *</label>
                <input autofocus type="text" class="form-input" name="lastName" id="lastName" required />
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
                <label for="admissionNumber">Admission Number: </label>
                <input type="text" class="form-input" name="admissionNumber" id="admissionNumber" />
            </div>
            <div class="form-group">
                <label for="examinationNumber">Examination Number: *</label>
                <input type="text" class="form-input" name="examinationNumber" id="examinationNumber" required />
            </div>
            <div class="form-group">
                <label for="classCategoryID">Class Category: *</label>
                <select style="width:100%" class="form-input" name="classCategoryID" id="classCategoryID" required>
                    <option value="0">Please Select</option>
                    <?php foreach($classCategory as $cC): ?>
                    <option value="<?=$cC['ID'];?>"><?=$cC['classCategoryTitle'];?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="form-group">
                <button name="create" id="submitButton" class="delete">Submit</button>
            </div>
        </form>
        <div class="form-group" style="">
            <a href="<?=$routeBase;?>students/upgrade" ><button class="">&rarr; Goto Students Upgrade &rarr;</button></a>
        </div>
        <br>
    </div>
    <div class="list-container">
        <div class="section-title">
            Existing Students
        </div>
        <div class="" style="margin-bottom:15px">
            <form method="POST">
            <div class="form-group">
                <label for="classCategoryID">Class Category: *</label>
                <select style="width:100%" class="form-input" name="classCategoryID" required>
                    <option value="-1">Please Select</option>
                    <option value="0">Show All Students</option>
                    <?php foreach($classCategory as $cC): ?>
                    <option value="<?=$cC['ID'];?>"><?=$cC['classCategoryTitle'];?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="form-group">
                <button name="fetchStudents" class="delete">fetch students</button>
            </div>
            </form>
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
                            <th>Admission Number</th>
                            <th>Examination Number</th>
                            <th>Class Category Title</th>
                            <th style="width:10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $x = 1; foreach($students as $student):?>
                        <tr class="<?=($x%2==0?'form-input':'')?> class-category-<?=$student['classCategoryID'];?>" style="visibility:visible">
                            <td><?=$x;?></td>
                            <td><?="{$student['lastName']}"?></td>
                            <td><?="{$student['firstName']}"?></td>
                            <td><?="{$student['middleName']}"?></td>
                            <td><?=$student['admissionNumber'];?></td>
                            <td><?=$student['examinationNumber'];?></td>
                            <td><?=$student['classCategoryTitle'];?></td>
                            <td>
                                <form action=""  style="width:auto; display:inline-block" method="POST" onsubmit="return false">
                                    <input type="text" name="delete" value="<?=$student['ID'];?>" hidden>
                                    <button class="delete" onclick="confirm('Are you sure?') ? this.parentElement.submit() : null">Delete</button>
                                </form>
                                <button 
                                    class="update" 
                                    onclick="update(
                                        `<?=$student['ID'];?>`,
                                        `<?=$student['lastName'];?>`,
                                         `<?=$student['firstName'];?>`,
                                         `<?=$student['middleName']?>`,
                                         `<?=$student['admissionNumber'];?>`,
                                         `<?=$student['examinationNumber'];?>`,
                                         `<?=$student['classCategoryID'];?>`
                                    );" 
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

        const [ID, lastName, firstName, middleName, admissionNumber, examinationNumber, classCategoryID] = args;
        
        document.querySelector("#ID").value = ID;
        
        document.querySelector("#lastName").value = lastName;
        document.querySelector("#lastName").setAttribute("autofocus", "autofocus");

        document.querySelector("#firstName").value = firstName;

        document.querySelector("#middleName").value = middleName;

        document.querySelector("#admissionNumber").value = admissionNumber;

        document.querySelector("#examinationNumber").value = examinationNumber;

        document.querySelector("#classCategoryID").value = classCategoryID;

        document.querySelector("#submitButton").setAttribute("class", "update");
        document.querySelector("#submitButton").setAttribute("name", "update");

        document.querySelector("#submitButton").textContent = "Update";

        window.location = "#top";
    }

    function toggleView(ID) {
        document.querySelectorAll(".class-category-" + ID).forEach((element)=>{
            element.style.visibility = element.style.visibility == 'collapse' ? 'visible' : 'collapse';
        });
    }
    </script>
<?php include_once(dirname(dirname(__FILE__) ). '/Footer.php'); ?>
