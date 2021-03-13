<?php $pagetitle = 'All Papers';?>
<?php include_once(dirname(dirname(__FILE__) ). '/Header.php');?>
    <div class="list-container">
        <div class="section-title" style="padding-right:5%">
            Existing Papers 
        </div>
        <div class="" style="margin-bottom:15px">
            <form method="POST">
            <div class="form-group">
                <label for="classCategoryID">Class Category: *</label>
                <select style="width:100%" class="form-input" name="classCategoryID" required>
                    <option value="-1">Please Select</option>
                    <option value="0">Show All Papers</option>
                    <?php foreach($classCategory as $cC): ?>
                    <option value="<?=$cC['ID'];?>"><?=$cC['classCategoryTitle'];?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="form-group">
                <button name="fetchPapers" class="delete">fetch papers</button>
            </div>
            </form>
        </div>
        <div class="existing">
            <div class="list" style="display:<?=(isset($_GET['page']) ? 'block': 'block');?>;">
                <table class="list-table" cellpadding="5" style="width:98%">
                    <thead>
                        <tr class="form-input" style="height:30px; text-transform:uppercase">
                            <th>S/N</th>
                            <th>Class Category</th>
                            <th>Subject</th>
                            <th>Paper Title</th>
                            <th>Maximum Score</th>
                            <th style="width:10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $x = 1; foreach($papers as $paper):?>
                        <tr class="<?=($x%2==0?'form-input':'')?>">
                            <td><?=$x;?></td>
                            <td><?=$paper['classCategoryTitle']?></td>
                            <td><?=$paper['subjectTitle']?></td>
                            <td><?=$paper['paperTitle']?></td>
                            <td><?=$paper['maximumScore']?></td>
                            <td>
                                <form action=""  style="width:auto; display:inline-block" method="POST" onsubmit="return false">
                                    <input type="text" name="delete" value="<?=$paper['ID'];?>" hidden>
                                    <button class="delete" onclick="confirm('Are you sure?') ? this.parentElement.submit() : null">Delete</button>
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
<?php include_once(dirname(dirname(__FILE__) ). '/Footer.php'); ?>
