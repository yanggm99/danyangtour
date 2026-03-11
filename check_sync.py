#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import sys, io, sqlite3, pymysql
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

db = sqlite3.connect('c:/Users/YANG/OneDrive/PROJECT/danyanginfo/danyang_info_v2.db')
db.row_factory = sqlite3.Row
sc = db.cursor()

my = pymysql.connect(host='192.168.0.200', port=3306, user='root',
    password='dbPP76002332$$', db='gmsoft', charset='utf8mb4')
mc = my.cursor(pymysql.cursors.DictCursor)

# SQLite: additional_images 있는 업체 전체
sc.execute("""
    SELECT name, image_url, additional_images,
           contact, address_road, address_jibun,
           latitude, longitude, homepage_url
    FROM places
    WHERE additional_images IS NOT NULL AND additional_images != '' AND additional_images != '[]'
""")
sqlite_rows = sc.fetchall()
print(f'SQLite additional_images 있는 업체: {len(sqlite_rows)}개')

# 그누보드 전체 업체 이름→행 매핑
mc.execute("SELECT wr_id, wr_subject, wr_2, wr_3, wr_5, wr_6, wr_7, wr_homepage FROM g5_write_tour WHERE wr_is_comment=0")
gnuboard_all = {r['wr_subject']: r for r in mc.fetchall()}
print(f'그누보드 전체 업체: {len(gnuboard_all)}개')

# 매칭 분석
match_need_update = []   # 이름 매칭 & wr_7 비어있음 → 업데이트 대상
match_already_ok  = []   # 이름 매칭 & wr_7 있음 → 스킵
no_match          = []   # 이름 매칭 안됨

for r in sqlite_rows:
    name = r['name']
    if name in gnuboard_all:
        gw = gnuboard_all[name]
        if not gw['wr_7']:
            match_need_update.append((r, gw))
        else:
            match_already_ok.append(name)
    else:
        no_match.append(name)

print(f'\n=== 분석 결과 ===')
print(f'업데이트 필요 (매칭O, wr_7 없음): {len(match_need_update)}개')
print(f'이미 OK (매칭O, wr_7 있음):       {len(match_already_ok)}개')
print(f'미매칭 (이름 다름):                {len(no_match)}개')

print(f'\n=== 업데이트 대상 샘플 5개 ===')
for r, gw in match_need_update[:5]:
    imgs = r['additional_images'][:80] if r['additional_images'] else ''
    print(f'  wr_id={gw["wr_id"]} | {r["name"]} | imgs={imgs}...')

db.close()
my.close()
