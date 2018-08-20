<?php 

echo '
            <div class="mo-modal-backdrop">
                <div class="mo_customer_validation-modal" tabindex="-1" role="dialog" id="mo_otp_feedback_modal">
                    <div class="mo_customer_validation-modal-backdrop">
                    </div>
                    <div class="mo_customer_validation-modal-dialog mo_customer_validation-modal-md feedback-modal">
                        <div class="login mo_customer_validation-modal-content">
                            <div class="mo_customer_validation-modal-header">
                                <b>FEEDBACK</b>
                                <a class="close" href="#" onclick="mo_otp_feedback_goback()">'.mo_('&larr; Go Back').'</a>
                            </div>
                            <form id="mo_otp_feedback_form" name="f" method="post" action="">
                                 <div class="mo_customer_validation-modal-body">
                                    <div class="mo_otp_note mo_feedback_note" hidden><i><b>'.$message.'</i></b></div>
                                    <br>
                                    <div class="mo_otp_feedback_form_div">
                                        <input type="hidden" name="option" value="mo_otp_feedback_option"/>
                                        <input type="hidden" value="false" id="feedback_type" name="plugin_deactivated"/>';

                                        wp_nonce_field($nonce);

                                        foreach ( $deactivate_reasons as $r )
                                        {
                                            echo'<div  class="radio" style="padding:1px;margin-left:2%">
                                                    <label style="font-weight:normal;font-size:14px" for="'.$r["topic"].'">
                                                        <input type="radio" name="feedback_reason" value="'.$r["topic"].'" data-message="'.$r["message"].'" 
                                                            data-textbox="'.$r['showTextBox'].'" class="feedback_radio" required>
                                                        '.$r["topic"].'
                                                    </label>
                                                </div>';
                                        }

echo'                                   
                                        <i><div class="mo_otp_note" hidden id="feedback_message" style="padding:10px;color:darkred;"></div></i>
                                        <br/>
                                        <textarea hidden id="query_feedback" name="query_feedback" style="width:100%" rows="2" placeholder="Type your feedback here"></textarea>
                                    </div>
                                </div>
                                <div class="mo_customer_validation-modal-footer" >
                                    <input type="button" id="mo_feedback_cancel_btn" class="button button-primary button-large" onclick="mo_otp_feedback_goback()" 
                                        value="'.mo_('&larr; Go Back').'"/>
                                        <input type="submit" name="miniorange_feedback_submit" class="button button-primary button-large" 
                                        data-sm="'.$submit_message.'" data-sm2="'.$submit_message2.'" value="" />
                                </div>
                            </form>    
                        </div>
                    </div>
                </div>
            </div>
       ';