<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>

<?php $appconfig = get_appconfig(); ?>

<div class="ciuis-body-content" ng-controller="Projects_Controller">

  <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-9">

    <md-toolbar class="toolbar-white" style="margin-bottom: 1%;">

      <div class="md-toolbar-tools">

        <md-button class="md-icon-button" aria-label="File">

          <md-icon><i class="icon ico-ciuis-projects text-muted"></i></md-icon>

        </md-button>

        <h2 flex md-truncate><?php echo lang2('projects'); ?> <small>(<span ng-bind="projects.length"></span>)</small></h2>

        <div class="ciuis-external-search-in-table" ng-cloak>

          <input ng-model="project_search" class="search-table-external" id="search" name="search" type="text" placeholder="<?php echo lang2('search_by') . ' ' . lang2('project') . ' ' . lang2('name') ?>">

          <md-button class="md-icon-button" aria-label="Search">

            <md-icon><i class="ion-search text-muted"></i></md-icon>

          </md-button>

        </div>

        <md-button ng-show="showGrid==true" ng-click="showGrid=false;showList=true;updateColumns('list_view', true);" class="md-icon-button" aria-label="New" ng-cloak>

          <md-tooltip md-direction="bottom"><?php echo lang2('show') . ' ' . lang2('list') ?></md-tooltip>

          <md-icon><i class="ion-ios-list-outline text-muted"></i></md-icon>

        </md-button>

        <md-button ng-show="showList==true" ng-click="showList=false;showGrid=true;updateColumns('list_view', false);" class="md-icon-button" aria-label="New" ng-cloak>

          <md-tooltip md-direction="bottom"><?php echo lang2('show') . ' ' . lang2('grid') ?></md-tooltip>

          <md-icon><i class="ion-android-apps text-muted"></i></md-icon>

        </md-button>

        <md-menu md-position-mode="target-right target">

          <md-button ng-show="showList==true" class="md-icon-button" aria-label="New" ng-cloak ng-click="$mdMenu.open($event)">

            <md-tooltip md-direction="bottom"><?php echo lang2('filter_columns') ?></md-tooltip>

            <md-icon><i class="ion-connection-bars text-muted"></i></md-icon>

          </md-button>

          <md-menu-content width="4" ng-cloak>

            <md-contet layout-padding>

              <md-checkbox md-no-ink aria-label="column select" class="md-primary" ng-model="table_columns.name" ng-change="updateColumns('name', table_columns.name);">

                <?php echo lang2('project') . ' ' . lang2('name') ?>

              </md-checkbox><br>

              <md-checkbox md-no-ink aria-label="column select" class="md-primary" ng-model="table_columns.customer" ng-change="updateColumns('customer', table_columns.customer);">

                <?php echo lang2('customer') ?>

              </md-checkbox><br>

              <md-checkbox md-no-ink aria-label="column select" class="md-primary" ng-model="table_columns.startdate" ng-change="updateColumns('startdate', table_columns.startdate);">

                <?php echo lang2('startdate') ?>

              </md-checkbox><br>

              <md-checkbox md-no-ink aria-label="column select" class="md-primary" ng-model="table_columns.deadline" ng-change="updateColumns('deadline', table_columns.deadline);">

                <?php echo lang2('deadline') ?>

              </md-checkbox><br>

              <md-checkbox md-no-ink aria-label="column select" class="md-primary" ng-model="table_columns.status" ng-change="updateColumns('status', table_columns.status);">

                <?php echo lang2('status') ?>

              </md-checkbox><br>

              <md-checkbox md-no-ink aria-label="column select" class="md-primary" ng-model="table_columns.members" ng-change="updateColumns('members', table_columns.members);">

                <?php echo lang2('members') ?>

              </md-checkbox><br>

              <md-checkbox md-no-ink aria-label="column select" class="md-primary" ng-model="table_columns.actions" ng-change="updateColumns('actions', table_columns.actions);">

                <?php echo lang2('actions') ?>

              </md-checkbox><br>

              <md-checkbox md-no-ink aria-label="column select" class="md-primary" ng-model="table_columns.value" ng-change="updateColumns('value', table_columns.value);">

                <?php echo lang2('project') . ' ' . lang2('value') ?>

              </md-checkbox>

            </md-contet>

          </md-menu-content>

        </md-menu>

        <md-button ng-click="toggleFilter()" class="md-icon-button" aria-label="Filter" ng-cloak>

          <md-tooltip md-direction="bottom"><?php echo lang2('filter') ?></md-tooltip>

          <md-icon><i class="ion-android-funnel text-muted"></i></md-icon>

        </md-button>

        <?php if (check_privilege('projects', 'create')) { ?>

          <md-button ng-click="Create()" class="md-icon-button" aria-label="New" ng-cloak>

            <md-tooltip md-direction="bottom"><?php echo lang2('create') ?></md-tooltip>

            <md-icon><i class="ion-android-add-circle text-success"></i></md-icon>

          </md-button>

        <?php } ?>

      </div>

    </md-toolbar>

    <div class="row projectRow">

      <div ng-show="projectLoader" layout-align="center center" class="text-center" id="circular_loader">

        <!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
        <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">


        <p style="font-size: 15px;margin-bottom: 5%;">

          <span>

            <?php echo lang2('please_wait') ?> <br>

            <small><strong><?php echo lang2('loading') . ' ' . lang2('projects') . '...' ?></strong></small>

          </span>

        </p>

      </div>

      <div ng-show="!projectLoader" id="ciuisprojectcard" style="padding-left: 15px;padding-right: 15px;" ng-cloak>

        <md-table-container ng-show="showList==true" class="bg-white">

          <table md-table md-progress="promise">

            <thead md-head md-order="projects_list.order">

              <tr md-row>

                <th md-column><span>#</span></th>

                <th ng-show="table_columns.name" md-column md-order-by="name"><span><?php echo lang2('project'); ?></span></th>
                <!--
                <th ng-show="table_columns.customer" md-column md-order-by="customer"><span><?php echo lang2('customer'); ?></span></th>
        -->
                <th ng-show="table_columns.startdate" md-column md-order-by="startdate"><span><?php echo lang2('startdate'); ?></span></th>

                <th ng-show="table_columns.deadline" md-column md-order-by="leftdays"><span><?php echo lang2('deadline'); ?></span></th>

                <th ng-show="table_columns.value" md-column><span><?php echo lang2('value'); ?></span></th>

                <th ng-show="table_columns.status" md-column md-order-by="progress"><span><?php echo lang2('status'); ?></span></th>

                <th ng-show="table_columns.members" md-column><span><?php echo lang2('members'); ?></span></th>

                <th ng-show="table_columns.actions" md-column style="text-align: right !important;"><span><?php echo lang2('actions'); ?></span></th>

              </tr>

            </thead>

            <tbody md-body>

              <tr class="select_row" md-row ng-repeat="project in projects | orderBy: projects_list.order | limitTo: projects_list.limit : (projects_list.page -1) * projects_list.limit | filter: project_search | filter: FilteredData">

                <td md-cell>

                  <strong>

                    <a class="link" ng-href="<?php echo base_url('/projects/project/') ?>{{project.id}}"> <span ng-bind="project.project_number"></span></a>

                  </strong>

                </td>

                <td ng-show="table_columns.name" md-cell class="cursor" ng-click="goToLink('projects/project/'+project.id)">

                  <strong><span class="link" ng-bind="project.name"></span></strong>
                  <!--
                </td>
                 <td md-cell ng-show="table_columns.customer">
                  <strong ng-bind="project.customer"></strong><br>
                  <small ng-bind="project.customeremail"></small>
                </td>
        -->
                <td md-cell ng-show="table_columns.startdate">

                  <span ng-bind="project.startdate"></span>

                </td>

                <td md-cell ng-show="table_columns.deadline">

                  <span ng-bind="project.leftdays"></span>

                </td>
                <!--
                <td md-cell ng-show="table_columns.value">
                  <span ng-bind-html="project.value | currencyFormat:cur_code:null:true:cur_lct"></span>
                </td>
        -->
                <td md-cell ng-show="table_columns.status">

                  <div class="progress-widget">

                    <div class="progress-data text-left"><span ng-hide="project.status_class == 'cancelled'" class="progress-value" ng-bind="project.progress+'%'"></span> <span class="name" ng-bind="project.status"></span> </div>

                    <div ng-hide="project.status_class == 'cancelled'" class="progress" style="height: 7px">

                      <div ng-hide="project.progress == 100" style="width: {{project.progress}}%;" class="progress-bar progress-bar-primary"></div>

                      <div ng-show="project.progress == 100" style="width: {{project.progress}}%;" class="progress-bar progress-bar-success"></div>

                    </div>

                  </div>

                </td>

                <td md-cell ng-show="table_columns.members">

                  <div class="bottom-right text-right">

                    <ul class="more-avatar">

                      <li ng-repeat="member in project.members" data-toggle="tooltip" data-placement="left" data-container="body" title="" data-original-title="{{member.staffname}}">

                        <md-tooltip md-direction="top">{{member.staffname}}</md-tooltip>

                        <div style=" background: lightgray url({{UPIMGURL}}{{member.staffavatar}}) no-repeat center / cover;"></div>

                      </li>

                      <div class="assigned-more-pro hidden"><i class="ion-plus-round"></i>2</div>

                    </ul>

                  </div>

                </td>

                <td md-cell ng-show="table_columns.actions">

                  <?php if (check_privilege('projects', 'edit')) { ?>

                    <div class="pull-right md-pr-10" ng-hide="project.status_id == '4' || project.status_id == '5'">

                      <i class="ciuis-project-badge pull-right ion-checkmark-circled text-success" ng-click="markasComplete(project.id)"></i>

                      <md-tooltip md-direction="top"><?php echo lang2('markasprojectcomplete') ?></md-tooltip>

                    </div>

                  <?php } ?>

                  <div class="pull-right md-pr-10" ng-show="project.pinned == 0">

                    <span>

                      <i ng-click='CheckPinned($index)' class="ciuis-project-badge pull-right ion-pin"></i>

                      <md-tooltip md-direction="top"><?php echo lang2('mark_as_pinned') ?></md-tooltip>

                    </span>

                    <img data-toggle="tooltip" data-placement="left" data-container="body" title="" data-original-title="{{project.status}}" class="pull-right md-mr-5" height="32" ng-src="{{IMAGESURL}}{{project.status_icon}}">

                  </div>

                  <?php if (check_privilege('projects', 'create')) { ?>

                    <div class="pull-right md-pr-10" ng-show="project.template == 1 || project.template == 'true'">

                      <i class="ciuis-project-badge pull-right ion-ios-copy" ng-click="copyProjectDialog(project.id)"></i>

                      <md-tooltip md-direction="top"><?php echo lang2('create_new_template_project') ?></md-tooltip>

                    </div>

                  <?php } ?>

                </td>

              </tr>

            </tbody>

          </table>

        </md-table-container>

        <md-table-pagination ng-show="showList==true" md-limit="projects_list.limit" md-limit-options="limitOptions" md-page="projects_list.page" md-total="{{projects.length}}"></md-table-pagination>

        <div ng-show="showGrid==true" ng-repeat="project in projects | filter: FilteredData | filter:search | pagination : currentPage*itemsPerPage | limitTo: 6" class="col-md-4 {{project.status_class}}" style="padding-left: 0px;padding-right: 10px;">

          <div id="project-card" class="ciuis-project-card">

            <div class="ciuis-project-content">

              <div class="ciuis-content-header">

                <a href="<?php echo base_url('/projects/project/') ?>{{project.id}}">

                  <div class="pull-left">

                    <p ng-attr-title="{{project.name}}" class="md-m-0" style="font-size: 14px;font-weight: 900;margin: unset;">

                      <span class="blur5" ng-bind="project.project_number"></span>

                      {{ project.name | limitTo: 28 }}{{project.name.length > 30 ? '...' : ''}}

                    </p>

                    <small ng-show="project.template == 1" ng-attr-title="<?php echo lang2('template') . ' ' . lang2('project') ?>"><?php echo lang2('template') . ' ' . lang2('project') ?></small>
                    <!--
                  <small ng-show="project.template == 0" ng-attr-title="{{project.customer}}">{{ project.customer | limitTo: 28 }}{{project.customer.length > 30 ? '...' : ''}}</small>
                  -->
                  </div>

                </a>

                <?php if (check_privilege('projects', 'edit')) { ?>

                  <div class="pull-right md-pr-10" ng-hide="project.status_id == '4' || project.status_id == '5'">

                    <i class="ciuis-project-badge pull-right ion-checkmark-circled text-success" ng-click="markasComplete(project.id)"></i>

                    <md-tooltip md-direction="top"><?php echo lang2('markasprojectcomplete') ?></md-tooltip>

                  </div>

                  <div class="pull-right md-pr-10" ng-show="project.template == 1 || project.template == 'true'">

                    <i class="ciuis-project-badge pull-right ion-ios-copy" ng-click="copyProjectDialog(project.id)"></i>

                    <md-tooltip md-direction="top"><?php echo lang2('create_new_template_project') ?></md-tooltip>

                  </div>

                  <div class="pull-right md-pr-10">

                    <span>

                      <i ng-click='CheckPinned($index)' class="ciuis-project-badge pull-right ion-pin"></i>

                      <md-tooltip md-direction="top"><?php echo lang2('mark_as_pinned') ?></md-tooltip>

                    </span>



                    <img data-toggle="tooltip" data-placement="left" data-container="body" title="" data-original-title="{{project.status}}" class="pull-right md-mr-5" height="32" ng-src="{{IMAGESURL}}{{project.status_icon}}">

                  </div>

                <?php } ?>

              </div>

              <div class="ciuis-project-dates">

                <div class="ciuis-project-start text-uppercase"><strong><?php echo lang2('start'); ?></strong><b ng-bind="project.startdate"></b></div>

                <div class="ciuis-project-end text-uppercase"><strong><?php echo lang2('deadline'); ?></strong><b ng-bind="project.leftdays"></b></div>

              </div>

              <div class="ciuis-project-stat col-md-12">

                <div class="col-md-6 bottom-left">

                  <div class="progress-widget">

                    <div class="progress-data text-left"><span ng-hide="project.status_class == 'cancelled'" class="progress-value" ng-bind="project.progress+'%'"></span> <span class="name" ng-bind="project.status"></span> </div>

                    <div ng-hide="project.status_class == 'cancelled'" class="progress" style="height: 7px">

                      <div ng-hide="project.progress == 100" style="width: {{project.progress}}%;" class="progress-bar progress-bar-primary"></div>

                      <div ng-show="project.progress == 100" style="width: {{project.progress}}%;" class="progress-bar progress-bar-success"></div>

                    </div>

                  </div>

                </div>

                <div class="col-md-6 md-p-0 bottom-right text-right">

                  <ul class="more-avatar">

                    <li ng-repeat="member in project.members" data-toggle="tooltip" data-placement="left" data-container="body" title="" data-original-title="{{member.staffname}}">

                      <md-tooltip md-direction="top">{{member.staffname}}</md-tooltip>

                      <div style=" background: lightgray url({{UPIMGURL}}{{member.staffavatar}}) no-repeat center / cover;"></div>

                    </li>

                    <div class="assigned-more-pro hidden"><i class="ion-plus-round"></i>2</div>

                  </ul>

                </div>

              </div>

            </div>

          </div>

        </div>

      </div>

      <md-content ng-show="!projects.length && !projectLoader" class="md-padding no-item-data" ng-cloak><?php echo lang2('notdata') ?></md-content>

    </div>

    <div ng-show="showGrid==true" ng-show="projects.length > 6 && !projectLoader" ng-cloak>

      <div class="pagination-div">

        <ul class="pagination">

          <li ng-class="DisablePrevPage()"> <a href ng-click="prevPage()"><i class="ion-ios-arrow-back"></i></a> </li>

          <li ng-repeat="n in range()" ng-class="{active: n == currentPage}" ng-click="setPage(n)"> <a href="#" ng-bind="n+1"></a> </li>

          <li ng-class="DisableNextPage()"> <a href ng-click="nextPage()"><i class="ion-ios-arrow-right"></i></a> </li>

        </ul>

      </div>

    </div>

  </div>

  <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-3 md-pl-0 bg-white">

    <div class="projects-graph">

      <div class="col-md-12" style="padding: 0px;">

        <div class="panel-default">

          <div class="panel-heading panel-heading-divider xs-pb-15 text-bold" style="margin: 0px;"><?php echo lang2('project') . ' ' . lang2('statuses'); ?></div>

          <div class="panel-body" style="padding: 0px;">

            <div class="project-stats-body pull-left">

              <div class="project-progress-data"> <span class="project-progress-value pull-right" ng-bind="stats.not_started_percent+'%'"></span> <span class="project-name"><?php echo lang2('notstarted'); ?></span> </div>

              <div class="progress" style="height: 5px">

                <div style="width: {{stats.not_started_percent}}%;" class="progress-bar progress-bar-success"></div>

              </div>

            </div>

            <div class="project-stats-body pull-left">

              <div class="project-progress-data"> <span class="project-progress-value pull-right" ng-bind="stats.started_percent+'%'"></span> <span class="project-name"><?php echo lang2('started'); ?></span> </div>

              <div class="progress" style="height: 5px">

                <div style="width: {{stats.started_percent}}%;" class="progress-bar progress-bar-success"></div>

              </div>

            </div>

            <div class="project-stats-body pull-left">

              <div class="project-progress-data"> <span class="project-progress-value pull-right" ng-bind="stats.percentage_percent+'%'"></span> <span class="project-name"><?php echo lang2('percentage'); ?></span> </div>

              <div class="progress" style="height: 5px">

                <div style="width: {{stats.percentage_percent}}%;" class="progress-bar progress-bar-success"></div>

              </div>

            </div>

            <div class="project-stats-body pull-left">

              <div class="project-progress-data"> <span class="project-progress-value pull-right" ng-bind="stats.cancelled_percent+'%'"></span> <span class="project-name"><?php echo lang2('cancelled'); ?></span> </div>

              <div class="progress" style="height: 5px">

                <div style="width: {{stats.cancelled_percent}}%;" class="progress-bar progress-bar-success"></div>

              </div>

            </div>

            <div class="project-stats-body pull-left">

              <div class="project-progress-data"> <span class="project-progress-value pull-right" ng-bind="stats.complete_percent+'%'"></span> <span class="project-name"><?php echo lang2('complete'); ?></span> </div>

              <div class="progress" style="height: 5px">

                <div style="width: {{stats.complete_percent}}%;" class="progress-bar progress-bar-success"></div>

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

    <div class="col-md-12 pinnedprojects bg-white">

      <div class="panel-default bg-white">

        <div class="pinned-projects-header bg-white"> <span><i class="ion-pin"></i> <?php echo lang2('pinnedprojects'); ?></span> <span class="pull-right hide-pinned-projects"><a data-toggle="collapse" data-parent="#pinned-projects" href="#pinned-projects"><i class="icon mdi ion-minus-circled"></i></a></span> </div>

        <div id="pinned-projects" class="panel-collapse collapse in" ng-cloak>

          <div class="pinned-projects">

            <div ng-repeat="project_pinned in pinnedprojects | filter: { pinned: '1' }" class="pinned-project-widget">

              <div class="pinned-project-body pull-left">

                <div class="project-progress-data"> <span class="project-progress-value pull-right" ng-bind="project_pinned.progress+'%'"></span> <span class="project-name" ng-bind="project_pinned.name"></span> </div>

                <div class="progress" style="height: 5px">

                  <div style="width:{{project_pinned.progress}}%;" class="progress-bar progress-bar-info"></div>

                </div>

              </div>

              <?php if (check_privilege('projects', 'delete')) { ?>

                <a ng-click='UnPinned(project_pinned.id)' class="pinned-project-action pull-right"><i class="ion-close-round"></i>
                  <md-tooltip md-direction="top"><?php echo lang2('remove') ?></md-tooltip>
                </a>

              <?php } ?>

              <a href="<?php echo base_url('projects/project/') ?>{{project_pinned.id}}" class="pinned-project-action pull-right"><i class="ion-android-open"></i>
                <md-tooltip md-direction="top"><?php echo lang2('go_to_project') ?></md-tooltip>
              </a>
            </div>

          </div>

        </div>

      </div>

    </div>

  </div>

  <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Create" ng-cloak style="width: 450px;">

    <md-toolbar class="toolbar-white">

      <div class="md-toolbar-tools">

        <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>

        <h2 flex md-truncate><?php echo lang2('create') ?></h2>

        <md-switch ng-model="project.template" aria-label="Type">

          <md-tooltip md-direction="bottom"><?php echo lang2('save_as_template'); ?></md-tooltip>

          <strong class="text-muted"><?php echo lang2('template'); ?> <i class="ion-information-circled"></i></strong>

        </md-switch>

      </div>

    </md-toolbar>

    <md-content>

      <md-content layout-padding>

        <md-input-container class="md-block">

          <label><?php echo lang2('name') ?></label>

          <input required type="text" ng-model="project.name" class="form-control" id="title" placeholder="<?php echo lang2('name'); ?>" />

        </md-input-container>

        <!--
        <md-input-container class="md-block" flex-gt-xs ng-hide="project.template">
          <md-select required placeholder="<?php echo lang2('choisecustomer'); ?>" ng-model="project.customer" name="customer" style="min-width: 200px;" data-md-container-class="selectdemoSelectHeader">
            <md-select-header class="demo-select-header">
              <label style="display: none;width: 450px;"><?php echo lang2('search') . ' ' . lang2('customer') ?></label>
              <input ng-submit="search_customers(search_input)" ng-model="search_input" type="text" placeholder="<?php echo lang2('search') . ' ' . lang2('customers') ?>" class="demo-header-searchbox md-text" ng-keyup="search_customers(search_input)">
            </md-select-header>
            <md-optgroup label="customers">
              <md-option ng-value="customer.id" ng-repeat="customer in all_customers">
                <span class="blur" ng-bind="customer.customer_number"></span>
                <strong ng-bind="customer.name"></strong><br>
                <span class="blur">(<small ng-bind="customer.email"></small>)</span>
              </md-option>
            </md-optgroup>
          </md-select>
        </md-input-container>
              -->

        <md-input-container>

          <label><?php echo lang2('startdate') ?></label>

          <md-datepicker name="start" ng-model="project.start" md-open-on-focus></md-datepicker>

        </md-input-container>

        <md-input-container>

          <label><?php echo lang2('deadline') ?></label>

          <md-datepicker md-min-date="project.start" name="deadline" ng-model="project.deadline" md-open-on-focus></md-datepicker>

        </md-input-container>
        <!--
        <md-input-container class="md-block">
          <label><?php echo lang2('projectcost') ?></label>
          <input type="number" required name="value" min="0" ng-model="project.value" ng-init="project.value=0" placeholder="<?php echo lang2('projectcost') ?>" class="form-control">
        </md-input-container>

        <md-input-container class="md-block">
          <label><?php echo $appconfig['tax_label'] ?></label>
          <input type="number" required name="tax" min="0" ng-model="project.tax" ng-init="project.tax=0" placeholder="<?php echo lang2('tax') ?>" class="form-control">
        </md-input-container>
        -->
        <md-input-container class="md-block">

          <label><?php echo lang2('description') ?></label><br>

          <textarea required name="description" ng-model="project.description" placeholder="<?php echo lang2('typeSomething'); ?>" class="form-control"></textarea>

        </md-input-container>

      </md-content>

      <custom-fields-vertical></custom-fields-vertical>

      <md-content layout-padding>

        <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>

          <md-button ng-click="CreateNew()" class="md-raised md-primary btn-report block-button" ng-disabled="saving == true">

            <span ng-hide="saving == true"><?php echo lang2('create'); ?></span>

            <md-progress-circular class="white" ng-show="saving == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

          </md-button>

          <br /><br /><br /><br />

        </section>

      </md-content>

    </md-content>

  </md-sidenav>

  <md-sidenav class="md-sidenav-right md-whiteframe-4dp projects-filter" md-component-id="ContentFilter" ng-cloak style="width: 450px;">

    <md-toolbar class="md-theme-light" style="background:#262626">

      <div class="md-toolbar-tools">

        <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>

        <md-truncate><?php echo lang2('filter') ?></md-truncate>

      </div>

    </md-toolbar>

    <md-content layout-padding="">

      <div ng-repeat="(prop, ignoredValue) in projects[0]" ng-init="filter[prop]={}" ng-if="prop != 'id' && prop != 'name' && prop != 'leftdays' && prop != 'members' && prop != 'milestones' && prop != 'pinned' && prop != 'progress' && prop != 'project_id' && prop != 'startdate' && prop != 'status_icon' && prop != 'status_id' && prop != 'tax' && prop != 'template' && prop != 'value' && prop!='customer' && prop!='status' && prop!='status_class' && prop!='customer_id' && prop!='<?php echo lang2('filterbycustomer') ?>' && prop != 'project_number' && prop != 'customeremail'">

        <div class="filter col-md-12">

          <h4 class="text-muted text-uppercase"><strong>{{prop}}</strong></h4>

          <hr>

          <div class="labelContainer" ng-repeat="opt in getOptionsFor(prop)" ng-if="prop=='<?php echo lang2('filterbystatus') ?>'">

            <md-checkbox id="{{[opt]}}" ng-model="filter[prop][opt]" aria-label="{{opt}}"><span class="text-uppercase">{{opt}}</span></md-checkbox>

          </div>

        </div>

      </div>

    </md-content>

  </md-sidenav>

</div>

<script type="text/ng-template" id="copyProjectDialog.html">

  <md-dialog aria-label="Expense Detail">

      <md-toolbar class="toolbar-white">

        <div class="md-toolbar-tools">

          <h2><strong class=""><?php echo lang2('create_new_template_project') ?></strong></h2>

          <span flex></span>

          <md-button class="md-icon-button" ng-click="close()">

            <md-icon class="ion-close-round" aria-label="Close dialog" style="color:black"></md-icon>

          </md-button>

        </div>

      </md-toolbar>

      <md-dialog-content style="max-width:800px;max-height:810px;">

        <md-content class="bg-white">

          <md-list flex>

            <md-list-item>

              <div class="ciuis-custom-list-item-item col-md-12">

                <p><?php echo lang2('projectCopy') ?></p>

                <md-input-container class="md-block" flex-gt-xs>

                  <md-checkbox ng-model="copy.service" ng-value="true" ng-checked="true">

                    <?php echo lang2('copy_services') ?>

                  </md-checkbox><br>

                  <md-checkbox ng-model="copy.expenses" ng-value="true" ng-checked="false">

                    <?php echo lang2('copy_expenses') ?>

                  </md-checkbox><br>

                  <md-checkbox ng-model="copy.milestones" ng-value="true" ng-checked="copy.tasks">

                    <?php echo lang2('copy_milesstones') ?>

                  </md-checkbox><br>

                  <md-checkbox ng-model="copy.tasks" ng-value="true" ng-checked="copy.milestones">

                    <?php echo lang2('copy_tasks') ?>

                  </md-checkbox><br>

                  <md-checkbox ng-model="copy.peoples" ng-value="true" ng-checked="true">

                    <?php echo lang2('copy_project_peoples') ?>

                  </md-checkbox><br>

                  <md-checkbox ng-model="copy.files" ng-value="true" ng-checked="true">

                    <?php echo lang2('copy_uploaded_files') ?>

                  </md-checkbox><br>

                  <md-checkbox ng-model="copy.notes" ng-value="true" ng-checked="false">

                    <?php echo lang2('copy_project_notes') ?>

                  </md-checkbox>

                </md-input-container>

                <div class="row">

                  <div class="col-md-6 md-block">

                    <md-input-container class="md-block" flex-gt-xs>

                      <label><?php echo lang2('customer'); ?></label>

                      <md-select required placeholder="<?php echo lang2('choisecustomer'); ?>" ng-model="copy.customer" name="customer" style="min-width: 200px;">

                        <md-option ng-value="customer.id" ng-repeat="customer in all_customers">{{customer.name}}</md-option>

                      </md-select>

                    </md-input-container>

                  </div>

                  <div class="col-md-6 md-block">

                  </div>

                </div>

                <md-input-container>

                  <label><?php echo lang2('startdate') ?></label>

                  <md-datepicker name="start" ng-model="copy.start" md-open-on-focus></md-datepicker>

                </md-input-container>

                <md-input-container>

                  <label><?php echo lang2('deadline') ?></label>

                  <md-datepicker name="deadline" ng-model="copy.end" md-open-on-focus></md-datepicker>

                </md-input-container>

              </div>

            </md-list-item>

          </md-list>

        </md-content>

      </md-dialog-content>

      <md-dialog-actions>

        <span flex></span>

        <md-button ng-click="close()"><?php echo lang2('cancel') ?>!</md-button>

        <md-button ng-click="copyProjectConfirm()"><?php echo lang2('doIt') ?></md-button>

      </md-dialog-actions>

    </md-dialog>

  </script>

<script type="text/ng-template" id="processing.html">

  <md-dialog id="updating" style="box-shadow:none;padding:unset;min-width: 25%;">

      <md-dialog-content layout-padding layout-align="center center" aria-label="wait" style="text-align: center;">

        <md-progress-circular md-mode="indeterminate" md-diameter="40" style="margin-left: auto;margin-right: auto;"></md-progress-circular>

        <span style="font-size: 15px;"><strong><?php echo lang2('processing'); ?></strong></span>

        <div class="row">

          <div class="col-md-12">

            <p style="opacity: 0.7;"><br><?php echo lang2('update_note'); ?></p>

          </div>

        </div>

      </md-dialog-content>

    </md-dialog>

  </script>

<script type="text/javascript">
  var lang = {};

  lang.doIt = '<?php echo lang("doIt") ?>';

  lang.project_complete_note = '<?php echo lang("project_complete_note") ?>';

  lang.attention = '<?php echo lang("attention") ?>';

  lang.cancel = '<?php echo lang("cancel") ?>';
</script>

<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>


<script type="text/javascript" src="<?php echo base_url('assets/js/projects.js') ?>"></script>