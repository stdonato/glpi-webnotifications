$(document).ready(function() {

   var ajax_baseurl = '../plugins/webnotifications/ajax';
   var path = document.location.pathname;
   // construct url for plugin pages
   if(path.indexOf('plugins/') !== -1) {
      var plugin_path = path.substring(path.indexOf('plugins'));
      var nb_directory = (plugin_path.match(/\//g) || []).length + 1;
      var ajax_baseurl = Array(nb_directory).join("../") + 'plugins/webnotifications/ajax';
   }

   var notificationDisplay = function() {
      // page index 
      $("#page").prepend("<tr><td colspan='1' id='displayed'></td></tr>");

      //$("#page").load(ajax_baseurl + "/display_mod.php");
      $("#displayed").load(ajax_baseurl + "/notification.php");          
   };
   
   
//	if (window.location.href.indexOf("helpdesk.faq.php") === -1) {
	if (document.getElementById('champRecherche') != null ) {
   	notificationDisplay();
	}

});

