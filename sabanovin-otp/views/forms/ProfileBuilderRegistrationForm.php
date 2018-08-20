<?php


echo' 	<div class="mo_otp_form"><input type="checkbox" '.$disabled.' id="pb_default" class="app_enable" name="mo_customer_validation_pb_default_enable" value="1" data-toggle="pb_default_options"
			'.$pb_enabled.' /><strong>'. $form_name .'</strong>';

				get_plugin_form_link(MoConstants::PB_FORM_LINK);

	echo'	<div class="mo_registration_help_desc" '.$pb_hidden.' id="pb_default_options">
				<b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
				<p>
					<input type="radio" '.$disabled.' id="pb_phone" class="app_enable" data-toggle="pb_phone_options" name="mo_customer_validation_pb_enable_type" value="'.$pb_reg_type_phone.'"
						'.($pb_enable_type == $pb_reg_type_phone ? "checked" : "" ).'/>
						<strong>'. mo_( "Enable Phone Verification" ).' <i>'.  mo_( "( Requires Hobbyist Version )" ) . '</i></strong>
				</p>
				<div '.($pb_enable_type != $pb_reg_type_phone ? "hidden" :"").' id="pb_phone_options" class="pb_form mo_registration_help_desc" >
					<ol>
						<li><a href="'.$pb_fields.'"  target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of fields" ).'</li>
						<li>'. mo_( "Choose a phone field from the Field Dropdown" ).'</li>
						<li>'. mo_( "Keep track of the Meta Name of the phone field as you will need it later on." ).'</li>
						<li>'. mo_( "Make sure to mark the phone field as required." ).'</li>
						<li>'. mo_( "Enter the meta name of your phone field" ).': <input class="mo_registration_table_textbox" id="pb_phone_field_key" name="pb_phone_field_key" type="text" value="'.$pb_phone_key.'"></li>
					</ol>
				</div>
				<p>
					<input type="radio" '.$disabled.' id="pb_email" class="app_enable" name="mo_customer_validation_pb_enable_type" value="'.$pb_reg_type_email.'"
						'.($pb_enable_type == $pb_reg_type_email? "checked" : "" ).'/>
						<strong>'. mo_( "Enable Email Verification" ).'</strong>
				</p>
				<p>
					<input type="radio" '.$disabled.' id="pb_both" class="app_enable" name="mo_customer_validation_pb_enable_type" data-toggle="pb_both_options"
						value="'.$pb_reg_type_both.'" '.($pb_enable_type == $pb_reg_type_both? "checked" : "" ).'/>
						<strong>'. mo_( "Let the user choose" ).'</strong>';
							mo_draw_tooltip(MoMessages::showMessage('INFO_HEADER'),MoMessages::showMessage('ENABLE_BOTH_BODY'));
echo '			</p>
				<div '.($pb_enable_type != $pb_reg_type_both ? "hidden" :"").' id="pb_both_options" class="pb_form mo_registration_help_desc" >
					<ol>
						<li><a href="'.$pb_fields.'"  target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of fields" ).'</li>
						<li>'. mo_( "Choose a phone field from the Field Dropdown" ).'</li>
						<li>'. mo_( "Keep track of the Meta Name of the phone field as you will need it later on." ).'</li>
						<li>'. mo_( "Make sure to mark the phone field as required." ).'</li>
						<li>'. mo_( "Enter the meta name of your phone field" ).': <input class="mo_registration_table_textbox" id="pb_phone_field_key1" name="pb_phone_field_key" type="text" value="'.$pb_phone_key.'"></li>
					</ol>
				</div>
			</div>';

echo' 	</div>';