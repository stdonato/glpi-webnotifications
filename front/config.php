<?php

include ('../../../inc/includes.php');

$plugin = new Plugin();
	if ($plugin->isActivated("webnotifications")) {

   Html::header('Web Notification', "", "plugins", "webnotifications");	      
	
	$file =  '../../../inc/html.class.php'; 
	
	$string = file_get_contents( $file ); 
	// poderia ser um string ao invés de file_get_contents().  /(.*).php  js/notify(.*)<
	
	$acha = preg_match('notifica.php/', $string, $matches );

	echo "<div class='center' style='height:300px; width:80%; background:#fff; margin:auto; float:none;'><br><p>\n";
   echo "<div id='config' class='center here ui-tabs-panel'>\n";
		  
   echo "
   		<br><p>
        <span style='color:blue; font-weight:bold; font-size:13pt;'>".__('Web Notifications Plugin')."</span> <br><br><p>";
                    
   		
   echo "
   	<div id=sound class='center here' style='margin-bottom:35px;' >
   		<span style='font-size:16px; margin-bottom:20px;'> Sound Alert:&nbsp;&nbsp; </span> </br><p></p>      		
   		<div style='margin-top:10px;'>
      		<a class='vsubmit' type='submit' onclick=\"window.location.href = 'config.php?sound=ativar';\"> "._x('button','Enable')." </a>
	   		&nbsp;&nbsp;&nbsp;&nbsp;
   	      <a class='vsubmit' type='submit' onclick=\"window.location.href = 'config.php?sound=desativar';\"> ".__('Disable')." </a>
         </div>				      	      	
   	</div>
   ";		

   // choose config server or config synchro

   } else {
      Html::header(__('Setup'),'',"config","plugins");
      echo "<div class='center'><br><br>";
      echo "<img src=\"".$CFG_GLPI["root_doc"]."/pics/warning.png\" alt='".__s('Warning')."'><br><br>";
      echo "<b>".__('Please activate the plugin', 'webnotifications')."</b></div>";
   }


//enable plugin
if(isset($_REQUEST['opt'])) {

$action = $_REQUEST['opt'];

	if($action == 'ativar') {
		
		$search = "// End of Head";		
		$replace = "// End of Head\n";		
		$replace .= "include('../plugins/webnotifications/notifica.php');";
		file_put_contents('../../../inc/html.class.php', str_replace($search, $replace, file_get_contents('../../../inc/html.class.php')));
		
		echo "<div id='config' class='center' style='font-size:18px;'>";
		echo "Plugin  "._x('plugin', 'Enabled')." <br><br><p> </div>";	
	}
	
	
	if($action == 'desativar') {
		
		$search = "include('../plugins/webnotifications/notifica.php');";	
		$replace = "";
		file_put_contents('../../../inc/html.class.php', str_replace($search, $replace, file_get_contents('../../../inc/html.class.php')));
		
		echo "<div id='config' class='center' style='font-size:18px;'>";
		echo "Plugin  ".__('Disabled')."  <br><br><p></div>";	
	}

}

//enable sound
if(isset($_REQUEST['sound'])) {

		if($_REQUEST['sound'] == 'ativar') {
			
			$query_act = "UPDATE glpi_plugin_webnotifications_config
			SET value = '1'
			WHERE name = 'sound' ";
		
		   $result_act = $DB->query($query_act);					
		}
			
		if($_REQUEST['sound'] == 'desativar') {
			
			$query_act = "UPDATE glpi_plugin_webnotifications_config
			SET value = '0'
			WHERE name = 'sound' ";
		
		   $result_act = $DB->query($query_act);
		}	   
}	   

echo "<div id='config' class='center'>
			<a class='vsubmit' type='submit' onclick=\"window.location.href = '". $CFG_GLPI['root_doc'] ."/front/plugin.php';\" >  ".__('Back')." </a> 
		</div></div>\n";

?>
