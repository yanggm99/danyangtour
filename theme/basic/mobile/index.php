<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (G5_COMMUNITY_USE === false) {
    include_once(G5_THEME_MSHOP_PATH . '/index.php');
    return;
}

include_once(G5_THEME_MOBILE_PATH . '/head.php');
include_once(G5_THEME_PATH . '/filter_config.php');

// 실시간 인기 태그 가져오기 (24시간 기준 상위 10개)
$trending_24h = array();
$sql_24h = "SELECT tag_id, count(*) as cnt FROM g5_tour_tag_hit_log 
            WHERE hit_time >= DATE_SUB('" . G5_TIME_YMDHIS . "', INTERVAL 24 HOUR) 
            GROUP BY tag_id ORDER BY cnt DESC LIMIT 10";
$res_24h = sql_query($sql_24h);
while ($row = sql_fetch_array($res_24h)) {
    if (isset($all_filters[$row['tag_id']])) {
        $trending_24h[] = array('id' => $row['tag_id'], 'label' => $all_filters[$row['tag_id']]);
    }
}

// 투어 게시판 아이디
$tour_bo_table = 'tour';
?>

<style>
    /* 모바일 전용 프리미엄 스타일 */
    .mobile-main {
        background: #f8f9fa;
    }

    /* 모바일 히어로 */
    .m-hero {
        position: relative;
        height: 350px;
        background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('<?php echo G5_THEME_URL ?>/img/main_hero_dynamic_v6.png') center/cover no-repeat;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: #fff;
        text-align: center;
        padding: 0 20px;
    }

    .m-hero h2 {
        font-size: 28px;
        font-weight: 900;
        margin-bottom: 8px;
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    }

    .m-hero p {
        font-size: 15px;
        margin-bottom: 25px;
        opacity: 0.9;
        font-weight: 300;
    }

    /* 모바일 AI 검색 */
    .m-search-wrap {
        width: 100%;
        max-width: 320px;
    }

    .m-search-box {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 30px;
        display: flex;
        padding: 5px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .m-search-box input {
        flex: 1;
        border: none;
        background: transparent;
        padding: 10px 15px;
        font-size: 14px;
        outline: none;
    }

    .m-search-btn {
        background: #007bff;
        color: #fff;
        border: none;
        padding: 0 15px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 13px;
    }

    /* 모바일 카테고리 네비 */
    .m-cat-nav {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        padding: 20px 15px;
        margin-top: -30px;
        position: relative;
        z-index: 10;
    }

    .m-cat-item {
        background: #fff;
        border-radius: 12px;
        padding: 15px 5px;
        text-align: center;
        text-decoration: none;
        color: #333;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid #eee;
    }

    .m-cat-item i {
        display: block;
        font-size: 20px;
        color: #007bff;
        margin-bottom: 8px;
    }

    .m-cat-item span {
        font-size: 13px;
        font-weight: 800;
    }

    /* 인기 태그 */
    .m-trending-section {
        padding: 10px 15px 30px;
    }

    .m-sec-title {
        font-size: 16px;
        font-weight: 900;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .m-sec-title i {
        color: #ff4757;
    }

    .m-trending-list {
        display: flex;
        overflow-x: auto;
        gap: 8px;
        padding-bottom: 10px;
        scrollbar-width: none;
        /* Firefox */
    }

    .m-trending-list::-webkit-scrollbar {
        display: none;
        /* Chrome, Safari */
    }

    .m-trending-item {
        white-space: nowrap;
        background: #fff;
        border: 1px solid #eee;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 13px;
        color: #555;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .m-rank {
        color: #007bff;
        font-weight: 900;
        font-style: italic;
        font-size: 12px;
    }

    /* 최신글 모바일 스타일 */
    .m-latest-section {
        padding: 0 15px 40px;
    }

    .m-latest-box {
        background: #fff;
        border-radius: 12px;
        padding: 15px;
        border: 1px solid #eee;
        margin-bottom: 20px;
    }
</style>

<div class="mobile-main">
    <!-- 1. 모바일 히어로 -->
    <section class="m-hero">
        <h2>더 단양 (The DanYang)</h2>
        <p>엄선된 큐레이션으로 만나는 단양</p>

        <div class="m-search-wrap">
            <form class="m-search-box" onsubmit="return handleMobileMainSearch(this);">
                <input type="text" id="m_main_query" placeholder="어떤 여행지를 찾으시나요?">
                <button type="submit" class="m-search-btn">검색</button>
            </form>
        </div>
    </section>

    <?php
    include_once(G5_THEME_PATH . '/filter_config.php');
    function get_m_category_display_names($label)
    {
        if (preg_match('/^(.*?)\s*\((.*?)\)$/', $label, $matches)) {
            return trim($matches[1]);
        }
        return $label;
    }

    $m_stay_name = get_m_category_display_names($tour_filters['stay']['label']);
    $m_food_name = get_m_category_display_names($tour_filters['food']['label']);
    $m_spot_name = get_m_category_display_names($tour_filters['spot']['label']);
    $m_etc_name = get_m_category_display_names($tour_filters['etc']['label']);
    ?>

    <!-- 2. 모바일 카테고리 -->
    <nav class="m-cat-nav">
        <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=route" class="m-cat-item">
            <i class="fa fa-map-signs"></i>
            <span>여행루트</span>
        </a>
        <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $tour_bo_table ?>&stay=1" class="m-cat-item">
            <i class="fa fa-home"></i>
            <span><?php echo $m_stay_name; ?></span>
        </a>
        <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $tour_bo_table ?>&food=1" class="m-cat-item">
            <i class="fa fa-cutlery"></i>
            <span><?php echo $m_food_name; ?></span>
        </a>
        <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $tour_bo_table ?>&spot=1&play=1" class="m-cat-item">
            <i class="fa fa-paper-plane"></i>
            <span><?php echo $m_spot_name; ?></span>
        </a>
        <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $tour_bo_table ?>&etc=1" class="m-cat-item">
            <i class="fa fa-shopping-bag"></i>
            <span><?php echo $m_etc_name; ?></span>
        </a>
        <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $tour_bo_table ?>" class="m-cat-item">
            <i class="fa fa-th-list"></i>
            <span>전체보기</span>
        </a>
    </nav>

    <!-- 3. 인기 태그 (가로 스크롤) -->
    <?php if (!empty($trending_24h)) { ?>
        <section class="m-trending-section">
            <h3 class="m-sec-title"><i class="fa fa-fire"></i> 지금 핫한 검색 태그</h3>
            <div class="m-trending-list">
                <?php foreach ($trending_24h as $idx => $tag) {
                    $tag_url = G5_BBS_URL . "/board.php?bo_table=" . $tour_bo_table . "&sop=and&sfl=wr_1&stx=&" . $tag['id'] . "=1";
                ?>
                    <a href="<?php echo $tag_url ?>" class="m-trending-item">
                        <span class="m-rank"><?php echo $idx + 1 ?></span>
                        <?php echo $tag['label'] ?>
                    </a>
                <?php } ?>
            </div>
        </section>
    <?php } ?>

    <!-- 4. 모바일 최신글 -->
    <section class="m-latest-section">
        <h3 class="m-sec-title"><i class="fa fa-bullhorn"></i> 공지 및 소식</h3>
        <div class="m-latest-box">
            <?php echo latest('theme/basic', 'notice', 5, 30); ?>
        </div>
        <div class="m-latest-box">
            <?php echo latest('theme/basic', 'free', 5, 30); ?>
        </div>
    </section>
</div>

<script>
    function handleMobileMainSearch(f) {
        const query = document.getElementById('m_main_query').value.trim();
        if (!query) {
            alert('검색어를 입력해주세요!');
            return false;
        }
        location.href = "<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $tour_bo_table ?>&main_search=" + encodeURIComponent(query);
        return false;
    }
</script>

<?php
include_once(G5_THEME_MOBILE_PATH . '/tail.php');
