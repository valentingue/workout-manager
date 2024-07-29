jQuery(document).ready(function(){
    jQuery('.formule-group input[type=radio]').each(function(e, currentTarget){
        jQuery(this).change(function(){
            jQuery('.formule-group input[type=radio]').not(jQuery(this)).each(function(t, formuleTarget){
                formuleTarget.checked = false;
            });
        });
    });

    jQuery('.payment-method-group input[type=radio]').each(function(e, currentTarget){
        jQuery(this).change(function(){
            jQuery('.payment-method-group input[type=radio]').not(jQuery(this)).each(function(t, paymentTarget){
                paymentTarget.checked = false;
            });
        });
    });

    jQuery('.reglement-date-group input[type=radio]').each(function(e, currentTarget){
        jQuery(this).change(function(){
            jQuery('.reglement-date-group input[type=radio]').not(jQuery(this)).each(function(t, reglementTarget){
                reglementTarget.checked = false;
            });
        });
    });
    /* let params = new URLSearchParams(window.location);
    let pdfFields = [];
    for (let p of params) {
        pdfFields.push()
    } */
    let url = new URL(window.location);
    let params = [];
    for (let param of url.searchParams) {
        if(Object.values(param)[0].includes('workout_manager_response[workout_manager]')){
            let value = Object.values(param)[0].slice(42, -1);
            params[value] = Object.values(param)[1];
        }
    }
    console.log(params);
    //const pdfDoc = new PDFLib.PDFDocument.create()
})