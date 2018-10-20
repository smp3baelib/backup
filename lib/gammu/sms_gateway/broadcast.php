<?php
/**
 *
 * Copyright (C) 2007,2008  Arie Nugraha (dicarve@yahoo.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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
/* Broadcast configuration */

// key to authenticate
define('INDEX_AUTH', '1');

// main system configuration
require '../../../sysconfig.inc.php';
// IP based access limitation
require LIB.'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-reporting');
// start the session
require SB.'admin/default/session.inc.php';
require SB.'admin/default/session_check.inc.php';
// privileges checking
$can_read = utility::havePrivilege('sms_gateway', 'r');
$can_write = utility::havePrivilege('sms_gateway', 'w');

if (!$can_read) {
    die('<div class="errorBox">'.__('You don\'t have enough privileges to access this area!').'</div>');
}

require SIMBIO.'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO.'simbio_GUI/form_maker/simbio_form_element.inc.php';
require SIMBIO.'simbio_GUI/paging/simbio_paging.inc.php';
require SIMBIO.'simbio_DB/datagrid/simbio_dbgrid.inc.php';
require MDLBS.'reporting/report_dbgrid.inc.php';

$page_title = 'Broadcast Messages';
$reportView = false;
$num_recs_show = 20;
if (isset($_GET['reportView'])) {
    $reportView = true;
}

if (!$reportView) {
?>
    <!-- filter -->
    <fieldset>
    <div class="per_title">
      <h2><?php echo __('Broadcast Message'); ?></h2>
    </div>
    <div class="infoBox">
    <?php echo __('Member Filter'); ?>
    </div>
    <div class="sub_section">
    <form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" target="reportView">
    <div id="filterForm">
        <!-- add -->
        <div class="divRow">
            <div class="divRowLabel"><?php echo __('Member Type'); ?></div>
            <div class="divRowContent">
            <?php
            $mtype_q = $dbs->query('SELECT member_type_id, member_type_name FROM mst_member_type');
            $mtype_options[] = array('0', __('ALL'));
            while ($mtype_d = $mtype_q->fetch_row()) {
                $mtype_options[] = array($mtype_d[0], $mtype_d[1]);
            }
            echo simbio_form_element::selectList('mtype[]', $mtype_options, '','multiple="multiple" size="5"');
            ?> <?php echo __('Press Ctrl and click to select multiple entries'); ?>
            </div>
        </div>

        <div class="divRow">
            <div class="divRowLabel"><?php echo __('Member ID').'/'.__('Member Name'); ?></div>
            <div class="divRowContent">
            <?php echo simbio_form_element::textField('text', 'id_name', '', 'style="width: 50%" autocomplete="off"'); ?>
            </div>
        </div>

        <div class="divRow">
            <div class="divRowLabel"><?php echo __('Pending'); ?>:</div>
            <div class="divRowContent">
            <?php
            $isPending_options[] = array('', __('None'));
            $isPending_options[] = array('no', __('Disable'));
            $isPending_options[] = array('yes', __('Enable'));
            echo simbio_form_element::selectList('isPending', $isPending_options);
            ?>
            </div>
        </div>

        <div class="divRow">
            <div class="divRowLabel"><?php echo __('Expire'); ?>:</div>
            <div class="divRowContent">
            <?php
            $expire_options[] = array('', __('None'));
            $expire_options[] = array('no', __('Disable'));
            $expire_options[] = array('yes', __('Enable'));
            echo simbio_form_element::selectList('expired', $expire_options);
            ?>
            </div>
        </div>

        <div class="divRow">
            <div class="divRowLabel"><?php echo __('Record each page'); ?></div>
            <div class="divRowContent">
                <input type="text" name="recsEachPage" size="3" maxlength="3" value="<?php echo $num_recs_show; ?>" /> <?php echo __('Set between 20 and 200'); ?>
            </div>
        </div>

    </div>
    <div style="padding-top: 10px; clear: both;">
    <input type="button" name="moreFilter" value="<?php echo __('Show More Filter Options'); ?>" />
    <input type="submit" class="btn btn-danger" name="applyFilter" value="<?php echo __('Add Queue'); ?>" />
    <?php if($can_read AND $can_write){ ?>
    <a class="notAJAX btn btn-danger openPopUp pull-right" href="<?php echo MWB; ?>sms_gateway/pop_broadcast.php" height="250" title="<?php echo __('Send Message Broadcast'); ?>"><i class="glyphicon glyphicon-envelope"></i><span></span> Write Message</a>    
    <input type="hidden" name="reportView" value="true" />
    <?php } ?>
    </div>
    </form>
    </div>
    </fieldset>
    <!-- filter end -->
    <div class="dataListHeader" style="padding: 3px;"><span id="pagingBox"></span></div>

    <iframe name="reportView" id="reportView" src="<?php echo $_SERVER['PHP_SELF'].'?reportView=true'; ?>" frameborder="0" style="width: 100%; height: 500px;"></iframe>
<?php
} else {
    ob_start();

    // table spec
    $_SESSION['phone'] = '';

    $table_spec = 'member AS m';
   // create datagrid
    $reportgrid = new report_datagrid();
    $reportgrid->setSQLColumn('m.member_id AS \''.__('Member ID').'\'', 
        'm.member_name AS \''.__('Member Name').'\'',
        'if(m.is_pending=0,"Active","Pending") AS \''.__('Status').'\'',
        'm.expire_date AS \''.__('Expire').'\'',
        'm.member_phone AS \''.__('Member Phone').'\'');
    $reportgrid->setSQLorder('m.member_id DESC');
    $reportgrid->sql_group_by = 'm.member_id';

    $broadcast_criteria = '(m.member_phone !="" AND m.member_phone IS NOT NULL AND m.member_phone!=" ")';

    if (isset($_GET['mtype'])) {
        $member_type_IDs = '';
        foreach ($_GET['mtype'] as $id) {
            $id = (integer)$id;
            if ($id) {
                $member_type_IDs .= "$id,";
            }
        }
        $member_type_IDs = substr_replace($member_type_IDs, '', -1);
        if ($member_type_IDs) {
            $broadcast_criteria .= " AND m.member_type_id IN($member_type_IDs)";
        }
    }   
     
    // other options
    if (isset($_GET['id_name']) AND !empty($_GET['id_name'])) {
        $id_name = $dbs->escape_string($_GET['id_name']);
        $broadcast_criteria  .= ' AND (m.member_id LIKE \''.$id_name.'%\' OR m.member_name LIKE \'%'.$id_name.'%\')';
    }

    if (isset($_GET['isPending']) AND !empty($_GET['isPending'])) {
        $isPending = $dbs->escape_string($_GET['isPending']);
        $is_Pending = $isPending=='no'?0:1;
        $broadcast_criteria .= ' AND m.is_pending LIKE \'%'.$is_Pending.'%\'';
    }

    if (isset($_GET['expired']) AND !empty($_GET['expired'])) {
        $expired = $dbs->escape_string($_GET['expired']);
        if($expired=='no'){
        $broadcast_criteria .= ' AND m.expire_date > curdate()';
        }
        else{
        $broadcast_criteria .= ' AND m.expire_date < curdate()';
        }
    }

    if (isset($_GET['recsEachPage'])) {
        $recsEachPage = (integer)$_GET['recsEachPage'];
        $num_recs_show = ($recsEachPage >= 5 && $recsEachPage <= 200)?$recsEachPage:$num_recs_show;
    }
    $reportgrid->setSQLCriteria($broadcast_criteria);

if(isset($_GET['applyFilter'])){
    $pn = $dbs->query('SELECT m.member_name, m.member_phone FROM '.$table_spec.' WHERE '.$broadcast_criteria);
    $a = 1;
    while ($pp = $pn->fetch_row()) {
        $_SESSION['phone'][$a] = array($pp[1],$pp[0]);
        $a++;
    }
}
    // set table and table header attributes
    
    $reportgrid->table_header_attr = 'class="dataListHeader" style="font-weight: bold;"';
    $reportgrid->column_width = array('0'=>'5%','1' => '30%', '2'=>'5%','3'=>'5%');
    $reportgrid->table_attr = 'align="center" class="dataListPrinted" cellpadding="5" cellspacing="0" ';
    // put the result into variables

    echo $reportgrid->createDataGrid($dbs, $table_spec, $num_recs_show);

    echo '<script type="text/javascript">'."\n";
    echo 'parent.$(\'#pagingBox\').html(\''.str_replace(array("\n", "\r", "\t"), '', $reportgrid->paging_set).'\');'."\n";
    echo '</script>';

    $content = ob_get_clean();

    // include the page template
    require SB.'/admin/'.$sysconf['admin_template']['dir'].'/printed_page_tpl.php';
}
