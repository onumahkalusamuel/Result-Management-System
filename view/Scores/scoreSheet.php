<?php $pagetitle = 'Empty Score Sheet';?>
<?php include_once(dirname(dirname(__FILE__) ). '/Header.php');?>
    <div class="form-container">
        <div class="section-title" style="">Generage Empty Score Sheet</div>
        <form id="fetchScoresForm" method="POST" onsubmit="return false;" target="blank">
            <input hidden name="generateEmptyScoreSheet"/>
            <div class="form-group">
                <label for="classCategoryID">Class Category: *</label>
                <select style="width:100%" class="form-input" name="classCategoryID" id="classCategoryID" onchange="loadSubjects()" required>
                    <option value="0">Please Select</option>
                    <?php foreach($classCategory as $cC):?>
                        <option  value="<?=$cC['ID'];?>"><?=$cC['classCategoryTitle'];?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <!-- Holder for the subjects offered by the classes -->
            <?php foreach($classCategory as $cC): $ID = $cC['ID']; ?>
                <div id="subjectOptions-<?=$ID;?>" style="display:none">
                    <option value="0">Please Select</option>
                    <?php if(!empty($papers[$ID])) foreach($papers[$ID] as $p):?>
                    <option value="<?=$p['ID'];?>"><?=($p['subjectTitle']);?> (<?=implode(', ', $p['papers']);?>)</option>
                    <?php endforeach;?>
                </div>
            <?php endforeach;?>
            <div class="form-group">
                <label for="subjectID">Subject: *</label>
                <select style="width:100%" class="form-input" name="subjectID" id="subjectID"></select>
            </div>
            <div class="form-group">
                <label for="examinationID">Examination: *</label>
                <select style="width:100%" class="form-input" name="examinationID" id="examinationID" required>
                <option value="0">Please Select</option>
                <?php foreach($examination as $exam):?>
                <option value="<?=$exam['ID'];?>"><?=$exam['examinationTitle'];?></option>
                <?php endforeach;?>
                </select>
            </div>
            <div class="form-group">
                <button class="delete" id="fetchScoresButton">Generage Empty Score Sheet</button>
            </div>
        </form>
        <br>
    </div>

    <script type="text/javascript">
        document.querySelector('#fetchScoresButton').addEventListener("click", function(){
            var theForm = document.querySelector('#fetchScoresForm');
            if(theForm.subjectID.value === '0' || theForm.examinationID.value === '0') {
                alert("Please select valid paper and examination."); 
                return false;
            }
            theForm.submit();
        });
        
        function loadSubjects() {
            console.log(event.target.value);
            var classCategoryID = document.querySelector("#classCategoryID").value;
            var subjectID = document.querySelector("#subjectID");

            subjectID.innerHTML = 
                (classCategoryID == 0) ?
                subjectID.innerHTML = "" :
                document.querySelector("#subjectOptions-"+classCategoryID).innerHTML;
            
        }
    </script>

<?php include_once(dirname(dirname(__FILE__) ). '/Footer.php'); ?>
