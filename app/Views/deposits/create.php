<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>

<?php $appconfig = get_appconfig(); ?>

<div class="ciuis-body-content" ng-controller="Deposits_Controller">

  <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-9">

    <div ng-show="depositsLoader" layout-align="center center" class="text-center" id="circular_loader">

      <!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
      <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">


      <p style="font-size: 15px;margin-bottom: 5%;">

        <span><?php echo lang2('loading') . ' ' . lang2('please_wait') . '....' ?> <br>

        </span>

      </p>

    </div>

    <md-toolbar ng-show="!depositsLoader" class="toolbar-white" ng-cloak>

      <div class="md-toolbar-tools">

        <md-button class="md-icon-button" aria-label="Invoice" ng-disabled="true">

          <md-icon><i class="ico-ciuis-expenses text-muted"></i></md-icon>

        </md-button>

        <h2 flex md-truncate ng-bind="newdeposit.title"><?php echo lang2('new') . ' ' . lang2('deposit') ?></h2>

        <md-switch ng-model="newdeposit.internal" aria-label="Internal">

          <md-tooltip md-direction="bottom"><?php echo lang2('mark_as_internal_deposit') ?></md-tooltip>

          <strong class="text-muted"><?php echo lang2('internal') ?></strong>

          <md-tooltip md-direction="bottom"><?php echo lang2('mark_as_internal_deposit') ?></md-tooltip>

        </md-switch>

        <md-switch ng-model="deposit_recurring" aria-label="Recurring"> <strong class="text-muted"><?php echo lang2('recurring') ?></strong> </md-switch>

        <md-button ng-href="<?php echo base_url('deposits') ?>" class="md-icon-button" aria-label="Save">

          <md-tooltip md-direction="bottom"><?php echo lang2('cancel') ?></md-tooltip>

          <md-icon><i class="ion-close-circled text-danger"></i></md-icon>

        </md-button>

        <md-button type="submit" ng-click="AddDeposit()" class="md-icon-button" aria-label="Save">

          <md-progress-circular ng-show="savingDeposit== true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

          <md-tooltip ng-hide="savingDeposit == true" md-direction="bottom"><?php echo lang2('create') ?></md-tooltip>

          <md-icon ng-hide="savingDeposit == true"><i class="ion-checkmark-circled text-success"></i></md-icon>

        </md-button>

      </div>

    </md-toolbar>

    <md-content ng-show="!depositsLoader" class="bg-white" layout-padding ng-cloak>

      <div layout-gt-xs="row">

        <md-input-container required class="md-block" flex-gt-sm>

          <label><?php echo lang2('title') ?></label>

          <input required ng-model="newdeposit.title" name="title">

        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs>

          <label><?php echo lang2('category'); ?></label>

          <md-select required ng-model="newdeposit.category" name="category" style="min-width: 200px;">

            <md-option ng-value="category.id" ng-repeat="category in categories">{{category.name}}</md-option>

          </md-select>

        </md-input-container>

        <br>

        <md-input-container class="md-block" ng-show="!newdeposit.internal" flex-gt-xs>

          <md-select required ng-model="newdeposit.customer" name="customer" data-md-container-class="selectdemoSelectHeader" style="min-width: 200px;">

            <md-select-header class="demo-select-header">

              <label style="display: none;"><?php echo lang2('search') . ' ' . lang2('customer') ?></label>

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

        <md-input-container class="md-block" flex-gt-xs ng-show="newdeposit.internal">

          <label><?php echo lang2('staff'); ?></label>

          <md-select required placeholder="<?php echo lang2('choisestaff'); ?>" ng-model="newdeposit.staff" name="customer" style="min-width: 200px;">

            <md-option ng-value="staf.id" ng-repeat="staf in staff">{{staf.name}}</md-option>

          </md-select>

        </md-input-container>

      </div>

      <div layout-gt-xs="row">

        <md-input-container class="md-block" flex-gt-xs>

          <label><?php echo lang2('deposit') . ' ' . lang2('description') ?></label>

          <input ng-model="newdeposit.description" name="newdeposit.description">

        </md-input-container>

        <br>

        <md-input-container class="md-block" flex-gt-xs>

          <label><?php echo lang2('account'); ?></label>

          <md-select required ng-model="newdeposit.account" name="account" style="min-width: 200px;">

            <md-option ng-value="account.id" ng-repeat="account in accounts">{{account.name}}</md-option>

          </md-select>

        </md-input-container>

        <br>

        <md-input-container>

          <label><?php echo lang2('date') ?></label>

          <md-datepicker required name="created" ng-model="newdeposit.date" md-open-on-focus></md-datepicker>

          <md-tooltip md-direction="top"><?php echo lang2('deposit') . ' ' . lang2('date') ?></md-tooltip>

        </md-input-container>

        <br>

      </div>

      <div ng-show="deposit_recurring" layout-gt-xs="row">

        <md-input-container class="md-block" flex-gt-xs>

          <label><?php echo lang2('recurring_period') ?></label>

          <input type="number" ng-value="1" value="1" ng-init="recurring_period = 1" min="1" ng-model="recurring_period" name="recurring_period">

        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs>

          <label><?php echo lang2('recurring_type') ?></label>

          <md-select ng-model="recurring_type" name="recurring_type">

            <md-option value="0"><?php echo lang2('days') ?></md-option>

            <md-option value="1"><?php echo lang2('weeks') ?></md-option>

            <md-option value="2" selected><?php echo lang2('months') ?></md-option>

            <md-option value="3"><?php echo lang2('years') ?></md-option>

          </md-select>

        </md-input-container>

        <br>

        <md-input-container>

          <label><?php echo lang2('ends_on') ?></label>

          <md-datepicker md-min-date="date" name="EndRecurring" ng-model="EndRecurring" style="min-width: 100%;" md-open-on-focus></md-datepicker>

          <div>

            <div ng-message="required" class="my-message"><?php echo lang2('leave_blank_for_lifetime') ?></div>

          </div>

        </md-input-container>

      </div>

    </md-content>

    <md-content ng-show="!depositsLoader" class="bg-white" layout-padding ng-cloak>

      <md-list-item ng-repeat="item in newdeposit.items">

        <div layout-gt-sm="row">

          <md-autocomplete md-autofocus md-items="product in GetProduct(item.name)" md-search-text="item.name" md-item-text="product.name" md-selected-item="selectedProduct" md-no-cache="true" md-min-length="0" md-floating-label="<?php echo lang2('productservice'); ?>">

            <md-item-template> <span md-highlight-text="item.name">{{product.name}}</span> <strong ng-bind-html="product.price | currencyFormat:cur_code:null:true:cur_lct"></strong> </md-item-template>

          </md-autocomplete>

          <md-input-container class="md-block">

            <label><?php echo lang2('description'); ?></label>

            <input class="min_input_width" type="hidden" ng-model="item.name">

            <bind-expression ng-init="selectedProduct.name = item.name" expression="selectedProduct.name" ng-model="item.name" />

            <textarea class="min_input_width" ng-model="item.description" placeholder="<?php echo lang2('description'); ?>"></textarea>

            <bind-expression ng-init="selectedProduct.description = item.description" expression="selectedProduct.description" ng-model="item.description" />

            <input class="min_input_width" type="hidden" ng-model="item.product_id">

            <bind-expression ng-init="selectedProduct.product_id = item.product_id" expression="selectedProduct.product_id" ng-model="item.product_id" />

            <input class="min_input_width" type="hidden" ng-model="item.code" ng-value="selectedProduct.code">

            <bind-expression ng-init="selectedProduct.code = item.code" expression="selectedProduct.code" ng-model="item.code" />

          </md-input-container>

          <md-input-container class="md-block" flex-gt-sm>

            <label><?php echo lang2('quantity'); ?></label>

            <input class="min_input_width" ng-model="item.quantity">

          </md-input-container>

          <md-input-container class="md-block" flex-gt-xs>

            <label><?php echo lang2('unit'); ?></label>

            <input class="min_input_width" ng-model="item.unit">

          </md-input-container>

          <md-input-container class="md-block">

            <label><?php echo lang2('price'); ?></label>

            <input class="min_input_width" ng-model="item.price">

            <bind-expression ng-init="selectedProduct.price = 0" expression="selectedProduct.price" ng-model="item.price" />

          </md-input-container>

          <md-input-container class="md-block" flex-gt-xs>

            <label><?php echo $appconfig['tax_label'] ?> (%)</label>

            <input class="min_input_width" ng-model="item.tax">

            <bind-expression ng-init="selectedProduct.tax = 0" expression="selectedProduct.tax" ng-model="item.tax" />

          </md-input-container>

          <md-input-container class="md-block">

            <label><?php echo lang2('total'); ?></label>

            <input disabled="" ng-value="item.quantity * item.price + ((item.tax)/100*item.quantity * item.price)">

          </md-input-container>

        </div>

        <md-icon aria-label="Remove Line" ng-click="remove($index)" class="md-secondary ion-trash-b text-muted"></md-icon>

      </md-list-item>

      <md-content class="bg-white" layout-padding>

        <div class="col-md-6">

          <md-button ng-click="add()" class="md-fab pull-left" ng-disabled="false" aria-label="Add Line">

            <md-icon class="ion-plus-round text-muted"></md-icon>

          </md-button>

        </div>

        <div class="col-md-6 md-pr-0" style="font-weight: 900; font-size: 16px; color: #c7c7c7;">

          <div class="col-md-7">

            <div class="text-right text-uppercase text-muted"><?php echo lang2('subtotal') ?>:</div>

            <div ng-show="totaltax() > 0" class="text-right text-uppercase text-muted"><?php echo lang2('total') . ' ' . $appconfig['tax_label'] ?>:</div>

            <div class="text-right text-uppercase text-black"><?php echo lang2('grandtotal') ?>:</div>

          </div>

          <div class="col-md-5">

            <div class="text-right" ng-bind-html="subtotal() | currencyFormat:cur_code:null:true:cur_lct"></div>

            <div ng-show="totaltax() > 0" class="text-right" ng-bind-html="totaltax() | currencyFormat:cur_code:null:true:cur_lct"></div>

            <div class="text-right" ng-bind-html="grandtotal() | currencyFormat:cur_code:null:true:cur_lct"></div>

          </div>

        </div>

      </md-content>

    </md-content>

  </div>

  <div class="main-content container-fluid lg-pl-0 col-xs-12 col-md-12 col-lg-3">

    <ciuis-sidebar></ciuis-sidebar>

  </div>

</div>

<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>


<script type="text/javascript" src="<?php echo base_url('assets/js/deposits.js') ?>"></script>