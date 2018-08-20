<?php

    class BaseActionHandler
    {
        protected $_nonce;
        
        
        protected function isValidRequest()
        {
            return ( !current_user_can( 'manage_options' ) || !MoUtility::micr()
            || !check_admin_referer( $this->_nonce )) ? FALSE : TRUE;
        }


        
        public function getNonceValue(){ return $this->_nonce; }
    }