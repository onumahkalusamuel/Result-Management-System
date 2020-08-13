<?php
namespace Core;

class Functions {    
    
    static function sanitize($input) {
        return htmlspecialchars(strip_tags(trim($input)));
    }

    static function make_slug($title) {
        $slug = strtolower($title);
        $slug = trim(preg_replace('/[^A-Za-z0-9]+/', '-', $slug),'-');
        return $slug;
    }

    static function prepareUpdateData($data) {
        $updateArray = [];
        foreach($data as $key => $d) 
            if( property_exists($data, $key) && ($key !== 'id') && ($key !== 'status') && !empty($d) ) $updateArray[] = $key .'="'. $d .'"';
        return implode(', ', $updateArray);
    }

    static function getGrade($total, $gradingSystem) {
        foreach( $gradingSystem as $grader ) 
            if ( ($total >= $grader['minimumScore']) && ($total < ($grader['maximumScore']+1)) ) 
                return $grader['grade'];
    }

    static function positionGenerator($number) {
        if(empty($number) || gettype($number) != "integer") return false;
        // accommodate *11th, *12th, *13th
        $last_two_numbers = (int) substr($number,strlen($number)-2, 2);
        switch($last_two_numbers) {
            case 11: case 12: case 13: $suffix = "th"; break;
            default: $suffix = null; break;
        }
        //accomodate the rest
        if(empty($suffix)) {
            $last_number = substr($number, strlen($number)-1, 1);
            switch ($last_number) {
                case 1: $suffix = "st"; break;
                case 2: $suffix = "nd"; break;
                case 3: $suffix = "rd"; break;
                default: $suffix = "th"; break;
            }
        }
        return $number . $suffix;
    }

    static function calculateAverage($array) {
        if (empty($array)) return false;
        $f = $fx = 0;
        $f = count($array);
        foreach($array as $val) $fx += $val;
        return round($fx / $f, 2);
    }

    static function sortArrayWithStringKeys($array, $reverse = false) {
        if(empty($array)) return false;
        $return = [];
        $keys_bucket = array_keys($array);
        sort($keys_bucket);
        foreach($keys_bucket as $k_b) {
            $return[$k_b] = $array[$k_b];
        }
        return $return;
    }

    static function processPositions($array = []) {
        $return = $scores = $scores2 = [];
        foreach($array as $key => $data) {
            $uniqid = $data . '-' . $key;
            $scores[$uniqid] = $key;
            $scores2[$key] = $uniqid;
        }
        
        $val = array_values($scores2);
        sort($val);
        $total_positions = $outof = count($val);
        $val = array_reverse($val);

        $current_position_holder; $position_to_generate;
        for($x = 0; $x < $total_positions; $x++) {
            $id = $scores[$val[$x]];
            if($x === 0) $position_to_generate = $current_position_holder = $x+1;
            if($x !== 0) {
                $previous = explode('-', $val[$x-1])[0];
                $present = explode('-', $val[$x])[0];
                if ($previous == $present) {
                    $position_to_generate = $current_position_holder;
                } else {
                    $position_to_generate = $current_position_holder = $x+1;
                }
            }
            $return[$id]['gotten'] = self::positionGenerator($position_to_generate);
            $return[$id]['outof'] = $outof;
        }
        return $return;
    }

    
}