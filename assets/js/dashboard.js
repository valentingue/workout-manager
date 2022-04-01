/* -------------------------------------------------------------------------- */
/*                            Import jspdf from cdn                           */
/* -------------------------------------------------------------------------- */
function addScript(url) {
    const script = document.createElement('script');
    script.type = 'application/javascript';
    script.src = url;
    document.head.appendChild(script);
}
  addScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.min.js');

/* -------------------------------------------------------------------------- */

$(document).ready(function(){  

    if ($('.workouts-calendar').attr('data-training-days') !== ''){
        let training_days = [];
        let days = JSON.parse($('.workouts-calendar').attr('data-training-days'));

        days.forEach(function(currentValue, index, arr){
            training_days.push( { title: 'Entrainement', start: new Date(currentValue) } ) ;
        });

        var calendarEl = document.getElementById('calendar');
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
            height: 650
        });
        calendar.render();
    }

    let $prevWorkout;
    $('.accordion-button').on('click', e => {
    if ($prevWorkout !== e.currentTarget) {
        $prevWorkout = e.currentTarget;
        $('.accordion-collapse').removeClass("show");
        $('.accordion-button').attr('aria-expanded', false);
    }
    });

    var doc = new jsPDF();
    var specialElementHandlers = {
        '#editor': function (element, renderer) {
            return true;
        }
    };

    $('#pdf-export').click(function () {
        doc.fromHTML($('#current-workout').html(), 15, 15, {
            'width': 170,
                'elementHandlers': specialElementHandlers
        });
        doc.save('workout.pdf');
    });


});
