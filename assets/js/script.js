$(document).ready(function () {
    $("#submit-createmodpack").on('click', function () {
        $('.loading-modpack').removeClass('hidden');
    });

    $('#createmodpack-form').submit(function (e) {
        e.preventDefault();
        $('.loading-modpack').removeClass('hidden');
        $('.download-modpack').addClass('hidden');
        $('.stats-modpack').addClass('hidden');
        $('#submit-createmodpack').addClass('disabled');

        var username = $('#createmodpack-username').val();
        var server = $('#createmodpack-server').val();

        if ($('#createmodpack-hd option:selected').text() == 'Yes') {
            var hd = 'true';
        } else {
            var hd = 'false'
        }

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

                    $('.stats-modpack table tbody').append('<tr>' +
                        '<td>' +
                        '<img class="img-responsive" src="'+ current[2]["small_icon"]+'" alt="tank">'+
                        '</td>' +
                        '<td>'+ current[2]["tier"]+'</td>' +
                        '<td>'+ current[2]["short_name"]+'</td>' +
                        '<td>'+ current[0]['all']['battles'] +'</td>' +
                        '<td>'+ Math.round(winrate * 100) / 100 +'</td>' +
                        '<td>'+ Math.round(damage) +'%</td>' +
                        '<td>'+ Math.round(xp)+'</td>' +
                        '<td class="'+ color +'-wn8">'+ wn8+'</td>' +
                        '</tr>');
                }

                $('.loading-modpack').addClass('hidden');
                $('.download-modpack-href').attr("href", response[0]);
                $('.download-modpack').removeClass('hidden');
                $('.stats-modpack').removeClass('hidden');
            },
            complete: function () {
                $(window).unbind();
            },
            error: function (data) {
                $('.loading-modpack').addClass('hidden');
                $('.error-modpack').removeClass('hidden');
                $('#submit-createmodpack').removeClass('disabled');
            },
            type: 'GET'
        });
    });

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
