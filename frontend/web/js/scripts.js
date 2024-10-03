$(document).on('click', '#myTab', function() {
    var firstTabEl = document.querySelector('#myTab li:last-child a');

    if (firstTabEl) {
        var firstTab = new bootstrap.Tab(firstTabEl);
        firstTab.show();
    }
});

$(document).ready(function() {
    $('#form-signup').on('change', 'input[name="SignupForm[type]"]', function() {
        var type = $(this).val()
        if (type === 'staff') {
            $('#staff-field').show();
            $('#organization-field').hide();
            $('#organization-field-select').val(null).trigger('change');
        } else if (type === 'organization') {
            $('#staff-field').hide();
            $('#staff-field-select').val(null).trigger('change');
            $('#organization-field').show();
        }
    });

    $('#form-departament').on('change', 'input[name="Departament[role]"]', function() {
        var departRole = $(this).val();

        if (departRole === 'laboratory') {
            $('#short_name-field').show();
            $('#abbreviation-field').show();
            $('#period-field').show();
        } else {
            $('#short_name-field').hide();
            $('#abbreviation-field').hide();
            $('#period-field').hide();
        }
    });

    if ($("#role-field-select").length) {
        $("#role-field-select").select2({
            value: $("#hidden-role-input").val(),
        }).on("change", function() {
            $("#hidden-role-input").val($(this).val());
        });
    }
});

$(function() {
    $('[data-toggle="tooltip"]').tooltip();
});
$(document).on('ajaxComplete', function() {
    fakeTooltip = document.querySelector('.tooltip.bs-tooltip-auto');
    if (fakeTooltip) fakeTooltip.remove();
    $('[data-toggle="tooltip"]').tooltip();
});

$(document).on('mouseenter', '.select2-selection__rendered', function () {
    $(this).removeAttr('title');
});

$(document).ready(function() {
    if ($('.calendar-redact').length) {
        const year_id = $('.calendar-redact').data('id');
        const calendarDays = document.querySelectorAll('.calendar-day:not(.today)');
        
        calendarDays.forEach(day => {
            day.addEventListener('click', () => {
                date = day.dataset.value;

                if (day.classList.contains('event')) {
                    $.ajax({
                        url: '/calendar/unset-event?year_id=' + year_id + '&date=' + date,
                        type: 'POST',
                    });

                    day.classList.remove('event');
                } else {
                    $.ajax({
                        url: '/calendar/set-event?year_id=' + year_id + '&date=' + date,
                        type: 'POST',
                    });

                    day.classList.add('event');
                }
            });
        });
    }
});