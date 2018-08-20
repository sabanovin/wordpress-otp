<?php

echo '<div class="mo_registration_divided_layout">
	<div class="mo_registration_table_layout">';

	if(!$vl)
	{
echo'	
		<div>
			<div style="width:50%;float:left;"><h4>Thank you for registering with us.</h4></div>
			<span style="width:50%;float:left;text-align:right;margin: 1em 0 1.33em 0">
				<input type="button" '.$disabled.' name="check_btn" id="check_btn" class="button button-primary button-large" value="'.mo_("Check License").'"/>
				<input type="button" '.$disabled.' name="remove_accnt" id="remove_accnt" class="button button-primary button-large" value="'.mo_("Remove Account").'"/>
			</span>';
	}
	else
	{
echo '<div>
		<div style="width:100%;float:left;"><h4>'.mo_("Thank you for registering with us.").'</h4></div>';
	}

echo'
	</div>
		<h3>'.mo_("Your Profile").'</h3>
		<table border="1" style="background-color:#FFFFFF; border:1px solid #CCCCCC; border-collapse: collapse; padding:0px 0px 0px 10px; margin:2px; width:100%">
			<tr>
				<td style="width:45%; padding: 10px;"><b>'.mo_("Registered Email").'</b></td>
				<td style="width:55%; padding: 10px;">'.$email.'</td>
			</tr>
			<tr>
				<td style="width:45%; padding: 10px;"><b>'.mo_("Customer ID").'</b></td>
				<td style="width:55%; padding: 10px;">'.$customer_id.'</td>
			</tr>
			<tr>
				<td style="width:45%; padding: 10px;"><b>'.mo_("API Key").'</b></td>
				<td style="width:55%; padding: 10px;">'.$api_key.'</td>
			</tr>
			<tr>
				<td style="width:45%; padding: 10px;"><b>'.mo_("Token Key").'</b></td>
				<td style="width:55%; padding: 10px;">'.$token.'</td>
			</tr>
		</table><br/><hr>
		<h3>'.mo_("Track your Transactions:").'</h3>
		<div style="margin-left:2%;">
			<b>'.mo_("Follow these steps to view your transactions:").'</b>
			<ol>
				<li>'.mo_("Click on the button below.").'</li>
				<li>'.mo_("Login using the credentials you used to register for this plugin.").'</li>
				<li>'.mo_("You will be presented with <i><b>View Transactions</b></i> page.").'</li>
				<li>'.mo_("From this page you can track your remaining transactions").'</li>
			</ol>
			<div style="margin-top:2%;text-align:center">
				<input type="button" title="'.mo_("Need to be registered for this option to be available").'" value="'.mo_("View Transactions").'" onclick="extraSettings(\''.MoConstants::HOSTNAME.'\',\'/moas/viewtransactions\');" class="button button-primary button-large" style="margin-right: 3%;">
			</div>
		</div>
		<form id="showExtraSettings" action="'.MoConstants::HOSTNAME.'/moas/login" target="_blank" method="post">
	       <input type="hidden" id="extraSettingsUsername" name="username" value="'.$email.'" />
	       <input type="hidden" id="extraSettingsRedirectURL" name="redirectUrl" value="" />
	       <input type="hidden" id="" name="requestOrigin" value="'.$plan_type.'" />
		</form>
		<form id="mo_ln_form" style="display:none;" action="" method="post">';
			wp_nonce_field( $nonce ); 
echo		'<input type="hidden" name="option" value="check_mo_ln" />
		</form>
		<form id="remove_accnt_form" style="display:none;" action="" method="post">';
			wp_nonce_field( $regnonce );
echo'		<input type="hidden" name="option" value="remove_account" />
		</form>
		<br/>
	</div>
</div>';