<?php

/**
 * Copyright (C) 2014  Heru Subekti (heroe_soebekti@yahoo.co.id)
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

/* gammu daemon */

$confirmation = 0;
if ($_SERVER['HTTP_HOST'] =='localhost'){
	$confirmation = 1;
}

if($confirmation = 1){
$dbs = new mysqli('localhost','root','','perpustakaan');
$key  = array('REG','UNREG','INFO','LOAN');

$set_q = $dbs->query("SELECT setting_value FROM setting WHERE setting_name='autoreply_sms_settings'");
$b = $set_q->fetch_row();
$setting = unserialize($b[0]);

	$status_q = $dbs->query("SELECT i.SenderNumber, i.textDecoded, i.ID FROM inbox AS i WHERE i.Processed='false'");
//*****************************************************************************************************	
	for ($a = 1; $a <= $status_q->num_rows; $a++ ) {
		$item_d[$a] = $status_q->fetch_array();
		$keyword = explode('#',strtoupper($item_d[$a][1]));
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		//IF KEYWORD TRUE
		if( in_array($keyword[0],$key)){ 
			
			$phone_q = $dbs->query("SELECT member_id,member_phone,member_name FROM member WHERE member_phone = '".$item_d[$a][0]."'");
			$phone_status = $phone_q->num_rows; 

//======================================================================================================	
			//if keyword is true but never register		
			if($phone_status==0)  { 
				if($setting['member_registration']==true && $keyword[0] == 'REG'){
					$member_q = $dbs->query("SELECT member_id, mpasswd, member_name, member_phone FROM member WHERE mpasswd='".md5($keyword[2])."' AND member_id='".$keyword[1]."'");
				    $member_status = $member_q->num_rows;   
				    if($member_status>0){ 
				    	$reg_data  = $member_q->fetch_array();
				    	//registration proccess
				    	//if keyword number and password true but never register
				    	if($reg_data[3]==0){
				    		//send message
	        				$content =  '['.$item_d[$a][2].']Msg:Registrasi Sukses nomor anda sekarang terdaftar di fasilitas SMS Gateway';
							$insert = $dbs->query("INSERT INTO outbox (DestinationNumber,TextDecoded,SendingDateTime,SenderID,CreatorID)
											 VALUES ('".$item_d[$a][0]."','".$content."','".date("Y-m-d H:i:s")."','SLiMS_Gateway','Senayan Library')");
							$update = $dbs->query("UPDATE inbox SET Processed = 'true' WHERE ID=".$item_d[$a][2]);	
				    		//echo  $data['TextDecoded'];
				    		//update data
				    		@$dbs->query('UPDATE member SET member_phone = \''.$item_d[$a][0].'\' WHERE member_id=\''.$keyword[1].'\'') ;
				    	}
				    	//if keyword number and password true but phone number register
				    	else{
				    		//send message
	        				$content=  '['.$item_d[$a][2].']Msg:userid dan password sudah terdaftar di nomor telepon lain';
							$insert = $dbs->query("INSERT INTO outbox (DestinationNumber,TextDecoded,SendingDateTime,SenderID,CreatorID)
											 VALUES ('".$item_d[$a][0]."','".$content."','".date("Y-m-d H:i:s")."','SLiMS_Gateway','Senayan Library')");
							$update = $dbs->query("UPDATE inbox SET Processed = 'true' WHERE ID=".$item_d[$a][2]);	
				    		//echo  $data['TextDecoded'];
				    	}
				    }
				    //if keyword number and password wrong
				    else{
				    		//send message
	        				$content =  '['.$item_d[$a][2].']Msg:nomor tidak dapat digunakan karena id dan password tidak sesuai';
							$insert = $dbs->query("INSERT INTO outbox (DestinationNumber,TextDecoded,SendingDateTime,SenderID,CreatorID)
											 VALUES ('".$item_d[$a][0]."','".$content."','".date("Y-m-d H:i:s")."','SLiMS_Gateway','Senayan Library')");
							$update = $dbs->query("UPDATE inbox SET Processed = 'true' WHERE ID=".$item_d[$a][2]);	
							//echo  $data['TextDecoded'];
				    }

				}

				//if all keyword true but number not register
				else{
					//send message
	        		$content =  '['.$item_d[$a][2].']Msg:silahkan registrasi dulu untuk menggunakan layanan ini';
					$insert = $dbs->query("INSERT INTO outbox (DestinationNumber,TextDecoded,SendingDateTime,SenderID,CreatorID)
											 VALUES ('".$item_d[$a][0]."','".$content."','".date("Y-m-d H:i:s")."','SLiMS_Gateway','Senayan Library')");
					$update = $dbs->query("UPDATE inbox SET Processed = 'true' WHERE ID=".$item_d[$a][2]);	
				}
			}
//========================================================================================================
			//if phone number register and keyword true
			elseif($phone_status>0) {
				$member_data  = $phone_q->fetch_array();

				if($setting['member_registration']==true && $keyword[0] == 'REG'){
					//send message
	        		$content =  '['.$item_d[$a][2].']Msg:Sdr. '.$member_data[2].', nomor anda sudah terdaftar, gunakan menu lain';
					$insert = $dbs->query("INSERT INTO outbox (DestinationNumber,TextDecoded,SendingDateTime,SenderID,CreatorID)
											 VALUES ('".$item_d[$a][0]."','".$content."','".date("Y-m-d H:i:s")."','SLiMS_Gateway','Senayan Library')");
					$update = $dbs->query("UPDATE inbox SET Processed = 'true' WHERE ID=".$item_d[$a][2]);	
				}

				elseif($setting['member_unregister']==true && $keyword[0] == 'UNREG'){
					//send message
	        		$content =  '['.$item_d[$a][2].']Msg:Sdr. '.$member_data[2].', nomor anda telah dinonaktifkan dari layanan smsgateway';
					$insert = $dbs->query("INSERT INTO outbox (DestinationNumber,TextDecoded,SendingDateTime,SenderID,CreatorID)
											 VALUES ('".$item_d[$a][0]."','".$content."','".date("Y-m-d H:i:s")."','SLiMS_Gateway','Senayan Library')");
					$update = $dbs->query("UPDATE inbox SET Processed = 'true' WHERE ID=".$item_d[$a][2]);		
					//update data
				    @$dbs->query('UPDATE member SET member_phone = \'\' WHERE member_id=\''.$member_data[0].'\'') ;
							
				}

				elseif($setting['loan_notification']==true && $keyword[0] == 'LOAN'){
				$sql_op = $dbs->query("SELECT l.member_id FROM loan AS l LEFT JOIN member AS m ON l.member_id=m.member_id WHERE l.is_return=0 and l.member_id='".$member_data[0]."'");
				//$sql_op = $dbs->query("SELECT CONCAT('anda punya pinjaman ', COUNT(l.member_id),' buku') FROM loan AS l WHERE l.is_return=0 and l.member_id='".$reg_data[0]."'");
				if($sql_op->num_rows>0){ $msg = 'anda punya pinjaman '.$sql_op->num_rows.' buku'; }
				else{ $msg = 'anda tidak mempunyai pinjaman saat ini'; }
					//send message
	        		$content=  '['.$item_d[$a][2].']Msg:Sdr. '.$member_data[2].', '.$msg;
					$insert = $dbs->query("INSERT INTO outbox (DestinationNumber,TextDecoded,SendingDateTime,SenderID,CreatorID)
											 VALUES ('".$item_d[$a][0]."','".$content."','".date("Y-m-d H:i:s")."','SLiMS_Gateway','Senayan Library')");
					$update = $dbs->query("UPDATE inbox SET Processed = 'true' WHERE ID=".$item_d[$a][2]);		
				}	

				elseif($setting['info_notification']==true && $keyword[0] == 'INFO'){
					//send message
	        		$content =  '['.$item_d[$a][2].']'.$setting['info_message'];
					$insert = $dbs->query("INSERT INTO outbox (DestinationNumber,TextDecoded,SendingDateTime,SenderID,CreatorID)
											 VALUES ('".$item_d[$a][0]."','".$content."','".date("Y-m-d H:i:s")."','SLiMS_Gateway','Senayan Library')");
					$update = $dbs->query("UPDATE inbox SET Processed = 'true' WHERE ID=".$item_d[$a][2]);		
				}

				else{
					$data['DestinationNumber'] =  $item_d[$a][0];
	        		$content =  '['.$item_d[$a][2].']'.'opsi ini tidak aktif';
					$insert = $dbs->query("INSERT INTO outbox (DestinationNumber,TextDecoded,SendingDateTime,SenderID,CreatorID)
											 VALUES ('".$item_d[$a][0]."','".$content."','".date("Y-m-d H:i:s")."','SLiMS_Gateway','Senayan Library')");
					$update = $dbs->query("UPDATE inbox SET Processed = 'true' WHERE ID=".$item_d[$a][2]);		
				}
			}
//===========================================================================================================	    
		}
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		//IF KEYWORD FALSE	
		else{ 
			$insert = $dbs->query("INSERT INTO other_inbox (DestinationNumber,TextDecoded,SendingDateTime) VALUES ('".$item_d[$a][0]."','".$item_d[$a][1]."','".date("Y-m-d H:i:s")."')");
			$delete = $dbs->query("DELETE FROM inbox  WHERE ID=".$item_d[$a][2]);
		}
	}
//***********************************************************************************************************
}

