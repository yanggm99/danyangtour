<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 필터 설정 (데이터베이스 필드명 => 라벨)

$tour_filters = array(
    // [숙박] 자고
    'stay' => array(
        'label' => '자고 (숙박)',
        'items' => array(
            'stay_pension' => '펜션',
            'stay_hotel' => '호텔',
            'stay_motel' => '모텔',
            'stay_resort' => '리조트',
            'stay_condo' => '콘도',
            'stay_minbak' => '민박',
            'stay_hanok' => '한옥 체험',
            'stay_pool' => '수영장 보유',
            'stay_valley' => '계곡 인근',
            'stay_pet' => '애견 동반 가능',
            'stay_bbq' => '솥뚜껑 바비큐',
            'stay_private' => '독채 펜션',
            'stay_glamping' => '글램핑',
            'stay_caravan' => '카라반',
            'stay_spa' => '스파/월풀',
            'stay_breakfast' => '조식 제공',
            'stay_riverview' => '리버뷰(강)',
            'stay_mountainview' => '마운틴뷰(산)',
            'stay_pickup' => '픽업 서비스',
            'stay_longterm' => '장박 가능',
            // --- 추가된 태그 ---
            'stay_family' => '가족 숙소',
            'stay_grass' => '잔디 마당',
            'stay_ondol' => '온돌방',
            'stay_bed' => '침대방',
            'stay_annex' => '별채',
            'stay_bbq_ind' => '개별 바비큐',
            'stay_camping' => '캠핑 가능',
            'stay_walk' => '산책로',
            'stay_footvolley' => '족구장',
            'stay_river' => '강 인근',
            'stay_clay' => '황토방',
            'stay_1f' => '1층 객실',
            'stay_no_pet' => '반려동물 불가',
            'stay_karaoke' => '노래방',
            'stay_group' => '단체 적합',
            'stay_view' => '전망 좋음',
            'stay_mountain' => '산 인근',
            'stay_no_group' => '소규모 권장',
            'stay_couple' => '연인 권장',
            'stay_duplex' => '복층 구조',
            'stay_long' => '장기 숙박'
        )
    ),

    // [식당] 먹고
    'food' => array(
        'label' => '먹고 (맛집/카페)',
        'items' => array(
            'food_garlic' => '마늘 특화 요리',
            'food_local' => '현지인 맛집',
            'food_pet' => '반려동물 출입',
            'food_private' => '룸/프라이빗',
            'food_group' => '단체석 완비',
            'food_tv' => '방송 출연 맛집',
            'food_vegan' => '비건 옵션',
            'food_kids' => '어린이 메뉴',
            'food_breakfast' => '아침 식사 가능',
            'food_view' => '뷰 맛집(카페)',
            'food_bakery' => '베이커리 카페',
            'food_parking' => '주차장 완비',
            'food_togo' => '포장/배달 가능',
            // --- 추가된 태그 ---
            'food_maeun' => '매운탕',
            'food_cafe' => '카페',
            'food_noodle' => '면요리',
            'food_korean' => '한식',
            'food_rib' => '갈비'
        )
    ),

    // [명소/체험] 보고/놀고/즐기고
    'spot' => array(
        'label' => '보고/놀고/즐기고 (명소/체험)',
        'items' => array(
            'spot_dodam' => '도담삼봉',
            'spot_gosu' => '고수동굴',
            'spot_skywalk' => '만천하스카이워크',
            'spot_aqua' => '다누리아쿠아리움',
            'spot_sainam' => '사인암',
            'spot_sobaek' => '소백산',
            'spot_guinsa' => '구인사',
            'spot_ondal' => '온달관광지',
            'spot_haseon' => '하선암',
            'spot_jungseon' => '중선암',
            'spot_sangseon' => '상선암',
            'spot_tunnel' => '수양개빛터널',
            'spot_gudam' => '구담봉',
            'spot_oksun' => '옥순봉',
            'spot_palgyeong' => '단양팔경',
            'spot_seokmun' => '석문',
            'spot_yangbang' => '양방산',
            'play_paragliding' => '패러글라이딩',
            'play_rafting' => '래프팅',
            'play_atv' => 'ATV/사륜오토바이',
            'play_cruise' => '유람선 투어',
            'play_fish' => '낚시',
            'play_photo' => '사진/영상 촬영',
            'play_unique' => '이색 체험',
            'play_kids' => '아이 동반 추천',
            'play_couple' => '커플 데이트',
            'play_reserve' => '사전 예약 필수',
            'play_rain' => '우천 시 이용 가능',
            'play_water' => '물놀이',
            'play_clay' => '황토체험',
            'enjoy_walk' => '걷기 좋은 길',
            'enjoy_fest' => '축제/행사',
            'enjoy_rural' => '농촌체험'
        )
    ),

    // [기타] 구매/이동
    'etc' => array(
        'label' => '구매/이동 (쇼핑/편의)',
        'items' => array(
            'buy_market' => '구경시장',
            'buy_souvenir' => '기념품',
            'move_station' => '단양역',
            'move_pickup' => '픽업 지원',
            'etc_market' => '구경시장 명물',
            'etc_souvenir' => '지역 특산품',
            'etc_ev' => '전기차 충전소',
            'etc_rent' => '렌터카 제휴',
            'etc_walk' => '뚜벅이 여행 추천',
            'etc_wheelchair' => '휠체어 접근 가능',
            'etc_lang' => '외국어 응대',
            'etc_luggage' => '짐 보관 서비스',
            'etc_voucher' => '단양 사랑 상품권',
            'etc_zeropay' => '제로페이 가맹점',
            'etc_wifi' => '무선인터넷'
        )
    )
);

// 전체 필터를 하나의 배열로 병합 (조회용)
$all_filters = array();
foreach ($tour_filters as $category) {
    foreach ($category['items'] as $key => $val) {
        $all_filters[$key] = $val;
    }
}
