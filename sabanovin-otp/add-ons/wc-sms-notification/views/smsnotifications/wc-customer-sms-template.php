<?php
	
	echo'	<div class="mo_registration_divided_layout">
				<div class="mo_registration_table_layout">';
					
					MoAddOnUtiltiy::is_addon_activated();

	echo'			<table id="wc_customer_sms_template" style="width:100%">
						<form name="f" method="post" action="" id="'.$formOptions.'">
							<input type="hidden" name="option" value="'.$formOptions.'" />
							<tr>
								<td colspan="2">
									<h2>'.$smsSettings->pageHeader.'
									<span style="float:right;margin-top:-10px;">
										<a href="'.$goBackURL.'" '.$disabled.' id="goBack" class="button button-primary button-large">
											'.mo_("Go Back").'
										</a>
										<input type="submit" name="save" id="save" class="button button-primary button-large" value="'.mo_('Save Settings').'">
									</span>
									</h2>
									<hr>
								</td>
							</tr>
							<tr>
								<td colspan="2">'.$smsSettings->pageDescription.'</td>
							</tr>
							<tr>
								<td style="width:160px"><h4>'.mo_("Enable/Disable").'</h4></td>
								<td>
									<input class="" '.$disabled.' type="checkbox" name="'.$enableDisableTag.'" 
										id="'.$enableDisableTag.'" style="" value="1" '.$enableDisable.'>
									'.mo_("Enable this SMS Notification").'
								</td>
							</tr>
							<tr>
								<td>
									<h4>'.mo_("SMS Body");
									mo_draw_tooltip(mo_('AVAILABLE TAGS'),$smsSettings->availableTags);
	echo'
									</h4>
								</td>
								<td>
									<textarea '.$disabled.' id="'.$textareaTag.'" class="mo_registration_table_textbox" 
										name="'.$textareaTag.'" placeholder="'.$smsSettings->defaultSmsBody.'" />'.$smsSettings->smsBody.'</textarea>
									<span id="characters">Remaining Characters : <span id="remaining">160</span> </span>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<div class="mo_otp_note">
										'.mo_('You can have new line characters in your sms text body. To enter a new line character use the <b><i>%0a</i></b> symbol. To enter a "#" character you can use the <b><i>%23</i></b> symbol. To see a complete list of special characters that you can send in a SMS check with your gateway provider.').'
									</div>
								</td>
							</tr>
						</form>	
					</table>
				</div>
			</div>';