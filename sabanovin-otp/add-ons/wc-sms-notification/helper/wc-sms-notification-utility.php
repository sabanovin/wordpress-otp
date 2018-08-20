<?php

	
	class MoAddOnUtiltiy
	{

		
		public static function getAdminPhoneNumber()
		{
			$user = new WP_User_Query( array( 
											'role' => 'Administrator',
											'search_columns' => array( 'ID', 'user_login' ) 
									) ); 
			return ! empty( $user->results[0] ) ? array(get_user_meta( $user->results[0]->ID, 'billing_phone', true)) : array();
		}


		
		public static function replaceString($replace,$string)
		{
			foreach ($replace as $key => $value) {
				$string = str_replace('{'.$key.'}',$value,$string);
			}
			return $string;
		}


		
		public static function areFormOptionsBeingSaved($keyVal)
		{
			return current_user_can('manage_options') 
					&& MoUtility::micr()
					&& array_key_exists('option',$_POST)
					&& isset($_POST['option'])
					&& $keyVal === $_POST['option'];
		}


		
		public static function send_notif($number,$msg)
		{
			$number = MoUtility::processPhoneNumber($number);
			$content = json_decode(MocURLOTP::send_notif(new NotificationSettings($number,$msg)));
			return strcasecmp($content->status,"SUCCESS")==0 ? TRUE : FALSE;
		}


		
		public static function is_addon_activated()
		{
			$registration_url = add_query_arg( array('page' => 'woocommerce_notif'), $_SERVER['REQUEST_URI'] );
			if(MoUtility::micr())  return;
			echo '<div style="display:block;margin-top:10px;color:red;background-color:rgba(251, 232, 0, 0.15);
								padding:5px;border:solid 1px rgba(255, 0, 9, 0.36);">
			 <a href="'.$registration_url.'">'.mo_( "Validate your purchase") .'</a> 
			 	'. mo_( " to enable the Add On").'</div>';
		}
	}