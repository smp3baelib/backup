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
/* by Heru Subekti,2014 (heroe_soebekti@yahoo.co.id)
/* new message application */

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

// privileges checking
$can_read = utility::havePrivilege('sms_gateway', 'r');
$can_write = utility::havePrivilege('sms_gateway', 'w');

if (!$can_read) {
    die('<div class="errorBox">'.__('You don\'t have enough privileges to access this area!').'</div>');
}

require SB.'admin/default/session_check.inc.php';
require SIMBIO.'simbio_FILE/simbio_directory.inc.php';
require SIMBIO.'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require SIMBIO.'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO.'simbio_DB/simbio_dbop.inc.php';



?>
<fieldset class="menuBox">
  <div class="menuBoxInner systemIcon">
    <div class="per_title">
      <h2><?php echo __('Message'); ?></h2>
    </div>
  </div>
</fieldset>
<?php

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
        else
            $hp = $nohp;
    }
    return $hp;
}


if (isset($_POST['sentData'])) {
    if (empty($_POST['number']) OR empty($_POST['msg'])) {
        utility::jsAlert(__('Field can\'t empty !')); //mfc
        exit();
    }

//GET SETTING VALUE
        $number = explode(' -- ', $_POST['number']);   
        $data['DestinationNumber'] = $number==''?$_POST['number']:$number[0];
        $data['TextDecoded'] =  trim($dbs->escape_string(strip_tags($_POST['msg'])));
        $data['SendingDateTime'] = date("Y-m-d H:i:s");
        $data['SenderID'] = 'SLiMS_Gateway';
        $data['CreatorID'] = 'Senayan Library';

        // create sql op object
        $sql_op = new simbio_dbop($dbs);

/* INSERT RECORD MODE */
// insert the data          
            $insert = $sql_op->insert('outbox', $data);
            if ($insert) {
                utility::jsAlert(__('Message Ready Sent!'));
                echo '<script type="text/javascript">parent.jQuery(\'#mainContent\').simbioAJAX(\''.$_SERVER['PHP_SELF'].'\');</script>';
            } else { utility::jsAlert(__('Message can\'t Sent. Please Contact System Administrator')."\nDEBUG : ".$sql_op->error); }
            exit();
}



// content message
// create new instance
$form = new simbio_form_table_AJAX('mainForm', $_SERVER['PHP_SELF'], 'post');
$form->submit_button_attr = 'name="sentData" value="'.__('Sent Message').'" class="button"';
// form table attributes
$form->table_attr = 'align="center" id="dataList" cellpadding="5" cellspacing="0"';
$form->table_header_attr = 'class="alterCell" style="font-weight: bold;"';
$form->table_content_attr = 'class="alterCell2"';

//number destination
$form->addTextField('textarea', 'msg', 'Content Message <br/><div id="remaining">characters remaining 158</div>',$s,'id="msg" style="width:90%"');
// create AJAX drop down
    $ajaxDD = new simbio_fe_AJAX_select();
    $ajaxDD->element_name = 'number';
    $s = isset($_GET['p'])?$ajaxDD->element_value = $_GET['p']:'';   
    $ajaxDD->element_css_class = 'ajaxInputField textField';
    $ajaxDD->handler_URL = MWB.'sms_gateway/phone_AJAX_response.php';
$form->addAnything('Number Destination', $ajaxDD->out());

// print out the object

echo $form->printOut();

/* main content end */

?>
<style type="text/css">
.textField{ width:850px;}</style>
<script type="text/javascript">
    $(document).ready(function(){
        $("#msg").keyup(function(){
            if(this.value.length>158){
                        this.value = this.value.substring(0,158);
            }
         $("#remaining").text("characters remaining " + (158 - this.value.length));
        })
    })  
</script>