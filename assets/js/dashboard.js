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
            events: training_days,
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

});
