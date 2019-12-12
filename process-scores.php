<?php

function getFrameScore($score1, $score2 = false) {
    if (!$score2) {
        return 'strike';
    }
    $score1 = intval($score1);
    $score2 = intval($score2);
    if ($score1 + $score2 == 10) {
        return 'spare';
    }
    return $score1 + $score2;
}
function checkStrike($index, $score_arr) {
    if (!isset($score_arr[$index + 1])) {
        return false;
    }
    $frame_score = 10 + $score_arr[$index + 1]['first'];
    if (isset($score_arr[$index + 1]['second'])) {
        $frame_score += $score_arr[$index + 1]['second'];
    } else if (isset($score_arr[$index + 2])) {
        $frame_score += $score_arr[$index + 2]['first'];
    } else {
        return false;
    }
    return $frame_score;
}
function checkSpare($index, $score_arr) {
    if (!isset($score_arr[$index + 1])) {
        return false;
    }
    return 10 + $score_arr[$index + 1]['first'];
}

$score = $_POST['scores'][0];
if (isset($_POST['scores'][1])) {
    $score = $score . ',' . $_POST['scores'][1];
}
$file = fopen("make-shift-database.txt", "a");
fwrite($file, $score . "\n");
fclose($file);
$frame_arr = explode("\n", file_get_contents("make-shift-database.txt"));
array_pop($frame_arr);
if (count($frame_arr) > 9) {
    unlink('make-shift-database.txt');
}
$score_arr = [];
foreach ($frame_arr as $key => $frame) {
    $frame_split = explode(',', $frame);
    $first = $frame_split[0];
    $second = false;
    if (isset($frame_split[1])) {
        $second = $frame_split[1];
    }
    $score_arr[$key] = [
        'first' => $first,
        'second' => $second,
        'frameScore' => getFrameScore($first, $second)
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
        'running_total' => $running_total
    ];
}
echo json_encode($return_arr);

?>
