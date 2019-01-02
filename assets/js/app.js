require('../css/app.scss');

const $ = require('jquery');
const Chart = require('chart.js');

$('.chart').each(function () {
    const $self = $(this);

    $.get({
        'url': $self.data('url'),
        'dataType': 'json'
    }).then(function (data) {
        new Chart($self, data);
    }, function (jqXHR, textStatus, errorThrown) {
        console.log(textStatus);
        console.log(errorThrown);
    });
});