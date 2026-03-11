<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (G5_COMMUNITY_USE === false) {
    define('G5_IS_COMMUNITY_PAGE', true);
    include_once(G5_THEME_SHOP_PATH . '/shop.head.php');
    return;
}

include_once(G5_THEME_PATH . '/head.sub.php');
include_once(G5_LIB_PATH . '/latest.lib.php');
include_once(G5_LIB_PATH . '/outlogin.lib.php');
include_once(G5_LIB_PATH . '/poll.lib.php');
include_once(G5_LIB_PATH . '/visit.lib.php');
include_once(G5_LIB_PATH . '/connect.lib.php');
include_once(G5_LIB_PATH . '/popular.lib.php');
?>

<style>
    /* 모바일 헤더 프리미엄 스타일 */
    #hd {
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
        height: 60px;
        display: flex;
        align-items: center;
        padding: 0 15px;
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    }

    #hd_wrapper {
        display: flex;
        align-items: center;
        width: 100%;
        justify-content: space-between;
    }

    #logo a img {
        height: 24px;
        width: auto;
    }

    .hd_opener {
        background: none;
        border: none;
        font-size: 20px;
        color: #333;
        cursor: pointer;
        padding: 10px;
    }

    #gnb_open {
        order: 1;
        margin-left: -10px;
    }

    #logo {
        order: 2;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
    }

    #user_btn {
        order: 3;
        margin-right: -10px;
        color: #007bff;
    }

    /* 메뉴 레이어 */
    .hd_div {
        background: #fff;
        position: fixed;
        top: 0;
        height: 100vh;
        width: 85%;
        z-index: 2000;
        box-shadow: 0 0 40px rgba(0, 0, 0, 0.2);
    }

    #gnb {
        left: -100%;
        transition: left 0.3s ease;
    }

    #gnb.active {
        left: 0;
    }

    #user_menu {
        right: -100%;
        transition: right 0.3s ease;
        height: auto;
        bottom: auto;
        top: 0;
        width: 100%;
        border-radius: 0 0 20px 20px;
    }

    #user_menu.active {
        right: 0;
    }

    .hd_closer {
        position: absolute;
        top: 15px;
        right: 15px;
        background: none;
        border: none;
        font-size: 24px;
        color: #333;
    }

    /* GNB 인테리어 */
    #gnb_1dul {
        padding: 60px 0 20px;
    }

    .gnb_1dli {
        border-bottom: 1px solid #f8f9fa;
    }

    .gnb_1da {
        display: block;
        padding: 15px 20px;
        font-size: 16px;
        font-weight: 700;
        color: #1a1a1a;
        text-decoration: none;
    }

    .gnb_2da {
        padding: 12px 30px;
        font-size: 14px;
        color: #666;
        display: block;
        text-decoration: none;
        background: #fafafa;
    }

    /* 검색 레이어 */
    #hd_sch {
        padding: 80px 20px 40px;
        text-align: center;
    }

    #hd_sch h2 {
        font-size: 18px;
        margin-bottom: 20px;
        color: #1a1a1a;
        font-weight: 900;
    }

    #hd_sch form {
        position: relative;
    }

    #sch_stx {
        width: 100%;
        border: 2px solid #007bff;
        border-radius: 30px;
        padding: 12px 50px 12px 20px;
        font-size: 15px;
        outline: none;
    }

    #sch_submit {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        background: #007bff;
        color: #fff;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    /* 인기 검색어 */
    .pop_sch {
        padding: 20px;
        background: #f8f9fa;
        margin: 0 15px;
        border-radius: 15px;
    }

    .pop_sch h2 {
        font-size: 14px;
        color: #999;
        margin-bottom: 10px;
    }

    .pop_sch ul {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .pop_sch li a {
        color: #333;
        font-size: 13px;
        text-decoration: none;
        background: #fff;
        padding: 5px 12px;
        border-radius: 15px;
        border: 1px solid #eee;
    }

    /* 컨테이너 보정 */
    #wrapper {
        padding-top: 60px;
    }
</style>

<header id="hd">
    <div id="hd_wrapper">
        <button type="button" id="gnb_open" class="hd_opener"><i class="fa fa-bars" aria-hidden="true"></i></button>

        <div id="logo">
            <a href="<?php echo G5_URL ?>" style="font-size: 24px; font-weight: 900; color: #007bff; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 5px;">
                <i class="fa fa-paper-plane" aria-hidden="true"></i> 더단양
            </a>
        </div>

        <button type="button" id="user_btn" class="hd_opener"><i class="fa fa-search" aria-hidden="true"></i></button>

        <!-- 메뉴 사이드바 -->
        <div id="gnb" class="hd_div">
            <button type="button" class="hd_closer"><i class="fa fa-times"></i></button>
            <div style="padding: 20px; background: #007bff; color: #fff;">
                <?php echo outlogin('theme/basic'); ?>
            </div>
            <ul id="gnb_1dul">
                <?php
                $menu_datas = get_menu_db(1, true);
                foreach ($menu_datas as $row) {
                    if (empty($row)) continue;
                ?>
                    <li class="gnb_1dli">
                        <a href="<?php echo $row['me_link']; ?>" class="gnb_1da"><?php echo $row['me_name'] ?></a>
                        <?php foreach ((array) $row['sub'] as $row2) { ?>
                            <a href="<?php echo $row2['me_link']; ?>" class="gnb_2da"><?php echo $row2['me_name'] ?></a>
                        <?php } ?>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <!-- 검색 레이어 -->
        <div id="user_menu" class="hd_div">
            <button type="button" class="hd_closer"><i class="fa fa-times"></i></button>
            <div id="hd_sch">
                <h2>무엇을 찾으시나요?</h2>
                <form name="fsearchbox" action="<?php echo G5_BBS_URL ?>/search.php" onsubmit="return fsearchbox_submit(this);" method="get">
                    <input type="hidden" name="sfl" value="wr_subject||wr_content">
                    <input type="hidden" name="sop" value="and">
                    <input type="text" name="stx" id="sch_stx" placeholder="검색어를 입력해주세요" required maxlength="20">
                    <button type="submit" id="sch_submit"><i class="fa fa-search"></i></button>
                </form>
            </div>
            <?php echo popular('theme/basic'); ?>
        </div>
    </div>
</header>

<script>
    $(function() {
        $("#gnb_open").on("click", function() {
            $("#gnb").addClass("active");
        });

        $("#user_btn").on("click", function() {
            $("#user_menu").addClass("active");
        });

        $(".hd_closer").on("click", function() {
            $(".hd_div").removeClass("active");
        });

        // 외부 클릭 시 닫기
        $(document).mouseup(function(e) {
            var gnb = $("#gnb");
            var user = $("#user_menu");
            if (!gnb.is(e.target) && gnb.has(e.target).length === 0) gnb.removeClass("active");
            if (!user.is(e.target) && user.has(e.target).length === 0) user.removeClass("active");
        });
    });

    function fsearchbox_submit(f) {
        var stx = f.stx.value.trim();
        if (stx.length < 2) {
            alert("검색어는 두글자 이상 입력하십시오.");
            f.stx.focus();
            return false;
        }
        return true;
    }
</script>

<div id="wrapper">
    <div id="container">
        <?php if (!defined("_INDEX_")) { ?>
            <h2 id="container_title" class="top" style="padding: 15px; font-size: 18px; font-weight: 900; background: #fff; border-bottom: 1px solid #eee; display: flex; align-items: center; gap: 10px;">
                <a href="javascript:history.back();" style="color:#333;"><i class="fa fa-chevron-left"></i></a> <?php echo get_head_title($g5['title']); ?>
            </h2>
        <?php }
