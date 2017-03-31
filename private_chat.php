<?php
	require_once('db_connect.php');
	$pr = new PrivateChat();
	if (isset($_GET['message']) && isset($_GET['name_from']) && isset($_GET['id_to'] ))
		$pr->PutPrivateMessage();
	
   
	if (isset($_GET['name_from']) && isset($_GET['msg_id']))
		$pr->FetchPrivateMessage();
	if (isset($_GET['flag']))
	$pr->GetUsersId();
	
?>
