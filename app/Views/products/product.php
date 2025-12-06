<?php $appconfig = get_appconfig(); ?>

<div id="pageContent">

  <div class="ciuis-body-content" ng-controller="Product_Controller">

    <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-9">

      <md-toolbar class="toolbar-white">

        <div class="md-toolbar-tools">

          <h2 class="md-pl-10" flex md-truncate ng-bind="product.product_number+' '+product.productname"></h2>

          <?php if (check_privilege('products', 'edit')) { ?>

            <md-button ng-click="Update()" class="md-icon-button" aria-label="Update">

              <md-tooltip md-direction="bottom"><?php echo lang2('update') ?></md-tooltip>

              <md-icon><i class="ion-compose  text-muted"></i></md-icon>

            </md-button>

          <?php }
          if (check_privilege('products', 'delete')) { ?>

            <md-button ng-click="Delete()" class="md-icon-button" aria-label="Delete">

              <md-tooltip md-direction="bottom"><?php echo lang2('delete') ?></md-tooltip>

              <md-icon><i class="ion-trash-b  text-muted"></i></md-icon>

            </md-button>

          <?php } ?>

        </div>

      </md-toolbar>

      <md-content class="bg-white">

        <div layout="row" layout="row" layout-wrap>

          <md-content class="bg-white" flex-gt-xs="50" flex-xs="100" style="border-right:1px solid #e0e0e0;">

            <md-list flex class="md-p-0 sm-p-0 lg-p-0">

              <md-list-item>

                <md-icon class="ion-pricetags icon"></md-icon>

                <strong flex md-truncate><?php echo lang2('productcategory') ?></strong>

                <p class="text-right" flex md-truncate ng-bind="product.category_name"></p>

              </md-list-item>

              <md-divider></md-divider>

              <md-list-item>

                <md-icon class="mdi mdi-label"></md-icon>

                <strong flex md-truncate><?php echo lang2('purchaseprice') ?></strong>

                <p class="text-right" flex md-truncate ng-bind-html="product.purchase_price | currencyFormat:cur_code:null:true:cur_lct"></p>

              </md-list-item>

              <md-divider></md-divider>

              <md-list-item>

                <md-icon class="mdi mdi-label-heart"></md-icon>

                <strong flex md-truncate><?php echo lang2('salesprice') ?></strong>

                <p class="text-right" flex md-truncate ng-bind-html="product.sale_price | currencyFormat:cur_code:null:true:cur_lct"></p>

              </md-list-item>

              <md-divider></md-divider>
              <!--
              <md-list-item>
                <md-icon class="mdi mdi-balance"></md-icon>
                <strong flex md-truncate><?php echo $appconfig['tax_label'] ?></strong>
                <p class="text-right" flex md-truncate ng-bind="product.vat+'%'"></p>
              </md-list-item>
              -->
              <md-divider></md-divider>
              <md-list-item>
                <md-icon><i class="fas fa-user-tie"></i></md-icon>
                <strong flex md-truncate>Fornecedor</strong>
                <p class="text-right" flex md-truncate ng-bind="product.company"></p>
              </md-list-item>

              <md-divider></md-divider>

              <md-list-item>

                <md-icon class="mdi mdi-book"></md-icon>

                <strong flex md-truncate><?php echo lang2('instock') ?></strong>

                <p class="text-right" flex md-truncate ng-bind="product.stock"></p>

              </md-list-item>

              <md-divider></md-divider>

              <md-list-item>

                <md-icon class="ion-ios-barcode-outline"></md-icon>

                <strong flex md-truncate><?php echo lang2('productcode') ?></strong>

                <p class="text-right" flex md-truncate ng-bind="product.code"></p>

              </md-list-item>

              <md-subheader ng-if="custom_fields && custom_fields.length>0"><?php echo lang2('custom_fields') ?></md-subheader>

              <md-list-item ng-if="custom_fields && custom_fields.length>0" ng-repeat="field in custom_fields">

                <md-icon class="{{field.icon}} material-icons"></md-icon>

                <strong flex md-truncate>{{field.name}}</strong>

                <p ng-if="field.type === 'input'" class="text-right" flex md-truncate ng-bind="field.data"></p>

                <p ng-if="field.type === 'textarea'" class="text-right" flex md-truncate ng-bind="field.data"></p>

                <p ng-if="field.type === 'date'" class="text-right" flex md-truncate ng-bind="field.data | date:'dd, MMMM yyyy EEEE'"></p>

                <p ng-if="field.type === 'select'" class="text-right" flex md-truncate ng-bind="custom_fields[$index].selected_opt.name"></p>

                <md-divider ng-if="custom_fields"></md-divider>

              </md-list-item>

            </md-list>

          </md-content>

          <div layout-padding flex-gt-xs="20" flex-xs="100">

            <div class="ciuis-product-summary">

              <h5 class="text-bold text-uppercase"><?php echo lang2('netrevenue') ?></h5>

              <small><?php echo lang2('grossrevenueproductsub') ?></small>

              <h1 class="txt-scale-xs no-margin-top xs-28px figures"><span ng-bind="product.total_sales"></span></h1>

            </div>

          </div>

          <div layout-padding flex-gt-xs="30" flex-xs="100">

            <div class="ciuis-product-summary">

              <h5 class="text-bold text-uppercase text-success"><?php echo lang2('netearnings') ?></h5>

              <small><?php echo lang2('netearningssub') . ' ' . $appconfig['tax_label']; ?></small>

              <h1 class="txt-scale-xs no-margin-top xs-28px figures"><span ng-bind-html="product.net_earning | currencyFormat:cur_code:null:true:cur_lct"></span></h1>

              <p class="secondary-text"><strong class="text-muted"><?php echo lang2('productnetearnings') ?></strong></p>

            </div>

          </div>

        </div>

      </md-content>

    </div>

    <ciuis-sidebar></ciuis-sidebar>

    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Update" ng-cloak style="width: 450px;">

      <md-toolbar class="toolbar-white">

        <div class="md-toolbar-tools">

          <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>

          <md-truncate><?php echo lang2('update') ?></md-truncate>

        </div>

      </md-toolbar>

      <md-content>

        <md-content layout-padding>

          <md-input-container class="md-block">

            <label><?php echo lang2('productname') ?></label>

            <input required type="text" ng-model="product.productname" class="form-control" id="name" placeholder="<?php echo lang2('productname'); ?>" />

          </md-input-container>

          <md-input-container class="md-block">

            <label><?php echo lang2('productcategory'); ?></label>

            <md-select placeholder="<?php echo lang2('productcategory'); ?>" ng-model="product.categoryid" style="min-width: 200px;">

              <md-option ng-value="name.id" ng-repeat="name in category">{{name.name}}</md-option>

            </md-select>

          </md-input-container>

          <br>

          <md-input-container class="md-block">
            <label>Fornecedor</label>
            <md-select placeholder="Fornecedor" ng-model="product.vendor_id" style="min-width: 200px;">
              <md-option ng-value="vendor.id" ng-repeat="vendor in vendors">{{vendor.name }}</md-option>
            </md-select>
          </md-input-container>

          <md-input-container class="md-block">

            <label><?php echo lang2('purchaseprice') ?></label>

            <input required type="text" ng-model="product.purchase_price" class="form-control money" id="amount" placeholder="0.00" />

          </md-input-container>

          <md-input-container class="md-block">

            <label><?php echo lang2('salesprice') ?></label>

            <input required type="text" ng-model="product.sale_price" class="form-control money" id="amount" placeholder="0.00" />

          </md-input-container>

          <md-input-container class="md-block">

            <label><?php echo lang2('productcode') ?></label>

            <input type="text" ng-model="product.code" class="form-control" id="productcode" placeholder="<?php echo lang2('productcode'); ?>" />

          </md-input-container>
          <!--
          <md-input-container class="md-block">
            <label><?php echo $appconfig['tax_label'] ?></label>
            <input type="text" ng-model="product.vat" class="form-control" id="tax" placeholder="<?php echo lang2('tax'); ?>" />
          </md-input-container>
          -->
          <md-input-container class="md-block">
            <label><?php echo lang2('instock') ?></label>
            <input ng-model="product.stock" class="form-control count" id="stock" placeholder="<?php echo lang2('instock'); ?>" type="text" value="0" name="demo0" data-bts-min="0" data-bts-max="100" data-bts-init-val="" data-bts-step="1" data-bts-decimal="0" data-bts-step-interval="100" data-bts-force-step-divisibility="round" data-bts-step-interval-delay="500" data-bts-prefix="" data-bts-postfix="" data-bts-prefix-extra-class="" data-bts-postfix-extra-class="" data-bts-booster="true" data-bts-boostat="10" data-bts-max-boosted-step="false" data-bts-mousewheel="true" data-bts-button-down-class="btn btn-primary" data-bts-button-up-class="btn btn-primary" />
          </md-input-container>

          <md-input-container class="md-block">
            <label><?php echo lang2('description') ?></label>
            <textarea name="description" ng-model="product.description" placeholder="<?php echo lang2('typeSomething'); ?>" class="form-control"></textarea>
          </md-input-container>

        </md-content>

        <custom-fields-vertical></custom-fields-vertical>

        <md-content layout-padding>

          <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>

            <md-button ng-click="UpdateProduct()" class="md-raised md-primary pull-right"><?php echo lang2('update'); ?></md-button>

          </section>

        </md-content>

      </md-content>

    </md-sidenav>

  </div>

  <script>
    var PRODUCTID = "<?php echo $product['id'] ?>"

    $(document).ready(function() {
      $(".count").TouchSpin();
    });

    var lang = {};

    lang.attention = "<?php echo lang2('attention') ?>";

    lang.doIt = "<?php echo lang2('doIt') ?>";

    lang.cancel = "<?php echo lang2('cancel') ?>";

    lang.product = "<?php echo lang2('product') ?>";

    lang.delete = "<?php echo lang2('delete') ?>";

    lang.productattentiondetail = "<?php echo lang2('productattentiondetail') ?>";
  </script>

</div>