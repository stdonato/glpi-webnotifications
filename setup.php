<?php

function plugin_init_webnotifications() {
  
   global $PLUGIN_HOOKS, $LANG ;
   
   $PLUGIN_HOOKS['csrf_compliant']['webnotifications'] = true;         
   
   $plugin = new Plugin();	

   if ($plugin->isInstalled('webnotifications') && $plugin->isActivated('webnotifications')) {
             
   	$PLUGIN_HOOKS['config_page']['webnotifications'] = 'front/config.php';
   	$PLUGIN_HOOKS['add_javascript']['webnotifications'] = "js/include.js";
	}
                
}


function plugin_version_webnotifications(){
	global $DB, $LANG;

	return array('name'			   => __('Web Notifications'),
					'version' 			=> '1.1.3',
					'author'			   => '<a href="mailto:stevenesdonato@gmail.com"> Stevenes Donato </b> </a>',
					'license'		 	=> 'GPLv2+',
					'homepage'			=> 'https://forge.glpi-project.org/projects/webnotifications',
					'minGlpiVersion'	=> '0.90');
}

function plugin_webnotifications_check_prerequisites(){
        if (GLPI_VERSION >= 0.90){
                return true;
        } else {
                echo "GLPI version not compatible need 0.90";
        }
}


function plugin_webnotifications_check_config($verbose=false){
	if ($verbose) {
		echo 'Installed / not configured';
	}
	return true;
}


?>
