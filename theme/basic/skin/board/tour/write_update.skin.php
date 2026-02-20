<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

// [태그 시스템] 옵션 저장 로직
// 1. g5_write_tour_options 테이블에 저장 (개별 행)
// 2. wr_1 필드에 공백 구분자로 연결하여 저장 (검색용)

// 기존 옵션 삭제 (수정 모드일 경우)
sql_query(" DELETE FROM g5_write_tour_options WHERE wr_id = '{$wr_id}' ");

$option_keys = array();

if (isset($_POST['options']) && is_array($_POST['options'])) {
    foreach ($_POST['options'] as $opt_key) {
        $opt_key = trim($opt_key);
        if (!$opt_key) continue;

        // DB Insert
        $sql = " INSERT INTO g5_write_tour_options
                    SET wr_id = '{$wr_id}',
                        option_key = '{$opt_key}',
                        reg_date = NOW() ";
        sql_query($sql);

        $option_keys[] = $opt_key;
    }
}

// wr_1 필드 업데이트 (공백 구분자로 연결)
// 예: "stay_pool stay_bbq food_pasta"
$wr_1_data = implode(' ', $option_keys);
$sql = " UPDATE {$write_table} SET wr_1 = '{$wr_1_data}' WHERE wr_id = '{$wr_id}' ";
sql_query($sql);

// [CUSTOM] 홈페이지(wr_homepage) 수동 업데이트
// 그누보드 기본 write_update.php 에서 link1, link2 등을 따로 처리하지만
// 스킨 내에서 wr_homepage 필드를 추가로 사용할 경우 이처럼 skin 파일에서 저장 처리해 줄 수 있습니다.
if (isset($_POST['wr_homepage'])) {
    $wr_homepage = clean_xss_tags(trim($_POST['wr_homepage']));
    $sql = " UPDATE {$write_table} SET wr_homepage = '{$wr_homepage}' WHERE wr_id = '{$wr_id}' ";
    sql_query($sql);
}
