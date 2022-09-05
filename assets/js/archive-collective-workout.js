jQuery(document).ready(function(){
    $('#gyms-filter').change(function(){
        get_gyms_attached_collective_workout(parseFloat($(this).val()));
    });
});

function get_gyms_attached_collective_workout(gym_id){
    $.ajax({
        url: main.ajax_url,
        type: 'POST',
        data: {
            'action': 'workout_manager', 
            'function': 'get_gyms_attached_collective_workout',
            'data': {
                'gym_id': gym_id
            }
        },
        success: data => {
            console.log(data.collective_workout);
            if (data.collective_workout !== '') {
                console.log('ici');
                $('#collective-workout-list').html(data.collective_workout);
            } else {
                $("#collective-workout-list").html("<h4 class='text-center'>Désolé, nous n'avons pas de résultats.</h4>");
            }
        },
        complete: data =>{
            //$('.news-loader').css('display', 'none');
        }
    });
}