<script>
     var appointly_please_wait = "<?= _l('appointment_please_wait'); ?>";
     var is_busy_times_enabled = "<?= get_option('appointly_busy_times_enabled'); ?>";

     $(function() {
          var apointmentsServerParams = {
               'custom_view': '[name="custom_view"]'
          }

          initDataTable('.table-appointments', '<?php echo admin_url('appointly/appointments/table'); ?>', [7], [7], apointmentsServerParams);
          $('body').on('click', '.approve_appointment', function() {
               $(this).attr('disabled', true);
               $(this).prev().next().addClass('approve_appointment_spacing');
               $(this).html('<i class="fa fa-refresh fa-spin fa-fw"></i>');
          });

          $('#createNewAppointment').click(function() {
               $("#modal_wrapper").load("<?php echo admin_url('appointly/appointments/modal'); ?>", {
                    slug: 'create'
               }, function() {
                    if ($('.modal-backdrop.fade').hasClass('in')) {
                         $('.modal-backdrop.fade').remove();
                    }
                    if ($('#newAppointmentModal').is(':hidden')) {
                         $('#newAppointmentModal').modal({
                              show: true
                         });
                    }
               });
          });
     });

     function appointmentUpdateModal(el) {
          var id = $(el).data('id');
          $("#modal_wrapper").load("<?php echo admin_url('appointly/appointments/modal'); ?>", {
               slug: 'update',
               appointment_id: id
          }, function() {
               if ($('.modal-backdrop.fade').hasClass('in')) {
                    $('.modal-backdrop.fade').remove();
               }
               if ($('#appointmentModal').is(':hidden')) {
                    $('#appointmentModal').modal({
                         show: true
                    });
               }
          });
     }

     $('.modal').on('hidden.bs.modal', function(e) {
          $(this).removeData();
     });

     var allowedHours = <?= json_encode(json_decode(get_option('appointly_available_hours'))); ?>;
     var appMinTime = <?= get_option('appointments_show_past_times'); ?>;
     var appWeekends = <?= (get_option('appointments_disable_weekends')) ? "[0, 6]" : "[]"; ?>;

     var todaysDate = new Date();

     var currentDate = todaysDate.getFullYear() + "-" + (((todaysDate.getMonth() + 1) < 10) ? "0" : "") + (todaysDate.getMonth() + 1 + "-" + ((todaysDate.getDate() < 10) ? "0" : "") + todaysDate.getDate());

     function initAppointmentScheduledDates() {
          $.post('appointly/appointments_public/busyDates').done(function(r) {
               r = JSON.parse(r);
               var dateFormat = app.options.date_format;
               var appointmentDatePickerOptions = {
                    dayOfWeekStart: app.options.calendar_first_day,
                    minDate: 0,
                    format: dateFormat,
                    defaultTime: "09:00",
                    allowTimes: allowedHours,
                    closeOnDateSelect: 0,
                    closeOnTimeSelect: 1,
                    validateOnBlur: false,
                    minTime: appMinTime,
                    disabledWeekDays: appWeekends,
                    onGenerate: function(ct) {

                         if (is_busy_times_enabled == 1) {
                              var selectedGeneratedDate = ct.getFullYear() + "-" + (((ct.getMonth() + 1) < 10) ? "0" : "") + (ct.getMonth() + 1 + "-" + ((ct.getDate() < 10) ? "0" : "") + ct.getDate());

                              $(r).each(function(i, el) {

                                   if (el.date == selectedGeneratedDate) {
                                        var currentTime = $('body')
                                             .find('.xdsoft_time:contains("' + el.start_hour + '")');
                                        if (el.source == undefined) {
                                             currentTime.addClass('busy_google_time');
                                        } else {
                                             currentTime.addClass('busy_time');
                                        }
                                   }
                              });
                         }
                    },
                    onSelectDate: function(ct) {

                         var selectedDate = ct.getFullYear() + "-" + (((ct.getMonth() + 1) < 10) ? "0" : "") + (ct.getMonth() + 1 + "-" + ((ct.getDate() < 10) ? "0" : "") + ct.getDate());

                         setTimeout(function() {
                              $('body').find('.xdsoft_time').removeClass('xdsoft_current xdsoft_today');

                              if (currentDate !== selectedDate) {
                                   $('body').find('.xdsoft_time.xdsoft_disabled').removeClass('xdsoft_disabled');
                              }
                         }, 200);
                    },
                    onChangeDateTime: function() {
                         var currentTime = $('body').find('.xdsoft_time');
                         currentTime.removeClass('busy_time');
                    }
               };

               if (app.options.time_format == 24) {
                    dateFormat = dateFormat + ' H:i';
               } else {
                    dateFormat = dateFormat + ' g:i A';
                    appointmentDatePickerOptions.formatTime = 'g:i A';
               }

               appointmentDatePickerOptions.format = dateFormat;

               $('.appointment-date').datetimepicker(appointmentDatePickerOptions);
          });

          $('#appointment_select_type').on('change', function(e) {
               var selectedColorType = $(this).children("option:selected").data('color');
               $('#appointment_color_type').attr('style', 'background-color:' + selectedColorType)
          });
     }

     // Create new task directly from relation, related options selected after modal is shown
     function new_task_from_relation_appointment(appointment) {
          var contact_name = $(appointment).data('name');
          var contact_id = $(appointment).data('contact-id');
          var rel_id = $(appointment).data('customer-id');
          var rel_type = $(appointment).data('source');

          if (rel_id !== 0 && rel_type === 'internal') {
               rel_type = 'customer';
               rel_id = rel_id;
          } else {
               rel_type = 'lead';
               rel_id = rel_id;
          }

          var url = admin_url + 'tasks/task?rel_id=' + rel_id + '&rel_type=' + rel_type;
          new_task(url);

          $('#_task').on('show.bs.modal', function(e) {
               $('body').find('#_task #name').val("<?= _l('appointments_contact_name_task'); ?> " + '[ ' + contact_name + ' ]');
          });
     }

     // Init lead convert to lead for appointment
     function init_appointment_lead(appointment) {
          var contact_name = $(appointment).data('name');
          var contact_email = $(appointment).data('email');
          var contact_phonenumber = $(appointment).data('phone');
          // In case header error
          if (init_lead_modal_data(undefined, undefined, false)) {
               $('#lead-modal').modal('show');
          }
          $('#lead-modal').on('show.bs.modal', function(e) {
               $('body').find('#lead-modal .modal-title').text("<?= _l('appointments_convert_to_lead'); ?>");
               $('body').find('#lead-modal #name').val(contact_name);
               $('body').find('#lead-modal #email').val(contact_email);
               $('body').find('#lead-modal #phonenumber').val(contact_phonenumber);
          });
     }

     // Request appointment feedback
     function request_appointment_feedback(appointment_id) {
          $("body").append('<div class="dt-loader"></div>');

          var url = admin_url + 'appointly/appointments/requestAppointmentFeedback/' + appointment_id;
          $.post(url).done(function(response) {
               if (response.success == true) {
                    alert_float('info', "<?= _l('appointment_feedback_reuested_alert'); ?>");
                    $("body").find('.dt-loader').remove();
               }
          }).fail(function(err) {
               $("body").find('.dt-loader').remove();
               console.log('An unknown error has been thrown' + err);
          });
     }
</script>