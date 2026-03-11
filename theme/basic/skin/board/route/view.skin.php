<?php
if (!defined('_GNUBOARD_')) exit;

// filter_config.php 먼저 로드 (태그 라벨·카테고리 참조)
if (!isset($tour_filters)) {
    include_once(G5_THEME_PATH . '/filter_config.php');
}

// wr_1 태그 파싱
$related_tags = array_values(array_filter(array_map('trim', explode(' ', $write['wr_1'] ?? ''))));

// 각 태그가 어느 카테고리에 속하는지 매핑 빌드
// tag_key → cat_key (stay/food/spot/etc)
$tag_to_cat = array();
foreach ($tour_filters as $cat_key => $category) {
    foreach ($category['items'] as $tag_key => $tag_val) {
        $tag_to_cat[$tag_key] = $cat_key;
    }
}

// 카테고리별 tour 목록 URL 파라미터 정의
$cat_url_params = array(
    'stay' => 'stay=1',
    'food' => 'food=1',
    'spot' => 'spot=1&play=1',
    'etc'  => 'etc=1',
);
// 카테고리 표시명 + 아이콘
$cat_display = array(
    'stay' => array('label' => '추천 숙소',    'icon' => '🏠', 'class' => 'btn-cat-stay'),
    'food' => array('label' => '추천 맛집',    'icon' => '🍽️', 'class' => 'btn-cat-food'),
    'spot' => array('label' => '추천 놀거리',  'icon' => '🎢', 'class' => 'btn-cat-spot'),
    'etc'  => array('label' => '추천 구매/이동','icon' => '🛍️', 'class' => 'btn-cat-etc'),
);

// 선택된 태그를 카테고리별로 그룹핑
$tags_by_cat = array('stay' => array(), 'food' => array(), 'spot' => array(), 'etc' => array());
foreach ($related_tags as $tag) {
    $cat = isset($tag_to_cat[$tag]) ? $tag_to_cat[$tag] : null;
    if ($cat && isset($tags_by_cat[$cat])) {
        $tags_by_cat[$cat][] = $tag;
    }
}

// 카테고리별 검색 URL 생성 (stx에 태그 키 공백구분으로 넣어야 즉시 결과 표시)
$cat_search_urls = array();
foreach ($tags_by_cat as $cat_key => $tags) {
    if (empty($tags)) continue;
    $stx = implode(' ', $tags); // 공백구분 태그키 → Gnuboard wr_1 AND 검색
    $params = 'bo_table=tour&sop=and&sfl=wr_1&stx=' . urlencode($stx) . '&' . $cat_url_params[$cat_key];
    foreach ($tags as $tag) {
        $params .= '&' . urlencode($tag) . '=1'; // 체크박스 UI 복원용
    }
    $cat_search_urls[$cat_key] = G5_BBS_URL . '/board.php?' . $params;
}
?>
<style>
.route-view-wrap {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px 16px 80px;
    font-family: 'Apple SD Gothic Neo', 'Noto Sans KR', sans-serif;
}
.route-view-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #555;
    text-decoration: none;
    font-size: 0.9rem;
    margin-bottom: 24px;
    padding: 8px 16px;
    background: #f5f5f5;
    border-radius: 8px;
    transition: background .2s;
}
.route-view-back:hover { background: #e8e8e8; }
.route-view-header { margin-bottom: 32px; }
.route-view-header h1 {
    font-size: 1.7rem;
    font-weight: 700;
    color: #1a1a2e;
    line-height: 1.4;
    margin: 0 0 12px;
}
.route-view-meta {
    display: flex;
    gap: 16px;
    color: #999;
    font-size: 0.85rem;
}
.route-view-body { color: #333; font-size: 1rem; line-height: 1.9; }
.route-view-body h2 {
    font-size: 1.3rem; font-weight: 700; color: #11998e;
    margin: 32px 0 12px; padding-bottom: 8px;
    border-bottom: 2px solid #e8f5e9;
}
.route-view-body h3 { font-size: 1.1rem; font-weight: 700; color: #333; margin: 24px 0 8px; }
.route-view-body h4 { font-size: 1rem; font-weight: 700; color: #555; margin: 16px 0 6px; }
.route-view-body ul, .route-view-body ol { padding-left: 20px; margin: 8px 0 16px; }
.route-view-body li { margin-bottom: 8px; }
.route-view-body p { margin: 0 0 16px; }
.route-view-body strong { color: #1a1a2e; }

/* 수정/삭제 버튼 */
.route-admin-btns {
    display: flex; gap: 8px; justify-content: flex-end;
    margin-top: 20px; padding-top: 16px;
    border-top: 1px solid #f0f0f0;
}
.route-admin-btns a {
    padding: 7px 16px; border-radius: 7px; text-decoration: none;
    font-size: 0.82rem; font-weight: 600;
    display: inline-flex; align-items: center; gap: 5px;
    transition: opacity .2s;
}
.route-admin-btns a:hover { opacity: 0.8; }
.btn-edit { background: #fff3e0; color: #e65100; border: 1px solid #ffcc80; }
.btn-del  { background: #fce4ec; color: #c62828; border: 1px solid #f48fb1; }

/* 연관 태그 박스 */
.route-related-tags {
    margin-top: 36px; padding: 20px;
    background: #f0faf8; border-radius: 12px;
    border-left: 4px solid #11998e;
}
.route-related-tags-title {
    font-size: 0.9rem; font-weight: 700; color: #11998e; margin: 0 0 12px;
}
.route-tag-cat-group { margin-bottom: 12px; }
.route-tag-cat-group:last-child { margin-bottom: 0; }
.route-tag-cat-label {
    font-size: 0.75rem; font-weight: 700; color: #888;
    text-transform: uppercase; letter-spacing: 0.3px;
    margin-bottom: 6px;
}
.route-tag-badges { display: flex; flex-wrap: wrap; gap: 6px; }
.route-tag-badge {
    display: inline-block; background: #fff;
    border: 1px solid #b2dfdb; color: #00796b;
    font-size: 0.82rem; font-weight: 600;
    padding: 4px 12px; border-radius: 20px;
}

/* 추천 업소 카테고리별 버튼 */
.route-shop-btns {
    margin-top: 20px;
    display: flex; flex-wrap: wrap; gap: 8px;
}
.route-shop-btns a {
    padding: 10px 18px; border-radius: 10px;
    text-decoration: none; font-size: 0.85rem; font-weight: 700;
    display: inline-flex; align-items: center; gap: 6px;
    transition: opacity .2s;
}
.route-shop-btns a:hover { opacity: 0.82; }
.btn-cat-stay { background: #3f51b5; color: #fff; }
.btn-cat-food { background: #e53935; color: #fff; }
.btn-cat-spot { background: #fb8c00; color: #fff; }
.btn-cat-etc  { background: #8e24aa; color: #fff; }

/* 하단 네비 버튼 */
.route-view-nav {
    margin-top: 36px; display: flex; gap: 10px; flex-wrap: wrap;
}
.route-view-nav a {
    flex: 1; min-width: 120px; text-align: center;
    padding: 13px 10px; border-radius: 10px;
    text-decoration: none; font-size: 0.88rem; font-weight: 700;
    transition: background .2s, transform .1s;
}
.route-view-nav a:active { transform: scale(0.97); }
.btn-list { background: #f0f0f0; color: #333; }
.btn-list:hover { background: #ddd; }
.btn-tour { background: #11998e; color: #fff; }
.btn-tour:hover { background: #0d7a70; }
@media (max-width: 600px) {
    .route-view-header h1 { font-size: 1.3rem; }
    .route-view-nav a { font-size: 0.82rem; padding: 11px 8px; }
}
</style>

<div class="route-view-wrap">
    <a href="<?php echo G5_BBS_URL; ?>/board.php?bo_table=<?php echo $board['bo_table']; ?>" class="route-view-back">
        ← 목록으로
    </a>

    <div class="route-view-header">
        <h1><?php echo htmlspecialchars($write['wr_subject']); ?></h1>
        <div class="route-view-meta">
            <span>📅 <?php echo substr($write['wr_datetime'], 0, 10); ?></span>
            <span>👁️ 조회 <?php echo number_format($write['wr_hit']); ?></span>
        </div>
    </div>

    <div class="route-view-body">
        <?php echo $write['wr_content']; ?>
    </div>

    <?php if ($update_href || $delete_href): ?>
    <div class="route-admin-btns">
        <?php if ($update_href): ?><a href="<?php echo $update_href; ?>" class="btn-edit">✏️ 수정</a><?php endif; ?>
        <?php if ($delete_href): ?><a href="<?php echo $delete_href; ?>" class="btn-del" onclick="del(this.href); return false;">🗑️ 삭제</a><?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($related_tags)): ?>
    <div class="route-related-tags">
        <div class="route-related-tags-title">🏷️ 이 루트의 추천 업소 태그</div>

        <?php foreach ($cat_display as $cat_key => $cat_info):
            if (empty($tags_by_cat[$cat_key])) continue;
        ?>
        <div class="route-tag-cat-group">
            <div class="route-tag-cat-label"><?php echo $cat_info['icon']; ?> <?php echo $cat_info['label']; ?></div>
            <div class="route-tag-badges">
                <?php foreach ($tags_by_cat[$cat_key] as $tag):
                    $tag_label = isset($all_filters[$tag]) ? $all_filters[$tag] : $tag;
                ?>
                    <span class="route-tag-badge"><?php echo htmlspecialchars($tag_label); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (!empty($cat_search_urls)): ?>
        <div class="route-shop-btns">
            <?php foreach ($cat_search_urls as $cat_key => $url): ?>
                <a href="<?php echo $url; ?>" class="<?php echo $cat_display[$cat_key]['class']; ?>">
                    <?php echo $cat_display[$cat_key]['icon']; ?> <?php echo $cat_display[$cat_key]['label']; ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="route-view-nav">
        <a href="<?php echo G5_BBS_URL; ?>/board.php?bo_table=<?php echo $board['bo_table']; ?>" class="btn-list">📋 추천 루트 목록</a>
        <a href="<?php echo G5_BBS_URL; ?>/board.php?bo_table=tour" class="btn-tour">🗺️ 단양 관광지 보기</a>
    </div>
</div>
