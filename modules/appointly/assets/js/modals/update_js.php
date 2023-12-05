<script>
     $(document).ready(function() {

          var div_name = $('#div_name');
          var div_email = $('#div_email');
          var div_phone = $('#div_phone');

          init_editor('textarea[name="notes"]', {
               menubar: false,
          });

          init_selectpicker();
          initAppointmentScheduledDates();

          $('#by_sms, #by_email').on('change', function() {
               var anyChecked = $('#by_sms').prop('checked') || $('#by_email').prop('checked');
               if (anyChecked) {
                    $('.appointment-reminder').removeClass('hide');
               } else {
                    $('.appointment-reminder').addClass('hide');
               }
          })

          $('.modal').on('hidden.bs.modal', function(e) {
               $('.xdsoft_datetimepicker').remove();
          });

          appValidateForm($("#appointment-form"), {
               subject: "required",
               description: "required",
               date: "required",
               name: "required",
               email: "required",
               'attendees[]': {
                    required: true,
                    minlength: 1
               }
          }, function(form) {
               $('button[type="submit"], button.close_btn').prop('disabled', true);
               $('button[type="submit"]').html('<i class="fa fa-refresh fa-spin fa-fw"></i>');
               form.submit();
          }, {
               'attendees[]': "Please select at least 1 staff member"
          });
     });

     function addEventToGoogleCalendar(button) {

          var form = $('#appointment-form').serialize();
          var url = "<?= admin_url('appointly/appointments/addEventToGoogleCalendar'); ?>";

          $.ajax({
               url: url,
               type: "POST",
               data: form,
               beforeSend: function() {
                    $(button).attr('disabled', true);
                    $('.modal .btn').attr('disabled', true);
                    $(button).html('' + appointly_please_wait + '<i class="fa fa-refresh fa-spin fa-fw"></i>');
               },
               success: function(r) {
                    if (r.result == 'success') {
                         alert_float('success', r.message);
                         $('.modal').modal('hide');
                         $('.table-appointments').DataTable().ajax.reload();
                    } else if (r.result == 'error') {
                         alert_float('danger', r.message);
                    }
               }
          });
     }
</script>