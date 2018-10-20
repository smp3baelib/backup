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
/* Autoreply configuration */

// key to authenticate
define('INDEX_AUTH', '1');
// key to get full database access
define('DB_ACCESS', 'fa');

if (!defined('SENAYAN_BASE_DIR')) {
    // main system configuration
    require '../../../sysconfig.inc.php';
    // start the session
    require SB.'admin/default/session.inc.php';
}
require LIB.'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-bibliography');

// privileges checking
$can_read = utility::havePrivilege('reporting', 'r');
$can_write = utility::havePrivilege('reporting', 'w');

if (!$can_read) {
    die('<div class="errorBox">'.__('You don\'t have enough privileges to access this area!').'</div>');
}

require SB.'admin/default/session_check.inc.php';
require SIMBIO.'simbio_FILE/simbio_directory.inc.php';
require SIMBIO.'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require SIMBIO.'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO.'simbio_DB/simbio_dbop.inc.php';
require LIB.'gammu/config.inc.php';

?>

<fieldset class="menuBox">
  <div class="menuBoxInner systemIcon">
    <div class="per_title">
 		<h2><?php echo __('Modify autoreply preferences'); ?></h2>
    </div>
  </div>
</fieldset>

<?php
$type = 'autoreply';
if (isset($_POST['updateData'])) {
    $setting_type = trim($_POST['settingType']);
    $setting_name = $setting_type.'_sms_settings';
    $dbs->query(sprintf("REPLACE INTO setting (setting_name, setting_value) VALUES ('%s', '%s')",
    $setting_name, $dbs->escape_string(serialize($_POST[$setting_type]))));
    echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(parent.$.ajaxHistory[0].url);</script>';
}


//load from database
utility::loadSettings($dbs);
$form = new simbio_form_table_AJAX('mainForm', $_SERVER['PHP_SELF'], 'post');
$form->submit_button_attr = 'name="updateData" value="'.__('Save Settings').'" class="button"';
$form->table_attr = 'align="center" id="dataList" cellpadding="5" cellspacing="0"';
$form->table_header_attr = 'class="alterCell" style="font-weight: bold;"';
$form->table_content_attr = 'class="alterCell2"';
$form->addHidden('settingType', $type);

//SETTING VALUE
foreach ($sysconf['autoreply_sms_settings'] as $setting_name => $val) {
    $options = null;
	$options[] = array(1, __('Enable'));
	$options[] = array(0, __('Disable'));
    
    if($setting_name == 'info_message'){
    $form->addTextField('textarea',$type.'['.$setting_name.']', ucwords(str_ireplace('_', ' ', $setting_name)), $sysconf[$type.'_sms_settings'][$setting_name]?$sysconf[$type.'_sms_settings'][$setting_name]:$val, 'rows="2" placeholder="maksimal 160 karakter" style="width: 70%;"');
    }
    else{
    $form->addSelectList($type.'['.$setting_name.']', ucwords(str_ireplace('_', ' ', $setting_name)), $options, $sysconf[$type.'_sms_settings'][$setting_name]?$sysconf[$type.'_sms_settings'][$setting_name]:$val);
       
    }
}
  
// print out the object
echo $form->printOut();

/* main content end */
