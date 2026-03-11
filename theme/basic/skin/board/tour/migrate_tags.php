<?php
$g5_path = realpath(dirname(__FILE__) . '/../../../../..');
include_once($g5_path . '/common.php');

$old_mappings = [
    'wr_11' => 'stay_hanok',
    'wr_12' => 'stay_pool',
    'wr_13' => 'stay_valley',
    'wr_14' => 'stay_pet',
    'wr_15' => 'stay_bbq',
    'wr_16' => 'stay_private',
    'wr_17' => 'stay_workshop',
    'wr_18' => 'stay_glamping',
    'wr_19' => 'stay_spa',
    'wr_20' => 'stay_breakfast',
    'wr_21' => 'stay_indiv_bbq',
    'wr_22' => 'stay_riverview',
    'wr_23' => 'stay_mountainview',
    'wr_24' => 'stay_pickup',
    'wr_25' => 'stay_longterm',
    'wr_26' => 'food_garlic',
    'wr_27' => 'food_local',
    'wr_28' => 'food_pet',
    'wr_29' => 'food_private',
    'wr_30' => 'food_group',
    'wr_31' => 'food_tv',
    'wr_32' => 'food_vegan',
    'wr_33' => 'food_kids',
    'wr_34' => 'food_breakfast',
    'wr_35' => 'food_view',
    'wr_36' => 'food_bakery',
    'wr_37' => 'food_parking',
    'wr_38' => 'food_togo',
    'wr_39' => 'play_para',
    'wr_40' => 'play_rafting',
    'wr_41' => 'play_atv',
    'wr_42' => 'play_cruise',
    'wr_43' => 'play_cave',
    'wr_44' => 'play_fishing',
    'wr_45' => 'play_photo',
    'wr_46' => 'play_unique',
    'wr_47' => 'play_kids',
    'wr_48' => 'play_couple',
    'wr_49' => 'play_reserve',
    'wr_50' => 'play_rain',
    'wr_51' => 'etc_market',
    'wr_52' => 'etc_souvenir',
    'wr_53' => 'etc_ev',
    'wr_54' => 'etc_rent',
    'wr_55' => 'etc_walk',
    'wr_56' => 'etc_wheelchair',
    'wr_57' => 'etc_lang',
    'wr_58' => 'etc_luggage',
    'wr_59' => 'etc_voucher',
    'wr_60' => 'etc_zeropay'
];

$sql = "select wr_id, wr_1 from {$g5['write_prefix']}tour where wr_1 != ''";
$result = sql_query($sql);
$count = 0;

while ($row = sql_fetch_array($result)) {
    $wr_1 = $row['wr_1'];
    $new_wr_1 = $wr_1;
    foreach ($old_mappings as $old => $new) {
        $new_wr_1 = preg_replace('/\b' . $old . '\b/', $new, $new_wr_1);
    }

    if ($wr_1 !== $new_wr_1) {
        // Update database
        $update_sql = "update {$g5['write_prefix']}tour set wr_1 = '{$new_wr_1}' where wr_id = '{$row['wr_id']}'";
        sql_query($update_sql);
        $count++;
    }
}
echo "MIGRATION_COMPLETE: Updated $count records.";
