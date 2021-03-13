<?php $pagetitle = 'Result Analysis'; ?>
<?php include_once(dirname(dirname(__FILE__) ). '/Header.php');?>
<?php
if($code!==200) die("Error occured");
$code = (object) $code;
$other = (object) $other;
$header = (object) $header;
$body = (object) $body;
$footer = (object) $footer;

$subject_bucket = [];
$total_subjects = 0;
// make sure we dont have empty data
if(empty($header->subject)) return "<h3 align='center'>Result Analysis not available for selected class at the moment</h3>";

//subject headings
foreach($header->subject as $key => $value) {
    $subject_bucket[$key] = "-";
    $total_subjects++;
}

$total_number_of_records = count(get_object_vars($body));

switch(true){
    case ($total_subjects <= 8 ): {$breaking_point = 14; break;}
    case ($total_subjects <= 11 ): {$breaking_point = 10; break;}
    case ($total_subjects <= 14 ): {$breaking_point = 7; break;}
    default: {$breaking_point = 5; break;}
}
$total_page_count = ceil(($total_number_of_records/$breaking_point));

?>
    <title><?=$other->classCategory;?> | <?=$other->examinationTitle;?> </title>
    <style>
        * {font-family: "Roboto";}
        #printthisarea {font-size:0.8em}
        td,th {padding: 0.3em 0.5em;}
        .heading2 {padding-top:0;letter-spacing: 1;line-height:2;}
        .student-data {text-align:center;font-size:0.9em;}
        .table-heading {writing-mode:vertical-rl; padding:auto; min-width:2.5%}
        .gradekey>span{line-height:2;}
        .thick-left-border {
            border-left: 4px solid black;
        }
        .paging { font-size:0.9em }
    </style>
<div id="imagepreview" style="z-index:-9999; margin: auto; overflow:hidden; "></div>
<div id="printthisarea" style="min-width: 1400px">
    <?php
    // footer 1
    $footer_1 = 
    "<tr>
        <td></td>
        <th class='student-data' rowspan=3> Subject Analysis</th>
        <th></th>";
        foreach($footer->subject as $f_s):
            $footer_1 .= "<th class='student-data'>";
            foreach($f_s as $key => $f_s_k):
                $footer_1 .= "{$key}&nbsp;-&nbsp;{$f_s_k}<br>";
            endforeach;
        $footer_1 .= "</th>";
        endforeach;
        $footer_1 .= "<th></th>
        <th></th>
    </tr>";

    // footer 2
    $footer_2 = "<strong>
    <div class='student-data'><br>
        <u>GRADE KEY</u><br>";
        if(!empty($other->gradingSystem)) {
            $container = [];
            foreach($other->gradingSystem as $key => $grade) : 
                    $grade = (object) $grade;
                    $container[] = "<span>{$grade->minimumScore}-{$grade->maximumScore} = {$grade->grade}</span>";
            endforeach; 
            $footer_2 .= implode(";&nbsp;&nbsp;&nbsp;&nbsp;", $container);
        } else {
            $footer_2 .= "No Grading Available for the selected class at the moment";
        }
    $footer_2 .= "</div>
        <br>
        <div style='font-size: 0.9em'>
            Date Printed: <u>".date('l, d F, Y', time())."</u>
        </div>
    </strong>";

    // Heading 1 and 2
    $heading_1 = ""; ?>
    <?php $heading_2 = "<div class='heading2'>
        <p align='center' style='padding-top:0'>
            <strong><u>RESULT ANALYSIS</u></strong> <br>
            <strong> EXAMINATION TITLE: <u>".strtoupper($other->examinationTitle)."</u> | CLASS: <u>".strtoupper($other->classCategory)."</u></strong><br>
        </p>
    </div>
    <!-- Result section -->
    <div class='studentbio' align='center'>  
        <table border=1 cellspacing='0' cellpadding='0' >
            <tr>
                <!-- Serial Number -->
                <th class='student-data table-heading'>S/N</th>
                <!-- Student Details -->";
                foreach($header->studentDetails as $h):
                    $heading_2 .= "<th class='student-data'>".ucwords($h)."</th>";
                endforeach;
                $heading_2 .= "<!-- Subjects -->";
                foreach($header->subject as $h):
                    $heading_2 .= "<th class='student-data table-heading'>".ucwords($h)."</th>";
                endforeach;
                $heading_2 .= "<th class='student-data'>Percentage</th>
                <th class='student-data'>".ucwords($header->summaryAnalysis)."</th>
            </tr>";
            ?>
            <!-- body -->
            <?php $x = 1; foreach($body as $b) :
                $b = (object) $b;
            if($x === 1 || (($x-1)%$breaking_point)===0) {
                echo "<div class='pages' id='page-".(($x-1)/$breaking_point)."'>";
                echo $heading_1;
                echo $heading_2;
            };
            ?>

            <tr>
                <td class='student-data'><?=$x;?></td>
                <!-- Student Data -->
                <td class='student-data' style="text-align:left"><?=$b->studentName;?></td>
                <!-- <td class='student-data'><?=$b->admissionNumber;?></td> -->
                <td class='student-data'><?=$b->examinationNumber;?></td>
                <!-- values -->
                <?php $subject_bucket_copy = $subject_bucket;
                    if(!empty($b->subject)) foreach($b->subject as $key => $h) $subject_bucket_copy[$key] = $h;
                ?>
                <?php foreach($subject_bucket_copy as $h) :?>
                <td class='student-data'><?=ucwords(empty($h) ? '-': $h);?></td>
                <?php endforeach;?>
                <th class='student-data'><?=ucwords($b->percentage ? $b->percentage : '-');?></th>
                <th class='student-data'>
                <?php
                if(!empty($b->summaryAnalysis)) {
                    $container = [];
                    foreach($b->summaryAnalysis as $key => $s_a):
                        $container[] = $key . '&nbsp;-&nbsp;' . $s_a;
                    endforeach; 
                    echo implode('; ', $container);
                }
                ?>
                
                </th>
            </tr>

            <?php
            if(($x%$breaking_point)===0 && $x !== $total_number_of_records) {
                echo $footer_1;
                echo "</table>";
                echo $footer_2;
                echo "<br>";
                echo "<div class='text-center paging'><strong>Page ".($x/$breaking_point)." of ".$total_page_count." </strong></div>";
                echo "</div>";
                echo "</div>";
                echo "<br><br><br>";
            };
            if( $x === $total_number_of_records ) {
                echo $footer_1;
                echo "</table>";
                echo $footer_2;
                echo "<br>";
                echo "<div class='text-center paging'><strong>Page ".$total_page_count." of ".$total_page_count."</strong></div>";
                echo "</div>";
            }
            ?>
            <?php $x++; endforeach;?>
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
    })
    
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
        var pdf  = new jsPDF('l','pt',[841.28, 595.25], true);
        
        var all_pages_count = all_pages.length;
        var current_page = 0
        internalLoop();

        async function internalLoop() {
            
            await domtoimage.toPng(document.querySelector("#page-"+current_page), {imagePlaceholder: placeholder})
            .then(async function (dataUrl) {
                
                // prepare image
                var img = new Image();
                img.src = dataUrl;

                document.querySelector("#imagepreview").appendChild(img);
                await Promise.all([new Promise(resolve => setTimeout(resolve, 200))]);
                console.log(img.offsetWidth, img.offsetHeight);
                var calculatedHeight = Math.floor(820/img.offsetWidth*img.offsetHeight);
                if(calculatedHeight > 575) {
                    img.height = 575;
                    img.width = Math.floor(575/img.offsetHeight*img.offsetWidth);
                } else {
                    img.height = calculatedHeight;
                    img.width = 820;
                }
                
                if( current_page !== 0 ) pdf.addPage();

                pdf.addImage( img, 'PNG', 10, 10, 820, img.height, "page"+current_page, 'FAST' );

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