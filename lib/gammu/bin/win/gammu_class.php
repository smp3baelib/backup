<?php
class gammu {

	function __construct(){
	$this->gammu = PATH;
		if (!file_exists($this->gammu)) {
			$this->error("<div class='warning'>".PATH."Can not found <b><u>{$this->gammu}</u></b> or Gammu is not installed\r\n</div>");
		} else {
			$this->Identify();
		}
	}

	function Identify(){
	    $cmd = shell_exec(PATH."gammu -c ".PATH."\gammurc identify");
	    $cmd_arr = explode("\n", $cmd);
	    $this->msg = '';
	    foreach ($cmd_arr as $key => $value) {
	        $this->msg .= $valuez;
	     }
	    return $this->msg;
	}

	function smsdrc_status(){
	if(file_exists(PATH.'smsdrc') && file_exists(PATH.'gammurc')){
	$get_config= file_get_contents(PATH.'smsdrc');
	$config =(substr_count($get_config,DB_HOST) && substr_count($get_config, DB_USERNAME)  && substr_count($get_config, DB_NAME))?true:false; }
	else { $config = false; }
	return $config;
	}


    function status(){   
	    exec("net start > ".PATH."service.log");
	    $handle = fopen(PATH."service.log", "r");
	    $baristeks = '';
	    while (!feof($handle)) { $baristeks .= fgets($handle);}
	    fclose($handle);
	    $html = substr_count($baristeks, 'Gammu SMSD Service (SLiMS_Gateway)')?true:false;
	    return $html;
	}

    function start(){  	
    	if($this->status() == false){
        $cmd .= exec(PATH."gammu-smsd -c ".PATH."smsdrc -n SLiMS_Gateway -i")."\n";
        $cmd .= exec("sc config SLiMS_Gateway start= auto")."\n";
        $cmd .= exec("sc start SLiMS_Gateway")."\n";
        $html = true;
    	}
    	else{
    	$html = true;
    	}
        return $html;
    }

	function stop(){
		$cmd  = exec("sc stop SLiMS_Gateway"); 
        $cmd .= exec(PATH."gammu-smsd -c ".PATH."smsdrc -n SLiMS_Gateway -u");
        return $cmd;

	}

	function info(){
		$msg = '';
		if(file_exists(PATH.'daemon.bat')){ 
		$get_bat= file_get_contents(PATH.'daemon.bat');
		if (substr_count($get_bat, 'SET PHP') == 0){
			$split1 = explode("\n", $get_bat);
			$php_path = explode("=", $split1[0]);
			if(!file_exists(trim($php_path[1]))){ 
				$msg .= '('.trim($php_path[1]).')  file not found, Please check <b>daemon.bat</b> configuration'."<br/>";
			}
			$daemon_path = explode("=", $split1[1]);
			if(!file_exists(trim($daemon_path[1]))){ 
				$msg .=  '('.trim($daemon_path[1]).') file not found, Please check <b>daemon.bat</b> configuration'."<br/>";
			}
			else{
				@chdir(MWB.'sms_gateway');
				$daemon_cfg = file_get_contents('daemon.php');
					if (substr_count($daemon_cfg, "('".DB_HOST."','".DB_USERNAME."','".DB_PASSWORD."','".DB_NAME."')") == 0){
						$msg .= 'Daemon Error Connecting to Database. Please check <b>daemon.php</b> configuration'."<br/>";
					}
				}
			}
		}
		else{
			$msg .= 'daemon.bat not found, Please check <b>daemon.bat</b> file';
		}
		return $msg==""?"":'<div class="warning">'.$msg.'</div>';
	}

    function error($e,$exit=0) {
		echo $e."\n";
		if ($exit == 1) { exit; }
	}


}
