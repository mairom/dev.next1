<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>

<?php $appconfig = get_appconfig(); ?>

<div class="ciuis-body-content" ng-controller="Purchase_Controller">

  <div class="main-content container-fluid col-md-9">

    <md-toolbar class="toolbar-white">

      <div class="md-toolbar-tools">

        <md-button class="md-icon-button" aria-label="Settings" ng-disabled="true">

          <md-icon><i class="ico-ciuis-invoices text-muted"></i></md-icon>

        </md-button>

        <h2 flex md-truncate ng-bind="purchase.properties.purchase_id"></h2>

        <md-button ng-click="sendEmail()" class="md-icon-button" aria-label="Email" ng-cloak>

          <md-progress-circular ng-show="sendingEmail == true" md-mode="indeterminate" md-diameter="20">

          </md-progress-circular>

          <md-tooltip ng-hide="sendingEmail == true" md-direction="bottom" ng-bind="lang.send"></md-tooltip>

          <md-icon ng-hide="sendingEmail == true"><i class="mdi mdi-email text-muted"></i></md-icon>

        </md-button>

        <md-button ng-show="purchase.pdf_status == '0'" ng-click="GeneratePDF()" class="md-icon-button" aria-label="Pdf" ng-cloak>

          <md-tooltip md-direction="bottom"><?php echo lang2('pdf') ?></md-tooltip>

          <md-icon><i class="mdi mdi-collection-pdf text-muted"></i> </md-icon>

				</md-button>

				<md-button ng-show="purchase.pdf_status == '1'" ng-href="<?php echo base_url('purchases/download_pdf/'.$purchases['id'] )?>" class="md-icon-button" aria-label="Pdf" ng-cloak>

          <md-tooltip md-direction="bottom"><?php echo lang2('pdf') ?></md-tooltip>

          <md-icon><i class="mdi mdi-collection-pdf text-muted"></i> </md-icon>

        </md-button>

        <md-button ng-href="<?php echo base_url('purchases/print_/{{purchase.id}}') ?>" class="md-icon-button"

          aria-label="Print" ng-cloak>

          <md-tooltip md-direction="bottom"><?php echo lang2('print') ?></md-tooltip>

          <md-icon><i class="mdi mdi-print text-muted"></i></md-icon>

        </md-button>

        <?php if (check_privilege('purchases', 'create')) { ?>
						<md-button style="padding: 0px 10px;font-size: 11px;" ng-href="<?php echo base_url('purchases/create') ?>" class="btn-shadow d-inline-flex align-items-center btn2 btn-success" aria-label="New" ng-cloak>
							Nova Compra
							<md-tooltip md-direction="bottom"><?php echo lang2('create') ?></md-tooltip>
							<md-icon style="vertical-align: unset;"><i class="ion-android-add"></i></md-icon>
						</md-button>

					<?php } ?>

        <div class="btn-group btn-hspace pull-right" ng-cloak>

          <md-menu md-position-mode="target-right target">

            <md-button aria-label="Open demo menu" class="md-icon-button" ng-click="$mdMenu.open($event)">

              <md-icon><i class="ion-android-more-vertical text-muted"></i></md-icon>

            </md-button>

            <md-menu-content width="4">

              <?php if (check_privilege('purchases', 'edit')) { ?>

              <md-menu-item>

                <md-button aria-label="draft" ng-click="MarkAsDraft()">

                  <div layout="row" flex>

                    <p flex ng-bind="lang.markasdraft"></p>

                    <md-icon md-menu-align-target class="ion-document" style="margin: auto 3px auto 0;"></md-icon>

                  </div>

                </md-button>

              </md-menu-item>

              <md-menu-item>

                <md-button aria-label="cancelled" ng-click="MarkAsCancelled()">

                  <div layout="row" flex>

                    <p flex ng-bind="lang.markascancelled"></p>

                    <md-icon md-menu-align-target class="mdi mdi-close-circle-o" style="margin: auto 3px auto 0;">

                    </md-icon>

                  </div>

                </md-button>

              </md-menu-item>

              <md-menu-item>

                <md-button aria-label="update" ng-click="UpdateInvoice(purchase.id)">

                  <div layout="row" flex>

                    <p flex ng-bind="lang.update"></p>

                    <md-icon md-menu-align-target class="mdi mdi-edit" style="margin: auto 3px auto 0;"></md-icon>

                  </div>

                </md-button>

              </md-menu-item>

              <?php } if(check_privilege('purchases', 'delete')) {?>

              <md-menu-item>

                <md-button aria-label="delete" ng-click="Delete()">

                  <div layout="row" flex>

                    <p flex ng-bind="lang.delete"></p>

                    <md-icon md-menu-align-target class="ion-trash-b" style="margin: auto 3px auto 0;"></md-icon>

                  </div>

                </md-button>

              </md-menu-item>

              <?php }?>

            </md-menu-content>

          </md-menu>

        </div>

      </div>

    </md-toolbar>

    <div ng-show="purchaseLoader" layout-align="center center" class="text-center" id="circular_loader">

      <md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>

      <p style="font-size: 15px;margin-bottom: 5%;">

        <span>

          <?php echo lang2('please_wait') ?> <br>

          <small><strong><?php echo lang2('loading'). ' '. lang2('purchase').'...' ?></strong></small>

        </span>

      </p>

    </div>

    <md-content ng-show="!purchaseLoader" class="bg-white invoice" ng-cloak>

      <div class="invoice-header col-md-12">

      <div class="invoice-to col-md-12 col-xs-12">
          <p>Vencimento: <b ng-bind="purchase.duedate"></b></p>
        </div>


        <div class="invoice-from col-md-4 col-xs-12">

          <small><?php echo  lang2('from'); ?></small>

          <address class="m-t-5 m-b-5">

            <strong ng-bind="settings.company"></strong><br>
            <span ng-bind="settings.address"></span><br>
            <span ng-bind="settings.phone"></span><br>

          </address>

        </div>

        <div class="invoice-to col-md-4 col-xs-12">

          <small><?php echo  lang2('to'); ?></small>

          <address class="m-t-5 m-b-5">

            <strong ng-bind="purchase.properties.vendor_company"></strong><br>

            <span ng-bind="purchase.properties.vendor_address"></span><br>

            <span ng-bind="purchase.properties.vendor_phone"></span>

          </address>

        </div>

        <div class="invoice-date col-md-4 col-xs-12">

          <div class="date m-t-5" ng-bind="purchase.created | date : 'MMM d, y'"></div>

          <div class="invoice-detail">

            <span ng-bind="purchase.serie + purchase.no"></span><br>

          </div>

        </div>

      </div>

      <div class="invoice-content col-md-12 md-p-0 xs-p-0 sm-p-0 lg-p-0">

        <div class="table-responsive">

          <table class="table table-invoice">

            <thead>

              <tr>

                <th><?php echo lang2('product') ?></th>

                <th><?php echo lang2('quantity') ?></th>

                <th><?php echo lang2('price') ?></th>

                <th><?php echo $appconfig['tax_label'] ?></th>

                <th><?php echo lang2('discount')?></th>

                <th><?php echo lang2('total') ?></th>

              </tr>

            </thead>

            <tbody>

              <tr ng-repeat="item in purchase.items">

                <td><span ng-bind="item.name"></span><br><pre class="pre_view" ng-cloak>{{item.description}}</pre></td>

                <td ng-bind="item.quantity"></td>

                <td ng-bind-html="item.price | currencyFormat:cur_code:null:true:cur_lct"></td>

                <td ng-bind="item.tax + '%'"></td>

                <td ng-bind="item.discount + '%'"></td>

                <td ng-bind-html="item.total | currencyFormat:cur_code:null:true:cur_lct"></td>

              </tr>

            </tbody>

          </table>

        </div>

        <div class="invoice-price">

          <div class="invoice-price-left">

            <div class="invoice-price-row">

              <div class="sub-price">

                <small><?php echo lang2('subtotal') ?></small>

                <span ng-bind-html="purchase.sub_total | currencyFormat:cur_code:null:true:cur_lct"></span>

              </div>

              <div class="sub-price">

                <i class="ion-plus-round"></i>

              </div>

              <div class="sub-price">

                <small><?php echo lang2('tax') ?></small>

                <span ng-bind-html="purchase.total_tax | currencyFormat:cur_code:null:true:cur_lct"></span>

              </div>

              <div class="sub-price">

                <i class="ion-minus-round"></i>

              </div>

              <div class="sub-price">

                <small><?php echo lang2('discount') ?></small>

                <span ng-bind-html="purchase.total_discount | currencyFormat:cur_code:null:true:cur_lct"></span>

              </div>

            </div>

          </div>

          <div class="invoice-price-right">

            <small><?php echo lang2('total') ?></small>

            <span ng-bind-html="purchase.total | currencyFormat:cur_code:null:true:cur_lct"></span>

          </div>

        </div>

      </div>

    </md-content>

  </div>

  <div class="main-content container-fluid col-md-3 md-pl-0">

    <md-toolbar class="toolbar-white">

      <div class="md-toolbar-tools">

        <h2 flex md-truncate class="pull-left" ng-show="purchase.balance == 0"><strong><?php echo lang2('balance')?> :

            <span ng-bind-html="purchase.balance | currencyFormat:cur_code:null:true:cur_lct"></span></strong></h2>

        <h2 flex md-truncate class="pull-left text-success" ng-hide="purchase.balance != 0" ng-cloak>

          <strong><?php echo lang2('paidinv') ?></strong></h2>

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

    <md-content ng-show="!purchaseLoader" class="bg-white" style="border-bottom:1px solid #e0e0e0;">

      <md-list flex ng-cloak>

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

          <p><strong ng-bind="purchase.properties.purchase_staff"></strong></p>

        </md-list-item>

      </md-list>

    </md-content>

    <md-toolbar class="toolbar-white">

      <div class="md-toolbar-tools">

        <h2 flex md-truncate class="text-bold"><?php echo lang2('payments'); ?><br><small flex

            md-truncate><?php echo lang2('paymentssidepurchase'); ?></small></h2>

        <?php if (check_privilege('purchases', 'edit')) { ?>

        <md-button ng-show="purchase.balance != 0" ng-click="RecordPayment()" class="md-icon-button"

          aria-label="Record Payment" ng-cloak>

          <md-tooltip md-direction="left"><?php echo lang2('recordpayment') ?></md-tooltip>

          <md-icon><i class="ion-plus-round text-muted"></i></md-icon>

        </md-button>

        <?php }?>

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

          <md-button class="md-secondary md-primary md-fab md-mini md-icon-button" ng-click="doSecondaryAction($event)"

            ng-href="<?php echo base_url('expenses/receipt/{{payment.expense_id}}');?>" aria-label="call">

            <md-icon class="ion-ios-search-strong"></md-icon>

          </md-button>

          <md-divider></md-divider>

        </md-list-item>

      </md-list>

    </md-content>

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

            <input mdc-datetime-picker="" date="true" time="true" type="text" id="datetime"

              placeholder="<?php echo lang2('chooseadate') ?>" show-todays-date="" minutes="true" show-icon="true"

              ng-model="date" class=" dtp-no-msclear dtp-input md-input">

          </md-input-container>

          <md-input-container class="md-block">

            <label><?php echo lang2('amount') ?></label>

            <input required type="number" name="amount" ng-model="amount"/>

          </md-input-container>

          <md-input-container class="md-block">

            <label><?php echo lang2('description') ?></label>

            <textarea required name="not" ng-model="not" placeholder="Type something" class="form-control"></textarea>

          </md-input-container>

          <md-input-container class="md-block">

            <label><?php echo lang2('account'); ?></label>

            <md-select placeholder="<?php echo lang2('account'); ?>" ng-model="account" name="account"

              style="min-width: 200px;">

              <md-option ng-value="account.id" ng-repeat="account in accounts">{{account.name}}</md-option>

            </md-select>

          </md-input-container>

          <div class="form-group pull-right">

            <?php if (check_privilege('purchases', 'edit')) { ?>

            <md-button ng-click="AddPayment()" class="md-raised md-primary ion-ios-paperplane" type="button">

              <span><?php echo lang2('save');?></span>

            </md-button>

            <?php }?>

          </div>

        </md-content>

      </form>

    </md-content>

  </md-sidenav>

  <script>

  var PURCHASEID = <?php echo $purchases['id']; ?>;

  var PURCHASVENDOR = <?php echo $purchases['vendor_id']; ?>;

  </script>

  <script type="text/ng-template" id="generate-purchase.html">

    <md-dialog aria-label="options dialog">

	<md-dialog-content layout-padding class="text-center">

		<md-content class="bg-white" layout-padding>

			<h2 class="md-title" ng-hide="PDFCreating == true"><?php echo lang2('generate').' '.lang2('purchase').' '.lang2('pdf') ?></h2>

			<h2 class="md-title" ng-if="PDFCreating == true"><?php echo lang2('report_generating') ?></h2>

			<span ng-hide="PDFCreating == false"><?php echo lang2('generate_pdf_purchase_msg') ?></span><br><br>

			<span ng-if="PDFCreating == false"><?php echo lang2('generate_pdf_last_msg') ?></span><br><br>

			<img ng-if="PDFCreating == true" ng-src="<?php echo base_url('assets/img/loading_time.gif') ?>" alt="">

			<a ng-if="PDFCreating == false" href="<?php echo base_url('purchases/download_pdf/'.$purchases['id'].'') ?>" ><img  width="30%" ng-src="<?php echo base_url('assets/img/download_pdf.png') ?>" alt=""></a>

		</md-content>

	</md-dialog-content>

	<md-dialog-actions>

		<span flex></span>

		<md-button class="text-success" ng-if="PDFCreating == false" href="<?php echo base_url('purchases/download_pdf/'.$purchases['id'].'') ?>">

      <?php echo lang2('download') ?>

    </md-button>

		<md-button class="text-success" ng-hide="PDFCreating == false" ng-click="CreatePDF()"><?php echo lang2('create') ?>!</md-button>

		<md-button class="text-danger" ng-click="CloseModal()"><?php echo lang2('cancel') ?>!</md-button>

	</md-dialog-actions>

  </md-dialog>

</script>

</div>

<script>

var lang = {};

lang.doIt = '<?php echo lang2('doIt')?>';

lang.cancel = '<?php echo lang2('cancel')?>';

lang.attention = '<?php echo lang2('attention')?>';

lang.delete_meesage = "<?php echo lang2('delete_meesage').''.lang2('purchase').'.'?>";

</script>

<?php include_once( APPPATH . 'Views/inc/footer.php' );?>

<script src="<?php echo base_url('assets/js/purchases.js'); ?>"></script>