<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>

<div class="ciuis-body-content">

	<div ng-controller="Purchases_Controller" class="main-content container-fluid col-xs-12 col-md-12 col-lg-9">

		<md-toolbar class="toolbar-white">

			<div class="md-toolbar-tools">

				<md-button class="md-icon-button" aria-label="Invoice" ng-disabled="true">

					<md-icon><i class="ico-ciuis-invoices text-muted"></i></md-icon>

				</md-button>

				<h2 flex md-truncate><?php echo lang2('newpurchase') ?></h2>

				<md-switch ng-model="purchase_status" aria-label="Status" ng-cloak><strong class="text-muted"><?php echo lang2('paid') ?></strong></md-switch>


				<md-switch ng-model="purchase_recurring" aria-label="Recurring" ng-cloak>

					<strong class="text-muted"><?php echo lang2('recurring') ?></strong>

				</md-switch>


				<md-button ng-href="<?php echo base_url('purchases') ?>" class="md-icon-button" aria-label="Save" ng-cloak>

					<md-tooltip md-direction="bottom"><?php echo lang2('cancel') ?></md-tooltip>

					<md-icon><i class="ion-close-circled text-muted"></i></md-icon>

				</md-button>

				<?php if (check_privilege('purchases', 'create')) { ?>

					<md-button type="submit" ng-click="saveAll()" class="md-icon-button" aria-label="Save" ng-cloak>

						<md-progress-circular ng-show="savingPurchase == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

						<md-tooltip ng-hide="savingPurchase == true" md-direction="bottom"><?php echo lang2('save') ?></md-tooltip>

						<md-icon ng-hide="savingPurchase == true"><i class="ion-checkmark-circled text-muted"></i></md-icon>

					</md-button>

				<?php } ?>

			</div>

		</md-toolbar>

		<md-content class="bg-white" layout-padding ng-cloak>

			<div layout-gt-xs="row">
				<!--
           <md-input-container class="md-block" flex-gt-sm>
            <label><?php echo lang2('serie') ?></label>
            <input ng-model="serie" name="serie">
          </md-input-container>
		-->
				<md-input-container class="md-block" flex-gt-sm>
					<label><?php echo lang2('purchase_number') ?></label>
					<input ng-model="no" name="no">
				</md-input-container>

				<md-input-container class="md-block" flex-gt-xs>

					<label><?php echo lang2('vendor'); ?></label>

					<md-select required placeholder="<?php echo lang2('vendor'); ?>" ng-model="vendor" name="vendor" style="min-width: 200px;">

						<md-select-header>

							<md-toolbar class="toolbar-white">

								<div class="md-toolbar-tools">

									<md-button class="md-icon-button" aria-label="Invoice" ng-disabled="true">

										<md-icon><i class="ico-ciuis-customers text-muted"></i></md-icon>

									</md-button>

									<h2 flex md-truncate><?php echo lang2('vendor') ?></h2>

									<md-button ng-href="<?php echo base_url('vendors') ?>" class="md-icon-button" aria-label="Create New">

										<md-icon><i class="mdi mdi-plus text-muted"></i></md-icon>

									</md-button>

								</div>

							</md-toolbar>

						</md-select-header>

						<md-option ng-value="vendor.id" ng-repeat="vendor in all_vendors">{{vendor.name}}</md-option>

					</md-select>

					<div ng-messages="userForm.customer" role="alert" multiple>

						<div ng-message="required" class="my-message"><?php //echo lang2('you_must_supply_a_customer')
																		?></div>

					</div>

				</md-input-container>

				<md-input-container flex-gt-xs>
					<label><?php echo lang2('dateofissuance') ?></label>
					<input type="date" name="created" ng-model="created" md-open-on-focus></input>
				</md-input-container>

				<md-input-container flex-gt-xs>
					<label><?php echo lang2('duedate') ?></label>
					<input type="date" name="duedate" ng-model="duedate" md-open-on-focus></input>
				</md-input-container>


			</div>

			<div ng-show="purchase_status" layout-gt-xs="row">

				<md-input-container class="md-block" flex-gt-xs>

					<label><?php echo lang2('paidcashornank'); ?></label>

					<md-select placeholder="<?php echo lang2('choiseaccount'); ?>" ng-model="account" name="account" style="min-width: 200px;">

						<md-option ng-value="account.id" ng-repeat="account in accounts">{{account.name}}</md-option>

					</md-select>

					<div ng-messages="userForm.customer" role="alert" multiple>

						<div ng-message="required" class="my-message">You must supply a Vendor.</div>

					</div>

				</md-input-container>

				<md-input-container>

					<label><?php echo lang2('datepaid') ?></label>

					<md-datepicker name="datepayment" ng-model="datepayment" md-open-on-focus></md-datepicker>

				</md-input-container>

			</div>

			<div ng-show="purchase_recurring" layout-gt-xs="row">
				<md-input-container class="md-block" flex-gt-xs style="max-width: 400px;">
					<label><?php echo lang2('recurring_type') ?></label>
					<md-select ng-model="recurring_type" name="recurring_type">
						<md-option value="0"><?php echo lang2('days') ?></md-option>
						<md-option value="1" selected><?php echo lang2('weeks') ?></md-option>
						<md-option value="2"><?php echo lang2('months') ?></md-option>
						<md-option value="3"><?php echo lang2('years') ?></md-option>
					</md-select>
				</md-input-container>

				<md-input-container style="max-width: 400px;">
					<label><?php echo lang2('ends_on') ?></label>
					<input type="date" name="EndRecurring" ng-model="EndRecurring" style="min-width: 100%;" md-open-on-focus></input>
				</md-input-container>
			</div>

			<div ng-show="!purchase_status">

			</div>

		</md-content>

		<md-content class="bg-white" layout-padding ng-cloak>

			<md-list-item ng-repeat="item in purchase.items">

				<div layout-gt-sm="row">
					<!--
					<md-autocomplete md-autofocus md-items="product in GetProduct(item.name)" md-search-text="item.name" md-item-text="product.name" md-selected-item="selectedProduct" md-no-cache="true" md-min-length="0" md-floating-label="<?php echo lang2('productservice'); ?>">
						<md-item-template>
							<span md-highlight-text="item.name">{{product.name}}</span>
							<strong ng-bind-html="product.price | currencyFormat:cur_code:null:true:cur_lct"></strong>
						</md-item-template>
					</md-autocomplete>
				-->



					<md-input-container class="md-block" flex-gt-sm style="min-width: 30px;">
						<label><?php echo lang2('productservice'); ?></label>
						<input class="min_input_width" ng-model="item.name">
					</md-input-container>

					<md-input-container class="md-block">
						<label><?php echo lang2('description'); ?></label>
						<!--
						<input class="min_input_width" type="hidden" ng-model="item.name">
						<bind-expression ng-init="selectedProduct.name = item.name" expression="selectedProduct.name" ng-model="item.name" />
				-->
						<textarea class="min_input_width" ng-model="item.description" placeholder="<?php echo lang2('description'); ?>"></textarea>

						<bind-expression ng-init="selectedProduct.description = item.description" expression="selectedProduct.description" ng-model="item.description" />

						<input class="min_input_width" type="hidden" ng-model="item.product_id">

						<bind-expression ng-init="selectedProduct.product_id = item.product_id" expression="selectedProduct.product_id" ng-model="item.product_id" />

						<input class="min_input_width" type="hidden" ng-model="item.code" ng-value="selectedProduct.code">

						<bind-expression ng-init="selectedProduct.code = item.code" expression="selectedProduct.code" ng-model="item.code" />

					</md-input-container>

					<md-input-container class="md-block" flex-gt-sm style="min-width: 30px;">

						<label><?php echo lang2('quantity'); ?></label>

						<input class="min_input_width" ng-model="item.quantity">

					</md-input-container>

					<md-input-container class="md-block" flex-gt-xs style="min-width: 40px;">

						<label><?php echo lang2('unit'); ?></label>

						<input class="min_input_width" ng-model="item.unit">

					</md-input-container>

					<md-input-container class="md-block">

						<label><?php echo lang2('price'); ?></label>

						<input class="min_input_width money" ng-model="item.price">

						<bind-expression ng-init="selectedProduct.price = 0" expression="selectedProduct.price" ng-model="item.price" />

					</md-input-container>


					<md-input-container class="md-block" flex-gt-xs>
						<label><?php echo $appconfig['tax_label']; ?></label>
						<input class="min_input_width" ng-model="item.tax">
						<bind-expression ng-init="selectedProduct.tax = 0" expression="selectedProduct.tax" ng-model="item.tax" />
					</md-input-container>

					<md-input-container class="md-block" flex-gt-sm>

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

						<div class="text-right text-uppercase text-muted">Sub Total:</div>

						<div ng-show="linediscount() > 0" class="text-right text-uppercase text-muted">Total Discount:</div>

						<div ng-show="totaltax() > 0" class="text-right text-uppercase text-muted">Total Tax:</div>

						<div class="text-right text-uppercase text-black">Grand Total:</div>

					</div>

					<div class="col-md-5">

						<div class="text-right" ng-bind-html="subtotal() | currencyFormat:cur_code:null:true:cur_lct"></div>

						<div ng-show="linediscount() > 0" class="text-right" ng-bind-html="linediscount() | currencyFormat:cur_code:null:true:cur_lct"></div>

						<div ng-show="totaltax() > 0" class="text-right" ng-bind-html="totaltax() | currencyFormat:cur_code:null:true:cur_lct"></div>

						<div class="text-right" ng-bind-html="grandtotal() | currencyFormat:cur_code:null:true:cur_lct"></div>

					</div>

				</div>

			</md-content>

		</md-content>

	</div>

</div>

<ciuis-sidebar></ciuis-sidebar>

<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>


<script src="<?php echo base_url('assets/js/purchases.js'); ?>"></script>