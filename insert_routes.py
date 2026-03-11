#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')
import pymysql
from datetime import datetime

my = pymysql.connect(host='192.168.0.200', port=3306, user='root',
    password='dbPP76002332$$', db='gmsoft', charset='utf8mb4')
cur = my.cursor()

now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

routes = [
    {
        'wr_subject': '단양 핵심 1박2일 코스 – 도담삼봉·고수동굴·만천하스카이워크',
        'wr_content': '''<h2>단양의 랜드마크만 골라 담은 알짜 코스</h2>
<p>시간이 없어도 단양의 핵심은 다 보고 싶다면 이 코스를 추천합니다. 하루 반나절이면 단양을 대표하는 세 곳을 모두 만날 수 있습니다.</p>

<h3>1일차</h3>
<ul>
  <li><strong>오전</strong> – 도담삼봉 · 석문: 남한강 위에 솟은 세 봉우리, 탁 트인 강변 풍경 감상</li>
  <li><strong>점심</strong> – 단양 마늘순대국밥 또는 올갱이해장국 (구경시장 인근)</li>
  <li><strong>오후</strong> – 고수동굴: 3억 년 수석 종유석·석순의 세계</li>
  <li><strong>저녁</strong> – 단양 구경시장 야경 & 마늘 먹거리 탐방</li>
</ul>

<h3>2일차</h3>
<ul>
  <li><strong>오전</strong> – 만천하스카이워크: 유리 전망대에서 남한강 절경 조망</li>
  <li><strong>오후</strong> – 선암계곡 트레킹 또는 단양강 잔도 산책 후 귀가</li>
</ul>

<p><strong>추천 숙소:</strong> 단양 시내 펜션 또는 리조트 (만천하 인근 숙소 추천)<br>
<strong>이동 팁:</strong> 도담삼봉 → 고수동굴 → 만천하 순서로 이동하면 동선이 가장 효율적입니다.</p>''',
        'ca_name': '',
    },
    {
        'wr_subject': '단양 레저 풀코스 – 래프팅·짚라인·번지점프 스릴 3종 세트',
        'wr_content': '''<h2>아드레날린이 필요하다면 단양으로!</h2>
<p>단양은 국내 최고 수준의 레저 스포츠 집결지입니다. 하루에 세 가지 스릴을 모두 경험할 수 있습니다.</p>

<h3>추천 일정 (당일치기 가능)</h3>
<ul>
  <li><strong>오전 9시</strong> – 단양 래프팅 (남한강 코스, 약 2시간)</li>
  <li><strong>점심</strong> – 래프팅 센터 인근 식당에서 식사 & 휴식</li>
  <li><strong>오후 2시</strong> – 만천하스카이워크 짚라인 (한강 위를 가로지르는 800m 코스)</li>
  <li><strong>오후 4시</strong> – 단양 번지점프 (60m 높이, 국내 최고 수준)</li>
</ul>

<h3>주의사항</h3>
<ul>
  <li>래프팅은 사전 예약 필수 (성수기 주말 조기 마감)</li>
  <li>레저 활동은 여벌 옷 필수 지참</li>
  <li>심장 질환·고혈압 등은 사전 의사와 상담 권장</li>
</ul>

<p><strong>추천 시즌:</strong> 5월~9월 (래프팅 최적 시기는 6~8월)</p>''',
        'ca_name': '',
    },
    {
        'wr_subject': '가족 여행 코스 – 아이와 함께하는 단양 2박3일',
        'wr_content': '''<h2>아이도 어른도 모두 행복한 단양 가족 여행</h2>
<p>단양은 자연 학습과 체험이 풍부해 아이들과 함께하기 최적의 여행지입니다.</p>

<h3>1일차 – 자연 탐험</h3>
<ul>
  <li>도담삼봉 유람선 탑승 (아이들이 특히 좋아함)</li>
  <li>단양 수석전시관 관람</li>
  <li>구경시장에서 마늘아이스크림 체험</li>
</ul>

<h3>2일차 – 동굴 탐험</h3>
<ul>
  <li>고수동굴 탐방 (자연 과학 학습)</li>
  <li>온달관광지 및 온달동굴 (역사 체험)</li>
  <li>단양 강변 물놀이</li>
</ul>

<h3>3일차 – 전망 & 귀가</h3>
<ul>
  <li>만천하스카이워크 (어린이 동반 가능)</li>
  <li>단양 잔도 산책</li>
  <li>귀가 전 단양 마늘 특산품 쇼핑</li>
</ul>

<p><strong>추천 숙소:</strong> 수영장 있는 가족형 펜션<br>
<strong>이동 팁:</strong> 자가용 이용 시 이동 훨씬 편리, 주요 관광지 주차 무료</p>''',
        'ca_name': '',
    },
    {
        'wr_subject': '힐링 온천 여행 – 단양 스파·족욕·명상 1박2일',
        'wr_content': '''<h2>지친 몸과 마음을 리셋하는 단양 힐링 코스</h2>
<p>도시의 스트레스에서 벗어나 자연 속 휴식을 원한다면 이 코스를 추천합니다.</p>

<h3>1일차</h3>
<ul>
  <li><strong>오후 도착</strong> – 숙소 체크인 (온천 시설 있는 리조트 추천)</li>
  <li><strong>저녁 전</strong> – 단양강 잔도 느린 산책 (5.4km 평탄 코스)</li>
  <li><strong>저녁</strong> – 단양 현지 식당에서 올갱이해장국 또는 마늘 요리</li>
  <li><strong>밤</strong> – 숙소 스파·온천 이용</li>
</ul>

<h3>2일차</h3>
<ul>
  <li><strong>오전</strong> – 선암계곡 삼림욕 & 족욕 체험</li>
  <li><strong>점심</strong> – 계곡 인근 자연식 식당</li>
  <li><strong>오후</strong> – 단양 도담삼봉 앞 카페에서 강변 여유</li>
</ul>

<p><strong>포인트:</strong> 빡빡한 관광지 방문 없이 자연과 함께 쉬는 것이 이 코스의 핵심입니다.</p>''',
        'ca_name': '',
    },
    {
        'wr_subject': '단양 8경 완전 정복 – 전통 명소 순례 코스',
        'wr_content': '''<h2>단양 8경을 모두 만나는 클래식 코스</h2>
<p>단양8경은 조선 시대부터 이름난 절경 8곳입니다. 이 코스로 모두 둘러볼 수 있습니다.</p>

<h3>단양 8경 목록</h3>
<ol>
  <li><strong>도담삼봉</strong> – 남한강 위 세 봉우리</li>
  <li><strong>석문</strong> – 자연이 만든 돌문</li>
  <li><strong>구담봉</strong> – 충주호 위 절벽</li>
  <li><strong>옥순봉</strong> – 옥처럼 솟은 바위</li>
  <li><strong>상선암</strong> – 선암계곡 상류</li>
  <li><strong>중선암</strong> – 계곡 중간의 반석</li>
  <li><strong>하선암</strong> – 3단 폭포와 소</li>
  <li><strong>사인암</strong> – 사인이 노닐던 바위</li>
</ol>

<h3>추천 일정</h3>
<ul>
  <li><strong>1일차</strong>: 도담삼봉 → 석문 → 구담봉·옥순봉(유람선)</li>
  <li><strong>2일차</strong>: 상선암 → 중선암 → 하선암 → 사인암</li>
</ul>

<p><strong>팁:</strong> 구담봉·옥순봉은 유람선으로 접근하는 것이 가장 아름답습니다.</p>''',
        'ca_name': '',
    },
    {
        'wr_subject': '단양 미식 여행 – 마늘·올갱이·민물고기 맛집 탐방',
        'wr_content': '''<h2>단양의 맛을 오감으로 즐기는 미식 여행</h2>
<p>단양은 마늘 특산지이자 올갱이(다슬기), 민물고기 요리로 유명합니다. 먹는 것이 여행의 반이라면 이 코스가 딱입니다.</p>

<h3>꼭 먹어야 할 단양 음식</h3>
<ul>
  <li><strong>마늘 순대국밥</strong> – 단양 마늘 향이 진한 구수한 국밥</li>
  <li><strong>올갱이해장국</strong> – 남한강 다슬기로 끓인 진하고 시원한 국</li>
  <li><strong>마늘 만두</strong> – 구경시장 명물</li>
  <li><strong>민물고기 매운탕</strong> – 쏘가리·메기 매운탕</li>
  <li><strong>마늘 아이스크림</strong> – 의외로 맛있는 단양 명물 디저트</li>
</ul>

<h3>추천 코스</h3>
<ul>
  <li>오전: 단양 구경시장 투어 & 아침 식사</li>
  <li>점심: 올갱이해장국 맛집</li>
  <li>오후: 강변 카페 탐방</li>
  <li>저녁: 민물고기 매운탕 & 마늘 요리 저녁 식사</li>
</ul>

<p><strong>시장 운영:</strong> 단양 구경시장 매일 운영, 5일장(4·9일) 때 더욱 풍성</p>''',
        'ca_name': '',
    },
    {
        'wr_subject': '단양 트레킹 코스 – 소백산·선암계곡·잔도 걷기 여행',
        'wr_content': '''<h2>걷는 것만으로도 힐링이 되는 단양 트레킹</h2>
<p>단양은 소백산 국립공원을 품은 트레킹 명소입니다. 수준에 따라 다양한 코스를 선택할 수 있습니다.</p>

<h3>난이도별 트레킹 코스</h3>

<h4>쉬움 – 단양강 잔도 (5.4km, 약 2시간)</h4>
<p>남한강 절벽을 따라 만들어진 평탄한 데크 길. 강변 경치를 즐기며 누구나 걸을 수 있습니다.</p>

<h4>보통 – 선암계곡 트레킹 (4km, 약 2.5시간)</h4>
<p>맑은 계곡물 따라 걷는 삼림욕 코스. 여름 피서에 최적.</p>

<h4>어려움 – 소백산 연화봉 코스 (11km, 약 5~6시간)</h4>
<p>소백산 국립공원 대표 코스. 철쭉이 피는 5월이 하이라이트.</p>

<h3>추천 시즌</h3>
<ul>
  <li>잔도: 연중 (단, 겨울 결빙 주의)</li>
  <li>선암계곡: 6~9월</li>
  <li>소백산: 5월(철쭉), 10월(단풍)</li>
</ul>''',
        'ca_name': '',
    },
    {
        'wr_subject': '단양 당일치기 코스 – 서울·수도권에서 3시간이면 OK',
        'wr_content': '''<h2>수도권에서 가장 가까운 자연 절경, 단양 당일치기</h2>
<p>서울에서 중앙고속도로로 2시간 30분~3시간. 당일치기로도 충분히 알찬 여행이 가능합니다.</p>

<h3>추천 당일 코스 (약 8시간)</h3>
<ul>
  <li><strong>07:00</strong> – 서울 출발</li>
  <li><strong>09:30</strong> – 단양 도착 · 고수동굴 탐방 (1시간)</li>
  <li><strong>11:00</strong> – 도담삼봉 관람 & 사진 촬영</li>
  <li><strong>12:00</strong> – 구경시장 점심 (마늘 순대국밥 or 올갱이해장국)</li>
  <li><strong>13:30</strong> – 만천하스카이워크 또는 단양강 잔도 산책</li>
  <li><strong>15:30</strong> – 단양 특산품 쇼핑 (마늘, 수석)</li>
  <li><strong>16:30</strong> – 단양 출발</li>
  <li><strong>19:00</strong> – 서울 도착 (예상)</li>
</ul>

<p><strong>교통 팁:</strong><br>
- 자가용: 서울~단양 중앙고속도로 약 160km<br>
- 버스: 동서울터미널 → 단양 (약 2시간 30분, 하루 다수 편)<br>
- 기차: 청량리 → 단양역 (무궁화호, 약 2시간)</p>''',
        'ca_name': '',
    },
    {
        'wr_subject': '커플 로맨틱 코스 – 단양에서 둘만의 특별한 시간',
        'wr_content': '''<h2>사랑하는 사람과 떠나는 단양 로맨틱 여행</h2>
<p>강변 절경, 동굴 탐험, 별빛 가득한 밤하늘… 단양은 커플 여행자에게 완벽한 배경을 제공합니다.</p>

<h3>1일차 – 설레는 첫날</h3>
<ul>
  <li><strong>오후 도착</strong> – 남한강 전망 좋은 숙소 체크인</li>
  <li><strong>해질녘</strong> – 도담삼봉 일몰 감상 (황금빛 노을과 세 봉우리의 환상 조합)</li>
  <li><strong>저녁</strong> – 강변 분위기 좋은 레스토랑에서 디너</li>
  <li><strong>밤</strong> – 단양 구경시장 야경 & 포장마차 체험</li>
</ul>

<h3>2일차 – 함께하는 추억</h3>
<ul>
  <li><strong>오전</strong> – 고수동굴 함께 탐험 (손잡고 걷는 신비로운 공간)</li>
  <li><strong>점심</strong> – 분위기 있는 강변 카페에서 브런치</li>
  <li><strong>오후</strong> – 만천하스카이워크 투명 유리 위에서 기념사진</li>
  <li><strong>저녁</strong> – 별빛 보이는 테라스에서 와인 한 잔</li>
</ul>

<p><strong>특별 팁:</strong> 도담삼봉 일출(오전 6~7시)도 매우 낭만적입니다. 일찍 일어날 용기가 있다면 꼭 도전해보세요!</p>''',
        'ca_name': '',
    },
    {
        'wr_subject': '단양 사계절 여행 – 봄꽃·여름 물놀이·가을단풍·겨울설경',
        'wr_content': '''<h2>어느 계절에 가도 아름다운 단양</h2>
<p>단양은 사계절 내내 다른 매력을 발산합니다. 언제 방문해도 실망 없는 여행지입니다.</p>

<h3>봄 (3~5월) – 꽃과 생명의 계절</h3>
<ul>
  <li>소백산 철쭉 군락 (5월 중순, 전국 최대 규모)</li>
  <li>단양 강변 벚꽃길 산책</li>
  <li>선암계곡 신록 트레킹</li>
</ul>

<h3>여름 (6~8월) – 시원한 물의 계절</h3>
<ul>
  <li>남한강 래프팅 (최적 시기)</li>
  <li>선암계곡 피서 & 물놀이</li>
  <li>단양 강변 캠핑</li>
</ul>

<h3>가을 (9~11월) – 단풍과 풍요의 계절</h3>
<ul>
  <li>소백산 단풍 트레킹 (10월 중순)</li>
  <li>충주호 유람선 단풍 감상</li>
  <li>사인암 가을 풍경</li>
</ul>

<h3>겨울 (12~2월) – 설경과 온천의 계절</h3>
<ul>
  <li>소백산 설경 산행 (눈꽃 장관)</li>
  <li>온천·스파 힐링 여행</li>
  <li>고수동굴 (겨울에도 15도 유지, 따뜻한 관광 가능)</li>
</ul>

<p><strong>단양 마늘 수확 시기:</strong> 6월 초, 마늘 축제와 함께 특산품 직거래 가능</p>''',
        'ca_name': '',
    },
]

# Get max wr_id from table (if exists)
cur.execute("SELECT COALESCE(MAX(wr_id), 0) FROM g5_write_route")
max_id = cur.fetchone()[0]

print(f"현재 최대 wr_id: {max_id}")

inserted = 0
for i, route in enumerate(routes):
    wr_id = max_id + i + 1
    wr_num = -(max_id + i + 1)

    cur.execute("""
        INSERT INTO g5_write_route
        (wr_id, wr_num, wr_reply, wr_parent, wr_is_comment, wr_comment, wr_comment_reply,
         ca_name, wr_option, wr_subject, wr_content, wr_seo_title,
         wr_link1, wr_link2, wr_link1_hit, wr_link2_hit,
         wr_hit, wr_good, wr_nogood, mb_id, wr_password, wr_name, wr_email, wr_homepage,
         wr_datetime, wr_file, wr_last, wr_ip,
         wr_facebook_user, wr_twitter_user,
         wr_1, wr_2, wr_3, wr_4, wr_5, wr_6, wr_7, wr_8, wr_9, wr_10)
        VALUES
        (%s, %s, '', %s, 0, 0, '',
         %s, '', %s, %s, '',
         '', '', 0, 0,
         0, 0, 0, 'admin', '', '관리자', '', '',
         %s, 0, %s, '',
         '', '',
         '', '', '', '', '', '', '', '', '', '')
    """, (
        wr_id, wr_num, wr_id,
        route['ca_name'], route['wr_subject'], route['wr_content'],
        now, now
    ))
    inserted += 1
    print(f"  [{i+1}] 삽입 완료: {route['wr_subject'][:30]}...")

# Update g5_board wr_count
cur.execute("UPDATE g5_board SET bo_count_write = %s WHERE bo_table = 'route'", (inserted,))

my.commit()
my.close()
print(f"\n총 {inserted}개 게시물 삽입 완료!")
