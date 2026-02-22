<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(dirname(__FILE__) . '/../../../filter_config.php'); // 필터 설정 로드
include_once(G5_LIB_PATH . '/thumbnail.lib.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="' . $board_skin_url . '/style.css">', 0);

// [CUSTOM] 실시간 인기 태그 로깅 및 가져오기 (24시간 / 일주일)
$trending_24h = array();
$trending_7d = array();

// 1. 현재 검색된 태그가 있다면 로그 기록
foreach ($all_filters as $tag_key => $tag_label) {
    if (isset($_GET[$tag_key]) && $_GET[$tag_key] == '1') {
        // 기존 요약 테이블 업데이트 (호환성 유지)
        $sql_sync = "INSERT INTO g5_tour_tag_log (tag_id, hit_count, last_update) 
                VALUES ('$tag_key', 1, '" . G5_TIME_YMDHIS . "') 
                ON DUPLICATE KEY UPDATE hit_count = hit_count + 1, last_update = '" . G5_TIME_YMDHIS . "'";
        sql_query($sql_sync);

        // 새로운 개별 히트 로그 기록
        $sql_hit = "INSERT INTO g5_tour_tag_hit_log (tag_id, hit_time) VALUES ('$tag_key', '" . G5_TIME_YMDHIS . "')";
        sql_query($sql_hit);
    }
}

// 2. 오래된 데이터 자동 삭제 (30일 경과)
sql_query("DELETE FROM g5_tour_tag_hit_log WHERE hit_time < DATE_SUB('" . G5_TIME_YMDHIS . "', INTERVAL 30 DAY)");

// 2. 24시간 인기 태그 가져오기
$sql_24h = "SELECT tag_id, count(*) as cnt FROM g5_tour_tag_hit_log 
            WHERE hit_time >= DATE_SUB('" . G5_TIME_YMDHIS . "', INTERVAL 24 HOUR) 
            GROUP BY tag_id ORDER BY cnt DESC LIMIT 10";
$res_24h = sql_query($sql_24h);
while ($row = sql_fetch_array($res_24h)) {
    if (isset($all_filters[$row['tag_id']])) {
        $trending_24h[] = array('id' => $row['tag_id'], 'label' => $all_filters[$row['tag_id']]);
    }
}

// 3. 일주일 인기 태그 가져오기
$sql_7d = "SELECT tag_id, count(*) as cnt FROM g5_tour_tag_hit_log 
            WHERE hit_time >= DATE_SUB('" . G5_TIME_YMDHIS . "', INTERVAL 7 DAY) 
            GROUP BY tag_id ORDER BY cnt DESC LIMIT 10";
$res_7d = sql_query($sql_7d);
while ($row = sql_fetch_array($res_7d)) {
    if (isset($all_filters[$row['tag_id']])) {
        $trending_7d[] = array('id' => $row['tag_id'], 'label' => $all_filters[$row['tag_id']]);
    }
}
$is_recommend = (isset($_GET['mode']) && $_GET['mode'] == 'recommend');

if ($is_recommend) {
    $g5['title'] = '단양 추천 코스 TOP 5';
}
?>

<!-- 게시판 목록 시작 { -->
<div id="bo_list" style="width:100%">

    <style>
        .recommend-header {
            text-align: center;
            margin: 30px 0 50px 0;
            padding: 40px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .recommend-header h2 {
            font-size: 32px;
            font-weight: 800;
            color: #222;
            margin-bottom: 15px;
            letter-spacing: -1px;
        }

        .recommend-header p {
            font-size: 17px;
            color: #777;
            line-height: 1.6;
            margin: 0;
        }

        .cat-section {
            margin-bottom: 70px;
        }

        .cat-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 26px;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f5f5f5;
        }

        .cat-title i {
            color: #007bff;
            font-size: 24px;
        }

        .cat-title small {
            font-size: 14px;
            font-weight: normal;
            color: #999;
            margin-left: auto;
        }

        /* Rank & Ad Badge */
        .rec-rank {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(33, 37, 41, 0.85);
            color: #fff;
            width: 28px;
            height: 28px;
            line-height: 28px;
            text-align: center;
            border-radius: 50%;
            font-weight: 700;
            font-size: 13px;
            z-index: 5;
        }

        .rec-rank.ad {
            background: #ff4757;
            color: #fff;
            width: auto;
            border-radius: 4px;
            padding: 0 10px;
            font-size: 12px;
            height: 24px;
            line-height: 24px;
            box-shadow: 0 4px 10px rgba(255, 71, 87, 0.3);
        }

        .view-count-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(0, 0, 0, 0.5);
            color: #fff;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 11px;
            z-index: 5;
            backdrop-filter: blur(4px);
        }

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

        /* 인기 태그 스타일 */
        .trending-tags-wrap {
            margin-bottom: 25px;
            padding: 20px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            /* 더 깊은 그림자 */
            border: 1px solid #f0f0f0;
        }

        .trending-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .trending-title {
            font-size: 16px;
            font-weight: 900;
            color: #1a1a1a;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 0;
        }

        .trending-title i {
            color: #ff4757;
            font-size: 18px;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.7;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* 프리미엄 세그먼트 탭 스타일 */
        .trending-tabs {
            display: inline-flex;
            background: #f1f3f5;
            padding: 4px;
            border-radius: 30px;
            position: relative;
            border: 1px solid #e9ecef;
        }

        .trending-tab-btn {
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 700;
            color: #868e96;
            background: none;
            border: none;
            cursor: pointer;
            position: relative;
            z-index: 1;
            transition: color 0.3s ease;
            white-space: nowrap;
        }

        .trending-tab-btn.active {
            color: #007bff;
        }

        /* 활성 탭 배경 슬라이더 효과 */
        .tab-slider {
            position: absolute;
            top: 4px;
            left: 4px;
            height: calc(100% - 8px);
            background: #fff;
            border-radius: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 0;
        }

        .trending-list {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .trending-item {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            border: 1px solid #dee2e6;
            padding: 7px 16px;
            border-radius: 25px;
            font-size: 14px;
            color: #343a40;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .trending-item:hover {
            border-color: #007bff;
            color: #007bff;
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 6px 15px rgba(0, 123, 255, 0.15);
        }

        .trending-rank {
            font-weight: 900;
            color: #007bff;
            font-size: 12px;
            opacity: 0.6;
        }

        .trending-content {
            display: none;
        }

        .trending-content.active {
            display: block;
        }
    </style>

    </style>

    <?php
    $prioritized_key = '';
    if (isset($_GET['stay']) && $_GET['stay'] == '1') $prioritized_key = 'stay';
    elseif (isset($_GET['food']) && $_GET['food'] == '1') $prioritized_key = 'food';
    elseif (isset($_GET['spot']) && $_GET['spot'] == '1') $prioritized_key = 'spot';
    elseif (isset($_GET['etc']) && $_GET['etc'] == '1') $prioritized_key = 'etc';
    ?>

    <?php if (!$is_recommend && $prioritized_key) { ?>
        <?php if (!empty($trending_24h) || !empty($trending_7d)) { ?>
            <!-- 실시간 인기 태그 순위 (프리미엄 탭 버전) -->
            <div class="trending-tags-wrap">
                <div class="trending-header">
                    <h3 class="trending-title">
                        <i class="fa fa-fire"></i> 실시간 인기 태그
                    </h3>
                    <div class="trending-tabs">
                        <div class="tab-slider" id="tab_slider"></div>
                        <button type="button" class="trending-tab-btn active" onclick="switchTrendingTab('24h', this)">지금 뜨는 (24h)</button>
                        <button type="button" class="trending-tab-btn" onclick="switchTrendingTab('7d', this)">이번 주 인기 (7d)</button>
                    </div>
                </div>

                <div id="trending_24h_content" class="trending-content active">
                    <div class="trending-list">
                        <?php if (empty($trending_24h)) {
                            echo "<p style='font-size:13px; color:#adb5bd; padding:20px 0;'>현재 트렌드를 분석 중입니다...</p>";
                        } else {
                            foreach ($trending_24h as $index => $tag) {
                                $rank = $index + 1;
                                $tag_link = G5_BBS_URL . "/board.php?bo_table=" . $bo_table . "&sop=and&sfl=wr_1&stx=&" . $tag['id'] . "=1";
                        ?>
                                <a href="<?php echo $tag_link; ?>" class="trending-item">
                                    <span class="trending-rank"><?php echo $rank; ?></span>
                                    <span class="trending-label"><?php echo $tag['label']; ?></span>
                                </a>
                        <?php }
                        } ?>
                    </div>
                </div>

                <div id="trending_7d_content" class="trending-content">
                    <div class="trending-list">
                        <?php if (empty($trending_7d)) {
                            echo "<p style='font-size:13px; color:#adb5bd; padding:20px 0;'>데이터를 집계 중입니다...</p>";
                        } else {
                            foreach ($trending_7d as $index => $tag) {
                                $rank = $index + 1;
                                $tag_link = G5_BBS_URL . "/board.php?bo_table=" . $bo_table . "&sop=and&sfl=wr_1&stx=&" . $tag['id'] . "=1";
                        ?>
                                <a href="<?php echo $tag_link; ?>" class="trending-item">
                                    <span class="trending-rank"><?php echo $rank; ?></span>
                                    <span class="trending-label"><?php echo $tag['label']; ?></span>
                                </a>
                        <?php }
                        } ?>
                    </div>
                </div>
            </div>

            <script>
                function switchTrendingTab(type, btn) {
                    const btns = document.querySelectorAll('.trending-tab-btn');
                    const contents = document.querySelectorAll('.trending-content');
                    const slider = document.getElementById('tab_slider');

                    btns.forEach(b => b.classList.remove('active'));
                    contents.forEach(content => content.classList.remove('active'));

                    btn.classList.add('active');

                    // 슬라이더 위치 계산
                    slider.style.width = btn.offsetWidth + 'px';
                    slider.style.left = btn.offsetLeft + 'px';

                    if (type === '24h') {
                        document.getElementById('trending_24h_content').classList.add('active');
                    } else {
                        document.getElementById('trending_7d_content').classList.add('active');
                    }
                }

                // 초기 슬라이더 설정
                document.addEventListener('DOMContentLoaded', function() {
                    const activeBtn = document.querySelector('.trending-tab-btn.active');
                    if (activeBtn) {
                        const slider = document.getElementById('tab_slider');
                        slider.style.width = activeBtn.offsetWidth + 'px';
                        slider.style.left = activeBtn.offsetLeft + 'px';
                    }
                });
            </script>
        <?php } ?>


        <!-- [CUSTOM] 필터 검색 영역 -->
        <div class="filter_search_wrap" style="margin-bottom: 20px; border: 1px solid #ddd; padding: 20px; background: #fafafa; border-radius: 8px;">
            <form name="fsearch" method="get" onsubmit="return fsearch_submit(this);">
                <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
                <input type="hidden" name="sca" value="<?php echo $sca ?>">
                <input type="hidden" name="sop" value="and">
                <input type="hidden" name="sfl" value="wr_1">
                <input type="hidden" name="stx" value="">
                <?php if ($prioritized_key) { ?>
                    <input type="hidden" name="<?php echo $prioritized_key; ?>" value="1">
                <?php } ?>

                <h3 style="font-size:18px; margin-bottom:15px; font-weight:bold;">
                    <i class="fa fa-search"></i> 맞춤형 단양 여행 찾기
                </h3>

                <!-- AI 검색 영역 -->
                <div class="ai-search-box" style="margin-bottom: 20px; background: #fff; padding: 15px; border-radius: 8px; border: 1px solid #007bff; display:flex; gap:10px; align-items:center;">
                    <i class="fa fa-magic" style="color:#007bff; font-size:20px;"></i>
                    <input type="text" id="ai_search_text" placeholder="예: 가족들과 갈만한 수영장 있는 글램핑장 찾아줘" style="flex:1; border:none; outline:none; font-size:15px; background:transparent;">
                    <button type="button" id="btn_ai_search" style="background:#007bff; color:#fff; border:none; padding:8px 15px; border-radius:4px; cursor:pointer; font-weight:bold; white-space:nowrap;">AI 추천 검색</button>
                </div>

                <?php
                // 메인 페이지에서 선택된 카테고리를 우선 정렬하기 위한 로직
                $ordered_filters = $tour_filters;

                if ($prioritized_key && isset($ordered_filters[$prioritized_key])) {
                    $temp = array($prioritized_key => $ordered_filters[$prioritized_key]);
                    foreach ($ordered_filters as $k => $v) {
                        if ($k !== $prioritized_key) $temp[$k] = $v;
                    }
                    $ordered_filters = $temp;
                }
                ?>

                <div class="filter_categories">
                    <?php foreach ($ordered_filters as $key => $category) { ?>
                        <div style="margin-bottom: 20px;">
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
    <?php } ?>

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

    <?php if ($is_recommend) { ?>
        <div class="recommend-header">
            <h2>단양 에디터 픽 & 인기 TOP 5</h2>
            <p>엄선된 추천 코스와 여행자들이 가장 많이 찾는 핫플레이스를 카테고리별로 만나보세요.</p>
        </div>

        <?php
        $rec_categories = array(
            'stay' => array('icon' => 'fa-home', 'title' => '자고', 'desc' => '숙박', 'filter_key' => 'stay'),
            'food' => array('icon' => 'fa-cutlery', 'title' => '먹고', 'desc' => '맛집/카페', 'filter_key' => 'food'),
            'spot' => array('icon' => 'fa-paper-plane', 'title' => '보고/놀고/즐기고', 'desc' => '명소/체험', 'filter_key' => 'spot'),
            'etc'  => array('icon' => 'fa-shopping-bag', 'title' => '구매/이동', 'desc' => '쇼핑/편의', 'filter_key' => 'etc')
        );

        foreach ($rec_categories as $cat_key => $cat_info) {
            $sql = " SELECT * FROM {$write_table} WHERE wr_is_comment = 0 AND ca_name = '{$cat_key}' ORDER BY wr_4 DESC, wr_hit DESC LIMIT 5 ";
            $result = sql_query($sql);

            if ($result && sql_num_rows($result) > 0) {
        ?>
                <div class="cat-section">
                    <h3 class="cat-title">
                        <i class="fa <?php echo $cat_info['icon']; ?>"></i> <?php echo $cat_info['title']; ?>
                        <small><?php echo $cat_info['desc']; ?></small>
                    </h3>

                    <ul class="tour-card-list">
                        <?php
                        $rank = 1;
                        while ($row = sql_fetch_array($result)) {
                            $is_ad = ($row['wr_4'] == '1');

                            // 썸네일 생성
                            $thumb_url = '';
                            $sql_img = "SELECT bf_file FROM {$g5['board_file_table']} WHERE bo_table = '{$bo_table}' AND wr_id = '{$row['wr_id']}' AND bf_file != '' ORDER BY bf_no ASC LIMIT 1";
                            $row_img = sql_fetch_array(sql_query($sql_img));
                            if ($row_img && $row_img['bf_file']) {
                                $filename = $row_img['bf_file'];
                                $filepath = G5_DATA_PATH . '/file/' . $bo_table;
                                $tname = thumbnail($filename, $filepath, $filepath, 600, 400, false, true);
                                if ($tname) {
                                    $thumb_url = G5_DATA_URL . '/file/' . $bo_table . '/' . $tname;
                                } else {
                                    $thumb_url = G5_DATA_URL . '/file/' . $bo_table . '/' . $filename;
                                }
                            } else {
                                $thumb_url = $board_skin_url . '/img/default_pension.png';
                            }

                            // 링크 세팅
                            $target_link = "./board.php?bo_table={$bo_table}&wr_id={$row['wr_id']}";
                            $target_blank = "";
                            $hp_str = trim($row['wr_homepage']);
                            if ($hp_str) {
                                $target_link = $hp_str;
                                if (!preg_match("/^(http|https):\/\//", $target_link)) $target_link = "http://" . $target_link;
                                $target_blank = 'target="_blank"';
                            }
                        ?>
                            <li class="tour-card-item">
                                <a href="<?php echo $target_link; ?>" <?php echo $target_blank; ?> class="tour-card-img">
                                    <?php if ($is_ad) { ?>
                                        <span class="rec-rank ad">추천</span>
                                    <?php } else { ?>
                                        <span class="rec-rank"><?php echo $rank; ?></span>
                                    <?php } ?>
                                    <span class="view-count-badge">조회 <?php echo number_format($row['wr_hit']); ?></span>
                                    <img src="<?php echo $thumb_url; ?>" alt="<?php echo get_text($row['wr_subject']); ?>">
                                </a>
                                <div class="tour-card-body">
                                    <a href="<?php echo $target_link; ?>" <?php echo $target_blank; ?> class="tour-card-title">
                                        <?php echo get_text($row['wr_subject']); ?>
                                    </a>
                                    <div class="tour-card-info">
                                        <?php if ($row['wr_2']) { ?>
                                            <div><i class="fa fa-phone" style="color:#28a745;"></i> <?php echo $row['wr_2']; ?></div>
                                        <?php } ?>
                                        <?php if ($row['wr_3']) { ?>
                                            <div><i class="fa fa-map-marker" style="color:#dc3545;"></i> <?php echo $row['wr_3']; ?></div>
                                        <?php } ?>
                                    </div>
                                    <div class="tour-card-tags">
                                        <?php
                                        foreach ($tour_filters[$cat_key]['items'] as $tag_key => $tag_val) {
                                            if (strpos($row['wr_1'], $tag_key) !== false) echo '<span>#' . $tag_val . '</span>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </li>
                        <?php
                            $rank++;
                        }
                        ?>
                    </ul>
                </div>
        <?php
            }
        }
        ?>
    <?php } else { ?>
        <?php if ($prioritized_key && !$stx) { ?>
            <!-- 검색 전 안내 메시지 -->
            <div style="text-align:center; padding:100px 20px; color:#666; background:#fff; border:1px solid #eee; border-radius:12px; margin:20px 0; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                <div style="width:80px; height:80px; background:#f0f7ff; border-radius:50%; display:flex; justify-content:center; align-items:center; margin:0 auto 25px;">
                    <i class="fa fa-mouse-pointer" style="font-size:35px; color:#007bff;"></i>
                </div>
                <h3 style="font-size:22px; font-weight:900; color:#1a1a1a; margin-bottom:12px;">어떤 <?php echo $ordered_filters[$prioritized_key]['label']; ?>을(를) 찾으시나요?</h3>
                <p style="font-size:16px; color:#666; line-height:1.6; max-width:500px; margin:0 auto;">
                    상단의 태그 옵션들을 자유롭게 선택해보세요.<br>
                    '검색하기' 버튼을 누르면 조건에 딱 맞는 곳들을 보여드립니다.
                </p>
            </div>
        <?php } else { ?>

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
                        <li style="grid-column: 1 / -1; text-align:center; padding:80px 20px; color:#666; background:#fff; border:1px solid #eee; border-radius:12px;">
                            <i class="fa fa-exclamation-triangle" style="font-size:30px; color:#adb5bd; margin-bottom:15px; display:block;"></i>
                            <p style="font-size:16px; font-weight:500;">옵션에 맞는 업체가 존재하지 않습니다.<br>다른 옵션을 선택하세요.</p>
                        </li>
                    <?php } ?>
                </ul>

                <!-- 페이지 -->
                <?php echo $write_pages; ?>
            </form>
        <?php } // search check end 
        ?>
    <?php } // is_recommend check end 
    ?>
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