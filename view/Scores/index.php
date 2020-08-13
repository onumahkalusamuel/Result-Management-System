<?php $pagetitle = 'Scores';?>
<?php include_once(dirname(dirname(__FILE__) ). '/Header.php');?>
    <div class="form-container">
        <div class="section-title" style="">Add Scores</div>
        <form id="fetchScoresForm" method="POST" onsubmit="return false;">
            <input hidden name="fetchStudentScore"/>
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
                <button class="delete" id="fetchScoresButton">FETCH STUDENTS / SCORES</button>
            </div>
        </form>
        <div class="form-group" style="">
            <a href="<?=$routeBase;?>scores/scoresheet" ><button class="">&rarr; goto empty score sheet &rarr;</button></a>
        </div>
        <br>
    </div>
    <div class="list-container">
        <?php if(!empty($scores)) : ?>
            <div class="section-title">
                STUDENTS' SCORES
            </div>
            <p style="font-size:0.9em">
                Examination Title: <strong><?=$scores['other']['examinationTitle'];?></strong>;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                Class Category: <strong><?=$scores['other']['classCategoryTitle'];?></strong>;&nbsp;&nbsp;&nbsp;&nbsp;
                Subject: <strong><?=$scores['other']['subjectTitle'];?></strong></p>
        <form action="" id="actualForm" method="POST" onsubmit="return false;">
            <div class="existing">
                <div class="list">
                    <table class="list-table" cellpadding="5" style="width:98%">
                        <thead>
                            <tr class="form-input" style="height:30px; text-transform:uppercase">
                                <th>S/N</th>
                                <th>Student Name</th>
                                <th>Admission Number</th>
                                <th>Examination Number</th>
                                <?php foreach($scores['header'] as $header): ?>
                                    <th><?=$header;?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                        <input hidden name="examinationID" value="<?=$scores['other']['examinationID'];?>">
                        <input hidden name="create" value="">
                            <?php $x = 1; foreach($scores['body'] as $studentID => $body):?>
                            <tr class="<?=($x%2==0?'form-input':'')?>">
                                <td><?=$x;?></td>
                                <td><?="{$body['studentDetails']['name']}"?></td>
                                <td><?="{$body['studentDetails']['admissionNumber']}"?></td>
                                <td><?="{$body['studentDetails']['examinationNumber']}"?></td>
                                
                                <?php foreach($body['scores'] as $paperID => $score):?>
                                    <td>
                                        <input name="scores[<?=$studentID;?>][<?=$paperID;?>][score]" style="width: 100px" class="form-input" type="text" value="<?=$score['score'];?>"/>
                                        <input hidden name="scores[<?=$studentID;?>][<?=$paperID;?>][scoreID]" value="<?=$score['scoreID'];?>"/>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php $x++; endforeach; ?>
                        </tbody>
                    </table>
                    <br>
                </div>
            </div>
            <div class="form-group">
                <input hidden name="create">
                <button class="delete submitter">Submit</button>
            </div>
        </form>
        <?php endif;?>

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
        
        document.querySelector('.submitter').addEventListener("click", function(){
            confirm("Sure to submit?") ? 
            document.querySelector('#actualForm').submit() :
             null
        });
        var queue = {}; // To hold the selected class category and subject combinations

        function addToQueue(event) {

            var classCategoryID = event.target.classCategoryID.value
            var subjectID = event.target.subjectID.value

            if(classCategoryID==='0' || subjectID==='0') {
                alert("Please select valid class category and subject");
                return;
            }

            if(undefined === queue[classCategoryID]) queue[classCategoryID] = [];

            if(queue[classCategoryID].indexOf(subjectID) > -1) {
                alert("Already in queue...");
                return;
            }

            queue[classCategoryID].push(subjectID);

            addToDOM(classCategoryID, subjectID);

        }

        function addToDOM(classCategoryID, subjectID) {
            document.querySelector("#fieldSet" + classCategoryID + subjectID).style.display = "block";
        }

        function addPaper(classCategoryID, subjectID) {
            var num = Math.random(1, 500000) * Math.random(1, 500000) * 1000000000;
            var paperTemplate = `<div class="oneSet">
                <div class="form-group">
                    <label for="paperTitle">Paper Title: *</label>
                    <input type="text" class="form-input" name="papers[${classCategoryID}][${subjectID}][${num}][paperTitle]" />
                </div>
                <div class="form-group">
                    <label for="maximumScore">Maximum Score: *</label>
                    <input type="number" class="form-input" name="papers[${classCategoryID}][${subjectID}][${num}][maximumScore]" />
                </div>
            </div>`;

            var span = document.createElement('span');
            span.innerHTML = paperTemplate;

            document.querySelector("#fieldSet" + classCategoryID + subjectID).append(span);
        }
        
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
