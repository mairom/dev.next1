<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>

<?php $appconfig = get_appconfig(); ?>

<div class="ciuis-body-content" ng-controller="Purchase_Controller">

	<div class="main-content container-fluid col-xs-12 col-md-12 col-lg-9">

		<md-toolbar class="toolbar-white">

			<div class="md-toolbar-tools">

				<md-button class="md-icon-button" aria-label="Purchase" ng-disabled="true">

					<md-icon><i class="ico-ciuis-invoices text-muted"></i></md-icon>

				</md-button>

				<h2 flex md-truncate><?php echo lang2('updated') . ' ' . lang2('purchase') ?></h2>

				<md-switch ng-model="purchase.purchase_status" aria-label="Status" ng-cloak><strong class="text-muted"><?php echo lang2('paid') ?></strong></md-switch>



				<md-switch ng-model="purchase.recurring_status" aria-label="recurring_status" ng-cloak>
					<strong class="text-muted"><?php echo lang2('recurring') ?></strong>
				</md-switch>


				<md-button ng-href="<?php echo base_url('purchases/purchase/{{purchase.id}}') ?>" class="md-icon-button" aria-label="View" ng-cloak>

					<md-tooltip md-direction="bottom"><?php echo lang2('view') ?></md-tooltip>

					<md-icon><i class="ion-eye text-muted"></i></md-icon>

				</md-button>

				<md-button ng-href="<?php echo base_url('purchases/purchase/{{purchase.id}}') ?>" class="md-icon-button" aria-label="Cancel" ng-cloak>

					<md-tooltip md-direction="bottom"><?php echo lang2('cancel') ?></md-tooltip>

					<md-icon><i class="ion-close-circled text-muted"></i></md-icon>

				</md-button>

				<?php if (check_privilege('purchases', 'edit')) { ?>

					<md-button ng-click="saveAll()" class="md-icon-button" aria-label="Save" ng-cloak>

						<md-tooltip md-direction="bottom"><?php echo lang2('save') ?></md-tooltip>

						<md-icon><i class="ion-checkmark-circled text-muted"></i></md-icon>

					</md-button>

				<?php } ?>

			</div>

		</md-toolbar>

		<div ng-show="purchaseLoader" layout-align="center center" class="text-center" id="circular_loader">

			<!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
			<img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">


			<p style="font-size: 15px;margin-bottom: 5%;">

				<span>

					<?php echo lang2('please_wait') ?> <br>

					<small><strong><?php echo lang2('loading') . ' ' . lang2('purchase') . '...' ?></strong></small>

				</span>

			</p>

		</div>

		<md-content ng-show="!purchaseLoader" class="bg-white" layout-padding ng-cloak>

			<div layout-gt-xs="row">
				<!--
				<md-input-container class="md-block" flex-gt-sm>
					<label><?php echo lang2('serie') ?></label>
					<input ng-model="purchase.serie" name="serie">
				</md-input-container>
				-->
				<md-input-container class="md-block" flex-gt-sm>

					<label><?php echo lang2('purchase_number') ?></label>

					<input ng-model="purchase.no" name="no">

				</md-input-container>

				<!--
				<md-input-container class="md-block" flex-gt-xs>
					<label><?php echo lang2('vendor'); ?></label>
					<md-select required placeholder="<?php echo lang2('choisevendor'); ?>" ng-model="purchase.vendor" name="vendor" style="min-width: 200px;">
						<md-option ng-value="vendor.id" ng-repeat="vendor in all_vendors">{{vendor.name}}</md-option>
					</md-select>
					<div ng-messages="userForm.vendor" role="alert" multiple>
						<div ng-message="required" class="my-message"><?php echo lang2('you_must_supply_a_customer') ?></div>
					</div>
				</md-input-container>
				-->

				<md-input-container class="md-block" flex-gt-xs>
					<label>Empresa Vendedora</label>
					<input type="text" ng-model="purchase.nm_vendor" placeholder="Empresa Vendedora">
				</md-input-container>

				<md-input-container class="md-block" flex-gt-xs>
					<label><?php echo lang2('dateofissuance') ?></label>
					<input type="date" name="created" ng-model="purchase.created_edit" md-open-on-focus></input>
				</md-input-container>

				<md-input-container class="md-block" flex-gt-xs>
					<label><?php echo lang2('duedate') ?></label>
					<input type="date" name="duedate" ng-model="purchase.duedate_edit" md-open-on-focus></input>
				</md-input-container>


			</div>

			<div ng-show="purchase.recurring_status" layout-gt-xs="row">

				<md-input-container class="md-block" flex-gt-xs>
					<label><?php echo lang2('recurring_type') ?></label>
					<md-select ng-model="purchase.recurring_type" name="recurring_type">
						<md-option value="0"><?php echo lang2('days') ?></md-option>
						<md-option value="1" selected><?php echo lang2('weeks') ?></md-option>
						<md-option value="2"><?php echo lang2('months') ?></md-option>
						<md-option value="3"><?php echo lang2('years') ?></md-option>
					</md-select>
				</md-input-container>

				<md-input-container>
					<label><?php echo lang2('ends_on') ?></label>
					<input type="date" name="recurring_endDate" ng-model="purchase.recurring_endDate" md-open-on-focus></input>
				</md-input-container>

			</div>
		</md-content>

		<md-content ng-show="!purchaseLoader" class="bg-white" layout-padding ng-cloak>

			<md-list-item ng-repeat="item in purchase.items">

				<div layout-gt-sm="row">

					<md-input-container class="md-block" flex-gt-sm style="min-width: 30px;">
						<label><?php echo lang2('productservice'); ?></label>
						<input class="min_input_width" ng-model="item.name">
					</md-input-container>

					<md-input-container class="md-block">

						<label><?php echo lang2('description'); ?></label>



						<textarea class="min_input_width" ng-model="item.description"></textarea>

						<bind-expression ng-init="selectedProduct.description = item.description" expression="selectedProduct.description" ng-model="item.description" />

						<input class="min_input_width" type="hidden" ng-model="item.product_id">

						<bind-expression ng-init="selectedProduct.product_id = item.product_id" expression="selectedProduct.product_id" ng-model="item.product_id" />

						<input class="min_input_width" type="hidden" ng-model="item.code" ng-value="selectedProduct.code">

						<bind-expression ng-init="selectedProduct.code = item.code" expression="selectedProduct.code" ng-model="item.code" />

					</md-input-container>

					<md-input-container class="md-block" flex-gt-sm style="min-width: 40px;">

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

						<bind-expression ng-init="selectedProduct.price = item.price" expression="selectedProduct.price" ng-model="item.price" />

					</md-input-container>

					<md-input-container class="md-block" flex-gt-sm style="min-width: 50px;">

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

					<?php if (check_privilege('purchases', 'edit')) { ?>

						<md-button ng-click="add()" class="md-fab pull-left" ng-disabled="false" aria-label="Add Line">

							<md-icon class="ion-plus-round text-muted"></md-icon>

						</md-button>

					<?php } ?>

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

	<div class="main-content container-fluid col-md-3 md-pl-0">

		<md-toolbar class="toolbar-white">

			<div class="md-toolbar-tools">

				<h2 flex md-truncate class="pull-left" ng-show="purchase.balance != 0"><strong><?php echo lang2('balance') ?> : <span ng-bind-html="purchase.balance | currencyFormat:cur_code:null:true:cur_lct"></span></strong></h2>

				<h2 flex md-truncate class="pull-left text-success" ng-hide="purchase.balance != 0" ng-cloak><strong><?php echo lang2('paidinv') ?></strong></h2>

				<md-button ng-hide="purchase.partial_is != 'true'" class="md-icon-button" aria-label="Partial" ng-cloak>

					<md-tooltip md-direction="bottom"><?php echo lang2('partial') ?></md-tooltip>

					<md-icon><i class="ion-pie-graph text-muted"></i></md-icon>

				</md-button>

				<md-button ng-hide="purchase.balance != 0" class="md-icon-button" aria-label="Paid" ng-cloak>

					<md-tooltip md-direction="bottom"><?php echo lang2('paid') ?></md-tooltip>

					<md-icon><i class="ion-checkmark-circled text-success"></i></md-icon>

				</md-button>

			</div>

		</md-toolbar>

		<md-content class="bg-white" style="border-bottom:1px solid #e0e0e0;" ng-cloak>

			<md-list flex>

				<md-list-item>

					<md-icon class="ion-ios-bell"></md-icon>

					<p ng-bind="purchase.duedate_text"></p>

				</md-list-item>

				<md-divider></md-divider>

				<md-list-item>

					<md-icon class="ion-android-mail"></md-icon>

					<p ng-bind="purchase.mail_status"></p>

				</md-list-item>

				<md-divider></md-divider>

				<md-list-item>

					<md-icon class="ion-person"></md-icon>

					<p><a href="<?php echo $purchases['staff_id']; ?>"><b><?php echo $purchases['staffmembername']; ?></b></a></p>

				</md-list-item>

			</md-list>

		</md-content>

		<md-toolbar class="toolbar-white">

			<div class="md-toolbar-tools">

				<h2 flex md-truncate class="text-bold"><?php echo lang2('payments'); ?><br><small flex md-truncate><?php echo lang2('paymentssidepurchase'); ?></small></h2>

				<md-button ng-show="purchase.balance != 0" ng-click="RecordPayment()" class="md-icon-button" aria-label="Record Payment" ng-cloak>

					<md-tooltip md-direction="left"><?php echo lang2('recordpayment') ?></md-tooltip>

					<md-icon><i class="ion-plus-round text-muted"></i></md-icon>

				</md-button>

			</div>

		</md-toolbar>

		<md-content class="bg-white" ng-cloak>

			<md-content ng-show="!purchase.payments.length" class="md-padding no-item-payment bg-white"></md-content>

			<md-list flex>

				<md-list-item class="md-2-line" ng-repeat="payment in purchase.payments">

					<md-icon class="ion-arrow-down-a text-muted"></md-icon>

					<div class="md-list-item-text">

						<h3 ng-bind="payment.name"></h3>

						<p ng-bind-html="payment.amount | currencyFormat:cur_code:null:true:cur_lct"></p>

					</div>

					<md-button class="md-secondary md-primary md-fab md-mini md-icon-button" ng-click="doSecondaryAction($event)" ng-href="<?php echo base_url('expenses/receipt/{{payment.expense_id}}'); ?>" aria-label="call">

						<md-icon class="ion-ios-search-strong"></md-icon>

					</md-button>

					<md-divider></md-divider>

				</md-list-item>

			</md-list>

		</md-content>

	</div>

	<div id="remove{{purchase.id}}" tabindex="-1" role="dialog" class="modal fade">

		<div class="modal-dialog">

			<div class="modal-content">

				<div class="modal-header">

					<button type="button" data-dismiss="modal" aria-hidden="true" class="close"><span class="mdi mdi-close"></span></button>

				</div>

				<div class="modal-body">

					<div class="text-center">

						<div class="text-danger"><span class="modal-main-icon mdi mdi-close-circle-o"></span>

						</div>

						<h3><?php echo lang2('attention'); ?></h3>

						<p><?php echo lang2('inv_remove_msg'); ?></p>

						<div class="xs-mt-50"> <a type="button" data-dismiss="modal" class="btn btn-space btn-default"><?php echo lang2('cancel'); ?></a> <a href="<?php echo site_url('purchases/remove/' . $purchases['id']); ?>" type="button" class="btn btn-space btn-danger"><?php echo lang2('delete'); ?></a> </div>

					</div>

				</div>

				<div class="modal-footer"></div>

			</div>

		</div>

	</div>

	<md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="RecordPayment" ng-cloak style="width: 450px;">

		<md-toolbar class="md-theme-light" style="background:#262626">

			<div class="md-toolbar-tools">

				<md-button ng-click="close()" class="md-icon-button" aria-label="Close">

					<i class="ion-android-arrow-forward"></i>

				</md-button>

				<md-truncate><?php echo lang2('recordpayment') ?></md-truncate>

			</div>

		</md-toolbar>

		<md-content layout-padding="">

			<form name="projectForm">

				<md-content layout-padding>

					<md-input-container class="md-block">

						<label><?php echo lang2('datepayment') ?></label>

						<input mdc-datetime-picker="" date="true" time="true" type="text" id="datetime" placeholder="<?php echo lang2('chooseadate') ?>" show-todays-date="" minutes="true" show-icon="true" ng-model="date" class=" dtp-no-msclear dtp-input md-input">

					</md-input-container>

					<md-input-container class="md-block">

						<label><?php echo lang2('amount') ?></label>

						<input required type="number" name="amount" ng-model="amount" />

					</md-input-container>

					<md-input-container class="md-block">

						<label><?php echo lang2('description') ?></label>

						<textarea required name="not" ng-model="not" placeholder="Type something" class="form-control"></textarea>

					</md-input-container>

					<md-input-container class="md-block">

						<label><?php echo lang2('account'); ?></label>

						<md-select placeholder="<?php echo lang2('account'); ?>" ng-model="account" name="account" style="min-width: 200px;">

							<md-option ng-value="account.id" ng-repeat="account in accounts">{{account.name}}</md-option>

						</md-select>

					</md-input-container>

					<div class="form-group pull-right">

						<md-button ng-click="AddPayment()" class="md-raised md-primary ion-ios-paperplane" type="button">

							<span><?php echo lang2('save'); ?></span>

						</md-button>

					</div>

				</md-content>

			</form>

		</md-content>

	</md-sidenav>

	<script>
		var PURCHASEID = <?php echo $purchases['id']; ?>;

		var PURCHASVENDOR = <?php echo $purchases['vendor_id']; ?>;
	</script>

</div>



<?php include_once(APPPATH . 'Views/inc/footer.php'); ?>

<script src="<?php echo base_url('assets/js/purchases.js?v=1.2.1'); ?>"></script>