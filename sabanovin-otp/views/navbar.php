<?php

echo'	<div class="wrap">
			<div><img style="float:left;" src="'.MOV_LOGO_URL.'"></div>
			<div class="otp-header">
				'.mo_("OTP Verification").'
				<a class="add-new-h2" href="'.$profile_url.'">'.mo_("Account ( API Settings )").'</a>
			</div>
		</div>';

echo'	<div id="tab">
			<h2 class="nav-tab-wrapper">';

echo '			<a class="nav-tab '.($active_tab == 'mosettings' ? 'nav-tab-active' : '').'" href="'.$settings		.'">
																						'.mo_("Forms").'</a>
				<a class="nav-tab '.($active_tab == 'otpsettings'? 'nav-tab-active' : '').'" href="'.$otpsettings	.'">
																						'.mo_("Security Settings").'</a>'.
				// <a class="nav-tab '.($active_tab == 'config'   	 ? 'nav-tab-active' : '').'" href="'.$config		.'">
				// 																		'.mo_("SMS/Email Templates").'</a>
				'<a class="nav-tab '.($active_tab == 'messages' 	 ? 'nav-tab-active' : '').'" href="'.$messages		.'">
																						'.mo_("Messages").'</a>
				<a class="nav-tab '.($active_tab == 'design' 	 ? 'nav-tab-active' : '').'" href="'.$design		.'">
								 														'.mo_("Design").'</a>'.
                // <a class="nav-tab '.($active_tab == 'addon' 	 ? 'nav-tab-active' : '').'" href="'.$addon.'">
								// 														'.mo_("AddOns").'</a>
		'</h2>
		</div>';
