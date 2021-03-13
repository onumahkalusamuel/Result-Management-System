<?php $pagetitle = 'Grading System'; ?>
<?php include_once(dirname(dirname(__FILE__) ). '/Header.php');?>
    <div class="form-container">
        <div class="section-title" style="">Add New Grading Item</div>
        <form action="" method="POST">
            <input hidden name="ID" id="ID"/>
            <div class="form-group">
                <label for="gradingID">Grading: *</label>
                <select style="width:100%" class="form-input" name="gradingID" id="gradingID" required>
                    <option value="0">Please Select</option>
                    <?php foreach($grading as $grad): ?>
                        <option value="<?=$grad['ID'];?>"><?=$grad['gradingTitle'];?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="form-group">
                <label for="minimumScore">Minimum Score (Lower Limit): *</label>
                <input type="number" class="form-input" name="minimumScore" id="minimumScore" required />
            </div>
            <div class="form-group">
                <label for="maximumScore">Maximum Score (Upper Limit): *</label>
                <input type="number" class="form-input" name="maximumScore" id="maximumScore" required />
            </div>
            <div class="form-group">
                <label for="grade">Grade: *</label>
                <input type="text" class="form-input" name="grade" id="grade" required />
            </div>
            <div class="form-group">
                <label for="ordering">Ordering: </label>
                <input type="number" class="form-input" name="ordering" id="ordering" />
            </div>
            <div class="form-group">
                <button name="create" id="submitButton" class="delete">Submit</button>
            </div>
        </form>
        <br>
    </div>
    <div class="list-container">
        <div class="section-title">
            Existing Grading Items
        </div>
        <div class="" style="margin-bottom:15px">
            <form method="POST">
            <div class="form-group">
                <label for="gradingID">Grading: *</label>
                <select style="width:100%" class="form-input" name="gradingID" required>
                    <option value="-1">Please Select</option>
                    <option value="0">Show All Grading Items</option>
                    <?php foreach($grading as $g): ?>
                    <option value="<?=$g['ID'];?>"><?=$g['gradingTitle'];?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="form-group">
                <button name="fetchGradingItems" class="delete">fetch grading Items</button>
            </div>
            </form>
        </div>
        <div class="existing">
            <div class="list">
                <table class="list-table" cellpadding="5" style="width:98%">
                    <thead>
                        <tr class="form-input" style="height:30px; text-transform:uppercase">
                            <th>S/N</th>
                            <th>Grading</th>
                            <th>Minimum Score (Lower Limit)</th>
                            <th>Maximum Score (Upper Limit)</th>
                            <th>Grade</th>
                            <th>Ordering</th>
                            <th style="width:10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $x = 1; foreach($gradingSystem as $gS):?>
                        <tr class="<?=($x%2==0?'form-input':'')?>">
                            <td><?=$x;?></td>
                            <td><?="{$gS['gradingTitle']}"?></td>
                            <td><?="{$gS['minimumScore']}"?></td>
                            <td><?="{$gS['maximumScore']}"?></td>
                            <td><?=$gS['grade'];?></td>
                            <td><?=$gS['ordering'];?></td>
                            <td>
                                <form action=""  style="width:auto; display:inline-block" method="POST" onsubmit="return false">
                                    <input type="text" name="delete" value="<?=$gS['ID'];?>" hidden>
                                    <button class="delete" onclick="confirm('Are you sure?') ? this.parentElement.submit() : null">Delete</button>
                                    <!-- <button>update</button> -->
                                </form>
                                <button 
                                    class="update" 
                                    onclick="update(
                                        `<?=$gS['ID'];?>`,
                                        `<?=$gS['gradingID'];?>`,
                                         `<?=$gS['minimumScore'];?>`,
                                         `<?=$gS['maximumScore']?>`,
                                         `<?=$gS['grade'];?>`,
                                         `<?=$gS['ordering'];?>`
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

            const [ID, gradingID, minimumScore, maximumScore, grade, ordering] = args;
            
            document.querySelector("#ID").value = ID;

            document.querySelector("#gradingID").value = gradingID;
            
            document.querySelector("#minimumScore").value = minimumScore;

            document.querySelector("#maximumScore").value = maximumScore;

            document.querySelector("#grade").value = grade;

            document.querySelector("#ordering").value = ordering;

            document.querySelector("#submitButton").setAttribute("class", "update");
            document.querySelector("#submitButton").setAttribute("name", "update");

            document.querySelector("#submitButton").textContent = "Update";

            window.location = "#top";
        }
    </script>
<?php include_once(dirname(dirname(__FILE__) ). '/Footer.php');?>
