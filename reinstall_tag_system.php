<?php
include_once('./_common.php');

// 1. Check if board exists
$bo_table = 'tour';
if (!isset($g5['write_prefix'])) $g5['write_prefix'] = $g5['table_prefix'] . 'write_'; // Safety
$write_table = $g5['write_prefix'] . $bo_table;

$sql = " SHOW TABLES LIKE '{$write_table}' ";
$row = sql_fetch($sql);

if (!$row) {
    echo "<h3>[오류] '{$bo_table}' 게시판 테이블이 존재하지 않습니다.</h3>";
    echo "<p>관리자 페이지에서 게시판 ID를 <strong>tour</strong>로 하여 생성해주신 후 다시 실행해주세요.</p>";
    exit;
} else {
    echo "<h3>[확인] '{$bo_table}' 게시판 테이블이 정상적으로 존재합니다.</h3>";
}

// 2. Drop existing tag table
$tag_table = 'g5_write_tour_options';
$sql = " DROP TABLE IF EXISTS {$tag_table} ";
sql_query($sql);
echo "<p>[삭제] 기존 태그 테이블({$tag_table})을 삭제했습니다.</p>";

// 3. Create tag table
$sql = "
CREATE TABLE IF NOT EXISTS `{$tag_table}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wr_id` int(11) NOT NULL DEFAULT '0',
  `option_key` varchar(50) NOT NULL DEFAULT '',
  `reg_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wr_id` (`wr_id`),
  KEY `option_key` (`option_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
sql_query($sql);
echo "<p>[생성] 태그 테이블({$tag_table})을 새로 생성했습니다.</p>";

// 4. Force Board Skin & Width Settings (Just in case)
$sql = " UPDATE {$g5['board_table']} SET bo_table_width = 0, bo_skin = 'tour', bo_theme = 'basic' WHERE bo_table = '{$bo_table}' ";
sql_query($sql);
echo "<p>[설정] 게시판 스킨(tour), 테마(basic), 너비(100%) 설정을 강제 적용했습니다.</p>";

echo "<hr>";
echo "<h2>🎉 초기화 완료!</h2>";
echo "<p>이제 게시판에 글을 쓰시면 태그 시스템이 정상 작동합니다.</p>";
echo "<p><a href='" . G5_BBS_URL . "/board.php?bo_table={$bo_table}'>[게시판 바로가기]</a></p>";
