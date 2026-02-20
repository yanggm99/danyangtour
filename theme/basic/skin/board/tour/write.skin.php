<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 필터 설정 로드
include_once(dirname(__FILE__) . '/../../../filter_config.php');

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

        <!-- [CUSTOM] 업체 기본 정보 입력 영역 (업체명, 홈페이지, 연락처, 주소 통합) -->
        <div class="write_div" style="margin: 20px 0; border: 1px solid #e5e5e5; padding: 20px; background: #fff;">
            <h3 style="margin-bottom: 20px; font-size: 1.2em; border-bottom: 2px solid #333; padding-bottom: 10px;">
                <i class="fa fa-info-circle" aria-hidden="true"></i> 업체 기본 정보
                <small style="color:#888; font-size:0.8em; font-weight:normal;">(업체명, 홈페이지, 연락처, 주소를 관리합니다)</small>
            </h3>

            <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                <div style="flex: 1; min-width: 100%;">
                    <label for="wr_subject" style="display:block; margin-bottom:8px; font-weight:bold; color:#555;">
                        <i class="fa fa-pencil" style="color:#f0ad4e; width:20px; text-align:center;"></i> 업체명(제목)<strong>필수</strong>
                    </label>
                    <div id="autosave_wrapper" style="position:relative;">
                        <input type="text" name="wr_subject" value="<?php echo $subject ?>" id="wr_subject" required class="frm_input full_input required" style="height:40px; border-radius:4px;" placeholder="업체명(제목)을 입력하세요">
                    </div>
                </div>

                <div style="flex: 1; min-width: 300px;">
                    <label for="wr_homepage" style="display:block; margin-bottom:8px; font-weight:bold; color:#555;">
                        <i class="fa fa-home" style="color:#337ab7; width:20px; text-align:center;"></i> 홈페이지 주소
                    </label>
                    <input type="text" name="wr_homepage" value="<?php echo $write['wr_homepage'] ?>" id="wr_homepage" class="frm_input full_input" style="height:40px; border-radius:4px;" placeholder="http:// 포함한 전체 주소 입력">
                </div>

                <div style="flex: 1; min-width: 300px;">
                    <label for="wr_2" style="display:block; margin-bottom:8px; font-weight:bold; color:#555;">
                        <i class="fa fa-phone" style="color:#5cb85c; width:20px; text-align:center;"></i> 전화번호
                    </label>
                    <input type="text" name="wr_2" value="<?php echo $write['wr_2'] ?>" id="wr_2" class="frm_input full_input" style="height:40px; border-radius:4px;" placeholder="전화번호 입력 (예: 010-0000-0000)">
                </div>

                <div style="flex: 1; min-width: 100%;">
                    <label for="wr_3" style="display:block; margin-bottom:8px; font-weight:bold; color:#555;">
                        <i class="fa fa-map-marker" style="color:#d9534f; width:20px; text-align:center;"></i> 주소
                    </label>
                    <input type="text" name="wr_3" value="<?php echo $write['wr_3'] ?>" id="wr_3" class="frm_input full_input" style="height:40px; border-radius:4px;" placeholder="지번 주소 또는 도로명 주소 입력">
                </div>
            </div>
        </div>

        <!-- [CUSTOM] 상세 옵션 체크박스 영역 (태그 시스템 Ver.) -->
        <?php
        // [태그 시스템] 수정 모드(w=u)일 때, 저장된 옵션 가져오기
        $saved_options = array();
        if ($w == 'u' && $wr_id) {
            $sql = " SELECT option_key FROM g5_write_tour_options WHERE wr_id = '{$wr_id}' ";
            $result = sql_query($sql);
            while ($row = sql_fetch_array($result)) {
                $saved_options[] = $row['option_key'];
            }
        }
        ?>

        <div class="write_div" style="margin: 20px 0; border: 1px solid #e5e5e5; padding: 20px; background: #fff;">
            <h3 style="margin-bottom: 20px; font-size: 1.2em; border-bottom: 2px solid #333; padding-bottom: 10px;">
                <i class="fa fa-check-square-o" aria-hidden="true"></i> 상세 옵션 선택
                <small style="color:#888; font-size:0.8em; font-weight:normal;">(해당하는 항목을 모두 체크해주세요)</small>
            </h3>

            <?php foreach ($tour_filters as $cat_key => $category) { ?>
                <div style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 15px; font-weight: bold; background: #f2f2f2; padding: 8px 12px; border-radius: 4px;">
                        <?php echo $category['label']; ?>
                    </h4>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <?php foreach ($category['items'] as $key => $val) {
                            $label = is_array($val) ? $val['label'] : $val;

                            // 현재 글에 저장된 태그인지 확인
                            $checked = (in_array($key, $saved_options)) ? 'checked' : '';
                        ?>
                            <label class="tag_check_item" style="
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
                                <input type="checkbox" name="options[]" value="<?php echo $key; ?>" <?php echo $checked; ?> style="margin-right: 6px; transform: scale(1.2);">
                                <span><?php echo $label; ?></span>
                            </label>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <!-- // [CUSTOM] 상세 옵션 체크박스 영역 끝 -->

        <style>
            /* 상세정보 입력창 내부 테두리 제거 및 스타일 최적화 */
            .wr_content_container textarea,
            .wr_content_container .cke_chrome,
            .wr_content_container .cke_inner {
                border: none !important;
                box-shadow: none !important;
                outline: none !important;
            }
        </style>
        <div class="write_div">
            <h3 style="margin: 20px 15px 10px; font-size: 1.2em; border-bottom: 2px solid #333; padding-bottom: 10px;">
                <i class="fa fa-file-text-o" aria-hidden="true"></i> 상세 정보
                <small style="color:#888; font-size:0.8em; font-weight:normal;">(업체에 대한 자세한 설명을 입력해주세요)</small>
            </h3>
            <label for="wr_content" class="sound_only">내용<strong>필수</strong></label>
            <div class="wr_content_container <?php echo $is_dhtml_editor ? $config['cf_editor'] : ''; ?>" style="margin: 0 15px; padding: 10px; border: 1px solid #e5e5e5; border-radius: 10px; overflow: hidden; background: #fafafa;">
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

        <style>
            /* 취소/작성완료 버튼 잘림 현상 수정 */
            .btn_confirm .btn_cancel,
            .btn_confirm .btn_submit {
                display: inline-block !important;
                width: auto !important;
                min-width: 100px;
                height: 45px !important;
                line-height: 45px !important;
                padding: 0 20px !important;
                font-size: 15px;
                text-align: center;
                box-sizing: border-box;
                vertical-align: middle;
            }
        </style>
        <div class="btn_confirm write_div" style="text-align:center; padding-top:20px;">
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