<?php

echo'	<div class="mo_registration_divided_layout">
				<div class="mo_registration_table_layout">';

                is_customer_registered();

echo'			<table style="width:100%">
						<form name="f" method="post" action="" id="mo_add_on_settings">
							<input type="hidden" name="option" value="mo_add_on_settings" />
							<tr>
								<td>
									<h2>'.mo_("OTP VERIFICATION ADD-ONS").'</h2>
									<hr>
								</td>
							</tr>
							<tr>
								<td>'.mo_("Various OTP Verification add-ons. Click on the configure button below to see add-on settings").'</td>
							</tr>
							<tr>
								<table class="addon-table-list" cellspacing="0">
									<thead>
										<tr>
											<th class="addon-table-list-status" style="width:20px;">Add On</th>
											<th class="addon-table-list-name">Description</th>
											<th class="addon-table-list-actions" style="width:10px;">Actions</th>						
										</tr>
									</thead>
									<tbody>
									    <tr>
									        <td class="addon-table-list-status">'.mo_("WooCommerce SMS Notification").'</td>
									        <td class="addon-table-list-name">
									            <i>
									                '.mo_("Allows your site to send order and WooCommerce notifications to buyers, sellers and admins. Click on the settings button to the right to see the list of notifications that go out.").'
									            </i>
									        </td>
									        <td class="addon-table-list-actions">
									            <a class="button tips" href="'.$woocommerce_url.'">'.mo_("Settings").'</a>
									        </td>
                                        </tr>
                                        <tr>
									        <td class="addon-table-list-status">'.mo_("Send Custom Message").'</td>
									        <td class="addon-table-list-name">
									            <i>
									                '.mo_("Send Customized message to any phone or email directly from the dashboard.").'
									            </i>
									        </td>
									        <td class="addon-table-list-actions">
									            <a class="button tips" href="'.$custom.'">'.mo_("Settings").'</a>
									        </td>
                                        </tr>
                                    </tbody>
								</table>
							</tr>
						</form>	
					</table>
				</div>
			</div>';