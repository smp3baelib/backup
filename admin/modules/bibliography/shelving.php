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

/* Shelving Management section */
/* modified by Heru Subekti (heroe.soebekti@gmail.com) */
/* date : 08-08-2016 */

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
do_checkIP('smc-bibliography');

require SB.'admin/default/session_check.inc.php';
require SIMBIO.'simbio_DB/simbio_dbop.inc.php';
require SIMBIO.'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require SIMBIO.'simbio_GUI/table/simbio_table.inc.php';    

// privileges checking
$can_read = utility::havePrivilege('bibliography', 'r');
$can_write = utility::havePrivilege('bibliography', 'w');

if (!($can_read AND $can_write)) {
    die('<div class="errorBox">'.__('You don\'t have enough privileges to view this section').'</div>');
}

if(isset($_POST['start'])){
$_SESSION['shelving'] = array();
$_SESSION['shelving']['location'] = $_POST['location'];
    $loc_q = $dbs->query("SELECT l.location_name FROM mst_location l WHERE l.location_id='".$_SESSION['shelving']['location']."'");
    $a = $loc_q->fetch_row();
$_SESSION['shelving']['location_name'] = $a[0];    
$_SESSION['shelving']['shelf_location'] =  $_POST['shelf_location'];
echo '<script type="text/javascript">top.$(\'#mainContent\').simbioAJAX(\''.MWB.'bibliography/shelving.php\');</script>';
}

if(isset($_POST['finish']) && $_POST['finish']=='true'){
    foreach ($_SESSION['shelving']['item'] as $_item_ID => $temp_item_list_d) {
        $sql_op = new simbio_dbop($dbs);
        $data['location_id'] = $_SESSION['shelving']['location'];
        $data['site'] = trim($dbs->escape_string(strip_tags($_POST['site'])));
        $updateRecordID = $temp_item_list_d['item_id'];
        $data['last_update'] = date('Y-m-d H:i:s');
        if($_SESSION['shelving']['location']!=$temp_item_list_d['location_id'] || strip_tags($_POST['site'])!=$temp_item_list_d['site']){
        $update = $sql_op->update('item', $data, "item_id=".$updateRecordID);
            if($update){
                if($_SESSION['shelving']['location']!=$temp_item_list_d['location_id']){
                utility::writeLogs($dbs, 'staff', $_SESSION['uid'], 'shelving', $_SESSION['realname'].' move location ('.$temp_item_list_d['item_code'].') with title  '.$temp_item_list_d['title'].' from '.$temp_item_list_d['site'].'/'.$temp_item_list_d['location_name'].' to '.strip_tags($_POST['site']).'/'.$_SESSION['shelving']['location_name']);
                }elseif(strip_tags($_POST['site'])!=$temp_item_list_d['site']){
                utility::writeLogs($dbs, 'staff', $_SESSION['uid'], 'shelving', $_SESSION['realname'].' move shelf location ('.$temp_item_list_d['item_code'].') with title  '.$temp_item_list_d['title'].' from '.$temp_item_list_d['site'].'/'.$temp_item_list_d['location_name'].' to '.strip_tags($_POST['site']).'/'.$_SESSION['shelving']['location_name']);
                }
            unset($_SESSION['shelving']['item'][$updateRecordID]);
            }
        }
    }
    unset($_SESSION['shelving']);
    echo '<script type="text/javascript">top.$(\'#mainContent\').simbioAJAX(\''.MWB.'bibliography/shelving.php\');</script>';
}

if(isset($_SESSION['shelving'])){
    // show location information
    echo '<div class="per_title"><h2>'.__('Shelving').'</h2></div><br/><form id="finishForm" method="post" target="blindSubmit" action="'.MWB.'bibliography/shelving.php" style="display: inline; padding:5px !important;"><input type="button" class="btn btn-danger" accesskey="T" value="'.__('Finish').'" onclick="confSubmit(\'finishForm\', \''.__('Are you sure want to finish current shelving?').'\')" /><input type="hidden" name="finish" value="true" />';
        echo '<table width="100%" class="border" style="margin-bottom: 5px;" cellpadding="5" cellspacing="0">'."\n";
        echo '<tr>'."\n";

    $loc_q = $dbs->query("SELECT l.location_name FROM mst_location l WHERE l.location_id='".$_SESSION['shelving']['location']."'");
    $a = $loc_q->fetch_row();
    echo '<td class="dataListHeader" width="70px;"><b>'.__('Location').'</b></td><td>:</td><td>'.$a[0].'</td>';
    echo '</tr>'."\n";
    echo '<tr>'."\n"; 
    echo '<td class="dataListHeader"><b>'.__('Shelf Location').'</b></td><td>:</td><td><input type="text" name="site" value="'.$_SESSION['shelving']['shelf_location'].'"></td>';
    echo '</tr>'."\n";        
    echo '</table>'."\n";
    echo '</form>';
    echo '<iframe name="listIframe" id="listIframe" class="expandable border" style="width: 100%; height: 250px;" src="'.MWB.'bibliography/iframe_shelving_list.php"></iframe>'."\n";        
}
 else {
?>
<fieldset class="menuBox">
  <div class="menuBoxInner systemIcon">
    <div class="per_title">
      <h2><?php echo __('Shelving'); ?></h2>
    </div>
    <div class="infoBox">
      <?php echo __('SHELVING - Pindah lokasi item sesuai perubahan rak terbaru Plugin Oleh Kak Heru Subekti'); ?>
    </div>
  </div>
</fieldset>
<?php
    // create new instance
    $form = new simbio_form_table_AJAX('mainForm', $_SERVER['PHP_SELF'], 'post');
    $form->submit_button_attr = 'name="start" value="'.__('Start Shelving').'" class="btn btn-primary"';

    // form table attributes
    $form->table_attr = 'align="center" id="dataList" cellpadding="5" cellspacing="0"';
    $form->table_header_attr = 'class="alterCell" style="font-weight: bold;"';
    $form->table_content_attr = 'class="alterCell2"';

    $loc_q = $dbs->query('SELECT l.location_id, l.location_name FROM mst_location l');
    $loc_options = array();
    while ($loc_d = $loc_q->fetch_row()) {
        $loc_options[] = array($loc_d[0], $loc_d[1]);
    }
    $form->addSelectList('location', __('Location'), $loc_options);

    $sloc_q = $dbs->query('SELECT DISTINCT site FROM item WHERE site !=""');
    $sloc_options = array();
    while ($sloc_d = $sloc_q->fetch_row()) {
        $sloc_options[] = array($sloc_d[0],$sloc_d[0]);
    }
    $form->addSelectList('shelf_location', __('Shelf Location'), $sloc_options);

    echo $form->printOut();

}
