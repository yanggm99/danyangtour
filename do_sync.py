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

# 그누보드 전체
mc.execute("SELECT wr_id, wr_subject, wr_2, wr_3, wr_5, wr_6, wr_7, wr_homepage FROM g5_write_tour WHERE wr_is_comment=0")
gnuboard_all = {}
for r in mc.fetchall():
    # 같은 이름이 여러 개면 마지막 것으로 (중복 병합 완료 상태라 1개여야 함)
    gnuboard_all[r['wr_subject']] = r

updated = 0
skipped = 0
seen_wr_ids = set()

for r in sqlite_rows:
    name = r['name']
    if name not in gnuboard_all:
        continue
    gw = gnuboard_all[name]
    wr_id = gw['wr_id']

    # 중복 wr_id 스킵 (같은 이름 SQLite 중복 행 처리)
    if wr_id in seen_wr_ids:
        continue

    # wr_7 이미 있으면 스킵
    if gw['wr_7']:
        skipped += 1
        seen_wr_ids.add(wr_id)
        continue

    # image_url과 additional_images를 합쳐서 wr_7 구성
    imgs = []
    if r['image_url']:
        imgs.append(r['image_url'].strip())
    if r['additional_images']:
        for u in r['additional_images'].split(','):
            u = u.strip()
            if u and u not in imgs:
                imgs.append(u)

    wr_7 = ','.join(imgs)

    # 업데이트할 필드 결정 (비어있는 것만 채움)
    fields = {}
    if wr_7:
        fields['wr_7'] = wr_7
    if not gw['wr_2'] and r['contact']:
        fields['wr_2'] = r['contact']
    if not gw['wr_3']:
        addr = r['address_road'] or r['address_jibun'] or ''
        if addr:
            fields['wr_3'] = addr
    if not gw['wr_5'] and r['latitude']:
        fields['wr_5'] = str(r['latitude'])
    if not gw['wr_6'] and r['longitude']:
        fields['wr_6'] = str(r['longitude'])
    if not gw['wr_homepage'] and r['homepage_url']:
        fields['wr_homepage'] = r['homepage_url']

    if not fields:
        seen_wr_ids.add(wr_id)
        continue

    set_clause = ', '.join([f"{k}=%s" for k in fields])
    vals = list(fields.values()) + [wr_id]
    mc.execute(f"UPDATE g5_write_tour SET {set_clause} WHERE wr_id=%s", vals)

    print(f'업데이트: wr_id={wr_id} | {name}')
    for k, v in fields.items():
        print(f'  {k} = {str(v)[:80]}')

    updated += 1
    seen_wr_ids.add(wr_id)

my.commit()
print(f'\n완료: {updated}건 업데이트, {skipped}건 스킵(이미 있음)')
db.close()
my.close()
