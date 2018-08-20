<?php

echo'	<div class="mo_registration_divided_layout">
			<form name="f" method="post" action="" id="mo_otp_verification_settings">
				';
				is_customer_registered();

				echo'				<input type="hidden" id="error_message" name="error_message" value="">
				<input type="hidden" name="option" value="mo_customer_validation_settings" />';

				wp_nonce_field( $nonce );
				echo '
				<div class="mo_registration_table_layout">
					<table style="width:100%">
						<tr>
							<td colspan="2">
								<h2 style="float:left">'.mo_("Select your form from the list below").':</h2>
								
							</td>
						</tr>
						<tr>
							<td colspan="2">';

								get_otp_verification_form_dropdown();
echo'
							</td>
						</tr>
					</table>
				</div>
				<div class="mo_registration_table_layout">
					<table id="mo_forms" style="width: 100%;">
						<tr>
							<td>
								<h2>
									<i>'.mo_("FORM SETTINGS").'</i>
									<span class="dashicons dashicons-arrow-up toggle-div" data-show="false" data-toggle="new_form_settings"></span>
								</h2><hr>
							</td>
						</tr>
						<tr>
							<td>
								<div id="new_form_settings">
									<div class="mo_otp_note">
										<div id="text">'.mo_("Please select a form from the list above to see it's settings here.").'</div>
										<img id="loader" style="display:none" src="'.MOV_LOADER_URL.'">
									</div>
									<div id="form_details"></div>
								</div>
							</td>
						</tr>
					</table>
				</div>
				<div class="mo_registration_table_layout">
					<table style="width:100%">
						<tr>
							<td>
								<h2>
									<i>'.mo_("CONFIGURED FORMS").'</i>
									<span class="dashicons dashicons-arrow-up toggle-div" data-show="false" data-toggle="configured_mo_forms"></span>
								</h2><hr>
							</td>
						</tr>
					</table>
					<div id="configured_mo_forms" style="width:100%">';
						show_configured_form_details($controller,$disabled,$page_list);
echo'				</div>
				</div>
				<input type="button" id="ov_settings_button"
						'.$disabled.' style="display:none;"
						class="button button-primary button-large" />
			</form>
		</div>';
