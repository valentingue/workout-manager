$(document).ready(function(){  
    let training_days = [];
    let days = JSON.parse($('.workouts-calendar').attr('data-training-days'));

    days.forEach(function(currentValue, index, arr){
        let start_date = $('.workouts-calendar').attr('data-start-date');
        training_days.push( { title: 'Training', start: getMonday(start_date, currentValue) } ) ;
    });

    console.log(training_days);

    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: training_days
    });
    calendar.render();

    console.log(); // Mon Nov 08 2010

});

function getMonday(d, training_day) {
    d = new Date(d);
    //d = new Date();
    var day = d.getDay();
    var diff = d.getDate() - day + (day == 0 ? -6:1) + parseInt(training_day) ; // adjust when day is sunday
    return new Date(d.setDate(diff)); 
}
