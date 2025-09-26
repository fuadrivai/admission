let observationDateId;
let levels=null;
$(document).ready(function(){
    $('#date').on('changeDate',function(){
        $('#list-time').empty()
        let date = moment($(this).val(),"DD MMMM YYYY").format("YYYY-MM-DD")
        let divisionId= $('#level option:selected').attr('data-id')
        getObservationDate(date,divisionId);
    })

    $('#list-time').on('click','.time-badge',function(){
        $('.time-badge').removeClass('selected');
        $(this).addClass('selected');
        let time = $(this).attr("data-time");
        observationDateId = $(this).attr("data-id");
        $('#selectedTime').val(time)
        $("#selectedTime").removeClass("is-invalid").addClass("is-valid");
    })

    $('#level').on('change',function(){
        $('#grade').attr('disabled',false)
        $('#date').attr('disabled',false)
        $('#date').val("");
        $('#selectedTime').val("")
        $('#list-time').empty()

        let levelId = $(this).val();
        const level =  levels.find(l => l.id == levelId);
        $('#grade').empty()
        $('#grade').append(`
                <option value="" selected disabled>Pilih Grade</option>  
            `)
        level.grades.forEach(val=>{
            $('#grade').append(`
                <option value="${val.id}">${val.name}</option>    
            `)
        })
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
                $('#btn-submit').attr('disabled',false)
                return false;
            }

            let data = $(form).serializeArray();
            let dataJSON = {};
            $.each(data, function() {
                dataJSON[this.name] = this.value;
            });
            dataJSON.observation_time_id = observationDateId;
            dataJSON.level = $('#level option:selected').text();
            dataJSON.grade = $('#grade option:selected').text();
            dataJSON.date = moment(dataJSON.date,"DD MMMM YYYY").format("YYYY-MM-DD")
            blockUI();
            postObservation(dataJSON)
        } else {
            unBlockUI();
            $(form).addClass("was-validated");
            form.reportValidity();
            $('#btn-submit').attr('disabled',false)
        }
    })

    getLevel();
})

function getObservationDate(date,divisionId) {
    ajax(null, `/observation/get/date/division/${date}/${divisionId}`, 'GET', function(json) {
        $('#list-time').empty()

        if ((json?.times ?? []).length < 1) {
            $('#list-time').append(`
                <span class="time-badge disabled">No Time Available</span>
            `)
            return false
        }
        
        if (json.is_active!=1) {
            $('#list-time').append(`
                <span class="time-badge disabled">No Time Available</span>
            `)
            return false
        }

        let today = moment().format('YYYY-MM-DD');
        let now = moment();
        let selectedDate = moment(date, 'YYYY-MM-DD');

        json.times.forEach(e => {
            let time = moment(e.time, "HH:mm:ss");
            let formattedTime = time.format('HH:mm');
            let rest = parseInt(e.rest);

            let isDisabled = false;

            // 1. Jika tanggal yang dipilih sudah lewat
            if (selectedDate.isBefore(today)) {
                isDisabled = true;
            }
            // 2. Jika tanggal yang dipilih adalah hari ini dan jam sudah lewat
            else if (date === today && time.isBefore(now)) {
                isDisabled = true;
            }
            // 3. Jika quota habis
            else if (rest < 1) {
                isDisabled = true;
            }

            $('#list-time').append(`
                <span class="time-badge ${isDisabled ? 'disabled' : ''}" 
                      data-id="${e.id}" 
                      data-time="${formattedTime}">
                      ${formattedTime}
                </span>
            `)
        });

        $('#selectedTime').val("")
    }, function(err) {
        toastify("Error", err?.responseJSON?.message ?? "Please try again later", "error");
    });
}

function getLevel(){
    ajax(null, `/level/get`, 'GET', function(json) {
        levels = json
        levels.forEach(val=>{
            $('#level').append(`
                <option data-id="${val.division.id}" value="${val.id}">${val.name}</option>
            `)
        })
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