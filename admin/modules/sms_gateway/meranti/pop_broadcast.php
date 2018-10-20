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

/* Broadcast Message*/
// modified by Heru Subekti

// key to authenticate
define('INDEX_AUTH', '1');
// key to get full database access
define('DB_ACCESS', 'fa');

if (!defined('SB')) {
  // main system configuration
  require '../../../sysconfig.inc.php';
  // start the session
  require SENAYAN_BASE_DIR.'admin/default/session.inc.php';
}
// IP based access limitation
require LIB_DIR.'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-membership');

require SENAYAN_BASE_DIR.'admin/default/session_check.inc.php';
require SIMBIO_BASE_DIR.'simbio_DB/simbio_dbop.inc.php';
require SIMBIO_BASE_DIR.'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require SIMBIO_BASE_DIR.'simbio_GUI/table/simbio_table.inc.php';

ob_start();

/* DATA INSERT PROCESS */
if (isset($_POST['sendMessage'])) {
    foreach ($_SESSION['phone'] as $key => $value) {
        $content = $_POST['member_name']=='0'?$_POST['msg']:ucfirst(strtolower($value[1])).', '.$_POST['msg'];
        $insert = $dbs->query("INSERT INTO outbox (DestinationNumber,TextDecoded,SendingDateTime,SenderID,CreatorID)
                                           VALUES ('".$value[0]."','".$content."','".date("Y-m-d H:i:s")."','SLiMS_Gateway','Senayan Library')");

    unset($_SESSION['phone'][$key]);
    }  
    utility::jsAlert('Message was successfully sent to the destination number'); 
    exit();
}

/* main content start */
?>
<div class="infoBox">
    <?php echo '<h4>'.__('Write Message').'</h4>'; ?>
</div>
<?php

if($_SESSION['phone']==''){
echo '<div class="alert alert-error">Nomor Telepon belum ditambahkan</div>';
}

else{
$form = new simbio_form_table_AJAX('mainForm', $_SERVER['PHP_SELF'], 'post');
$form->submit_button_attr = 'name="sendMessage" value="'.__('Send Messages').'" class="btn btn-default"';
$form->table_attr = 'align="center" id="dataList" cellpadding="5" cellspacing="0"';
$form->table_header_attr = 'class="alterCell" style="font-weight: bold;"';
$form->table_content_attr = 'class="alterCell2"';
$form->addTextField('textarea', 'msg', __('Message').'<br/><div id="remaining"">characters remaining 110</div>', '', 'id="msg" style="width: 90%;"');
$options[] = array('1', 'Include');
$form->addCheckBox('member_name', __('Member Name'), $options, '0');
echo $form->printOut();
}
/* main content end */
$content = ob_get_clean();
// include the page template
require SB.'/admin/'.$sysconf['admin_template']['dir'].'/notemplate_page_tpl.php';
?>

<script type="text/javascript">
    $(document).ready(function(){
        $("#msg").keyup(function(){
            if(this.value.length>110){
                        this.value = this.value.substring(0,110);
            }
         $("#remaining").text("characters remaining " + (110 - this.value.length));
        })
    })  
</script>