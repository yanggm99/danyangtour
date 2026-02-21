<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(dirname(__FILE__) . '/../../../filter_config.php'); // 필터 설정 로드
include_once(G5_LIB_PATH . '/thumbnail.lib.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="' . $board_skin_url . '/style.css">', 0);
?>

<!-- 게시판 목록 시작 { -->
<div id="bo_list" style="width:100%">

    <style>
        .tour-card-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            padding: 0;
            margin: 0 0 20px 0;
            list-style: none;
        }

        .tour-card-item {
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .tour-card-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .tour-card-img {
            width: 100%;
            height: 200px;
            background: #f4f4f4;
            position: relative;
            overflow: hidden;
            display: block;
        }

        .tour-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .tour-card-body {
            padding: 20px;
        }

        .tour-card-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            display: block;
            text-decoration: none;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .tour-card-info {
            margin-bottom: 15px;
        }

        .tour-card-info>div {
            font-size: 13px;
            color: #666;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tour-card-info i {
            width: 16px;
            text-align: center;
        }

        .tour-card-info a {
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }

        .tour-card-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }

        .tour-card-tags span {
            display: inline-block;
            font-size: 11px;
            background: #f1f1f1;
            color: #555;
            padding: 3px 6px;
            border-radius: 12px;
        }
    </style>

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

            <!-- AI 검색 영역 -->
            <div class="ai-search-box" style="margin-bottom: 20px; background: #fff; padding: 15px; border-radius: 8px; border: 1px solid #007bff; display:flex; gap:10px; align-items:center;">
                <i class="fa fa-magic" style="color:#007bff; font-size:20px;"></i>
                <input type="text" id="ai_search_text" placeholder="예: 가족들과 갈만한 수영장 있는 글램핑장 찾아줘" style="flex:1; border:none; outline:none; font-size:15px; background:transparent;">
                <button type="button" id="btn_ai_search" style="background:#007bff; color:#fff; border:none; padding:8px 15px; border-radius:4px; cursor:pointer; font-weight:bold; white-space:nowrap;">AI 추천 검색</button>
            </div>

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

            document.addEventListener('DOMContentLoaded', function() {
                var btnAiSearch = document.getElementById('btn_ai_search');
                var txtAiSearch = document.getElementById('ai_search_text');

                if (btnAiSearch) {
                    btnAiSearch.addEventListener('click', function() {
                        var text = txtAiSearch.value.trim();
                        if (!text) {
                            alert('원하시는 여행 조건을 입력해주세요.');
                            txtAiSearch.focus();
                            return;
                        }

                        var btn = this;
                        var originalText = btn.innerHTML;
                        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> 분석 중...';
                        btn.disabled = true;

                        var formData = new URLSearchParams();
                        formData.append('query', text);
                        formData.append('bo_table', '<?php echo $bo_table ?>');

                        fetch('<?php echo $board_skin_url ?>/ajax.ai_search.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: formData.toString()
                            })
                            .then(function(response) {
                                return response.json();
                            })
                            .then(function(data) {
                                btn.innerHTML = originalText;
                                btn.disabled = false;

                                if (data.success) {
                                    // 체크박스 모두 해제
                                    var chks = document.querySelectorAll('.filter_tag input[type="checkbox"]');
                                    chks.forEach(function(chk) {
                                        chk.checked = false;
                                        toggle_filter_tag(chk);
                                    });

                                    // 전달받은 태그 체크
                                    var found = 0;
                                    data.tags.forEach(function(tagKey) {
                                        var chk = document.querySelector('.filter_tag input[name="' + tagKey + '"]');
                                        if (chk) {
                                            chk.checked = true;
                                            toggle_filter_tag(chk);
                                            found++;
                                        }
                                    });

                                    if (found > 0) {
                                        fsearch_submit(document.fsearch);
                                        document.fsearch.submit();
                                    } else {
                                        alert('입력하신 조건과 완전히 일치하는 태그를 찾지 못했습니다. 직접 선택하여 검색해보시겠어요?');
                                    }
                                } else {
                                    alert(data.message || 'AI 분석 중 오류가 발생했습니다.');
                                }
                            })
                            .catch(function(error) {
                                btn.innerHTML = originalText;
                                btn.disabled = false;
                                alert('통신 오류가 발생했습니다.');
                                console.error(error);
                            });
                    });

                    // 엔터키 입력 처리
                    txtAiSearch.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            btnAiSearch.click();
                        }
                    });
                }
            });

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
            <?php echo $total_page ?> 페이지
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
        <!-- list content (Card Layout) -->
        <ul class="tour-card-list">
            <?php
            for ($i = 0; $i < count($list); $i++) {
                // 커스텀: 외부 링크(홈페이지) 주소 세팅
                $target_link = $list[$i]['href'];
                $target_blank = "";
                $hp_str = trim($list[$i]['wr_homepage']);
                if (!empty($hp_str)) {
                    $target_link = $hp_str;
                    if (!preg_match("/^(http|https):\/\//", $target_link)) {
                        $target_link = "http://" . $target_link;
                    }
                    $target_blank = 'target="_blank"';
                }

                // 커스텀: g5_board_file에서 다이렉트로 사진 2개 가져와서 초소형 썸네일 생성
                $sql_imgs = "SELECT bf_file FROM {$g5['board_file_table']} WHERE bo_table = '{$board['bo_table']}' AND wr_id = '{$list[$i]['wr_id']}' AND bf_file != '' ORDER BY bf_no ASC LIMIT 2";
                $res_imgs = sql_query($sql_imgs);
                $imgs_arr = array();
                while ($row_img = sql_fetch_array($res_imgs)) {
                    $filename = $row_img['bf_file'];
                    $filepath = G5_DATA_PATH . '/file/' . $board['bo_table'];

                    // 썸네일 생성 (가로 600px, 세로 400px - 리스트에 맞게)
                    $tname = thumbnail($filename, $filepath, $filepath, 600, 400, false, true);

                    if ($tname) {
                        $imgs_arr[] = G5_DATA_URL . '/file/' . $board['bo_table'] . '/' . $tname;
                    } else {
                        // 썸네일 생성 실패 시 원본 사용
                        $imgs_arr[] = G5_DATA_URL . '/file/' . $board['bo_table'] . '/' . $filename;
                    }
                }
                $img_count = count($imgs_arr);
            ?>
                <li class="tour-card-item <?php if ($list[$i]['is_notice']) echo "bo_notice"; ?>">
                    <a href="<?php echo $target_link ?>" <?php echo $target_blank ?> class="tour-card-img" style="position:relative; display:block; overflow:hidden;">
                        <?php
                        if ($img_count == 0) {
                            echo '<img src="' . $board_skin_url . '/img/default_pension.png" alt="기본 펜션 이미지" style="position:absolute; top:0; left:0; width:100%; height:100%; object-fit:cover;">';
                        } else if ($img_count == 1) {
                            echo '<img src="' . $imgs_arr[0] . '" alt="' . $list[$i]['subject'] . '" style="position:absolute; top:0; left:0; width:100%; height:100%; object-fit:cover;">';
                        } else {
                            // 2장일 경우 겹쳐놓고 슬라이드
                            echo '<img src="' . $imgs_arr[0] . '" alt="' . $list[$i]['subject'] . '" class="slider-img active-img" style="position:absolute; top:0; left:0; width:100%; height:100%; object-fit:cover; opacity:1; transition: opacity 1s ease-in-out;">';
                            echo '<img src="' . $imgs_arr[1] . '" alt="' . $list[$i]['subject'] . '" class="slider-img" style="position:absolute; top:0; left:0; width:100%; height:100%; object-fit:cover; opacity:0; transition: opacity 1s ease-in-out;">';
                        }
                        ?>

                        <?php if ($list[$i]['is_notice']) { ?>
                            <span style="position:absolute; top:10px; left:10px; background:#e53935; color:#fff; padding:3px 8px; font-size:12px; border-radius:4px; font-weight:bold;">공지</span>
                        <?php } ?>
                    </a>

                    <div class="tour-card-body">
                        <?php if ($is_checkbox) { ?>
                            <div style="margin-bottom:10px;">
                                <input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
                                <label for="chk_wr_id_<?php echo $i ?>" class="sound_only"><?php echo $list[$i]['subject'] ?></label>
                            </div>
                        <?php } ?>

                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                            <a href="<?php echo $target_link ?>" <?php echo $target_blank ?> class="tour-card-title" style="margin-bottom:0; flex:1; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                <?php echo $list[$i]['subject'] ?>
                                <?php if ($list[$i]['comment_cnt']) { ?>
                                    <span style="font-size:13px; color:#e53935; font-weight:normal;">(<?php echo $list[$i]['comment_cnt']; ?>)</span>
                                <?php } ?>
                            </a>
                            <a href="<?php echo $list[$i]['href'] ?>" style="display:inline-block; font-size:11px; padding:3px 8px; background:#007bff; color:#fff; border-radius:4px; text-decoration:none; margin-left:8px; white-space:nowrap;"><i class="fa fa-info-circle"></i> 수정/확인</a>
                        </div>

                        <div class="tour-card-info">
                            <?php
                            $hp_str = trim($list[$i]['wr_homepage']);
                            $link2_str = trim($list[$i]['wr_link2']);
                            if (strlen($hp_str) <= 10 && $link2_str) {
                                echo '<div><i class="fa fa-home" style="color:#007bff;"></i> <a href="' . $link2_str . '" target="_blank" style="color:#666; text-decoration:none;">단양군청 제휴정보 ' . $link2_str . '</a></div>';
                            } else if ($hp_str) {
                                echo '<div><i class="fa fa-home" style="color:#007bff;"></i> <a href="' . $hp_str . '" target="_blank" style="color:#666; text-decoration:none;">' . $hp_str . '</a></div>';
                            }
                            if ($list[$i]['wr_2']) {
                                echo '<div><i class="fa fa-phone" style="color:#28a745;"></i> ' . $list[$i]['wr_2'] . '</div>';
                            }
                            if ($list[$i]['wr_3']) {
                                echo '<div><i class="fa fa-map-marker" style="color:#dc3545;"></i> ' . $list[$i]['wr_3'] . '</div>';
                            }
                            ?>
                        </div>

                        <div class="tour-card-tags">
                            <?php
                            foreach ($all_filters as $key => $val) {
                                $label = is_array($val) ? $val['label'] : $val;
                                if (strpos($list[$i]['wr_1'], $key) !== false) {
                                    echo '<span>#' . $label . '</span>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                </li>
            <?php } ?>
            <?php if (count($list) == 0) { ?>
                <li style="grid-column: 1 / -1; text-align:center; padding:50px 0; color:#999; border:1px solid #eee; border-radius:12px; background:#fff;">게시물이 없습니다.</li>
            <?php } ?>
        </ul>

        <!-- 페이지 -->
        <?php echo $write_pages; ?>
        <!-- 페이지 -->

    </form>
</div>
<script>
    // [CUSTOM] 리스트 페이지 대표 사진 슬라이더 (5초 간격 크로스 페이드)
    document.addEventListener("DOMContentLoaded", function() {
        setInterval(function() {
            var cardImgs = document.querySelectorAll('.tour-card-img');
            cardImgs.forEach(function(container) {
                var imgs = container.querySelectorAll('.slider-img');
                if (imgs.length === 2) {
                    if (imgs[0].style.opacity == "1") {
                        imgs[0].style.opacity = "0";
                        imgs[1].style.opacity = "1";
                    } else {
                        imgs[0].style.opacity = "1";
                        imgs[1].style.opacity = "0";
                    }
                }
            });
        }, 5000);
    });
</script>
<!-- } 게시판 목록 끝 -->