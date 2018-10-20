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

/* Global application configuration */

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

require SB.'admin/default/session_check.inc.php';
require SIMBIO.'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require LIB.'gammu/config.inc.php';

  function fsize($file){
    $a = array("B", "KB", "MB", "GB","TB", "PB");
    $pos = 0;
    $size = filesize($file);
      while ($size >= 1024){
      $size /= 1024;
      $pos++;
      }
    return round($size,2)." ".$a[$pos];
  }

$sms = new gammu();
if(isset($_POST['stop'])){
    $status = $sms->stop();
    $dbs->query("DELETE FROM phones WHERE ID='SLiMS_Gateway'");
    utility::jsAlert($status); //mfc
    echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(parent.$.ajaxHistory[0].url);</script>';
}

if(isset($_POST['start'])){
    $status = $sms->start();
    utility::jsAlert('Gammu Service Started'); 
    sleep(8);
    echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(parent.$.ajaxHistory[0].url);</script>';
}

if(isset($_POST['identify'])){
    $status = $sms->identify();
    utility::jsAlert($status); 
    echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(parent.$.ajaxHistory[0].url);</script>';
}

if(isset($_POST['clear'])){
    unlink(PATH."logsmsdrc");
    echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(parent.$.ajaxHistory[0].url);</script>';
}

?>
<fieldset class="menuBox">
  <div class="menuBoxInner systemIcon">
    <?php echo $sms->info()!=''?$sms->info():'';?>
    <div class="per_title">
      <h2><?php echo __('Gammu Status'); ?></h2>
    </div>
    <div class="infoBox">
    <div class="sub_section">
      <?php 
      $msg = ''; 
      if($sms-> smsdrc_status()==true){
        if($sms->status() == true){
            $msg  .= 'Gammu Service Running<br/>';
            $menu ='<a href="#" onclick="confSubmit(\'stopGammu\',\''.__('Stop Gammu Service?').'\')" class="button notAJAX btn btn-danger pull-right"><i class="glyphicon glyphicon-download"></i>&nbsp;'.__('Stop Gammu Service').'</a>';
            $now  = date('Y-m-d H:i');
            $set_q = $dbs->query("SELECT * FROM phones WHERE ID='SLiMS_Gateway' AND UpdatedInDB LIKE '$now%' OR TimeOut LIKE '$now%' ");
            $d = $set_q->fetch_assoc();
              if($d==NULL){
                $msg .= __('Error at init connection: Error opening device, it doesn\'t exist.<br/>Going to 30 seconds sleep because of too much connection errors ');              
              }
              else{
                $msg .= '<table>';
                $msg .= '<tr><td width="10%">'.__('Device Status').'</td><td width="1%">:</td><td width="60%">'.__('Connected').'</td>';
                $msg .= '<tr><td width="8%">IMEI</td><td width="1%">:</td><td width="60%">'.$d['IMEI'].'</td>';
                $msg .= '<tr><td width="8%">'.__('Signal').'</td><td width="1%">:</td><td width="60%">'.$d['Signal'].' dB</td>';
                $msg .= '<tr><td width="8%">'.__('Message Send').'</td><td width="1%">:</td><td width="60%">'.$d['Sent'].'</td>';
                $msg .= '<tr><td width="8%">'.__('Message Received').'</td><td width="1%">:</td><td width="60%">'.$d['Received'].'</td>';
                $msg .= '</table>';
              }
            $menu .='<form action="'.MWB.'sms_gateway/index.php" id="stopGammu" target="blindSubmit" method="post" style="display: inline;"><input type="hidden" name="stop" value="true" /></form>';
        }else{
            if ($sms->info()==''){
              $msg  .= 'Gammu Service Not Installed';
              $menu .='<a href="#" onclick="confSubmit(\'startGammu\',\''.__('Start Gammu Service?').'\')" class="button notAJAX btn btn-default"><i class="glyphicon glyphicon-download"></i>&nbsp;'.__('Start Gammu Service').'</a>';
              $menu .='<form action="'.MWB.'sms_gateway/index.php" id="startGammu" target="blindSubmit" method="post" style="display: inline;"><input type="hidden" name="start" value="true" /></form>'; }
              $menu .='<a href="#" onclick="confSubmit(\'identify\',\''.__('Check Hardware?').'\')" class="button notAJAX btn btn-default"><i class="glyphicon glyphicon-download"></i>&nbsp;'.__('Modem Identify').'</a>';
              $menu .='<form action="'.MWB.'sms_gateway/index.php" id="identify" target="blindSubmit" method="post" style="display: inline;"><input type="hidden" name="identify" value="true" /></form>';
         }
       }
       else{
        $msg .= __('Can\'t start Service, Please click setting first<a class="button btn btn-info pull-right"').' href="'.MWB.'sms_gateway/setting.php"><i class="glyphicon glyphicon-wrench"></i> Setting</a>';
       }
      $msg .= '<br/>';

    
    if(file_exists(trim(PATH.'logsmsdrc')) AND $_SESSION['uid'] == 1){
      $msg .= 'Log gammu : '.fsize(PATH.'logsmsdrc');
      $msg .= '<div class="btn-group pull-right"><a href="#" onclick="confSubmit(\'clear\',\''.__('Clear Log?').'\')" class="button notAJAX btn btn-danger"><i class="glyphicon glyphicon-remove"></i></i>&nbsp;'.__('Clear Log').'</a>';
      $msg .= '<form action="'.MWB.'sms_gateway/index.php" id="clear" target="blindSubmit" method="post" style="display: inline;"><input type="hidden" name="clear" value="true" /></form>';
      $msg .=  '<a class="button notAJAX btn btn-info" onclick="window.open(\''.MWB.'sms_gateway/view.php\', \'_blank\', \'toolbar=0,location=0,menubar=0,height=700,width=800,scrollbars=1,directories=0,titlebar=0\');"><i class="glyphicon glyphicon-download"></i></i>&nbsp;'.__('View Log').'</a>';

     

    }  echo isset($menu)?'<div class="btn-group">'.$menu.'</div>':'';
   echo '<div class="well" style="font-size:12pt;margin-top:50px;">'.$msg.'</div>';  
   
    ?>
  </div>
</div>
</fieldset>
