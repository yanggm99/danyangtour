<?php
if (!defined('_GNUBOARD_')) exit;

$board_skin_url = get_skin_url($board['bo_skin'], 'board');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
.route-wrap {
    max-width: 960px;
    margin: 0 auto;
    padding: 20px 16px 60px;
    font-family: 'Apple SD Gothic Neo', 'Noto Sans KR', sans-serif;
}
.route-header {
    text-align: center;
    padding: 30px 0 40px;
}
.route-header h1 {
    font-size: 2rem;
    font-weight: 700;
    color: #1a1a2e;
    margin: 0 0 10px;
}
.route-header p {
    color: #666;
    font-size: 1rem;
    margin: 0;
}
.route-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 24px;
}
.route-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
    display: block;
}
.route-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}
.route-card-thumb {
    width: 100%;
    height: 180px;
    object-fit: cover;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}
.route-card-thumb-default {
    width: 100%;
    height: 180px;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
}
.route-card-body {
    padding: 20px;
}
.route-card-num {
    display: inline-block;
    background: #e8f5e9;
    color: #2e7d32;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    margin-bottom: 10px;
}
.route-card-title {
    font-size: 1rem;
    font-weight: 700;
    color: #1a1a2e;
    line-height: 1.5;
    margin: 0 0 10px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.route-card-excerpt {
    font-size: 0.85rem;
    color: #777;
    line-height: 1.6;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.route-card-footer {
    padding: 12px 20px;
    border-top: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8rem;
    color: #999;
}
@media (max-width: 600px) {
    .route-header h1 { font-size: 1.5rem; }
    .route-grid { grid-template-columns: 1fr; }
}
</style>
</head>
<body>
<div class="route-wrap">
    <div class="route-header">
        <h1>단양 여행 추천 루트</h1>
        <p>단양 여행을 더 알차게, 테마별 추천 여행 코스를 만나보세요.</p>
    </div>

    <div class="route-grid">
    <?php
    $num = 0;
    $emojis = ['🏔️','🎢','👨‍👩‍👧','♨️','🗺️','🍽️','🥾','🚗','💑','🍂'];
    foreach ($list as $i => $row) :
        if ($row['wr_is_comment']) continue;
        $num++;
        $title = $row['wr_subject'];
        // 본문 첫 단락에서 텍스트만 추출해 excerpt 생성
        $excerpt = strip_tags($row['wr_content']);
        $excerpt = preg_replace('/\s+/', ' ', $excerpt);
        $excerpt = mb_substr(trim($excerpt), 0, 100);
        $emoji = $emojis[($num - 1) % count($emojis)];
        $view_url = get_pretty_url($board['bo_table'], $row['wr_id']);
    ?>
        <a href="<?php echo $view_url; ?>" class="route-card">
            <div class="route-card-thumb-default"><?php echo $emoji; ?></div>
            <div class="route-card-body">
                <span class="route-card-num">코스 <?php echo $num; ?></span>
                <div class="route-card-title"><?php echo htmlspecialchars($title); ?></div>
                <div class="route-card-excerpt"><?php echo htmlspecialchars($excerpt); ?>...</div>
            </div>
            <div class="route-card-footer">
                <span>📅 <?php echo substr($row['wr_datetime'], 0, 10); ?></span>
            </div>
        </a>
    <?php endforeach; ?>
    </div>
</div>
</body>
</html>
