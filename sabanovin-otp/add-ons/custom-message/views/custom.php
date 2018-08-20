<?php 

echo'	<div class="mo_registration_divided_layout">
			<div class="mo_registration_table_layout">';

				is_customer_registered();

echo'			<form name="f" method="post" action="">	
					<table class="mo_registration_settings_table" style="width: 100%;">
						<input type="hidden" name="option" value="mo_customer_validation_custom_phone_notif" />
						<input type="hidden" name="action" value="custom_phone_msg" />';
						wp_nonce_field( $nonce ); 
echo'					<tr>
							<td>
								<h2>'.mo_("SEND CUSTOM SMS MESSAGE").'
									<span style="float:right;margin-top:-10px;">
                                        <a href="'.$addon.'" id="goBack" class="button button-primary button-large">'.mo_("Go Back").'</a>
										<input name="save" id="save" class="button button-primary button-large" 
											value="'.mo_("Send Message").'" type="submit">
										<span class="dashicons dashicons-arrow-up toggle-div" data-show="false" data-toggle="custom_sms"></span>
									</span>
								</h2>
								<hr/>
							</td>
						</tr>
					</table>
					<div id="custom_sms">
						<table style="width:100%">
							<tr>
								<td>
									<b>'.mo_("Phone Numbers:").'</b>
									<input '.$disabled.' class="mo_registration_table_textbox" style="border:1px solid #ddd" 
										name="mo_phone_numbers" 
										placeholder="'.mo_("Enter semicolon(;) separated Phone Numbers").'" 
										value="" required="">
									<br/><br/>
								</td>
							</tr>
							<tr>
								<td>
									<b>'.mo_("Message").'</b>
									<span id="characters">Remaining Characters : <span id="remaining"></span> </span>
									<textarea '.$disabled.' id="custom_sms_msg" class="mo_registration_table_textbox" 
										name="mo_customer_validation_custom_sms_msg" 
										placeholder="'.mo_("Enter OTP SMS Message").'" 
										required/></textarea>
									<div class="mo_otp_note">
										'.mo_('You can have new line characters in your sms text body. To enter a new line character use the <b><i>%0a</i></b> symbol. To enter a "#" character you can use the <b><i>%23</i></b> symbol. To see a complete list of special characters that you can send in a SMS check with your gateway provider.').'
									</div>
								</td>
							</tr>
						</table>
					</div>
				</form>
			</div>
			<div class="mo_registration_table_layout">
				<form name="f" method="post" action="">
					<table class="mo_registration_settings_table" style="width: 100%;">
						<input type="hidden" name="option" value="mo_customer_validation_custom_email_notif" />
						<input type="hidden" name="action" value="custom_email_msg" />';
						wp_nonce_field( $nonce ); 
echo'					<tr>
							<td>
								<h2>'.mo_("SEND CUSTOM EMAIL MESSAGE").'
									<span style="float:right;margin-top:-10px;">
									    <a href="'.$addon.'" id="goBack" class="button button-primary button-large">'.mo_("Go Back").'</a>
										<input name="save" id="save" class="button button-primary button-large" 
											value="'.mo_("Send Message").'" type="submit">
										<span class="dashicons dashicons-arrow-up toggle-div" data-show="false" data-toggle="custom_email"></span>
									</span>
								</h2>
								<hr/>
							</td>
						</tr>
					</table>
					<div id="custom_email">
						<table style="width:100%">
							<tr>
								<td>
									<b>'.mo_('From ID:').'</b>
									<div >
										<input  '.$disabled.' id="custom_email_from_id" class="mo_registration_table_textbox" style="border:1px solid #ddd" name="fromEmail" placeholder="'.mo_("Enter email address").'" value = "" required/>
									</div><br>
									<b>'.mo_('From Name:').'</b>
									<div >
										<input  '.$disabled.' id="custom_email_from_name" class="mo_registration_table_textbox" style="border:1px solid #ddd" name="fromName" placeholder="'.mo_("Enter Name").'" value = "" required/>
									</div><br>
									<b>'.mo_('Subject:').'</b>
									<div >
										<input  '.$disabled.' id="custom_email_subject" class="mo_registration_table_textbox" style="border:1px solid #ddd" name="subject" placeholder="'.mo_("Enter your OTP Email Subject").'" value = "" required/>
									</div><br>
									<b>'.mo_('To Email Address:').'</b>
									<div >
										<input  '.$disabled.' id="custom_email_to" class="mo_registration_table_textbox" style="border:1px solid #ddd" name="toEmail" placeholder="'.mo_("Enter semicolon (;) separated email-addresses to send the email to").'" value = "" required/>
									</div><br>
								</td>
							</tr>
							<tr>
								<td>
									<b>'.mo_('Body:').'</b>';
									wp_editor( $content, $editorId ,$templateSettings);
echo'							</td>
							</tr>
						</table>
					</div>
				</form>
			</div>
		</div>';
