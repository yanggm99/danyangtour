<?php
if (!defined('_GNUBOARD_')) exit;

include_once(G5_THEME_PATH . '/filter_config.php');

// 기존 wr_1 값 파싱 (수정 시 체크박스 복원용)
$saved_tags = array();
if ($w == 'u' && !empty($write['wr_1'])) {
    $saved_tags = array_filter(array_map('trim', explode(' ', $write['wr_1'])));
    $saved_tags = array_flip($saved_tags); // key로 빠른 조회
}
?>
<style>
.rw-wrap {
    max-width: 860px;
    margin: 0 auto;
    padding: 20px 16px 60px;
    font-family: 'Apple SD Gothic Neo', 'Noto Sans KR', sans-serif;
}
.rw-section {
    background: #fff;
    border: 1px solid #e8e8e8;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 20px;
}
.rw-section-title {
    font-size: 1rem;
    font-weight: 700;
    color: #1a1a2e;
    margin: 0 0 16px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.rw-section-title i { color: #11998e; }
.rw-field {
    margin-bottom: 16px;
}
.rw-label {
    display: block;
    font-size: 0.85rem;
    font-weight: 700;
    color: #555;
    margin-bottom: 6px;
}
.rw-input {
    width: 100%;
    height: 42px;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 0 12px;
    font-size: 0.95rem;
    box-sizing: border-box;
    transition: border-color .2s;
}
.rw-input:focus { outline: none; border-color: #11998e; }
textarea.rw-input {
    height: 300px;
    padding: 12px;
    resize: vertical;
    line-height: 1.7;
}

/* 태그 피커 */
.tag-picker-wrap {
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.tag-cat-block {}
.tag-cat-title {
    font-size: 0.82rem;
    font-weight: 700;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0 0 8px;
}
.tag-items {
    display: flex;
    flex-wrap: wrap;
    gap: 7px;
}
.tag-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    border: 1.5px solid #ddd;
    border-radius: 20px;
    font-size: 0.82rem;
    color: #555;
    cursor: pointer;
    user-select: none;
    transition: all .15s;
    background: #fff;
}
.tag-chip input[type=checkbox] {
    display: none;
}
.tag-chip:hover {
    border-color: #11998e;
    color: #11998e;
    background: #f0faf8;
}
.tag-chip.selected {
    border-color: #11998e;
    background: #11998e;
    color: #fff;
}
/* 선택된 태그 요약 */
.tag-selected-summary {
    margin-top: 14px;
    padding: 12px 14px;
    background: #f0faf8;
    border-radius: 8px;
    font-size: 0.82rem;
    color: #555;
    min-height: 42px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
}
.tag-selected-summary .summary-label {
    font-weight: 700;
    color: #11998e;
    white-space: nowrap;
}
.tag-selected-pill {
    background: #11998e;
    color: #fff;
    padding: 2px 10px;
    border-radius: 12px;
    font-size: 0.78rem;
}
.tag-selected-empty { color: #aaa; font-style: italic; }

/* 버튼 */
.rw-btn-wrap {
    display: flex;
    gap: 12px;
    justify-content: center;
    margin-top: 28px;
}
.rw-btn {
    padding: 13px 36px;
    border-radius: 10px;
    font-size: 0.95rem;
    font-weight: 700;
    border: none;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: opacity .2s;
}
.rw-btn:hover { opacity: 0.85; }
.rw-btn-cancel { background: #f0f0f0; color: #555; }
.rw-btn-submit { background: #11998e; color: #fff; }

@media (max-width: 600px) {
    .rw-section { padding: 16px; }
    .rw-btn { padding: 12px 20px; }
}
</style>

<div class="rw-wrap">
<section id="bo_w">
<form name="fwrite" id="fwrite" action="<?php echo $action_url ?>" onsubmit="return fwrite_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
    <input type="hidden" name="uid"      value="<?php echo $uid ?>">
    <input type="hidden" name="w"        value="<?php echo $w ?>">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="wr_id"    value="<?php echo $wr_id ?>">
    <input type="hidden" name="sca"      value="<?php echo $sca ?>">
    <input type="hidden" name="sfl"      value="<?php echo $sfl ?>">
    <input type="hidden" name="stx"      value="<?php echo $stx ?>">
    <input type="hidden" name="spt"      value="<?php echo $spt ?>">
    <input type="hidden" name="sst"      value="<?php echo $sst ?>">
    <input type="hidden" name="sod"      value="<?php echo $sod ?>">
    <input type="hidden" name="page"     value="<?php echo $page ?>">
    <?php if ($is_dhtml_editor): ?>
        <input type="hidden" value="html1" name="html">
    <?php endif; ?>
    <?php if ($is_notice): ?>
        <input type="checkbox" id="notice" name="notice" value="1" <?php echo $notice_checked ?> style="margin-right:4px;"><label for="notice">공지</label>
    <?php endif; ?>

    <!-- 제목 -->
    <div class="rw-section">
        <div class="rw-section-title"><i class="fa fa-pencil"></i> 루트 제목</div>
        <div class="rw-field">
            <input type="text" name="wr_subject" id="wr_subject" value="<?php echo $subject ?>"
                   class="rw-input required" required placeholder="여행 루트 제목을 입력하세요 (예: 단양 핵심 1박2일 코스)">
        </div>
    </div>

    <!-- 본문 -->
    <div class="rw-section">
        <div class="rw-section-title"><i class="fa fa-align-left"></i> 루트 내용</div>
        <div class="rw-field">
            <?php echo $editor_html; // 에디터 또는 textarea ?>
            <?php if (!$is_dhtml_editor): ?>
            <textarea name="wr_content" id="wr_content" class="rw-input" placeholder="여행 루트에 대한 상세 내용을 입력하세요"><?php echo $content ?></textarea>
            <?php endif; ?>
        </div>
    </div>

    <!-- 연관 태그 피커 -->
    <div class="rw-section">
        <div class="rw-section-title"><i class="fa fa-tags"></i> 추천 업소 태그 선택</div>
        <p style="font-size:0.85rem; color:#888; margin:0 0 16px;">이 루트와 연관된 업소 태그를 선택하세요. 선택한 태그로 추천 업소를 검색할 수 있습니다.</p>

        <!-- wr_1에 실제로 저장될 hidden input -->
        <input type="hidden" name="wr_1" id="wr_1_hidden" value="<?php echo htmlspecialchars($write['wr_1'] ?? ''); ?>">

        <div class="tag-picker-wrap">
        <?php foreach ($tour_filters as $cat_key => $category):
            $cat_label_raw = $category['label'];
            // "(괄호)" 제거
            $cat_label = preg_replace('/\s*\(.*?\)/', '', $cat_label_raw);
        ?>
            <div class="tag-cat-block">
                <div class="tag-cat-title"><?php echo htmlspecialchars($cat_label); ?></div>
                <div class="tag-items">
                <?php foreach ($category['items'] as $tag_key => $tag_val):
                    $label = is_array($tag_val) ? $tag_val['label'] : $tag_val;
                    $checked = isset($saved_tags[$tag_key]);
                ?>
                    <label class="tag-chip <?php echo $checked ? 'selected' : ''; ?>"
                           data-key="<?php echo htmlspecialchars($tag_key); ?>">
                        <input type="checkbox" value="<?php echo htmlspecialchars($tag_key); ?>"
                               <?php echo $checked ? 'checked' : ''; ?>>
                        <?php echo htmlspecialchars($label); ?>
                    </label>
                <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
        </div>

        <!-- 선택된 태그 요약 -->
        <div class="tag-selected-summary" id="tag_summary">
            <span class="summary-label">선택된 태그:</span>
            <span class="tag-selected-empty" id="tag_empty_msg">아직 선택된 태그가 없습니다.</span>
        </div>
    </div>

    <!-- 버튼 -->
    <div class="rw-btn-wrap">
        <a href="<?php echo $list_href ?>" class="rw-btn rw-btn-cancel">취소</a>
        <button type="submit" id="btn_submit" class="rw-btn rw-btn-submit">
            <i class="fa fa-check"></i> 작성완료
        </button>
    </div>
</form>
</section>
</div>

<script>
(function() {
    var chips    = document.querySelectorAll('.tag-chip');
    var hidden   = document.getElementById('wr_1_hidden');
    var summary  = document.getElementById('tag_summary');
    var emptyMsg = document.getElementById('tag_empty_msg');

    function updateHidden() {
        var keys = [];
        chips.forEach(function(chip) {
            var cb = chip.querySelector('input[type=checkbox]');
            if (cb && cb.checked) keys.push(cb.value);
        });
        hidden.value = keys.join(' ');

        // 요약 갱신
        // 기존 pill 제거
        summary.querySelectorAll('.tag-selected-pill').forEach(function(el) { el.remove(); });

        if (keys.length === 0) {
            emptyMsg.style.display = '';
        } else {
            emptyMsg.style.display = 'none';
            keys.forEach(function(k) {
                var chip = document.querySelector('.tag-chip[data-key="' + k + '"]');
                var label = chip ? chip.textContent.trim() : k;
                var pill = document.createElement('span');
                pill.className = 'tag-selected-pill';
                pill.textContent = label;
                summary.appendChild(pill);
            });
        }
    }

    chips.forEach(function(chip) {
        // label 기본 동작(checkbox 자동 토글)을 막고 JS에서만 처리
        chip.addEventListener('click', function(e) {
            e.preventDefault();
            var cb = chip.querySelector('input[type=checkbox]');
            cb.checked = !cb.checked;
            chip.classList.toggle('selected', cb.checked);
            updateHidden();
        });
    });

    // 초기 요약 렌더링 (수정 시)
    updateHidden();
})();

function fwrite_submit(f) {
    <?php echo $editor_js; ?>

    if (!f.wr_subject.value.trim()) {
        alert('제목을 입력해주세요.');
        f.wr_subject.focus();
        return false;
    }

    document.getElementById('btn_submit').disabled = true;
    return true;
}
</script>
