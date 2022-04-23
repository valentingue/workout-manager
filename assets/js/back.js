jQuery(document).ready(function(){
    jQuery('.formule-group input[type=radio]').each(function(e, currentTarget){
        jQuery(this).change(function(){
            jQuery('.formule-group input[type=radio]').not(jQuery(this)).each(function(t, formuleTarget){
                console.log(jQuery(formuleTarget));
                formuleTarget.checked = false;
            });
        });
    });

    jQuery('.payment-method-group input[type=radio]').each(function(e, currentTarget){
        jQuery(this).change(function(){
            jQuery('.payment-method-group input[type=radio]').not(jQuery(this)).each(function(t, paymentTarget){
                console.log(jQuery(paymentTarget));
                paymentTarget.checked = false;
            });
        });
    });

    jQuery('.reglement-date-group input[type=radio]').each(function(e, currentTarget){
        jQuery(this).change(function(){
            jQuery('.reglement-date-group input[type=radio]').not(jQuery(this)).each(function(t, reglementTarget){
                console.log(jQuery(reglementTarget));
                reglementTarget.checked = false;
            });
        });
    });
})