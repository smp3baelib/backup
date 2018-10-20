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

/* sms_gateway module submenu items */
// IP based access limitation

do_checkIP('smc');
do_checkIP('smc-membership');

$menu[] = array('Header', __('Configure Phone/Modem'));
$menu[] = array(__('Status'), MWB.'sms_gateway/index.php', __('View Status Phone/Modem'));
if ($_SESSION['uid'] == 1) {
$menu[] = array(__('Setting Phone/Modem'), MWB.'sms_gateway/setting.php', __('Add New Library Member Data'));
$menu[] = array(__('Auto Reply'), MWB.'sms_gateway/autoreply.php', __('View Sent Messages'));
$menu[] = array(__('Task Scheduler'), MWB.'sms_gateway/task_scheduler.php', __('Task Scheduler'));
}
$menu[] = array('Header', __('Message'));
$menu[] = array(__('Write Message'), MWB.'sms_gateway/compose.php', __('Create New Message'));
$menu[] = array(__('Broadcast Messages'), MWB.'sms_gateway/broadcast.php', __('Broadcast Message'));
$menu[] = array(__('Inbox'), MWB.'sms_gateway/inbox.php', __('View Inbox Messages'));
$menu[] = array(__('Outbox'), MWB.'sms_gateway/outbox.php', __('View Outbox Messages'));
$menu[] = array(__('Send Items'), MWB.'sms_gateway/sentitems.php', __('View Sent Messages'));
$menu[] = array(__('Other Messages'), MWB.'sms_gateway/other_messages.php', __('Other Message'));

