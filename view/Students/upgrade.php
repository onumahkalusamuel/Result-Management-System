<?php $pagetitle = 'Upgrade Students'; ?>
<?php include_once(dirname(dirname(__FILE__) ). '/Header.php');?>
    <div class="form-container" id="formContainer">
        <div class="section-title" style="">Move Students:</div>
        <!-- <small>NOTE: STUDENTS WILL BE MERGED TOGETHER IF YOU DO NOT SELECT CLASSES CORRECTLY. PLEASE GO THROUGH ALL THE SELECTIONS</small> -->
        <form action="" method="POST">
        <?php foreach($classCategory as $c): ?>
            <div class="form-group">
                <label for="classCategoryID">From: </label>
                    <input value="<?=$c['ID'];?>" hidden name="class[<?=$c['ID']?>][from]" />
                    <input value="<?=$c['classCategoryTitle'];?>" class="form-input" disabled/>
            </div>
            <div class="form-group">
                <label for="classCategoryID">To: </label>
                <select style="width:100%" class="form-input" name="class[<?=$c['ID']?>][to]" required>
                    <?php foreach($classCategory as $cC): ?>
                    <option <?=($c['ID']==$cC['ID'] ? 'selected': '')?> value="<?=$cC['ID'];?>"><?=$cC['classCategoryTitle'];?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <br>
            <?php endforeach; ?>
            <div class="form-group" style="">
                <button class="delete" type="submit">Upgrade Students</button>
            </div>
        </form>
        <br>
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
