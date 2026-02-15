<?php
include_once('./_common.php');
$bo_table = 'tour';
$sql = " SELECT bo_skin, bo_theme, bo_table_width FROM {$g5['board_table']} WHERE bo_table = '{$bo_table}' ";
$board = sql_fetch($sql);
echo "Skin: " . $board['bo_skin'] . "\n";
echo "Theme: " . $board['bo_theme'] . "\n";
echo "Width: " . $board['bo_table_width'] . "\n";
