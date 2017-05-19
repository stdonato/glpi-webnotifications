<?php


if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

if (!defined("GLPI_NOT_DIR")) {
   define("GLPI_NOT_DIR",GLPI_ROOT . "/plugins/webnotifications");
}

class PluginWebnotifications extends CommonDBTM {

   static function notify() {
      global $CFG_GLPI;
      
	   include(GLPI_NOT_DIR.'/front/notifica.php');

	}
	

}
?>