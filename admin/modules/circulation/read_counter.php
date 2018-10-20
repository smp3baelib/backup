<?php
/**
* ---------------------
* Plugin	    : Read Counter
* Create Date : November,11-12 2015
* Author      : Eddy Subratha (eddy.subratha{at}gmail.com), 
* Syam Suryanto (syamsuryanto@gmail.com) Erwan Setyo Budi (erwans818@gmail.com)
* --------------------------------------------
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

/* Read Counter Management section */

// key to authenticate
define('INDEX_AUTH', '1');
// key to get full database access
define('DB_ACCESS', 'fa');

// main system configuration
require '../../../sysconfig.inc.php';
// IP based access limitation
require LIB.'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-masterfile');
// start the session
require SB.'admin/default/session.inc.php';
require SB.'admin/default/session_check.inc.php';
require SIMBIO.'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO.'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require SIMBIO.'simbio_GUI/paging/simbio_paging.inc.php';
require SIMBIO.'simbio_DB/datagrid/simbio_dbgrid.inc.php';
require SIMBIO.'simbio_DB/simbio_dbop.inc.php';

// privileges checking
$can_read = utility::havePrivilege('circulation', 'r');
$can_write = utility::havePrivilege('circulation', 'w');

if (!$can_read) {
    die('<div class="errorBox">'.__('You don\'t have enough privileges to view this section').'</div>');
}

/* RECORD OPERATION */
if (isset($_POST['saveData'])) {
    $collTypeName = trim(strip_tags($_POST['collTypeName']));
    // check form validity
    if (empty($collTypeName)) {
        utility::jsAlert(__('Item ID can\'t be empty'));
        exit();
    } else {
        $data['item_id'] = $dbs->escape_string($collTypeName);
        $data['update_at'] = date('Y-m-d H:i:s');

        // create sql op object
        $sql_op = new simbio_dbop($dbs);
        if (isset($_POST['updateRecordID'])) {
            /* UPDATE RECORD MODE */
            // remove input date
            unset($data['input_date']);
            // filter update record ID
            $updateRecordID = (integer)$_POST['updateRecordID'];
            // update the data
            $update = $sql_op->update('read_counter', $data, 'read_id='.$updateRecordID);
            if ($update) {
                utility::jsAlert(__('ID Item berhasil berubah..!!!'));
                echo '<script type="text/javascript">parent.jQuery(\'#mainContent\').simbioAJAX(parent.jQuery.ajaxHistory[0].url);</script>';
            } else { utility::jsAlert(__('ID Item gagal di ubah. Please Contact System Administrator')."\nDEBUG : ".$sql_op->error); }
            exit();
        } else {
            /* INSERT RECORD MODE */
            // insert the data
            $insert = $sql_op->insert('read_counter', $data);
            if ($insert) {
               // utility::jsAlert(__('New Read Counter Data Successfully Saved'));
                echo '<script type="text/javascript">parent.jQuery(\'#mainContent\').simbioAJAX(\''.$_SERVER['PHP_SELF'].'\');</script>';
            } else { utility::jsAlert(__('Author Data FAILED to Save. Please Contact System Administrator')."\nDEBUG : ".$sql_op->error); }
            exit();
        }
    }
    exit();
} else if (isset($_POST['itemID']) AND !empty($_POST['itemID']) AND isset($_POST['itemAction'])) {
    if (!($can_read AND $can_write)) {
        die();
    }
    /* DATA DELETION PROCESS */
    $sql_op = new simbio_dbop($dbs);
    $failed_array = array();
    $error_num = 0;
    if (!is_array($_POST['itemID'])) {
        // make an array
        $_POST['itemID'] = array((integer)$_POST['itemID']);
    }
    // loop array
    foreach ($_POST['itemID'] as $itemID) {
        $itemID = (integer)$itemID;
        // check if this item data still have an item
        // $item_q = $dbs->query('SELECT ct.read_id, COUNT(item_id) FROM item AS i
        //     LEFT JOIN mst_coll_type AS ct ON i.coll_type_id=ct.coll_type_id
        //     WHERE i.coll_type_id='.$itemID.' GROUP BY i.coll_type_id');
        // $item_d = $item_q->fetch_row();
        // if ($item_d[1] < 1) {
            if (!$sql_op->delete('read_counter', "read_id=$itemID")) {
                $error_num++;
            }
        // } else {
        //     $msg = str_replace('{item_name}', $item_d[0], __('Location ({item_name}) still used by {number_items} item(s)')); //mfc
        //     $msg = str_replace('{number_items}', $item_d[1], $msg);
        //     $still_have_item[] = $msg;
        //     $error_num++;
        // }
    }

    if ($still_have_item) {
        $undeleted_coll_types = '';
        foreach ($still_have_item as $coll_type) {
            $undeleted_coll_types .= $coll_type."\n";
        }
        utility::jsAlert(__('Below data can not be deleted:').$undeleted_coll_types);
        exit();
    }
    // error alerting
    if ($error_num == 0) {
        utility::jsAlert(__('All Data Successfully Deleted'));
        echo '<script type="text/javascript">parent.jQuery(\'#mainContent\').simbioAJAX(\''.$_SERVER['PHP_SELF'].'?'.$_POST['lastQueryStr'].'\');</script>';
    } else {
        utility::jsAlert(__('Some or All Data NOT deleted successfully!\nPlease contact system administrator'));
        echo '<script type="text/javascript">parent.jQuery(\'#mainContent\').simbioAJAX(\''.$_SERVER['PHP_SELF'].'?'.$_POST['lastQueryStr'].'\');</script>';
    }
    exit();
}
/* RECORD OPERATION END */

/* search form */
?>
<fieldset class="menuBox">
<div class="menuBoxInner masterFileIcon">
	<div class="per_title">
	    <h2><?php echo __('Baca Di Tempat '); ?></h2>
  </div>
	<div class="sub_section">
	  <div class="btn-group">
      <a href="<?php echo MWB; ?>circulation/read_counter.php?action=detail" class="btn btn-default"><i class="glyphicon glyphicon-list-alt"></i>&nbsp;<?php echo __('Read Counter List'); ?></a>
      <a href="<?php echo MWB; ?>circulation/read_counter.php" class="btn btn-default"><i class="glyphicon glyphicon-plus"></i>&nbsp;<?php echo __('Add New Read Counter'); ?></a>
	  </div>
    <form name="search" action="<?php echo MWB; ?>circulation/read_counter.php" id="search" method="get" style="display: inline;"><?php echo __('Search'); ?> :
    <input type="text" name="keywords" size="30" />
    <input type="hidden" name="action" value="detail" />
    <input type="submit" id="doSearch" value="<?php echo __('Search'); ?>" class="button" />
    </form>
  </div>
</div>
</fieldset>
<?php
/* search form end */
/* main content */
if (isset($_POST['detail']) OR (isset($_GET['action']) AND $_GET['action'] == 'detail')) {
   /* COLLECTION TYPE LIST */
    // table spec
    $table_spec = 'read_counter AS ct LEFT JOIN item i ON i.item_code = ct.item_id LEFT JOIN biblio b ON b.biblio_id = i.biblio_id';

    // create datagrid
    $datagrid = new simbio_datagrid();
    if ($can_read AND $can_write) {
        $datagrid->setSQLColumn(
            'ct.read_id', 
            'ct.item_id AS \''.__('Kode Barcode/ No.Inv').'\'',
            'b.title AS \''.__('Judul').'\'',
            'b.classification AS \''.__('Klasifikasi').'\'',
            'ct.update_at AS \''.__('Tanggal Baca').'\'');
    } else {
        $datagrid->setSQLColumn('ct.update_at AS \''.__('Kode Item').'\'', 'ct.update_at AS \''.__('Last Update').'\'');
    }
    $datagrid->setSQLorder('ct.update_at DESC');

    // change the record order
    if (isset($_GET['fld']) AND isset($_GET['dir'])) {
        $datagrid->setSQLorder("'".urldecode($_GET['fld'])."' ".$dbs->escape_string($_GET['dir']));
    }

    // is there any search
    if (isset($_GET['keywords']) AND $_GET['keywords']) {
       $keywords = $dbs->escape_string($_GET['keywords']);
       $datagrid->setSQLCriteria("b.title LIKE '%$keywords%'");
    }

    // set table and table header attributes
    $datagrid->table_attr = 'align="center" id="dataList" cellpadding="5" cellspacing="0"';
    $datagrid->table_header_attr = 'class="dataListHeader" style="font-weight: bold;"';
    // set delete proccess URL
    $datagrid->chbox_form_URL = $_SERVER['PHP_SELF'];

    // put the result into variables
    $datagrid_result = $datagrid->createDataGrid($dbs, $table_spec, 20, ($can_read AND $can_write));
    if (isset($_GET['keywords']) AND $_GET['keywords']) {
        $msg = str_replace('{result->num_rows}', $datagrid->num_rows, __('Found <strong>{result->num_rows}</strong> from your keywords')); //mfc
        echo '<div class="infoBox">'.$msg.' : "'.$_GET['keywords'].'"</div>';
    }

    echo $datagrid_result;  
} else {
    if (!($can_read AND $can_write)) {
        die('<div class="errorBox">'.__('You don\'t have enough privileges to view this section').'</div>');
    }
    $itemID = (integer)isset($_POST['itemID'])?$_POST['itemID']:0;
    $rec_q = $dbs->query("SELECT * FROM read_counter WHERE read_id=$itemID");
    $rec_d = $rec_q->fetch_assoc();

    // create new instance
    $form = new simbio_form_table_AJAX('mainForm', $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'], 'post');
    $form->submit_button_attr = 'name="saveData" value="'.__('Save').'" class="button"';

    // form table attributes
    $form->table_attr = 'align="center" id="dataList" cellpadding="5" cellspacing="0"';
    $form->table_header_attr = 'class="alterCell" style="font-weight: bold;"';
    $form->table_content_attr = 'class="alterCell2"';

    // edit mode flag set
    if ($rec_q->num_rows > 0) {
        $form->edit_mode = true;
        // record ID for delete process
        $form->record_id = $itemID;
        // form record title
        $form->record_title = $rec_d['item_id'];
        // submit button attribute
        $form->submit_button_attr = 'name="saveData" value="'.__('Update').'" class="button"';
    }

    /* Form Element(s) */
    // coll_type_name
    $form->addTextField('text', 'collTypeName', __('Kode Item').'*', $rec_d['item_id'], 'style="width: 60%; border: solid 4px #EAF573;"');
    //$form->addTextField('textarea', 'keterangan', __('Keterangan').'*', $rec_d['item_id'], 'style="width: 60%;"');

    // edit mode messagge
    if ($form->edit_mode) {
        echo '<div class="infoBox">'.__('You are going to edit collection Read Counter').' : <b>'.$rec_d['item_id'].'</b>  <br />'.__('Last Update').$rec_d['update_at'].'</div>'; //mfc
    }
    // print out the form object
    echo $form->printOut();

}
/* main content end */
