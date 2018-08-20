<?php
	
	echo '
			<div id="custom_order_sms_meta_box">
				<input type="hidden" id="post_ID" name="post_ID" value="'.$orderDetails->get_order_number().'">
				<div id="jsonMsg" hidden></div>
				'.mo_("Billing Phone").': <input type="text" id="billing_phone" name="billing_phone" value="'.$orderDetails->get_billing_phone().'" style="width:67%"/>
				<p>
					<textarea type="text" name="mo_wc_custom_order_msg" id="mo_wc_custom_order_msg" class="mo_registration_table_textbox" style="width: 100%;" 
						rows="4" value="" placeholder="'.mo_("Your custom message to be sent to the user").'""></textarea>
				</p>
				<p>
					<a class="button button-primary" id="mo_custom_order_send_message">'.mo_("Send SMS").'</a>
	        		<span id="characters" style="font-size:12px;float:right;">Remaining Characters : <span id="remaining">160</span> </span>
	        	</p>
        	</div>';