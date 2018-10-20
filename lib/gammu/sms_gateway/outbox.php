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
/* by Heru Subekti,2014 (heroe_soebekti@yahoo.co.id) 
/* Outbox Management section */

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
$can_read = utility::havePrivilege('sms_gateway', 'r');
$can_write = utility::havePrivilege('sms_gateway', 'w');

if (!$can_read) {
    die('<div class="errorBox">'.__('You don\'t have enough privileges to access this area!').'</div>');
}

?>
<fieldset class="menuBox">
<div class="menuBoxInner masterFileIcon">
	<div class="per_title">
	    <h2><?php echo __('Outbox Messages'); ?></h2>
  </div>
	<div class="sub_section">
    <form name="search" action="<?php echo MWB; ?>sms_gateway/inbox.php" id="search" method="get" style="display: inline;"><?php echo __('Search'); ?> :
    <input type="text" name="keywords" size="30" />
    <input type="submit" id="doSearch" value="<?php echo __('Search'); ?>" class="button" />
    </form>
  </div>
</div>
</fieldset>
<?php


/* RECORD OPERATION */
    $sql_op = new simbio_dbop($dbs);
    $failed_array = array();
    $error_num = 0;

    if (isset($_POST['itemID']) AND !empty($_POST['itemID']) AND isset($_POST['itemAction'])) {
        if (!$can_read) {
            die();
        }
        foreach ($_POST['itemID'] as $itemID) {
            $itemID = (integer)$itemID;
            if (!$sql_op->delete('outbox', 'ID='.$itemID)) {
                $error_num++;
            }
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


    /* COLLECTION TYPE LIST */
    // table spec
    $table_spec = 'outbox AS i LEFT JOIN member AS m ON m.member_phone=i.DestinationNumber';

    // create datagrid
    $datagrid = new simbio_datagrid();
    if ($can_write AND $can_read ) {     
        $datagrid->setSQLColumn('i.ID',
                                'i.DestinationNumber AS \''.__('Destination Number').'\'',
        						'i.SendingDateTime AS \''.__('Date/Time').'\'',
        						'i.TextDecoded AS \''.__('Message').'\'');
    }
    else{
        $datagrid->setSQLColumn('i.DestinationNumber AS \''.__('Destination Number').'\'',
                                'i.SendingDateTime AS \''.__('Date/Time').'\'',
                                'i.TextDecoded AS \''.__('Message').'\'');        
    }   
    $datagrid->setSQLorder('i.SendingDateTime');

    // change the record order
    if (isset($_GET['fld']) AND isset($_GET['dir'])) {
        $datagrid->setSQLorder("'".urldecode($_GET['fld'])."' ".$dbs->escape_string($_GET['dir']));
    }

    // is there any search
    if (isset($_GET['keywords']) AND $_GET['keywords']) {
       $keywords = $dbs->escape_string($_GET['keywords']);
       $datagrid->setSQLCriteria("i.DestinationNumber LIKE '%$keywords%'");
    }

    // set table and table header attributes
    $datagrid->table_attr = 'align="center" id="dataList" cellpadding="5" cellspacing="0"';
    $datagrid->table_header_attr = 'class="dataListHeader" style="font-weight: bold;"';
    // edit and checkbox property
    $datagrid->edit_property = false;
    $datagrid->chbox_property = array('itemID', __('Select'));
    $datagrid->chbox_action_button = __('Delete Message');
    $datagrid->chbox_confirm_msg = __('You Sure to Delete Message?');
    // set delete proccess URL
    $datagrid->chbox_form_URL = $_SERVER['PHP_SELF'];
    //$datagrid->column_width = array(0 =>'12%', 1 => '15%', 2 => '75%');

    // put the result into variables
    $datagrid_result = $datagrid->createDataGrid($dbs, $table_spec,10, ($can_read AND $can_write));
    if (isset($_GET['keywords']) AND $_GET['keywords']) {
        $msg = str_replace('{result->num_rows}', $datagrid->num_rows, __('Found <strong>{result->num_rows}</strong> from your keywords')); //mfc
        echo '<div class="infoBox">'.$msg.' : "'.$_GET['keywords'].'"</div>';
    }

    echo $datagrid_result;
