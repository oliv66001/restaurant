$('#accept-cookies').on('click', function() {
    $.post('/accept-cookies', function(data) {
        if (data.status === 'success') {
            $('#cookie-banner').hide();
        }
    });
});

$('#refuse-cookies').on('click', function() {
    $.post('/refuse-cookies', function(data) {
        if (data.status === 'success') {
            $('#cookie-banner').hide();
        }
    });
});

