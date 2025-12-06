<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>

<?php $appconfig = get_appconfig(); ?>
<style>
  @media only screen and (min-width: 700px) {
    .contentArqv {
      float: right;
      margin-top: 5px;
    }
  }
</style>

<div class="ciuis-body-content" ng-controller="Invoice_Controller">

  <div class="main-content container-fluid col-md-9">

    <!-- <div ng-show="invoiceLoader" layout-align="center center" class="text-center" id="circular_loader">

          <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular>

            <p style="font-size: 15px;margin-bottom: 5%;">

             <span>

                <?php echo lang2('please_wait') ?> <br>

               <small><strong><?php echo lang2('loading') . ' ' . lang2('invoice') . '...' ?></strong></small>

             </span>

           </p>

         </div> -->

    <md-toolbar ng-show="!invoiceLoader" class="toolbar-white">

      <div class="md-toolbar-tools" style="padding-top: 40px;">

        <md-button class="md-icon-button" aria-label="Settings" ng-disabled="true" ng-cloak>

          <md-icon><i class="ico-ciuis-invoices text-muted"></i></md-icon>

        </md-button>

        <h2 flex md-truncate ng-bind="invoice.properties.invoice_number"></h2>

        <md-button ng-click="Discussions()" class="md-icon-button" aria-label="Discussions" ng-cloak>

          <md-tooltip md-direction="bottom"><?php echo lang2('discussions') ?></md-tooltip>

          <md-icon><i class="mdi ion-chatboxes text-muted"></i></md-icon>

        </md-button> <!-- ng-href="<?php //echo base_url('invoices/send_email/{{invoice.id}}')
                                    ?>" -->

        <md-button ng-click="sendEmail()" class="md-icon-button" aria-label="Email" ng-cloak>

          <md-progress-circular ng-show="sendingEmail == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

          <md-tooltip ng-hide="sendingEmail == true" md-direction="bottom" ng-bind="lang.send"></md-tooltip>

          <md-icon ng-hide="sendingEmail == true"><i class="mdi mdi-email text-muted"></i></md-icon>

        </md-button>



        <md-button ng-show="invoice.pdf_status == '0'" ng-click="GeneratePDF()" class="md-icon-button" aria-label="Pdf" ng-cloak>

          <md-tooltip md-direction="bottom"><?php echo lang2('pdf') ?></md-tooltip>

          <md-icon><i class="mdi mdi-collection-pdf text-muted"></i> </md-icon>

        </md-button>

        <md-button ng-show="invoice.pdf_status == '1'" ng-href="<?php echo base_url('invoices/download_pdf/' . $invoices['id']) ?>" class="md-icon-button" aria-label="Pdf" ng-cloak>

          <md-tooltip md-direction="bottom"><?php echo lang2('pdf') ?></md-tooltip>

          <md-icon><i class="mdi mdi-collection-pdf text-muted"></i> </md-icon>

        </md-button>

        <md-button ng-href="<?php echo base_url('invoices/print_/{{invoice.id}}') ?>" class="md-icon-button" aria-label="Print" ng-cloak>

          <md-tooltip md-direction="bottom" ng-bind="lang.print"></md-tooltip>

          <md-icon><i class="mdi mdi-print text-muted"></i></md-icon>

        </md-button>

        <?php if (check_privilege('invoices', 'create')) { ?>


          <md-button style="padding: 0px 10px;font-size: 11px;" ng-href="<?php echo base_url('invoices/create') ?>" class="btn-shadow d-inline-flex align-items-center btn2 btn-success" aria-label="New" ng-cloak>
            Nova fatura
            <md-tooltip md-direction="bottom"><?php echo lang2('create') ?></md-tooltip>
            <md-icon style="vertical-align: unset;"><i class="ion-android-add"></i></md-icon>
          </md-button>

        <?php } ?>

        <?php if (check_privilege('invoices', 'edit') || check_privilege('invoices', 'delete')) { ?>

          <md-menu md-position-mode="target-right target" ng-cloak style="margin: 0;">

            <md-button aria-label="Open demo menu" class="md-icon-button" ng-click="$mdMenu.open($event)">

              <md-icon><i class="ion-android-more-vertical text-muted"></i></md-icon>

            </md-button>

            <md-menu-content width="4">

              <?php if (check_privilege('invoices', 'edit')) { ?>

                <md-menu-item>

                  <md-button ng-click="MarkAsDraft()">

                    <div layout="row" flex>

                      <p flex ng-bind="lang.markasdraft"></p>

                      <md-icon md-menu-align-target class="ion-document" style="margin: auto 3px auto 0;"></md-icon>

                    </div>

                  </md-button>

                </md-menu-item>

                <md-menu-item>

                  <md-button ng-click="MarkAsCancelled()">

                    <div layout="row" flex>

                      <p flex ng-bind="lang.markascancelled"></p>

                      <md-icon md-menu-align-target class="mdi mdi-close-circle-o" style="margin: auto 3px auto 0;"></md-icon>

                    </div>

                  </md-button>

                </md-menu-item>

                <md-menu-item>

                  <md-button ng-click="UpdateInvoice(invoice.id)">

                    <div layout="row" flex>

                      <p flex ng-bind="lang.update"></p>

                      <md-icon md-menu-align-target class="mdi mdi-edit" style="margin: auto 3px auto 0;"></md-icon>

                    </div>

                  </md-button>

                </md-menu-item>

              <?php }
              if (check_privilege('invoices', 'delete')) { ?>

                <md-menu-item>

                  <md-button ng-click="Delete()">

                    <div layout="row" flex>

                      <p flex ng-bind="lang.delete"></p>

                      <md-icon md-menu-align-target class="ion-trash-b" style="margin: auto 3px auto 0;"></md-icon>

                    </div>

                  </md-button>

                </md-menu-item>

              <?php } ?>


            </md-menu-content>

          </md-menu>

        <?php } ?>

      </div>

    </md-toolbar>

    <md-content ng-show="!invoiceLoader" class="bg-white invoice">

      <div class="invoice-header col-md-12">
        <div class="invoice-to col-md-12 col-xs-12">
          <p>Vencimento: <b ng-bind="invoice.duedate"></b></p>
        </div>



        <div class="invoice-from col-md-4 col-xs-12"> <small class="text-uppercase" ng-bind="lang.from"></small>

          <address class="m-t-5 m-b-5">

            <strong ng-bind="settings.company"></strong><br>

            <span ng-bind="settings.address"></span><br>

            <span ng-bind="settings.phone"></span><br>

          </address>

        </div>


        <div class="invoice-to col-md-4 col-xs-12">
          <small class="text-uppercase" ng-bind="lang.to"></small>

          <address class="m-t-5 m-b-5">

            <strong ng-bind="invoice.properties.customer"></strong><br>

            <span ng-bind="invoice.properties.customer_address"></span><br>

            <span ng-bind="invoice.properties.customer_phone"></span>

          </address>

        </div>

        <div class="invoice-date col-md-4 col-xs-12">

          <div class="date m-t-5" ng-bind="invoice.created | date : 'MMM d, y'"></div>

          <div class="invoice-detail"> <span ng-bind="invoice.serie + invoice.no"></span><br>

          </div>

        </div>

      </div>

      <div class="invoice-content col-md-12 md-p-0 xs-p-0 sm-p-0 lg-p-0">

        <div class="table-responsive">
          <table class="table table-invoice">
            <thead>
              <tr>
                <th ng-bind="lang.product"></th>
                <th ng-bind="lang.quantity"></th>
                <th ng-bind="lang.price"></th>
                <th ng-bind="lang.discount"></th>
                <th ng-bind="lang.total"></th>
              </tr>
            </thead>

            <tbody>
              <tr ng-repeat="item in invoice.items">
                <td><span ng-bind="item.name"></span><br>
                  <pre class="pre_view" ng-cloak>{{item.description}}</pre>
                </td>
                <td ng-bind="item.quantity"></td>
                <td ng-bind-html="item.price | currencyFormat:cur_code:null:true:cur_lct"></td>
                <td ng-bind="item.discount + '%'"></td>
                <td ng-bind-html="item.total | currencyFormat:cur_code:null:true:cur_lct"></td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="invoice-price">

          <div class="invoice-price-left">

            <div class="invoice-price-row">

              <div class="sub-price"> <small ng-bind="lang.subtotal"></small>
                <span ng-bind-html="invoice.sub_total | currencyFormat:cur_code:null:true:cur_lct"></span>
              </div>

              <div class="sub-price"> <i class="ion-plus-round"></i> </div>

              <div class="sub-price"> <i class="ion-minus-round"></i> </div>

              <div class="sub-price"> <small ng-bind="lang.discount"></small>
                <span ng-bind-html="invoice.total_discount | currencyFormat:cur_code:null:true:cur_lct"></span>
              </div>

            </div>

          </div>

          <div class="invoice-price-right"> <small ng-bind="lang.total"></small>
            <span ng-bind-html="invoice.total | currencyFormat:cur_code:null:true:cur_lct"></span>
          </div>

        </div>

      </div>

    </md-content>

  </div>

  <div ng-show="!invoiceLoader" class="main-content container-fluid col-md-3 md-pl-0" ng-cloak>
    <md-toolbar class="toolbar-white">

      <div class="md-toolbar-tools">

        <h2 flex md-truncate class="pull-left" ng-show="invoice.balance != 0 && invoice.status_id != 4"><strong><span ng-bind="lang.balance"></span> : <span ng-bind-html="invoice.balance | currencyFormat:cur_code:null:true:cur_lct"></span></strong></h2>

        <h2 flex md-truncate class="pull-left text-success" ng-hide="invoice.balance != 0"><strong ng-bind="lang.paidinv"></strong></h2>

        <h2 flex md-truncate class="pull-left text-danger text-uppercase" ng-show="invoice.status_id == 4"><strong ng-bind="lang.cancelled"></strong></h2>

        <md-button ng-hide="invoice.partial_is != true" class="md-icon-button" aria-label="Partial">

          <md-tooltip md-direction="bottom" ng-bind="lang.partial"></md-tooltip>

          <md-icon><i class="ion-pie-graph text-muted"></i></md-icon>

        </md-button>

        <md-button ng-hide="invoice.balance != 0" class="md-icon-button" aria-label="Paid">

          <md-tooltip md-direction="bottom" ng-bind="lang.paid"></md-tooltip>

          <md-icon><i class="ion-checkmark-circled text-success"></i></md-icon>

        </md-button>

      </div>

    </md-toolbar>

    <md-content class="bg-white" style="border-bottom:1px solid #e0e0e0;">

      <md-list flex>

        <md-list-item>

          <md-icon class="ion-ios-bell"></md-icon>

          <p ng-bind="invoice.duedate_text"></p>

        </md-list-item>

        <md-divider></md-divider>

        <md-list-item>

          <md-icon class="ion-android-mail"></md-icon>

          <p ng-bind="invoice.mail_status"></p>

        </md-list-item>

        <md-divider></md-divider>

        <md-list-item>

          <md-icon class="ion-person"></md-icon>

          <p><strong ng-bind="invoice.properties.invoice_staff"></strong></p>

        </md-list-item>

      </md-list>

      <md-subheader ng-if="custom_fields.length > 0"><?php echo lang2('custom_fields'); ?></md-subheader>

      <md-list-item ng-if="custom_fields" ng-repeat="field in custom_fields">

        <md-icon class="{{field.icon}} material-icons"></md-icon>

        <strong flex md-truncate>{{field.name}}</strong>

        <p ng-if="field.type === 'input'" class="text-right" flex md-truncate ng-bind="field.data"></p>

        <p ng-if="field.type === 'textarea'" class="text-right" flex md-truncate ng-bind="field.data"></p>

        <p ng-if="field.type === 'date'" class="text-right" flex md-truncate ng-bind="field.data | date:'dd, MMMM yyyy EEEE'"></p>

        <p ng-if="field.type === 'select'" class="text-right" flex md-truncate ng-bind="custom_fields[$index].selected_opt.name"></p>

        <md-divider ng-if="custom_fields"></md-divider>

      </md-list-item>

    </md-content>

    <?php if (check_privilege('invoices', 'edit')) { ?>

      <md-toolbar class="toolbar-white">

        <div class="md-toolbar-tools">

          <h2 flex md-truncate class="text-bold"><?php echo lang2('payments'); ?><br>

            <small flex md-truncate><?php echo lang2('paymentsside'); ?></small>

          </h2>

          <md-button ng-show="invoice.balance != 0 && invoice.status_id != 4" ng-click="RecordPayment()" class="md-icon-button" aria-label="Record Payment">

            <md-tooltip md-direction="left"><?php echo lang2('recordpayment') ?></md-tooltip>

            <md-icon><i class="ion-android-add-circle text-success"></i></md-icon>

          </md-button>

        </div>

      </md-toolbar>

      <md-content class="bg-white">

        <md-content ng-show="!invoice.payments.length" class="md-padding no-item-payment bg-white"></md-content>

        <md-list flex>

          <md-list-item class="md-2-line" ng-repeat="payment in invoice.payments">

            <md-icon class="ion-arrow-down-a text-muted"></md-icon>

            <div class="md-list-item-text">

              <h3 ng-bind="payment.name"></h3>

              <p ng-bind-html="payment.amount | currencyFormat:cur_code:null:true:cur_lct"></p>

            </div>

            <md-button class="md-secondary md-primary md-fab md-mini md-icon-button" ng-click="doSecondaryAction($event)" aria-label="call">

              <md-icon class="ion-ios-search-strong"></md-icon>

            </md-button>

            <md-divider></md-divider>

          </md-list-item>

        </md-list>

      </md-content>

    <?php } ?>

  </div>

  <div ng-show="!invoiceLoader" class="main-content container-fluid col-md-3 md-pl-0 contentArqv" ng-cloak>
    <md-toolbar class="toolbar-white">
      <div class="md-toolbar-tools">
        <md-button class="md-icon-button" aria-label="" ng-disabled="true" ng-cloak>
          <md-icon><i class="ion-document text-muted"></i></md-icon>
        </md-button>

        <h2 flex md-truncate><?php echo lang2('files') ?></h2>
        <?php if (check_privilege('invoices', 'edit')) { ?>
          <md-button ng-click="UploadFile()" class="md-icon-button md-primary" aria-label="Add File" ng-cloak>
            <md-tooltip md-direction="bottom"><?php echo lang2('upload_new_file') ?></md-tooltip>
            <md-icon class="ion-plus-round add-file"></md-icon>
          </md-button>
        <?php } ?>
      </div>
    </md-toolbar>

    <div ng-show="invoicesFiles" layout-align="center center" class="text-center" id="circular_loader">
      <!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
      <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">

      <p style="font-size: 15px;margin-bottom: 5%;">
        <span><?php echo lang2('please_wait') ?> <br>
          <small><strong><?php echo lang2('loading') . ' ' . lang2('invoice_files') . '...' ?></strong></small></span>
      </p>
    </div>

    <md-content ng-show="!invoicesFiles" class="bg-white">
      <md-list flex ng-cloak>
        <md-list-item class="md-2-line" ng-repeat="file in files | pagination : currentPage*itemsPerPage | limitTo: 6">
          <div class="md-list-item-text image-preview">
            <a ng-if="file.type == 'image'" class="cursor" ng-click="ViewFile($index, image)">
              <md-tooltip md-direction="left"><?php echo lang2('preview') ?></md-tooltip>
              <img src="{{file.path}}">
            </a>

            <a ng-if="(file.type == 'archive')" class="cursor" ng-href="<?php echo base_url('invoices/download_file/{{file.id}}'); ?>">
              <md-tooltip md-direction="left"><?php echo lang2('download') ?></md-tooltip>
              <img src="<?php echo base_url('assets/img/zip_icon.png'); ?>">
            </a>

            <a ng-if="(file.type == 'file')" class="cursor" ng-href="<?php echo base_url('invoices/download_file/{{file.id}}'); ?>">
              <md-tooltip md-direction="left"><?php echo lang2('download') ?></md-tooltip>
              <img src="<?php echo base_url('assets/img/file_icon.png'); ?>">
            </a>

            <a ng-if="file.type == 'pdf'" class="cursor" ng-href="<?php echo base_url('invoices/download_file/{{file.id}}'); ?>">
              <md-tooltip md-direction="left"><?php echo lang2('download') ?></md-tooltip>
              <img src="<?php echo base_url('assets/img/pdf_icon.png'); ?>">
            </a>
          </div>

          <div class="md-list-item-text">
            <a class="cursor" ng-href="<?php echo base_url('invoices/download_file/{{file.id}}'); ?>">
              <h3 class="link" ng-bind="file.file_name"></h3>
            </a>
          </div>

          <?php if (check_privilege('invoices', 'delete')) { ?>
            <md-button class="md-secondary md-primary md-fab md-mini md-icon-button" ng-click='DeleteFile(file.id)' aria-label="call">
              <md-icon class="ion-trash-b"></md-icon>
            </md-button>
          <?php } ?>
          <md-divider></md-divider>
        </md-list-item>
        <div ng-show="!files.length" class="text-center">
          <img style="height: 140px;" src="<?php echo base_url('assets/img/nofiles2.png') ?>" alt="">
        </div>
      </md-list>

      <div ng-show="files.length>6 && !invoicesFiles" class="pagination-div" ng-cloak>
        <ul class="pagination">
          <li ng-class="DisablePrevPage()"> <a href ng-click="prevPage()"><i class="ion-ios-arrow-back"></i></a> </li>
          <li ng-repeat="n in range()" ng-class="{active: n == currentPage}" ng-click="setPage(n)"> <a href="#" ng-bind="n+1"></a> </li>
          <li ng-class="DisableNextPage()"> <a href ng-click="nextPage()"><i class="ion-ios-arrow-right"></i></a> </li>
        </ul>
      </div>
    </md-content>

  </div>


  <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="RecordPayment" ng-cloak style="width: 450px;">

    <md-toolbar class="toolbar-white" style="background:#262626">

      <div class="md-toolbar-tools">

        <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward" ng-cloak></i> </md-button>

        <md-truncate><?php echo lang2('recordpayment') ?></md-truncate>

      </div>

    </md-toolbar>

    <md-content layout-padding="">

      <form name="InvoiceRecordPayment">

        <md-content layout-padding>

          <md-input-container class="md-block">
            <label><?php echo lang2('datepayment') ?></label>

            <input time="false" date="true" mdc-datetime-picker="" type="text" id="datetime" day-of-week-len="3" show-icon="true" placeholder="<?php echo lang2('chooseadate') ?>" click-outside-to-close="true" show-todays-date="" today-btn="false" ng-model="date" min-date="minDate" max-date="maxDate" class=" md-input" ng-model="date">
          </md-input-container>

          <md-input-container class="md-block">

            <label><?php echo lang2('amount') ?></label>

            <input required type="number" name="amount" ng-model="amount" />

          </md-input-container>

          <md-input-container class="md-block">
            <label><?php echo lang2('description') ?></label>
            <textarea name="not" ng-model="not" placeholder="<?php echo lang2('typeSomething'); ?>" class="form-control"></textarea>
          </md-input-container>

          <md-input-container class="md-block">

            <label><?php echo lang2('account'); ?></label>

            <md-select placeholder="<?php echo lang2('account'); ?>" ng-model="account" name="account" style="min-width: 200px;">

              <md-option ng-value="account.id" ng-repeat="account in accounts">{{account.name}}</md-option>

            </md-select>

          </md-input-container>

          <section layout="row" layout-sm="column" layout-align="center right" layout-wrap>

            <md-button ng-click="AddPayment()" class="md-raised md-primary pull-right template-button" ng-disabled="doing == true">

              <span ng-hide="doing == true"><?php echo lang2('save'); ?></span>

              <md-progress-circular class="white" ng-show="doing == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

            </md-button>

            <!-- <md-button ng-click="AddPayment()" class="md-raised md-primary pull-right" ng-bind="lang.save"></md-button> -->

          </section>

        </md-content>

      </form>

    </md-content>

  </md-sidenav>

  <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Discussions" ng-cloak style="width: 450px;">

    <md-toolbar class="toolbar-white">

      <div class="md-toolbar-tools">

        <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>

        <h2 flex md-truncate><?php echo lang2('discussions'); ?></h2>

        <md-button ng-click="NewDiscussion()" class="md-icon-button" aria-label="Record Payment">

          <md-tooltip md-direction="left"><?php echo lang2('new_disscussion'); ?></md-tooltip>

          <md-icon><i class="ion-plus-round text-muted"></i></md-icon>

        </md-button>

      </div>

    </md-toolbar>

    <md-content class="bg-white">

      <md-list flex>

        <md-list-item class="md-2-line" ng-repeat="discussion in discussions" ng-click="Discussion_Detail($index)" aria-label="Discussion Detail">

          <div data-letter-avatar="--" class="ticket-area-av-im2 md-avatar"></div>

          <div class="md-list-item-text" ng-class="{'md-offset': phone.options.offset }">

            <h3 ng-bind="discussion.subject"></h3>

            <p ng-bind="discussion.contact"></p>

          </div>

          <md-divider></md-divider>

        </md-list-item>

      </md-list>

    </md-content>

  </md-sidenav>

  <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="NewDiscussion" ng-cloak style="width: 450px;">

    <md-toolbar class="toolbar-white">

      <div class="md-toolbar-tools">

        <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>

        <h2 flex md-truncate><?php echo lang2('new_disscussion'); ?></h2>

        <md-switch ng-model="ShowCustomer" aria-label="Type"><strong class="text-muted"><?php echo lang2('show_customer'); ?></strong></md-switch>

      </div>

    </md-toolbar>

    <md-content layout-padding="">

      <md-content layout-padding>

        <md-input-container class="md-block">

          <label><?php echo lang2('subject') ?></label>

          <input required type="text" ng-model="new_discussion.subject" />

        </md-input-container>

        <md-input-container class="md-block">

          <label><?php echo lang2('description') ?></label>

          <textarea required ng-model="new_discussion.description" placeholder="<?php echo lang2('typeSomething'); ?>" class="form-control"></textarea>

        </md-input-container>

        <md-input-container class="md-block">

          <label><?php echo lang2('contact'); ?></label>

          <md-select placeholder="<?php echo lang2('contact'); ?>" ng-model="new_discussion.contact_id" name="contact" style="min-width: 200px;">

            <md-option ng-value="contact.id" ng-repeat="contact in contacts">{{contact.name}}</md-option>

          </md-select>

        </md-input-container>

        <div class="form-group pull-right">

          <button ng-click="CreateDiscussion()" class="btn btn-warning btn-xl ion-ios-paperplane"> <?php echo lang2('create') ?></button>

        </div>

      </md-content>

    </md-content>

  </md-sidenav>

  <div style="visibility: hidden">

    <div ng-repeat="discussion in discussions" class="md-dialog-container" id="Discussion_Detail-{{discussion.id}}">

      <md-dialog aria-label="Discussion_Detail">

        <md-toolbar class="toolbar-white">

          <div class="md-toolbar-tools">

            <h2>{{discussion.subject}} by {{discussion.contact}}</h2>

            <span flex></span>

            <md-button class="md-icon-button" ng-click="CloseModal()">

              <md-icon class="ion-close-round" aria-label="Close dialog" style="color:black"></md-icon>

            </md-button>

          </div>

        </md-toolbar>

        <md-dialog-content style="max-width:800px;max-height:810px; ">

          <md-content class="md-padding bg-white">

            <md-list flex>

              <md-list-item>

                <md-icon class="mdi mdi-calendar"></md-icon>

                <p><?php echo lang2('date') ?></p>

                <p class="md-secondary" ng-bind="discussion.datecreated | date : 'MMM d, y'"></p>

              </md-list-item>

              <md-divider></md-divider>

              <md-content class="bg-white" layout-padding>

                <p class="md-secondary" ng-bind="discussion.description"></p>

              </md-content>

              <md-divider></md-divider>

            </md-list>

            <md-content class="bg-white" layout-padding>

              <section class="ciuis-notes show-notes">

                <article ng-repeat="comment in discussion.comments" class="ciuis-note-detail">

                  <div class="ciuis-note-detail-img"> <img src="<?php echo base_url('assets/img/comment.png') ?>" alt="" width="50" height="50" /> </div>

                  <div class="ciuis-note-detail-body">

                    <div class="text">

                      <p ng-bind="comment.content"></p>

                    </div>

                    <p class="attribution"><?php echo lang2('repliedby'); ?> <strong><span ng-bind="comment.full_name"></span></strong> at <span ng-bind="comment.created"></span></p>

                  </div>

                </article>

              </section>

              <md-input-container class="md-block">

                <label><?php echo lang2('message') ?></label>

                <textarea required ng-model="discussion.newcontent" placeholder="<?php echo lang2('typeSomething'); ?>" class="form-control comment-description"></textarea>

              </md-input-container>

            </md-content>

          </md-content>

        </md-dialog-content>

        <md-dialog-actions layout="row">

          <md-button ng-click="AddComment($index)" style="margin-right:20px;"> <?php echo lang2('reply') ?> </md-button>

        </md-dialog-actions>

      </md-dialog>

    </div>

  </div>

  <script>
    var INVOICEID = <?php echo $invoices['id']; ?>;

    var INVOICECUSTOMER = <?php echo $invoices['customer_id']; ?>;
  </script>

  <script type="text/ng-template" id="generate-invoice.html">

    <md-dialog aria-label="options dialog">

	<md-dialog-content layout-padding class="text-center">

		<md-content class="bg-white" layout-padding>

			<h2 class="md-title" ng-hide="PDFCreating == true"><?php echo lang2('generate_pdf') ?></h2>

			<h2 class="md-title" ng-if="PDFCreating == true"><?php echo lang2('report_generating') ?></h2>

			<span ng-hide="PDFCreating == false"><?php echo lang2('generate_pdf_msg') ?></span><br><br>

			<span ng-if="PDFCreating == false"><?php echo lang2('generate_pdf_last_msg') ?></span><br><br>

			<img ng-if="PDFCreating == true" ng-src="<?php echo base_url('assets/img/loading_time.gif') ?>" alt="">

			<a ng-if="PDFCreating == false" href="<?php echo base_url('invoices/download_pdf/' . $invoices['id'] . '') ?>"><img  width="30%" ng-src="<?php echo base_url('assets/img/download_pdf.png') ?>" alt=""></a>

		</md-content>

	</md-dialog-content>

	<md-dialog-actions>

	  <span flex></span>

	  <md-button class="text-success" ng-if="PDFCreating == false" href="<?php echo base_url('invoices/download_pdf/' . $invoices['id'] . '') ?>">

      <?php echo lang2('download') ?>

    </md-button>

    <md-button class="text-success" ng-hide="PDFCreating == false" ng-click="CreatePDF()"><?php echo lang2('create') ?></md-button>

    <md-button class="text-danger" ng-click="CloseModal()"><?php echo lang2('cancel') ?></md-button>

	</md-dialog-actions>

  </md-dialog>

</script>

</div>

<script type="text/ng-template" id="addfile-template.html">
  <md-dialog aria-label="options dialog">
  <?php helper('form');echo form_open_multipart('invoices/add_file/' . $invoices['id'] . '', array("class" => "form-horizontal")); ?>
  <md-dialog-content layout-padding>
    <h2 class="md-title"><?php echo lang2('choosefile'); ?></h2>
    <input type="file" required name="file_name" file-model="project_file">
  </md-dialog-content>

  <md-dialog-actions>
    <span flex></span>
    <md-button ng-click="close()" aria-label="add"><?php echo lang2('cancel') ?>!</md-button>
    <md-button ng-click="uploadInvoiceFile()" class="template-button" ng-disabled="uploading == true">
      <span ng-hide="uploading == true"><?php echo lang2('upload'); ?></span>
      <md-progress-circular class="white" ng-show="uploading == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>
    </md-button>

  </md-dialog-actions>
  <?php echo form_close(); ?>
  </md-dialog>
</script>

<script type="text/ng-template" id="view_image.html">
  <md-dialog aria-label="options dialog">
  <?php helper('form');echo form_open_multipart('invoices/add_file/' . $invoices['id'] . '', array("class" => "form-horizontal")); ?>
  <md-dialog-content layout-padding>
    <?php $path = '{{file.path}}';
    if ($path) { ?>
      <img src="<?php echo $path ?>">
    <?php } ?>
  </md-dialog-content>
  <md-dialog-actions>
    <span flex></span>
    <?php if (check_privilege('invoices', 'delete')) { ?>
      <md-button ng-click='DeleteFiles(file.id)'><?php echo lang2('delete') ?>!</md-button>
    <?php } ?>
    <md-button ng-href="<?php echo base_url('invoices/download_file/') ?>{{file.id}}"><?php echo lang2('download') ?>!</md-button>
    <md-button ng-click="close()"><?php echo lang2('cancel') ?>!</md-button>
  </md-dialog-actions>
  <?php echo form_close(); ?>
  </md-dialog>
</script>


<?php include_once(APPPATH . 'Views/inc/footer.php'); ?>

<script type="text/javascript" src="<?php echo base_url('assets/js/invoices.js') ?>"></script>