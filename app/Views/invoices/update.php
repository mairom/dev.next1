<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>

<?php $appconfig = get_appconfig(); ?>

<div class="ciuis-body-content" ng-controller="Invoice_Controller">

  <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-9">

    <md-toolbar class="toolbar-white">

      <div class="md-toolbar-tools">

        <md-button class="md-icon-button" aria-label="Invoice" ng-disabled="true">

          <md-icon><i class="ico-ciuis-invoices text-muted"></i></md-icon>

        </md-button>

        <h2 flex md-truncate><?php echo lang2('updateinvoicetitle') ?></h2>
        
        <md-switch ng-model="invoice.recurring_status" aria-label="recurring_status" ng-cloak> <strong class="text-muted"><?php echo lang2('recurring') ?></strong> </md-switch>

        <md-button ng-href="<?php echo base_url('invoices/invoice/{{invoice.id}}') ?>" class="md-icon-button" aria-label="View" ng-cloak>

          <md-tooltip md-direction="bottom"><?php echo lang2('view') ?></md-tooltip>

          <md-icon><i class="ion-eye text-warning"></i></md-icon>

        </md-button>

        <md-button ng-href="<?php echo base_url('invoices/invoice/{{invoice.id}}') ?>" class="md-icon-button" aria-label="Cancel" ng-cloak>

          <md-tooltip md-direction="bottom"><?php echo lang2('cancel') ?></md-tooltip>

          <md-icon><i class="ion-close-circled text-danger"></i></md-icon>

        </md-button>

        <md-button ng-click="saveAll()" class="md-icon-button" aria-label="Save" ng-cloak>

          <md-progress-circular ng-show="savingInvoice == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

          <md-tooltip ng-hide="savingInvoice == true" md-direction="bottom"><?php echo lang2('save') ?></md-tooltip>

          <md-icon ng-hide="savingInvoice == true"><i class="ion-checkmark-circled text-success"></i></md-icon>

        </md-button>

      </div>

    </md-toolbar>

    <div ng-show="invoiceLoader" layout-align="center center" class="text-center" id="circular_loader">

      <md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>

      <p style="font-size: 15px;margin-bottom: 5%;">

        <span>

          <?php echo lang2('please_wait') ?> <br>

          <small><strong><?php echo lang2('loading') . ' ' . lang2('invoice') . '...' ?></strong></small>

        </span>

      </p>

    </div>

    <md-content ng-show="!invoiceLoader" class="bg-white" layout-padding ng-cloak>

      <div layout-gt-xs="row">
        <!--
        <md-input-container class="md-block" flex-gt-sm>
          <label><?php echo lang2('serie') ?></label>
          <input ng-model="invoice.serie" name="serie">
        </md-input-container>
        -->
        <md-input-container class="md-block" flex-gt-sm>
          <label><?php echo lang2('invoicenumber') ?></label>
          <input ng-model="invoice.no" name="no">
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs>
          <md-select required placeholder="<?php echo lang2('choisecustomer'); ?>" ng-model="invoice.customer" ng-change="changeBank(invoice.customer)" name="customer" style="min-width: 200px;" data-md-container-class="selectdemoSelectHeader">
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
          </md-select>

          <div ng-messages="userForm.customer" role="alert" multiple>
            <div ng-message="required" class="my-message"><?php echo lang2('you_must_supply_a_customer') ?></div>
          </div>
        </md-input-container>

        <md-input-container  class="md-block" flex-gt-xs>
          <label><?php echo lang2('dateofissuance') ?></label>
          <md-datepicker md-hide-icons="all"	name="created" ng-model="invoice.created_edit" md-open-on-focus></md-datepicker>
        </md-input-container>

        <md-input-container  class="md-block" flex-gt-xs>
          <label><?php echo lang2('duedate') ?></label>
          <md-datepicker md-hide-icons="all"	name="duedate" ng-model="invoice.duedate_edit" md-open-on-focus></md-datepicker>
        </md-input-container>

      </div>

      <div ng-show="invoice.recurring_status" layout-gt-xs="row">

        <input name="recurring_id" ng-model="invoice.recurring_id" type="hidden">

     

        <md-input-container class="md-block" flex-gt-xs>

          <label><?php echo lang2('recurring_type') ?></label>

          <md-select ng-model="invoice.recurring_type" name="recurring_type">

            <md-option value="0"><?php echo lang2('days') ?></md-option>

            <md-option value="1" selected><?php echo lang2('weeks') ?></md-option>

            <md-option value="2"><?php echo lang2('months') ?></md-option>

            <md-option value="3"><?php echo lang2('years') ?></md-option>

          </md-select>

        </md-input-container>

        <md-input-container flex-gt-xs  style="max-width: 400px;">
          <label><?php echo lang2('ends_on') ?></label>
          <input  type = "date" name="recurring_endDate" ng-model="invoice.recurring_endDate" style="width: 100%;" md-open-on-focus></input>
        </md-input-container>

    
      </div>

      <div layout-gt-xs="row">

        <md-input-container class="md-block" flex-gt-sm>

          <label><?php echo lang2('duenote') ?></label>

          <input ng-model="invoice.duenote" name="duenote">

        </md-input-container>

        <?php
        if ($user_data['super_admin'] == "1") {
        ?>
          <md-input-container class="md-block">
            <label><?php echo lang2('payment_method'); ?></label>
            <md-select placeholder="<?php echo lang2('default_payment_method'); ?>" ng-model="invoice.default_payment_method" name="default_payment_method" style="min-width: 200px;">
              <?php
              $gateways = get_active_payment_methods();
              foreach ($gateways as $gateway) { ?>
                <md-option ng-value='"<?php echo $gateway['relation'] ?>"'><?php echo lang($gateway['relation']) ? lang($gateway['relation']) : $gateway['name'] ?></md-option>
              <?php } ?>
            </md-select>
          </md-input-container>
        <?php
        }
        ?>
        <md-input-container class="md-block" style="margin-bottom: 1em !important;">
          <label>Forma de pagamento</label>
          <md-select placeholder="Forma de pagamento" ng-model="invoice.id_forma_pagment" name="default_payment_method" style="min-width: 200px;">
            <md-option ng-value="formaDpag.id_forma" ng-repeat="formaDpag in formas_pagamento">{{formaDpag.nm_forma}}</md-option>
          </md-select>
        </md-input-container>

      </div>

    </md-content>

    <md-content ng-show="!invoiceLoader" class="bg-white" layout-padding ng-cloak>

      <md-list-item ng-repeat="item in invoice.items">

        <div layout-gt-sm="row">
          <md-autocomplete class="md-autocomplete" md-autofocus md-items="product in GetProduct(item.name)" md-search-text="item.name" md-item-text="product.name" md-selected-item="selectedProduct" md-no-cache="true" md-min-length="0" md-floating-label="<?php echo lang2('productservice'); ?>" placeholder="What is your favorite US state?">
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

            <input class="min_input_width money inptValue" ng-model="item.price">

            <bind-expression ng-init="selectedProduct.price = item.price" expression="selectedProduct.price" ng-model="item.price" />

          </md-input-container>
          <!--
          <md-input-container class="md-block" flex-gt-sm>
            <input class="min_input_width" ng-model="item.tax">
            <bind-expression ng-init="selectedProduct.tax = item.tax" expression="selectedProduct.tax" ng-model="item.tax" />
          </md-input-container>
            -->

          <md-input-container class="md-block" flex-gt-xs>
            <label>% <?php echo lang2('discount'); ?> </label>
            <input class="min_input_width" ng-model="item.discount">
          </md-input-container>

          <md-input-container class="md-block">

            <label><?php echo lang2('total'); ?></label>

            <input class="min_input_width" ng-value="formatReal(item.quantity * (moneyEua(item.price) + (( item.tax ) / 100 * item.quantity * moneyEua(item.price) ) - ( (item.discount) / 100 * item.quantity * moneyEua(item.price))))">

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

            <div class="text-right text-uppercase text-muted"><?php echo lang2('sub_total'); ?>:</div>

            <div ng-show="linediscount() > 0" class="text-right text-uppercase text-muted"><?php echo lang2('total_discount'); ?>:</div>

           <!-- <div ng-show="totaltax() > 0" class="text-right text-uppercase text-muted"><?php echo lang2('total') . ' ' . $appconfig['tax_label']; ?>:</div> -->

            <div class="text-right text-uppercase text-black"><?php echo lang2('grand_total'); ?>:</div>

          </div>

          <div class="col-md-5">

            <div class="text-right" ng-bind-html="subtotal() | currencyFormat:cur_code:null:true:cur_lct"></div>

            <div ng-show="linediscount() > 0" class="text-right" ng-bind-html="linediscount() | currencyFormat:cur_code:null:true:cur_lct"></div>

            <!-- <div ng-show="totaltax() > 0" class="text-right" ng-bind-html="totaltax() | currencyFormat:cur_code:null:true:cur_lct"></div> -->

            <div class="text-right" ng-bind-html="grandtotal() | currencyFormat:cur_code:null:true:cur_lct"></div>

          </div>

        </div>

      </md-content>

    </md-content>

  </div>

  <div ng-show="!invoiceLoader" class="main-content container-fluid lg-pl-0 col-xs-12 col-md-12 col-lg-3" ng-cloak>

    <md-toolbar class="toolbar-white">

      <div class="md-toolbar-tools">

        <md-button class="md-icon-button" aria-label="Invoice" ng-disabled="true">

          <md-icon><i class="ico-ciuis-invoices text-muted"></i></md-icon>

        </md-button>

        <h2 flex md-truncate><?php echo lang2('billing_and_shipping_details'); ?></h2>

      </div>

    </md-toolbar>

    <md-subheader class="md-primary bg-white text-uppercase text-bold"><?php echo lang2('billing_address'); ?></md-subheader>

    <md-divider></md-divider>

    <md-content layout-padding class="bg-white">

      <address class="m-t-5 m-b-5">

        <strong ng-bind="invoice.billing_street"></strong><br>

        <span ng-bind="invoice.billing_city"></span> / <span ng-bind="invoice.billing_state"></span> <span ng-bind="invoice.billing_zip"></span><br>

        <strong ng-bind="invoice.billing_country"></strong>

        <!-- <srong ng-bind="invoice.billing_country_id"></srong> -->

      </address>

      <md-content ng-if='EditBilling == true' layout-padding class="bg-white">

        <md-input-container class="md-block">

          <label><?php echo lang2('address') ?></label>

          <textarea ng-model="invoice.billing_street" md-maxlength="500" rows="2" md-select-on-focus></textarea>

        </md-input-container>

        <md-input-container class="md-block">

          <md-select placeholder="<?php echo lang2('country'); ?>" ng-model="invoice.billing_country_id" ng-change="getBillingStates(invoice.billing_country_id)" name="billing_country_id" style="min-width: 200px;">

            <md-option ng-value="country.id" ng-repeat="country in countries">{{country.shortname}}</md-option>

          </md-select><br />

        </md-input-container>

        <br />

        <md-input-container class="md-block">

          <label><?php echo lang2('state'); ?></label>

          <md-select placeholder="<?php echo lang2('state'); ?>" ng-model="invoice.billing_state_id" name="billing_state_id" style="min-width: 200px;">

            <md-option ng-value="state.id" ng-repeat="state in billingStates">{{state.state_name}}</md-option>

          </md-select>

        </md-input-container>

        <md-input-container class="md-block">

          <label><?php echo lang2('city'); ?></label>

          <input name="city" ng-model="invoice.billing_city">

        </md-input-container>

        <md-input-container class="md-block">

          <label><?php echo lang2('zipcode'); ?></label>

          <input name="zipcode" ng-model="invoice.billing_zip">

        </md-input-container>

      </md-content>

      <md-switch ng-model="NeedShippingAddress" aria-label="Status"><strong class="text-muted"><?php echo lang2('need_shipping_address'); ?></strong></md-switch>

      <md-button ng-show='EditBilling == false' ng-click="EditBilling = true" ng-init="EditBilling=false" class="md-icon-button pull-right" aria-label="Edit">

        <md-icon><i class="mdi mdi-edit text-muted"></i></md-icon>

        <md-tooltip md-direction="left"><?php echo lang2('edit'); ?></md-tooltip>

      </md-button>

      <md-button ng-show='EditBilling == true' ng-click="EditBilling = false" class="md-icon-button pull-right" aria-label="Hide Billing Form">

        <md-icon><i class="mdi mdi-minus-circle-outline text-muted"></i></md-icon>

        <md-tooltip md-direction="left"><?php echo lang2('hide'); ?></md-tooltip>

      </md-button>

      <md-button ng-click='CopyBillingFromCustomer()' class="md-icon-button pull-right" aria-label="Billing Copy">

        <md-icon><i class="mdi mdi-copy text-muted"></i></md-icon>

        <md-tooltip md-direction="left"><?php echo lang2('copy_from_customer'); ?></md-tooltip>

      </md-button>

    </md-content>

    <md-divider></md-divider>

    <md-subheader ng-show='NeedShippingAddress == true' class="md-primary bg-white text-uppercase text-bold"><?php echo lang2('shipping_address'); ?></md-subheader>

    <md-divider ng-show='NeedShippingAddress == true'></md-divider>

    <md-content ng-show='NeedShippingAddress == true' layout-padding class="bg-white">

      <address ng-hide='EditShipping == true' class="m-t-5 m-b-5">

        <strong ng-bind="invoice.shipping_street"></strong><br>

        <span ng-bind="invoice.shipping_city"></span> / <span ng-bind="invoice.shipping_state"></span> <span ng-bind="invoice.shipping_zip"></span><br>

        <strong ng-bind="invoice.shipping_country"></strong>

        <!-- <srong ng-bind="invoice.shipping_country.id"></srong> -->

      </address>

      <md-content ng-show='EditShipping == true' layout-padding class="bg-white">

        <md-input-container class="md-block">

          <label><?php echo lang2('address') ?></label>

          <textarea ng-model="invoice.shipping_street" md-maxlength="500" rows="2" md-select-on-focus></textarea>

        </md-input-container>

        <md-input-container class="md-block">

          <md-select placeholder="<?php echo lang2('country'); ?>" ng-model="invoice.shipping_country_id" ng-change="getShippingStates(invoice.shipping_country_id)" name="shipping_country_id" style="min-width: 200px;">

            <md-option ng-value="{{country.id}}" ng-repeat="country in countries">{{country.shortname}}</md-option>

          </md-select><br>

        </md-input-container>

        <md-input-container class="md-block">

          <label><?php echo lang2('state'); ?></label>

          <md-select placeholder="<?php echo lang2('state'); ?>" ng-model="invoice.shipping_state_id" name="shipping_state_id" style="min-width: 200px;">

            <md-option ng-value="state.id" ng-repeat="state in shippingStates">{{state.state_name}}</md-option>

          </md-select>

        </md-input-container>

        <md-input-container class="md-block">

          <label><?php echo lang2('city'); ?></label>

          <input name="city" ng-model="invoice.shipping_city">

        </md-input-container>

        <md-input-container class="md-block">

          <label><?php echo lang2('zipcode'); ?></label>

          <input name="zipcode" ng-model="invoice.shipping_zip">

        </md-input-container>

        <bind-expression ng-init="invoice.shipping_country = '----'" expression="customer.shipping_country" ng-model="invoice.shipping_country" />

      </md-content>

      <md-button ng-show='EditShipping == false' ng-click="EditShipping = true" ng-init="EditShipping=false" class="md-icon-button pull-right" aria-label="Edit">

        <md-icon><i class="mdi mdi-edit text-muted"></i></md-icon>

        <md-tooltip md-direction="left"><?php echo lang2('edit'); ?></md-tooltip>

      </md-button>

      <md-button ng-show='EditShipping == true' ng-click="EditShipping = false" class="md-icon-button pull-right" aria-label="Hide Form">

        <md-icon><i class="mdi mdi-minus-circle-outline text-muted"></i></md-icon>

        <md-tooltip md-direction="left"><?php echo lang2('hide'); ?></md-tooltip>

      </md-button>

      <md-button ng-click='CopyShippingFromCustomer()' class="md-icon-button pull-right" aria-label="Cop Shipping">

        <md-icon><i class="mdi mdi-copy text-muted"></i></md-icon>

        <md-tooltip md-direction="left"><?php echo lang2('copy_from_customer'); ?></md-tooltip>

      </md-button>

    </md-content>

    <md-content class="bg-white">

      <custom-fields-vertical></custom-fields-vertical>

      </md-toolbar>

      <md-divider></md-divider>

  </div>

  <div id="remove{{invoice.id}}" tabindex="-1" role="dialog" class="modal fade">

    <div class="modal-dialog">

      <div class="modal-content">

        <div class="modal-header">

          <button type="button" data-dismiss="modal" aria-hidden="true" class="close"><span class="mdi mdi-close"></span></button>

        </div>

        <div class="modal-body">

          <div class="text-center">

            <div class="text-danger"><span class="modal-main-icon mdi mdi-close-circle-o"></span> </div>

            <h3><?php echo lang2('attention'); ?></h3>

            <p><?php echo lang2('inv_remove_msg'); ?></p>

            <div class="xs-mt-50"> <a type="button" data-dismiss="modal" class="btn btn-space btn-default"><?php echo lang2('cancel'); ?></a> <a href="<?php echo site_url('invoices/remove/' . $invoices['id']); ?>" type="button" class="btn btn-space btn-danger"><?php echo lang2('delete'); ?></a> </div>

          </div>

        </div>

        <div class="modal-footer"></div>

      </div>

    </div>

  </div>

  <script>
    var INVOICEID = <?php echo $invoices['id']; ?>;

    var INVOICECUSTOMER = <?php echo $invoices['customer_id']; ?>;
  </script>

</div>

<?php include_once(APPPATH . 'Views/inc/footer.php'); ?>

<script type="text/javascript" src="<?php echo base_url('assets/js/invoices.js?v=1.2.2') ?>"></script>

<script>
    function initGraph(){

}
  $(document).ready(function() {
    $(".md-autocomplete").change(function() {
      setTimeout(a => {
        $(".money").maskMoney({
          prefix: '',
          allowNegative: true,
          thousands: '.',
          decimal: ',',
          affixesStay: false
        }).trigger('mask.maskMoney').change();
      }, 1000)
    });

    $("md-virtual-repeat-container").click(function() {
      setTimeout(a => {
        $(".money").maskMoney({
          prefix: '',
          allowNegative: true,
          thousands: '.',
          decimal: ',',
          affixesStay: false
        }).trigger('mask.maskMoney').change();
      }, 1000)
    });
  });
</script>