<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (G5_COMMUNITY_USE === false) {
    include_once(G5_THEME_SHOP_PATH . '/shop.tail.php');
    return;
}

?>
</div>
</div>


<?php echo poll('theme/basic'); // 설문조사 
?>
<?php echo visit('theme/basic'); // 방문자수 
?>


<style>
    /* 모바일 푸터 프리미엄 스타일 */
    #ft {
        background: #1a1a1a;
        color: #888;
        padding: 40px 20px 80px;
        text-align: center;
        border-top: 1px solid #333;
    }

    #ft_copy {
        margin-bottom: 20px;
        font-size: 11px;
    }

    #ft_company {
        margin-bottom: 15px;
        display: flex;
        justify-content: center;
        gap: 15px;
    }

    #ft_company a {
        color: #ccc;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
    }

    .ft_cnt {
        text-align: left;
        background: #222;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
    }

    .ft_cnt h2 {
        display: none;
    }

    .ft_info {
        font-size: 12px;
        line-height: 1.8;
        color: #777;
    }

    .ft_info b {
        color: #aaa;
    }

    #top_btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 45px;
        height: 45px;
        background: #007bff;
        color: #fff;
        border: none;
        border-radius: 50%;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        z-index: 1000;
    }

    #device_change {
        display: inline-block;
        margin-top: 20px;
        padding: 8px 20px;
        border: 1px solid #444;
        border-radius: 20px;
        color: #999;
        text-decoration: none;
        font-size: 12px;
    }
</style>

<div id="ft">
    <div id="ft_company">
        <a href="<?php echo get_pretty_url('content', 'company'); ?>">회사소개</a>
        <a href="<?php echo get_pretty_url('content', 'privacy'); ?>">개인정보처리방침</a>
        <a href="<?php echo get_pretty_url('content', 'provision'); ?>">이용약관</a>
    </div>

    <div class="ft_cnt">
        <p class="ft_info">
            <b>상호</b> : <?php echo $config['cf_title']; ?> | <b>대표자</b> : 홍길동<br>
            <b>주소</b> : 충청북도 단양군 단양읍...<br>
            <b>사업자번호</b> : 123-45-67890<br>
            <b>연락처</b> : 043-123-4567<br>
            <span style="color:#007bff; font-weight:bold;">[MOBILE PREMIUM VIEW]</span>
        </p>
    </div>

    <div id="ft_copy">
        Copyright &copy; <b><?php echo $_SERVER['HTTP_HOST']; ?></b> All rights reserved.
    </div>

    <?php if (G5_DEVICE_BUTTON_DISPLAY && G5_IS_MOBILE) { ?>
        <a href="<?php echo get_device_change_url(); ?>" id="device_change">PC 버전으로 보기</a>
    <?php } ?>

    <button type="button" id="top_btn"><i class="fa fa-arrow-up"></i></button>
</div>

<script>
    $(function() {
        $("#top_btn").on("click", function() {
            $("html, body").animate({
                scrollTop: 0
            }, '300');
            return false;
        });
    });
</script>

<?php
include_once(G5_THEME_PATH . "/tail.sub.php");
