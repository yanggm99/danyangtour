<?php
include_once('./_common.php');

$bo_table = 'tour';
echo "<h2>Diagnosing Board Table: {$g5['board_table']}</h2>";

// 1. Check Columns
$sql = " SHOW COLUMNS FROM {$g5['board_table']} ";
$result = sql_query($sql);
$columns = array();
while ($row = sql_fetch_array($result)) {
    $columns[] = $row['Field'];
}

echo "<h3>Columns (Partial List):</h3>";
echo "bo_skin: " . (in_array('bo_skin', $columns) ? "EXISTS" : "MISSING") . "\n";
echo "bo_theme: " . (in_array('bo_theme', $columns) ? "EXISTS" : "MISSING") . "\n";
echo "bo_table_width: " . (in_array('bo_table_width', $columns) ? "EXISTS" : "MISSING") . "\n";

// 2. Try Isolated Updates with Error Reporting
echo "<h3>Isolated Update Tests:</h3>";

// Test 1: Width
$sql = " UPDATE {$g5['board_table']} SET bo_table_width = 0 WHERE bo_table = '{$bo_table}' ";
$res = sql_query($sql, false);
if ($res) echo "Update Width: SUCCESS \n";
else echo "Update Width: FAILED - " . sql_error_info() . "\n";

// Test 2: Skin
$sql = " UPDATE {$g5['board_table']} SET bo_skin = 'tour' WHERE bo_table = '{$bo_table}' ";
$res = sql_query($sql, false);
if ($res) echo "Update Skin: SUCCESS \n";
else echo "Update Skin: FAILED - " . sql_error_info() . "\n";

// Test 3: Theme (Only if column exists)
if (in_array('bo_theme', $columns)) {
    $sql = " UPDATE {$g5['board_table']} SET bo_theme = 'basic' WHERE bo_table = '{$bo_table}' ";
    $res = sql_query($sql, false);
    if ($res) echo "Update Theme: SUCCESS \n";
    else echo "Update Theme: FAILED - " . sql_error_info() . "\n";
} else {
    echo "Update Theme: SKIPPED (Column Missing) \n";
}

// 3. Final State Check
$sql = " SELECT bo_skin, bo_theme, bo_table_width FROM {$g5['board_table']} WHERE bo_table = '{$bo_table}' ";
$board = sql_fetch($sql);
echo "<hr>";
echo "Final State:\n";
echo "Skin: " . $board['bo_skin'] . "\n";
echo "Theme: " . $board['bo_theme'] . "\n";
echo "Width: " . $board['bo_table_width'] . "\n";
