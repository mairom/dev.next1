<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<?php $appconfig = get_appconfig(); ?>
<div class="ciuis-body-content" ng-controller="Customers_sucess_Controller">
    <style type="text/css">
        rect.highcharts-background {
            fill: #f3f3f3;
        }

        .topRow {
            margin-bottom: 30px;
        }

        .on-drag-hover:before {
            display: block;
            color: white;
            font-size: x-large;
            font-weight: 800;
        }

        .noright2:hover {
            background: #e7e7e7;
        }

        .small {
            height: 20px;
        }

        .md-toolbar-tools>md-menu:last-child {
            margin-top: 0px !important;
        }
    </style>

    <div class="ciuis_lead_kanban_board main-content container-fluid col-xs-12 col-md-12 col-lg-12">
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


                    <md-button ng-click="toggleFilter()" class="md-icon-button btn_settings" aria-label="Filter" ng-cloak>
                        <md-tooltip md-direction="bottom"><?php echo lang2('filter') ?></md-tooltip>
                        <md-icon>
                            <i class="fas fa-filter" style="color: #3f6ad8;font-size: 15px !important;"></i>
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


                    <?php if (check_privilege_export('customer_sucess')) { ?>
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

        <div class='filtrosLeads collapseFiltroOpen' id="collapseFiltro">
            <md-input-container class="md-block">
                <i class="iconSearch ion-funnel text-muted "></i>
                <md-select placeholder="Escolha um funil" id="flt_funil" ng-model="filtros.flt_funil" ng-change="changeFunil(filtros.flt_funil);" style="min-width: 200px;">
                    <md-option ng-selected='$index == funilSelect_index' ng-value="$index" ng-repeat="funil in funilsCustomers">{{funil.nm_funil}}</md-option>
                </md-select>
            </md-input-container>

            <md-input-container class="md-block">
                <label>Funcionário</label>
                <md-select placeholder="<?php echo lang2('choosestaff'); ?>" id="flt_funcionario" ng-model="filtros.flt_funcionario" style="min-width: 200px;" ng-change="filtrarCustomers()">
                    <md-option ng-value="-1">Todos</md-option>
                    <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
                </md-select>
            </md-input-container>

        </div>



        <div ng-show="customersLoader" layout-align="center center" class="text-center" id="circular_loader" ng-cloak>
            <md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
            <p style="font-size: 15px;margin-bottom: 5%;">
                <span><?php echo lang2('please_wait') ?> <br>
                    <small><strong><?php echo lang2('loading') . ' ' . lang2('customers') . '...' ?></strong></small></span>
            </p>
        </div>


        <div class="todoLeads">
            <div ng-repeat="funil in funilsCustomers" style="float: left;" ng-show="$index == filtros.flt_funil">
                <md-list class="ciuis_lead_status_card" style="width: 220px;" ng-init="parentIndex = $index" flex ng-repeat="etapa in funil.etapas" ui-on-Drop="onDrop($event,$data,etapa.id, $index)">
                    <div class="div-title-funil" style="min-height: 45px;">
                        <md-toolbar class="toolbar-white md-toolbar-funil" style="min-height: 0;">
                            <div class="md-toolbar-tools toolbarPerson" style="padding-bottom: 5px;padding-top: 5px;border-radius: 5px;padding-left: 10px;padding-right: 5px; border-left: 3px solid {{etapa.color}};">
                                <h3 flex md-truncate>{{etapa.name}} <span>({{getCustomerByStatus(etapa.id).length}})</span></h3>
                                <md-menu md-position-mode="target-right target" style="width: 30px;">
                                    <md-button aria-label="Open demo menu" style="width: 30px;margin-left: -10px;" class="md-icon-button" ng-click="$mdMenu.open($event)">
                                        <md-icon aria-label="Add Source">
                                            <i class="ion-android-more-vertical text-muted" style="vertical-align: super;"></i>
                                        </md-icon>
                                    </md-button>
                                    <md-menu-content width="4">
                                        <?php if (check_privilege('customers', 'edit')) { ?>
                                            <md-menu-item>
                                                <md-button ng-click="EditStatus(etapa.id,etapa.name, $event, $index)" aria-label="Add">
                                                    <div layout="row" flex>
                                                        <p flex><?php echo lang2('edit_status'); ?></p>
                                                        <md-icon aria-label="Add Source" md-menu-align-target class="ion-edit" style="margin: auto 3px auto 0;"></md-icon>
                                                    </div>
                                                </md-button>
                                            </md-menu-item>
                                        <?php } ?>
                                    </md-menu-content>
                                </md-menu>
                            </div>
                        </md-toolbar>
                    </div>
                    <div class="items_list" id="items_list{{etapa.id}}" style="height: 100vh;margin-top: 10px;">
                        <md-list-item class="md-3-line" style="margin: 5px 0px;" ng-if="etapa.id == customer.etapa" ui-draggable="true" drag="customer" on-drop-success="dropSuccessHandler($event,$index,etapa.id)" ng-repeat="customer in customers | orderBy: customer.name | filter: { etapa: etapa.id, view : '1' } ">
                            <div class="md-list-item-text" layout="column">
                                <div layout="row" layout-wrap>
                                    <div flex-gt-xs="80" flex-xs="80">
                                        <h3 flex>
                                            <a class="link" target="_blank" ng-href="<?php echo base_url('customers/customer/') ?>{{customer.id}}" style="font-weight: bold;font-size: 14px;">
                                                {{ customer.name | limitTo: 20 }}{{customer.name.length > 20 ? '...' : ''}}
                                            </a>
                                        </h3>
                                    </div>
                                </div>


                            
                                <p class="small smallCardLe">
                                    <span class="blur " style="color: #000;font-weight: 600;font-size: 11px;"><?php echo lang2('Cliente desde') ?>:</span>
                                    <span>{{ customer.created }}</span>
                                </p>

                                <p class="small smallCardLe">
                                    <span class="blur " style="color: #000;font-weight: 600;font-size: 11px;"><?php echo lang2('Customer sucess') ?>:</span>
                                    <span>{{ customer.name_custumer | limitTo: 30 }}{{customer.name_custumer.length > 28 ? '...' : ''}}</span>
                                </p>


                                <div class="fxVertical2" ng-style="customer.prazo == '1' && {'background':'#26b759'}  || customer.prazo == '2' && {'background':'#ef2828'} || customer.prazo == '3' && {'background':'#eead2d'} "></div>


                            </div>
                        </md-list-item>


                    </div>
                </md-list>
            </div>
        </div>


        <md-content ng-show="!customers.length && !customersLoader" class="md-padding no-item-data">
            <?php echo lang2('notdata') ?>
        </md-content>
        </md-content>
    </div>

    <md-sidenav class="md-sidenav-right md-whiteframe-4dp " md-component-id="Create" style="width: 450px;" ng-cloak>
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

        <md-toolbar class="toolbar-white" style="background:#262626">
            <div class="md-toolbar-tools">

                <md-truncate>Funils</md-truncate>

                <md-button aria-label="Add Status" class="md-icon-button" ng-click="NewFunil()">
                    <md-tooltip md-direction="top">Adicionar novo funil</md-tooltip>
                    <md-icon aria-label="Add Source"><i class="ion-plus-round text-success"></i></md-icon>
                </md-button>
            </div>
        </md-toolbar>

        <md-content>
            <div ng-repeat="list in funilsCustomers">
                <md-toolbar class="toolbar-white" style="background:#262626">
                    <div class="md-toolbar-tools">
                        <h4 class="text-bold text-muted" flex ng-click="EditFunil(list.id,list.nm_funil, $event)">{{list.nm_funil}}</h4>
                        <?php
                        if (check_privilege('leads', 'create')) { ?>
                            <md-button aria-label="Add Status" class="md-icon-button" ng-click="NewStatus(list.id)">
                                <md-tooltip md-direction="top"><?php echo lang2('addstatus') ?></md-tooltip>
                                <md-icon aria-label="Add Source"><i class="ion-plus-round text-success"></i></md-icon>
                            </md-button>
                        <?php }
                        if (check_privilege('leads', 'delete')) { ?>
                            <md-button aria-label="Remove Funil" class="md-icon-button" ng-click='DeleteLeadFunil(list.id)'>
                                <md-tooltip md-direction="top"><?php echo lang2('delete') ?></md-tooltip>
                                <md-icon aria-label="Remove Funil"><i class="fas fa-trash" style="color: #777;font-size: 20px;"></i></md-icon>
                            </md-button>
                        <?php } ?>
                    </div>
                </md-toolbar>

                <ul ui-sortable="sortableOptions" ui-sortable ng-model="list.etapas" style="padding-left: 0px;">
                    <li style="padding: 15px 10px;" ng-repeat="status in list.etapas | orderBy: status.ordem" class="noright noright2" aria-label="Edit Status">
                        <i class="fas fa-sort" style="margin-right: 10px;color: #9b9b9b;"></i>
                        <strong ng-click="EditStatus(status.id,status.name, $event)" ng-bind="status.name"></strong>
                        <?php if (check_privilege('leads', 'delete')) { ?>
                            <md-icon ng-click='DeleteLeadStatus(status.id)' style="float: right;" aria-label="Remove Status" class="md-secondary md-hue-3 ion-trash-b">
                                <md-tooltip md-direction="top"><?php echo lang2('delete') ?></md-tooltip>
                            </md-icon>
                        <?php } ?>
                    </li>
                </ul>

            </div>






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
            <?php helper('form');  echo form_open_multipart('customers/customersimport'); ?>
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
                <a href="<?php echo base_url('uploads/samples/customerimport2.csv') ?>" class="btn btn-success pull-left"><?php echo lang2('downloadsample'); ?></a>
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
<script type="text/javascript" src="<?php echo base_url('assets/js/customers_sucess.js?v=2.2.6') ?>"></script>