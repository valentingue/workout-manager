$(document).ready(function(){  
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

});
