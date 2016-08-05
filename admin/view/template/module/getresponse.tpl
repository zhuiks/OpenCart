<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <a onclick="$('#form').submit();" class="btn btn-primary"><?php echo $button_save; ?></a>
        <a onclick="location = '<?php echo $cancel; ?>';"
           class="btn btn-default"><?php echo $button_cancel; ?></a>
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li <?php echo ("GetResponse" == $breadcrumb['text'])?'id="GrBreadcrumb"':'Gr';?> ><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
      <div class="gr-info"></div>
    <?php if (isset($error_warning) && !empty($error_warning)) { ?>
    <div class="alert alert-danger" role="alert"><?php echo $error_warning; ?></div>
    <?php } ?>

    <?php if (isset($save_success) && !empty($save_success)) { ?>
    <div class="alert alert-success" role="alert"><?php echo $save_success; ?></div>
    <?php } ?>

    <div class="panel panel-default">
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form"
              class="form-horizontal">
          <input type="hidden" value="1" name="getresponse_status">
          <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="<?php if ($active_tab == 'home') {echo "active";} ?>"><a href="#settings" aria-controls="home" role="tab" data-toggle="tab"><?php echo $apikey_title; ?></a></li>
            <?php if (!empty($getresponse_apikey)) { ?>
            <li role="presentation" class="<?php if ($active_tab == 'export') {echo "active";} ?>"><a href="#export" aria-controls="export" role="tab" data-toggle="tab"><?php echo $export_title; ?></a></li>
            <li role="presentation" class="<?php if ($active_tab == 'registration') {echo "active";} ?>"><a href="#registration" aria-controls="registration" role="tab" data-toggle="tab"><?php echo $register_title; ?></a></li>
            <li role="presentation" class="<?php if ($active_tab == 'webform') {echo "active";} ?>"><a href="#webform" aria-controls="webform" role="tab" data-toggle="tab"><?php echo $webform_title; ?></a></li>
            <?php } ?>
          </ul>
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane <?php if ($active_tab == 'home') {echo "active";} ?>" id="settings">
                <div>
                    <?php
                    if (isset($getresponse_accounts['name'])) {
                        echo $getresponse_accounts['name'] . ", " . $getresponse_accounts['email'] . "<br>";
                        echo $getresponse_accounts['street'] . ", " . $getresponse_accounts['zipCode'] . " " . $getresponse_accounts['city'] . "<br>";
                        echo $getresponse_accounts['state'] . ", " . $getresponse_accounts['countryCode'] . "<br>";
                    } else {
                        echo "<p>" . $apikey_info . "</p>";
                    }
                    ?>
                </div>
              <div class="form-group required">
                <label for="getresponse-apikey"
                       class="col-sm-2 control-label"><?php echo $entry_apikey; ?></label>
                <div class="col-sm-10">

                  <div class="form-inline">
                    <input id="getresponse-apikey" class="form-control" type="text" name="getresponse_apikey"
                         value="<?php echo $getresponse_apikey; ?>" size="50" <?=(empty($getresponse_apikey)?'':'readonly') ?> />
                      <?php if (empty($getresponse_apikey)) { ?>
                          <button class="btn btn-primary" type="submit" id="button_connect"><?php echo $button_connect; ?></button>
                      <?php }else{ ?>
                          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#getresponseModal">
                              <?php echo $button_disconnect; ?>
                          </button>
                      <?php }?>
                      <input type="hidden" value="0" name="getresponse_disconnect" id="getresponse_disconnect">
                  </div>
                  <span id="gr-apikey-help" class="help-block"><?php echo $entry_apikey_hint; ?></span>
                </div>
              </div>
            </div>
            <?php if (!empty($getresponse_apikey)) { ?>
            <div role="tabpanel" class="tab-pane <?php if ($active_tab == 'export') {echo "active";} ?>" id="export">
                <div class="form-group">
                <label for="getresponse-campaign"
                       class="col-sm-2 control-label"><?php echo $label_campaign; ?></label>
                <div class="col-sm-4">
                  <select id="getresponse-campaign" class="form-control" name="getresponse_campaign">
                    <?php foreach ($campaigns as $campaign) { ?>
                    <option value="<?php echo $campaign->campaignId; ?>"<?php if (isset($getresponse_campaign) && $getresponse_campaign == $campaign->campaignId) { ?> selected<?php } ?>><?php echo $campaign->name; ?></option>
                    <?php } ?>
                  </select>
                  <span id="config-campaign-help" class="help-block"><?php echo $entry_campaign_hint; ?></span>
                </div>
              </div>
                <div class="form-group sequence-ex-day-label" style="border-top:none">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-10 checkbox">
                        <label>
                            <input type="checkbox"
                                   value="1"<?php if (isset($getresponse_ex['sequence_active']) && $getresponse_ex['sequence_active'] == 1) { ?> checked<?php } ?>
                                   name="getresponse_ex[sequence_active]" id="getresponse_ex_sequence_active">
                        </label> <?php echo $label_auto_queue; ?>
                    </div>
                </div>
                <div class="form-group sequence-ex-day-label" style="border-top:none">
                    <label for="getresponse-ex-day"
                           class="col-sm-2 control-label"><?php echo $label_day_of_cycle; ?></label>
                    <div class="col-sm-4">
                        <select name="getresponse_ex[day]" id="getresponse-ex-day" class="form-control"></select>
                        <span class="help-block"><?php echo $entry_campaign_description; ?></span>
                    </div>
                </div>
              <div class="col-sm-offset-2 col-sm-3">
                <a id="gr-export" class="button btn btn-primary"><?php echo $button_export; ?></a>
              </div>
              <div class="form-group" id="edit-export-hint">
                <div class="col-sm-10">
                  <a class="gr-hint" href="#"><?php echo $label_export_info; ?></a>
                  <div id="gr-hint-ex" style="display: none;">
                    <?php echo $export_info; ?>
                  </div>
                </div>
              </div>
            </div>
            <div role="tabpanel" class="tab-pane <?php if ($active_tab == 'registration') {echo "active";} ?>" id="registration">
                <div class="form-group" style="border-top:none">
                    <label for="getresponse-reg-active"
                           class="col-sm-2 control-label"><?php echo $label_active_register; ?></label>
                    <div class="col-sm-10">
                        <label class="checkbox-inline">
                            <input type="checkbox"
                                   value="1" <?php if (isset($getresponse_reg['active']) && $getresponse_reg['active'] == 1) {
                                echo 'checked';
                            } ?> name="getresponse_reg[active]">
                        </label>
                    </div>
                </div>
              <div class="form-group" style="border-top:none">
                <label for="getresponse-reg-campaign" class="col-sm-2 control-label"><?php echo $label_campaign; ?></label>
                <div class="col-sm-4">
                  <select name="getresponse_reg[campaign]" id="getresponse-reg-campaign" class="form-control">
                    <?php foreach ($campaigns as $campaign) { ?>
                    <option value="<?php echo $campaign->campaignId; ?>"<?php if ($getresponse_reg['campaign'] == $campaign->campaignId) { ?> selected<?php } ?>><?php echo $campaign->name; ?></option>
                    <?php } ?>
                  </select>
                  <span class="help-block"><?php echo $entry_campaign_description; ?></span>
                </div>
              </div>
              <div class="form-group sequence-day-label" style="border-top:none">
                <div class="col-sm-2"></div>
                <div class="col-sm-10 checkbox">
                  <label>
                    <input type="checkbox" value="1"<?php if (isset($getresponse_reg['sequence_active']) && $getresponse_reg['sequence_active'] == 1) { ?> checked<?php } ?> name="getresponse_reg[sequence_active]">
                  </label> <?php echo $label_auto_queue; ?>
                </div>
              </div>
              <div class="form-group sequence-day-label" style="border-top:none">
                <label for="getresponse-reg-day" class="col-sm-2 control-label"><?php echo $label_day_of_cycle; ?></label>
                <div class="col-sm-4">
                  <select name="getresponse_reg[day]" id="getresponse-reg-day" class="form-control"></select>
                  <span class="help-block"><?php echo $entry_campaign_description; ?></span>
                </div>
              </div>
              <div id="edit-register-hint">
                <div>
                  <a class="gr-hint" href="#"><?php echo $label_register_info; ?></a>
                  <div id="gr-hint-reg" style="display: none;">
                    <?php echo $register_info; ?>
                  </div>
                </div>
              </div>
            </div>
                <div role="tabpanel" class="tab-pane <?php if ($active_tab == 'webform') {echo "active";} ?>" id="webform">
              <div class="form-group" style="border-top:none">
                <label for="getresponse-form-active" class="col-sm-2 control-label"><?php echo $label_active_forms; ?></label>
                <div class="col-sm-10">
                  <label class="checkbox-inline">
                    <input type="checkbox" value="1" <?php if ($getresponse_form['active'] == 1) { ?> checked<?php } ?> name="getresponse_form[active]">
                  </label>
                </div>
              </div>
              <div class="form-group" style="border-top:none">
                <label for="getresponse-reg-active" class="col-sm-2 control-label"><?php echo $label_form; ?></label>
                <div class="col-sm-4">
                  <select name="getresponse_form[id]" id="getresponse-form-id" class="form-control">
                    <?php
                    if (isset($new_forms) && !empty($new_forms)) {
                      echo '<optgroup label="' . $label_new_forms . '">';
                      foreach ($new_forms as $form) {
                        $selectedOption = ($getresponse_form['id'] == $form['id']) ? 'selected' : '';
                        echo '<option value="' . $form['id'] . '" ' . $selectedOption . ' data-url="' . $form['url'] . '">'.$form['name'].'</option>';
                      }
                      echo '</optgroup>';
                    }
                    if (isset($old_forms) && !empty($old_forms)) {
                      echo '<optgroup label="' . $label_old_forms . '">';
                      foreach ($old_forms as $form) {
                        $selectedOption = ($getresponse_form['id'] == $form['id']) ? 'selected' : '';
                        echo '<option value="' . $form['id'] . '" ' . $selectedOption . '  data-url="' . $form['url'] . '">' . $form['name'] . '</option>';
                      }
                      echo '</optgroup>';
                    }
                    ?>
                  </select>
                  <span id="config-campaign-help" class="help-block"><?php echo $entry_campaign_hint; ?></span>
                  <input type="hidden" name="getresponse_form[url]" id="getresponse-form-url" value="<?php echo $getresponse_form['url']; ?>">
                </div>
              </div>
              <div id="edit-webform-hint">
                <div>
                  <a class="gr-hint" href="#"><?php echo $label_webform_info; ?></a>
                  <div id="gr-hint-wf" style="display: none;">
                    <?php echo $webform_info; ?>
                  </div>
                </div>
              </div>
            </div>
            <?php } ?>
          </div>
          <input type="hidden" name="getresponse_form[current_tab]" id="getresponse-current-tab" value="">
        </form>
      </div>
    </div>
  </div>
</div>
    <!-- Modal -->
    <div class="modal fade" id="getresponseModal" tabindex="-1" role="dialog" aria-labelledby="getresponseModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="getresponseModalLabel"><?php echo $disconnect_title; ?></h4>
                </div>
                <div class="modal-body">
                    <?php echo $disconnect_info; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $button_stay; ?></button>
                    <button class="btn btn-primary" type="submit" id="button_disconnect"><?php echo $label_yes.", ".strtolower($button_disconnect); ?></button>

                </div>
            </div>
        </div>
    </div>

<script type="text/javascript">
  <!--
  $(function () {
      var api_key_element = $('#getresponse-apikey');
      var campaign_element = $('#getresponse-campaign');
      var export_element = $('#gr-export');
      var tab_element = $("li[role='presentation']");
      var current_tab_input = $('#getresponse-current-tab');
      var register_hint = $('#edit-register-hint a');
      var export_hint = $('#edit-export-hint a');
      var webform_hint = $('#edit-webform-hint a');
      var connect_token = $('#getresponse_disconnect')

    $('#getresponse-form-id').change(function () {
      $('#getresponse-form-url').val($(this).find('option:selected').data('url'));
    });

      $('#getresponse-reg-campaign').change(function () {
          getCycleDays('#getresponse-reg-campaign', '#getresponse-reg-day', '.sequence-day-label');
      });
      $('#getresponse-campaign').change(function () {
          getCycleDays('#getresponse-campaign', '#getresponse-ex-day', '.sequence-ex-day-label');
      });
      function getCycleDays(getresponse_campaign, getresponse_day, sequence_day_label) {
          var campaign_id = $(getresponse_campaign).val(),
              day_element = $(getresponse_day),
              cycles_label = $(sequence_day_label),
              checkbox = $(sequence_day_label + ' input'),
              cycles = $.parseJSON('<?php echo isset($campaign_days) ? addslashes(json_encode($campaign_days)) : "{}"; ?>');
          day_element.text('');

      if (cycles.hasOwnProperty(campaign_id)) {
          var obj = cycles[campaign_id];
          var current_cycle_day = "<?php echo $getresponse_reg['day']; ?>";
          for (var prop in obj) {
              if (obj.hasOwnProperty(prop)) {
                  var select = (current_cycle_day == obj[prop].day) ? 'selected' : '';
                  var option = '<option value="' + parseInt(obj[prop].day) + '" ' + select + '>Day ' +
                          obj[prop].day + ': ' + obj[prop].name + ' (' + obj[prop].status + ')' +
                          '</option>';

                  day_element.append(option);
              }
          }
          day_element.prop('disabled', false);
          checkbox.prop('disabled', false);
      } else {
          var option = '<option value="" >no autoresponders</option>';
          day_element.append(option);
          day_element.prop('disabled', true);
          checkbox.prop('checked', false);
          checkbox.prop('disabled', true);
      }
    }

      getCycleDays('#getresponse-reg-campaign', '#getresponse-reg-day', '.sequence-day-label');
      getCycleDays('#getresponse-campaign', '#getresponse-ex-day', '.sequence-ex-day-label');

    function ajax_export() {
      var api_key = api_key_element.val();
      var campaign = campaign_element.val();
        var cycle_days = undefined;
        if($('#getresponse_ex_sequence_active').prop('checked')){
            cycle_days = $('#getresponse-ex-day').val();
        }

      $.ajax({
        url: "index.php?route=module/getresponse/export&token=<?php echo $token; ?>",
        beforeSend: function () {
          $('.gr-info').html('<div class="alert alert-info"><?php echo $info_loading; ?></div>')
        },
        data: {
            'api_key': api_key,
            'campaign': campaign,
            'cycle_days': cycle_days
        },
        success: function (data) {
          $('.gr-info').html('<div class="alert alert-success"><?php echo $text_export_success; ?></div>');
        },
        error: function (response) {
          $('.gr-info').html(' <div class="alert alert-danger"><?php echo $info_ajax_error; ?></div>');
        },
        type: "POST",
        async: false,
        dataType: "json"
      });
    }

      $(export_element).click(function () {
              ajax_export();
      });

    $(tab_element).click(function() {
      var new_value = $(this).find('a').attr('aria-controls');
      current_tab_input.val(new_value);
    });
    current_tab_input.val('<?php echo $active_tab; ?>');

    $(register_hint).on( "click", function(e) {
      $('#gr-hint-reg').toggle();
      e.preventDefault()
    });

    $(export_hint).on( "click", function(e) {
      $('#gr-hint-ex').toggle();
      e.preventDefault()
    });
    $(webform_hint).on( "click", function(e) {
      $('#gr-hint-wf').toggle();
      e.preventDefault()
    });
      $("#button_disconnect").on("click", function () {
          $(connect_token).val(1);
          $('#form').submit();
      });
      $("#button_connect").on("click", function () {
          $(connect_token).val(2);
          $('#form').submit();
      });
      $('.nav li').on("click", function () {
          $('.container-fluid .alert').remove();
          $('.gr-info').html('');

      });
      $('#GrBreadcrumb a').on("click", function (e) {
          $('.nav-tabs li a')[0].click();
          e.preventDefault();
      });
  });
  //-->
</script>
<?php echo $footer; ?>