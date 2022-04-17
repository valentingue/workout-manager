jQuery(document).ready(function($) {

    // Gestion de l'authentification
    $('form#athlete-login').on('submit', function(e){
        
        $('form#athlete-login .loader').removeClass("d-none");
        $('form#athlete-login button').attr("disabled" , "true");
        $('form#athlete-login button').addClass("disabled");


        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: workout_manager_athlete_ajax_object.ajaxurl,
            data: { 
                'action': 'workout_manager', 
                'function' : 'login',
                'data':workout_manager_get_form_datas('athlete-login')
               
            },
            success: function(data){
                let html_message = data.message.replace(/\n/g, "<br />");
       
                if(data.success === -1) {
                    toastr.error(html_message);
                } else if(data.success === 0) {
                    toastr.warning(html_message);
                } else {
                    toastr.success(html_message);
                    document.location.href = data.redirecturl;
                }
            },
            complete: function(data){
                $('form#athlete-login .loader').addClass("d-none");
                $('form#athlete-login button').removeAttr("disabled");
                $('form#athlete-login button').removeClass("disabled");
            },
        });
        e.preventDefault();
    });


    // Gestion de l'inscription
    $('form#athlete-register').on('submit', function(e){

        $('form#athlete-register .loader').removeClass("d-none");
        $('form#athlete-register button').attr("disabled" , "true");
        $('form#athlete-register button').addClass("disabled");

        $.ajax({
          type: "POST",
          dataType: "json",
          url: workout_manager_athlete_ajax_object.ajaxurl,
          data: {
            action: "workout_manager",
            function: "register",
            data: workout_manager_get_form_datas("athlete-register"),
          },
            success: function (data) {

            let html_message = data.message.replace(/\n/g, "<br />");

            if (data.success === -1) {
              toastr.error(html_message);
            } else {
              toastr.success(html_message);
              $("#modal-user-datas .btn-close").click();
            }
          },
          complete: function (data) {
            $("form#athlete-register .loader").addClass("d-none");
            $("form#athlete-register button").removeAttr("disabled");
            $("form#athlete-register button").removeClass("disabled");
          },
        });
        e.preventDefault();
    });

    // Gestion des modifications de coordonnées
    $('form#athlete-infopersos').on('submit', function(e){

        let alert = $('form#athlete-infopersos .alert');
        alert.addClass("d-none");
        
        $('form#athlete-infopersos .loader').removeClass("d-none");

        $('form#athlete-infopersos button').attr("disabled" , "true");
        $('form#athlete-infopersos button').addClass("disabled");

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: workout_manager_athlete_ajax_object.ajaxurl,
            data: { 
                'action': 'workout_manager', 
                'function' : 'edit_profile',
                'data': workout_manager_get_form_datas('athlete-infopersos')
            },
            success: function(data){
                let html_message = data.message.replace(/\n/g, "<br />");

                if(data.success === -1) {
                    toastr.error(html_message);
                } else if(data.success === 0) {
                    toastr.danger(html_message);
                } else {
                    toastr.success(html_message);
                }
             
            },
            complete: function(data){
                $('form#athlete-infopersos .loader').addClass("d-none");
                $('form#athlete-infopersos button').removeAttr("disabled");
                $('form#athlete-infopersos button').removeClass("disabled");
            },
        });
        e.preventDefault();
    });
});



function workout_manager_get_form_datas(form_id){

    // get data from form as associative array
    let data = {};

    $("#"+form_id).serializeArray().forEach(fieldData => {

        // add to data list
        if (!!data[fieldData.name]) {
            // convert string to array to save more data in this field
            if (typeof data[fieldData.name] === "string") {
                data[fieldData.name] = [data[fieldData.name]];
            }
            data[fieldData.name].push(fieldData.value);
        } else {
            data[fieldData.name] = fieldData.value;
        }
    });

    return data;

}

function workout_manager_delete_account(){

    if( !confirm(workout_manager_athlete_ajax_object.delete_account_confirmation_sentence)) {
        return false;
    }
    else{

        let alert = $('form#athlete-infopersos .alert');
        alert.addClass("d-none");
        
        $('form#athlete-infopersos .loader').removeClass("d-none");

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: workout_manager_athlete_ajax_object.ajaxurl,
            data: { 
                'action': 'workout_manager', 
                'function' : 'delete_account',
                'data': workout_manager_get_form_datas('athlete-infopersos')
            },
            success: function(data){
                let html_message = data.message.replace(/\n/g, "<br />");

                if(data.success == -1) {
                    toastr.error(html_message);
                } else if(data.success == 0) {
                    toastr.warning(html_message);
                } else {
                    toastr.success(html_message);
                    document.location.href = data.redirecturl;
                }
            },
            complete: function(data){
                $('form#athlete-infopersos .loader').addClass("d-none");
            },
        });
        

    }

}