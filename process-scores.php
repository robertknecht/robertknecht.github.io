<?php

function getFrameScore($index, $score1, $score2 = false, $score3 = false) {
    if ($index == 9) { // must be 10th frame
        $score = intval($score1) + intval($score2);
        if ($score3) {
            $score += intval($score3);
        }
        return $score;
    }   
    $score1 = intval($score1);
    $score2 = intval($score2);
    if (intval($score1) == 10) {
        return 'strike';
    }
    if ($score1 + $score2 == 10) {
        return 'spare';
    }
    return $score1 + $score2;
}
function checkStrike($index, $score_arr) {
    if ($index == 10) {
        return 10 + $score_arr[$index]['second'] + $score_arr[$index]['third'];
    }
    $next_2_shots = [];
    if (isset($score_arr[$index + 1])) {
        array_push($next_2_shots, $score_arr[$index + 1]['first']);
    }
    if ($score_arr[$index + 1]['second']) {
        array_push($next_2_shots, $score_arr[$index + 1]['second']);
    }
    if (isset($score_arr[$index + 2])) {
        array_push($next_2_shots, $score_arr[$index + 2]['first']);
    }
    if (count($next_2_shots) > 1) {
        return 10 + $next_2_shots[0] + $next_2_shots[1];
    } else {
        return false;
    }
}
function checkSpare($index, $score_arr) {
    if ($index == 10) { // i.e. we're on the 10th frame
        return 10 + $score_arr[$index]['third'];
    }
    if (!isset($score_arr[$index + 1])) {
        return false;
    }
    return 10 + $score_arr[$index + 1]['first'];
}

$score = $_POST['scores'][0];
if (isset($_POST['scores'][1])) {
    $score = $score . ',' . $_POST['scores'][1];
}
if (isset($_POST['scores'][2])) { // last frame
    $score = $score . ',' . $_POST['scores'][2];
}

$file = fopen("make-shift-database.txt", "a");
fwrite($file, $score . "\n");
fclose($file);

$frame_arr = explode("\n", file_get_contents("make-shift-database.txt"));
array_pop($frame_arr); // remove empty string cause by final return carriage
if (count($frame_arr) > 9) {
    unlink('make-shift-database.txt'); // we're done with our make-shift database and can delete it to start afresh some other time
}
$score_arr = [];
foreach ($frame_arr as $key => $frame) {
    $frame_split = explode(',', $frame);
    $first = $frame_split[0];
    $second = false;
    $third = false;
    // print_r($frame_split);
    if (isset($frame_split[1])) {
        $second = $frame_split[1];
    }
    if (isset($frame_split[2])) {
        $third = $frame_split[2];
    }
    $score_arr[$key] = [
        'first' => $first,
        'second' => $second,
        'third' => $third,
        'frameScore' => getFrameScore($key, $first, $second, $third)
    ];
}
$return_arr = [];
$score_total = 0;
foreach ($score_arr as $key => $score) {
    if ($score['frameScore'] == 'strike') {
        $score['frameScore'] = checkStrike($key, $score_arr);
    }
    if ($score['frameScore'] == 'spare') {
        $score['frameScore'] = checkSpare($key, $score_arr);
    }
    if (!$score['frameScore']) {
        $running_total = false;
    } else {
        $score_total = $score_total + $score['frameScore'];
        $running_total = $score_total;
    }
    
    $return_arr["$key"] = [
        'first' => $score['first'],
        'second' => $score['second'],
        'third' => $score['third'],
        'running_total' => $running_total
    ];
}
echo json_encode($return_arr);

?>
