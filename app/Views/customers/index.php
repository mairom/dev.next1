<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<?php $appconfig = get_appconfig(); ?>
<div class="ciuis-body-content" ng-controller="Customers_Controller">
  <style type="text/css">
    rect.highcharts-background {
      fill: #f3f3f3;
    }

    .md-button.md-icon-button {
      float: left;
    }
  </style>

  <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">
    <md-toolbar class="toolbar-white toolbar-title">
      <div class="md-toolbar-tools md-tools">
        <div class="col-12 col-md-8">
          <md-button class="md-icon-button" aria-label="File">
            <img style="transition: all 0.1s ease-in-out;width: 100%;text-align: center;margin: 0 auto;" src="https://www.next1crm.com.br/assets/img/menu/Clientes.svg">
          </md-button>

          <h2 flex md-truncate><?php echo lang2('customers'); ?></h2>

          <div class="search-wrapper ">
            <button class="close" ng-click="openSearch()">
            </button>
            <div class="input-holder">
              <input ng-model="customer_search" id="search" name="search" type="text" placeholder="<?php echo lang2('searchword') ?>" class="search-input">
              <button class="search-icon" ng-click="openSearch()">
                <span></span>
              </button>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <md-button ng-click="CreateGroup()" class="md-icon-button btn_settings" aria-label="Settings" ng-cloak>
            <md-tooltip md-direction="bottom"><?php echo lang2('settings') ?></md-tooltip>
            <md-icon style="font-size: 20px;">
              <i class="fas fa-sliders-h" style="color: #3f6ad8;font-size: 15px !important;"></i>
            </md-icon>
          </md-button>



          <?php if (check_privilege('customers', 'create')) { ?>
            <md-button ng-click="ImportCustomersNav()" class="md-icon-button btn_settings">
              <div layout="row" flex>
                <md-tooltip md-direction="bottom"><?= lang2('importcustomers'); ?></md-tooltip>
                <i class="fas fa-upload" style="color: #3f6ad8;font-size: 17px !important;"></i>
                </md-icon>
              </div>
            </md-button>
          <?php } ?>


          <?php if (check_privilege_export('customers')) { ?>
            <md-button class="md-icon-button btn_settings">
              <div layout="row" flex>
                <md-tooltip md-direction="bottom"><?= lang2('exportcustomers'); ?></md-tooltip>
                <i class="fas fa-download" style="color: #3f6ad8;font-size: 17px !important;"></i>
              </div>
            </md-button>
          <?php } ?>


          <?php if (check_privilege('customers', 'create')) { ?>
            <button type="button" class="btn-shadow d-inline-flex align-items-center btn2 btn-success" ng-click="Create()">
              <i class="fas fa-plus"></i> Novo Cliente
            </button>
          <?php } ?>


        </div>

      </div>
    </md-toolbar>

    <div ng-show="customersLoader" layout-align="center center" class="text-center" id="circular_loader" ng-cloak>
      <md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
      <p style="font-size: 15px;margin-bottom: 5%;">
        <span><?php echo lang2('please_wait') ?> <br>
          <small><strong><?php echo lang2('loading') . ' ' . lang2('customers') . '...' ?></strong></small></span>
      </p>
    </div>

    <md-content ng-show="!customersLoader" class="bg-white" ng-cloak>
      <md-tabs md-dynamic-height md-border-bottom>
        <md-tab label="Ativos ({{getAtivos()}})">

          <md-table-container ng-show="customers.length > 0">
            <table md-table md-progress="promise">
              <thead md-head md-order="customer_list.order">
                <tr md-row>
                  <th md-column md-order-by="name"><span><?php echo lang2('name'); ?></span></th>
                  <td md-column><span>Cliente desde</span></td>
                  <td md-column><span><?php echo lang2('group'); ?></span></td>
                  <td md-column>LT</td>
                  <td md-column>LTV</td>
                  <td md-column>Ultima fatura</td>
                  <td md-column>Ultima atividade</td>
                </tr>
              </thead>

              <tbody md-body>

                <tr class="select_row" md-row ng-repeat="customer in customers | orderBy: customer_list.order | filter: customer_search | filter: FilteredData | filter:{customer_status_id: 1}" class="cursor" ng-click="goToLink('customers/customer/'+customer.id, true)">

                  <td md-cell>
                    <span style="color: #090862;" ng-bind="customer.name"></span>
                  </td>

                  <td md-cell>
                    <span ng-bind="customer.created"></span>
                  </td>

                  <td md-cell>
                    <span class="badge" ng-bind="customer.group_name"></span>
                  </td>

                  <td md-cell>
                    <span ng-bind="customer.lt"></span>
                  </td>

                  <td md-cell>
                    <span ng-bind-html="customer.ltv | currencyFormat:cur_code:null:true:cur_lct"></span>
                  </td>



                  <td md-cell>
                    <span ng-bind="customer.ultima_fat"></span>
                  </td>

                  <td md-cell>
                    <span ng-bind="customer.ultima_atv"></span>
                  </td>
                </tr>
              </tbody>
            </table>
          </md-table-container>

        </md-tab>

        <md-tab label="Inativos ({{getDesativos()}})">
          <md-table-container ng-show="customers.length > 0">
            <table md-table md-progress="promise">
              <thead md-head md-order="customer_list.order">
                <tr md-row>
                  <th md-column md-order-by="name"><span><?php echo lang2('name'); ?></span></th>
                  <td md-column><span>Cliente desde</span></td>
                  <td md-column><span><?php echo lang2('group'); ?></span></td>
                  <td md-column>LT</td>
                  <td md-column>LTV</td>
                  <td md-column>Ultima fatura</td>
                  <td md-column>Ultima atividade</td>
                </tr>
              </thead>
              <tbody md-body>
                <tr class="select_row" md-row ng-repeat="customer in customers | orderBy: customer_list.order  | filter: customer_search | filter: FilteredData | filter:{customer_status_id: 0}" class="cursor" ng-click="goToLink('customers/customer/'+customer.id, true)">
                  <td md-cell>
                    <strong><span ng-bind="customer.name"></span></strong>
                  </td>
                  <td md-cell>
                    <span ng-bind="customer.created"></span>
                  </td>

                  <td md-cell>
                    <strong><span class="badge" ng-bind="customer.group_name"></span></strong>
                  </td>

                  <td md-cell>
                    <span ng-bind="customer.lt "></span>
                  </td>

                  <td md-cell>
                    <span ng-bind-html="customer.ltv | currencyFormat:cur_code:null:true:cur_lct"></span>
                  </td>

                  <td md-cell>
                    <span class="badge" ng-bind="customer.ultima_fat"></span>
                  </td>

                  <td md-cell>
                    <span class="badge" ng-bind="customer.ultima_atv"></span>
                  </td>

                </tr>
              </tbody>
            </table>
          </md-table-container>
          <!--
          <md-table-pagination ng-show="customers.length > 0" md-limit="customer_list.limit" md-limit-options="limitOptions" md-page="customer_list.page" md-total="{{customers.length}}"></md-table-pagination>
            -->
        </md-tab>
      </md-tabs>

      <md-content ng-show="!customers.length && !customersLoader" class="md-padding no-item-data">
        <?php echo lang2('notdata') ?>
      </md-content>
    </md-content>
  </div>

  <!-- <ciuis-sidebar ng-show="!customersLoader"></ciuis-sidebar> -->
  <!--
  <div class="main-content container-fluid col-xs-12 col-md-3 col-lg-3 md-pl-0 lead-left-bar">

    <div class="panel-default panel-table borderten lead-manager-head">

      <md-toolbar class="toolbar-white">
        <div class="md-toolbar-tools">
          <h2 flex md-truncate class="text-bold"><?php echo lang2('customer') . ' ' . lang2('group'); ?>
            <md-button ng-click="CreateGroup()" class="md-icon-button pull-right" aria-label="New" ng-cloak>
              <md-icon><i class="ion-gear-a text-muted"></i></md-icon>
            </md-button>
          </h2>
        </div>
      </md-toolbar>

      <div class="tasks-status-stat">

        <div class="widget-chart-container">

          <div class="widget-counter-group widget-counter-group-right">

            <div style="width: auto" class="pull-left"> <i style="font-size: 38px;color: #bfc2c6;margin-right: 10px" class="ion-stats-bars pull-left"></i>

              <div class="pull-right" style="text-align: left;margin-top: 10px;line-height: 10px;">

                <h4 style="padding: 0px;margin: 0px;">

                  <b><?php echo lang2('customers') . ' ' . lang2('noteby') . ' ' . lang2('group') ?></b>

                </h4>

                <small><?php echo lang2('customer') . ' ' . lang2('stats') ?></small>

              </div>

            </div>

          </div>

          <div id="container" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>

        </div>

      </div>

    </div>

  </div>
        -->

  <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Create" style="width: 450px;" ng-cloak>
    <md-toolbar class="toolbar-white">
      <div class="md-toolbar-tools">
        <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>
        <h2 flex md-truncate><?php echo lang2('create') ?></h2>
        <!-- <md-switch ng-model="isIndividual" aria-label="Type"><strong class="text-muted"><?php echo lang2('individual') ?></strong></md-switch> -->
        <!-- <md-switch ng-model="isContact" aria-label="Contact"><strong class="text-muted"><?php echo lang2('create') . ' ' . lang2('contact') ?></strong></md-switch> -->
      </div>
    </md-toolbar>
    <md-content>

      <md-content layout-padding>

        <md-input-container class="md-block ">
          <label>Tipo de pessoa</label>
          <md-select required placeholder="" ng-model="customer.tp_pessoa" style="min-width: 200px;">
            <md-option ng-value="1">Pessoa física</md-option>
            <md-option ng-value="2">Pessoa jurídica</md-option>
          </md-select>
        </md-input-container>

        <div ng-show="customer.tp_pessoa != null" class="addLeadDiv">

          <md-input-container class="md-block" ng-if="customer.tp_pessoa == 1">
            <label><?php echo lang2('name'); ?></label>
            <input required name="name" ng-model="customer.company">
          </md-input-container>

          <md-input-container class="md-block" ng-if="customer.tp_pessoa == 2">
            <label><?php echo lang2('company'); ?></label>
            <input required ng-model="customer.company">
          </md-input-container>

          <md-input-container class="md-block" ng-if="customer.tp_pessoa == 2">
            <label>Cnpj</label>
            <input name="cnpj" ng-model="customer.cnpj">
          </md-input-container>

          <md-input-container class="md-block" ng-if="customer.tp_pessoa == 2">
            <label>Website</label>
            <input name="web_site" ng-model="customer.web">
          </md-input-container>

          <md-input-container class="md-block" ng-if="customer.tp_pessoa == 1">
            <label>Cpf</label>
            <input name="cpf" ng-model="customer.cpf">
          </md-input-container>

          <md-input-container class="md-block" ng-if="customer.tp_pessoa == 2">
            <label>Setor de atividade</label>
            <input name="setor_atividade" ng-model="customer.setor_atividade">
          </md-input-container>

          <md-input-container class="md-block" ng-if="customer.tp_pessoa == 1">
            <label>Data de nascimento</label>
            <input name="dt_nascimento" type="date" ng-model="customer.dt_nascimento">
          </md-input-container>

          <md-input-container class="md-block" ng-if="customer.tp_pessoa == 2">
            <label>Porte</label>
            <input name="porte" ng-model="customer.porte">
          </md-input-container>

          <md-content>
            <md-input-container class="md-block" flex="70" style="float: left;width: 100%;">
              <label>Telefone</label>
              <input name="phone" class="phone" ng-model="customer.phone">
            </md-input-container>

            <md-input-container class="md-block" flex="25" style="float: left;margin-top: 0;margin-left: 1em;width: 100%;">
              <md-switch ng-model="customer.is_whatsApp" aria-label="Recurring"> <label>WhatsApp</label></md-switch>
            </md-input-container>
          </md-content>

          <md-input-container class="md-block">
            <label><?php echo lang2('email'); ?></label>
            <input type="email" ng-model="customer.email" minlength="10" maxlength="100" ng-pattern="/^.+@.+\..+$/">
          </md-input-container>

          <md-input-container class="md-block" ng-if="customer.tp_pessoa == 1">
            <label>Instagram</label>
            <input name="instagram" ng-model="customer.instagram">
          </md-input-container>

          <md-input-container class="md-block" ng-if="customer.tp_pessoa == 1">
            <label>Facebook</label>
            <input name="facebook" ng-model="customer.facebook">
          </md-input-container>

          <md-input-container class="md-block" ng-if="customer.tp_pessoa == 1">
            <label>Linkedin</label>
            <input name="linkedin" ng-model="customer.linkedin">
          </md-input-container>

          <md-input-container class="md-block">
            <label><?php echo lang2('address') ?></label>
            <textarea ng-model="customer.address" md-maxlength="500" rows="3" md-select-on-focus></textarea>
          </md-input-container>

          <md-input-container class="md-block">
            <label><?php echo lang2('city'); ?></label>
            <input ng-model="customer.city">
          </md-input-container>

          <md-input-container class="md-block">
            <label><?php echo lang2('zip'); ?></label>
            <input ng-model="customer.zipcode">
          </md-input-container>

          <md-input-container class="md-block">
            <label><?php echo lang2('country'); ?></label>
            <md-select placeholder="<?php echo lang2('country'); ?>" ng-model="customer.country_id" ng-change="getStates(customer.country_id)" name="country_id" style="min-width: 200px;">
              <md-option ng-selected="$index == 31" ng-value="country.id" ng-repeat="country in countries">{{country.shortname}}</md-option>
            </md-select>
          </md-input-container>

          <md-input-container class="md-block" style="margin-top: 3em;">
            <label><?php echo lang2('state'); ?></label>
            <md-select placeholder="<?php echo lang2('states'); ?>" ng-model="customer.state_id" name="state_id" style="min-width: 200px;">
              <md-option ng-value="state.id" ng-repeat="state in states">{{state.state_name}}</md-option>
            </md-select>
          </md-input-container>

          <md-input-container class="md-block" style="margin-top: 3em;">
            <label><?php echo lang2('source'); ?></label>
            <md-select placeholder="<?php echo lang2('source'); ?>" ng-model="customer.source_id" style="min-width: 200px;">
              <md-option ng-value="source.id" ng-repeat="source in leadssources">{{source.name}}</md-option>
            </md-select>
          </md-input-container>

          <md-input-container class="md-block" style="margin-top: 3em;">
            <label><?php echo lang2('description') ?></label>
            <textarea ng-model="customer.description" md-maxlength="500" rows="3" md-select-on-focus></textarea>
          </md-input-container>

          <md-input-container class="md-block" style="margin-top: 1em;">
            <label><?php echo lang2('Closer'); ?></label>
            <md-select placeholder="<?php echo lang2('Closer'); ?>" ng-model="customer.closer" style="min-width: 200px;">
              <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
            </md-select>
          </md-input-container>

          <md-input-container class="md-block" style="margin-top: 1em;">
            <label><?php echo lang2('Customer Sucess'); ?></label>
            <md-select placeholder="<?php echo lang2('Customer Sucess'); ?>" ng-model="customer.customer_sucess" style="min-width: 200px;">
              <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
            </md-select>
          </md-input-container>

          <md-input-container class="md-block">
            <label><?php echo lang2('customer') . ' ' . lang2('group'); ?></label>
            <md-select placeholder="<?php echo lang2('customer') . ' ' . lang2('group'); ?>" ng-model="customer.group_id" style="min-width: 200px;" required>
              <md-select-header>
                <md-toolbar class="toolbar-white">
                  <div class="md-toolbar-tools">
                    <h4 flex md-truncate><?php echo lang2('groups') ?></h4>
                    <md-button class="md-icon-button" ng-click="NewGroup()" aria-label="Create New">
                      <md-icon><i class="mdi mdi-plus text-muted"></i></md-icon>
                    </md-button>
                  </div>
                </md-toolbar>
              </md-select-header>
              <md-option ng-value="name.id" ng-repeat="name in group">{{name.name}}</md-option>
            </md-select>
          </md-input-container>


          <md-input-container class="md-block " style="margin-top: 3em;">
            <label>Funil</label>
            <md-select placeholder="" ng-model="customer.funil" style="min-width: 200px;">
              <md-option ng-value="$index" ng-repeat="list in funilsCustomers">{{list.nm_funil}}</md-option>
            </md-select>
          </md-input-container>

          <md-input-container class="md-block" style="margin-top: 3em;">
            <label>Status</label>
            <md-select placeholder="<?php echo lang2('status'); ?>" ng-model="customer.etapa" style="min-width: 200px;">
              <md-option ng-value="status.id" ng-repeat="status in funilsCustomers[customer.funil].etapas">{{status.name}}</md-option>
            </md-select>
          </md-input-container>

          <md-input-container class="md-block">
            <label><?php echo lang2('default_payment_method'); ?></label>
            <md-select placeholder="<?php echo lang2('default_payment_method'); ?>" ng-model="customer.default_payment_method" name="default_payment_method" style="min-width: 200px;">
              <?php
              $gateways = get_active_payment_methods();
              foreach ($gateways as $gateway) { ?>
                <md-option ng-value='"<?php echo $gateway['relation'] ?>"'><?php echo lang($gateway['relation']) ? lang($gateway['relation']) : $gateway['name'] ?></md-option>
              <?php } ?>
            </md-select>
          </md-input-container>

          <br>
          <md-input-container class="md-block">
            <label><?php echo lang2('date_contacted') ?></label>
            <input mdc-datetime-picker="" date="true" time="true" type="text" id="date_contacted" click-outside-to-close="true" placeholder="<?php echo lang2('chooseadate') ?>" show-todays-date="" minutes="true" today-btn="false" show-icon="true" ng-model="customer.created" class=" dtp-no-msclear dtp-input md-input">
          </md-input-container>

          <br>
          <md-input-container class="md-block">
            <label>Data de inativação</label>
            <input mdc-datetime-picker="" date="true" time="true" type="text" click-outside-to-close="true" placeholder="<?php echo lang2('chooseadate') ?>" show-todays-date="" minutes="true" today-btn="false" show-icon="true" ng-model="customer.dt_inactive" class=" dtp-no-msclear dtp-input md-input">
          </md-input-container>

          <md-slider-container> <span><?php echo lang2('riskstatus'); ?></span>
            <md-slider flex min="0" max="100" ng-model="customer.risk" aria-label="red" id="red-slider"> </md-slider>
            <md-input-container>
              <input name="risk" flex type="number" ng-model="customer.risk" aria-label="red" aria-controls="red-slider">
            </md-input-container>
          </md-slider-container>
        </div>

      </md-content>
      <md-subheader class="md-primary">
        <md-truncate><?php echo lang2('billing_address') ?></md-truncate>
        <md-button ng-click='SameAsCustomerAddress()' class="md-icon-button" aria-label="Copy Customer Address">
          <md-icon class="ion-ios-copy">
            <md-tooltip md-direction="top"><?php echo lang2('same_as_customer') ?></md-tooltip>
          </md-icon>
        </md-button>

        <md-button class="pull-right hide-pinned-projects md-icon-button" aria-label="<?php echo lang2('billing_address') ?>">
          <a data-toggle="collapse" data-parent="#billing_address" href="#billing_address">
            <md-icon class="ion-chevron-down">
            </md-icon>
          </a>
        </md-button>
      </md-subheader>

      <md-content layout-padding id="billing_address" class="panel-collapse collapse out">

        <md-input-container class="md-block">

          <label><?php echo lang2('address') ?></label>

          <textarea ng-model="customer.billing_street" name="address" md-maxlength="500" rows="3" md-select-on-focus></textarea>

        </md-input-container>

        <md-input-container class="md-block">

          <label><?php echo lang2('country'); ?></label>

          <md-select placeholder="<?php echo lang2('country'); ?>" ng-model="customer.billing_country" ng-change="getBillingStates(customer.billing_country)" name="billing_country" style="min-width: 200px;">

            <md-option ng-value="country.id" ng-repeat="country in countries">{{country.shortname}}</md-option>

          </md-select>

        </md-input-container>

        <md-input-container class="md-block">

          <label><?php echo lang2('state'); ?></label>

          <md-select placeholder="<?php echo lang2('states'); ?>" ng-model="customer.billing_state_id" name="billing_state_id" style="min-width: 200px;">

            <md-option ng-value="state.id" ng-repeat="state in billingStates">{{state.state_name}}</md-option>

          </md-select>

        </md-input-container>

        <md-input-container class="md-block">

          <label><?php echo lang2('city'); ?></label>

          <input name="city" ng-model="customer.billing_city">

        </md-input-container>

        <md-input-container class="md-block">

          <label><?php echo lang2('zipcode'); ?></label>

          <input name="zipcode" ng-model="customer.billing_zip">

        </md-input-container>

        <br>

      </md-content>

      <md-subheader class="md-primary">

        <md-truncate><?php echo lang2('shipping_address') ?></md-truncate>

        <md-button ng-click='SameAsBillingAddress()' class="md-icon-button" aria-label="Favorite">

          <md-icon class="ion-ios-copy">

            <md-tooltip md-direction="top"><?php echo lang2('same_as_billing') ?></md-tooltip>

          </md-icon>

        </md-button>

        <md-button class="pull-right hide-pinned-projects md-icon-button" aria-label="<?php echo lang2('shipping_address') ?>">

          <a data-toggle="collapse" data-parent="#shipping_address" href="#shipping_address">

            <md-icon class="ion-chevron-down">

            </md-icon>

          </a>

        </md-button>

      </md-subheader>

      <md-content layout-padding id="shipping_address" class="panel-collapse collapse out">

        <md-input-container class="md-block">

          <label><?php echo lang2('address') ?></label>

          <textarea ng-model="customer.shipping_street" name="address" md-maxlength="500" rows="3" md-select-on-focus></textarea>

        </md-input-container>

        <md-input-container class="md-block">

          <label><?php echo lang2('country'); ?></label>

          <md-select placeholder="<?php echo lang2('country'); ?>" ng-model="customer.shipping_country" ng-change="getShippingStates(customer.shipping_country)" name="shipping_country" style="min-width: 200px;">

            <md-option ng-value="country.id" ng-repeat="country in countries">{{country.shortname}}</md-option>

          </md-select>

        </md-input-container>

        <md-input-container class="md-block">

          <label><?php echo lang2('state'); ?></label>

          <md-select placeholder="<?php echo lang2('states'); ?>" ng-model="customer.shipping_state_id" name="shipping_state_id" style="min-width: 200px;">

            <md-option ng-value="state.id" ng-repeat="state in shippingStates">{{state.state_name}}</md-option>

          </md-select>

        </md-input-container>

        <md-input-container class="md-block">

          <label><?php echo lang2('city'); ?></label>

          <input name="city" ng-model="customer.shipping_city">

        </md-input-container>



        <md-input-container class="md-block">

          <label><?php echo lang2('zipcode'); ?></label>

          <input name="zipcode" ng-model="customer.shipping_zip">

        </md-input-container>

      </md-content>

      <custom-fields-vertical></custom-fields-vertical>

      <md-content class="layout-padding">

      </md-content>

      <md-content layout-padding>

        <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>

          <md-button ng-click="AddCustomer()" class="md-raised md-primary btn-report block-button" ng-disabled="saving == true">

            <span ng-hide="saving == true"><?php echo lang2('create'); ?></span>

            <md-progress-circular class="white" ng-show="saving == true" md-mode="indeterminate" md-diameter="20">

            </md-progress-circular>

          </md-button>

          <br /><br /><br /><br />

        </section>

      </md-content>

    </md-content>

  </md-sidenav>



  <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="CreateGroup" ng-cloak style="width: 450px;">

    <md-toolbar class="toolbar-white" style="background:#262626;">

      <div class="md-toolbar-tools" style="padding-top: 40px;padding-bottom: 40px;">

        <md-button ng-click="close()" class="md-icon-button" aria-label="Close"><i class="ion-android-arrow-forward"></i></md-button>

        <md-truncate><?php echo lang2('groups') ?></md-truncate>

        <?php if (check_privilege('customers', 'create')) { ?>

          <md-button aria-label="Add Status" class="md-icon-button" ng-click="NewGroup()" style="position: absolute; right: 20px;">
            <md-tooltip md-direction="bottom"><?php echo lang2('add') . ' ' . lang2('customer') . ' ' . lang2('group') ?>
            </md-tooltip>
            <md-icon><i class="ion-plus-round text-success"></i></md-icon>
          </md-button>

        <?php } ?>

      </div>

    </md-toolbar>

    <md-content>



      <md-list-item ng-repeat="name in group" class="noright" ng-click="EditGroup(name.id,name.name, $event)" aria-label="Edit Status"> <strong ng-bind="name.name"></strong>

        <?php if (check_privilege('customers', 'edit')) { ?>

          <md-icon class="md-secondary md-hue-3 ion-compose " aria-hidden="Edit group"></md-icon>

        <?php }
        if (check_privilege('customers', 'delete')) { ?>

          <md-icon ng-click='DeleteCustomerGroup($index)' aria-label="Remove Status" class="md-secondary md-hue-3 ion-trash-b"></md-icon>

        <?php } ?>

      </md-list-item>

    </md-content>

  </md-sidenav>

  <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="ContentFilter" ng-cloak style="width: 450px;">

    <md-toolbar class="md-theme-light" style="background:#262626">

      <div class="md-toolbar-tools">

        <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>

        <md-truncate><?php echo lang2('filter') ?></md-truncate>

      </div>

    </md-toolbar>

    <md-content layout-padding="">

      <div ng-repeat="(prop, ignoredValue) in customers[0]" ng-init="filter[prop]={}" ng-if="prop != 'id' && prop != 'name' && prop != 'address' && prop != 'email' && prop != 'phone' && prop != 'balance' && prop != 'customer_id' && prop != 'contacts' && prop != 'billing_street' && prop != 'billing_city' && prop != 'billing_state_id' && prop != 'billing_state' && prop != 'billing_zip' && prop != 'billing_country_id' && prop != 'billing_country' && prop != 'shipping_street' && prop != 'shipping_city' && prop != 'shipping_state' && prop != 'shipping_state_id' && prop != 'shipping_zip' && prop != 'shipping_country' && prop != 'shipping_country_id' && prop != 'customer_country' && prop != 'default_payment_method' && prop != 'state_id' && prop != 'group_name' && prop != 'group_id' && prop != 'customer_number'">

        <div class="filter col-md-12">

          <h4 class="text-muted text-uppercase"><strong>{{prop}}</strong></h4>

          <hr>

          <div class="labelContainer" ng-repeat="opt in getOptionsFor(prop)" ng-if="prop!='<?php echo lang2('filterbycountry') ?>' && prop!='<?php echo lang2('filterbyassigned') ?>'">

            <md-checkbox id="{{[opt]}}" ng-model="filter[prop][opt]" aria-label="{{opt}}"><span class="text-uppercase">{{opt}}</span></md-checkbox>

          </div>

          <div ng-if="prop=='<?php echo lang2('filterbycountry') ?>'">

            <md-select aria-label="Filter" ng-model="filter_select" ng-init="filter_select='all'" ng-change="updateDropdown(prop)">

              <md-option value="all"><?php echo lang2('all') ?></md-option>

              <md-option ng-repeat="opt in getOptionsFor(prop) | orderBy:'':true" value="{{opt}}">{{opt}}</md-option>

            </md-select>

          </div>

        </div>

      </div>

    </md-content>

  </md-sidenav>

  <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="ImportCustomersNav" ng-cloak style="width: 450px;">
    <md-toolbar class="md-theme-light" style="background:#262626">
      <div class="md-toolbar-tools">
        <md-button ng-click="close()" class="md-icon-button" aria-label="Close"><i class="ion-android-arrow-forward"></i></md-button>
        <md-truncate><?php echo lang2('importcustomers') ?></md-truncate>
      </div>
    </md-toolbar>
    <md-content>
      <?php  helper('form'); echo form_open_multipart('customers/customersimport'); ?>
      <div class="modal-body">
        <div class="form-group">
          <label for="name">
            <?php echo lang2('choosecsvfile'); ?>
          </label>
          <div class="file-upload">
            <div class="file-select">
              <div class="file-select-button" id="fileName"><span class="mdi mdi-accounts-list-alt"></span>
                <?php echo lang2('attachment') ?>
              </div>
              <div class="file-select-name" id="noFile">
                <?php echo lang2('notchoise') ?>
              </div>
              <input type="file" name="userfile" id="chooseFile" required="" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" file-model="customer_file">
            </div>
          </div>
        </div>
        <br>
        <div class="well well-sm"><?php echo lang2('importcustomersinfo'); ?></div>
      </div>
      <div class="modal-footer">
        <a href="<?php echo base_url('uploads/samples/customerimport5.csv') ?>" class="btn btn-success pull-left"><?php echo lang2('downloadsample'); ?></a>
        <button type="button" ng-click="importCustomer()" class="btn btn-default"><?php echo lang2('save'); ?></button>
      </div>
      <?php echo form_close(); ?>
      <div ng-show="importerror">
        <md-content>
          <ul>
            <li ng-repeat="error in errors">
              <p><?php echo lang2('row') . ' ' ?>{{error.line}}<?php echo ' ' . lang2('importSkipError') ?></p>
            </li>
          </ul>
        </md-content>
      </div>
    </md-content>
  </md-sidenav>

</div>

<script type="text/javascript">
  var lang = {};

  lang.customer = '<?php echo lang2('customer') ?>';

  lang.group = '<?php echo lang2('group') ?>';

  lang.new = '<?php echo lang2('new') ?>';

  lang.name = '<?php echo lang2('name') ?>';

  lang.add = '<?php echo lang2('add') ?>';

  lang.cancel = '<?php echo lang2('cancel') ?>';

  lang.save = '<?php echo lang2('save') ?>';

  lang.edit = '<?php echo lang2('edit') ?>';

  lang.doIt = '<?php echo lang2('doIt') ?>';
  lang.attention = '<?php echo lang2('attention') ?>';
</script>

<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>

<script src="<?php echo base_url('assets/lib/highcharts/highcharts.js') ?>"></script>


<script type="text/javascript" src="<?php echo base_url('assets/js/customers.js?v=2.2.4') ?>"></script>