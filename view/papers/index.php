<?php $pagetitle = 'Papers'; ?>
<?php include_once(dirname(dirname(__FILE__) ). '/Header.php');?>
    <div class="form-container">
        <div class="section-title" style="">Add Papers</div>
        <form id="enqueueingForm" method="POST" onsubmit="return false;">
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
                <label for="subjectID">Subject: *</label>
                <select style="width:100%" class="form-input" name="subjectID" id="subjectID" required>
                <option value="0">Please Select</option>
                <?php foreach($subjects as $subject):?>
                <option value="<?=$subject['ID'];?>"><?=$subject['subjectTitle'];?></option>
                <?php endforeach;?>
                </select>
            </div>
            <div class="form-group">
                <button class="delete">Enqueue</button>
            </div>
        </form>
        <br>
        <div class="form-group">
                <a href="<?=$routeBase;?>papers/allpapers"><button class="">SHOW ALL PAPERS</button></a>
            </div>
    </div>
    <div class="list-container">
        <div class="section-title">
            enqueued papers
        </div>
        <form action="" id="actualForm" method="POST" onsubmit="return false;">
            <!-- <div class="form-group" style="">
                <button class="delete queueSubmitter">Submit</button>
            </div> -->
            <?php foreach($classCategory as $cC) :?>
                <div id="classCategory<?=$cC['ID'];?>" class="" style="display:none">
                <div class="section-title"><?=$cC['classCategoryTitle'];?></div>
                <?php foreach($subjects as $subject) :?>
                <fieldset class="selfContained" style="display:none" id="fieldSet<?=$cC['ID'];?><?=$subject['ID'];?>">
                    <legend><?=$subject['subjectTitle'];?></legend>
                    <div class="form-group" style="border-bottom:none; width:100px; font-size:0.7em; float:right">
                        <button
                            style="font-weight:bold"
                            class="paperAdder update"
                            onclick="addPaper(<?=$cC['ID'];?>, <?=$subject['ID'];?>)"
                        >ADD PAPER</button>
                    </div>
                </fieldset>
                <?php endforeach;?>
                </div>
            <?php endforeach;?>
            <div class="form-group" style="display:none" id="queueSubmitterContainer">
            <input hidden name="create">
                <button class="delete queueSubmitter" >Submit</button>
            </div>
        </form>

    </div>
    <script type="text/javascript">
        document.querySelector('#enqueueingForm').addEventListener("submit", addToQueue);
        document.querySelector('.queueSubmitter').addEventListener("click", function(){
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
            document.querySelector("#classCategory" + classCategoryID).style.display = "block";
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

            //show button
            document.querySelector("#queueSubmitterContainer").style.display = 'block';

            document.querySelector("#fieldSet" + classCategoryID + subjectID).append(span);
        }
        
    </script>

<?php include_once(dirname(dirname(__FILE__) ). '/Footer.php'); ?>
