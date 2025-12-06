<?php include_once( APPPATH . 'Views/inc/ciuis_data_table_header.php' ); ?>
<?php $appconfig = get_appconfig(); ?>
<div class="ciuis-body-content" ng-controller="Orders_Controller">
  <div class="main-content container-fluid col-xs-12 col-md-3 col-lg-3 hidden-xs">
    <div class="panel-heading"> <strong><?php echo lang2('ordersituation') ?></strong> <span class="panel-subtitle"><?php echo lang2('ordersituationsdesc') ?></span> </div>
    <div class="row" style="padding: 0px 20px 0px 20px;">
      <div class="col-md-6 col-xs-6 border-right">
        <div class="tasks-status-stat">
          <h3 class="text-bold ciuis-task-stat-title"> <span class="task-stat-number" ng-bind="(orders | filter:{status_id:'1'}).length"></span> <span class="task-stat-all" ng-bind="'/'+' '+orders.length+' '+'<?php echo lang2('order') ?>'"></span> </h3>
          <span class="ciuis-task-percent-bg"> <span class="ciuis-task-percent-fg" style="width: {{(orders | filter:{status_id:'1'}).length * 100 / orders.length }}%;"></span> </span>
        </div>
        <span class="text-uppercase" style="color:#989898"><?php echo lang2('draft')?></span>
      </div>
      <div class="col-md-6 col-xs-6 border-right">
        <div class="tasks-status-stat">
          <h3 class="text-bold ciuis-task-stat-title"> <span class="task-stat-number" ng-bind="(orders | filter:{status_id:'2'}).length"></span> <span class="task-stat-all" ng-bind="'/'+' '+orders.length+' '+'<?php echo lang2('order') ?>'"></span> </h3>
          <span class="ciuis-task-percent-bg"> <span class="ciuis-task-percent-fg" style="width: {{(orders | filter:{status_id:'2'}).length * 100 / orders.length }}%;"></span> </span>
        </div>
        <span class="text-uppercase" style="color:#989898"><?php echo lang2('sent')?></span>
      </div>
      <div class="col-md-6 col-xs-6 border-right">
        <div class="tasks-status-stat">
          <h3 class="text-bold ciuis-task-stat-title"> <span class="task-stat-number" ng-bind="(orders | filter:{status_id:'3'}).length"></span> <span class="task-stat-all" ng-bind="'/'+' '+orders.length+' '+'<?php echo lang2('order') ?>'"></span> </h3>
          <span class="ciuis-task-percent-bg"> <span class="ciuis-task-percent-fg" style="width: {{(orders | filter:{status_id:'3'}).length * 100 / orders.length }}%;"></span> </span>
        </div>
        <span class="text-uppercase" style="color:#989898"><?php echo lang2('open')?></span>
      </div>
      <div class="col-md-6 col-xs-6 border-right">
        <div class="tasks-status-stat">
          <h3 class="text-bold ciuis-task-stat-title"> <span class="task-stat-number" ng-bind="(orders | filter:{status_id:'4'}).length"></span> <span class="task-stat-all" ng-bind="'/'+' '+orders.length+' '+'<?php echo lang2('order') ?>'"></span> </h3>
          <span class="ciuis-task-percent-bg"> <span class="ciuis-task-percent-fg" style="width: {{(orders | filter:{status_id:'4'}).length * 100 / orders.length }}%;"></span> </span>
        </div>
        <span class="text-uppercase" style="color:#989898"><?php echo lang2('revised')?></span>
      </div>
      <div class="col-md-6 col-xs-6 border-right">
        <div class="tasks-status-stat">
          <h3 class="text-bold ciuis-task-stat-title"> <span class="task-stat-number" ng-bind="(orders | filter:{status_id:'5'}).length"></span> <span class="task-stat-all" ng-bind="'/'+' '+orders.length+' '+'<?php echo lang2('order') ?>'"></span> </h3>
          <span class="ciuis-task-percent-bg"> <span class="ciuis-task-percent-fg" style="width: {{(orders | filter:{status_id:'5'}).length * 100 / orders.length }}%;"></span> </span>
        </div>
        <span class="text-uppercase" style="color:#989898"><?php echo lang2('declined')?></span>
      </div>
      <div class="col-md-6 col-xs-6 border-right">
        <div class="tasks-status-stat">
          <h3 class="text-bold ciuis-task-stat-title"> <span class="task-stat-number" ng-bind="(orders | filter:{status_id:'6'}).length"></span> <span class="task-stat-all" ng-bind="'/'+' '+orders.length+' '+'<?php echo lang2('order') ?>'"></span> </h3>
          <span class="ciuis-task-percent-bg"> <span class="ciuis-task-percent-fg" style="width: {{(orders | filter:{status_id:'6'}).length * 100 / orders.length }}%;"></span> </span>
        </div>
        <span class="text-uppercase" style="color:#989898"><?php echo lang2('accepted')?></span>
      </div>
    </div>
  </div>
  <div class="main-content container-fluid col-xs-12 col-md-9 col-lg-9">
    <md-toolbar class="toolbar-white">
      <div class="md-toolbar-tools">
        <h2 flex md-truncate class="text-bold"><?php echo lang2('orders'); ?> <small>(<span ng-bind="orders.length"></span>)</small><br>
          <small flex md-truncate><?php echo lang2('organize_your_orders'); ?></small></h2>
        <div class="ciuis-external-search-in-table">
          <input ng-model="order_search" class="search-table-external" id="search" name="search" type="text" placeholder="<?php echo lang2('searchword')?>">
          <md-button class="md-icon-button" aria-label="Search" ng-cloak>
            <md-icon><i class="ion-search text-muted"></i></md-icon>
          </md-button>
        </div>
        <md-button ng-click="toggleFilter()" class="md-icon-button" aria-label="Filter" ng-cloak>
          <md-icon><i class="ion-android-funnel text-muted"></i></md-icon>
        </md-button>
        <?php if (check_privilege('orders', 'create')) { ?>
          <md-button ng-href="<?php echo base_url('orders/create') ?>" class="md-icon-button" aria-label="New" ng-cloak>
            <md-icon><i class="ion-android-add-circle text-success"></i></md-icon>
          </md-button>
        <?php } ?>
      </div>
    </md-toolbar>
    <md-content ng-show="!orderLoader" class="bg-white" ng-cloak>
      <md-table-container ng-show="orders.length > 0">
        <table md-table md-progress="promise">
          <thead md-head md-order="order_list.order">
            <tr md-row>
              <th md-column><span><?php echo lang2('order'); ?></span></th>
              <th md-column md-order-by="customer"><span><?php echo lang2('customer') . ' / ' . lang2('lead') ?></span>
              </th>
              <th md-column md-order-by="date"><span><?php echo lang2('date'); ?></span></th>
              <th md-column md-order-by="opentill"><span><?php echo lang2('opentill'); ?></span></th>
              <th md-column md-order-by="status"><span><?php echo lang2('status'); ?></span></th>
              <th md-column md-order-by="total"><span><?php echo lang2('amount'); ?></span></th>
            </tr>
          </thead>
          <tbody md-body>
            <tr class="select_row" md-row ng-repeat="order in orders | orderBy: order_list.order | limitTo: order_list.limit : (order_list.page -1) * order_list.limit | filter: order_search | filter: FilteredData" class="cursor" ng-click="goToLink('orders/order/'+order.id)">
              <td md-cell>
                <strong>
                  <a class="link" ng-href="<?php echo base_url('orders/order/') ?>{{order.id}}"> <span ng-bind="order.longid"></span></a>
                </strong><br>
                <small ng-bind="order.subject"></small>
              </td>
              <td md-cell>
                <strong><span ng-bind="order.customer"></span></strong><br>
                <span class="blur" ng-bind="order.customer_email"></span>
              </td>
              <td md-cell>
                <strong><span class="badge" ng-bind="order.date"></span></strong>
              </td>
              <td md-cell>
                <strong><span class="badge" ng-bind="order.opentill"></span></strong>
              </td>
              <td md-cell>
                <strong class="text-uppercase text-{{order.status}}" ng-bind="order.status"></strong>
              </td>
              <td md-cell>
                <strong ng-bind-html="order.total | currencyFormat:cur_code:null:true:cur_lct"></strong>
              </td>
            </tr>
          </tbody>
        </table>
      </md-table-container>
      <md-table-pagination ng-show="orders.length > 0" md-limit="order_list.limit" md-limit-options="limitOptions" md-page="order_list.page" md-total="{{orders.length}}"></md-table-pagination>
      <md-content ng-show="!orders.length" class="md-padding no-item-data"><?php echo lang2('notdata') ?></md-content>
    </md-content>
  </div>
  <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="ContentFilter" ng-cloak style="width: 450px;">
    <md-toolbar class="md-theme-light" style="background:#262626">
      <div class="md-toolbar-tools">
        <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>
        <md-truncate><?php echo lang2('filter') ?></md-truncate>
      </div>
    </md-toolbar>
    <md-content layout-padding="">
      <div ng-repeat="(prop, ignoredValue) in orders[0]" ng-init="filter[prop]={}" ng-if="prop != 'id' && prop != 'assigned' && prop != 'subject' && prop != 'customer' && prop != 'date' && prop != 'opentill' && prop != 'status' && prop != 'staff' && prop != 'staffavatar' && prop != 'total' && prop != 'class' && prop != 'relation' && prop != 'status_id' && prop != 'prefix' && prop != 'longid' && prop != 'relation_type' && prop != 'customer_email'">
        <div class="filter col-md-12">
          <h4 class="text-muted text-uppercase"><strong>{{prop}}</strong></h4>
          <hr>
          <div class="labelContainer" ng-repeat="opt in getOptionsFor(prop)" ng-if="prop!='<?php echo lang2('filterbycustomer') ?>' && prop!='<?php echo lang2('filterbyassigned') ?>'">
            <md-checkbox id="{{[opt]}}" ng-model="filter[prop][opt]" aria-label="{{opt}}"><span class="text-uppercase">{{opt}}</span></md-checkbox>
          </div>
          <div ng-if="prop=='<?php echo lang2('filterbycustomer') ?>'">
            <md-select aria-label="Filter" ng-model="filter_select" ng-init="filter_select='all'" ng-change="updateDropdown(prop)">
              <md-option value="all"><?php echo lang2('all') ?></md-option>
              <md-option ng-repeat="opt in getOptionsFor(prop) | orderBy:'':true" value="{{opt}}">{{opt}}</md-option>
            </md-select>
          </div>
          <div ng-if="prop=='<?php echo lang2('filterbyassigned') ?>'">
            <md-select aria-label="Filter" ng-model="filter_select" ng-init="filter_select='all'" ng-change="updateDropdown(prop)">
              <md-option value="all"><?php echo lang2('all') ?></md-option>
              <md-option ng-repeat="opt in getOptionsFor(prop) | orderBy:'':true" value="{{opt}}">{{opt}}</md-option>
            </md-select>
          </div>
        </div>
      </div>
    </md-content>
  </md-sidenav>
</div>
<?php include_once( APPPATH . 'Views/inc/other_footer.php' ); ?>
<script src="<?php echo base_url('assets/js/orders.js'); ?>"></script>