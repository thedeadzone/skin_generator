$(document).ready(function () {
    $("#submit-createmodpack").on('click', function () {
        $('.loading-modpack').removeClass('hidden');
    });

    $('#createmodpack-form').submit(function (e) {
        e.preventDefault();
        $('.loading-modpack').removeClass('hidden');
        $('.download-modpack').addClass('hidden');
        $('.stats-modpack').addClass('hidden');
        $('.progress-modpack').addClass('hidden');
        $('#submit-createmodpack').addClass('disabled');

        var username = $('#createmodpack-username').val();
        var server = $('#createmodpack-server').val();

        if ($('#createmodpack-hd option:selected').text() == 'Yes') {
            var hd = 'true';
        } else {
            var hd = 'false'
        }

        //Makes sure user does not leave page as quick before modpack is finished
        $(window).bind('beforeunload', function () {
            return 'Are you sure you want to leave before your modpack is created?';
        });

        $.ajax({
            url: '../createModpack/getUser.php',
            data: {
                username: username,
                hd: hd,
                server: server
            },
            dataType: 'json',
            success: function (data) {
                var response = data;

                for (i = 0; i < response[1].length; i++) {
                    var current = response[1][i];

                    var wn8 = Math.round(current[1]);
                    wn8 = parseInt(wn8);
                    var color = '';

                    // Color the Wn8 Column in the table
                    if (wn8 <= 599) {
                        color = 'red';
                    } else if ( wn8 <= 899) {
                        color = 'orange';
                    } else if ( wn8 <= 1199) {
                        color = 'yellow';
                    } else if ( wn8 <= 1799) {
                        color = 'green';
                    } else if ( wn8 <= 2299) {
                        color = 'blue';
                    } else if ( wn8 >= 2300) {
                        color = 'purple';
                    }

                    var winrate = current[0]['all']['wins'] / current[0]['all']['battles'] * 100;
                    var damage = current[0]['all']['damage_dealt'] / current[0]['all']['battles'];
                    var xp = current[0]['all']['xp'] / current[0]['all']['battles'];

                    // Add a tank table row for every tank to the table
                    $('.stats-modpack table tbody').append('<tr>' +
                        '<td>' +
                        '<img class="img-responsive" src="'+ current[2]["small_icon"]+'" alt="tank">'+
                        '</td>' +
                        '<td>'+ current[2]["tier"]+'</td>' +
                        '<td>'+ current[2]["short_name"]+'</td>' +
                        '<td>'+ current[0]['all']['battles'] +'</td>' +
                        '<td>'+ Math.round(winrate * 100) / 100 +'%</td>' +
                        '<td>'+ Math.round(damage) +'</td>' +
                        '<td>'+ Math.round(xp)+'</td>' +
                        '<td class="'+ color +'-wn8">'+ wn8+'</td>' +
                        '</tr>');
                }

                $('.loading-modpack').addClass('hidden');
                $('.download-modpack-href').attr("href", response[0]);
                $('.download-modpack').removeClass('hidden');
                $('.stats-modpack').removeClass('hidden');

                $('.progress-modpack .progress .progress-bar').css('width', '100%');
                $('.progress-modpack .progress .progress-bar').html('100%');
                $('.progress-modpack').addClass('hidden');
                $("#myTable").tablesorter( {sortList: [[3,1]]} );

            },
            complete: function () {
                $(window).unbind();
                clearInterval(timeout)
            },
            error: function (data) {
                $('.loading-modpack').addClass('hidden');
                $('.error-modpack').removeClass('hidden');
                $('#submit-createmodpack').removeClass('disabled');
            },
            type: 'GET'
        });

        timeout = setInterval(
            function(){
                $.get("../createModpack/getProgress.php", function( data ) {
                    var p = data.progress;
                    $('.progress-modpack').removeClass('hidden');
                    var width = p + '%';

                    //Settings text under the progressbar to be interesting
                    if (width == '10%') {
                        $('#modpack-progress-text').text('Looking up the right garage');
                    } else if (width == '20%') {
                        $('#modpack-progress-text').text('Handpicking the tanks');
                    } else if (width == '30%') {
                        $('#modpack-progress-text').text('Driving the tanks out of the garage');
                    } else if (width == '40%') {
                        $('#modpack-progress-text').text('Mixing the paint');
                    } else if (width == '50%') {
                        $('#modpack-progress-text').text('Painting tanks pink');
                    } else if (width == '60%') {
                        $('#modpack-progress-text').text('Painting tanks in the right color');
                    } else if (width == '70%') {
                        $('#modpack-progress-text').text('Blowing the paint dry with a hair blower');
                    } else if (width == '80%') {
                        $('#modpack-progress-text').text('Rolling tanks carefully back inside the garage');
                    } else if (width == '90%') {
                        $('#modpack-progress-text').text('Wrapping tanks in gift paper');
                    } else if (width == '100%') {
                        $('.progress-modpack').addClass('hidden');
                    }

                    //Setting width of progress bar + text as width + %
                    $('.progress-modpack .progress .progress-bar').css('width', width);
                    $('.progress-modpack .progress .progress-bar').html(width);
                    return p;
                }, "json");
        }, 2000);
    });

    //Submitting feedback form data to database
    $('#feedback-form').submit(function (e) {
        e.preventDefault();
        $('#submit-feedback').addClass('disabled');

        var text = $('#feedback-text').val();
        var type = $('#feedback-type').val();

        $.ajax({
            url: '../createModpack/saveFeedback.php',
            data: {
                type: type,
                text: text
            },
            dataType: 'json',
            success: function (data) {
                $('#feedback-submitted').removeClass('hidden');
            },
            error: function (data) {
                $('#feedback-submitted-error').removeClass('hidden');
            },
            type: 'GET'
        });
    });
});
