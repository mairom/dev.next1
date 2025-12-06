<?php include_once(APPPATH . 'Views/inc/header.php'); ?>

<?php $appconfig = get_appconfig(); ?>

<div class="ciuis-body-content" ng-controller="Deposit_Controller">

  <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-9">



    <md-toolbar ng-show="!depositsLoader" class="toolbar-white">

      <div class="md-toolbar-tools">

        <md-button class="md-icon-button" aria-label="Invoice" ng-disabled="true" ng-cloak>

          <md-icon><i class="ico-ciuis-expenses text-muted"></i></md-icon>

        </md-button>

        <h2 flex md-truncate><span ng-bind="deposit.longid"></span></h2>

        <md-switch ng-model="deposit.recurring_status" aria-label="Recurring" ng-cloak> <strong class="text-muted"><?php echo lang2('recurring') ?></strong> </md-switch>

        <md-button ng-href="<?php echo base_url('deposits') ?>" class="md-icon-button" aria-label="Save" ng-cloak>

          <md-tooltip md-direction="bottom"><?php echo lang2('cancel') ?></md-tooltip>

          <md-icon><i class="ion-close-circled text-danger"></i></md-icon>

        </md-button>

        <md-button type="submit" ng-click="UpdateDeposit()" class="md-icon-button" aria-label="Save" ng-cloak>

          <md-progress-circular ng-show="savingDeposit == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

          <md-tooltip ng-hide="savingDeposit == true" md-direction="bottom"><?php echo lang2('save') ?></md-tooltip>

          <md-icon ng-hide="savingDeposit == true"><i class="ion-checkmark-circled text-success"></i></md-icon>

        </md-button>

      </div>

    </md-toolbar>

    <div ng-show="depositsLoader" layout-align="center center" class="text-center" id="circular_loader">

      <!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
      <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">

      <p style="font-size: 15px;margin-bottom: 5%;">

        <span><?php echo lang2('please_wait') . '....' ?> <br>

          <small><strong><?php echo lang2('loading') . ' ' . lang2('deposit') . '...' ?></strong></small>

        </span>

      </p>

    </div>

    <md-content ng-show="!depositsLoader" class="bg-white" layout-padding ng-cloak>

      <div layout-gt-xs="row">

        <input name="recurring_id" ng-model="deposit.recurring_id" type="hidden">

        <md-input-container required class="md-block" flex-gt-sm>

          <label><?php echo lang2('title') ?></label>

          <input required ng-model="deposit.title" name="title">

        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs>

          <label><?php echo lang2('category'); ?></label>

          <md-select required ng-model="deposit.category" name="category" style="min-width: 200px;">

            <md-option ng-value="category.id" ng-repeat="category in categories">{{category.name}}</md-option>

          </md-select>

        </md-input-container>

        <br>

        <md-input-container flex-gt-xs ng-show="!deposit.internal" class="md-block">

          <md-select required placeholder="<?php echo lang2('choisecustomer'); ?>" ng-model="deposit.customer" name="customer" style="min-width: 200px;" data-md-container-class="selectdemoSelectHeader">

            <md-select-header class="demo-select-header">

              <label style="display: none;"><?php echo lang2('search') . ' ' . lang2('customer') ?></label>

              <input ng-submit="search_customers(search_input)" ng-model="search_input" type="text" placeholder="<?php echo lang2('search') . ' ' . lang2('customers') ?>" class="demo-header-searchbox md-text" ng-keyup="search_customers(search_input)">

            </md-select-header>

            <md-optgroup label="customers">

              <md-option ng-value="customer.id" ng-repeat="customer in all_customers">

                <span class="blur" ng-bind="customer.customer_number"></span>

                <span ng-bind="customer.name"></span><br>

                <span class="blur">(<small ng-bind="customer.email"></small>)</span>

              </md-option>

            </md-optgroup>

            <!-- <md-option ng-value="customer.id" ng-repeat="customer in all_customers">{{customer.name}}</md-option> -->

          </md-select>

        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs ng-show="deposit.internal">

          <label><?php echo lang2('staff'); ?></label>

          <md-select required placeholder="<?php echo lang2('choisestaff'); ?>" ng-model="deposit.staff_id" name="customer" style="min-width: 200px;">

            <md-option ng-value="staf.id" ng-repeat="staf in staff">{{staf.name}}</md-option>

          </md-select>

        </md-input-container>

        <br>

      </div>

      <div layout-gt-xs="row">

        <md-input-container class="md-block" flex-gt-xs>

          <label><?php echo lang2('deposit') . ' ' . lang2('description') ?></label>

          <input ng-model="deposit.description" name="deposit.description">

        </md-input-container>

        <br>

        <md-input-container class="md-block" flex-gt-xs>

          <label><?php echo lang2('account'); ?></label>

          <md-select required ng-model="deposit.account" name="account" style="min-width: 200px;">

            <md-option ng-value="account.id" ng-repeat="account in accounts">{{account.name}}</md-option>

          </md-select>

        </md-input-container>

        <br>

        <md-input-container class="md-block">

          <label><?php echo lang2('date') ?></label>

          <md-datepicker required name="created" ng-model="deposit.date_edit" md-open-on-focus></md-datepicker>

          <md-tooltip md-direction="top"><?php echo lang2('deposit') . ' ' . lang2('date') ?></md-tooltip>

        </md-input-container>

        <br>

      </div>

      <div ng-show="deposit.recurring_status" layout-gt-xs="row">

        <md-input-container class="md-block" flex-gt-xs>

          <label><?php echo lang2('recurring_period') ?></label>

          <input type="number" ng-value="1" value="1" ng-init="recurring_period = 1" min="1" ng-model="deposit.recurring_period" name="recurring_period">

        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs>

          <label><?php echo lang2('recurring_type') ?></label>

          <md-select ng-model="deposit.recurring_type" name="recurring_type">

            <md-option value="0"><?php echo lang2('days') ?></md-option>

            <md-option value="1"><?php echo lang2('weeks') ?></md-option>

            <md-option value="2" selected><?php echo lang2('months') ?></md-option>

            <md-option value="3"><?php echo lang2('years') ?></md-option>

          </md-select>

        </md-input-container>

        <br>

        <md-input-container>

          <label><?php echo lang2('ends_on') ?></label>

          <md-datepicker md-min-date="date" name="EndRecurring" ng-model="deposit.EndRecurring" style="min-width: 100%;" md-open-on-focus></md-datepicker>

          <div>

            <div ng-message="required" class="my-message"><?php echo lang2('leave_blank_for_lifetime') ?></div>

          </div>

        </md-input-container>

      </div>

    </md-content>

    <md-content ng-show="!depositsLoader" class="bg-white" layout-padding ng-cloak>

      <md-list-item ng-repeat="item in deposit.items">

        <div layout-gt-sm="row">

          <md-autocomplete md-autofocus md-items="product in GetProduct(item.name)" md-search-text="item.name" md-item-text="product.name" md-selected-item="selectedProduct" md-no-cache="true" md-min-length="0" md-floating-label="<?php echo lang2('productservice'); ?>" placeholder="What is your favorite US state?">

            <md-item-template> <span md-highlight-text="item.name">{{product.name}}</span> <strong ng-bind-html="product.price | currencyFormat:cur_code:null:true:cur_lct"></strong> </md-item-template>

          </md-autocomplete>

          <md-input-container class="md-block">

            <label><?php echo lang2('description'); ?></label>

            <input class="min_input_width" type="hidden" ng-model="item.name">

            <bind-expression ng-init="selectedProduct.name = item.name" expression="selectedProduct.name" ng-model="item.name" />

            <textarea class="min_input_width" ng-model="item.description"></textarea>

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

            <bind-expression ng-init="selectedProduct.price = item.price" expression="selectedProduct.price" ng-model="item.price" />

          </md-input-container>

          <md-input-container class="md-block" flex-gt-sm>

            <label><?php echo $appconfig['tax_label']; ?> (%)</label>

            <input class="min_input_width" ng-model="item.tax">

            <bind-expression ng-init="selectedProduct.tax = item.tax" expression="selectedProduct.tax" ng-model="item.tax" />

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

  <script>
    var lang = {};

    var DEPOSITID = "<?php echo $deposit['id'] ?>";

    lang.doIt = '<?php echo lang2('doIt') ?>';

    lang.cancel = '<?php echo lang2('cancel') ?>';

    lang.attention = '<?php echo lang2('attention') ?>';

    lang.delete_deposit = "<?php echo lang2('depositsatentiondetail') ?>";
  </script>

  <?php include_once(APPPATH . 'Views/inc/footer.php'); ?>

  <script type="text/javascript" src="<?php echo base_url('assets/js/deposits.js') ?>"></script>