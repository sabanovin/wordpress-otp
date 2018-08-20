<?php

echo '<div class="mo_registration_divided_layout">
		<div class="mo_registration_table_layout">';
			is_customer_registered();
		
echo '		<table style="width: 100%;">
				<tr>
					<td colspan="3">
						<h3>
							'.mo_("SMS & EMAIL CONFIGURATION").'
							<span class="dashicons dashicons-arrow-up toggle-div" data-show="false" data-toggle="configuration_instructions"></span>
						</h3>
						<hr>
					</td>
				</tr>
			</table>
			<div id="configuration_instructions">
				<table style="width: 100%;">
					<tr>
						<td>
						    <div class="mo_otp_note" style="color:#942828;">
                                <b>'.mo_("Look at the sections below to customize the Email and SMS that you receive:").'</b>
                                <ol>
                                    <li>
                                        <b>
                                            <a href="'.MoConstants::FAQ_BASE_URL.'set-sms-email-otp-message-template/" target="_blank">
                                                '.mo_("Custom SMS Template").'
                                            </a> :
                                        </b> '.mo_("Change the text of the message that you receive on your phones.").'
                                    </li>
                                    <li>
                                        <b>
                                            <a href="'.MoConstants::FAQ_BASE_URL.'use-own-gateway-plugin/" target="_blank">
                                                '.mo_("Custom SMS Gateway").'
                                            </a> :
                                        </b> '.mo_("You can configure settings to use your own SMS gateway.").'
                                    </li>
                                    <li>
                                        <b>
                                            <a href="'.MoConstants::FAQ_BASE_URL.'set-sms-email-otp-message-template/" target="_blank">
                                                '.mo_("Custom Email Template").'
                                            </a> :
                                        </b> '.mo_("Change the text of the email that you receive.").'
                                    </li>
                                    <li>
                                        <b>
                                            <a href="'.MoConstants::FAQ_BASE_URL.'use-own-gateway-plugin/" target="_blank">
                                                '.mo_("Custom Email Gateway").'
                                            </a> :
                                        </b> '.mo_("You can configure settings to use your own Email gateway.").'
                                    </li>
                                </ol>
                            </div>
                        </td>
					</tr>
					<tr>
						<td>
							<div class="mo_otp_note" style="color:#942828;">
								<b>
                                    <a href="'.MoConstants::FAQ_BASE_URL.'change-sender-id/" target="_blank">
                                        '.mo_("How can I change the senderid/number of the sms i receive?").'
                                    </a>
								</b>				
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="mo_otp_note" style="color:#942828;">
								<b>
								    <a href="'.MoConstants::FAQ_BASE_URL.'change-email-address/" target="_blank">
									        '.mo_("How can I change the sender email of the email i receive?").'
									</a>
								</b>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>		
	</div>';