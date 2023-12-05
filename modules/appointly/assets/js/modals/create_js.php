<script>
     $(function() {

          var div_name = $('#div_name');
          var div_email = $('#div_email');
          var div_phone = $('#div_phone');

          init_editor('textarea[name="notes"]', {
               menubar: false,
          });

          initAppointmentScheduledDates();
          $('.modal').on('hidden.bs.modal', function(e) {
               $('.xdsoft_datetimepicker').remove();
               $(this).removeData();
          });

          $('#by_sms, #by_email').on('change', function() {
               var anyChecked = $('#by_sms').prop('checked') || $('#by_email').prop('checked');
               if (anyChecked) {
                    $('.appointment-reminder').removeClass('hide');
               } else {
                    $('.appointment-reminder').addClass('hide');
               }
          });

          appValidateForm($("#appointment-form"), {
               subject: "required",
               description: "required",
               date: "required",
               rel_type: "required",
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

          $("body").on('change', '#rel_type', function() {
               var optionSelected = $("option:selected", this).attr('id');
               var contact_id = $("#contact_id");
               var select_contacts = $('#select_contacts');
               var div_nep = $('#div_name, #div_email, #div_phone');
               var dninput = $('#div_name input, #div_email input, #div_phone input');
               var rel_id_wrapper = $('#rel_id_wrapper');
               var lead_select = $('#rel_id');

               if (optionSelected == 'external') {
                    dninput.val('').attr('disabled', false).attr('required', true);
                    $('#div_phone input').attr('required', false);
                    div_nep.removeClass('hidden');
                    select_contacts.addClass('hidden');
                    contact_id.selectpicker("refresh").attr('required', false);;
                    rel_id_wrapper.addClass('hide');
                    lead_select.attr('required', false).val('default').selectpicker("refresh");

               } else if (optionSelected == 'internal') {
                    contact_id.val('default').selectpicker("refresh").attr('required', true);
                    div_nep.addClass('hidden').attr('required', false);
                    select_contacts.removeClass('hidden');
                    rel_id_wrapper.addClass('hide');
                    lead_select.attr('required', false).val('default').selectpicker("refresh");
               } else {
                    dninput.attr('required', false);
                    contact_id.val('default').selectpicker("refresh").attr('required', false);
                    div_nep.addClass('hidden').attr('required', false);
                    select_contacts.addClass('hidden');
                    rel_id_wrapper.removeClass('hide');
                    lead_select.attr('required', true);
               }
          });

          $('body').on('change', '#contact_id, #rel_id', function() {

               var contact_id = $("option:selected", this).val();

               if (contact_id == "" && div_name.children('input').is(":visible")) {
                    div_name.children('input').val('');
                    div_email.children('input').val('');
                    div_phone.children('input').val('');
               }

               var url = "<?= admin_url('appointly/appointments/fetch_contact_data'); ?>";

               $.post(url, {
                    contact_id: contact_id,
                    lead: ($(this).attr('id') == 'rel_id') ? true : false
               }).done(function(response) {
                    if (response !== null) {

                         $('#div_name, #div_email, #div_phone').removeClass('hidden');

                         var full_name = (typeof(response.firstname) != 'undefined') ? response.firstname + ' ' + response.lastname : response.name;
                         var email = response.email;
                         var phone = response.phonenumber;

                         div_name.children('input').val(full_name).attr('disabled', true);

                         div_email.children('input').val(email).attr('disabled', (response.email == '') ? false : true);
                         div_email.children('input[name="email"]').val(email).attr('required', (response.email == '') ? true : false);
                         div_phone.children('input').val(phone).attr('disabled', true);
                    }
               });
          });

          init_selectpicker();

          // Leads functionality
          var _rel_id = $('#rel_id'),
               _rel_type = $('#rel_lead_type');

          // Items ajax search for leads
          var serverData = {};

          init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'items/search');
          serverData.rel_id = _rel_id.val();
          init_ajax_search(_rel_type.val(), _rel_id, serverData);
     });
</script>