jQuery(document).ready(function () {
    $mo = jQuery;
    $mo('<input id="option" name="option" value="multisite_register" hidden>').insertAfter("#user_email");
    $mo('<label for="multisite_user_phone">Phone</label><input name="multisite_user_phone_miniorange" id="multisite_user_phone"  class="required" type="text">').insertAfter("#user_email");
});