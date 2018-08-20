<?php

echo' <div class="mo_registration_pricing_layout" style="width:'.$width.'">';

is_customer_registered();

echo'<table class="mo_registration_pricing_table">
		<h2>'.mo_("LICENSING PLANS").'
			<span style="float:right">';
			if(!$vl)
			{
				echo '<input type="button" '.$disabled.' name="check_btn" id="check_btn" 
						class="button button-primary button-large" value="'.mo_("Check License").'"/>';
			}	
echo'			<input type="button" name="ok_btn" id="ok_btn" class="button button-primary button-large" 
						value="'.mo_("OK, Got It").'" onclick="window.location.href=\''.$settings.'\'" />
			</span>
		<h2>
		<hr>
		<tr style="vertical-align:top;">';

			if(!$plan)
			{
echo'			<td>
					<div class="mo_registration_thumbnail mo_registration_pricing_free_tab" >
						<h3 class="mo_registration_pricing_header">'.$free_plan_name.'</h3>
						<h4 class="mo_registration_pricing_sub_header">
							'.mo_("( You are automatically on this plan )").'<br/><br/>
						</h4>
						<hr>
						<div class="pricing-div"> 
                            <!--<p class="mo_registration_pricing_text">For 1 site - Forever</p><hr>-->
                            <p  style="margin-bottom: 30.5%;" class="mo_registration_pricing_text">
                                $0 - '.mo_("One Time Payment").'<br/><br/>
                                '.mo_("10 SMS and 10 Email Verifications through miniOrange gateway").'<br/><br/><br/>
                            </p>
						</div>
						<hr>
						<p class="mo_registration_pricing_text">'.mo_("Features:").'</p>
						<p class="mo_registration_pricing_text" >';

							foreach($free_plan_features as $feature)
								echo $feature . '<br/>';
echo'
						</p>
						<hr>
						<p class="mo_registration_pricing_text">Basic Support by Email</p>
					</div>
				</td>';
			}
	
echo'		<td>
				<div class="mo_registration_thumbnail mo_registration_pricing_paid_tab">
					<h3 class="mo_registration_pricing_header">';

echo 				$basic_plan_name.'
					</h3>
						<h4 class="mo_registration_pricing_sub_header">';
						if($vl) {
                            echo '<input type="button" style="margin-bottom:3.8%;" ' . $disabled . ' 
										class="button button-primary button-large" 
										onclick="mo2f_upgradeform(\'email_verification_upgrade_instances_plan\')" 
										value="' . mo_("Buy More Instances") . '"/>
						</h4>';
                        } else {
                            echo '<input type="button" style="margin-bottom:3.8%;" ' . $disabled . ' 
										class="button button-primary button-large" 
										onclick="mo2f_upgradeform(\'wp_email_verification_intranet_basic_plan\')" 
										value="' . mo_("Upgrade Now") . '"/></h4>';
                        }

echo'				<hr>
				    <div class="pricing-div"> 
                        <p style="margin-bottom: 17.7%;" class="mo_registration_pricing_text"><b>'.$basic_plan_price.'</b><br/><br/>
                            '.mo_("Unlimited OTP Generation and Verification through the plugin.").'<br/><br/>
                                <span style="color:#b01a1a"><i>
                                    '.mo_("SMS and Email delivery will be through your gateway.").'<br/>
                                    '.mo_("Delivery Charges of your gateway would apply.").'
                                </i></span>
                        <br/><br/>
                        <span class="pricing_question tooltip">
                            <i style="cursor: pointer;" data-toggle="onprem">[ When do I opt for this plan? ] </i><br/>
                            <span class="tooltiptext" style="margin-left:-130px;">
                                <span class="header"><b><i>'
                                    .MoMessages::showMessage('YOUR_GATEWAY_HEADER').'</i></b></span><br/><br/><span class="body">'
                                    .MoMessages::showMessage('YOUR_GATEWAY_BODY').'
                                </span>
                            </span>
                        </span>';
echo'				    </p>
				    </div>
					<hr>
					<p class="mo_registration_pricing_text">Features:</p>
					<p class="mo_registration_pricing_text" >';

							foreach($basic_plan_features as $feature)
								echo $feature . '<br/>';
echo'
						</p>
					<hr>
					<p class="mo_registration_pricing_text">'.mo_("Premium Support Plans").'</p>
				</div>
			</td>
		</td>
		<td><div class="mo_registration_thumbnail mo_registration_pricing_free_tab">
				<h3 class="mo_registration_pricing_header">';

echo 				$premium_plan_name.'
					</h3>
				<h4 class="mo_registration_pricing_sub_header">';
						if(strcmp($plan,MoConstants::PCODE)!=0 && strcmp($plan,MoConstants::BCODE)!=0
                            && strcmp($plan,MoConstants::CCODE)!=0 && strcmp($plan,MoConstants::NCODE)!=0)
echo'							<input type="button" style="margin-bottom:3.8%;"  '.$disabled.' class="button button-primary button-large" 
										onclick="mo2f_upgradeform(\'wp_otp_verification_basic_plan\')" 
										value="'.mo_("Upgrade Now").'"></input>';
						else
echo'							<input type="button" style="margin-bottom:3.8%;"  '.$disabled.' class="button button-primary button-large" 
										onclick="mo2f_upgradeform(\'wp_otp_verification_upgrade_plan\')" 
										value="'.mo_("Recharge").'"></input>';

echo' 			</h4>
				<hr>
				<div class="pricing-div"> 
                    <div style="margin-top:4.5%;" class="mo_registration_pricing_text"><b>'.$premium_plan_price.'</b><br/>+</div>
                    <table>
                    <tr>
                        <td class="mo_registration_pricing_text" style="width:25%">For Email:</td>
                        <td><select class="mo-form-control">
                            <option>$5 per 100 Email</option>
                            <option>$15 per 500 Email</option>
                            <option>$22 per 1k Email</option>
                            <option>$30 per 5k Email</option>
                            <option>$40 per 10k Email</option>
                            <option>$90 per 50k Email</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="mo_registration_pricing_text" style="width:25%">For SMS:</td>
                        <td><select class="mo-form-control">
                            <option>$5 per 100 OTP* + SMS delivery charges</option>
                            <option>$15 per 500 OTP* + SMS delivery charges</option>
                            <option>$22 per 1k OTP* + SMS delivery charges</option>
                            <option>$30 per 5k OTP* + SMS delivery charges</option>
                            <option>$40 per 10k OTP* + SMS delivery charges</option>
                            <option>$90 per 50k OTP* + SMS delivery charges</option>
                            </select>
                        </td>		
                    </tr>
                    </table>
                        <p class="mo_registration_pricing_text">
                         <i><span style="color:#b01a1a">'.mo_("SMS delivery charges depend on country").'**</span><br/>
                            '.mo_("You can refill at anytime <br/> Lifetime validity").'</i>
                         <br><br>
                         <span class="pricing_question tooltip">
                            <i style="cursor: pointer;" data-toggle="onprem">[ When do I opt for this plan? ] </i><br/>
                            <span class="tooltiptext" style="margin-left:-130px;">
                                <span class="header"><b><i>'
                                    .MoMessages::showMessage('MO_GATEWAY_HEADER').'</i></b></span><br/><br/><span class="body">'
                                    .MoMessages::showMessage('MO_GATEWAY_BODY').'
                                </span>
                            </span>
                        </span>';
echo'                    
                        </p>				
				 </div>
				<hr>
				<p class="mo_registration_pricing_text">Features:</p>
				<p class="mo_registration_pricing_text" >';

						foreach($premium_plan_features as $feature)
							echo $feature . '<br/>';
echo'
						</p><hr>
				<p class="mo_registration_pricing_text">'.mo_("Premium Support Plans").'</p>
			</div></td>
		</td>
		</tr>

		</table>
		<br>
		<div id="disclaimer" style="margin-bottom:15px;">
			<span style="font-size:15px;">
				<b>'.mo_("SMS gateway").'</b>
					'.mo_(" is a service provider for sending SMS on your behalf to your users.").'<br>
				<b>'.mo_("SMTP gateway").'</b>
					'.mo_(" is a service provider for sending Emails on your behalf to your users.").'<br><br>
				*'.mo_("Transaction prices may very depending on country. If you want to use more than 50k transactions, mail us at").' 
					<a href="mailto:'.MoConstants::SUPPORT_EMAIL.'"><b>'.MoConstants::SUPPORT_EMAIL.'</b></a> 
					'.mo_("or submit a support request using the support form under User").' 
					<a href="'.$profile_url.'">'.mo_("Profile tab").'</a>.<br/><br/>
				**'.mo_("If you want to <b>use miniorange SMS/SMTP gateway</b>, and your country is not in list, mail us at").' <a href="mailto:'.MoConstants::SUPPORT_EMAIL.'"><b>'.MoConstants::SUPPORT_EMAIL.'</b></a>
						'.mo_("or submit a support request using the support form under User").' 
						<a href="'.$profile_url.'">'.mo_("Profile tab").'</a>. 
						'.mo_("We will get back to you promptly.").'<br><br>
				***'.mo_("<b>Custom integration charges</b> will be applied for supporting a registration form which is not already supported 
						by our plugin. Each request will be handled on a per case basis.").'<br>
			</span>
		</div>

		<h3>'.mo_("10 Days Return Policy").' -</h3>
		<p>'.mo_("At miniOrange, we want to ensure you are 100% happy with your purchase.  If the premium plugin you purchased is not working as 
				advertised and you have attempted to resolve any feature issues with our support team, which couldn't get resolved. We will 
				refund the whole amount within 10 days of the purchase. Please email us at").' 
				<a href="mailto:'.MoConstants::SUPPORT_EMAIL.'">'.MoConstants::SUPPORT_EMAIL.'</a> 
				'.mo_("for any queries regarding the return policy.<br> If you have any doubts regarding the licensing plans, you can mail us at"
					).' <a href="mailto:'.MoConstants::SUPPORT_EMAIL.'">'.MoConstants::SUPPORT_EMAIL.'</a> 
				'.mo_("or submit a query using the support form.").'</p>
		<br>

		</div>

	 <form style="display:none;" id="mocf_loginform" action="'.$form_action.'" target="_blank" method="post">
		<input type="email" name="username" value="'.$email.'" />
		<input type="text" name="redirectUrl" value="'.$redirect_url.'" />
		<input type="text" name="requestOrigin" id="requestOrigin"  />
	</form>
	<form id="mo_ln_form" style="display:none;" action="" method="post">';

        wp_nonce_field($nonce);

echo'	<input type="hidden" name="option" value="check_mo_ln" />
	</form>
	<script>
		function mo2f_upgradeform(planType){
			jQuery("#requestOrigin").val(planType);
			jQuery("#mocf_loginform").submit();
		}
	</script>';