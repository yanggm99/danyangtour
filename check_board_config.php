<?php
include_once('./_common.php');

$bo_table = 'tour';
$sql = " SELECT * FROM {$g5['board_table']} WHERE bo_table = '{$bo_table}' ";
$board = sql_fetch($sql);

echo "Board: " . $bo_table . "\n";
echo "bo_table_width: " . $board['bo_table_width'] . "\n";
echo "bo_skin: " . $board['bo_skin'] . "\n";
echo "bo_mobile_skin: " . $board['bo_mobile_skin'] . "\n";

if ($board['bo_table_width'] < 100 && $board['bo_table_width'] != 0) {
    echo "Warning: Width is very small! Resetting to 100 (which typically means 100% or default).\n";
    $sql = " UPDATE {$g5['board_table']} SET bo_table_width = 100 WHERE bo_table = '{$bo_table}' ";
    sql_query($sql);
    echo "Reset complete.\n";
} else if ($board['bo_table_width'] == 0) {
    echo "Width is 0. This typically means 100% in GnuBoard, but let's force 100 just in case.\n";
    $sql = " UPDATE {$g5['board_table']} SET bo_table_width = 100 WHERE bo_table = '{$bo_table}' ";
    sql_query($sql);
    echo "Set to 100.\n";
} else {
    echo "Width seems okay (" . $board['bo_table_width'] . ").\n";
}
