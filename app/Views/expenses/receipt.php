<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>

<?php $appconfig = get_appconfig(); ?>

<div class="ciuis-body-content" ng-controller="Expense_Controller">

  <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-9">

    <div ng-show="expensesLoader" layout-align="center center" class="text-center" id="circular_loader">

      <!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
      <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">


      <p style="font-size: 15px;margin-bottom: 5%;">

        <span><?php echo lang2('please_wait') ?> <br>

          <small><strong><?php echo lang2('loading') . ' ' . lang2('expense') . '...' ?></strong></small></span>

      </p>

    </div>

    <md-toolbar ng-show="!expensesLoader" class="toolbar-white" ng-cloak>

      <div class="md-toolbar-tools">

        <h2 class="md-pl-10" flex md-truncate>

          <span ng-bind="expense.longid"></span>

          <span class="badge"><strong ng-bind="expense.category_name"></strong></span>

        </h2>

        <?php if (check_privilege('invoices', 'create')) { ?>

          <md-button ng-if="expense.internal === false" ng-hide="expense.invoice_id" ng-click="Convert()" class="md-icon-button" aria-label="Convert" ng-cloak>

            <md-tooltip md-direction="bottom"><?php echo lang2('convert') ?></md-tooltip>

            <md-icon><i class="ion-loop text-success"></i></md-icon>

          </md-button>

        <?php } ?>

        <md-button ng-if="expense.invoice_id" ng-href="<?php echo base_url('invoices/invoice/{{expense.invoice_id}}') ?>" class="md-icon-button" ng-cloak>

          <md-tooltip md-direction="bottom"><?php echo lang2('invoice') ?></md-tooltip>

          <md-icon><i class="ion-document-text text-success"></i></md-icon>

        </md-button>

        <md-button ng-click="sendEmail()" class="md-icon-button" aria-label="Email" ng-cloak>

          <md-progress-circular ng-show="sendingEmail == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

          <md-tooltip ng-hide="sendingEmail == true" md-direction="bottom" ng-bind="lang.send"></md-tooltip>

          <md-icon ng-hide="sendingEmail == true"><i class="mdi mdi-email text-muted"></i></md-icon>

        </md-button>

        <md-button ng-show="expense.pdf_status == '0'" ng-click="GeneratePDF()" class="md-icon-button" aria-label="Pdf" ng-cloak>

          <md-tooltip md-direction="bottom"><?php echo lang2('expense') . ' ' . lang2('summary') ?></md-tooltip>

          <md-icon><i class="mdi mdi-collection-pdf text-muted"></i> </md-icon>

        </md-button>

        <md-button ng-show="expense.pdf_status == '1'" ng-href="<?php echo base_url('expenses/download_pdf/') . $expenses['id'] ?>" class="md-icon-button" aria-label="Pdf" ng-cloak>

          <md-tooltip md-direction="bottom"><?php echo lang2('expense') . ' ' . lang2('summary') ?></md-tooltip>

          <md-icon><i class="mdi mdi-collection-pdf text-muted"></i> </md-icon>

        </md-button>

        <?php if (check_privilege('expenses', 'create') == true) { ?>
          <md-button style="padding: 0px 10px;font-size: 11px;" ng-href="<?php echo base_url('expenses/create') ?>" class="btn-shadow d-inline-flex align-items-center btn2 btn-success" aria-label="New" ng-cloak>
            Nova Despesa
            <md-tooltip md-direction="bottom"><?php echo lang2('newexpense') ?></md-tooltip>
            <md-icon style="vertical-align: unset;"><i class="ion-android-add"></i></md-icon>
          </md-button>
        <?php } ?>

        <?php if (check_privilege('expenses', 'edit') || check_privilege('expenses', 'delete')) { ?>

          <md-menu md-position-mode="target-right target" ng-cloak>

            <md-button aria-label="Open demo menu" class="md-icon-button" ng-click="$mdMenu.open($event)" ng-cloak>

              <md-icon><i class="ion-android-more-vertical text-muted"></i></md-icon>

            </md-button>

            <md-menu-content width="4">

              <?php if (check_privilege('expenses', 'edit')) { ?>

                <md-menu-item>

                  <md-button ng-href="<?php echo base_url('expenses/update/') ?>{{expense.id}}" ng-cloak>

                    <div layout="row" flex>

                      <p flex ng-bind="lang.update"></p>

                      <md-icon md-menu-align-target class="mdi mdi-edit" style="margin: auto 3px auto 0;"></md-icon>

                    </div>

                  </md-button>

                </md-menu-item>

              <?php }
              if (check_privilege('expenses', 'delete')) { ?>

                <md-menu-item>

                  <md-button ng-click="Delete()" ng-cloak>

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

    <md-content ng-show="!expensesLoader" class="bg-white invoice" layout-padding ng-cloak>

      <div class="invoice-header col-md-12">

        <div class="col-md-6 col-xs-6">

          <div class="ciuis-expenses-receipt-xs-colum" style="border: unset;"> <i class="mdi mdi-balance-wallet" aria-hidden="true"></i>

            <p>
              <span><?php echo lang2('amount') ?>:</span><br>
              <span style="font-size: 26px;font-weight: 900;" ng-bind-html="expense.amount | currencyFormat:cur_code:null:true:cur_lct"></span><br>


              <small><?php echo lang2('paidvia') ?> <strong ng-bind="expense.account_name"></strong></small> <br>
              <small>Forma de pgt: <strong ng-bind="expense.nm_forma"></strong></small> <br>
              <small>Descrição: <strong ng-bind="expense.description"></strong></small> <br>
            </p>

          </div>
        </div>
      </div>

      <div class="invoice-header col-md-12">
        <div class="col-md-2 col-xs-3">
          <div class="ciuis-expenses-receipt-xs-colum">
            <p><?php echo lang2('expense') . ' ' . lang2('date') ?>:<br>
              <span><strong ng-bind="expense.date | date:'dd, MMMM yyyy EEEE'"></strong></span>
            </p>
          </div>
        </div>

        <div class="col-md-3 col-xs-3">
          <div class="ciuis-expenses-receipt-xs-colum">
            <p><?= lang2('duedate') ?>:<br>
              <span><strong ng-bind="expense.duedate | date:'dd.MM.yyyy'"></strong></span>
            </p>
          </div>
        </div>

        <div class="col-md-2 col-xs-3">
          <div class="ciuis-expenses-receipt-xs-colum">
            <p><?php echo lang2('created') ?>:<br>
              <span><strong ng-bind="expense.created"></strong></span>
            </p>
          </div>
        </div>

        <div class="col-md-2 col-xs-3">
          <div class="ciuis-expenses-receipt-xs-colum">
            <p>Criado por:<br>
              <a ng-href="<?php echo base_url('staff/staffmember/') ?>{{expense.staff_id}}">
                <span><strong ng-bind="expense.staff_name"></strong></span>
              </a>
            </p>
          </div>
        </div>

        <div class="col-md-2 col-xs-3">
          <div class="ciuis-expenses-receipt-xs-colum">
            <p>Funcionário:<br>
              <a ng-href="<?php echo base_url('staff/staffmember/') ?>{{expense.funcionario_id}}">
                <span><strong ng-bind="expense.funcionario_name"></strong></span>
              </a>
            </p>
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
                <th><?php echo $appconfig['tax_label'] ?></th>
                <th ng-bind="lang.total"></th>

              </tr>

            </thead>

            <tbody>

              <tr ng-repeat="item in expense.items">

                <td><!--<span ng-bind="item.name"></span><br> -->

                  <pre class="pre_view" ng-cloak>{{item.description}}</pre>
                </td>

                <td ng-bind="item.quantity"></td>

                <td ng-bind-html="item.price | currencyFormat:cur_code:null:true:cur_lct"></td>

                <td ng-bind="item.tax + '%'"></td>

                <td ng-bind-html="item.total | currencyFormat:cur_code:null:true:cur_lct"></td>

              </tr>

            </tbody>

          </table>

        </div>

        <div class="invoice-price">

          <div class="invoice-price-left">

            <div class="invoice-price-row">

              <div class="sub-price"> <small ng-bind="lang.subtotal"></small> <span ng-bind-html="expense.sub_total | currencyFormat:cur_code:null:true:cur_lct"></span> </div>

              <div class="sub-price"> <i class="ion-plus-round"></i> </div>

              <div class="sub-price"> <small><?php echo $appconfig['tax_label'] ?></small> <span ng-bind-html="expense.total_tax | currencyFormat:cur_code:null:true:cur_lct"></span> </div>

            </div>

          </div>

          <div class="invoice-price-right"> <small ng-bind="lang.total"></small> <span ng-bind-html="expense.total | currencyFormat:cur_code:null:true:cur_lct"></span> </div>

        </div>

      </div>

    </md-content>

    </md-content>

    <md-content ng-show="!expensesLoader" class="bg-white" ng-cloak>

      <md-subheader ng-if="custom_fields.length > 0"><?php echo lang2('custom_fields') ?></md-subheader>

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

  </div>

  <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-3 project-sidebar">
    <md-toolbar class="toolbar-white">
      <div class="md-toolbar-tools">
        <md-button class="md-icon-button" aria-label="" ng-disabled="true" ng-cloak>
          <md-icon><i class="ion-document text-muted"></i></md-icon>
        </md-button>

        <h2 flex md-truncate><?php echo lang2('files') ?></h2>
        <?php if (check_privilege('expenses', 'edit')) { ?>
          <md-button ng-click="UploadFile()" class="md-icon-button md-primary" aria-label="Add File" ng-cloak>
            <md-tooltip md-direction="bottom"><?php echo lang2('upload_new_file') ?></md-tooltip>
            <md-icon class="ion-plus-round add-file"></md-icon>
          </md-button>
        <?php } ?>
      </div>
    </md-toolbar>

    <div ng-show="expensesFiles" layout-align="center center" class="text-center" id="circular_loader">
      <!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
      <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">

      <p style="font-size: 15px;margin-bottom: 5%;">
        <span><?php echo lang2('please_wait') ?> <br>
          <small><strong><?php echo lang2('loading') . ' ' . lang2('expense_files') . '...' ?></strong></small></span>
      </p>
    </div>

    <md-content ng-show="!expensesFiles" class="bg-white">
      <md-list flex ng-cloak>
        <md-list-item class="md-2-line" ng-repeat="file in files | pagination : currentPage*itemsPerPage | limitTo: 6">
          <div class="md-list-item-text image-preview">
            <a ng-if="file.type == 'image'" class="cursor" ng-click="ViewFile($index, image)">
              <md-tooltip md-direction="left"><?php echo lang2('preview') ?></md-tooltip>
              <img src="{{file.path}}">
            </a>

            <a ng-if="(file.type == 'archive')" class="cursor" ng-href="<?php echo base_url('expenses/download_file/{{file.id}}'); ?>">
              <md-tooltip md-direction="left"><?php echo lang2('download') ?></md-tooltip>
              <img src="<?php echo base_url('assets/img/zip_icon.png'); ?>">
            </a>

            <a ng-if="(file.type == 'file')" class="cursor" ng-href="<?php echo base_url('expenses/download_file/{{file.id}}'); ?>">
              <md-tooltip md-direction="left"><?php echo lang2('download') ?></md-tooltip>
              <img src="<?php echo base_url('assets/img/file_icon.png'); ?>">
            </a>

            <a ng-if="file.type == 'pdf'" class="cursor" ng-href="<?php echo base_url('expenses/download_file/{{file.id}}'); ?>">
              <md-tooltip md-direction="left"><?php echo lang2('download') ?></md-tooltip>
              <img src="<?php echo base_url('assets/img/pdf_icon.png'); ?>">
            </a>
          </div>

          <div class="md-list-item-text">
            <a class="cursor" ng-href="<?php echo base_url('expenses/download_file/{{file.id}}'); ?>">
              <h3 class="link" ng-bind="file.file_name"></h3>
            </a>
          </div>

          <?php if (check_privilege('expenses', 'delete')) { ?>
            <md-button class="md-secondary md-primary md-fab md-mini md-icon-button" ng-click='DeleteFile(file.id)' aria-label="call">
              <md-icon class="ion-trash-b"></md-icon>
            </md-button>
          <?php } ?>
          <md-divider></md-divider>
        </md-list-item>
        <div ng-show="!files.length" class="text-center"><img width="70%" src="<?php echo base_url('assets/img/nofiles2.png') ?>" alt=""></div>
      </md-list>

      <div ng-show="files.length>6 && !expensesFiles" class="pagination-div" ng-cloak>
        <ul class="pagination">
          <li ng-class="DisablePrevPage()"> <a href ng-click="prevPage()"><i class="ion-ios-arrow-back"></i></a> </li>
          <li ng-repeat="n in range()" ng-class="{active: n == currentPage}" ng-click="setPage(n)"> <a href="#" ng-bind="n+1"></a> </li>
          <li ng-class="DisableNextPage()"> <a href ng-click="nextPage()"><i class="ion-ios-arrow-right"></i></a> </li>
        </ul>
      </div>
    </md-content>

  </div>

  <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Update" ng-cloak style="width: 450px;">

    <md-toolbar class="toolbar-white">

      <div class="md-toolbar-tools">

        <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>

        <md-truncate><?php echo lang2('addexpense') ?></md-truncate>

      </div>

    </md-toolbar>

    <md-content>

      <md-content layout-padding="">
        <!--
        <md-input-container class="md-block">
          <label><?php echo lang2('title') ?></label>
          <input required type="text" ng-model="expense.title" class="form-control" id="title" placeholder="<?php echo lang2('title'); ?>" />
        </md-input-container>
          -->

        <md-input-container class="md-block">

          <label><?php echo lang2('amount') ?></label>

          <input required type="text" ng-model="expense.amount" class="form-control" id="amount" placeholder="0.00" />

        </md-input-container>

        <md-input-container class="md-block">

          <label><?php echo lang2('date') ?></label>

          <input mdc-datetime-picker="" date="true" time="true" type="text" id="datetime" placeholder="<?php echo lang2('chooseadate') ?>" show-todays-date="" minutes="true" min-date="date" show-icon="true" ng-model="expense.date" class=" dtp-no-msclear dtp-input md-input">

        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs>

          <label><?php echo lang2('category'); ?></label>

          <md-select required ng-model="expense.category" name="category" style="min-width: 200px;">

            <md-option ng-value="category.id" ng-repeat="category in expensescategories">{{category.name}}</md-option>

          </md-select>

        </md-input-container>

        <br>
        <!--
        <md-input-container ng-show="expense.customer != 0" class="md-block" flex-gt-xs>
          <label><?php echo lang2('customer'); ?></label>
          <md-select placeholder="<?php echo lang2('choisecustomer'); ?>" ng-model="expense.customer" name="customer" style="min-width: 200px;">
            <md-option ng-value="customer.id" ng-repeat="customer in all_customers">{{customer.name}}</md-option>
          </md-select>
        </md-input-container>
          -->

        <br ng-show="expense.customer != 0">

        <md-input-container class="md-block" flex-gt-xs>

          <label><?php echo lang2('account'); ?></label>

          <md-select required ng-model="expense.account" name="account" style="min-width: 200px;">

            <md-option ng-value="account.id" ng-repeat="account in accounts">{{account.name}}</md-option>

          </md-select>

        </md-input-container>

        <br>

        <md-input-container class="md-block">

          <label><?php echo lang2('description') ?></label>

          <textarea required name="description" ng-model="expense.description" placeholder="<?php echo lang2('typeSomething'); ?>" class="form-control"></textarea>

        </md-input-container>

      </md-content>

      <custom-fields-vertical></custom-fields-vertical>

      <md-content layout-padding>

        <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>

          <md-button ng-click="UpdateExpense()" class="md-raised md-primary pull-right"><?php echo lang2('update'); ?></md-button>

        </section>

      </md-content>

    </md-content>

  </md-sidenav>

</div>

<?php $fileUrl = base_url('uploads/files/expenses/$expenses["id"]/') ?>

<script>
  var EXPENSEID = "<?php echo $expenses['id'] ?>";

  var lang = {};

  lang.doIt = "<?php echo lang2('doIt') ?>";

  lang.cancel = "<?php echo lang2('cancel') ?>";

  lang.attention = "<?php echo lang2('attention') ?>";

  lang.delete_expense = "<?php echo lang2('expensesatentiondetail') ?>";

  lang.convert_title = "<?php echo lang2('convert') . ' ' . lang2('expense') . ' ' . lang2('to') . ' ' . lang2('invoice') ?>";

  lang.convert_text = "<?php echo lang2('convertmsg') . ' ' . lang2('expense') . ' ' . lang2('to') . ' ' . lang2('invoice') ?>";

  lang.convert = "<?php echo lang2('convert') ?>";

  lang.email_sent_success = "<?php echo lang2('email_sent_success') ?>";
</script>

<script type="text/ng-template" id="addfile-template.html">
  <md-dialog aria-label="options dialog">
  <?php  helper('form');  echo form_open_multipart('expenses/add_file/' . $expenses['id'] . '', array("class" => "form-horizontal")); ?>
  <md-dialog-content layout-padding>
    <h2 class="md-title"><?php echo lang2('choosefile'); ?></h2>
    <input type="file" required name="file_name" file-model="project_file">
  </md-dialog-content>

  <md-dialog-actions>
    <span flex></span>
    <md-button ng-click="close()" aria-label="add"><?php echo lang2('cancel') ?>!</md-button>
    <md-button ng-click="uploadExpenseFile()" class="template-button" ng-disabled="uploading == true">
      <span ng-hide="uploading == true"><?php echo lang2('upload'); ?></span>
      <md-progress-circular class="white" ng-show="uploading == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>
    </md-button>

  </md-dialog-actions>
  <?php echo form_close(); ?>
  </md-dialog>
</script>

<script type="text/ng-template" id="generate-expense-summary.html">

  <md-dialog aria-label="options dialog">

  <md-dialog-content layout-padding class="text-center">

    <md-content class="bg-white" layout-padding>

      <h2 class="md-title" ng-hide="PDFCreating == true"><?php echo lang2('generate') . ' ' . lang2('expense') . ' ' . lang2('pdf') ?></h2>

      <h2 class="md-title" ng-if="PDFCreating == true"><?php echo lang2('report_generating') ?></h2>

      <span ng-hide="PDFCreating == false"><?php echo lang2('generate_expense_pdf_msg') ?></span><br><br>

      <span ng-if="PDFCreating == false"><?php echo lang2('generate_pdf_last_msg') ?></span><br><br>

      <img ng-if="PDFCreating == true" ng-src="<?php echo base_url('assets/img/loading_time.gif') ?>" alt="">

      <a ng-if="PDFCreating == false" href="<?php echo base_url('expenses/download_pdf/' . $expenses['id'] . '') ?>"><img  width="30%"ng-src="<?php echo base_url('assets/img/download_pdf.png') ?>" alt=""></a>

    </md-content>

  </md-dialog-content>

  <md-dialog-actions>

    <span flex></span>

    <md-button class="text-success" ng-if="PDFCreating == false" href="<?php echo base_url('expenses/download_pdf/' . $expenses['id'] . '') ?>">

      <?php echo lang2('download') ?>

    </md-button>

    <md-button class="text-success" ng-hide="PDFCreating == false" ng-click="CreatePDF()"><?php echo lang2('create') ?></md-button>

    <md-button class="text-danger" ng-click="close()"><?php echo lang2('cancel') ?></md-button>

  </md-dialog-actions>

  </md-dialog>

</script>

<script type="text/ng-template" id="view_image.html">
  <md-dialog aria-label="options dialog">
  <?php echo form_open_multipart('expenses/add_file/' . $expenses['id'] . '', array("class" => "form-horizontal")); ?>
  <md-dialog-content layout-padding>
    <?php $path = '{{file.path}}';
    if ($path) { ?>
      <img src="<?php echo $path ?>">
    <?php } ?>
  </md-dialog-content>
  <md-dialog-actions>
    <span flex></span>
    <?php if (check_privilege('expenses', 'delete')) { ?>
      <md-button ng-click='DeleteFiles(file.id)'><?php echo lang2('delete') ?>!</md-button>
    <?php } ?>
    <md-button ng-href="<?php echo base_url('expenses/download_file/') ?>{{file.id}}"><?php echo lang2('download') ?>!</md-button>
    <md-button ng-click="close()"><?php echo lang2('cancel') ?>!</md-button>
  </md-dialog-actions>
  <?php echo form_close(); ?>
  </md-dialog>
</script>

<?php include_once(APPPATH . 'Views/inc/footer.php'); ?>

<script type="text/javascript" src="<?php echo base_url('assets/js/expenses.js') ?>"></script>