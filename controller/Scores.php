<?php

namespace Controller;

class Scores extends \Core\Controller {

    public function index() {

        $message = $scores = [];

        if(isset($_POST['fetchStudentScore'])) $scores = $this->fetchStudentScore();

        if(isset($_POST['create'])) $message = $this->create();
        if(isset($_POST['delete'])) $message = $this->delete();

        $papers = $this->getPapers();
        $classCategory = $this->classCategory();
        $examination = $this->examination();

        \Core\View::render('scores/index.php', compact('message', 'papers', 'classCategory', 'examination', 'scores'));

    }

    private function create() {
        
        $message = ["No input detected"];
        $errorCount = 0;

        $scores = new \Model\Scores();
        $scores->examinationID = $_POST['examinationID'];
        
        if(empty($_POST['examinationID'])) return $message;

        if(!empty($_POST['scores'])) {

            foreach($_POST['scores'] as $studentID => $eachPaper) {

                $scores->studentID = $studentID;

                foreach($eachPaper as $paperID => $details) {

                    if(empty($details['score'])) continue;

                    $scores->paperID = $paperID;
                    $scores->score = $details['score'];

                    if(!empty($details['scoreID'])) {
                        $scores->ID = $details['scoreID'];
                        if( $scores->update() !== 1 ) $errorCount++;
                    } else {
                        if( $scores->create() !== 1 ) $errorCount++;
                    }
                }
            }
            
            $message = ['Operation completed successfully. Error Count: ' . $errorCount];
        }
        return $message;
    }

    private function delete($id = null) { 

        $message = ["No input detected"];

        if(!empty($_POST['delete'])) {

            $papers = new \Model\Scores();
            $papers->ID = $_POST['delete'];

            $delete = $papers->delete();

            if($delete === 1){$message = ['Grading System deleted successfully'];}
            if($delete === 0){$message = ['The specified user was not found'];}

            return $message;

        }

    }

    private function getPapers() { 

        $return = [];

        $papers = $this->papers();
        
        if(!empty($papers)) {

            foreach($papers as $r)  {

                if(empty($return[$r['classCategoryID']][$r['subjectID']])) {

                    $return[$r['classCategoryID']][$r['subjectID']] = [
                        'ID' => $r['subjectID'],
                        'subjectTitle' => $r['subjectTitle'],
                        'papers' => [$r['paperTitle']]
                    ];

                } else {
                    $return[$r['classCategoryID']][$r['subjectID']]['papers'][] = $r['paperTitle'];
                }
            }
        }

        return $return;
    }

    private function fetchStudentScore() {

        $classCategoryID = $_POST['classCategoryID'];
        $subjectID = $_POST['subjectID'];
        $examinationID = $_POST['examinationID'];

        if(empty($classCategoryID) || empty($subjectID) || empty($examinationID)) return false;

        $return = ['header'=>[], 'body'=>[], 'other'=>[
            'examinationID' => $examinationID
        ]];

        $bodyTemplate = [];
        $oldScores = [];
        
        // pick exam title
        $return['other']['examinationTitle'] = $this->examination(['ID'=>$examinationID])[0]['examinationTitle'];

        $pap = $this->papers(['subjectID'=>$subjectID, 'classCategoryID'=>$classCategoryID]);

        //get basic details
        $return['other']['subjectTitle'] = $pap[0]['subjectTitle'];
        $return['other']['classCategoryTitle'] = $pap[0]['classCategoryTitle'];

        $stu = $this->students(['classCategoryID'=>$classCategoryID]);

        foreach($pap as $r) {

            $return['header'][$r['ID']] = "{$r['paperTitle']} ({$r['maximumScore']})";

            $bodyTemplate[$r['ID']] = [
                'score' => '',
                'scoreID' => ''
            ];

            // get the scores too
            $oldScores[$r['ID']] = $this->scores(['paperID' => $r['ID'], 'examinationID' => $examinationID]);

        }

        foreach($stu as $s) {
            $return['body'][$s['ID']]['studentDetails'] = [
                'name' => "{$s['lastName']} {$s['firstName']} {$s['middleName']}",
                'admissionNumber' => $s['admissionNumber'],
                'examinationNumber' => $s['examinationNumber'],
            ];
            $return['body'][$s['ID']]['scores'] = $bodyTemplate;
        }

        foreach($oldScores as $pID => $pScores) {

            if(empty($pScores)) continue;

            foreach($pScores as $pScore) {
                $return['body'][$pScore['studentID']]['scores'][$pID]['score'] = $pScore['score'];
                $return['body'][$pScore['studentID']]['scores'][$pID]['scoreID'] = $pScore['ID'];
            }
        }

        return $return;

    }

    public function scoreSheet() {
        $message = $scores = [];
        if(isset($_POST['generateEmptyScoreSheet'])) return $this->generateEmptyScoreSheet();
        $papers = $this->getPapers();
        $classCategory = $this->classCategory();
        $examination = $this->examination();

        \Core\View::render('scores/scoreSheet.php', compact('message', 'papers', 'classCategory', 'examination', 'scores'));
    }

    public function generateEmptyScoreSheet() {
        
        $classCategoryID = $_POST['classCategoryID'];
        $subjectID = $_POST['subjectID'];
        $examinationID = $_POST['examinationID'];

        if(empty($classCategoryID) || empty($subjectID) || empty($examinationID)) return false;

        $return = ['header'=>[], 'body'=>[], 'other'=>[
            'examinationID' => $examinationID
        ]];

        $bodyTemplate = [];
        
        // pick exam title
        $return['other']['examinationTitle'] = $this->examination(['ID'=>$examinationID])[0]['examinationTitle'];

        $pap = $this->papers(['subjectID'=>$subjectID, 'classCategoryID'=>$classCategoryID]);

        //get basic details
        $return['other']['subjectTitle'] = $pap[0]['subjectTitle'];
        $return['other']['classCategoryTitle'] = $pap[0]['classCategoryTitle'];

        $stu = $this->students(['classCategoryID'=>$classCategoryID]);

        $return['header']['total'] = 0;
        
        foreach($pap as $r) {
            $return['header']['papers'][$r['ID']] = "{$r['paperTitle']} ({$r['maximumScore']})";
            $return['header']['total'] += $r['maximumScore'];
        }

        foreach($stu as $s) {
            $return['body'][$s['ID']]['studentDetails'] = [
                'studentName' => "{$s['lastName']} {$s['firstName']} {$s['middleName']}",
                'admissionNumber' => $s['admissionNumber'],
                'examinationNumber' => $s['examinationNumber']
            ];
            $return['header']['studentDetails'] = [
                'studentName' => "Student Name",
                'admissionNumber' => "Admission Number",
                'examinationNumber' => "Examination Number"
            ];
        }

        \Core\View::render('scores/scoreSheetTemplate.php', $return);
    }
}