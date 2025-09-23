let observationDateId;
$(document).ready(function(){
    $('#date').on('changeDate',function(){
        $('#list-time').empty()
        let date = moment($(this).val(),"DD MMMM YYYY").format("YYYY-MM-DD")
        getObservationDate(date);
    })

    $('#list-time').on('click','.time-badge',function(){
        $('.time-badge').removeClass('selected');
        $(this).addClass('selected');
        let time = $(this).attr("data-time");
        observationDateId = $(this).attr("data-id");
        $('#selectedTime').val(time)
        $("#selectedTime").removeClass("is-invalid").addClass("is-valid");
    })

    $('#observationForm').on('submit',function(e){
        e.preventDefault();
        const form = this;
        
        if (form.checkValidity()) {
            $('#btn-submit').attr('disabled',true)
            $(form).addClass("was-validated");

            if ($("#selectedTime").val() == "" || $("#selectedTime").val() == null) {
                $("#selectedTime").addClass("is-invalid");
                form.reportValidity();
                return false;
            }

            let data = $(form).serializeArray();
            let dataJSON = {};
            $.each(data, function() {
                dataJSON[this.name] = this.value;
            });
            dataJSON.observation_time_id = observationDateId;
            dataJSON.date = moment(dataJSON.date,"DD MMMM YYYY").format("YYYY-MM-DD")
            postObservation(dataJSON)
        } else {
            $(form).addClass("was-validated");
            form.reportValidity();
            $('#btn-submit').attr('disabled',false)
        }
    })
})

function getObservationDate(date){
    ajax(null, `/observation/get/date/${date}`, 'GET', function(json) {
        $('#list-time').empty()
        if ((json?.times??[]).length <1) {
            $('#list-time').append(`
                <span class="time-badge disabled">No Time Available</span>
            `)
            return false
        }
        json.times.forEach(e => {
            let time = moment(e.time,"HH:mm:ss").format('HH:mm')
            let rest = parseInt(e.rest);
            $('#list-time').append(`
                <span class="time-badge ${(rest <1)?'disabled':''}" data-id="${e.id}" data-time="${time}">${time}</span>
            `)
        });
        $('#selectedTime').val("")
    }, function(err) {
        toastify("Error", err?.responseJSON?.message ?? "Please try again later", "error");
    });
}

function postObservation(data) {
    ajax(data, `/observation`, 'POST', function(json) {
        $('#btn-submit').attr('disabled',false)
        toastify("success","Observasi Berhasil");
         setTimeout(() => {
                    window.location.href = "/observation-success";
        }, 1500);
    }, function(err) {
        $('#btn-submit').attr('disabled',false)
        toastify("Error", err?.responseJSON?.message ?? "Please try again later", "error");
    });
}