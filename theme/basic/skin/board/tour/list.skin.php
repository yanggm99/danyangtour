<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(dirname(__FILE__) . '/../../../filter_config.php'); // 필터 설정 로드
include_once(G5_LIB_PATH . '/thumbnail.lib.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="' . $board_skin_url . '/style.css">', 0);
?>

<!-- 게시판 목록 시작 { -->
<div id="bo_list" style="width:100%">

    <!-- [CUSTOM] 필터 검색 영역 -->
    <div class="filter_search_wrap" style="margin-bottom: 20px; border: 1px solid #ddd; padding: 20px; background: #fafafa; border-radius: 8px;">
        <form name="fsearch" method="get" onsubmit="return fsearch_submit(this);">
            <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
            <input type="hidden" name="sca" value="<?php echo $sca ?>">
            <input type="hidden" name="sop" value="and">
            <input type="hidden" name="sfl" value="wr_1">
            <input type="hidden" name="stx" value="">

            <h3 style="font-size:18px; margin-bottom:15px; font-weight:bold;">
                <i class="fa fa-search"></i> 맞춤형 단양 여행 찾기
            </h3>

            <div class="filter_categories">
                <?php foreach ($tour_filters as $key => $category) { ?>
                    <div style="margin-bottom: 15px;">
                        <strong style="display:block; font-size:14px; margin-bottom:8px; color:#333;"><?php echo $category['label']; ?></strong>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                            <?php foreach ($category['items'] as $k => $v) {
                                $label = is_array($v) ? $v['label'] : $v;
                                // 검색 파라미터가 있는지 확인 (예: &wr_11=1)
                                $is_active = (isset($_GET[$k]) && $_GET[$k] == '1') ? 'checked' : '';
                                $active_style = $is_active ? 'background:#007bff; color:#fff; border-color:#007bff;' : 'background:#fff;';
                            ?>
                                <label class="filter_tag" style="<?php echo $active_style; ?> display:inline-block; padding:5px 10px; border:1px solid #ccc; border-radius:15px; font-size:13px; cursor:pointer;">
                                    <input type="checkbox" name="<?php echo $k ?>" value="1" <?php echo $is_active ?> style="display:none;" onchange="toggle_filter_tag(this);">
                                    <?php echo $label; ?>
                                </label>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div style="margin-top:20px; text-align:center;">
                <button type="button" onclick="location.href='<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $bo_table ?>'" class="btn btn_default">필터 초기화</button>
                <button type="submit" class="btn btn_submit">검색하기</button>
            </div>
        </form>

        <script>
            function toggle_filter_tag(chk) {
                var label = chk.parentNode;
                if (chk.checked) {
                    label.style.background = '#007bff';
                    label.style.color = '#fff';
                    label.style.borderColor = '#007bff';
                } else {
                    label.style.background = '#fff';
                    label.style.color = '#333';
                    label.style.borderColor = '#ccc';
                }
            }

            function fsearch_submit(f) {
                var stx_arr = [];
                var chks = f.querySelectorAll('.filter_tag input[type="checkbox"]:checked');
                for (var i = 0; i < chks.length; i++) {
                    stx_arr.push(chks[i].name);
                }

                if (stx_arr.length > 0) {
                    f.stx.value = stx_arr.join(' ');
                    f.sfl.value = 'wr_1';
                } else {
                    f.stx.value = '';
                    f.sfl.value = '';
                }
                return true;
            }
        </script>
    </div>
    <!-- // [CUSTOM] 필터 검색 영역 끝 -->

    <!-- 게시판 카테고리 시작 { -->
    <?php if ($is_category) { ?>
        <nav id="bo_cate">
            <h2><?php echo $board['bo_subject'] ?> 카테고리</h2>
            <ul id="bo_cate_ul">
                <?php echo $category_option ?>
            </ul>
        </nav>
    <?php } ?>
    <!-- } 게시판 카테고리 끝 -->

    <div class="bo_fx">
        <div id="bo_list_total">
            <span>Total <?php echo number_format($total_count) ?>건</span>
            <?php echo $page_rows ?> 페이지
        </div>
        <?php if ($rss_href || $write_href) { ?>
            <ul class="btn_bo_user">
                <?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b01 btn">RSS</a></li><?php } ?>
                <?php if ($admin_href) { ?><li><a href="<?php echo $admin_href ?>" class="btn_admin btn">관리자</a></li><?php } ?>
                <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02 btn">글쓰기</a></li><?php } ?>
            </ul>
        <?php } ?>
    </div>

    <form name="fboardlist" id="fboardlist" action="./board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
        <!-- list content -->
        <div class="tbl_head01 tbl_wrap">
            <table>
                <caption class="sound_only"><?php echo $board['bo_subject'] ?> 목록</caption>
                <thead>
                    <tr>
                        <th scope="col">번호</th>
                        <?php if ($is_checkbox) { ?>
                            <th scope="col">
                                <label for="chkall" class="sound_only">현재 페이지 게시물 전체</label>
                                <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);">
                            </th>
                        <?php } ?>
                        <th scope="col">제목</th>
                        <th scope="col">글쓴이</th>
                        <th scope="col"><?php echo subject_sort_link('wr_hit', $qstr2, 1) ?>조회</a></th>
                        <th scope="col"><?php echo subject_sort_link('wr_datetime', $qstr2, 1) ?>날짜</a></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    for ($i = 0; $i < count($list); $i++) {
                    ?>
                        <tr class="<?php if ($list[$i]['is_notice']) echo "bo_notice"; ?>" style="border-bottom: 2px solid #ddd;">
                            <td class="td_num">
                                <?php
                                if ($list[$i]['is_notice']) // 공지사항
                                    echo '<strong>공지</strong>';
                                else if ($wr_id == $list[$i]['wr_id'])
                                    echo "<span class=\"bo_current\">열람중</span>";
                                else
                                    echo $list[$i]['num'];
                                ?>
                            </td>
                            <?php if ($is_checkbox) { ?>
                                <td class="td_chk">
                                    <label for="chk_wr_id_<?php echo $i ?>" class="sound_only"><?php echo $list[$i]['subject'] ?></label>
                                    <input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
                                </td>
                            <?php } ?>
                            <td class="td_subject" style="padding-left:<?php echo $list[$i]['reply'] ? (strlen($list[$i]['wr_reply']) * 10) : '0'; ?>px">
                                <?php
                                if ($list[$i]['is_notice'])
                                    echo "<strong>공지</strong>";

                                echo "<a href=\"" . $list[$i]['href'] . "\" style=\"font-size:16px; font-weight:bold; color:#333;\">";
                                echo $list[$i]['subject'];
                                echo "</a>";

                                // [CUSTOM] 업체 기본 정보 노출 (홈페이지, 전화번호, 주소)
                                echo '<div style="margin: 5px 0 5px 0; font-size:13px; color:#666;">';
                                if ($list[$i]['wr_homepage']) {
                                    echo '<div style="margin-bottom:3px;"><i class="fa fa-home" style="color:#007bff; width:15px; text-align:center;"></i> <a href="' . $list[$i]['wr_homepage'] . '" target="_blank" style="color:#007bff; text-decoration:none;">' . $list[$i]['wr_homepage'] . '</a></div>';
                                }
                                if ($list[$i]['wr_2']) {
                                    echo '<div style="margin-bottom:3px;"><i class="fa fa-phone" style="color:#28a745; width:15px; text-align:center;"></i> ' . $list[$i]['wr_2'] . '</div>';
                                }
                                if ($list[$i]['wr_3']) {
                                    echo '<div><i class="fa fa-map-marker" style="color:#dc3545; width:15px; text-align:center;"></i> ' . $list[$i]['wr_3'] . '</div>';
                                }
                                echo '</div>';

                                if ($list[$i]['comment_cnt'])
                                    echo $list[$i]['comment_cnt'];

                                // [CUSTOM] 목록에도 주요 태그 노출 (모두 보이기)
                                $shown_tags = 0;
                                echo '<div class="list_tags" style="margin-top:5px;">';
                                foreach ($all_filters as $key => $val) {
                                    // $val은 array('label'=>..., 'keywords'=>...) 형태이므로 label 추출
                                    $label = is_array($val) ? $val['label'] : $val;

                                    // wr_1 필드에 해당 키가 포함되어 있는지 확인
                                    if (strpos($list[$i]['wr_1'], $key) !== false) {
                                        echo '<span style="display:inline-block; font-size:11px; background:#f1f1f1; color:#555; padding:3px 6px; border-radius:12px; margin-right:4px; margin-bottom:4px;">#' . $label . '</span>';
                                        $shown_tags++;
                                    }
                                }
                                echo '</div>';
                                ?>
                            </td>
                            <td class="td_name sv_use"><?php echo $list[$i]['name'] ?></td>
                            <td class="td_num"><?php echo $list[$i]['wr_hit'] ?></td>
                            <td class="td_datetime"><?php echo $list[$i]['datetime2'] ?></td>
                        </tr>
                    <?php } ?>
                    <?php if (count($list) == 0) {
                        echo '<tr><td colspan="' . $colspan . '" class="empty_table">게시물이 없습니다.</td></tr>';
                    } ?>
                </tbody>
            </table>
        </div>

        <!-- 페이지 -->
        <?php echo $write_pages; ?>
        <!-- 페이지 -->

    </form>
</div>
<!-- } 게시판 목록 끝 -->