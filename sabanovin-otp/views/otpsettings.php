<?php

echo'	<div class="mo_registration_divided_layout">
			<form name="f" method="post" action="" id="mo_otp_verification_settings">
				<div class="mo_registration_table_layout">';

					is_customer_registered();

echo'				<table style="width:100%">
						<input type="hidden" name="option" value="mo_otp_extra_settings" />';
						wp_nonce_field( $nonce );
echo'					<tr>
							<td colspan="2">
								<h2>'.mo_("SECURITY SETTINGS").'
								<span style="float:right;margin-top:-10px;">
									<input type="submit" '.$disabled.' name="save" id="ov_settings_button"
										class="button button-primary button-large" value="'.mo_("Save Settings").'"/>
								</span>
								</h2><hr>
							</td>
						</tr>';
echo'				</table>
				<p>'.mo_("Apply security limitation here").'
				</div>
				<div class="mo_registration_table_layout">
					<table style="width:100%">
						<tr>
							<td>
								<h3>
									'.mo_("COUNTRY CODE: ").'
									<span class="dashicons dashicons-arrow-up toggle-div" data-show="false" data-toggle="country_code_settings"></span>
								</h3><hr>
							</td>
						</tr>
					</table>
					<div id="country_code_settings">
						<table style="width:100%">
							<tr>
								<td colspan="2">
									<strong><i>'.mo_("Select Default Country Code").': </i></strong>
								';

									get_country_code_dropdown();
									mo_draw_tooltip(MoMessages::showMessage('COUNTRY_CODE_HEAD'),MoMessages::showMessage('COUNTRY_CODE_BODY'));

									echo "<i style='margin-left:1%''>".mo_("Country Code").": <span id='country_code'></span></i> " ;

echo							'</td>
							</tr>
							<tr>
								<td colspan="2"><br/><input type="checkbox" '.$disabled.' name="show_dropdown_on_form" value="1"'.$show_dropdown_on_form.' /> '.mo_("Show a country code dropdown on the phone field.").'</td>
							</tr>
						</table>
					</div>
				</div>
				<div class="mo_registration_table_layout">
					<table style="width:100%">
						<tr>
							<td colspan="2">
								<h3>
									<i>'.mo_("BLOCKED EMAIL DOMAINS: ").'</i>
									<span class="dashicons dashicons-arrow-up toggle-div" data-show="false" data-toggle="blocked_email_settings"></span>
								</h3><hr>
							</td>
						</tr>
					</table>
					<div id="blocked_email_settings">
						<table style="width:100%">
							<tr>
								<td colspan="2"><textarea name="mo_otp_blocked_email_domains" rows="5" style="width:100%;height:50px"
									placeholder="'.mo_(" Enter semicolon separated domains that you want to block. Eg. gmail.com ").'">'.$otp_blocked_email_domains.'</textarea></td>
							</tr>
						</table>
					</div>
				</div>
				<div class="mo_registration_table_layout">
					<table style="width:100%">
						<tr>
							<td colspan="2">
								<h3>
									<i>'.mo_("BLOCKED PHONE NUMBERS: ").'</i>
									<span class="dashicons dashicons-arrow-up toggle-div" data-show="false" data-toggle="blocked_sms_settings"></span>
								</h3><hr>
							</td>
						</tr>
					</table>
					<div id="blocked_sms_settings">
						<table style="width:100%">
							<tr>
								<td colspan="2"><textarea name="mo_otp_blocked_phone_numbers" rows="5" style="width:100%;height:50px"
									placeholder="'.mo_(" Enter semicolon separated phone numbers (with country code) that you want to block. Eg. +1XXXXXXXX ").'">'.$otp_blocked_phones.'</textarea></td>
							</tr>
						</table>
					</div>
				</div>
			</form>
		</div>';
