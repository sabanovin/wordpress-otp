<?php

$content 		   = '';
$editorId 		   = 'customEmailMsgEditor';
$templateSettings  = array('media_buttons'=>false,'textarea_name'=>'content','editor_height' => '170px',
							'wpautop'=>false);
$handler           = CustomMessages::instance();
$nonce 			   = $handler->getNonceValue();

include MCM_DIR . 'views/custom.php';