<?php
	
	$page_list = admin_url().'edit.php?post_type=page';
	$nonce = $adminHandler->getNonceValue();

	include MOV_DIR . 'views/settings.php';