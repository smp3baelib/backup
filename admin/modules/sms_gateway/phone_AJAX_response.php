<?php
/**
 * Copyright (C) 2007,2008  Arie Nugraha (dicarve@yahoo.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */
/* modified by Heru Subekti,2014 (heroe_soebekti@yahoo.co.id) */
// key to authenticate
define('INDEX_AUTH', '1');

sleep(1);
require '../../../sysconfig.inc.php';
// IP based access limitation
require LIB.'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-membership');
require SB.'admin/default/session.inc.php';

// privileges checking
$can_read = utility::havePrivilege('membership', 'r');
if (!$can_read) { die(); }

function convert_number($nohp) {
    $nohp = str_replace(" ","",$nohp);
    $nohp = str_replace("(","",$nohp);
    $nohp = str_replace(")","",$nohp);
    $nohp = str_replace(".","",$nohp);

    if(!preg_match('/[^+0-9]/',trim($nohp))){
        if(substr(trim($nohp), 0, 3)=='+62'){
            $hp = trim($nohp);
        }
        elseif(substr(trim($nohp), 0, 1)=='0'){
            $hp = '+62'.substr(trim($nohp), 1);
        }

        elseif(substr(trim($nohp), 0, 1)!='0'){
            $hp = '+62'.substr(trim($nohp), 1);
        }
        else
            $hp = $nohp;
    }
    return $hp;
}

header('Content-type: text/json');
$json_array = array();

// get search value
if (isset($_POST['inputSearchVal'])) {
    $searchVal = $dbs->escape_string(trim($_POST['inputSearchVal']));
} else {
    $json_array[] = '';
    echo json_encode($json_array);
    exit();
}
// query to database
$member_q = $dbs->query("SELECT member_phone, member_name
    FROM member WHERE member_phone LIKE '%$searchVal%' OR member_name LIKE '%$searchVal%' AND member_phone!='' LIMIT 10");
if ($member_q->num_rows < 1) {
    $json_array[] = __('No Data Found');
    echo json_encode($json_array);
    exit();
}

// loop data
while ($member_d = $member_q->fetch_row()) {
    $json_array[] = convert_number($member_d[0]).' -- '.$member_d[1];
}
// encode to JSON array
if (!function_exists('json_encode')) {
    echo json_encode($json_array);
    exit();
}

echo json_encode($json_array);
