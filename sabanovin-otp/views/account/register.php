<?php	
	
echo'<!--Register with miniOrange-->
	<form name="f" method="post" action="" id="register-form">';
        wp_nonce_field($nonce);
echo'	<input type="hidden" name="option" value="mo_registration_register_customer" />
		<div class="mo_registration_divided_layout">
			<div class="mo_registration_table_layout">
				<h3>'.mo_("REGISTER WITH MINIORANGE").'</h3>
				<p>'.mo_("Please enter a valid email that you have access to. You will be able to move forward after verifying an OTP that we will be sending to this email.").' 
							<b>OR</b> '.mo_("Login using your miniOrange credentials.").'</p>
				<table class="mo_registration_settings_table">
					<tr>
						<td><b><font color="#FF0000">*</font>'.mo_("Email:").'</b></td>
						<td><input class="mo_registration_table_textbox" type="email" name="email"
							required placeholder="person@example.com"
							value="'.$current_user->user_email.'" /></td>
					</tr>

					<tr>
						<td><b><font color="#FF0000">*</font>'.mo_("Website/Company Name:").'</b></td>
						<td><input class="mo_registration_table_textbox" type="text" name="company"
							required placeholder="'.mo_("Enter your companyName").'"
							value="'.$_SERVER["SERVER_NAME"].'" /></td>
						<td></td>
					</tr>

					<tr>
						<td><b>&nbsp;&nbsp;'.mo_("FirstName:").'</b></td>
						<td><input class="mo_registration_table_textbox" type="text" name="fname"
							placeholder="'.mo_("Enter your First Name").'"
							value="'.$current_user->user_firstname.'" /></td>
						<td></td>
					</tr>

					<tr>
						<td><b>&nbsp;&nbsp;'.mo_("LastName:").'</b></td>
						<td><input class="mo_registration_table_textbox" type="text" name="lname"
							placeholder="'.mo_("Enter your Last Name").'"
							value="'.$current_user->user_lastname.'" /></td>
						<td></td>
					</tr>

					<tr>
						<td><b>&nbsp;&nbsp;'.mo_("Phone number:").'</b></td>
						<td><input class="mo_registration_table_textbox" type="tel" id="phone"
							pattern="[\+]\d{7,14}|[\+]\d{1,4}[\s]\d{6,12}" name="phone"
							title="'.MoMessages::showMessage('MO_REG_ENTER_PHONE').'"
							placeholder="'.MoMessages::showMessage('MO_REG_ENTER_PHONE').'"
							value="'.$admin_phone.'" /><br>'.mo_("We will call only if you need support.").'</td>
						<td></td>
					</tr>

					<tr>
						<td><b><font color="#FF0000">*</font>'.mo_("Password:").'</b></td>
						<td><input class="mo_registration_table_textbox" required type="password"
							name="password" placeholder="'.mo_("Choose your password (Min. length 6)").'" /></td>
					</tr>
					<tr>
						<td><b><font color="#FF0000">*</font>'.mo_("Confirm Password:").'</b></td>
						<td><input class="mo_registration_table_textbox" required type="password"
							name="confirmPassword" placeholder="'.mo_("Confirm your password").'" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><br /><input type="submit" name="submit" value="'.mo_("Register").'" style="width:100px;"
							class="button button-primary button-large" />
							<a href="#goToLoginPage" class="button button-primary button-large">'.mo_("Already Have an Account? Sign In").'</a>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</form>
	<form id="goToLoginPageForm" method="post" action="">';
        wp_nonce_field($nonce);
echo'	<input type="hidden" name="option" value="mo_go_to_login_page" />
	</form>
	<script>
		jQuery(document).ready(function(){
			$mo(\'a[href="#forgot_password"]\').click(function(){
				$mo("#forgotpasswordform").submit();
			});

			$mo(\'a[href="#goToLoginPage"]\').click(function(){
				$mo("#goToLoginPageForm").submit();
			});
		});
	</script>';