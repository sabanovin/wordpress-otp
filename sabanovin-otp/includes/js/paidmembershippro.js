jQuery(document).ready(function () {
    $mo = jQuery;
    $mo('<div class="pmpro_checkout-field pmpro_checkout-field-bconfirmemail">' +
        '<label for="phone_paidmembership" >Phone</label>' +
        '<input id="phone_paidmembership" size= "30" class="input pmpro_required" style="max-width: 90%;" name="phone_paidmembership" required="true" type="text">' +
        '<span class="pmpro_asterisk"> <abbr title="Required Field">*</abbr></span>' +
        '</div>').insertAfter(".pmpro_checkout-field-bconfirmemail");
});
