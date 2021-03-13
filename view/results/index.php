<?php $pagetitle = 'Results'; ?>
<?php include_once(dirname(dirname(__FILE__) ). '/Header.php');?>
    <div class="form-container">
        <div class="section-title" style="">preview or save Results</div>
        <form id="processResult" method="POST" onsubmit="return false;">
            <div class="form-group">
                <label for="classCategoryID">Class Category: *</label>
                <select style="width:100%" class="form-input" name="classCategoryID" id="classCategoryID" required>
                <option value="0">Please Select</option>
                <?php foreach($classCategory as $cC):?>
                <option value="<?=$cC['ID'];?>"><?=$cC['classCategoryTitle'];?></option>
                <?php endforeach;?>
                </select>
            </div>
            <div class="form-group">
                <label for="examinationID">Examination: *</label>
                <select style="width:100%" class="form-input" name="examinationID" id="examinationID" required>
                <option value="0">Please Select</option>
                <?php foreach($examination as $e):?>
                <option value="<?=$e['ID'];?>"><?=$e['examinationTitle'];?></option>
                <?php endforeach;?>
                </select>
            </div>
            <div class="form-group">
                <label for="resultType">Result Type: *</label>
                <select style="width:100%" class="form-input" name="resultType" id="resultType" required>
                    <option value="0">Please Select</option>
                    <option value="resultAnalysis">Result Analysis</option>
                    <option value="scoreSheet">Score Sheet</option>
                    <option value="studentResult">Student Result</option>
                </select>
            </div>
            <div class="form-group">
                <label><input type="checkbox" id="previewToggle" name="preview" value="preview" disabled /> show preview only (don't save) </label>
                <input hidden name="processResult" />
                <button class="delete">PROCESS RESULTS</button>
            </div>
        </form>
        <p style="text-transform:uppercase; font-size: 0.8em"><strong>Note:</strong> any new result processed and saved will overwrite corresponding previous ones.</p>
        <br>
    </div>
    <div class="list-container">
        <div class="section-title">
            SHOW PROCESSED RESULTS
        </div>
        <div class="" style="margin-bottom:15px">
            <form method="POST">
            <div class="form-group">
                <label for="classCategoryID">Class Category: *</label>
                <select style="width:100%" class="form-input" name="classCategoryID" required>
                    <option value="-1">Please Select</option>
                    <option value="0">All Class Categories</option>
                    <?php foreach($classCategory as $cC):?>
                    <option value="<?=$cC['ID'];?>"><?=$cC['classCategoryTitle'];?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="form-group">
                <label for="examinationID">Examination: *</label>
                <select style="width:100%" class="form-input" name="examinationID" required>
                    <option value="-1">Please Select</option>
                    <option value="0">All Examinations</option>
                    <?php foreach($examination as $e):?>
                    <option value="<?=$e['ID'];?>"><?=$e['examinationTitle'];?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="form-group">
                <label for="resultType">Result Type: *</label>
                <select style="width:100%" class="form-input" name="resultType" required>
                    <option value="-1">Please Select</option>
                    <option value="0">All Result Types</option>
                    <option value="resultAnalysis">Result Analysis</option>
                    <option value="scoreSheet">Score Sheet</option>
                    <option value="studentResult">Student Result</option>
                </select>
            </div>
            <div class="form-group">
                <button name="fetchResults" class="delete">fetch Results</button>
            </div>
            </form>
        </div>
        <div class="existing">
            <div class="list" style="display:<?=(isset($_GET['page']) ? 'block': 'block');?>;">
                <table class="list-table" cellpadding="5" style="width:98%">
                    <thead>
                        <tr class="form-input" style="height:30px; text-transform:uppercase">
                            <th>S/N</th>
                            <th>Result Type </th>
                            <th>Examination Title </th>
                            <th>Class Category </th>
                            <th>Student Name </th>
                            <th>Subject Title </th>
                            <th style="">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $x = 1; foreach($results as $result):?>
                        <tr class="<?=($x%2==0?'form-input':'')?>">
                            <td><?=$x;?></td>
                            <td><?="{$result['resultType']}"?></td>
                            <td><?=!empty($result['examinationTitle']) ?$result['examinationTitle']: null ?></td>
                            <td><?=!empty($result['classCategoryTitle']) ?$result['classCategoryTitle']: null ?></td>
                            <td><?=!empty($result['lastName']) && !empty($result['firstName']) ? $result['lastName'] .' '. $result['firstName']: null ?></td>
                            <td><?=!empty($result['subjectTitle']) ?$result['subjectTitle']: null ?></td>
                            <td>
                                <form action=""  style="width:auto; display:inline-block" method="POST" onsubmit="return false">
                                    <input type="text" name="delete" value="<?=$result['ID'];?>" hidden>
                                    <button class="delete" onclick="confirm('Are you sure?') ? this.parentElement.submit() : null">Delete</button>
                                </form>
                                <form action="" style="width:auto; display:inline-block" method="POST" target="blank">
                                    <input type="text" name="preview" value="" hidden>
                                    <input type="text" name="ID" value="<?=$result['ID'];?>" hidden>
                                    <button class="update" style="">preview</button>
                                </form>
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
        document.querySelector('#resultType').addEventListener("change", function(){
            if(event.target.value === 'resultAnalysis') {
                document.querySelector('#previewToggle').checked = false;
                document.querySelector('#previewToggle').removeAttribute('disabled')
            } else {
                document.querySelector('#previewToggle').checked = false;
                document.querySelector('#previewToggle').setAttribute('disabled', 'disabled')
            }
            
        })
        document.querySelector('#processResult').addEventListener("submit", processResult);
        function processResult(event) {

            var classCategoryID = event.target.classCategoryID.value
            var examinationID = event.target.examinationID.value
            var resultType = event.target.resultType.value

            if(classCategoryID==='0' || examinationID==='0' || resultType==='0') {
                alert("Please select valid class category, examination and result type.");
                return;
            }

            event.target.submit();

        }
        
    </script>

<?php include_once(dirname(dirname(__FILE__) ). '/Footer.php'); ?>
