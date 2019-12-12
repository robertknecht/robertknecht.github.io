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
    //
}
function checkSpare($index, $score_arr) {
    //
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
$score_total = 0;
foreach ($score_arr as $key => $score) {
    if ($score['frameScore'] == 'strike') {
        
    }
}
echo json_encode($score_arr);

?>
