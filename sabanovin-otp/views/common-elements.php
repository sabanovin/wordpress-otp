<?php


	function is_customer_registered()
	{
		$registration_url = add_query_arg( array('page' => 'otpaccount'), $_SERVER['REQUEST_URI'] );
		if(MoUtility::micr())  return;
		echo '<div style="display:block;margin-top:10px;color:red;background-color:rgba(251, 232, 0, 0.15);
							padding:5px;border:solid 1px rgba(255, 0, 9, 0.36);">
		 <a href="'.$registration_url.'">'.mo_( "Please set api key and Gateway") .'</a>
		 	'. mo_( "to enable OTP Verification").'</div>';
	}



	function get_plugin_form_link($formalink)
	{
		echo '&nbsp;<a class="dashicons dashicons-feedback" href="'.$formalink.'" title="'.$formalink.'" ></a>';
	}



	function mo_draw_tooltip($header,$message)
	{
		echo '<span class="tooltip">
				<span class="dashicons dashicons-editor-help"></span>
				<span class="tooltiptext"><span class="header"><b><i>'. mo_( $header).'</i></b></span><br/><br/>
				<span class="body">'.mo_($message).'</span></span>
			  </span>';
	}



	function extra_post_data($data=null)
	{
	    $ignore_fields  = [
	        "moFields"     => ['option','mo_customer_validation_otp_token','miniorange_otp_token_submit',
                'miniorange-validate-otp-choice-form','submit','mo_customer_validation_otp_choice','register_nonce','timestamp'],
            "loginOrSocialForm"  => ['user_login','user_email','register_nonce','option','register_tml_nonce',
                'mo_customer_validation_otp_token'],
        ];

	    $extraPostData      = '';
	    $loginOrSocialForm  = FALSE;
	    $loginOrSocialForm  = apply_filters('is_login_or_social_form',$loginOrSocialForm,$ignore_fields);
	    $fields = !$loginOrSocialForm ? "moFields" : "loginOrSocialForm";
        foreach ($_POST as $key => $value) {
            $extraPostData .= !in_array($key,$ignore_fields[$fields]) ? get_hidden_fields($key,$value) : "";
        }
		return $extraPostData;
	}



	function get_hidden_fields($key,$value)
	{
	    $hiddenVal = '';
		if(is_array($value))
			foreach ($value as $t => $val)
				$hiddenVal .= get_hidden_fields($key.'['.$t.']',$val);
		else
			$hiddenVal .= '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
		return $hiddenVal;
	}



	function miniorange_site_otp_validation_form($user_login,$user_email,$phone_number,$message,$otp_type,$from_both)
	{
		if(!headers_sent()) header('Content-Type: text/html; charset=utf-8');
		$errorPopupHandler =  ErrorPopup::instance();
		$defaultPopupHandler =  DefaultPopup::instance();
		$htmlcontent = MoUtility::isBlank($user_email) && MoUtility::isBlank($phone_number) ?
						apply_filters( 'mo_template_build', '', $errorPopupHandler->getTemplateKey() ,$message,$otp_type,$from_both)
						: apply_filters( 'mo_template_build', '', $defaultPopupHandler->getTemplateKey() ,$message,$otp_type,$from_both);
		echo $htmlcontent;
		exit();
	}



	function miniorange_verification_user_choice($user_login, $user_email,$phone_number,$message,$otp_type)
	{
		if(!headers_sent()) header('Content-Type: text/html; charset=utf-8');
		$userChoicePopup =  UserChoicePopup::instance();
		$htmlcontent = apply_filters( 'mo_template_build', '',$userChoicePopup->getTemplateKey() ,$message,$otp_type,TRUE);
		echo $htmlcontent;
		exit();
	}



	function mo_external_phone_validation_form($goBackURL,$user_email,$message,$form,$usermeta)
	{
		if(!headers_sent()) header('Content-Type: text/html; charset=utf-8');
		$externalPopUp =  ExternalPopup::instance();
		$htmlcontent = apply_filters( 'mo_template_build', '', $externalPopUp->getTemplateKey() ,$message,NULL,FALSE);
		echo $htmlcontent;
		exit();
	}



	function get_otp_verification_form_dropdown()
	{
		$formHandler = FormList::instance();
		echo '
			<div class="modropdown">
				<span class="dashicons dashicons-search"></span><input type="text" class="dropbtn"
				    placeholder="'.mo_( 'Search and select your Form. You will see all settings for the selected form below.' ).'" />
				<div class="modropdown-content">';
			foreach ($formHandler->getForms() as $key=>$form)
			{
				echo '<div class="search_box">';
				echo '<a class="mo_search ';
				echo $form->isFormEnabled() ? 'enabled' : '';
				echo '" data-value="'.$form->getFormName().'" data-form="'.get_class($form).'">';
				echo $form->isFormEnabled() ? "( ENABLED ) " : "";
				echo $form->getFormName().'</a></div>';
			}
		echo	'</div>
			</div>';
	}



	function get_country_code_dropdown()
	{
		echo '<select name="default_country_code" id="mo_country_code">';
		echo '<option value="" disabled selected="selected">
				--------- '.mo_( 'Select your Country' ).' -------
			  </option>';
		foreach (CountryList::getCountryCodeList() as $key => $country)
		{
			echo '<option data-countrycode="'.$country['countryCode'].'" value="'.$key.'"';
			echo CountryList::isCountrySelected($country['countryCode'],$country['alphacode']) ? 'selected' : '';
			echo '>'.$country['name'].'</option>';
		}
		echo '</select>';
	}



	function get_country_code_multiple_dropdown()
	{
		echo '<select multiple size="5" name="allow_countries[]" id="mo_country_code">';
		echo '<option value="" disabled selected="selected">
				--------- '.mo_( 'Select your Countries' ).' -------
			  </option>';
		foreach (CountryList::getCountryCodeList() as $country)
		{

		}
		echo '</select>';
	}



	function show_form_details($controller,$disabled,$page_list)
	{
		$formHandler = FormList::instance();
		foreach ($formHandler->getForms() as $form) {
			echo'<tr> <td>';
			include $controller . 'forms/'. get_class($form) . '.php';
			echo '</tr></td>';
		}
	}



	function show_configured_form_details($controller,$disabled,$page_list)
	{
		$formHandler = FormList::instance();
		foreach ($formHandler->getForms() as $form) {
			if($form->isFormEnabled()) {
				include $controller . 'forms/'. get_class($form) . '.php';
				echo'<br/>';
			}
		}
	}



	function get_wc_payment_dropdown($disabled,$checkout_payment_plans)
	{
		if( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			echo mo_( '[ Please activate the WooCommerce Plugin ]' ); return;
		}
		$paymentPlans = WC()->payment_gateways->payment_gateways();
		echo '<select multiple size="5" name="wc_payment[]" id="wc_payment">';
		echo 	'<option value="" disabled>'.mo_( 'Select your Payment Methods' ).'</option>';
		foreach ($paymentPlans as $paymentPlan) {
			echo '<option ';
			if($checkout_payment_plans && array_key_exists($paymentPlan->id, $checkout_payment_plans)) echo 'selected';
			elseif(!$checkout_payment_plans) echo 'selected';
			echo ' value="'.esc_attr( $paymentPlan->id ).'">'.$paymentPlan->title.'</option>';
		}
		echo '</select>';
	}
