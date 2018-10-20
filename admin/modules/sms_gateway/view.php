<?php
/**
 *
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

/* by Heru Subekti,2015 (heroe_soebekti@yahoo.co.id)
/* view log */

// key to authenticate
define('INDEX_AUTH', '1');
// key to get full database access
define('DB_ACCESS', 'fa');

if (!defined('SB')) {
    // main system configuration
    require '../../../sysconfig.inc.php';
    // start the session
    require SB.'admin/default/session.inc.php';
}
// IP based access limitation
require LIB.'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-system');

// only administrator have privileges to change global settings
if ($_SESSION['uid'] != 1) {
    header('Location: '.MWB.'system/content.php');
    die();
}

require SB.'admin/default/session_check.inc.php';
require SIMBIO.'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require LIB.'gammu/config.inc.php';

?>
<style type="text/css">
.list{font-size: 10pt;font-family: arial;color: #063B59;line-height: 25px;padding-left: 20px;}
.list:hover {background-color: rgba(201, 192, 192, 0.6); }</style>
<?php

$file = fopen(PATH."logsmsdrc","r");
while(! feof($file)){
  echo '<div class="list">'.fgets($file). "</div>"."\n";
  }
fclose($file);
?> 