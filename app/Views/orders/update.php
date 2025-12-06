<?php include_once(APPPATH . 'Views/inc/header.php'); ?>

<?php $appconfig = get_appconfig(); ?>

<div class="ciuis-body-content">

  <div ng-controller="Order_Controller" class="main-content container-fluid col-xs-12 col-md-12 col-lg-9"> <?php echo form_open('orders/update',array("class"=>"form-horizontal orderForm")); ?>

    <md-toolbar class="toolbar-white">

      <div class="md-toolbar-tools">

        <md-button class="md-icon-button" aria-label="Invoice" ng-disabled="true" ng-cloak>

          <md-icon><i class="ico-ciuis-orders text-muted"></i></md-icon>

        </md-button>

        <h2 flex md-truncate><?php echo lang2('update') . ' ' . lang2('order') ?></h2>

        <md-button ng-href="<?php echo base_url('orders/order/{{order.id}}')?>" class="md-icon-button" aria-label="Save" ng-cloak>

          <md-tooltip md-direction="bottom"><?php echo lang2('cancel') ?></md-tooltip>

          <md-icon><i class="ion-close-circled text-muted"></i></md-icon>

        </md-button>

        <md-button ng-click="saveAll()" class="md-icon-button" aria-label="Save" ng-cloak>

          <md-tooltip md-direction="bottom"><?php echo lang2('save') ?></md-tooltip>

          <md-icon><i class="ion-checkmark-circled text-muted"></i></md-icon>

        </md-button>

      </div>

    </md-toolbar>

    <md-content class="bg-white" layout-padding ng-cloak>

      <div layout-gt-xs="row">

        <md-input-container class="md-block" flex-gt-sm>

          <label><?php echo lang2('subject')?></label>

          <input ng-model="order.subject" name="subject">

        </md-input-container>

        <md-input-container ng-show="order.order_type == false" class="md-block" flex-gt-xs>

          <label><?php echo lang2('customer'); ?></label>

          <md-select required placeholder="<?php echo lang2('choisecustomer'); ?>" ng-model="order.customer" name="customer" style="min-width: 200px;" data-md-container-class="selectdemoSelectHeader">

            <md-select-header class="demo-select-header">

              <label style="display: none;"><?php echo lang2('search').' '.lang2('customer')?></label>

              <input ng-submit="search_customers(search_input)" ng-model="search_input" type="text" placeholder="<?php echo lang2('search').' '.lang2('customers')?>" class="demo-header-searchbox md-text" ng-keyup="search_customers(search_input)">

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

            <div ng-message="required" class="my-message"><?php echo lang2('you_must_supply_a_customer') ?>.</div>

          </div>

        </md-input-container>

        <md-input-container ng-show="order.order_type == true" class="md-block" flex-gt-xs>

          <label><?php echo lang2('lead'); ?></label>

          <md-select required placeholder="<?php echo lang2('choiselead'); ?>" ng-model="order.lead" name="lead" style="min-width: 200px;">

            <md-option ng-value="lead.id" ng-repeat="lead in leads">{{lead.name}}</md-option>

          </md-select>

          <div ng-messages="userForm.customer" role="alert" multiple>

            <div ng-message="required" class="my-message"><?php echo lang2('you_must_supply_a_customer') ?>.</div>

          </div>

        </md-input-container>

        <md-input-container>

          <label><?php echo lang2('dateofissuance') ?></label>

          <md-datepicker name="created" ng-model="order.created_edit" md-open-on-focus></md-datepicker>

        </md-input-container>

      </div>

      <div layout-gt-xs="row">

        <md-input-container class="md-block" flex-gt-xs>

          <label><?php echo lang2('assigned'); ?></label>

          <md-select required placeholder="<?php echo lang2('assigned'); ?>" ng-model="order.assigned" name="assigned" style="min-width: 200px;">

            <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>

          </md-select>

          <div ng-messages="userForm.assigned" role="alert" multiple>

            <div ng-message="required" class="my-message"><?php echo lang2('must_supply_assigner') ?>.</div>

          </div>

        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs>

          <label><?php echo lang2('status'); ?></label>

          <md-select ng-init="statuses = [{id: 1,name: '<?php echo lang2('draft'); ?>'}, {id: 2,name: '<?php echo lang2('sent'); ?>'}, {id: 3,name: '<?php echo lang2('open'); ?>'}, {id: 4,name: '<?php echo lang2('revised'); ?>'}, {id:5,name: '<?php echo lang2('declined'); ?>'}, {id: 6,name: '<?php echo lang2('accepted'); ?>'}];" required placeholder="<?php echo lang2('status'); ?>" ng-model="order.status" name="status" style="min-width: 200px;">

            <md-option ng-value="status.id" ng-repeat="status in statuses"><span class="text-uppercase">{{status.name}}</span></md-option>

          </md-select>

          <div ng-messages="userForm.status" role="alert" multiple>

            <div ng-message="required" class="my-message"><?php echo lang2('must_select_status') ?>.</div>

          </div>

        </md-input-container>

        <md-input-container>

          <label><?php echo lang2('opentill') ?></label>

          <md-datepicker name="opentill" ng-model="order.opentill_edit" md-open-on-focus></md-datepicker>

        </md-input-container>

      </div>

      <div layout-gt-xs="row">

        <md-input-container class="md-block" flex-gt-xs>

          <label><?php echo lang2('detail') ?></label>

          <textarea ng-model="order.content" rows="3"></textarea>

        </md-input-container>

      </div>

      <md-checkbox class="pull-right" ng-model="order.comment" ng-true-value="true" ng-false-value="false" aria-label="Comment"> <strong class="text-muted text-uppercase"><?php echo lang2('allowcomments');?></strong> </md-checkbox>

    </md-content>

    <md-content class="bg-white" layout-padding ng-cloak>

      <md-list-item ng-repeat="item in order.items">

        <div layout-gt-sm="row">

          <md-autocomplete

  	  	 	md-autofocus

  	  	 	md-items="product in GetProduct(item.name)"

		    md-search-text="item.name"

		    md-item-text="product.name"

		    md-selected-item="selectedProduct"

		    md-no-cache="true"

		    md-min-length="0"

		    md-floating-label="<?php echo lang2('productservice'); ?>"

		    placeholder="What is your favorite US state?">

            <md-item-template> <span md-highlight-text="item.name">{{product.name}}</span> <strong ng-bind-html="product.price | currencyFormat:cur_code:null:true:cur_lct"></strong> </md-item-template>

          </md-autocomplete>

          <md-input-container class="md-block">

            <label><?php echo lang2('description'); ?></label>

            <input class="min_input_width" type="hidden" ng-model="item.name">

            <input class="min_input_width" type="hidden" ng-model="item.id">

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

            <input class="min_input_width" ng-model="item.quantity" >

          </md-input-container>

          <md-input-container class="md-block" flex-gt-xs>

            <label><?php echo lang2('unit'); ?></label>

            <input class="min_input_width" ng-model="item.unit" >

          </md-input-container>

          <md-input-container class="md-block">

            <label><?php echo lang2('price'); ?></label>

            <input class="min_input_width money" ng-model="item.price">

            <bind-expression ng-init="selectedProduct.price = item.price" expression="selectedProduct.price" ng-model="item.price" />

          </md-input-container>

          <md-input-container class="md-block" flex-gt-sm>

            <label><?php echo $appconfig['tax_label']; ?></label>

            <input class="min_input_width" ng-model="item.tax">

            <bind-expression ng-init="selectedProduct.tax = item.tax" expression="selectedProduct.tax" ng-model="item.tax" />

          </md-input-container>

          <md-input-container class="md-block" flex-gt-xs>

            <label><?php echo lang2('discount'); ?></label>

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

            <div class="text-right text-uppercase text-muted"><?php echo lang2('sub_total') ?>:</div>

            <div ng-show="linediscount() > 0" class="text-right text-uppercase text-muted"><?php echo lang2('total_discount') ?>:</div>

            <div ng-show="totaltax() > 0"class="text-right text-uppercase text-muted"><?php echo lang2('total').' '.$appconfig['tax_label'] ?>:</div>

            <div class="text-right text-uppercase text-black"><?php echo lang2('grand_total') ?>:</div>

          </div>

          <div class="col-md-5">

            <div class="text-right" ng-bind-html="subtotal() | currencyFormat:cur_code:null:true:cur_lct"></div>

            <div ng-show="linediscount() > 0" class="text-right" ng-bind-html="linediscount() | currencyFormat:cur_code:null:true:cur_lct"></div>

            <div ng-show="totaltax() > 0"class="text-right" ng-bind-html="totaltax() | currencyFormat:cur_code:null:true:cur_lct"></div>

            <div class="text-right" ng-bind-html="grandtotal() | currencyFormat:cur_code:null:true:cur_lct"></div>

          </div>

        </div>

      </md-content>

    </md-content>

    <?php echo form_close(); ?>

    <script>

		var ORDERID = <?php echo $order['id']; ?>;

	</script>

  </div>

  <ciuis-sidebar></ciuis-sidebar>

</div>

<?php include_once( APPPATH . 'Views/inc/footer.php' );?>

<script src="<?php echo base_url('assets/js/orders.js'); ?>"></script>