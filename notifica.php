<?php

global $DB, $CFG_GLPI;

if (isset($_SESSION['glpiID'])) {

$version = substr($CFG_GLPI["version"],0,5);

if($version == "0.84") {
	echo "<script type='text/javascript' src='".$CFG_GLPI['url_base']."/plugins/webnotifications/js/jquery.min.js'></script>";
}

echo "<script type='text/javascript' src='".$CFG_GLPI['url_base']."/plugins/webnotifications/js/notify.js'></script>";
echo "<audio id='audiotag1' src='".$CFG_GLPI['url_base']."/plugins/webnotifications/audio/audio.wav' preload='auto'></audio>";

//enable / disable sound
$query_som = 
"SELECT value
FROM glpi_plugin_webnotifications_config
WHERE name = 'sound' ";

$result_som = $DB->query($query_som);
$sound = $DB->result($result_som,0,'value');

//notify techs
$sql = "
SELECT COUNT(gt.id) AS total
FROM glpi_tickets_users gtu, glpi_tickets gt
WHERE gtu.users_id = ". $_SESSION['glpiID'] ."
AND gtu.type = 2
AND gt.is_deleted = 0
AND gt.id = gtu.tickets_id" ;

$resulta = $DB->query($sql);
$abertos = $DB->result($resulta,0,'total');

//$abertos = $data; 
$init = $abertos - 1;

$query_u = "
INSERT IGNORE INTO glpi_plugin_webnotifications_count(users_id, quant, type) 
VALUES ('". $_SESSION['glpiID'] ."', '" . $init ."', '0' )  ";

$result_u = $DB->query($query_u);


$query = "SELECT users_id, quant, type 
FROM glpi_plugin_webnotifications_count
WHERE users_id = ". $_SESSION['glpiID'] ."
AND type = 0 " ;

$result = $DB->query($query);

$atual = $DB->result($result,0,'quant');
$type = $DB->result($result,0,'type');

$dif = $abertos - $atual;

//update tickets count	
$query_up = "UPDATE glpi_plugin_webnotifications_count
SET quant=". $abertos ."
WHERE users_id = ". $_SESSION['glpiID'] ." 
AND type = 0 ";

$result_up = $DB->query($query_up);
	

//create notification
if($abertos > $atual) {
				
	if($dif >= 5) { $dif = 5; }
	
	$queryc = 
	"SELECT gt.id AS id, gt.name AS name
	FROM glpi_tickets_users gtu, glpi_tickets gt
	WHERE gtu.users_id = ". $_SESSION['glpiID'] ."
	AND gtu.type = 2
	AND gt.is_deleted = 0
	AND gt.id = gtu.tickets_id
	ORDER BY id DESC
	LIMIT ".$dif." ";
	
	$res = $DB->query($queryc);		
}	

//followups
$queryn = "SELECT COUNT(gtf.id) AS total
FROM glpi_ticketfollowups gtf, glpi_tickets_users gtu
WHERE gtf.tickets_id =  gtu.tickets_id 
AND gtu.type = 2
AND gtu.users_id = ". $_SESSION['glpiID'] ." ";

$resultf = $DB->query($queryn);

$abertosn = $DB->result($resultf,0,'total');

$initn = $abertosn - 1;

$query_un = "
INSERT IGNORE INTO glpi_plugin_webnotifications_count(users_id, quant, type) 
VALUES ('". $_SESSION['glpiID'] ."', '" . $initn ."', '1' )  ";

$result_un = $DB->query($query_un);


$queryn1 = "SELECT users_id, quant, type 
FROM glpi_plugin_webnotifications_count
WHERE users_id = ". $_SESSION['glpiID'] ."
AND type = 1 " ;

$resultn1 = $DB->query($queryn1);

$atualn = $DB->result($resultn1,0,'quant');
$typen = $DB->result($resultn1,0,'type');

$difn = $abertosn - $atualn;


//update notif count	
$query_upn = "UPDATE glpi_plugin_webnotifications_count
SET quant=". $abertosn ."
WHERE users_id = ". $_SESSION['glpiID'] ." 
AND type = 1 ";

$result_upn = $DB->query($query_upn);


if($abertosn > $atualn) {
				
	if($difn >= 5) { $difn = 5; }
	
	$querycn = 
	"SELECT DISTINCT gt.id AS id, gt.name AS name, gtf.content AS content, gtf.date AS date 
	FROM glpi_tickets_users gtu, glpi_tickets gt, glpi_ticketfollowups gtf
	WHERE gtu.users_id = ".$_SESSION['glpiID']."
	AND gtf.tickets_id =  gtu.tickets_id 
	AND gtu.type = 2
	AND gt.is_deleted = 0
	AND gt.id = gtu.tickets_id
	ORDER BY date DESC
	LIMIT ".$difn." ";
	
	$resn = $DB->query($querycn);
}


//New tickets
if($abertos > $atual) {

	$DB->data_seek($res, 0);	
		
	while($row = $DB->fetch_assoc($res)) {
	
		$icon = "../plugins/webnotifications/img/icon.png";
		$titulo = __('New ticket');
		$text = __('New ticket').": ".$row['id']." - ".$row['name'];
		
		$text2 = __('New ticket').": <a href=".$CFG_GLPI['url_base']."/front/ticket.form.php?id=".$row['id']." style=color:#ffffff;>".$row['id']."</a> - ".$row['name'];
		
		$id = $row['id'];
		
		$user_agent = $_SERVER['HTTP_USER_AGENT']; 
		
		if (!preg_match('/Chrome/i', $user_agent)) { 
			echo"<script>notify('".$titulo."','".$text."','".$icon."','".$id."');</script>"; 
							
			if($sound == '1') {
				echo"<script>audio();</script>";
			} 
		} 
		
		else { 
			echo"<script>notify('".$titulo."','".$text."','".$icon."','".$id."');</script>"; 
							
			if($sound == '1') {
				echo"<script>audio();</script>";
			} 	
		}
	}			
}	


//followup
if($abertosn > $atualn) {

$DB->data_seek($resn, 0);
while($row = $DB->fetch_assoc($resn)) {

	$icon = "../plugins/webnotifications/img/icon.png";
	$titulo = __('New followup');
	$text = __('Ticket').": ".$row['id']." - ".$row['content'];
	$id = $row['id'];
	
	$text2 = __('Ticket').": <a href=".$CFG_GLPI['url_base']."/front/ticket.form.php?_itemtype=Ticket&_glpi_tab=TicketFollowup$1&id=".$row['id']." style=color:#ffffff;>".$row['id']."</a> - ".$row['content'];
	
	$id = $row['id'];
	
	$user_agent = $_SERVER['HTTP_USER_AGENT']; 
	
	if (!preg_match('/Chrome/i', $user_agent)) { 
		echo"<script>notify('".$titulo."','".$text."','".$icon."','".$id."');</script>"; 
						
			if($sound == '1') {
				echo"<script>audio();</script>";
			} 
	} 
	
	else { 
		echo"<script>notify('".$titulo."','".$text."','".$icon."','".$id."');</script>"; 
						
			if($sound == '1') {
				echo"<script>audio();</script>";
			} 	
		}
	}
}

//notify techs - end	


//notify requester
$sql_req = "
SELECT COUNT(gt.id) AS total
FROM glpi_tickets_users gtu, glpi_tickets gt
WHERE gtu.users_id = ". $_SESSION['glpiID'] ."
AND gtu.type = 1
AND gt.is_deleted = 0
AND gt.id = gtu.tickets_id" ;

$resulta_req = $DB->query($sql_req);
$abertos_req = $DB->result($resulta_req,0,'total');

//$abertos = $data; 
$init_req = $abertos_req - 1;

$query_u_req = "
INSERT IGNORE INTO glpi_plugin_webnotifications_count_req(users_id, quant, type) 
VALUES ('". $_SESSION['glpiID'] ."', '" . $init_req ."', '0' )  ";

$result_u_req = $DB->query($query_u_req);


$query_req = "SELECT users_id, quant, type 
FROM glpi_plugin_webnotifications_count_req
WHERE users_id = ". $_SESSION['glpiID'] ."
AND type = 0 " ;

$result_req = $DB->query($query_req);

$atual_req = $DB->result($result_req,0,'quant');
$type_req = $DB->result($result_req,0,'type');

$dif_req = $abertos_req - $atual_req;

//update tickets count	
$query_up_req = "UPDATE glpi_plugin_webnotifications_count_req
SET quant=". $abertos_req ."
WHERE users_id = ". $_SESSION['glpiID'] ." 
AND type = 0 ";

$result_up_req = $DB->query($query_up_req);
	
//create notification
if($abertos_req > $atual_req) {
				
	if($dif_req >= 5) { $dif_req = 5; }
	
	$queryc_req = 
	"SELECT gt.id AS id, gt.name AS name
	FROM glpi_tickets_users gtu, glpi_tickets gt
	WHERE gtu.users_id = ". $_SESSION['glpiID'] ."
	AND gtu.type = 1
	AND gt.is_deleted = 0
	AND gt.id = gtu.tickets_id
	ORDER BY id DESC
	LIMIT ".$dif_req." ";
	
	$res_req = $DB->query($queryc_req);		
}	

//followups
$queryn_req = "SELECT COUNT(gtf.id) AS total
FROM glpi_ticketfollowups gtf, glpi_tickets_users gtu
WHERE gtf.tickets_id =  gtu.tickets_id 
AND gtu.users_id = ". $_SESSION['glpiID'] ."
AND gtu.type = 1 ";

$resultf_req = $DB->query($queryn_req);

$abertosn_req = $DB->result($resultf_req,0,'total');

$initn_req = $abertosn_req - 1;

$query_un_req = "
INSERT IGNORE INTO glpi_plugin_webnotifications_count_req(users_id, quant, type) 
VALUES ('". $_SESSION['glpiID'] ."', '" . $initn_req ."', '1' )  ";

$result_un_req = $DB->query($query_un_req);


$queryn1_req = "SELECT users_id, quant, type 
FROM glpi_plugin_webnotifications_count_req
WHERE users_id = ". $_SESSION['glpiID'] ."
AND type = 1 ";

$resultn1_req = $DB->query($queryn1_req);

$atualn_req = $DB->result($resultn1_req,0,'quant');
$typen_req = $DB->result($resultn1_req,0,'type');

$difn_req = $abertosn_req - $atualn_req;


//update notif count	
$query_upn_req = "UPDATE glpi_plugin_webnotifications_count_req
SET quant=". $abertosn_req ."
WHERE users_id = ". $_SESSION['glpiID'] ." 
AND type = 1 ";

$result_upn_req = $DB->query($query_upn_req);


if($abertosn_req > $atualn_req) {
				
	if($difn_req >= 5) { $difn_req = 5; }
	
	$querycn_req = 
	"SELECT DISTINCT gt.id AS id, gt.name AS name, gtf.content AS content, gtf.date AS date 
	FROM glpi_tickets_users gtu, glpi_tickets gt, glpi_ticketfollowups gtf
	WHERE gtu.users_id = ".$_SESSION['glpiID']."
	AND gtf.tickets_id =  gtu.tickets_id 
	AND gtu.type = 1
	AND gt.is_deleted = 0
	AND gt.id = gtu.tickets_id
	ORDER BY date DESC
	LIMIT ".$difn_req." ";
	
	$resn_req = $DB->query($querycn_req);
}

//New tickets requester

if($abertos_req > $atual_req) {

	$DB->data_seek($res_req, 0);	
		
	while($row_req = $DB->fetch_assoc($res_req)) {
	
		$icon_req = "../plugins/webnotifications/img/icon.png";
		$titulo_req = __('New ticket');
		$text_req = __('New ticket').": ".$row_req['id']." - ".$row_req['name'];		
		$text2_req = __('New ticket').": <a href=".$CFG_GLPI['url_base']."/front/ticket.form.php?id=".$row_req['id']." style=color:#ffffff;>".$row_req['id']."</a> - ".$row_req['name'];
		
		$id_req = $row_req['id'];
		
		$user_agent = $_SERVER['HTTP_USER_AGENT']; 
		
		if (!preg_match('/Chrome/i', $user_agent)) { 
			echo"<script>notify('".$titulo_req."','".$text_req."','".$icon_req."','".$id_req."');</script>"; 
							
			if($sound == '1') {
				echo"<script>audio();</script>";
			} 
		} 
		
		else { 
			echo"<script>notify('".$titulo_req."','".$text_req."','".$icon_req."','".$id_req."');</script>"; 
							
			if($sound == '1') {
				echo"<script> audio(); </script>";
			} 	
		}
	}			
}	

//followup requester
if($abertosn_req > $atualn_req) {

$DB->data_seek($resn_req, 0);
while($row_req = $DB->fetch_assoc($resn_req)) {

	$icon_req = "../plugins/webnotifications/img/icon.png";
	$titulo_req = __('New followup');
	$text_req = __('Ticket').": ".$row_req['id']." - ".$row_req['content'];
	$id_req = $row_req['id'];
	
	$text2_req = __('Ticket').": <a href=".$CFG_GLPI['url_base']."/front/ticket.form.php?_itemtype=Ticket&_glpi_tab=TicketFollowup$1&id=".$row_req['id']." style=color:#ffffff;>".$row_req['id']."</a> - ".$row_req['content'];
	
	$id_req = $row_req['id'];
	
	$user_agent = $_SERVER['HTTP_USER_AGENT']; 
	
	if (!preg_match('/Chrome/i', $user_agent)) { 
		echo"<script>notify('".$titulo_req."','".$text_req."','".$icon_req."','".$id_req."');</script>"; 
						
			if($sound == '1') {
				echo"<script>audio();</script>";
			} 
	} 
	
	else { 
		echo"<script>notify('".$titulo_req."','".$text_req."','".$icon_req."','".$id_req."');</script>"; 
						
			if($sound == '1') {
				echo"<script>audio();</script>";
			} 	
		}
	}
}

//notify requester - end	


//groups 
$groups = implode(",",$_SESSION['glpigroups']);

$sql1 = "
SELECT ggu.users_id AS user, ggu.groups_id AS grupo
FROM glpi_groups_users ggu
WHERE `users_id` = ". $_SESSION['glpiID'] ." ";

$resulta1 = $DB->query($sql1);

while($row = $DB->fetch_assoc($resulta1)) {

	// numero de chamados por grupo
	$sqlg = "
	SELECT count( ggt.tickets_id ) AS total
	FROM glpi_groups_tickets ggt
	WHERE ggt.groups_id = ". $row['grupo'] ." ";
	
	$resultag = $DB->query($sqlg);
	$up_total = $DB->result($resultag,0,'total');
	
	// grupos e numero de chamados
	$query_g = "
	INSERT IGNORE INTO glpi_plugin_webnotifications_count_grp(groups_id, quant, users_id) 
	VALUES ('". $row['grupo'] ."', '" . $up_total ."','" . $_SESSION['glpiID']  ."')  ";
	
	$result_g = $DB->query($query_g);	
	$abertosg = $DB->result($resultag,0,'total');
	$initg = $abertosg - 1;
	
	$queryg = "SELECT groups_id, quant, users_id
	FROM glpi_plugin_webnotifications_count_grp
	WHERE groups_id = ". $row['grupo'] ." 
	AND users_id = ".$_SESSION['glpiID']." ";
	
	$resultg = $DB->query($queryg);	
	$atualg = $DB->result($resultg,0,'quant');		
	$difg = $abertosg - $atualg;	
	
	if($abertosg > $atualg) {
					
		if($difg >= 5) { $difg = 5; }
				
		$queryc = 
		"SELECT gt.id AS id, gt.name AS name
		FROM glpi_groups_tickets ggt, glpi_tickets gt
		WHERE ggt.groups_id = ". $row['grupo']  ."
		AND gt.is_deleted = 0
		AND gt.id = ggt.tickets_id 
		ORDER BY id DESC
		LIMIT ".$difg." ";
		
		$resg = $DB->query($queryc);		
		
		
		//update tickets count	
		$query_upg = "UPDATE glpi_plugin_webnotifications_count_grp
		SET quant = ". $abertosg ."
		WHERE groups_id = ". $row['grupo'] ." 
		AND users_id = ". $_SESSION['glpiID']  ." ";
	
	   $result_upg = $DB->query($query_upg);

		
		$DB->data_seek($resg, 0);
		
		while($row1 = $DB->fetch_assoc($resg)) {			
			
			//New ticket group		
			$icon = "../plugins/webnotifications/img/icon.png";
			$titulo = __('Group')." - ". __('New ticket');
			$text = __('Ticket').": ".$row1['id']." - ".$row1['name'];
			$id = $row1['id'];
			
			$text2 = __('Ticket').": <a href=".$CFG_GLPI['url_base']."/front/ticket.form.php?id=".$id." style=color:#ffffff;>".$id."</a> - ".$row1['name'];						
			
			$user_agent = $_SERVER['HTTP_USER_AGENT']; 
			
			if (!preg_match('/Chrome/i', $user_agent)) { 
				echo"<script>notify('".$titulo."','".$text."','".$icon."','".$id."');</script>"; 
				
				if($sound == '1') {
					echo"<script>audio();</script>";
				}  
			} 
			
			else { 
				echo"<script>notify('".$titulo."','".$text."','".$icon."','".$id."');</script>"; 
								
				if($sound == '1') {
					echo"<script>audio();</script>";
				} 
			}	
		}
	}

}	

}

?>	
