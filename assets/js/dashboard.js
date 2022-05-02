$(document).ready(function(){  
    /* -------------------------------------------------------------------------- */
    /*                                  Calendar                                  */
    /* -------------------------------------------------------------------------- */
    $('.workouts-calendar').each((index, value)=>{

        if( $(value).attr('data-training-days') !== '[]'){
            let training_days = [];
            let days = JSON.parse($(value).attr('data-training-days'));

            days.forEach(function(currentValue, index, arr){
                training_days.push( { title: 'Entrainement', start: new Date(currentValue) } ) ;
            });

            var calendarEl = document.getElementById('calendar'+'-'+$(value).attr('data-workout-name'));
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,today,next',
                    center: 'title',
                    right: 'dayGridWeek,dayGridMonth'
                },  
                locale: 'frLocale',  
                events: training_days,
                buttonText: {
                    today:    'Aujourd\'hui',
                    month:    'Mois',
                    week:     'Semaine',
                    day:      'Jour',
                    list:     'Liste'
                },
                aspectRatio: 1,
                //height: 650
            });
            calendar.render();
        }
        else $(value).remove();
    });

    /* -------------------------------------------------------------------------- */
    /*                     Close all accordion except current                     */
    /* -------------------------------------------------------------------------- */
    let $prevWorkout;
    $('.accordion-button').on('click', e => {
    if ($prevWorkout !== e.currentTarget) {
        $prevWorkout = e.currentTarget;
        $('.accordion-collapse').removeClass("show");
        $('.accordion-button').attr('aria-expanded', false);
    }
    });

    /* -------------------------------------------------------------------------- */
    /*                              Download workout                              */
    /* -------------------------------------------------------------------------- */
    var doc = new jsPDF();
    var specialElementHandlers = {
        '#editor': function (element, renderer) {
            return true;
        }
    };

    $('#pdf-export').click(function (e) {
        console.log($(e.currentTarget).attr('data-export-workout'));

        doc.fromHTML($('#workout-container-'+$(e.currentTarget).attr('data-export-workout')).html(), 15, 15, {
            'width': 170,
                'elementHandlers': specialElementHandlers
        });
        doc.save('workout.pdf');
    });


});
