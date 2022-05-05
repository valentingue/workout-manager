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

    $('#pdf-export').click(function () {
        //var doc = new jsPDF();
        var doc = new jsPDF('p','px','a4');

        var margins = {
            top:10,
            bottom: 10,
            left: 10,
            width: 1000
        };
        
        var specialElementHandlers = {
            '.no-export': function (element, renderer) {
                return true;
            }
        };

        doc.fromHTML(
            $('#workout-container-'+$(this).attr('data-export-workout')).html(), 
            margins.left, margins.top, 
            {
                'width': margins.width,
                'elementHandlers': specialElementHandlers,
            },
            function(dispose){
                doc.save('workout.pdf');
            },margins);
    });

    /* $('#pdf-export').click(function () {
        const doc = new jsPDF('p', 'cm', 'a4');

        doc.autoTable({html: '#table-test'});
    
        doc.save('workout.pdf');
    }); */


    /* $('#pdf-export').click(function () {
        var doc = new jsPDF();

        doc.autoTable({
            styles: { fillColor: [255, 0, 0] },
            columnStyles: { 0: { halign: 'center', fillColor: [0, 255, 0] } }, // Cells in first column centered and green
            margin: { top: 10 },
            html: '#table-'+$(this).attr('data-export-workout') 
        });

        doc.save('workout.pdf');
    }); */

});
