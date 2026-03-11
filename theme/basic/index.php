<?php
if (!defined('_INDEX_')) define('_INDEX_', true);
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (G5_IS_MOBILE) {
    include_once(G5_THEME_MOBILE_PATH . '/index.php');
    return;
}

if (G5_COMMUNITY_USE === false) {
    include_once(G5_THEME_SHOP_PATH . '/index.php');
    return;
}

include_once(G5_THEME_PATH . '/head.php');
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

// 투어 게시판 아이디 (상황에 맞게 수정 필요, 기본값 'tour')
$tour_bo_table = 'tour';
?>

<style>
    /* 메인 전용 프리미엄 스타일 */
    #container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
    }

    #wrapper {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
    }

    .main-hero {
        position: relative;
        height: 550px;
        background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('<?php echo G5_THEME_URL ?>/img/main_hero_dynamic_v6.png') center/cover no-repeat;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: #fff;
        text-align: center;
        padding: 0 20px;
    }

    .hero-content h2 {
        font-size: 48px;
        font-weight: 900;
        text-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        margin-bottom: 10px;
        letter-spacing: -1px;
    }

    .hero-content p {
        font-size: 20px;
        font-weight: 300;
        margin-bottom: 40px;
        opacity: 0.9;
    }

    /* AI 검색 박스 */
    .hero-search-box {
        width: 100%;
        max-width: 700px;
        background: rgba(255, 255, 255, 0.95);
        padding: 10px 15px;
        border-radius: 50px;
        display: flex;
        align-items: center;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
        transition: transform 0.3s ease;
    }

    .hero-search-box:focus-within {
        transform: scale(1.02);
    }

    .hero-search-box i {
        color: #007bff;
        font-size: 20px;
        margin-left: 10px;
    }

    .hero-search-box input {
        flex: 1;
        border: none;
        background: transparent;
        padding: 12px 20px;
        font-size: 17px;
        outline: none;
        color: #333;
    }

    .hero-search-btn {
        background: #007bff;
        color: #fff;
        border: none;
        padding: 12px 30px;
        border-radius: 30px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.2s;
        font-size: 15px;
    }

    .hero-search-btn:hover {
        background: #0056b3;
    }

    /* 카테고리 네비게이션 */
    .category-nav-wrap {
        max-width: 1200px;
        margin: -60px auto 50px;
        position: relative;
        z-index: 10;
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
        padding: 0 20px;
    }

    .category-card {
        background: #fff;
        border-radius: 20px;
        padding: 30px 20px;
        text-align: center;
        text-decoration: none;
        color: #333;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        border: 1px solid #f0f0f0;
    }

    .category-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0, 123, 255, 0.1);
        border-color: #007bff;
    }

    .category-icon {
        width: 60px;
        height: 60px;
        background: #f8f9fa;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto 15px;
        font-size: 24px;
        color: #007bff;
        transition: all 0.3s;
    }

    .category-card:hover .category-icon {
        background: #007bff;
        color: #fff;
    }

    .category-card span {
        font-weight: 800;
        font-size: 16px;
        display: block;
    }

    .category-card small {
        font-size: 12px;
        color: #999;
        margin-top: 5px;
        display: block;
    }

    /* 실시간 인기 태그 섹션 */
    .main-trending-wrap {
        max-width: 1200px;
        margin: 0 auto 60px;
        padding: 0 20px;
    }

    .section-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .section-title h3 {
        font-size: 22px;
        font-weight: 900;
        color: #1a1a1a;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title h3 i {
        color: #ff4757;
        animation: pulse 1.5s infinite;
    }

    .trending-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .trending-pill {
        padding: 8px 18px;
        background: #fff;
        border: 1px solid #eee;
        border-radius: 30px;
        font-size: 14px;
        color: #495057;
        text-decoration: none;
        transition: all 0.2s;
        font-weight: 500;
    }

    .trending-pill:hover {
        border-color: #007bff;
        color: #007bff;
        background: #f0f7ff;
        transform: scale(1.05);
    }

    .rank-tag {
        color: #007bff;
        font-weight: 900;
        margin-right: 5px;
        font-style: italic;
    }

    /* 반응형 */
    @media (max-width: 991px) {
        .category-nav-wrap {
            grid-template-columns: repeat(3, 1fr);
            margin-top: -40px;
        }

        .hero-content h2 {
            font-size: 36px;
        }
    }

    @media (max-width: 767px) {
        .category-nav-wrap {
            grid-template-columns: repeat(2, 1fr);
        }

        .main-hero {
            height: 450px;
        }
    }
</style>

<div class="main-page">
    <!-- 1. 히어로 섹션 -->
    <section class="main-hero">
        <div class="hero-content">
            <h2>더 단양 (The DanYang): 단양의 모든 것</h2>
            <p>자고, 먹고, 보고, 즐기고... 당신이 찾던 완벽한 단양을 만나보세요.</p>

            <form id="main_ai_form" class="hero-search-box" onsubmit="return handleMainSearch(this);">
                <i class="fa fa-magic"></i>
                <input type="text" id="main_ai_query" placeholder="예: 경치 좋은 단양의 펜션을 찾아줘" autocomplete="off">
                <button type="submit" class="hero-search-btn">추천 검색</button>
            </form>
        </div>
    </section>

    <?php
    include_once(G5_THEME_PATH . '/filter_config.php');
    function get_category_display_names($label)
    {
        if (preg_match('/^(.*?)\s*\((.*?)\)$/', $label, $matches)) {
            return array(trim($matches[1]), trim($matches[2]));
        }
        return array($label, '');
    }

    $stay_names = get_category_display_names($tour_filters['stay']['label']);
    $food_names = get_category_display_names($tour_filters['food']['label']);
    $spot_names = get_category_display_names($tour_filters['spot']['label']);
    $etc_names = get_category_display_names($tour_filters['etc']['label']);
    ?>

    <!-- 2. 카테고리 네비게이션 -->
    <nav class="category-nav-wrap">
        <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=route" class="category-card">
            <div class="category-icon"><i class="fa fa-map-signs"></i></div>
            <span>여행루트</span>
            <small>테마별 추천 코스</small>
        </a>
        <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $tour_bo_table ?>&stay=1" class="category-card">
            <div class="category-icon"><i class="fa fa-home"></i></div>
            <span><?php echo $stay_names[0]; ?></span>
            <small><?php echo $stay_names[1]; ?></small>
        </a>
        <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $tour_bo_table ?>&food=1" class="category-card">
            <div class="category-icon"><i class="fa fa-cutlery"></i></div>
            <span><?php echo $food_names[0]; ?></span>
            <small><?php echo $food_names[1]; ?></small>
        </a>
        <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $tour_bo_table ?>&spot=1&play=1" class="category-card">
            <div class="category-icon"><i class="fa fa-paper-plane"></i></div>
            <span><?php echo $spot_names[0]; ?></span>
            <small><?php echo $spot_names[1]; ?></small>
        </a>
        <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $tour_bo_table ?>&etc=1" class="category-card">
            <div class="category-icon"><i class="fa fa-shopping-bag"></i></div>
            <span><?php echo $etc_names[0]; ?></span>
            <small><?php echo $etc_names[1]; ?></small>
        </a>
    </nav>

    <!-- 3. 실시간 인기 태그 -->
    <?php if (!empty($trending_24h)) { ?>
        <section class="main-trending-wrap">
            <div class="section-title">
                <h3><i class="fa fa-fire"></i> 지금 단양에서 가장 인기 있는 태그</h3>
                <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $tour_bo_table ?>" style="font-size:13px; color:#999; text-decoration:none;">전체보기 <i class="fa fa-chevron-right"></i></a>
            </div>
            <div class="trending-pills">
                <?php foreach ($trending_24h as $idx => $tag) {
                    $tag_url = G5_BBS_URL . "/board.php?bo_table=" . $tour_bo_table . "&sop=and&sfl=wr_1&stx=&" . $tag['id'] . "=1";
                ?>
                    <a href="<?php echo $tag_url ?>" class="trending-pill">
                        <span class="rank-tag"><?php echo $idx + 1 ?></span>
                        <?php echo $tag['label'] ?>
                    </a>
                <?php } ?>
            </div>
        </section>
    <?php } ?>

    <!-- 4. 하단 레이아웃 (기본 게시판들 살리기) -->
    <div style="max-width:1200px; margin:0 auto; padding:0 20px 80px;">
        <div class="section-title">
            <h3><i class="fa fa-bullhorn" style="color:#007bff; animation:none;"></i> 공지사항 및 커뮤니티</h3>
        </div>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:30px;">
            <div class="latest-box" style="background:#fff; border-radius:15px; padding:20px; border:1px solid #eee;">
                <?php echo latest('theme/basic', 'notice', 5, 40); ?>
            </div>
            <div class="latest-box" style="background:#fff; border-radius:15px; padding:20px; border:1px solid #eee;">
                <?php echo latest('theme/basic', 'free', 5, 40); ?>
            </div>
        </div>
    </div>
</div>

<script>
    function handleMainSearch(f) {
        const query = document.getElementById('main_ai_query').value.trim();
        if (!query) {
            alert('무엇을 찾고 싶으신지 입력해주세요!');
            return false;
        }

        // 리스트 페이지로 이동하면서 AI 쿼리를 전달하거나 직접 검색 수행
        // 여기서는 자연스러운 흐름을 위해 리스트 페이지의 AI 검색 기능을 활용하도록 유도하거나 직접 action 지정
        location.href = "<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $tour_bo_table ?>&main_search=" + encodeURIComponent(query);
        return false;
    }
</script>

<?php
include_once(G5_THEME_PATH . '/tail.php');
