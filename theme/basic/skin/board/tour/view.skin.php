<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
include_once($board_skin_path . '/../../filter_config.php'); // 필터 설정 로드
include_once(G5_LIB_PATH . '/thumbnail.lib.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="' . $board_skin_url . '/style.css">', 0);
?>

<!-- 게시물 읽기 시작 { -->
<article id="bo_v" style="width:100%">
    <header>
        <h2 id="bo_v_title">
            <?php if ($category_name) { ?>
                <span class="bo_v_cate"><?php echo $view['ca_name']; // 분류 출력 끝 
                                        ?></span>
            <?php } ?>
            <span class="bo_v_tit"><?php echo cut_str(get_text($view['wr_subject']), 70); // 글제목 출력 
                                    ?></span>
        </h2>
    </header>

    <!-- [CUSTOM] Add Pension Info (Homepage, Address, Phone) -->
    <div class="bo_v_extra_info" style="margin-top:10px; font-size:1.1em; line-height:1.6; padding: 0 10px;">
        <?php
        // 홈페이지 링크 처리 (wr_homepage 우선, 없으면 wr_link2(단양군청 소개페이지))
        $homepage_url = '';
        $is_fallback = false;

        $temp_url = isset($view['wr_homepage']) ? trim($view['wr_homepage']) : '';
        // 유효하지 않은 주소 패턴 (http://만 있거나 http://- 등) 및 최소 길이 체크
        $is_valid = ($temp_url && !preg_match('/^https?:\/\/[-]?$/', $temp_url) && strlen($temp_url) > 10);

        if ($is_valid) {
            $homepage_url = $temp_url;
        } elseif (isset($view['link'][2]) && $view['link'][2]) {
            $homepage_url = $view['link'][2];
            $is_fallback = true;
        } elseif (isset($view['wr_link2']) && $view['wr_link2']) {
            $homepage_url = $view['wr_link2'];
            $is_fallback = true;
        }

        if ($homepage_url) {
            $link_color = $is_fallback ? '#d9534f' : '#337ab7';
        ?>
            <p class="homepage" style="margin-bottom:5px;">
                <i class="fa fa-home" style="color:<?php echo $link_color; ?>; width:20px; text-align:center;"></i>
                <a href="<?php echo $homepage_url; ?>" target="_blank" style="color:<?php echo $link_color; ?>; text-decoration:underline;">
                    <?php if ($is_fallback) echo '<span style="font-size:0.9em; font-weight:bold;">[단양군청 제휴 정보]</span> '; ?>
                    <?php echo $homepage_url; ?>
                </a>
            </p>
        <?php } ?>

        <?php if ($view['wr_3']) { ?>
            <p class="addr" style="margin-bottom:5px;">
                <i class="fa fa-map-marker" style="color:#d9534f; width:20px; text-align:center;"></i>
                <?php echo $view['wr_3']; ?>
            </p>
        <?php } ?>

        <?php if ($view['wr_2']) { ?>
            <p class="tel" style="margin-bottom:5px;">
                <i class="fa fa-phone" style="color:#5cb85c; width:20px; text-align:center;"></i>
                <?php echo $view['wr_2']; ?>
            </p>
        <?php } ?>
    </div>
    <!-- // [CUSTOM] End -->

    <section id="bo_v_info">
        <h2>페이지 정보</h2>
        <div class="profile_info">
            <span class="sound_only">작성자</span> <strong><?php echo $view['name'] ?></strong>
            <span class="sound_only">댓글</span><strong><a href="#bo_vc"> <i class="fa fa-commenting-o" aria-hidden="true"></i> <?php echo number_format($view['wr_comment']) ?>건</a></strong>
            <span class="sound_only">조회</span><strong><i class="fa fa-eye" aria-hidden="true"></i> <?php echo number_format($view['wr_hit']) ?>회</strong>
            <strong class="if_date"><span class="sound_only">작성일</span><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo date("y-m-d H:i", strtotime($view['wr_datetime'])) ?></strong>
        </div>
    </section>

    <!-- [CUSTOM] 상세 옵션 태그 출력 영역 -->
    <section id="bo_v_custom_tags" style="margin: 20px 0; padding: 20px; background: #f8f9fa; border-radius: 8px;">
        <h3 style="font-size: 1.1em; color: #555; margin-bottom: 10px; border-left: 3px solid #000; padding-left: 10px;">
            제공 옵션 & 서비스
        </h3>
        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
            <?php
            $has_tag = false;
            // filter_config.php 의 $tour_filters 순회
            foreach ($all_filters as $key => $label) {
                // 값이 '1'인 경우만 출력
                if (isset($view[$key]) && $view[$key] == '1') {
                    $has_tag = true;
                    // 태그 스타일링
                    echo '<span style="
                        display: inline-block;
                        padding: 6px 12px;
                        background: #fff;
                        border: 1px solid #007bff;
                        color: #007bff;
                        border-radius: 20px;
                        font-size: 14px;
                        font-weight: 500;
                    ">#' . $label . '</span>';
                }
            }
            if (!$has_tag) {
                echo '<span style="color:#999;">등록된 상세 옵션이 없습니다.</span>';
            }
            ?>
        </div>
    </section>
    <!-- // [CUSTOM] 상세 옵션 태그 출력 영역 끝 -->

    <section id="bo_v_atc">
        <h2 id="bo_v_atc_title">본문</h2>

        <?php
        // 파일 출력
        $v_img_count = count($view['file']);
        if ($v_img_count) {
            echo "<div id=\"bo_v_img\">\n";
            for ($i = 0; $i <= count($view['file']); $i++) {
                if ($view['file'][$i]['view']) {
                    //echo $view['file'][$i]['view'];
                    echo get_view_thumbnail($view['file'][$i]['view']);
                }
            }
            echo "</div>\n";
        }
        ?>

        <!-- 본문 내용 시작 { -->
        <div id="bo_v_con"><?php echo get_view_thumbnail($view['content']); ?></div>
        <!-- } 본문 내용 끝 -->

        <?php if ($is_signature) { ?><p><?php echo $signature ?></p><?php } ?>
    </section>

    <!-- 링크 버튼 등 하단 메뉴 -->
    <div id="bo_v_top">
        <?php ob_start(); ?>
        <ul class="bo_v_com">
            <?php if ($update_href) { ?><li><a href="<?php echo $update_href ?>" class="btn_b01 btn"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> 수정</a></li><?php } ?>
            <?php if ($delete_href) { ?><li><a href="<?php echo $delete_href ?>" class="btn_b01 btn" onclick="del(this.href); return false;"><i class="fa fa-trash-o" aria-hidden="true"></i> 삭제</a></li><?php } ?>
            <li><a href="<?php echo $list_href ?>" class="btn_b01 btn"><i class="fa fa-list" aria-hidden="true"></i> 목록</a></li>
        </ul>
        <?php
        $link_buttons = ob_get_contents();
        ob_end_flush();
        ?>
    </div>

</article>
<!-- } 게시물 읽기 끝 -->