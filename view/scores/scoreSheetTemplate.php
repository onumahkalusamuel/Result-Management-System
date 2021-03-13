<?php $pagetitle = 'Score Sheet'; ?>
<?php include_once(dirname(dirname(__FILE__) ). '/Header.php');?>
<?php


$other = (object) $other;
$header = (object) $header;
$body = (object) $body;

$breaking_point = 40;
$total_number_of_records = count(get_object_vars($body));
$total_page_count = ceil(($total_number_of_records/$breaking_point));

?>
    <title><?=strtoupper($other->examinationTitle);?> | <?=strtoupper($other->classCategoryTitle);?> | <?=strtoupper($other->subjectTitle);?> - SCORE SHEET
    </title>
    <style>
        * {font-family: "Roboto";}
        td,th {padding: 0.1em 0.4em;}
        .mainheading {
            padding-top:20px;
            display:flex;
            align: center;
            font-weight: bold;
        }
        .schooladdress {font-size:0.7em;}
        .heading2 {padding-top:0;letter-spacing:1;line-height:2;}
        .student-data {text-align:center;font-size:0.9em;}
        .table-heading {padding:auto; min-width:2.5%}
        .gradekey > span{line-height:2;}

        .thick-left-border {
            border-left: 2px solid black;
            width:0px;
            padding:0;
            margin:0
        }
        .paging { font-size:0.9em }
    </style>
    <div id="imagepreview" style="z-index:-9999; margin: auto; overflow:hidden; "></div>
        <div id="printthisarea" style="min-width: 1400px">
            <!-- Heading 1 -->
            <?php $heading_1 = "";?>
            <!-- Heading 2 -->
            <?php $heading_2 = "<div class='heading2'>
                <p align='center'>
                    <strong><u>SCORE SHEET</u></strong> <br>
                    <strong> EXAMINATION TITLE: <u>".strtoupper($other->examinationTitle)."</u> | CLASS: <u>".strtoupper($other->classCategoryTitle)."</u> | SUBJECT: <u>".strtoupper($other->subjectTitle)."</u></strong><br>
                </p>
            </div>
            <!-- Result section -->
            <div class='studentbio' align='center'>  
                <table border=1 cellspacing='0' cellpadding='0' >
                    <tr>
                        <!-- Serial Number -->
                        <th class='student-data'>S/N</th>
                        <!-- Student Details -->";
                        foreach($header->studentDetails as $h):
                            $heading_2 .= "<th class='student-data'>".ucwords($h)."</th>";
                        endforeach;
                        $heading_2 .= "<!-- Assignments -->";
                        foreach($header->papers as $h):
                            $heading_2 .= "<th class='student-data table-heading'>".ucwords($h)."</th>";
                        endforeach;
                        
                        $heading_2 .= "<!-- Cumulative Assignment -->
                        <!-- Total -->
                        <th class='student-data table-heading'>Total (" . $header->total . ")</th>
                        <!-- Percentage -->
                        <th class='student-data table-heading'>Percentage (100%)</th>
                        <!-- Grade -->
                        <th class='student-data table-heading'>Grade</th>
                        <!-- Position -->
                        <th class='student-data table-heading'>Position</th>
                        <!-- Remark -->
                        <th class='student-data'>Remark</th>
                    </tr>";
                    ?>

                    <!-- body -->
                    <?php $x = 1; foreach($body as $b) :

                    if($x === 1 || (($x-1)%$breaking_point)===0) {
                        echo "<div class='pages' id='page-".(($x-1)/$breaking_point)."'>";
                        echo $heading_1;
                        echo $heading_2;
                    };
                    ?>
                    <tr>
                        <td class="student-data"><?=$x;?></td>
                        <!-- Student Data -->
                        <td class="student-data" style="text-align:left"><?=$b['studentDetails']['studentName'];?></td>
                        <td class="student-data"><?=$b['studentDetails']['admissionNumber'];?></td>
                        <td class="student-data"><?=$b['studentDetails']['examinationNumber'];?></td>
                        <!-- Assignments -->
                        <?php foreach($header->papers as $h) :?>
                        <td class="student-data"></td>
                        <?php endforeach;?>
                        <td class="student-data"></td>
                        <td class="student-data"></td>
                        <!-- Grade -->
                        <td class="student-data"></td>
                        <!-- Position -->
                        <td class="student-data"></td>
                        <!-- Remark -->
                        <td class="student-data table-heading">&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    </tr>
                    <?php
                    if(($x%$breaking_point)===0 && $x !== $total_number_of_records) {
                        echo "</table>";
                        echo "<br>";
                        echo "<div class='text-center paging'><strong>Page ".($x/$breaking_point)." of ".$total_page_count." </strong></div>";
                        echo "</div>";
                        echo "</div>";
                        echo "<br><br><br>";
                    };
                    if( $x === $total_number_of_records ) {
                        echo "</table>";
                        echo "<br>";
                        echo "<div class='text-center paging'><strong>Page ".$total_page_count." of ".$total_page_count."</strong></div>";
                        echo "</div>";
                    };
                    ?>
                    <?php $x++; endforeach;?>
                
                <br><br>
                <div style="font-size:0.9em; text-align:center">
                    <strong>
                        Subject Teacher's Signature: __________________________
                    </strong>
                    <br><br>
                    <strong>Date Printed: <u><?=date("l, d F, Y", time());?></u></strong>
                </div>
                <!-- for last page -->
                 <?php echo "</div>"; ?>
        </div>
        <script type="text/javascript" src="<?=$routeBase?>assets/js/dom-to-image.min.js"> </script>
<script type="text/javascript" src="<?=$routeBase?>assets/js/jspdf.min.js"> </script>
<script type="text/javascript">

    var goBack = document.createElement("button");
    goBack.textContent = "Close";
    goBack.setAttribute("class", "delete");
    goBack.style = "width: 100px; float:right; padding:0; margin:0 20px 0 0; height: 30px;";
    
    document.querySelector("#secondHeader").prepend(goBack);
    goBack.addEventListener("click", function(){
        window.close();
    })

    var button = document.createElement("button");
    button.textContent = "Download as PDF";
    button.style = "width: 200px; float:right; padding:0; margin:0; height: 30px";
    
    document.querySelector("#secondHeader").prepend(button);

    button.addEventListener("click", function(){

        document.getElementById("loadingContainer").style.display = "block";

        var node = document.getElementsByClassName("page")[0];
        var doctitle = node.querySelector("title").innerHTML;
        doctitle = doctitle.split(" ").join("_").split("|").join("_").split("-").join("_").split(",").join("").split("/").join("_");
        // return
        var placeholder = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAARCAYAAAAVKGZoAAAAFklEQVQYlWNgGDEggoGB4T8S3kE3mwHMPgQO5MLnnwAAAABJRU5ErkJggg==";
        var all_pages = node.querySelectorAll(".pages");
        var pdf  = new jsPDF("p","pt",[595.25 , 841.28], true);
        
        var all_pages_count = all_pages.length;
        var current_page = 0
        internalLoop();

        async function internalLoop() {
            
            await domtoimage.toPng(document.querySelector("#page-"+current_page), {imagePlaceholder: placeholder})
            .then(async function (dataUrl) {
                
                // prepare image
                var img = new Image();
                img.src = dataUrl;
                var imgHeight = img.offsetHeight;
                var imgWidth = img.offsetWidth;
                
                img.width = 575;
                img.height = Math.floor(575/imgWidth*imgHeight)

                if( current_page !== 0 ) pdf.addPage();

                pdf.addImage( img, "PNG", 10, 10, 575, 0, "page"+current_page, "FAST" );

                if((current_page+1) === all_pages_count) {pdf.save(`${doctitle}.pdf`); document.getElementById("loadingContainer").style.display = "none";}
                else{current_page++; await internalLoop();}
            })
            .catch(function (error) {
                console.error("oops, something went wrong!", error);
                document.getElementById("loadingContainer").style.display = "none";
            });
        }
    })
</script>
<?php include_once(dirname(dirname(__FILE__) ). "/Footer.php"); ?>