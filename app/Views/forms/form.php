<?php include_once(APPPATH . 'Views/inc/header.php'); ?>
<link rel='stylesheet' href="<?php echo base_url('assets/lib/bootstrap/dist/css/bootstrap.min.css')?>">
<link rel='stylesheet' href="<?php echo base_url('assets/lib/form-builder/formio.full.min.css')?>">
<script src="<?php echo base_url('assets/lib/form-builder/formio.full.min.js')?>"></script> 
<div class="ciuis-body-content" ng-controller="WebLead_Controller">
  <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">
    <style type="text/css">
      .btn-primary {
        color: #fff;
        background-color: #656565;
        border-color: #656565;
        font-size: 14px !important;
        padding: 8px !important;
      }
      .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
        background-color: rgb(255,188,0) !important;
        border-color: rgb(255,188,0) !important;
        box-shadow: inset 0 -1px 0 #ffbc00;
      }
      .formio-component-widget.type {
        display: none !important;
      }

      @media (max-width: 1500px) {
        .formcomponents {
          width: 25% !important;
        }
        .formarea.drag-container {
          width: 75% !important;
        }
      }
    </style>
    <div ng-show="webleadsLoader" layout-align="center center" class="text-center" id="circular_loader">
      <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular>
      <p style="font-size: 15px;margin-bottom: 5%;">
        <span>
          <?php echo lang2('please_wait') ?> <br>
          <small><strong><?php echo lang2('loading'). ' '. lang2('webleads').'...' ?></strong></small>
        </span>
      </p>
    </div>
    <md-toolbar ng-show="!webleadsLoader" class="toolbar-white" ng-cloak>
      <div class="md-toolbar-tools">
        <md-button class="md-icon-button" aria-label="" ng-disabled="true">
          <md-icon><i class="ion-earth text-muted"></i></md-icon>
        </md-button>
        <h2 flex md-truncate><?php echo lang2('webleads') ?></h2>
        <md-button id="saveFormContent" class="md-raised md-primary pull-right" ng-disabled="savingLead == true">
          <span ng-hide="savingLead == true"><?php echo lang2('save');?></span>
          <md-progress-circular class="white" ng-show="savingLead == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>
        </md-button>
        <md-button ng-click="viewIntegration()" class="md-icon-button" aria-label="Update">
          <md-tooltip md-direction="bottom"><?php echo lang2('integrationcode') ?></md-tooltip> 
          <md-icon><i class="ion-gear-b text-muted"></i></md-icon>
        </md-button>
        <md-switch ng-change="changeStatus(weblead.status)" ng-model="weblead.status" aria-label="Type">
          <strong class="text-muted"><?php echo lang2('active') ?></strong>
        </md-switch>
        <?php if (check_privilege('leads', 'edit') || check_privilege('leads', 'delete')) { ?>   
          <md-menu md-position-mode="target-right target">
            <md-button aria-label="Open demo menu" class="md-icon-button" ng-click="$mdMenu.open($event)">
              <md-icon><i class="ion-android-more-vertical text-muted"></i></md-icon>
            </md-button>
            <md-menu-content width="4">
              <?php if (check_privilege('leads', 'edit')) { ?> 
                <md-menu-item>
                  <md-button ng-click="UpdateForm()">
                    <div layout="row" flex>
                      <p flex ng-bind="lang.edit"></p>
                      <md-icon md-menu-align-target class="ion-compose text-muted" style="margin: auto 3px auto 0;"></md-icon>
                    </div>
                  </md-button>
                </md-menu-item>
              <?php } if (check_privilege('leads', 'delete')) { ?> 
                <md-menu-item>
                  <md-button ng-click="deleteForm()">
                    <div layout="row" flex>
                      <p flex ng-bind="lang.delete"></p>
                      <md-icon md-menu-align-target class="ion-trash-b text-muted" style="margin: auto 3px auto 0;"></md-icon>
                    </div>
                  </md-button>
                </md-menu-item>
              <?php } ?>
            </md-menu-content>
          </md-menu>
        <?php } ?>
      </div>
    </md-toolbar>
    <md-content class="md-padding bg-white">
      <div id='builder' style="width: 90%;margin-left: auto;margin-right: auto;border: 1px solid whitesmoke;padding: 10px;"></div>
      <md-content layout-padding class="bg-white" style="width: 80%;margin-left: auto;margin-right: auto;">
       <!--  <section layout="row" layout-sm="column" layout-align="pull-right" layout-wrap style="float: left;">
          <md-button id="saveFormContent" class="md-raised md-primary pull-right" ng-disabled="savingLead == true">
            <span ng-hide="savingLead == true"><?php echo lang2('save');?></span>
            <md-progress-circular class="white" ng-show="savingLead == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>
          </md-button>
        </section> -->
      </md-content>
    </md-content>
  </div>
  <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Update" ng-cloak style="width: 450px;">
    <md-toolbar class="md-theme-light" style="background:#262626">
      <div class="md-toolbar-tools">
        <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>
        <md-truncate><?php echo lang2('update') ?></md-truncate> &nbsp;&nbsp;&nbsp;
      </div>
    </md-toolbar>
    <md-content>
      <md-content layout-padding>
        <md-input-container class="md-block">
          <label><?php echo lang2('form_name'); ?></label>
          <input ng-model="weblead.name">
        </md-input-container>
        <md-input-container class="md-block">
          <label><?php echo lang2('assigned'); ?></label>
          <md-select placeholder="<?php echo lang2('choosestaff'); ?>" ng-model="weblead.assigned_id" style="min-width: 200px;">
            <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
          </md-select>
        </md-input-container>
        <br>
        <md-input-container class="md-block">
          <label><?php echo lang2('status'); ?></label>
          <md-select placeholder="<?php echo lang2('status'); ?>" ng-model="weblead.status_id" style="min-width: 200px;">
            <md-select-header>
              <md-toolbar class="toolbar-white">
                <div class="md-toolbar-tools">
                  <h4 flex md-truncate><?php echo lang2('status') ?></h4>
                  <md-button class="md-icon-button" ng-click="NewStatus()" aria-label="Create New">
                    <md-icon><i class="mdi mdi-plus text-muted"></i></md-icon>
                  </md-button>
                </div>
              </md-toolbar>
            </md-select-header>
            <md-option ng-value="status.id" ng-repeat="status in leadstatuses">{{status.name}}</md-option>
          </md-select>
        </md-input-container>
        <br>
        <md-input-container class="md-block">
          <label><?php echo lang2('source'); ?></label>
          <md-select placeholder="<?php echo lang2('source'); ?>" ng-model="weblead.source_id" style="min-width: 200px;">
            <md-select-header>
              <md-toolbar class="toolbar-white"> 
                <div class="md-toolbar-tools">
                  <h4 flex md-truncate><?php echo lang2('source') ?></h4>
                  <md-button class="md-icon-button" ng-click="NewSource()" aria-label="Create New">
                    <md-icon><i class="mdi mdi-plus text-muted"></i></md-icon>
                  </md-button>
                </div>
              </md-toolbar>
            </md-select-header>
            <md-option ng-value="source.id" ng-repeat="source in leadssources">{{source.name}}</md-option>
          </md-select>
        </md-input-container><br>
        <!-- <md-input-container class="md-block">
          <label><?php echo lang2('submit_text'); ?></label>
          <?php $submit = lang2('submit'); ?>
          <input ng-model="weblead.submit_text" ng-init="weblead.submit_text = '<?php echo $submit ?>'">
        </md-input-container> -->
        <md-input-container class="md-block">
          <label><?php echo lang2('message_after_success') ?></label>
          <?php $success = lang2('leads_success_message'); ?>
          <textarea ng-model="weblead.success_message" ng-init="weblead.success_message = '<?php echo $success ?>'" md-maxlength="300" md-select-on-focus></textarea>
        </md-input-container>
        <md-checkbox ng-checked="true" ng-model="weblead.notification"><?php echo lang2('email_notification') ?></md-checkbox>
        <md-checkbox ng-checked="true" ng-model="weblead.duplicate"><?php echo lang2('allow_duplicate') ?></md-checkbox>
      </md-content>
      <md-content layout-padding>
        <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>
          <?php if (check_privilege('leads', 'edit')) { ?> 
            <md-button id="saveFormContent" ng-click="SaveWebLeadForm()" class="md-raised md-primary pull-right" ng-disabled="savingLead == true">
              <span ng-hide="savingLead == true"><?php echo lang2('save');?></span>
              <md-progress-circular class="white" ng-show="savingLead == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>
            </md-button>
          <?php } ?>
        </section>
      </md-content>
    </md-content>
  </md-sidenav>
  <script type="text/ng-template" id="viewIntegration.html">
  <md-dialog aria-label="options dialog">
    <md-toolbar class="toolbar-white">
        <div class="md-toolbar-tools">
          <h2><strong class="text-success"><?php echo lang2('integrationcode') ?></strong></h2>
          <span flex></span>
          <md-button class="md-icon-button" ng-click="close()" aria-label="add">
            <md-icon class="ion-close-round" aria-label="Close dialog" style="color:black"></md-icon>
          </md-button>
        </div>
      </md-toolbar>
  <md-dialog-content layout-padding>
    <md-content class="md-padding bg-white">
      <p>
        <?php echo lang2('integrationcode_line1') ?>
      </p>
      <pre style="padding: 20px 10px;" class="editorField"> {{iframeText}}
      </pre><br>
      <?php echo lang2('test_it') ?>: <a class=""  target="_blank" href="<?php echo base_url('forms/wlf/'.$token) ?>"><?php echo base_url('forms/wlf/'.$token) ?></a> <br>
    </md-content>
  </md-dialog-content>
  </md-dialog>
</script>
</div>
  <script type="text/javascript">
    var lang = {};
    lang.title = '<?php echo lang2('title') ?>';
    lang.formbuilder = '<?php echo lang2('form_builder') ?>';
    lang.name = '<?php echo lang2('name') ?>';
    lang.company = '<?php echo lang2('company') ?>';
    lang.phone = '<?php echo lang2('phone') ?>';
    lang.email = '<?php echo lang2('email') ?>';
    lang.website = '<?php echo lang2('website') ?>';
    lang.company = '<?php echo lang2('company') ?>';
    lang.state = '<?php echo lang2('state') ?>';
    lang.city = '<?php echo lang2('city') ?>';
    lang.zip = '<?php echo lang2('zip') ?>';
    lang.address = '<?php echo lang2('address') ?>';
    lang.country = '<?php echo lang2('country') ?>';
    lang.description = '<?php echo lang2('description') ?>';
    lang.date = '<?php echo lang2('date') ?>';
    lang.submit = '<?php echo $submit ?>';
    lang.emailError = '<?php echo lang2('emailError') ?>';
    lang.custom_fields = '<?php echo lang2('custom_fields') ?>';

    var FORMID = '<?php echo $formId; ?>';
</script>
<?php include_once( APPPATH . 'Views/inc/footer.php' );?>
<script src="<?php echo base_url('assets/js/webleads.js'); ?>"></script>