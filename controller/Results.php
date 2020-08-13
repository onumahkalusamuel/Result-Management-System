<?php

namespace Controller;

class Results extends \Core\Controller {
    
    private $app = [];

    public function index() {
        $message = $results = [];

        if(isset($_POST['processResult'])) return $this->processResult();

        if(isset($_POST['preview'])) return $this->preview();

        if(isset($_POST['delete'])) $message = $this->delete();

        if(isset($_POST['fetchResults'])) {
            $results = $this->results([
                'classCategoryID' => $_POST['classCategoryID'],
                'examinationID' => $_POST['examinationID'],
                'resultType' => $_POST['resultType']
            ]);

            if(empty($results)) $message[] = 'No records found with search criteria';
        }

        $classCategory = $this->classCategory();
        $examination = $this->examination();

        \Core\View::render('results/index.php', compact('message', 'results', 'classCategory', 'examination'));
    }

    private function preview($params = array()) { 

        $message = ['Error occured'];
        
        $fetch = $this->results($_POST)[0];

        $results = json_decode($fetch['content']);
        $keys = [];
        
        foreach($results as $key => $value) {
            $$key = $value;
            $keys[] = $key;
        }

        \Core\View::render("results/{$fetch['resultType']}.php", compact($keys));
    }

    private function delete() { 

        $message = ["No input detected"];

        if(!empty($_POST['delete'])) {

            $papers = new \Model\Results();
            $papers->ID = $_POST['delete'];

            $delete = $papers->delete();

            if($delete === 1){$message = ['Result Record deleted successfully'];}
            if($delete === 0){$message = ['The specified Record was not found'];}

            return $message;

        }

    }

    private function processResult() {

        if(empty($_POST['examinationID']) || empty($_POST['classCategoryID']) || empty($_POST['resultType'])) $message = ["No requested data detected"];

        switch ($_POST['resultType']) {
            case 'resultAnalysis': { $this->processResultAnalysis(); break;}
            case 'scoreSheet': { $this->processScoreSheet(); break;}
            case 'studentResult': { $this->processStudentResult(); break;}
            default: { \Core\View::render('results/index.php', ['message'=>['An error occured.']]); }
        }

    }

    private function processResultAnalysis() {

        if(empty($_POST['examinationID']) || empty($_POST['classCategoryID'])) return false;

        $examinationID = $_POST['examinationID'];
        $classCategoryID = $_POST['classCategoryID'];
        $previewOnly = isset($_POST['preview']) ? true : false;

        //initialize return and declare needed keys
        $return = [];
        $return['code'] = 200;

        //bootstrap necessary data
        $this->bootstrap($classCategoryID, $examinationID);

        $return['other']['examinationTitle'] = $this->app['examination']['examinationTitle'];
        $return['other']['examinationID'] = $examinationID;
        $return['other']['classCategory'] = $this->app['classCategory']['classCategoryTitle'];
        $return['other']['classCategoryID'] = $classCategoryID;
        $gradingID = $this->app['classCategory']['gradingID'];

        //continue
        if(
           empty($this->app['headerTemplate'])
        || empty($this->app['recordTemplate'])
        || empty($this->app['footerTemplate'])
        ) {
        
            //prepare the student record template
            $this->app['recordTemplate']['studentName'] = "";
            $this->app['recordTemplate']['admissionNumber'] = "";
            $this->app['recordTemplate']['examinationNumber'] = "";
            $this->app['recordTemplate']['subject'] = [];
            $this->app['recordTemplate']['temp'] = [];
            $this->app['recordTemplate']['percentage'] = "";

            //prepare the header
            $this->app['headerTemplate']['studentDetails'] = ['Student&nbsp;Name', 'Examination Number'];

            //process the subjects into header and footer and student template
            foreach($this->app['subjects'] as $sub) {

                $this->app['headerTemplate']['subject'][$sub['ID']] = $sub['subjectTitle'];

                $this->app['footerTemplate']['subject'][$sub['ID']] = [];

                $this->app['recordTemplate']['subject'][$sub['ID']] = "";
                $this->app['recordTemplate']['temp'][$sub['ID']] = 0;

            }

            //add summary analysis to the header and record template

            $this->app['headerTemplate']['summaryAnalysis'] = "Individual&nbsp;Analysis";

            $this->app['recordTemplate']['summaryAnalysis'] = [];

        }


        //get the grading system
        $gradingSystem = $this->gradingSystem(['gradingID' => $gradingID]);
        $return['other']['gradingSystem'] = $gradingSystem;

        //declare return header, body and footer
        $return['header'] = $this->app['headerTemplate'];
        $return['body'] = [];
        $return['footer'] = $this->app['footerTemplate'];

        //add students to the body

        foreach($this->app['students'] as $stu) {
            $return['body'][$stu['ID']] = $this->app['recordTemplate'];
            $return['body'][$stu['ID']]['studentName'] = "{$stu['lastName']} {$stu['firstName']} {$stu['middleName']}";
            $return['body'][$stu['ID']]['admissionNumber'] = $stu['admissionNumber'];
            $return['body'][$stu['ID']]['examinationNumber'] = $stu['examinationNumber'];
        }

        // fetch the records
        $scores = $this->scores(['classCategoryID' => $classCategoryID, 'examinationID'=> $examinationID]);

        //verify that the results are not empty
        if(count($scores)===0) return false;

        //then loopthrough and format
        foreach($scores as $row) {

            if(!key_exists($row['studentID'], $return['body'])) continue;

            //capture in the temp array
            $return['body'][$row['studentID']]['temp'][$row['subjectID']] += (int) $row['score'];

        }

        //further processing
        foreach($return['body'] as $studentKey => $body) {

            foreach($body['temp'] as $subjectKey => $subjectValue) {

                // make sure student has at least one score by totals
                if(empty($subjectValue))
                {
                    unset($return['body'][$studentKey]['subject'][$subjectKey]);
                    continue;
                }
                $score = round(
                    $return['body'][$studentKey]['temp'][$subjectKey] 
                    / $this->app['scoring'][$classCategoryID][$subjectKey]['total']
                    * 100, 
                    2
                );
                //get grade
                $grade =  \Core\Functions::getGrade(
                    $score, 
                    $gradingSystem
                );

                $return['body'][$studentKey]['subject'][$subjectKey] = "{$score}&nbsp;($grade)";
                
                //pill up for percentage calculation
                $return['body'][$studentKey]['temp']['percentage'][] = $score;

                // populate the summary analysis
                $return['body'][$studentKey]['summaryAnalysis'][$grade] = isset($return['body'][$studentKey]['summaryAnalysis'][$grade]) ? $return['body'][$studentKey]['summaryAnalysis'][$grade] + 1 : 1; 
                $return['footer']['subject'][$subjectKey][$grade] = isset($return['footer']['subject'][$subjectKey][$grade]) ? $return['footer']['subject'][$subjectKey][$grade] + 1 : 1; 

            }

            //be sure the student has at least one record
            if(empty($return['body'][$studentKey]['subject'])) {
                unset($return['body'][$studentKey]);
                continue;
            }

            //calculate the average percentage
            $return['body'][$studentKey]['percentage'] = \Core\Functions::calculateAverage($return['body'][$studentKey]['temp']['percentage']);
            
            //sort and update summary analysis
            $return['body'][$studentKey]['summaryAnalysis'] = \Core\Functions::sortArrayWithStringKeys($return['body'][$studentKey]['summaryAnalysis']);
            
            //remove the temp array now 
            unset($return['body'][$studentKey]['temp']);

        }

        //remove the empty footer and header subject
        foreach($return['footer']['subject'] as $key => $footerSubject) {
            if(empty($footerSubject)) {
                unset($return['footer']['subject'][$key]);
                unset($return['header']['subject'][$key]);
            } else {
                $return['footer']['subject'][$key] = \Core\Functions::sortArrayWithStringKeys($return['footer']['subject'][$key]);
            }
        }

        //make sure the return values are not empty
        if(empty($return['body']) || empty($return['header']) || empty($return['footer'])) return false;
        
        if($previewOnly === false) {
            // save to db
            $return['message'] = $this->saveResult(['resultType'=>'resultAnalysis', 'classCategoryID'=>$classCategoryID, 'examinationID'=>$examinationID, 'content'=>$return]);
        }

        \Core\View::render('results/resultAnalysis.php', $return);

    }

    private function processScoreSheet() {
        if(empty($_POST['examinationID']) || empty($_POST['classCategoryID'])) return false;

        $examinationID = $_POST['examinationID'];
        $classCategoryID = $_POST['classCategoryID'];

        //initialize return and declare needed keys
        $return['code'] = 200;

        //run bootstrap
        $this->bootstrap($classCategoryID, $examinationID);
        
        //set some return variables
        $return['other']['examinationTitle'] = $this->app['examination']['examinationTitle'];
        $return['other']['examinationID'] = $examinationID;
        $return['other']['classCategory'] = $this->app['classCategory']['classCategoryTitle'];
        $return['other']['classCategoryID'] = $classCategoryID;
        $gradingID = $this->app['classCategory']['gradingID'];

        // fetch all the scores 
        $scores = $this->scores(['examinationID'=>$examinationID, 'classCategoryID'=>$classCategoryID]);
        $scoresHolder = [];
        foreach($scores as $score) {
            $scoresHolder[$score['studentID']][$score['subjectID']][$score['paperID']] = $score['score'];
        }

        //prepare the header
        $return['header']['studentDetails'] = ['Student&nbsp;Name', 'Admission Number', 'Examination Number'];
        $return['header']['papers'] = [];
        $return['header']['total'] = "Total";
        $return['header']['percentage'] = "Percentage (100%)";
        $return['header']['grade'] = "Grade";
        $return['header']['position'] = "Position";
        $return['header']['remark'] = "Remark";

        //get the grading system
        $gradingSystem = $this->gradingSystem(['gradingID' => $gradingID]);
        $return['other']['gradingSystem'] = $gradingSystem;

        //process the subjects
        foreach($this->app['subjects'] as $sub) {

            $subjectKey = $sub['ID'];
            $return['other']['subjectTitle'] = $sub['subjectTitle'];
            
            if(empty($this->app['scoring'][$classCategoryID][$subjectKey])) continue;
            
            $return['body'] = [];
            $return['header']['papers'] = $this->app['scoring'][$classCategoryID][$subjectKey]['papers'];
            $return['header']['total'] = "Total ({$this->app['scoring'][$classCategoryID][$subjectKey]['total']})";

            //subject not offered by class
            
            foreach($this->app['students'] as $stu) {
                
                $studentKey = $stu['ID'];

                $return['body'][$studentKey] = [];
                $return['body'][$studentKey]['studentName'] = "{$stu['lastName']} {$stu['firstName']} {$stu['middleName']}";
                $return['body'][$studentKey]['admissionNumber'] = $stu['admissionNumber'];
                $return['body'][$studentKey]['examinationNumber'] = $stu['examinationNumber'];
                $return['body'][$studentKey]['papers'] = [];
                $return['body'][$studentKey]['total'] = 0;
                $return['body'][$studentKey]['percentage'] = 0;
                $return['body'][$studentKey]['grade'] = "";
                $return['body'][$studentKey]['position'] = "";
                $return['body'][$studentKey]['remark'] = "";
                // the heading scores

                foreach($this->app['scoring'][$classCategoryID][$subjectKey]['papers'] as $paperKey => $paperValue) {
                    $return['body'][$studentKey]['papers'][$paperKey] = 
                        !empty($scoresHolder[$studentKey][$subjectKey][$paperKey]) 
                        ? $scoresHolder[$studentKey][$subjectKey][$paperKey] 
                        : 0;
                        $return['body'][$studentKey]['total'] += $return['body'][$studentKey]['papers'][$paperKey];
                }
            }
            
            //further processing
            $to_process = []; //for positions
            foreach($return['body'] as $studentKey => $body) {
                
                if(empty($body['total'])) {
                    unset($return['body'][$studentKey]);
                    continue;
                    // because there was no total found
                }

                // percentage
                $return['body'][$studentKey]['percentage'] = round(
                    $body['total'] 
                    / $this->app['scoring'][$classCategoryID][$subjectKey]['total']
                    * 100,
                    2
                );

                //get grade
                $return['body'][$studentKey]['grade'] =  \Core\Functions::getGrade(
                    $return['body'][$studentKey]['percentage'], 
                    $gradingSystem
                );

                // process for positions
                $to_process[$studentKey] = $return['body'][$studentKey]['percentage'];
            }
            
            // get positions
            
            $positions = \Core\Functions::processPositions($to_process);
            
            foreach($positions as $key => $value) {
                $return['body'][$key]['position'] = $value['gotten'];
            }
            
            //make sure the return values are not empty
            if(empty($return['body']) || empty($return['header']) || empty($return['other'])) continue;
            
            // save to db
            $saveResult = $this->saveResult(['resultType'=>'scoreSheet', 'classCategoryID'=>$classCategoryID, 'examinationID'=>$examinationID, 'subjectID'=> $subjectKey, 'content'=>$return]);
            
            $message[] = "Subject: " . $sub['subjectTitle'] . ': '. $saveResult[0];

        }

        $results = $this->results(['resultType'=>'scoreSheet', 'classCategoryID'=>$classCategoryID, 'examinationID'=>$examinationID]);
        $classCategory = $this->classCategory();
        $examination = $this->examination();
        \Core\View::render('results/index.php', compact('message', 'results', 'classCategory', 'examination'));

    }
    
    private function processStudentResult() {
       
        if(empty($_POST['examinationID']) || empty($_POST['classCategoryID'])) return false;

        $examinationID = $_POST['examinationID'];
        $classCategoryID = $_POST['classCategoryID'];

        //initialize return and declare needed keys
        $return['code'] = 200;

        //run bootstrap
        $this->bootstrap($classCategoryID, $examinationID);

        // set some return variables
        $return['other']['examinationTitle'] = $this->app['examination']['examinationTitle'];
        $return['other']['examinationID'] = $examinationID;
        $return['other']['classCategory'] = $this->app['classCategory']['classCategoryTitle'];
        $return['other']['classCategoryID'] = $classCategoryID;
        $gradingID = $this->app['classCategory']['gradingID'];

        // fetch all the scores 
        $scores = $this->scores(['examinationID'=>$examinationID, 'classCategoryID'=>$classCategoryID]);
        $scoresHolder = [];
        foreach($scores as $score) {
            $scoresHolder[$score['studentID']][$score['subjectID']][$score['paperID']] = $score['score'];
        }

        //prepare the paper titles in header
        foreach($this->app['scoring'][$classCategoryID] as $papers) {
            foreach($papers['papers'] as $p) {
                $this->app['header']['papers'][$p['paperSlug']] = $p['paperTitle'];
            }
        }
        
        //get the grading system
        $gradingSystem = $this->gradingSystem(['gradingID' => $gradingID]);
        $return['other']['gradingSystem'] = $gradingSystem;

        $return['body'] = [];

        //process the subjects
        foreach($this->app['subjects'] as $sub) {

            $subjectKey = $sub['ID'];

            if(empty($this->app['scoring'][$classCategoryID][$subjectKey])) continue;
            
            //subject not offered by class

            foreach($this->app['students'] as $stu) {

                $studentKey = $stu['ID'];
                
                if(!isset($return['body'][$studentKey])) {
                    $return['body'][$studentKey]['studentName'] = "{$stu['lastName']} {$stu['firstName']} {$stu['middleName']}";
                    $return['body'][$studentKey]['admissionNumber'] = $stu['admissionNumber'];
                    $return['body'][$studentKey]['examinationNumber'] = $stu['examinationNumber'];
                    $return['body'][$studentKey]['subjects'] = [];
                    $return['body'][$studentKey]['papers'] = $this->app['header']['papers'];
                    $return['body'][$studentKey]['total'] = 0;
                    $return['body'][$studentKey]['obtainableTotal'] = 0;
                    $return['body'][$studentKey]['percentage'] = 0;
                    $return['body'][$studentKey]['position'] = "";
                }

                // the heading scores
                foreach($this->app['scoring'][$classCategoryID][$subjectKey]['papers'] as $paperKey => $paperValue) {
                    if(!isset($return['body'][$studentKey]['subjects'][$subjectKey]['total'])) {
                        $return['body'][$studentKey]['subjects'][$subjectKey]['total'] = 0;
                        $return['body'][$studentKey]['subjects'][$subjectKey]['papers'] = [];
                    }
                    
                    if(empty($scoresHolder[$studentKey][$subjectKey][$paperKey])) continue;

                    $return['body'][$studentKey]['subjects'][$subjectKey]['total'] += $scoresHolder[$studentKey][$subjectKey][$paperKey];
                    $return['body'][$studentKey]['subjects'][$subjectKey]['papers'][$paperValue['paperSlug']] = $scoresHolder[$studentKey][$subjectKey][$paperKey];

                }
                
                if(empty($return['body'][$studentKey]['subjects'][$subjectKey]['total'])) {
                    unset($return['body'][$studentKey]['subjects'][$subjectKey]);
                    continue;
                }

                $return['body'][$studentKey]['subjects'][$subjectKey]['subjectTitle'] = $sub['subjectTitle'];
               
                //total / percentage
                $return['body'][$studentKey]['subjects'][$subjectKey]['percentage'] = round(
                    $return['body'][$studentKey]['subjects'][$subjectKey]['total']
                    / $this->app['scoring'][$classCategoryID][$subjectKey]['total']
                    * 100,
                    2
                );

                // add to total percentrage for student
                $return['body'][$studentKey]['total'] += $return['body'][$studentKey]['subjects'][$subjectKey]['percentage'];
                $return['body'][$studentKey]['obtainableTotal'] += 100;

                //get grade
                $return['body'][$studentKey]['subjects'][$subjectKey]['grade'] =  \Core\Functions::getGrade(
                    $return['body'][$studentKey]['subjects'][$subjectKey]['percentage'], 
                    $gradingSystem
                );

            }

            //further processing
            foreach($return['body'] as $studentKey => $body) { 
                if(empty($body['total'])) { 
                    unset($return['body'][$studentKey]); } }
        }

        // calculate percentages and position
        $toProcess = [];

        foreach($return['body'] as $studentKey => $body) { 

            // calculate percentage 
            $return['body'][$studentKey]['percentage'] = round(
                $return['body'][$studentKey]['total'] /
                $return['body'][$studentKey]['obtainableTotal']
                * 100
                , 2
            );

            $toProcess[$studentKey] = $return['body'][$studentKey]['percentage'];

        }
        // get positions
        $positions = \Core\Functions::processPositions($toProcess);
        
        //finalize 
        foreach($positions as $key => $value) {

            $return['body'][$key]['position'] = $value;

            // save to db
            $toDb = ['code'=>200, 'body'=> $return['body'][$key], 'other'=> $return['other']];

            $saveResult = $this->saveResult(
                [
                    'resultType'=>'studentResult', 
                    'classCategoryID'=>$classCategoryID, 
                    'examinationID'=>$examinationID, 
                    'studentID'=> $key, 
                    'content'=>$toDb
                ]
            );
        
            $message[] = "Student: " . $return['body'][$key]['studentName'] . ': '. $saveResult[0];

        }

        $results = $this->results(['resultType'=>'studentResult', 'classCategoryID'=>$classCategoryID, 'examinationID'=>$examinationID]);
        $classCategory = $this->classCategory();
        $examination = $this->examination();
        
        \Core\View::render('results/index.php', compact('message', 'results', 'classCategory', 'examination'));

    }

    private function saveResult($params = array()) {

        if(empty($params)) return ["Cannot save result without necessary content."];
        if(empty($params['resultType'])) return ["Unable to detect result type."];
        if(empty($params['examinationID'])) return ["Unable to detect examination being recorded."];
        if(empty($params['content'])) return ["No result content detected."];

        $params['content'] = json_encode($params['content']);
        
        $results = new \Model\Results();
        foreach($params as $key => $value) {
            if (!empty($value) && property_exists($results, $key)) $results->$key = $value;
        }

        $create = $results->create();
        if($create === 1){$message = ['Result Record saved successfully'];}
        if($create === 0){$message= ['An error occured. Please try again'];}

        return $message;
    }


    // helper methods

    private function bootstrap($classCategoryID, $examinationID) {
        $this->setExamination($examinationID);
        $this->setClassCategory($classCategoryID);
        $this->setScoring($classCategoryID);
        $this->setStudents($classCategoryID);
        $this->setSubjects();
    }

    private function setExamination($examinationID) {
        if(empty($this->app['examination'])) {
            $examination = $this->examination(['ID'=>$examinationID]);
            $this->app['examination'] = $examination[0];
        }
    }

    private function setClassCategory($classCategoryID) {
        if(empty($this->app['classCategory'])) {
            $classCategory = $this->classCategory(['ID'=>$classCategoryID]);
            $this->app['classCategory'] = $classCategory[0];
        }
    }

    private function setSubjects() {
        if(empty($this->app['subjects'])) {
            $this->app['subjects'] = $this->subjects();
        }
    }

    private function setScoring($classCategoryID) {

        if(empty($this->app['scoring'][$classCategoryID])) {
            
            $this->app['scoring'][$classCategoryID] = [];

            $papers = $this->papers(['classCategoryID' => $classCategoryID]);
            
            foreach($papers as $pValue) 
            {
                if(!isset($this->app['scoring'][$classCategoryID][$pValue['subjectID']]))  {

                    $this->app['scoring'][$classCategoryID][$pValue['subjectID']]['total'] = 0;
                    $this->app['scoring'][$classCategoryID][$pValue['subjectID']]['papers'] = [];

                }
            
                $this->app['scoring'][$classCategoryID][$pValue['subjectID']]['total'] += $pValue['maximumScore'];
                $this->app['scoring'][$classCategoryID][$pValue['subjectID']]['papers'][$pValue['ID']] = $pValue;

            }

        }

    }

    private function setStudents($classCategoryID) {
        if(empty($this->app['students'])) {
            $this->app['students'] = $this->students(['classCategoryID' => $classCategoryID]);
        }
    }
}