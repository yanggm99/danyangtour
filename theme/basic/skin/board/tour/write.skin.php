<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 필터 설정 로드
include_once($board_skin_path . '/../../filter_config.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="' . $board_skin_url . '/style.css">', 0);
?>

<section id="bo_w">
    <h2 class="sound_only"><?php echo $g5['title'] ?></h2>

    <!-- 게시물 작성/수정 시작 { -->
    <form name="fwrite" id="fwrite" action="<?php echo $action_url ?>" onsubmit="return fwrite_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off" style="width:100%">
        <input type="hidden" name="uid" value="<?php echo $uid ?>">
        <input type="hidden" name="w" value="<?php echo $w ?>">
        <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
        <input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">
        <input type="hidden" name="sca" value="<?php echo $sca ?>">
        <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
        <input type="hidden" name="stx" value="<?php echo $stx ?>">
        <input type="hidden" name="spt" value="<?php echo $spt ?>">
        <input type="hidden" name="sst" value="<?php echo $sst ?>">
        <input type="hidden" name="sod" value="<?php echo $sod ?>">
        <input type="hidden" name="page" value="<?php echo $page ?>">
        <?php
        $option = '';
        $option_hidden = '';
        if ($is_notice || $is_html || $is_secret || $is_mail) {
            $option = '';
            if ($is_notice) {
                $option .= "\n" . '<input type="checkbox" id="notice" name="notice" value="1" ' . $notice_checked . '>' . "\n" . '<label for="notice">공지</label>';
            }
            if ($is_html) {
                if ($is_dhtml_editor) {
                    $option_hidden .= '<input type="hidden" value="html1" name="html">';
                } else {
                    $option .= "\n" . '<input type="checkbox" id="html" name="html" onclick="html_auto_br(this);" value="' . $html_value . '" ' . $html_checked . '>' . "\n" . '<label for="html">HTML</label>';
                }
            }
            if ($is_secret) {
                if ($is_admin || $is_secret == 1) {
                    $option .= "\n" . '<input type="checkbox" id="secret" name="secret" value="secret" ' . $secret_checked . '>' . "\n" . '<label for="secret">비밀글</label>';
                } else {
                    $option_hidden .= '<input type="hidden" name="secret" value="secret">';
                }
            }
            if ($is_mail) {
                $option .= "\n" . '<input type="checkbox" id="mail" name="mail" value="mail" ' . $recv_email_checked . '>' . "\n" . '<label for="mail">답변메일받기</label>';
            }
        }

        echo $option_hidden;
        ?>

        <?php if ($is_category) { ?>
            <div class="bo_w_select write_div">
                <label for="ca_name" class="sound_only">분류<strong>필수</strong></label>
                <select name="ca_name" id="ca_name" required class="frm_input required">
                    <option value="">분류를 선택하세요</option>
                    <?php echo $category_option ?>
                </select>
            </div>
        <?php } ?>

        <div class="bo_w_tit write_div">
            <label for="wr_subject" class="sound_only">제목<strong>필수</strong></label>
            <div id="autosave_wrapper write_div">
                <input type="text" name="wr_subject" value="<?php echo $subject ?>" id="wr_subject" required class="frm_input full_input required" size="50" maxlength="255" placeholder="업체명(제목)을 입력하세요">
            </div>
        </div>

        <!-- [CUSTOM] 상세 옵션 체크박스 영역 -->
        <div class="write_div" style="margin: 20px 0; border: 1px solid #e5e5e5; padding: 20px; background: #fff;">
            <h3 style="margin-bottom: 20px; font-size: 1.2em; border-bottom: 2px solid #333; padding-bottom: 10px;">
                <i class="fa fa-check-square-o" aria-hidden="true"></i> 상세 옵션 선택
                <small style="color:#888; font-size:0.8em; font-weight:normal;">(해당하는 항목을 모두 체크해주세요)</small>
            </h3>

            <?php foreach ($tour_filters as $key => $category) { ?>
                <div style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 15px; font-weight: bold; background: #f2f2f2; padding: 8px 12px; border-radius: 4px;">
                        <?php echo $category['label']; ?>
                    </h4>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <?php foreach ($category['items'] as $k => $v) {
                            // 저장된 값이 있으면 체크, 없으면 해제
                            // 그누보드 여분필드는 wr_11 처럼 필드명 그대로 사용
                            // 체크박스 value=1 로 설정
                            $is_checked = ($write[$k] == '1') ? 'checked' : '';
                        ?>
                            <label style="
                        display: inline-flex; 
                        align-items: center; 
                        cursor: pointer; 
                        background: #fff; 
                        padding: 8px 12px; 
                        border: 1px solid #ddd; 
                        border-radius: 20px; 
                        font-size: 14px;
                        transition: all 0.2s;
                    " onmouseover="this.style.borderColor='#333'" onmouseout="this.style.borderColor='#ddd'">
                                <input type="checkbox" name="<?php echo $k ?>" value="1" <?php echo $is_checked ?> style="margin-right: 6px; transform: scale(1.2);">
                                <?php echo $v ?>
                            </label>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <!-- // [CUSTOM] 상세 옵션 체크박스 영역 끝 -->

        <div class="write_div">
            <label for="wr_content" class="sound_only">내용<strong>필수</strong></label>
            <div class="wr_content <?php echo $is_dhtml_editor ? $config['cf_editor'] : ''; ?>">
                <?php if ($write_min || $write_max) { ?>
                    <!-- 최소/최대 글자 수 사용 시 -->
                    <p id="char_count_desc">이 게시판은 최소 <strong><?php echo $write_min; ?></strong>글자 이상, 최대 <strong><?php echo $write_max; ?></strong>글자 이하까지 글을 쓰실 수 있습니다.</p>
                <?php } ?>
                <?php echo $editor_html; // 에디터 사용시는 에디터로, 아니면 textarea 로 노출 
                ?>
                <?php if ($write_min || $write_max) { ?>
                    <div id="char_count_wrap"><span id="char_count"></span>글자</div>
                <?php } ?>
            </div>
        </div>

        <!-- 링크/파일 업로드 등은 필요시 활성화 -->
        <!--
    <?php for ($i = 1; $is_link && $i <= G5_LINK_COUNT; $i++) { ?>
    <div class="bo_w_link write_div">
        <label for="wr_link<?php echo $i ?>"><i class="fa fa-link" aria-hidden="true"></i><span class="sound_only"> 링크 #<?php echo $i ?></span></label>
        <input type="text" name="wr_link<?php echo $i ?>" value="<?php if ($w == "u") {
                                                                        echo $write['wr_link' . $i];
                                                                    } ?>" id="wr_link<?php echo $i ?>" class="frm_input full_input" size="50">
    </div>
    <?php } ?>
    -->

        <div class="btn_confirm write_div">
            <a href="<?php echo $list_href ?>" class="btn_cancel btn">취소</a>
            <button type="submit" id="btn_submit" accesskey="s" class="btn_submit btn">작성완료</button>
        </div>
    </form>

    <script>
        <?php if ($write_min || $write_max) { ?>
            // 글자수 제한
            var char_min = parseInt(<?php echo $write_min; ?>); // 최소
            var char_max = parseInt(<?php echo $write_max; ?>); // 최대
            check_byte("wr_content", "char_count");

            $(function() {
                $("#wr_content").on("keyup", function() {
                    check_byte("wr_content", "char_count");
                });
            });
        <?php } ?>

        function html_auto_br(obj) {
            if (obj.checked) {
                result = confirm("자동 줄바꿈을 하시겠습니까?\n\n자동 줄바꿈은 게시물 내용중 줄바뀐 곳을<br>태그로 변환하는 기능입니다.");
                if (result)
                    obj.value = "html2";
                else
                    obj.value = "html1";
            } else
                obj.value = "";
        }

        function fwrite_submit(f) {
            <?php echo $editor_js; // 에디터 사용시 자바스크립트에서 내용을 폼으로 넘겨주는 코드를 써주어야 함 
            ?>

            var subject = "";
            var content = "";
            $.ajax({
                url: g5_bbs_url + "/ajax.filter.php",
                type: "POST",
                data: {
                    "subject": f.wr_subject.value,
                    "content": f.wr_content.value
                },
                dataType: "json",
                async: false,
                cache: false,
                success: function(data, textStatus) {
                    subject = data.subject;
                    content = data.content;
                }
            });

            if (subject) {
                alert("제목에 금지단어('" + subject + "')가 포함되어있습니다");
                f.wr_subject.focus();
                return false;
            }

            if (content) {
                alert("내용에 금지단어('" + content + "')가 포함되어있습니다");
                if (typeof(ed_wr_content) != "undefined")
                    ed_wr_content.returnFalse();
                else
                    f.wr_content.focus();
                return false;
            }

            if (document.getElementById("char_count")) {
                if (char_min > 0 || char_max > 0) {
                    var cnt = parseInt(check_byte("wr_content", "char_count"));
                    if (char_min > 0 && char_min > cnt) {
                        alert("내용은 " + char_min + "글자 이상 쓰셔야 합니다.");
                        return false;
                    }
                    if (char_max > 0 && char_max < cnt) {
                        alert("내용은 " + char_max + "글자 이하로 쓰셔야 합니다.");
                        return false;
                    }
                }
            }

            <?php echo $captcha_js; // 캡챠 사용시 자바스크립트에서 입력된 캡챠를 검사하는 코드를 써주어야 함 
            ?>

            document.getElementById("btn_submit").disabled = "disabled";

            return true;
        }
    </script>
</section>
<!-- } 게시물 작성/수정 끝 -->