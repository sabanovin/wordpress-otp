<?php
	
	$registered 	= MoUtility::micr();
	$request_uri    = remove_query_arg('addon',$_SERVER['REQUEST_URI']);
	$profile_url	= add_query_arg( array('page' => 'otpaccount' ), $request_uri );
	$settings		= add_query_arg( array('page' => 'mosettings' ), $request_uri );
	$messages		= add_query_arg( array('page' => 'messages'	  ), $request_uri );
	$license_url	= add_query_arg( array('page' => 'pricing'	  ), $request_uri );
	$config			= add_query_arg( array('page' => 'config'	  ), $request_uri );
	$otpsettings	= add_query_arg( array('page' => 'otpsettings'), $request_uri );
	$design			= add_query_arg( array('page' => 'design'	  ), $request_uri );
	$addon          = add_query_arg( array('page' => 'addon'	  ), $request_uri );

    $help_url		= MoConstants::FAQ_URL;

    $active_tab 	= $_GET['page'];
	
	include MOV_DIR . 'views/navbar.php';