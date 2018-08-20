<?php
 	
 	
 	class BaseAddOn
 	{
 		function __construct()
		{
			add_action( 'mo_otp_verification_add_on_controller' , array( $this, 'show_addon_settings_page'   ), 1,1);
            $this->initializeHelpers();
			$this->initializeHandlers();
		}
 	}