<?php
	if(isset($_POST['sabanovin_setting_nonce']) and wp_verify_nonce($_POST['sabanovin_setting_nonce'], 'sabanovin_setting')){
		// api key
		$api_key = isset($_POST['sabanovin_api_key']) ? $_POST['sabanovin_api_key'] : '';

		// Gateway
		$gateway = isset($_POST['sabanovin_gateway']) ? $_POST['sabanovin_gateway'] : '';

		// save api key and Gateway
		update_option('sabanovin_api_key', $api_key);
		update_option('sabanovin_gateway', $gateway);

	}
?>
<div class="mo_registration_divided_layout">
	<div class="mo_registration_table_layout">
		<h1><?php echo mo_("Sabanovin account setting");?></h1>
		<form method="post" id="sabanovin_account_form">
			<table class="form-table">
				<tr>
					<th>
						<label for="sabanovin_api_key"><?php echo mo_("API Key");?> : </label>
					</th>
					<td>
						<input type="text" id="sabanovin_api_key" name="sabanovin_api_key" placeholder="<?php echo mo_("API Key");?>" value="<?php echo get_option('sabanovin_api_key');?>">
					</td>
				</tr>

				<tr>
					<th>
						<label for="sabanovin_gateway"><?php echo mo_("Gateway");?> : </label>
					</th>
					<td>
						<input type="text" id="sabanovin_gateway" name="sabanovin_gateway" placeholder="<?php echo mo_("Gateway");?>" value="<?php echo get_option('sabanovin_gateway');?>">
					</td>
				</tr>
			</table>
			<?php wp_nonce_field('sabanovin_setting', 'sabanovin_setting_nonce');?>
			<?php submit_button(mo_("Save"));?>
		</form>
	</div>
</div>
