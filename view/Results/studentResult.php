<?php $pagetitle = 'Student Result'; ?>
<?php include_once(dirname(dirname(__FILE__) ). '/Header.php');?>
<?php include_once(dirname(dirname(dirname(__FILE__) )). '/core/Config.php');?>

<?php
if($code !== 200 ) die("Error occured");
$code = (object) $code; //done
$other = (object) $other; //done
$body = (object) $body; //done

?>
<title><?=$body->studentName;?> | <?=$other->classCategory;?> - Student Result </title>
<style>
    td,th {padding: 0.2em 0.5em;}
    .heading2 {padding-top:0;line-height:2; text-transform: uppercase}
    .student-data {text-align:center;font-size:0.8em;}
    .main-result>* {font-size:0.8em;}
    .gradekey>span{line-height:2;}
</style>
<div id="imagepreview" style="z-index:-9999; margin: auto; overflow:hidden; "></div>
<div id="printthisarea" style="text-align:center; margin:auto">
    <div class="pages" id="page-0" style="display:inline-block;">
    <?php if(!empty(@file_get_contents(\Core\Config::BASEURL . 'assets/images/result_heading.jpg'))) :?>
        <div style="height: 95px;">
            <img src="<?php echo \Core\Config::BASEURL . 'assets/images/result_heading.jpg';?>" style="height: 95px" />
        </div>
    <?php else:?>
        <div style="background-color:#f8f8f8; height: 95px;"></div>
    <?php endif;?>
        <!-- Heading 2 -->
        <div class="heading2">
            <p align="center">
                <strong><u><?=$other->examinationTitle;?> REPORT SHEET</u></strong>
            </p>
        </div>
        <!-- Result section -->
        <div align="center">
            <table >
                <tr>
                    <td>
                        <div >
                            <!-- Student details -->
                            <div class="studentbio" align="center">  
                                <table border=1 cellspacing="0" cellpadding="0" width="100%">
                                    <tr>
                                        <th class="student-data">Student Name</th>
                                        <th class="student-data">Admission Number</th>
                                        <th class="student-data">Examination Number</th>
                                        <th class="student-data">Class</th>
                                    </tr>
                                    <tr>
                                        <td class="student-data"> <?=$body->studentName;?> </td>
                                        <td class="student-data"> <?=$body->admissionNumber;?> </td>
                                        <td class="student-data"> <?=$body->examinationNumber;?> </td>
                                        <td class="student-data"> <?=$other->classCategory;?> </td>
                                    </tr>
                                </table>
                            </div>
                            <p></p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <div class="main-result">
                            <table class="innertable table1" width="100%" cellspacing="0" cellpadding="0" border="1">
                                <tbody>
                                    <!-- Academic Row Start -->
                                    <tr>
                                        <th width="5%" valign="center"> <strong>S/N</strong> </th>
                                        <th valign="center"><strong>Subjects</strong></th>
                                        <?php if(!empty($body->papers)): ?>
                                            <?php foreach($body->papers as $bPaper): ?>
                                                <th width="7%" valign="center"><?php echo $bPaper;?></th>
                                            <?php endforeach;?>
                                        <?php endif;?>
                                        <th width="7%" valign="center">Total</th>
                                        <th width="7%" valign="center">Percentage</th>
                                        <th width="6%" valign="center">Grade</th>
                                        <th width="7%" valign="center">Teacher’s Signature</th>
                                    </tr>
                                    <!-- Subject Start -->
                                    <?php $x=0; foreach($body->subjects as $key => $s) : ?>
                                    <tr>
                                        <td width="5%" valign="center" align="center">
                                            <?=(++$x);?>
                                        </td>
                                        <td width="14%" valign="center">
                                            <?=(!empty($s->subjectTitle) ? $s->subjectTitle : '-');?>
                                        </td>
                                        <?php if(!empty($body->papers)): ?>
                                            <?php foreach($body->papers as $bKey => $bValue): ?>
                                                <td width="7%" valign="center" align="center">
                                                    <?=(!empty($s->papers->$bKey) ? $s->papers->$bKey : '-');?>
                                                </td>
                                            <?php endforeach;?>
                                        <?php endif;?>
                                        <td width="7%" valign="center" align="center">
                                            <?=(!empty($s->total) ? $s->total : '-');?>
                                        </td>
                                        <td width="7%" valign="center" align="center">
                                            <?=(!empty($s->percentage) ? $s->percentage : '-');?>
                                        </td>
                                        <td width="6%" valign="center" align="center">
                                            <?=(!empty($s->grade) ? $s->grade : '-');?>
                                        </td>
                                        <td width="6%" valign="center" align="center" for="teacher's signature"></td>
                                    </tr>
                                    <?php endforeach;?>
                                    <!-- Subject End -->
                                    <?php  $colspan = 2; if(!empty($body->papers)) foreach($body->papers as $bP) $colspan++; ?>
                                    <!-- Grand Total Start -->
                                    <tr>
                                        <td rowspan="2"></td>
                                        <td rowspan="2" colspan="<?=$colspan;?>" width="19%" valign="center">
                                            <strong>GRAND TOTAL</strong>
                                        </td>
                                        <td colspan="2" width="19%" valign="center">
                                            <strong>
                                            Obtainable: 100%
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" width="19%" valign="center">
                                            <strong>
                                                Obtained: <?=(!empty($body->percentage) ? $body->percentage : '-');?>%
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <!-- Grand Total End -->
                                    <tr>
                                        <td colspan="10">
                                            <strong>You took <u><?=$body->position->gotten;?></u> position out of <u><?=$body->position->outof; ?> students</u>!</strong>
                                        </td>
                                    <tr>
                                    <tr>
                                        <td colspan="10">
                                            <br>
                                            Principal’s Comment:
                                            <br><br><br>
                                            Signature:
                                            <hr>
                                            <br>
                                            Class Teacher’s Comment:
                                            <br><br><br>
                                            Signature:
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="10">
                                            <strong>
                                            <div class='student-data'>
                                                <u>GRADE KEY</u><br>
                                                <?php if(!empty($other->gradingSystem)) {
                                                    $container = []; $x = 1;
                                                    foreach($other->gradingSystem as $key => $grade) : 
                                                            $grade = (object) $grade;
                                                            $container[] = "<span>** {$grade->minimumScore}-{$grade->maximumScore} = {$grade->grade}</span>" . ($x%5===0 ? "<br>" : "");
                                                            
                                                            $x++;
                                                    endforeach; 
                                                    echo implode(" &nbsp;&nbsp;", $container);
                                                } else {
                                                    echo "No Grading Available for the selected class at the moment";
                                                }?>
                                            </div>
                                            </strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                <!-- For credits -->
                <tr>
                    <td >
                    <div style="font-weight:bold;width:100%;background-color:rgb(230,230,230);font-size:0.7em; text-align:center;">dESIGNED bY: onumahkalusamuel.tk</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript" src="./assets/js/dom-to-image.min.js"> </script>
<script type="text/javascript" src="./assets/js/jspdf.min.js"> </script>
<script type="text/javascript">
var goBack = document.createElement("button");
    goBack.textContent = "Close";
    goBack.setAttribute("class", "delete");
    goBack.style = "width: 100px; float:right; padding:0; margin:0 20px 0 0; height: 30px;";
    
    document.querySelector("#secondHeader").prepend(goBack);
    goBack.addEventListener("click", function(){
        window.close();
    });

    var button = document.createElement('button');
    button.textContent = "Download as PDF";
    button.style = "width: 200px; float:right; padding:0; margin:0; height: 30px";
    
    document.querySelector("#secondHeader").prepend(button);

    button.addEventListener("click", function(){

        document.getElementById("loadingContainer").style.display = "block";

        var node = document.getElementsByClassName('page')[0];
        var doctitle = node.querySelector("title").innerHTML;
        doctitle = doctitle.split(" ").join("_").split("|").join("_").split("-").join("_").split(",").join("").split("/").join("_");
        var placeholder = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAARCAYAAAAVKGZoAAAAFklEQVQYlWNgGDEggoGB4T8S3kE3mwHMPgQO5MLnnwAAAABJRU5ErkJggg==";
        var all_pages = node.querySelectorAll(".pages");
        var pdf  = new jsPDF('p','pt',[595.25, 841.28], true);
        
        var all_pages_count = all_pages.length;
        var current_page = 0
        internalLoop();

        async function internalLoop() {
            console.log(document.querySelector("#page-"+current_page));
            await domtoimage.toPng(document.querySelector("#page-"+current_page), {imagePlaceholder: placeholder})
            .then(async function (dataUrl) {
                
                // prepare image
                var img = new Image();
                img.src = dataUrl;

                document.querySelector("#imagepreview").appendChild(img);
                await Promise.all([new Promise(resolve => setTimeout(resolve, 200))]);
                console.log(img.offsetWidth, img.offsetHeight);
                var calculatedWidth = Math.floor(820/img.offsetHeight*img.offsetWidth);
                if(calculatedWidth > 575) {
                    img.width = 575;
                    img.height = Math.floor(575/img.offsetWidth*img.offsetHeight);
                } else {
                    img.width = calculatedWidth;
                    img.height = 820;
                }
                
                if( current_page !== 0 ) pdf.addPage();

                pdf.addImage( img, 'PNG', 10, 10, img.width, img.height, "page"+current_page, 'FAST' );

                document.querySelector("#imagepreview").innerHTML = "";
                
                if((current_page+1) === all_pages_count) {pdf.save(`${doctitle}.pdf`); document.getElementById("loadingContainer").style.display = "none";}
                else{current_page++; await internalLoop();}
            })
            .catch(function (error) {
                console.error('oops, something went wrong!', error);
                document.getElementById("loadingContainer").style.display = "none";
            });
        }
    })

    </script>
    <?php include_once(dirname(dirname(__FILE__) ). '/Footer.php'); ?>