<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<?php $appconfig = get_appconfig(); ?>
<div class="ciuis-body-content" ng-controller="Staff_Controller">

  <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">

    <!-- <div ng-show="staffLoader" layout-align="center center" class="text-center" id="circular_loader">

        <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular>

        <p style="font-size: 15px;margin-bottom: 5%;">

         <span>

            <?php echo lang2('please_wait') ?> <br>

           <small><strong><?php echo lang2('loading') . ' ' . lang2('details') . '...' ?></strong></small>

         </span>

       </p>

     </div> -->

    <md-content ng-show="!staffLoader" class="bg-white user-profile">

      <div class="col-12 col-md-4 user-display">

        <div class="user-display-bg"><img ng-src="<?php echo base_url('assets/img/staffmember_bg.png'); ?>"></div>

        <div class="user-display-bottom">

          <div class="user-display-avatar"><img ng-src="<?php echo base_url('uploads/images/') ?>{{staff.avatar}}"></div>

          <div class="user-display-info">

            <div class="name" ng-bind="staff.name"></div>

            <div class="nick"><span ng-bind="staff.staff_number"></span></div>

            <div class="nick"><span class="mdi mdi-account"></span> <span ng-bind="staff.properties.department"></span></div>

          </div>

        </div>

        <md-divider></md-divider>

        <md-content class="bg-white">

          <md-list flex class="md-p-0 sm-p-0 lg-p-0">

            <md-divider></md-divider>

            <md-list-item>

              <md-icon class="ion-android-call"></md-icon>

              <p ng-bind="staff.phone"></p>

            </md-list-item>

            <md-divider></md-divider>

            <md-list-item>

              <md-icon class="mdi ion-location"></md-icon>

              <p ng-bind="staff.address"></p>

            </md-list-item>

            <md-divider></md-divider>

            <md-list-item>

              <md-icon class="mdi ion-android-mail"></md-icon>

              <p ng-bind="staff.email"></p>

            </md-list-item>

          </md-list>

        </md-content>

        <md-divider></md-divider>

        <md-content class="md-padding bg-white">

          <div class="col-xs-4">

            <div class="title"><?php echo lang2('sales') ?></div>

            <div class="counter"><strong ng-bind-html="staff.properties.sales_total | currencyFormat:cur_code:null:true:cur_lct"></strong></div>

          </div>

          <div class="col-xs-4">

            <div class="title"><?php echo lang2('customers') ?></div>

            <div class="counter"><strong ng-bind="staff.properties.total_customer"></strong></div>

          </div>

          <div class="col-xs-4">

            <div class="title"><?php echo lang2('tickets') ?></div>

            <div class="counter"><strong ng-bind="staff.properties.total_ticket"></strong></div>

          </div>

        </md-content>

      </div>

      <div class="col-md-8 col-12" style="padding: 0px">

        <md-toolbar class="toolbar-white">

          <div class="md-toolbar-tools">

            <h2 class="md-pl-10" flex md-truncate><?php echo lang2('staffdetail') ?></h2>

            <?php //if (check_privilege('staff', 'edit')) {
            ?>

            <md-button ng-click="Update()" class="md-icon-button" aria-label="Update" ng-cloak>

              <md-tooltip md-direction="bottom"><?php echo lang2('update') ?></md-tooltip>

              <md-icon><i class="ion-compose  text-muted"></i></md-icon>

            </md-button>

            <?php //}
            ?>

            <md-menu md-position-mode="target-right target" ng-cloak>

              <md-button aria-label="Open demo menu" class="md-icon-button" ng-click="$mdMenu.open($event)">

                <md-icon><i class="ion-android-more-vertical text-muted"></i></md-icon>

              </md-button>

              <md-menu-content width="4">

                <?php //if (check_privilege('staff', 'edit')) {
                ?>

                <md-menu-item>

                  <md-button ng-click="ChangePassword()">

                    <div layout="row" flex>

                      <p flex><?php echo lang2('changepassword') ?></p>

                      <md-icon md-menu-align-target class="ion-locked" style="margin: auto 3px auto 0;"></md-icon>

                    </div>

                  </md-button>

                </md-menu-item>

                <md-menu-item>

                  <md-button ng-click="ChangeAvatar()">

                    <div layout="row" flex>

                      <p flex><?php echo lang2('changeprofilepicture') ?></p>

                      <md-icon md-menu-align-target class="ion-android-camera" style="margin: auto 3px auto 0;"></md-icon>

                    </div>

                  </md-button>

                </md-menu-item>

                <?php //}
                if (check_privilege('staff', 'delete')) { ?>

                  <md-menu-item>

                    <md-button ng-click="Delete()">

                      <div layout="row" flex>

                        <p flex><?php echo lang2('delete') ?></p>

                        <md-icon md-menu-align-target class="ion-trash-b" style="margin: auto 3px auto 0;"></md-icon>

                      </div>

                    </md-button>

                  </md-menu-item>

                <?php } ?>

              </md-menu-content>

            </md-menu>

          </div>

        </md-toolbar>

        <md-content class="bg-white">

          <div class="col-md-12" style="align-self: flex-end;">

            <div class="widget-chart-container">

              <div class="widget-counter-group widget-counter-group-right md-p-20">

                <div class="pull-left text-left">

                  <h4><b><?php echo lang2('staffsalesgraphtitle') ?></b></h4>

                  <small><?php echo lang2('staffsalesgraphdescription') ?></small>
                </div>

                <div class="counter counter-big md-p-10">

                  <div class="text-warning value" ng-bind-html="staff.properties.sales_total | currencyFormat:cur_code:null:true:cur_lct"></div>

                  <div class="desc"><?php echo lang2('inthisyear') ?></div>

                </div>

              </div>

              <div class="ciuis-chart" style="align-self: flex-end;" ng-cloak>

                <div class="card">

                  <canvas width="900px" height="260px" id="staff_sales_chart"></canvas>

                  <div class="axis">

                    <div ng-repeat="inline in staff.properties.chart_data.inline_graph" class="tick"> {{inline.month}} <span class="value value--this" ng-bind-html="inline.total | currencyFormat:cur_code:null:true:cur_lct"></span> <span class="value value--prev" ng-bind-html="inline.total | currencyFormat:cur_code:null:true:cur_lct"></span> </div>

                  </div>

                </div>

              </div>

            </div>

          </div>

        </md-content>

      </div>

      <?php if (!session()->get('other')) { ?>

        <div class="col-md-12 col-12" ng-cloak>
          <md-divider></md-divider>
          <md-content class="bg-white">
            <md-tabs md-dynamic-height md-border-bottom>


              <md-tab label="Performance">
                <md-content class="bg-white">
                  <div class='filtrosLeads' style="min-height: 10px;padding: 1em 2em 0em 2em;">
                    <md-input-container class="md-block">
                      <label>Período</label>
                      <input required type=date ng-model="filtros.dt_de" ng-change="getResultados()">
                    </md-input-container>

                    <md-input-container class="md-block">
                      <label>Á</label>
                      <input required type=date ng-model="filtros.dt_ate" ng-change="getResultados()">
                    </md-input-container>

                    <md-button style="width: 110px !important;margin-top: 13px;margin-left: 20px;" ng-click="filtrarChartTime()" class="md-raised md-primary btn-report block-button" aria-label="Close"> Filtrar</md-button>

                  </div>
                </md-content>

                <md-content class="bg-white" style="margin-bottom: 20px;">
                  <div class="ciuis-chart" style="align-self: flex-end;" ng-cloak>
                    <div class="card">
                      <canvas width="900" height="350" id="staff_graph_time"></canvas>

                      <div class="axis" style="height: 93%;">
                        <div style="padding-top: 270px;" ng-click="openAtividades($index)" ng-repeat="inline in staff.properties.chart_data_time.inline_graph" class="tick"> {{inline.label}}
                          <span class="value value--this" style="transform: translateY(0);display: block;" ng-bind-html="inline.total "></span>
                          <span class="value value--prev" ng-bind-html="inline.total "></span>
                        </div>
                      </div>
                    </div>

                </md-content>
                <md-content class="bg-white" style="margin-bottom: 50px;">
                  <h4 style="margin-left: 15px;">Lista de atividades</h4>

                  <md-list flex class="md-p-0 sm-p-0 lg-p-0">
                    <md-list-item ng-repeat="log in logsList" aria-label="Logs">
                      <md-icon><i class="fa fa-chart-line"></i></md-icon>
                      <p><strong ng-bind-html="log.detail"></strong></p>
                      <h5><span ng-bind-html="log.date"></span></h5>
                      <md-divider></md-divider>
                    </md-list-item>
                  </md-list>
                  <md-content ng-show="!logsList.length" class="md-padding bg-white no-item-data"><?php echo lang2('notdata') ?>
                    <br>
                    <small>Clique em um dia acima para visualizar as atividades!</small>
                  </md-content>


                </md-content>
              </md-tab>

              <md-tab label="Vencimentos">
                <md-content class="bg-white">
                  <div class='filtrosLeads' style="min-height: 10px;padding: 1em 2em 0em 2em;">
                    <md-input-container class="md-block">
                      <label>Período</label>
                      <input required type=date ng-model="filtrosVenci.dt_de">
                    </md-input-container>

                    <md-input-container class="md-block">
                      <label>Á</label>
                      <input required type=date ng-model="filtrosVenci.dt_ate">
                    </md-input-container>

                    <md-button style="width: 110px !important;margin-top: 13px;margin-left: 20px;" ng-click="filtrarVenci()" class="md-raised md-primary btn-report block-button" aria-label="Close"> Filtrar</md-button>

                  </div>
                </md-content>

                <md-content class="bg-white widget-fullwidth ciuis-body-loading" id="contentMain" style="min-height: 500px;">


                  <div layout-xs="column" layout="row" class="">
                    <div flex-xs flex-gt-xs="100" layout="column">
                      <md-card>
                        <div class="widget-chart-container">
                          <div class="widget-counter-group widget-counter-group-right">
                            <div style="width: 100%;" class="pull-left">
                              <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                              <div class="titleDash">
                                <h4 style="padding: 0px;margin: 0px;"><b>Salário/comissão e bônus</b>
                                  <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['salarioEComissao'] != null && baloesGraphs['salarioEComissao'].exibir == '1')" ng-click="exibeBalao('salarioEComissao')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                </h4>
                              </div>
                            </div>
                          </div>
                          <div class="my-2" style="padding-bottom: 60px;">
                            <div class="chart-wrapper" style="height:362px">
                              <canvas height="300" style="padding-top: 25px;" id="salarioEComissao"></canvas>
                            </div>
                          </div>
                        </div>
                      </md-card>
                    </div>
                  </div>
                </md-content>
              </md-tab>

              <md-tab label="<?php echo lang2('work_plan') ?>">

                <md-content class="text-center bg-white">

                  <md-toolbar class="toolbar-white">

                    <div class="md-toolbar-tools">

                      <h2 flex md-truncate><?php echo lang2('work_plan') ?></h2>

                      <?php if (check_privilege('staff', 'edit')) { ?>

                        <md-button class="md-icon-button" aria-label="view" ng-href="<?php echo base_url('staff/restore_workplan/') . $id ?>">

                          <md-icon><i class="ion-ios-refresh-outline text-muted"></i></md-icon>

                          <md-tooltip md-direction="bottom"><?php echo lang2('restore') . ' ' . lang2('workplan') ?></md-tooltip>

                        </md-button>

                      <?php } ?>

                      <md-button ng-show='View_Work == false' ng-click="View_Work = true" ng-init="View_Work = false" class="md-icon-button" aria-label="view">

                        <md-icon><i class="mdi mdi-plus-circle text-muted"></i></md-icon>

                        <md-tooltip md-direction="bottom"><?php echo lang2('show') ?></md-tooltip>

                      </md-button>

                      <md-button aria-label="Select All" class="md-icon-button" ng-show='View_Work == true' ng-click="View_Work = false">

                        <md-icon><i class="mdi mdi-minus-circle-outline text-muted"></i></md-icon>

                        <md-tooltip md-direction="bottom"><?php echo lang2('hide') ?></md-tooltip>

                      </md-button>

                      <?php if (check_privilege('staff', 'edit')) { ?>

                        <md-button ng-click="UpdateWorkPlan()" class="md-raised md-primary btn-report" aria-label='Update Work Plan' ng-disabled="savingWork == true">

                          <span ng-hide="savingWork == true"><?php echo lang2('save'); ?></span>

                          <md-progress-circular class="white" ng-show="savingWork == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

                          <md-tooltip ng-hide="savingWork == true" md-direction="bottom"><?php echo lang2('update_work_plan') ?></md-tooltip>

                        </md-button>

                      <?php } ?>

                    </div>

                  </md-toolbar>

                  <div layout="row" layout-wrap ng-show="View_Work == true">

                    <md-content flex="13" class="week-day-time bg-white" ng-repeat="weekday in staff.work_plan" layout-padding>

                      <md-checkbox ng-model="weekday.status"><span class="text-uppercase text-bold">{{ weekday.day }}</span></md-checkbox>

                      <fieldset class="demo-fieldset">

                        <legend class="demo-legend"><?php echo lang2('working_hours') ?></legend>

                        <md-input-container>

                          <label><?php echo lang2('start') ?></label>

                          <input str-to-time="" ng-model="weekday.start" type="time">

                        </md-input-container>

                        <md-input-container>

                          <label><?php echo lang2('end') ?></label>

                          <input str-to-time="" ng-model="weekday.end" type="time">

                        </md-input-container>

                      </fieldset>

                      <fieldset class="demo-fieldset">

                        <legend class="demo-legend"><?php echo lang2('break_time') ?></legend>

                        <md-input-container>

                          <label><?php echo lang2('start') ?></label>

                          <input str-to-time="" ng-model="weekday.breaks.start" type="time">

                        </md-input-container>

                        <md-input-container>

                          <label><?php echo lang2('end') ?></label>

                          <input str-to-time="" ng-model="weekday.breaks.end" type="time">

                        </md-input-container>

                      </fieldset>

                    </md-content>

                  </div>

                </md-content>

              </md-tab>

              <md-tab label="<?php echo lang2('invoices'); ?>">

                <md-content class="bg-white">
                  <md-list flex class="md-p-0 sm-p-0 lg-p-0">
                    <md-list-item ng-repeat="invoice in invoices" ng-click="GoInvoice($index)" aria-label="Invoice">
                      <md-icon class="ico-ciuis-invoices"></md-icon>
                      <p><strong ng-bind="invoice.longid"></strong></p>
                      <h4><strong ng-bind-html="invoice.total | currencyFormat:cur_code:null:true:cur_lct"></strong></h4>
                      <md-divider></md-divider>
                    </md-list-item>
                  </md-list>
                  <md-content ng-show="!invoices.length" class="md-padding bg-white no-item-data"><?php echo lang2('notdata') ?></md-content>
                </md-content>

              </md-tab>

              <md-tab label="<?php echo lang2('proposals'); ?>">

                <md-content class="bg-white">

                  <md-list flex class="md-p-0 sm-p-0 lg-p-0">

                    <md-list-item ng-repeat="proposal in proposals" ng-click="GoProposal($index)" aria-label="Proposal">

                      <md-icon class="ico-ciuis-proposals"></md-icon>

                      <p><strong ng-bind="proposal.longid"></strong></p>

                      <h4><strong ng-bind-html="proposal.total | currencyFormat:cur_code:null:true:cur_lct"></strong></h4>

                      <md-divider></md-divider>

                    </md-list-item>

                  </md-list>

                  <md-content ng-show="!proposals.length" class="md-padding bg-white no-item-data"><?php echo lang2('notdata') ?></md-content>

                </md-content>

              </md-tab>

              <md-tab label="<?php echo lang2('tickets'); ?>">

                <md-content class="bg-white">

                  <md-list flex class="md-p-0 sm-p-0 lg-p-0">

                    <md-list-item ng-repeat="ticket in tickets" ng-click="GoTicket($index)" aria-label="Ticket">

                      <md-icon class="ico-ciuis-supports"></md-icon>

                      <p><strong ng-bind="ticket.ticket_number"></strong>&nbsp;<strong ng-bind="ticket.subject"></strong></p>

                      <p><strong ng-bind="ticket.contactname"></strong></p>

                      <h4><strong ng-bind="ticket.priority"></strong></h4>

                      <md-divider></md-divider>

                    </md-list-item>

                  </md-list>

                  <md-content ng-show="!tickets.length" class="md-padding bg-white no-item-data"><?php echo lang2('notdata') ?></md-content>

                </md-content>

              </md-tab>


            </md-tabs>
          </md-content>


    </md-content>
    </md-content>

  </div>

<?php } ?>


<md-content class="bg-white" ng-cloak>

  <md-subheader ng-if="custom_fields > 0"><?php echo lang2('custom_fields'); ?></md-subheader>

  <md-list-item ng-if="custom_fields" ng-repeat="field in custom_fields">

    <md-icon class="{{field.icon}} material-icons"></md-icon>

    <strong flex md-truncate>{{field.name}}</strong>

    <p ng-if="field.type === 'input'" class="text-right" flex md-truncate ng-bind="field.data"></p>

    <p ng-if="field.type === 'textarea'" class="text-right" flex md-truncate ng-bind="field.data"></p>

    <p ng-if="field.type === 'date'" class="text-right" flex md-truncate ng-bind="field.data | date:'dd, MMMM yyyy EEEE'"></p>

    <p ng-if="field.type === 'select'" class="text-right" flex md-truncate ng-bind="custom_fields[$index].selected_opt.name"></p>

    <md-divider ng-if="custom_fields"></md-divider>

  </md-list-item>

</md-content>



<md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Update" ng-cloak style="width: 450px;">

  <md-toolbar class="toolbar-white">

    <div class="md-toolbar-tools">

      <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>

      <h2 flex md-truncate><?php echo lang2('update') ?></h2>

      <md-switch ng-model="staff.active" aria-label="Type"><strong class="text-muted"><?php echo lang2('active') ?></strong></md-switch>

    </div>

  </md-toolbar>

  <md-content>

    <md-content layout-padding>

      <md-input-container class="md-block">

        <label><?php echo lang2('name') ?></label>

        <input required type="text" ng-model="staff.name" class="form-control" id="title" />

      </md-input-container>

      <md-input-container class="md-block">

        <label><?php echo lang2('email') ?></label>

        <input required type="text" ng-model="staff.email" class="form-control" id="title" />

      </md-input-container>

      <md-input-container class="md-block">

        <label><?php echo lang2('phone') ?></label>

        <input type="text" ng-model="staff.phone" class="form-control phone" id="title" />

      </md-input-container>
      <?php if (check_privilege('staff', 'edit')) { ?>
        <md-input-container class="md-block" flex-gt-xs>

          <label><?php echo lang2('staffdepartment'); ?></label>

          <md-select required ng-model="staff.department_id" name="assigned" style="min-width: 200px;">

            <md-option ng-value="department.id" ng-repeat="department in departments">{{department.name}}</md-option>

          </md-select>

          <br>

        </md-input-container>
      <?php } ?>

      <md-input-container class="md-block" flex-gt-xs>

        <label><?php echo lang2('language'); ?></label>

        <md-select required ng-model="staff.language" name="assigned" style="min-width: 200px;">

          <md-option ng-value="language.foldername" ng-repeat="language in languages">{{language.name}}</md-option>

        </md-select>

        <br>

      </md-input-container>
      <?php if (check_privilege('staff', 'edit')) { ?>
        <md-input-container class="md-block" flex-gt-xs>

          <label><?php echo lang2('roles'); ?></label>

          <md-select required ng-model="staff.assigned_role" name="assigned_role" style="min-width: 200px;">

            <md-option ng-value="assigned_role.role_id" ng-repeat="assigned_role in roles">{{assigned_role.role_name}} <span class="badge">{{assigned_role.role_type}}</span></md-option>

          </md-select>

          <br>

        </md-input-container>

      <?php } ?>

      <md-input-container class="md-block">

        <label><?php echo lang2('staff') . ' ' . lang2('timezone') ?></label>

        <md-select ng-model="staff_timezone">

          <md-optgroup ng-repeat="timezone in timezones" label="{{timezone.group}}">

            <md-option ng-value="zone.value" ng-repeat="zone in timezone.zones">{{zone.value}}</md-option>

          </md-optgroup>

        </md-select>

      </md-input-container>

      <md-input-container class="md-block">

        <label><?php echo lang2('address') ?></label>

        <textarea rows="2" ng-model="staff.address" class="form-control"></textarea>

      </md-input-container>

    </md-content>

    <custom-fields-vertical></custom-fields-vertical>

    <md-content>

      <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>

        <md-button ng-click="UpdateStaff()" class="md-raised md-primary btn-report block-button" ng-disabled="saving == true">

          <span ng-hide="saving == true"><?php echo lang2('update'); ?></span>

          <md-progress-circular class="white" ng-show="saving == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

        </md-button>

        <br /><br /><br /><br />

      </section>

    </md-content>

  </md-content>

</md-sidenav>
</div>

<script type="text/ng-template" id="change-avatar-template.html">

  <md-dialog aria-label="options dialog">

  <md-dialog-content layout-padding>

    <h2 class="md-title"><?php echo lang2('choosefile'); ?></h2>

    <input type="file" required name="profile_photo" file-model="profile_photo" accept="image/*">

  </md-dialog-content>

  <md-dialog-actions>

    <span flex></span>

    <md-button ng-click="close()" aria-label="add"><?php echo lang2('cancel') ?>!</md-button>

    <md-button ng-click="updateProfilePic()" class="template-button" ng-disabled="uploading == true">

      <span ng-hide="uploading == true"><?php echo lang2('save'); ?></span>

      <md-progress-circular class="white" ng-show="uploading == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

    </md-button>

  </md-dialog-actions>

  </md-dialog>

  </script>

<script type="text/ng-template" id="google-calendar-template.html">

  <md-dialog aria-label="options dialog">

  <md-dialog-content layout-padding>

    <h2 class="md-title"><?php echo lang2('google_calendar_settings'); ?></h2>

    <md-content class="bg-white" layout-padding>

    <md-input-container ng-if="staff.google_calendar_enable" class="md-block">

      <label><?php echo lang2('google_calendar_id') ?></label>

      <input required ng-model="staff.google_calendar_id">

    </md-input-container>

    <md-input-container ng-if="staff.google_calendar_enable" class="md-block">

      <label><?php echo lang2('google_calendar_api_key') ?></label>

      <input required ng-model="staff.google_calendar_api_key">

    </md-input-container>

    <md-switch class="pull-left" ng-model="staff.google_calendar_enable" aria-label="Enable">

      <strong class="text-muted"><?php echo lang2('enable') ?></strong>

    </md-switch>

    </md-content>

  </md-dialog-content>

  <md-dialog-actions>

    <span flex></span>

    <md-button ng-click="close()"><?php echo lang2('cancel') ?>!</md-button>

    <md-button ng-click="UpdateGoogleCalendar()"><?php echo lang2('update') ?>!</md-button>

  </md-dialog-actions>

  </md-dialog>

</script>

<script type="text/ng-template" id="change-password.html">

  <md-dialog aria-label="options dialog">

  <md-dialog-content layout-padding>

    <h2 class="md-title"><?php echo lang2('changepassword'); ?></h2>

    <md-content class="bg-white" layout-padding>

    <md-input-container class="md-block">

      <label><?php echo lang2('old') . ' ' . lang2('password') ?></label>

      <input type="password" required ng-model="password.old">

    </md-input-container>

    <md-input-container class="md-block">

      <label><?php echo lang2('new') . ' ' . lang2('password') ?></label>

      <input type="password" required ng-model="password.newpassword">

    </md-input-container>

    <md-input-container class="md-block">

      <label><?php echo lang2('confirm') . ' ' . lang2('new') . ' ' . lang2('password') ?></label>

      <input type="password" required ng-model="password.c_newpassword">

    </md-input-container>

    </md-content>

  </md-dialog-content>

  <md-dialog-actions>

    <span flex></span>

    <md-button ng-click="close()"><?php echo lang2('cancel') ?>!</md-button>

    <md-button ng-click="UpdatePassword()" class="md-raised md-primary pull-right" ng-disabled="saving == true">

      <span ng-hide="saving == true"><?php echo lang2('update'); ?></span>

      <md-progress-circular class="white" ng-show="saving == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

    </md-button>

  </md-dialog-actions>

  </md-dialog>

</script>

<script type="text/ng-template" id="change-password-admin.html">

  <md-dialog aria-label="options dialog">

  <md-dialog-content layout-padding>

    <h2 class="md-title"><?php echo lang2('changepassword'); ?></h2>

    <md-content class="bg-white" layout-padding>

    <md-input-container class="md-block">

      <label><?php echo lang2('new') . ' ' . lang2('password') ?></label>

      <input type="password" required ng-model="apassword.newpassword">

    </md-input-container>

    <md-input-container class="md-block">

      <label><?php echo lang2('confirm') . ' ' . lang2('new') . ' ' . lang2('password') ?></label>

      <input type="password" required ng-model="apassword.c_newpassword">

    </md-input-container>

    </md-content>

  </md-dialog-content>

  <md-dialog-actions>

    <span flex></span>

    <md-button ng-click="close()"><?php echo lang2('cancel') ?>!</md-button>

    <md-button ng-click="UpdatePasswordAdmin()" class="md-raised md-primary pull-right" ng-disabled="saving == true">

      <span ng-hide="saving == true"><?php echo lang2('update'); ?></span>

      <md-progress-circular class="white" ng-show="saving == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

    </md-button>

  </md-dialog-actions>

  </md-dialog>

</script>

</div>

<script>
  var STAFFID = "<?php echo $id; ?>"
  var lang = {};
  lang.doIt = "<?php echo lang2('doIt') ?>";
  lang.cancel = "<?php echo lang2('cancel') ?>";
  lang.attention = "<?php echo lang2('attention') ?>";
  lang.delete_staff = "<?php echo lang2('staffattentiondetail') ?>";
</script>
<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>

<script src="<?php echo base_url('assets/lib/chartjs/dist/Chart.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/CiuisAngular.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/staffs.js?v=d1.1.10'); ?>"></script>