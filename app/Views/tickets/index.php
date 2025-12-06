<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>

<?php $appconfig = get_appconfig(); ?>

<style>
  .topRow {

    margin-bottom: 30px;

  }

  .on-drag-enter {}

  .on-drag-hover:before {

    display: block;

    color: white;

    font-size: x-large;

    font-weight: 800;

  }
</style>

<div class="ciuis-body-content" ng-controller="Tickets_Controller">

  <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">

    <div class="main-content container-fluid col-xs-12 col-md-3 col-lg-3 md-pl-0">

      <div class="panel-default panel-table borderten lead-manager-head">

        <md-content style="border-bottom: 2px dashed #e8e8e8; padding-bottom: 20px;" layout-padding>

          <div class="col-md-3 col-xs-6 border-right text-uppercase">

            <div class="tasks-status-stat">

              <h4 class="text-bold ciuis-task-stat-title">

                <span class="task-stat-number ng-binding" ng-bind="(tickets | filter:{status_id:'1'}).length"></span>

                <span class="task-stat-all ng-binding" ng-bind="'/'+' '+tickets.length+' '+'<?php echo lang2('ticket') ?>'"></span>

              </h4>

              <span class="ciuis-task-percent-bg">

                <span class="ciuis-task-percent-fg" style="width: {{(tickets | filter:{status_id:'1'}).length * 100 / tickets.length }}%;"></span>

              </span>

            </div>

            <span style="color:#989898"><?php echo lang2('open') ?></span>

          </div>

          <div class="col-md-3 col-xs-6 border-right text-uppercase">

            <div class="tasks-status-stat">

              <h4 class="text-bold ciuis-task-stat-title">

                <span class="task-stat-number ng-binding" ng-bind="(tickets | filter:{status_id:'2'}).length"></span>

                <span class="task-stat-all ng-binding" ng-bind="'/'+' '+tickets.length+' '+'<?php echo lang2('ticket') ?>'"></span>

              </h4>

              <span class="ciuis-task-percent-bg">

                <span class="ciuis-task-percent-fg" style="width: {{(tickets | filter:{status_id:'2'}).length * 100 / tickets.length }}%;"></span>

              </span>

            </div>

            <span style="color:#989898"><?php echo lang2('inprogress') ?></span>

          </div>

          <div class="col-md-3 col-xs-6 border-right text-uppercase">

            <div class="tasks-status-stat">

              <h4 class="text-bold ciuis-task-stat-title">

                <span class="task-stat-number ng-binding" ng-bind="(tickets | filter:{status_id:'3'}).length"></span>

                <span class="task-stat-all ng-binding" ng-bind="'/'+' '+tickets.length+' '+'<?php echo lang2('ticket') ?>'"></span>

              </h4>

              <span class="ciuis-task-percent-bg">

                <span class="ciuis-task-percent-fg" style="width: {{(tickets | filter:{status_id:'3'}).length * 100 / tickets.length }}%;"></span>

              </span>

            </div>

            <span style="color:#989898"><?php echo lang2('answered') ?></span>

          </div>

          <div class="col-md-3 col-xs-6 border-right text-uppercase">

            <div class="tasks-status-stat">

              <h4 class="text-bold ciuis-task-stat-title">

                <span class="task-stat-number ng-binding" ng-bind="(tickets | filter:{status_id:'4'}).length"></span>

                <span class="task-stat-all ng-binding" ng-bind="'/'+' '+tickets.length+' '+'<?php echo lang2('ticket') ?>'"></span>

              </h4>

              <span class="ciuis-task-percent-bg">

                <span class="ciuis-task-percent-fg" style="width: {{(tickets | filter:{status_id:'4'}).length * 100 / tickets.length }}%;"></span>

              </span>

            </div>

            <span style="color:#989898"><?php echo lang2('closed') ?></span>

          </div>

        </md-content>

        <div class="ticket-contoller-left">

          <div id="tickets-left-column text-left">

            <div class="col-md-12 ticket-row-left text-left">

              <div class="tickets-vertical-menu">

                <a ng-click="TicketsFilter = NULL" class="highlight text-uppercase"><i class="fa fa-inbox fa-lg" aria-hidden="true"></i> <?php echo lang2('alltickets') ?> <span class="ticket-num" ng-bind="tickets.length"></span></a>

                <a ng-click="TicketsFilter = {status_id: 1}" class="side-tickets-menu-item"><?php echo lang2('open') ?> <span class="ticket-num" ng-bind="(tickets | filter:{status_id:'1'}).length"></span></a>

                <a ng-click="TicketsFilter = {status_id: 2}" class="side-tickets-menu-item"><?php echo lang2('inprogress') ?> <span class="ticket-num" ng-bind="(tickets | filter:{status_id:'2'}).length"></span></a>

                <a ng-click="TicketsFilter = {status_id: 3}" class="side-tickets-menu-item"><?php echo lang2('answered') ?> <span class="ticket-num" ng-bind="(tickets | filter:{status_id:'3'}).length"></span></a>

                <a ng-click="TicketsFilter = {status_id: 4}" class="side-tickets-menu-item"><?php echo lang2('closed') ?> <span class="ticket-num" ng-bind="(tickets | filter:{status_id:'4'}).length"></span></a>

                <h5 href="#" class="menu-icon active text-uppercase text-muted"><i class="fa fa-file-text fa-lg" aria-hidden="true"></i><?php echo lang2('filterbypriority') ?></h5>

                <a ng-click="TicketsFilter = {priority_id: 1}" class="side-tickets-menu-item"><?php echo lang2('low') ?> <span class="ticket-num" ng-bind="(tickets | filter:{priority_id:'1'}).length"></span></a>

                <a ng-click="TicketsFilter = {priority_id: 2}" class="side-tickets-menu-item"><?php echo lang2('medium') ?> <span class="ticket-num" ng-bind="(tickets | filter:{priority_id:'2'}).length"></span></a>

                <a ng-click="TicketsFilter = {priority_id: 3}" class="side-tickets-menu-item"><?php echo lang2('high') ?> <span class="ticket-num" ng-bind="(tickets | filter:{priority_id:'3'}).length"></span></a>

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

    <div class="main-content container-fluid col-xs-12 col-md-9 col-lg-9 md-p-0 lead-table">

      <md-toolbar class="toolbar-white">

        <div class="md-toolbar-tools">

          <h2 flex md-truncate class="text-bold"><?php echo lang2('tickets'); ?> <small>(<span ng-bind="tickets.length"></span>)</small><br>

            <small flex md-truncate><?php echo lang2('tracktickets'); ?></small>

          </h2>

          <md-button ng-if="!KanbanBoard" ng-click="ShowKanban()" class="md-icon-button" aria-label="Show Kanban" ng-cloak>

            <md-tooltip md-direction="bottom"><?php echo lang2('showkanban'); ?></md-tooltip>

            <md-icon><i class="mdi mdi-view-week text-muted"></i></md-icon>

          </md-button>

          <md-button ng-if="KanbanBoard" ng-click="HideKanban()" class="md-icon-button" aria-label="Show List" ng-cloak>

            <md-tooltip md-direction="bottom"><?php echo lang2('showlist'); ?></md-tooltip>

            <md-icon><i class="mdi mdi-view-list text-muted"></i></md-icon>

          </md-button>

          <?php if (check_privilege('tickets', 'create')) { ?>

            <md-button ng-click="Create()" class="md-icon-button" aria-label="New" ng-cloak>

              <md-tooltip md-direction="bottom"><?php echo lang2('newticket') ?></md-tooltip>

              <md-icon><i class="ion-plus-round text-muted"></i></md-icon>

            </md-button>

          <?php } ?>

        </div>

      </md-toolbar>

      <div ng-show="ticketsLoader" layout-align="center center" class="text-center" id="circular_loader">

        <!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
        <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">


        <p style="font-size: 15px;margin-bottom: 5%;">

          <span>

            <?php echo lang2('please_wait') ?> <br>

            <small><strong><?php echo lang2('loading') . ' ' . lang2('tickets') . '...' ?></strong></small>

          </span>

        </p>

      </div>

      <md-content ng-show="!ticketsLoader" ng-if="KanbanBoard" class="" style="padding: 0px" ng-cloak>

        <md-content class="col-md-4" style="padding: 0px; border: 1px solid #e4e4e4; height: 500px;">

          <md-list flex style="padding-top: unset;">

            <md-subheader class="md-no-sticky bg-white"><md-icon class="ion-android-alert text-danger"></md-icon><strong><?php echo lang2('high') ?></strong></md-subheader>

            <md-divider></md-divider>

            <md-list-item class="md-2-line" ng-repeat="ticket in tickets | filter:TicketsFilter | filter:search | filter: { priority_id: '3' }" ng-click="GoTicket(ticket.id)">

              <div class="md-list-item-text">

                <h3> {{ ticket.subject }} </h3>

                <p> {{ ticket.contactname }} </p>

                <p ng-show="ticket.lastreply != NULL"><small><strong><?php echo lang2('lastreply') ?></strong> <small ng-bind="ticket.lastreply | date : 'MMM d, y h:mm:ss a'"></small></small></p>

                <p ng-show="ticket.lastreply == NULL"><small><strong><?php echo lang2('lastreply') ?></strong> <small><?php echo lang2('n_a') ?></small></small></p>

              </div>

              <md-button ng-hide="ticket.status_id != 4" class="md-secondary md-icon-button" aria-label="Closed">

                <md-tooltip md-direction="bottom"><?php echo lang2('closed') ?></md-tooltip>

                <md-icon class="ion-happy text-success"></md-icon>

              </md-button>

              <md-button ng-hide="ticket.status_id != 3" class="md-secondary md-icon-button" aria-label="Closed">

                <md-tooltip md-direction="bottom"><?php echo lang2('answered') ?></md-tooltip>

                <md-icon class="mdi mdi-mail-reply-all text-muted"></md-icon>

              </md-button>

              <md-button ng-hide="ticket.status_id != 2" class="md-secondary md-icon-button" aria-label="Closed">

                <md-tooltip md-direction="bottom"><?php echo lang2('inprogress') ?></md-tooltip>

                <md-icon class="mdi mdi-hourglass-alt text-muted"></md-icon>

              </md-button>

              <md-button ng-hide="ticket.status_id != 1" class="md-secondary md-icon-button" aria-label="Closed">

                <md-tooltip md-direction="bottom"><?php echo lang2('open') ?></md-tooltip>

                <md-icon class="mdi mdi-flash text-danger"></md-icon>

              </md-button>

              <md-divider></md-divider>

            </md-list-item>

          </md-list>

        </md-content>

        <md-content class="col-md-4" style="padding: 0px; border: 1px solid #e4e4e4; height: 500px;">

          <md-list flex style="padding-top: unset;">

            <md-subheader class="md-no-sticky bg-white"><md-icon class="ion-android-alert text-warning"></md-icon><strong><?php echo lang2('medium') ?></strong></md-subheader>

            <md-divider></md-divider>

            <md-list-item class="md-2-line" ng-repeat="ticket in tickets | filter:TicketsFilter | filter:search | filter: { priority_id: '2' }" ng-click="GoTicket(ticket.id)">

              <div class="md-list-item-text">

                <h3> {{ ticket.subject }} </h3>

                <p> {{ ticket.contactname }} </p>

                <p ng-show="ticket.lastreply != NULL"><small><strong><?php echo lang2('lastreply') ?></strong> <small ng-bind="ticket.lastreply | date : 'MMM d, y h:mm:ss a'"></small></small></p>

                <p ng-show="ticket.lastreply == NULL"><small><strong><?php echo lang2('lastreply') ?></strong> <small><?php echo lang2('n_a') ?></small></small></p>

              </div>

              <md-button ng-hide="ticket.status_id != 4" class="md-secondary md-icon-button" aria-label="Closed">

                <md-tooltip md-direction="bottom"><?php echo lang2('closed') ?></md-tooltip>

                <md-icon class="ion-happy text-success"></md-icon>

              </md-button>

              <md-button ng-hide="ticket.status_id != 3" class="md-secondary md-icon-button" aria-label="Closed">

                <md-tooltip md-direction="bottom"><?php echo lang2('answered') ?></md-tooltip>

                <md-icon class="mdi mdi-mail-reply-all text-muted"></md-icon>

              </md-button>

              <md-button ng-hide="ticket.status_id != 2" class="md-secondary md-icon-button" aria-label="Closed">

                <md-tooltip md-direction="bottom"><?php echo lang2('inprogress') ?></md-tooltip>

                <md-icon class="mdi mdi-hourglass-alt text-muted"></md-icon>

              </md-button>

              <md-button ng-hide="ticket.status_id != 1" class="md-secondary md-icon-button" aria-label="Closed">

                <md-tooltip md-direction="bottom"><?php echo lang2('open') ?></md-tooltip>

                <md-icon class="mdi mdi-flash text-danger"></md-icon>

              </md-button>

              <md-divider></md-divider>

            </md-list-item>

          </md-list>

        </md-content>

        <md-content class="col-md-4" style="padding: 0px; border: 1px solid #e4e4e4; height: 500px;">

          <md-list flex style="padding-top: unset;">

            <md-subheader class="md-no-sticky bg-white"><md-icon class="ion-android-alert text-success"></md-icon><strong><?php echo lang2('low') ?></strong></md-subheader>

            <md-divider></md-divider>

            <md-list-item class="md-2-line" ng-repeat="ticket in tickets | filter:TicketsFilter | filter:search | filter: { priority_id: '1' }" ng-click="GoTicket(ticket.id)">

              <div class="md-list-item-text">

                <h3> {{ ticket.subject }} </h3>

                <p> {{ ticket.contactname }} </p>

                <p ng-show="ticket.lastreply != NULL"><small><strong><?php echo lang2('lastreply') ?></strong> <small ng-bind="ticket.lastreply | date : 'MMM d, y h:mm:ss a'"></small></small></p>

                <p ng-show="ticket.lastreply == NULL"><small><strong><?php echo lang2('lastreply') ?></strong> <small><?php echo lang2('n_a') ?></small></small></p>

              </div>

              <md-button ng-hide="ticket.status_id != 4" class="md-secondary md-icon-button" aria-label="Closed">

                <md-tooltip md-direction="bottom"><?php echo lang2('closed') ?></md-tooltip>

                <md-icon class="ion-happy text-success"></md-icon>

              </md-button>

              <md-button ng-hide="ticket.status_id != 3" class="md-secondary md-icon-button" aria-label="Closed">

                <md-tooltip md-direction="bottom"><?php echo lang2('answered') ?></md-tooltip>

                <md-icon class="mdi mdi-mail-reply-all text-muted"></md-icon>

              </md-button>

              <md-button ng-hide="ticket.status_id != 2" class="md-secondary md-icon-button" aria-label="Closed">

                <md-tooltip md-direction="bottom"><?php echo lang2('inprogress') ?></md-tooltip>

                <md-icon class="mdi mdi-hourglass-alt text-muted"></md-icon>

              </md-button>

              <md-button ng-hide="ticket.status_id != 1" class="md-secondary md-icon-button" aria-label="Closed">

                <md-tooltip md-direction="bottom"><?php echo lang2('open') ?></md-tooltip>

                <md-icon class="mdi mdi-flash text-danger"></md-icon>

              </md-button>

              <md-divider></md-divider>

            </md-list-item>

          </md-list>

        </md-content>

      </md-content>

      <md-content ng-show="!ticketsLoader" ng-if="!KanbanBoard" class="md-pt-0 bg-white" ng-cloak>

        <md-table-container ng-show="tickets.length > 0">

          <table md-table md-progress="promise" ng-cloak>

            <thead md-head md-order="ticket_list.order">

              <tr md-row>

                <th md-column><span>#</span></th>

                <th md-column md-order-by="subject"><span><?php echo lang2('ticket'); ?></span></th>

                <th md-column md-order-by="contactname"><span><?php echo lang2('contact'); ?></span></th>

                <th md-column md-order-by="priority_id"><span><?php echo lang2('priority'); ?></span></th>

                <th md-column md-order-by="status_id"><span><?php echo lang2('status'); ?></span></th>

                <th md-column md-order-by="lastreply"><span><?php echo lang2('lastreply'); ?></span></th>

              </tr>

            </thead>

            <tbody md-body>

              <tr class="select_row" md-row ng-repeat="ticket in tickets | orderBy: ticket_list.order | limitTo: ticket_list.limit : (ticket_list.page -1) * ticket_list.limit | filter: TicketsFilter | filter:search | filter: ticket.priority" class="cursor" ng-click="goToLink('tickets/ticket/'+ticket.id)">

                <td md-cell>

                  <strong>

                    <a class="link" ng-href="<?php echo base_url('tickets/ticket/') ?>{{ticket.id}}"> <strong ng-bind="ticket.ticket_number"></strong></a>

                  </strong>

                </td>

                <td md-cell>

                  <strong ng-bind="ticket.subject"></strong><br>

                  <small ng-bind="ticket.message"></small>

                </td>

                <td md-cell>

                  <strong ng-bind="ticket.contactname"></strong><br>

                  <small ng-bind="ticket.contactemail" class="blur"></small>

                </td>

                <td md-cell>

                  <span ng-switch="ticket.priority_id">

                    <strong ng-switch-when="1"><?php echo lang2('low'); ?></strong>

                    <strong ng-switch-when="2"><?php echo lang2('medium'); ?></strong>

                    <strong ng-switch-when="3"><?php echo lang2('high'); ?></strong>

                  </span>

                </td>

                <td md-cell>

                  <span ng-switch="ticket.status_id">

                    <strong ng-switch-when="1"><?php echo lang2('open'); ?></strong>

                    <strong ng-switch-when="2"><?php echo lang2('inprogress'); ?></strong>

                    <strong ng-switch-when="3"><?php echo lang2('answered'); ?></strong>

                    <strong ng-switch-when="4"><?php echo lang2('closed'); ?></strong>

                  </span>

                </td>

                <td md-cell>

                  <span>

                    <span ng-show="ticket.lastreply == NULL" class="badge"><?php echo lang2('n_a') ?></span><span ng-show="ticket.lastreply != NULL" class="badge" ng-bind="ticket.lastreply | date : 'MMM d, y h:mm:ss a'"></span>

                  </span>

                </td>

              </tr>

            </tbody>

          </table>

        </md-table-container>

        <md-table-pagination ng-show="tickets.length > 0" md-limit="ticket_list.limit" md-limit-options="limitOptions" md-page="ticket_list.page" md-total="{{tickets.length}}"></md-table-pagination>

        <md-content ng-show="!tickets.length" class="md-padding no-item-data"><?php echo lang2('notdata') ?></md-content>

      </md-content>

    </div>

  </div>

  <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Create" ng-cloak style="width: 450px;">

    <md-toolbar class="toolbar-white">
      <div class="md-toolbar-tools">
        <md-button ng-click="close()" class="md-icon-button" aria-label="Close">
          <i class="ion-android-arrow-forward"></i>
        </md-button>
        <md-truncate><?php echo lang2('create') ?></md-truncate>
      </div>

    </md-toolbar>
    <md-content layout-padding="">
      <md-content layout-padding>
        <?php helper('form');echo form_open_multipart('tickets/create'); ?>
        <md-input-container class="md-block">
          <label><?php echo lang2('subject') ?></label>
          <input required type="text" ng-model="ticket.subject" name="subject" class="form-control">
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs>
          <label><?php echo lang2('customer'); ?></label>
          <md-select required placeholder="<?php echo lang2('choisecustomer'); ?>" ng-model="ticket.customer" name="customer">
            <md-option ng-value="customer" ng-repeat="customer in customers">{{customer.name}}</md-option>
          </md-select><br>

        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs>
          <label><?php echo lang2('contact'); ?></label>
          <md-select ng-model="ticket.contact" name="contact">
            <md-option ng-value="contact.id" ng-repeat="contact in ticket.customer.contacts">{{contact.name + ' ' + contact.surname}}</md-option>
          </md-select><br>
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs>
          <label><?php echo lang2('department'); ?></label>
          <md-select required ng-model="ticket.department" name="department">
            <md-option ng-value="department.id" ng-repeat="department in departments">{{department.name}}</md-option>
          </md-select><br>
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs>
          <label><?php echo lang2('priority'); ?></label>
          <md-select ng-init="priorities = [{id: 1,name: '<?php echo lang2('low'); ?>'}, {id: 2,name: '<?php echo lang2('medium'); ?>'}, {id: 3,name: '<?php echo lang2('high'); ?>'}];" required placeholder="<?php echo lang2('priority'); ?>" ng-model="ticket.priority" name="priority">
            <md-option ng-value="priority.id" ng-repeat="priority in priorities"><span class="text-uppercase">{{priority.name}}</span></md-option>
          </md-select><br>
        </md-input-container>

        <md-input-container class="md-block">
          <label><?php echo lang2('message') ?></label>
          <textarea required name="message" ng-model="ticket.message" placeholder="<?php echo lang2('typeSomething'); ?>" class="form-control"></textarea>
        </md-input-container>

        <div class="file-upload">
          <div class="file-select">
            <div class="file-select-button" id="fileName"><span class="mdi mdi-accounts-list-alt"></span> <?php echo lang2('attachment') ?></div>
            <div class="file-select-name" id="noFile"><?php echo lang2('nofile') ?></div>
            <input type="file" name="attachment" id="chooseFile" file-model="ticket.ticket_attachment">
          </div>
        </div>
        <br>

        <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>
          <md-button ng-click="createTicket()" class="md-raised md-primary btn-report block-button" ng-disabled="uploading == true">
            <span ng-hide="uploading == true"><?php echo lang2('create'); ?></span>
            <md-progress-circular class="white" ng-show="uploading == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>
          </md-button>
          <br /><br /><br /><br />
        </section>
        <?php echo form_close(); ?>
      </md-content>

    </md-content>

  </md-sidenav>





</div>

<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>
<script type="text/javascript" src="<?php echo base_url('assets/js/tickets.js') ?>"></script>