
<?php

echo'	
        <div class="mo_otp_form">
            <input type="checkbox" '.$disabled.' id="wpform_basic" class="app_enable" data-toggle="wpform_options" 
                name="mo_customer_validation_wpform_enable" value="1" '.$is_wpform_enabled.' />
                <strong>'.$form_name.'</strong>';

            get_plugin_form_link(MoConstants::WP_FORMS_LINK);

echo        '<div class="mo_registration_help_desc" '.$is_wpform_hidden.' id="wpform_options">
                <b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
                <p>
                    <input type="radio" '.$disabled.' id="wp_form_email" class="app_enable" 
                    data-toggle="wpform_email_option" name="mo_customer_validation_wpform_enable_type" 
                    value="'.$wpform_email_type.'" '.( $wpform_enabled_type == $wpform_email_type ? "checked" : "").' />
                    <strong>'. mo_( "Enable Email Verification" ).'</strong>
                </p>
                        
                
                <div '.($wpform_enabled_type != $wpform_email_type ? "hidden" :"").' class="mo_registration_help_desc" id="wpform_email_option"">
                    <ol>
                        <li><a href="'.$wpform_form_list.'" target="_blank">'. mo_( "Click Here" ).'</a> 
                            '. mo_( " to see your list of forms" ).'</li>
                        <li>'. mo_( "Click on the <b>Edit</b> option of your WPForm." ).'</li>
                        <li>'. mo_( "Add an Email Field to your form. Note the Field Label of the Email field." ).'</li>
                        <li>'. mo_( "Add a Verification Field to your form where users will enter the OTP sent to their Email Address. 
                                    Note the Field Label of the Verification field." ).'</li>
                        <li>'. mo_( "Enter your Form ID, Email Field Label and Verification Field Label below" ).':<br>
                            <br/>'. mo_( "Add Form " ).': <input type="button"  value="+" '. $disabled .' 
                            onclick="add_wpform(\'email\',1);" class="button button-primary" />&nbsp;

                            <input type="button" value="-" '. $disabled .' onclick="remove_wpform(1);" class="button button-primary" />
                            <br/><br/>';
                
                        $form_results = get_wpform_list($wpform_list_of_forms_otp_enabled,$disabled,1); 
                        $counter1     =  !MoUtility::isBlank($form_results['counter']) ? max($form_results['counter']-1,0) : 0 ;                           
echo '              </ol>
                </div>


                <p>
                    <input type="radio" '.$disabled.' id="wp_form_phone" 
                        class="app_enable" data-toggle="wpform_phone_option" name="mo_customer_validation_wpform_enable_type" 
                        value="'.$wpform_phone_type.'"'.( $wpform_enabled_type == $wpform_phone_type ? "checked" : "").' />                                                                            
                    <strong>'. mo_( "Enable Phone Verification" ).'</strong>
                </p>
                    
                <div '.($wpform_enabled_type != $wpform_phone_type ? "hidden" :"").' class="mo_registration_help_desc" 
                    id="wpform_phone_option" '.$disabled.'">
                    <ol>
                        <li><a href="'.$wpform_form_list.'" target="_blank">'. mo_( "Click Here" ).'</a> 
                            '. mo_( " to see your list of forms" ).'</li>
                        <li>'. mo_( "Click on the <b>Edit</b> option of your wp form." ).'</li>
                        <li>'. mo_( "Add an Phone Field to your form. Note the Field Label of the Phone field." ).'</li>
                        <li>'. mo_( "Add a Verification Field to your form where users will enter the OTP sent to their Phone. 
                                    Note the Field Label of the Verification field." ).'</li>
                        <li>'. mo_( "Enter your Form ID, Phone Field Label and Verification Field Label below" ).':<br>
                            <br/>'. mo_( "Add Form " ).': <input type="button"  value="+" '. $disabled .' onclick="add_wpform(\'phone\',2);
                                " class="button button-primary" />&nbsp; <input type="button" value="-" '. $disabled .' \
                                onclick="remove_wpform(2);" class="button button-primary" /><br/><br/>';
                                                
                                $form_results = get_wpform_list($wpform_list_of_forms_otp_enabled,$disabled,2); 
                                $counter2     =  !MoUtility::isBlank($form_results['counter']) ? max($form_results['counter']-1,0) : 0 ;
echo
                        '</ol>
                    </div>  
                    <p style="margin-left:2%;">
                        <i><b>'.mo_("Verification Button text").':</b></i>
                        <input class="mo_registration_table_textbox" name="mo_customer_validation_wpforms_button_text" type="text" value="'.$button_text.'">
                    </p>             
                </div>
        </div>';

echo  '<script>
            var countWpf1, countWpf2;
            function add_wpform(t,n)
            {                
                var countWpfIdpAttr = this["countWpf"+n];
                var hidden1="",hidden2="",space="";
                if(n==1) hidden2 = "hidden";
                if(n==2) hidden1 = "hidden";
                countWpfIdpAttr += 1;
                var sel = "<div id=\'ajax_row_wpform"+n+"_"+countWpfIdpAttr+"\'> '.mo_( "Form ID" ).': "+ 
                    "<input id=\'wp_form_"+n+"_"+countWpfIdpAttr+"\' class=\'field_data\'"+ 
                    "name=\'wpform[form][]\' type=\'text\' value=\'\'/> <span "+hidden1+" >&nbsp;'.mo_( "Email Field Label" ).': "+
                    "<input id=\'wp_form_email_"+n+"_"+countWpfIdpAttr+"\'  class=\'field_data\' name=\'wpform[emailLabel][]\'"+ 
                    "type=\'text\' value=\'\'></span> <span "+hidden2+">"+space+"'.mo_( "Phone Field Label" ).': "+ 
                    "<input id=\'wp_form_phone_"+n+"_"+countWpfIdpAttr+"\' class=\'field_data\' name=\'wpform[phoneLabel][]\' "+
                    "type=\'text\' value=\'\'></span> <span>"+space+"'.mo_( "Verification Field Label" ).': "+ 
                    "<input id=\'wp_form_verify_"+n+"_"+countWpfIdpAttr+"\' class=\'field_data\' name=\'wpform[verifyLabel][]\' "+
                    "type=\'text\' value=\'\'></span></div>";
                if(countWpfIdpAttr!=0)
                        $mo(sel).insertAfter($mo(\'#ajax_row_wpform\'+n+\'_\'+(countWpfIdpAttr-1)+\'\'));
                this["countWpf"+n]=countWpfIdpAttr;
            }

            function remove_wpform()
            {  
                var countWpfIdpAttr =   Math.max(this["countWpf1"],this["countWpf2"]);
                if(countWpfIdpAttr != 0){
                    $mo("#ajax_row_wpform1_" + countWpfIdpAttr).remove();
                    $mo("#ajax_row_wpform2_" + countWpfIdpAttr).remove();
                    $mo("#ajax_row_wpform3_" + countWpfIdpAttr).remove();
                    countWpfIdpAttr -= 1;
                    this["countWpf1"]=this["countWpf2"]=countWpfIdpAttr;
                }
            }
            jQuery(document).ready(function(){  countWpf1 = '. $counter1 .'; countWpf2 = ' .$counter2. '; });
        </script>';

