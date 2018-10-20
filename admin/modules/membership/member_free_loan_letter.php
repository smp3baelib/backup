<?php
/**
 * Copyright (C) 2007,2008  Arie Nugraha (dicarve@yahoo.com)
 * Some modifications by Drajat Hasan 2017 (drajat@feraproject.wc.lt)
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

/* Member Free Loan Letter Print v1.2 */

// key to authenticate
define('INDEX_AUTH', '1');

// main system configuration
require '../../../sysconfig.inc.php';
// IP based access limitation
require LIB.'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-membership');
// start the session
require SB.'admin/default/session.inc.php';
require SB.'admin/default/session_check.inc.php';
require SIMBIO.'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO.'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require SIMBIO.'simbio_GUI/paging/simbio_paging.inc.php';
require SIMBIO.'simbio_DB/datagrid/simbio_dbgrid.inc.php';
require SIMBIO.'simbio_DB/simbio_dbop.inc.php';

// privileges checking
$can_read = utility::havePrivilege('membership', 'r');

if (!$can_read) {
    die('<div class="errorBox">You dont have enough privileges to view this section</div>');
}

// local settings
$max_print = 1;
// local date format
$sysconf['month'] = date('n');
$sysconf['array_ina_month_format'] =  array('1' => 'Januari','2' => 'Februari','3' => 'Maret','4' => 'April','5' => 'Mei','6' => 'Juni','7' => 'Juli', '8' => 'Agustus','9' => 'September','10' => 'Oktober','11' => 'November','12' => 'Desember');
$sysconf['month_full'] = date('d')."&nbsp;".$sysconf['array_ina_month_format'][$sysconf['month']]."&nbsp;".date("Y");

// clean print queue
if (isset($_GET['action']) AND $_GET['action'] == 'clear') {
    // update print queue count object
    echo '<script type="text/javascript">parent.$(\'#queueCount\').html(\'0\');</script>';
    utility::jsAlert(__('Print queue cleared!'));
    unset($_SESSION['fll']);
    exit();
}

if (isset($_POST['itemID']) AND !empty($_POST['itemID']) AND isset($_POST['itemAction'])) {
    if (!$can_read) {
        die();
    }
    if (!is_array($_POST['itemID'])) {
        // make an array
        $_POST['itemID'] = array($_POST['itemID']);
    }
    // loop array
    if (isset($_SESSION['fll'])) {
        $print_count = count($_SESSION['fll']);
    } else {
        $print_count = 0;
    }
    // card size
    $size = 2;
    // create AJAX request
    echo '<script type="text/javascript" src="'.JWB.'jquery.js"></script>';
    echo '<script type="text/javascript">';
    // loop array
    foreach ($_POST['itemID'] as $itemID) {
        if ($print_count == $max_print) {
            $limit_reach = true;
            break;
        }
        if (isset($_SESSION['fll'][$itemID])) {
            continue;
        }
        if (!empty($itemID)) {
            $card_text = trim($itemID);
            //echo '$.ajax({url: \''.SWB.'lib/phpbarcode/barcode.php?code='.$card_text.'&encoding='.$sysconf['barcode_encoding'].'&scale='.$size.'&mode=png\', type: \'GET\', error: function() { alert(\'Error creating member card!\'); } });'."\n";
            // add to sessions
            $_SESSION['fll'][$itemID] = $itemID;
            $print_count++;
        }
    }
    echo '</script>';
    if (isset($limit_reach)) {
        $msg = str_replace('{max_print}', $max_print, __('Selected items NOT ADDED to print queue. Only {max_print} can be printed at once')); //mfc
        utility::jsAlert($msg);
    } else {
        // update print queue count object
        echo '<script type="text/javascript">parent.$(\'#queueCount\').html(\''.$print_count.'\');</script>';
        utility::jsAlert(__('Selected items added to print queue'));
    }
    exit();
}

// Function to show number on letter
function counter(){
$handle = fopen('../../../files/freeloan/loanquee.txt', "r");
  if(!$handle){
    utility::jsAlert(__('File Not Found!'));
  } else {
    $counter = (int) fread($handle,20);
    fclose ($handle);
    $counter++;
    $handle = fopen('../../../files/freeloan/loanquee.txt', "w");
    fwrite($handle,$counter) ;
    fclose ($handle) ;  
  }
}

function reseter(){
$handle = fopen('../../../files/freeloan/loanquee.txt', "r");
  if(!$handle){
    utility::jsAlert(__('File Not Found!'));
  } else {
    $counter = (int) fread($handle,20);
    fclose ($handle);
    $counter++;
    $handle = fopen('../../../files/freeloan/loanquee.txt', "w");
    fwrite($handle,"0") ;
    fclose ($handle) ;  
  }
}


$show = fopen("../../../files/freeloan/loanquee.txt", "r");
  if (!$show) {
    utility::jsAlert(__('Can not read file!'));
  } else {
    $see = (int ) fread($show,20);
    $seepp = $see+1;
    $sysconf['number'] = substr('0000'.$seepp,-3,3);
  } 
// Reset
if (isset($_GET['action']) AND $_GET['action'] == 'reset') {
    reseter();
}
// card pdf download
if (isset($_GET['action']) AND $_GET['action'] == 'print') {
    // check if label session array is available
    if (!isset($_SESSION['fll'])) {
        utility::jsAlert(__('There is no data to print!'));
        die();
    }
    if (count($_SESSION['fll']) < 1) {
        utility::jsAlert(__('There is no data to print!'));
        die();
    }
    // concat all ID together
    $member_ids = '';
    foreach ($_SESSION['fll'] as $id) {
        $member_ids .= '\''.$id.'\',';
    }
    // strip the last comma
    $member_ids = substr_replace($member_ids, '', -1);
    // send query to database
    /*$member_q = $dbs->query('SELECT m.member_name, m.member_id, m.member_image, mt.member_type_name FROM member AS m
        LEFT JOIN mst_member_type AS mt ON m.member_type_id=mt.member_type_id
        WHERE m.member_id IN('.$member_ids.')'); */
	/*
	member_id 	member_name 	member_image member_type_id 	member_address 	member_mail_address 	member_email 	postal_code 	inst_name 	 	 	member_phone 	member_since_date 	register_date 	expire_date 	input_date

	*/

	$member_q = $dbs->query('SELECT m.member_essay, m.member_name, m.member_id, m.member_image, m.member_address, m.member_email, m.inst_name, m.postal_code, m.pin, m.member_phone, m.expire_date, m.register_date, mt.member_type_name FROM member AS m
        LEFT JOIN mst_member_type AS mt ON m.member_type_id=mt.member_type_id
        WHERE m.member_id IN('.$member_ids.')');
    $member_datas = array();
    while ($member_d = $member_q->fetch_assoc()) {
        if ($member_d['member_id']) {
            $member_datas[] = $member_d;
        }
    }

    // include printed settings configuration file
    include SB.'admin'.DS.'admin_template'.DS.'printed_settings.inc.php';
    // check for custom template settings
    $custom_settings = SB.'admin'.DS.$sysconf['admin_template']['dir'].DS.$sysconf['template']['theme'].DS.'printed_settings.inc.php';
    if (file_exists($custom_settings)) {
        include $custom_settings;
    }

	  // load print settings from database to override value from printed_settings file
    loadPrintSettings($dbs, 'freeloan');

    // chunk cards array
    $chunked_card_arrays = array_chunk($member_datas, $sysconf['print']['freeloan']['items_per_row']);
    // create html ouput
    // Origin html template by Jushadi Arman Saz
    // Some modification by Drajat Hasan
    $html_str = '<!DOCTYPE html>'."\n";
    $html_str .= '<html><head><title>Free Loan Letter By Drajat Hasan</title>'."\n";
    $html_str .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    $html_str .= '<meta http-equiv="Pragma" content="no-cache" /><meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, post-check=0, pre-check=0" /><meta http-equiv="Expires" content="Fry, 02 Oct 2012 12:00:00 GMT" />';
    $html_str .= '<style type="text/css">'."\n";
    $html_str .= 'body {margin: 0;padding: 0;}'."\n";
    $html_str .= '.letter {width: 21cm;height: 29,7cm;padding-bottom: 20px;margin-left: auto;margin-right: auto;}'."\n";
    $html_str .= '.letter-header {width: 100%;border-bottom: 4px solid black;}'."\n";
    $html_str .= '.letter-header img {width: 4cm;height: 4cm;margin-left: 20px;}'."\n";
    $html_str .= '.letter-header h4 {margin: 0;text-align: center;font-size: 18pt;width: 100%;}'."\n";
    $html_str .= '.letter-header h5 {margin: 0;text-align: center;font-size: 18pt;width: 100%;}'."\n";
    $html_str .= '.letter-header h6,h7 {margin: 0;text-align: center;font-size: 14pt;width: 100%;}'."\n";
    $html_str .= '.letter-header .identity {margin-bottom: 58px;}'."\n";
    $html_str .= '.letter-content {width: 100%;}'."\n";
    $html_str .= '.letter-content h3 {text-decoration: underline;font-weight: bold;}'."\n";
    $html_str .= '.letter-content .caption {z-index:9;float:left;width: 100px;text-align: left;font-weight: bold;}'."\n";
    $html_str .= '.letter-content .official-number-letter {text-align:center; display:block; font-weight: bold}'."\n";
    $html_str .= '.letter-content .official-declaration-letter {text-align:justify; margin-left:100px; margin-right: 100px;font-size: 14pt;}'."\n";
    $html_str .= '.letter-content .official-result-letter {text-align:justify; margin-left:100px; margin-right: 100px;padding-top: 20px;font-size: 14pt;}'."\n";
    $html_str .= '.letter-content .official-head-library-signature-date {width: 15.7cm;text-align:right; margin-left:100px; margin-right: 21cm;padding-top: 20px;font-size: 14pt;}'."\n";
    $html_str .= '.letter-content .official-head-library-signature-division {width: 15,7cm;text-align:right; margin-left:100px; margin-right: 100px;padding-top: 5px;font-size: 14pt;}'."\n";
    $html_str .= '.letter-content .official-head-library-signature-name {width: 15,7cm;text-align:right; margin-right: 100px;padding-top: 80px;font-size: 14pt;}'."\n";
    $html_str .= '.letter-content .official-head-library-signature-id {width: 15,7cm;text-align:right; margin-right: 100px;padding-top: 5px;font-size: 14pt;}'."\n";
    $html_str .= '.letter-content .person-identity {text-align:justify; margin-left: 100px; margin-right: 100px;font-weight: bold;font-size: 14pt;}'."\n";
    $html_str .= '.letter-content .person-identity .address {z-index: 4;float: left;width: 480px;margin-bottom: 0px;margin-left: 3px;text-align: justify;}'."\n";
    $html_str .= '.letter-content .person-identity .phone {z-index: 4;float: left;width: 480px;margin-bottom: 0px;margin-left: 3px;}'."\n";
    $html_str .= '</style>'."\n";
    $html_str .= '</head>'."\n";
    $html_str .= '<body>'."\n";
    $html_str .= '<a href="#" onclick="window.print()">Print Again</a><br /><br />'."\n";
    $html_str .= '<div class="letter">'."\n";
    $html_str .= '<div class="letter-header">'."\n";
    // loop the chunked arrays to row
    foreach ($chunked_card_arrays as $membercard_rows) {
        foreach ($membercard_rows as $card) {
          // Check Essay
          if (is_null($card['member_essay'])) {
            $sysconf['essay'] = '<font style="color: red;">Belum mengumpulkan judul skripsi</font>';
          } else {
            $sysconf['essay'] = $card['member_essay'];
          }
          //$html_str .= '<thead>';
          $html_str .= '<table width="100%" style="margin: 0; padding: 0;" cellspacing="0" cellpadding="0">'."\n";
          $html_str .= '<thead>'."\n";
          $html_str .= '<th rowspan="9"><img src="'.SWB.'files/freeloan/'.$sysconf['print']['freeloan']['logo_surat'].'"></th>';
          $html_str .= '<th class="identity">';
          $html_str .= '<h4>'.$sysconf['print']['freeloan']['header1_text'].'</h4>';
          $html_str .= '<h5>'.$sysconf['print']['freeloan']['header2_text'].'</h5>';
          $html_str .= '<h5>'.$sysconf['print']['freeloan']['header3_text'].'</h5>';
          $html_str .= '<h6>'.$sysconf['print']['freeloan']['header4_text'].'</h6>'; 
          $html_str .= '<h7>'.$sysconf['print']['freeloan']['header5_text'].'</h6>';
          $html_str .= '</th>';
          $html_str .= '</thead>';
          $html_str .= '</table>';
          $html_str .= '</div>';
          $html_str .= '<div class="letter-content">';
          $html_str .= '<h3 style="text-align: center;">'.$sysconf['print']['freeloan']['caption_letter'].'</h3>';
          $html_str .= '<span class="official-number-letter">Nomor : '.$sysconf['number'].$sysconf['print']['freeloan']['number_format'].$sysconf['print']['freeloan']['institute']."/".$sysconf['print']['freeloan']['period']."/".$sysconf['print']['freeloan']['year'].'</span>';
          $html_str .= '<p class="official-declaration-letter">'.$sysconf['print']['freeloan']['declare_letter'].'</p>';
          $html_str .= '<p class="person-identity"><label class="caption">Nama</label><span>:</span><span class="name">&nbsp;'.$card['member_name'].'</span></p>';
          $html_str .= '<p class="person-identity"><label class="caption">NIM</label><span>:</span><span class="no-identity">&nbsp;'.$card['member_id'].'</span></p>';
          $html_str .= '<p class="person-identity"><label class="caption">Alamat</label><span style="float:left">:</span><span class="address">'.$card['member_address'].'</span></p>';          
          $html_str .= '<br>';
          $html_str .= '<br>';
          $html_str .= '<p class="person-identity"><label class="caption">Judul Skripsi</label><span style="float:left">:</span><span class="phone">'.$sysconf['essay'].'</span></p>';
          $html_str .= '<br>';
          $html_str .= '<p class="official-result-letter">'.$sysconf['print']['freeloan']['result_letter'].'</p>';
          $html_str .= '<p class="official-head-library-signature-date"><span style="float:right">'.$sysconf['print']['freeloan']['city'].', '.$sysconf['month_full'].'</span></p>';  
          $html_str .= '<p class="official-head-library-signature-division"><span style="float:right">'.$sysconf['print']['freeloan']['division_of_signature'].'</span></p>';
          $html_str .= '<p class="official-head-library-signature-name"><span style="float:right">'.$sysconf['print']['freeloan']['name_of_signature'].'</span></p>';
          $html_str .= '<p class="official-head-library-signature-id"><span style="float:right">NIK: '.$sysconf['print']['freeloan']['id_of_signature'].'</span></p>';  
          $html_str .= '</div>';
        }
        //$html_str .= '<tr>'."\n";
    }
    //$html_str .= '</table>'."\n";
    $html_str .= '<script type="text/javascript">self.print();</script>'."\n";
    $html_str .= '</body></html>'."\n";
    // unset the session
    unset($_SESSION['fll']);
    // Counter
    counter();
    // write to file
    $print_file_name = 'member_free_loan_letter_gen_print_result_'.strtolower(str_replace(' ', '_', $_SESSION['uname'])).'.html';
    $file_write = @file_put_contents(UPLOAD.$print_file_name, $html_str);
    if ($file_write) {
        // update print queue count object
        echo '<script type="text/javascript">parent.$(\'#queueCount\').html(\'0\');</script>';
        // open result in window
        echo '<script type="text/javascript">top.jQuery.colorbox({href: "'.SWB.FLS.'/'.$print_file_name.'", iframe: true, width: 800, height: 500, title: "'.__('Free Loan Letter Printing').'"})</script>';
    } else { utility::jsAlert('ERROR! Cards failed to generate, possibly because '.SB.FLS.' directory is not writable'); }
    exit();
}

?>
<fieldset class="menuBox">
<div class="menuBoxInner printIcon">
	<div class="per_title">
    	<h2><?php echo __('Free Loan Letter Printing'); ?></h2>
    </div>
	<div class="sub_section">
		<div class="btn-group">
		<a target="blindSubmit" href="<?php echo MWB; ?>membership/member_free_loan_letter.php?action=clear" class="notAJAX btn btn-default" style="color: #f00;"><i class="glyphicon glyphicon-trash"></i>&nbsp;<?php echo __('Clear Print Queue'); ?></a>
		<a target="blindSubmit" href="<?php echo MWB; ?>membership/member_free_loan_letter.php?action=print" class="notAJAX btn btn-default"><i class="glyphicon glyphicon-print"></i>&nbsp;<?php echo __('Cetak Surat Bebas Peminjaman'); ?></a>
		<a target="blindSubmit" href="<?php echo MWB; ?>membership/member_free_loan_letter.php?action=reset" class="notAJAX btn btn-default" style="color: #f00;"><i class="glyphicon glyphicon-trash"></i>&nbsp;<?php echo __('Reset Nomor'); ?></a>
        <a href="<?php echo MWB; ?>bibliography/pop_print_settings.php?type=freeloan" class="notAJAX btn btn-default openPopUp" title="<?php echo __('Setelan Surat Bebas Peminjaman'); ?>"><i class="glyphicon glyphicon-wrench"></i></a>
    </div>
	    <form name="search" action="<?php echo MWB; ?>membership/member_free_loan_letter.php" id="search" method="get" style="display: inline;"><?php echo __('Search'); ?>:
	    <input type="text" name="keywords" size="30" />
	    <input type="submit" id="doSearch" value="<?php echo __('Search'); ?>" class="button" />
	    </form>
    </div>
    <div class="infoBox">
    <?php
    echo __('Maximum').' <font style="color: #f00">'.$max_print.'</font> '.__('records can be printed at once. Currently there is').' '; //mfc
    if (isset($_SESSION['fll'])) {
        echo '<font id="queueCount" style="color: #f00">'.count($_SESSION['fll']).'</font>';
    } else { echo '<font id="queueCount" style="color: #f00">0</font>'; }
    echo ' '.__('in queue waiting to be printed.'); //mfc
    ?>
    </div>
</div>
</fieldset>
<?php
/* search form end */
/* ITEM LIST */
// table spec
$table_spec = 'member AS m
    LEFT JOIN mst_member_type AS mt ON m.member_type_id=mt.member_type_id';
// create datagrid
$datagrid = new simbio_datagrid();
$datagrid->setSQLColumn('m.member_id',
    'm.member_id AS \''.__('Member ID').'\'',
    'm.member_name AS \''.__('Member Name').'\'',
    'SUBSTRING(`member_essay`, 1, 20) AS \''.__('Judul Skripsi').'\''); // Length of the titile is limit for 20 character
$datagrid->setSQLorder('m.last_update DESC');
// is there any search
if (isset($_GET['keywords']) AND $_GET['keywords']) {
    $keyword = $dbs->escape_string(trim($_GET['keywords']));
    $words = explode(' ', $keyword);
    if (count($words) > 1) {
        $concat_sql = ' (';
        foreach ($words as $word) {
            $concat_sql .= " (m.member_id LIKE '%$word%' OR m.member_name LIKE '%$word%'";
        }
        // remove the last AND
        $concat_sql = substr_replace($concat_sql, '', -3);
        $concat_sql .= ') ';
        $datagrid->setSQLCriteria($concat_sql);
    } else {
        $datagrid->setSQLCriteria("m.member_id LIKE '%$keyword%' OR m.member_name LIKE '%$keyword%'");
    }
}
// set table and table header attributes
$datagrid->table_attr = 'align="center" id="dataList" cellpadding="5" cellspacing="0"';
$datagrid->table_header_attr = 'class="dataListHeader" style="font-weight: bold;"';
// edit and checkbox property
$datagrid->edit_property = false;
$datagrid->chbox_property = array('itemID', __('Add'));
$datagrid->chbox_action_button = __('Add To Print Queue');
$datagrid->chbox_confirm_msg = __('Add to print queue?');
$datagrid->column_width = array('10%', '70%', '15%');
// set checkbox action URL
$datagrid->chbox_form_URL = $_SERVER['PHP_SELF'];
// put the result into variables
$datagrid_result = $datagrid->createDataGrid($dbs, $table_spec, 20, $can_read);
if (isset($_GET['keywords']) AND $_GET['keywords']) {
    echo '<div class="infoBox">'.__('Found').' '.$datagrid->num_rows.' '.__('from your search with keyword').': "'.$_GET['keywords'].'"</div>'; //mfc
}
echo $datagrid_result;
/* main content end */
