$(document).ready(function() {

   var ajax_baseurl = '../plugins/webnotifications/front/ajax';
   var path = document.location.pathname;
   // construct url for plugin pages
   if(path.indexOf('plugins/') !== -1) {
      var plugin_path = path.substring(path.indexOf('plugins'));
      var nb_directory = (plugin_path.match(/\//g) || []).length + 1;
      var ajax_baseurl = Array(nb_directory).join("../") + 'plugins/webnotifications/front/ajax';
   }

   var notificationDisplay = function() {
      // page index
      //$("#page > .tab_cadre_postonly > tbody").prepend("<tr><td colspan='2' id='mod_inserted'></td></tr>");
      $("#page").prepend("<tr><td colspan='1' id='not_inserted'></td></tr>");

      //$("#page").load(ajax_baseurl + "/display_mod.php");
      $("#not_inserted").load(ajax_baseurl + "/notification.php");
          
   };
   
   notificationDisplay();


});

