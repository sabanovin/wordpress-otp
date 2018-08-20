<?php

interface AddOnInterface
{
        public function initializeHandlers();
    public function initializeHelpers();
    public function show_addon_settings_page();
}