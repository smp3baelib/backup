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
/* by Heru Subekti,2014 (heroe_soebekti@yahoo.co.id) */
/* task scheduler */

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
$can_read = utility::havePrivilege('reporting', 'r');
$can_write = utility::havePrivilege('reporting', 'w');

if (!$can_read) {
    die('<div class="errorBox">'.__('You don\'t have enough privileges to access this area!').'</div>');
}

require SB.'admin/default/session_check.inc.php';
require SIMBIO.'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require SIMBIO.'simbio_GUI/table/simbio_table.inc.php';


if(isset($_POST['updateData'])){

    if($_POST['active']==1){
            $dbs->query("DROP EVENT task");
            $dbs->query("SET GLOBAL event_scheduler = 0");
            $dbs->query("SET GLOBAL event_scheduler = 1");
            $dbs->query("CREATE EVENT task ON SCHEDULE EVERY 1 DAY STARTS '2009-10-10 ".$_POST['hourTask'].":".$_POST['minutesTask'].":00' DO
                        INSERT INTO outbox (DestinationNumber,SendingDateTime,Class,TextDecoded,SenderID,CreatorID) 
                        SELECT m.member_phone,now(),'-1',
                            (SELECT CONCAT( m.member_name,', terlambat ',(select GROUP_CONCAT('[',ln.item_code,']',IF(LENGTH(b.title)>20,CONCAT(SUBSTR(b.title,1,19),'.. '),b.title),'(',(TO_DAYS(DATE(NOW()))-TO_DAYS(ln.due_date)),' hr.)') 
                                FROM loan as ln 
                                LEFT JOIN item  AS i ON i.item_code=ln.item_code 
                                lEFT JOIN biblio AS b ON b.biblio_id=i.biblio_id 
                                WHERE is_return='0' AND ln.member_id=m.member_id),', admin.') ),
                        'SLiMS_Gateway','Senayan Library' 
                        FROM member AS m
                        LEFT JOIN loan AS l ON l.member_id=m.member_id
                        WHERE m.member_phone!='' AND l.is_return='0' AND l.due_date < now('Y-m-d') GROUP BY m.member_id ");
            echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(parent.$.ajaxHistory[0].url);</script>';
        }
        elseif($_POST['active']==0){
            $dbs->query("DROP EVENT task");
            $dbs->query("SET GLOBAL event_scheduler = 0");
            echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(parent.$.ajaxHistory[0].url);</script>';
        }
    }
?>

<fieldset class="menuBox">
    <div class="per_title">
      <h2><?php echo __('Task Scheduler'); ?></h2>
    </div>
</fieldset>

<?php
    $set_q = $dbs->query("SHOW EVENTS");
    $b = $set_q->num_rows;
    if($b>0){
        $c = $set_q->fetch_array();
        echo '<div class="infoBox" style="color:#000 !important;">'. $c[1]. ' schedule runs every day at '.substr($c[8],11).'</div>';
        $task = 1;
        $_hour = substr($c[8],11,2);
        $_minutes = substr($c[8],14,2);     
    }
    else{
        echo '<div class="warning">'.'no running schedule (setting is Disable)'.'</div>';
        $task = 0;
        $_hour = date("G");
        $_minutes = date("i");
    }
// create new instance
$form = new simbio_form_table_AJAX('mainForm', $_SERVER['PHP_SELF'], 'post');
$form->submit_button_attr = 'name="updateData" value="'.__('Save Settings').'" class="button"';

// form table attributes
$form->table_attr = 'align="center" id="dataList" cellpadding="5" cellspacing="0"';
$form->table_header_attr = 'class="alterCell" style="font-weight: bold;"';
$form->table_content_attr = 'class="alterCell2"';

$options[] = array('0', __('Disable'));
$options[] = array('1', __('Enable'));
$form->addSelectList('active', __('Task'), $options, $task);

for($hour=0;$hour<=23;$hour++){$hour_options[] = array($hour,$hour);}
$str_input  = simbio_form_element::selectList('hourTask', $hour_options,$_hour, 'style="width: 50px;"');

for($min=0;$min<=59;$min++){ $minutes_options[] = array($min,$min);}
$str_input .= simbio_form_element::selectList('minutesTask', $minutes_options, $_minutes, 'style="width: 50px;"');

$form->addAnything(__('Time'), $str_input);
// print out the object
echo $form->printOut();
