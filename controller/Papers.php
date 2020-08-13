<?php

namespace Controller;

class Papers extends \Core\Controller {

    public function index() {
        $message = [];
        if(isset($_POST['create'])) $message = $this->create();
        if(isset($_POST['delete'])) $message = $this->delete();

        $classCategory = $this->classCategory();
        $subjects = $this->subjects();

        \Core\View::render('papers/index.php', compact('message', 'subjects', 'classCategory'));
    }

    private function create() {

        $message = ["No input detected"];
        $errorCount = 0;

        $papers = new \Model\Papers();

        if(!empty($_POST)) {
            
            foreach($_POST['papers'] as $classCategoryID => $eachPaper) {

                $papers->classCategoryID = $classCategoryID;

                foreach($eachPaper as $subjectID => $eachSubject) {

                    $papers->subjectID = $subjectID;

                    foreach($eachSubject as $details) {
                        $papers->paperTitle = $details['paperTitle'];
                        $papers->maximumScore = $details['maximumScore'];

                        if( $papers->create() !== 1 ) $errorCount++;
                    }
                }
            }
            
            $message = ['Operation completed successfully. Error Count: ' . $errorCount];
        }
        return $message;
    }

    private function delete() { 

        $message = ["No input detected"];

        if(!empty($_POST['delete'])) {

            $papers = new \Model\Papers();
            $papers->ID = $_POST['delete'];

            $delete = $papers->delete();

            if($delete === 1){$message = ['Paper deleted successfully'];}
            if($delete === 0){$message = ['The specified paper was not found'];}

            return $message;

        }

    }

    public function allpapers() {
        $message = $papers = [];

        if(isset($_POST['fetchPapers'])) $papers = $this->papers(['classCategoryID'=>$_POST['classCategoryID']]);;
        if(isset($_POST['delete'])) $message = $this->delete();

        $classCategory = $this->classCategory();

        \Core\View::render('papers/allpapers.php', compact('message', 'papers', 'classCategory'));
    }
}