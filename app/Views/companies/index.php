<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<?php $appconfig = get_appconfig(); ?>
<link href="<?php echo base_url('assets/lib/select2/select2.min.css'); ?>" rel="stylesheet" />

<style>
    .select2-container {
        width: 100% !important;
    }

    .select2-selection {
        min-height: 48px !important;
    }

    .modal md-select.md-default-theme .md-select-value,
    md-select .md-select-value {
        border-bottom-color: transparent !important;
    }

    .modal md-select.md-default-theme:not([disabled]):focus .md-select-value,
    md-select:not([disabled]):focus .md-select-value {
        border-bottom-color: transparent !important;
        color: rgba(0, 0, 0, 0.87);
    }

    .form-control {
        margin: 0 !important;
        margin-bottom: 0 !important;
    }
</style>
<div class="ciuis-body-content" ng-controller="Companies_Controller">
    <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-9">
        <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">
                <md-button class="md-icon-button" aria-label="File">
                    <md-icon><i class="ion-document text-muted"></i></md-icon>
                </md-button>
                <h2 flex md-truncate class="text-bold">
                    Empresas
                    <br>
                    <small flex md-truncate>Configurações de administrador</small>
                </h2>
                <?php helper('form');
                echo form_open_multipart('companies/exportdata', array("class" => "form-horizontal")); ?>
                <md-button type="submit" class="md-icon-button btn_settings" style="background: transparent;">
                    <div layout="row" flex>
                        <md-tooltip md-direction="bottom">Exportar empresas</md-tooltip>
                        <i class="fas fa-download" style="color: #3f6ad8;font-size: 17px !important;"></i>
                    </div>
                </md-button>
                <?php echo form_close(); ?>

                <div class="ciuis-external-search-in-table">
                    <input ng-model="customer_search" class="search-table-external" id="search" name="search" type="text" placeholder="<?php echo lang2('searchword') ?>">
                    <md-button class="md-icon-button" aria-label="Search" ng-cloak>
                        <md-icon><i class="ion-search text-muted"></i></md-icon>
                    </md-button>
                </div>

                <md-button ng-click="Create()" class="md-icon-button" aria-label="New" ng-cloak>
                    <md-tooltip md-direction="bottom"><?php echo lang2('create') ?></md-tooltip>
                    <md-icon><i class="ion-android-add-circle text-success"></i></md-icon>
                </md-button>
            </div>
        </md-toolbar>

        <div ng-show="companiesLoader" layout-align="center center" class="text-center" id="circular_loader" ng-cloak>
            <md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
            <p style="font-size: 15px;margin-bottom: 5%;">
                <span><?php echo lang2('please_wait') ?> <br>
                    <small><strong><?php echo lang2('loading') . ' empresas...' ?></strong></small></span>
            </p>
        </div>

        <md-content ng-show="!companiesLoader" class="bg-white" ng-cloak>
            <md-tabs md-dynamic-height md-border-bottom>
                <md-tab label="Ativos">
                    <md-table-container ng-show="companies.length > 0">
                        <table md-table md-progress="promise">
                            <thead md-head md-order="customer_list.order">
                                <tr md-row>
                                    <th md-column><span>#</span></th>
                                    <th md-column md-order-by="name"><?php echo lang2('name'); ?></th>
                                    <th md-column md-order-by="status"><?php echo lang2('Status'); ?></th>
                                    <th md-column md-order-by="acoes" width="250px">Ações</th>
                                </tr>
                            </thead>

                            <tbody md-body>
                                <tr class="select_row" md-row ng-repeat="company in companies | filter: { status: 1 }" class="cursor">
                                    <td md-cell>
                                        <strong>
                                            <a class="link" ng-href="/companies/company/{{company.id_company}}"> <span ng-bind="company.id_company"></span></a>
                                        </strong>
                                    </td>

                                    <td md-cell>
                                        <strong><span ng-if="company.tp_pessoa == '2'" ng-bind="company.nm_company"></span></strong>
                                        <strong><span ng-if="company.tp_pessoa == '1'" ng-bind="company.name"></span></strong>
                                    </td>

                                    <td md-cell>
                                        {{company.status == 0 ? 'Inativo' : 'Ativo'}}
                                    </td>

                                    <td md-cell>
                                        <md-button style="width: 30px;height: 30px;" ng-click="Permissions(company.id_company)" class="md-icon-button md-primary" aria-label="Actions" ng-cloak>
                                            <md-icon style="font-size: 21px;"><i class="fas fa-tasks"></i></md-icon>
                                        </md-button>

                                        <md-button style="width: 30px;height: 30px;" ng-click="Update(company)" class="md-icon-button md-primary" aria-label="Actions" ng-cloak>
                                            <md-icon style="font-size: 21px;" class="mdi mdi-edit"></md-icon>
                                        </md-button>

                                        <md-button style="width: 30px;height: 30px;" ng-click="Remove(company.id_company)" class="md-icon-button md-primary" aria-label="Actions" ng-cloak>
                                            <md-icon style="font-size: 21px;" class="mdi mdi-close"></md-icon>
                                        </md-button>

                                        <md-button style="width: 30px;height: 30px;" ng-click="openModalReports(company.id_company)" class="md-icon-button md-primary" aria-label="Actions" ng-cloak>
                                            <md-icon style="font-size: 21px;"> <i class="fas fa-envelope"></i></md-icon>
                                        </md-button>

                                        <a href="<?= base_url('companies/login/'); ?>{{company.id_company}}" style="width: 30px;height: 30px; display: inline-flex;" class="md-icon-button md-primary" aria-label="Actions" ng-cloak>
                                            <md-tooltip md-direction="bottom">Logar como</md-tooltip>
                                            <md-icon style="font-size: 21px;"> <i class="fas fa-sign-in-alt"></i></md-icon>
                                        </a>

                                    </td>


                                </tr>

                            </tbody>

                        </table>

                    </md-table-container>

                    <md-table-pagination ng-show="companies.length > 0" md-limit="companies_list.limit" md-limit-options="limitOptions" md-page="companies_list.page" md-total="{{companies.length}}"></md-table-pagination>
                    <md-content ng-show="!companies.length && !companiesLoader" class="md-padding no-item-data">
                        <?php echo lang2('notdata') ?></md-content>

                </md-tab>

                <md-tab label="Inativos">


                    <md-table-container ng-show="companies.length > 0">
                        <table md-table md-progress="promise">
                            <thead md-head md-order="customer_list.order">
                                <tr md-row>
                                    <th md-column><span>#</span></th>
                                    <th md-column md-order-by="name"><?php echo lang2('name'); ?></th>
                                    <th md-column md-order-by="status"><?php echo lang2('Status'); ?></th>
                                    <th md-column md-order-by="acoes" width="250px">Ações</th>
                                </tr>
                            </thead>

                            <tbody md-body>
                                <tr class="select_row" md-row ng-repeat="company in companies  | filter: { status: 0 }" class="cursor">
                                    <td md-cell>
                                        <strong>
                                            <a class="link" ng-href="/companies/company/{{company.id_company}}"> <span ng-bind="company.id_company"></span></a>
                                        </strong>
                                    </td>

                                    <td md-cell>
                                        <strong><span ng-if="company.tp_pessoa == '2'" ng-bind="company.nm_company"></span></strong>
                                        <strong><span ng-if="company.tp_pessoa == '1'" ng-bind="company.name"></span></strong>
                                    </td>

                                    <td md-cell>
                                        {{company.status == 0 ? 'Inativo' : 'Ativo'}}
                                    </td>

                                    <td md-cell>
                                        <md-button style="width: 30px;height: 30px;" ng-click="Permissions(company.id_company)" class="md-icon-button md-primary" aria-label="Actions" ng-cloak>
                                            <md-icon style="font-size: 21px;"><i class="fas fa-tasks"></i></md-icon>
                                        </md-button>

                                        <md-button style="width: 30px;height: 30px;" ng-click="Update(company)" class="md-icon-button md-primary" aria-label="Actions" ng-cloak>
                                            <md-icon style="font-size: 21px;" class="mdi mdi-edit"></md-icon>
                                        </md-button>

                                        <md-button style="width: 30px;height: 30px;" ng-click="Remove(company.id_company)" class="md-icon-button md-primary" aria-label="Actions" ng-cloak>
                                            <md-icon style="font-size: 21px;" class="mdi mdi-close"></md-icon>
                                        </md-button>

                                        <md-button style="width: 30px;height: 30px;" ng-click="openModalReports(company.id_company)" class="md-icon-button md-primary" aria-label="Actions" ng-cloak>
                                            <md-icon style="font-size: 21px;"> <i class="fas fa-envelope"></i></md-icon>
                                        </md-button>

                                        <a href="<?= base_url('companies/login/'); ?>{{company.id_company}}" style="width: 30px;height: 30px; display: inline-flex;" class="md-icon-button md-primary" aria-label="Actions" ng-cloak>
                                            <md-tooltip md-direction="bottom">Logar como</md-tooltip>
                                            <md-icon style="font-size: 21px;"> <i class="fas fa-sign-in-alt"></i></md-icon>
                                        </a>

                                    </td>


                                </tr>

                            </tbody>

                        </table>

                    </md-table-container>

                    <md-table-pagination ng-show="companies.length > 0" md-limit="companies_list.limit" md-limit-options="limitOptions" md-page="companies_list.page" md-total="{{companies.length}}"></md-table-pagination>
                    <md-content ng-show="!companies.length && !companiesLoader" class="md-padding no-item-data">
                        <?php echo lang2('notdata') ?></md-content>

                </md-tab>
            </md-tabs>
        </md-content>


    </div>

    <!-- <ciuis-sidebar ng-show="!companiesLoader"></ciuis-sidebar> -->
    <div class="main-content container-fluid col-xs-12 col-md-3 col-lg-3 md-pl-0 lead-left-bar">
        <div class="panel-default panel-table borderten lead-manager-head">
            <md-toolbar class="toolbar-white">
                <div class="md-toolbar-tools">
                </div>
            </md-toolbar>
            <div class="tasks-status-stat">
                <div class="widget-chart-container">
                    <div class="widget-counter-group widget-counter-group-right">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Create" style="width: 450px;" ng-cloak>
        <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">
                <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>
                <h2 flex md-truncate>{{company.id_company != null ? 'Atualizar' : 'Adicionar'}}</h2>
                <md-switch ng-model="company.ativo" aria-label="Active"><strong class="text-muted"><?php echo lang2('active') ?></strong></md-switch>
            </div>
        </md-toolbar>
        <md-content layout-padding>

            <md-input-container class="md-block ">
                <label>Tipo de pessoa</label>
                <md-select required placeholder="" ng-model="company.tp_pessoa" style="min-width: 200px;">
                    <md-option ng-value="1">Pessoa física</md-option>
                    <md-option ng-value="2">Pessoa jurídica</md-option>
                </md-select>
            </md-input-container>

            <div ng-show="company.tp_pessoa != null" class="addLeadDiv">

                <md-input-container class="md-block" ng-if="company.tp_pessoa == 1">
                    <label><?php echo lang2('name'); ?></label>
                    <input required name="name" ng-model="company.name">
                </md-input-container>

                <md-input-container class="md-block" ng-if="company.tp_pessoa == 2">
                    <label><?php echo lang2('company'); ?></label>
                    <input required ng-model="company.nm_company">
                </md-input-container>

                <md-input-container class="md-block" ng-if="company.tp_pessoa == 2">
                    <label>Cnpj</label>
                    <input name="cnpj" ng-model="company.cnpj">
                </md-input-container>

                <md-input-container class="md-block" ng-if="company.tp_pessoa == 2">
                    <label>Website</label>
                    <input name="web_site" ng-model="company.web_site">
                </md-input-container>

                <md-input-container class="md-block" ng-if="company.tp_pessoa == 1">
                    <label>Cpf</label>
                    <input name="cpf" ng-model="company.cpf">
                </md-input-container>

                <md-input-container class="md-block" ng-if="company.tp_pessoa == 2">
                    <label>Setor de atividade</label>
                    <input name="setor_atividade" ng-model="company.setor_atividade">
                </md-input-container>

                <md-input-container class="md-block" ng-if="company.tp_pessoa == 1">
                    <label>Data de nascimento</label>
                    <input name="dt_nascimento" type="date" ng-model="company.dt_nascimento">
                </md-input-container>

                <md-input-container class="md-block" ng-if="company.tp_pessoa == 2">
                    <label>Porte</label>
                    <input name="porte" ng-model="company.porte">
                </md-input-container>

                <md-input-container class="md-block">
                    <label>Telefone</label>
                    <input name="phone" ng-model="company.phone">
                </md-input-container>


                <md-input-container class="md-block">
                    <label><?php echo lang2('email'); ?></label>
                    <input type="email" ng-model="company.email" minlength="10" maxlength="100" ng-pattern="/^.+@.+\..+$/">
                </md-input-container>

                <md-input-container class="md-block" ng-if="company.tp_pessoa == 1">
                    <label>Instagram</label>
                    <input name="instagram" ng-model="company.instagram">
                </md-input-container>

                <md-input-container class="md-block" ng-if="company.tp_pessoa == 1">
                    <label>Facebook</label>
                    <input name="facebook" ng-model="company.facebook">
                </md-input-container>

                <md-input-container class="md-block" ng-if="company.tp_pessoa == 1">
                    <label>Linkedin</label>
                    <input name="linkedin" ng-model="company.linkedin">
                </md-input-container>

                <md-input-container class="md-block">
                    <label><?php echo lang2('address') ?></label>
                    <textarea ng-model="company.address" md-maxlength="500" rows="3" md-select-on-focus></textarea>
                </md-input-container>

                <md-input-container class="md-block">
                    <label><?php echo lang2('city'); ?></label>
                    <input ng-model="company.city">
                </md-input-container>

                <md-input-container class="md-block">
                    <label><?php echo lang2('zip'); ?></label>
                    <input ng-model="company.zip">
                </md-input-container>

                <md-input-container class="md-block">
                    <label><?php echo lang2('country'); ?></label>
                    <md-select placeholder="<?php echo lang2('country'); ?>" ng-model="company.country_id" ng-change="getStates(company.country_id)" name="country_id" style="min-width: 200px;">
                        <md-option ng-value="country.id" ng-repeat="country in countries">{{country.shortname}}</md-option>
                    </md-select>
                </md-input-container>

                <md-input-container class="md-block" style="margin-top: 3em;">
                    <label><?php echo lang2('state'); ?></label>
                    <md-select placeholder="<?php echo lang2('states'); ?>" ng-model="company.state_id" name="state_id" style="min-width: 200px;">
                        <md-option ng-value="state.id" ng-repeat="state in states">{{state.state_name}}</md-option>
                    </md-select>
                </md-input-container>


                <md-input-container class="md-block" style="margin-top: 3em;">
                    <label><?php echo lang2('source'); ?></label>
                    <md-select placeholder="<?php echo lang2('source'); ?>" ng-change="$('#date_contacted').focus()" ng-model="company.source_id" style="min-width: 200px;">
                        <md-option ng-value="source.id" ng-repeat="source in leadssources">{{source.name}}</md-option>
                    </md-select>
                </md-input-container>

                <md-input-container class="md-block" style="margin-top: 3em;">
                    <label><?php echo lang2('description') ?></label>
                    <textarea ng-model="company.description" md-maxlength="500" rows="3" md-select-on-focus></textarea>
                </md-input-container>

                <md-input-container class="md-block">
                    <label><?php echo lang2('date_contacted') ?></label>
                    <input type="date" placeholder="<?php echo lang2('chooseadate') ?>" ng-model="company.date_contacted" class="form-control">
                </md-input-container>

                <md-input-container class="md-block">
                    <label>Vencimento do plano</label>
                    <input type="date" placeholder="Vencimento do plano" ng-model="company.vencimento" class="form-control">
                </md-input-container>

                <md-input-container class="md-block">
                    <label>Créditos no lead+1</label>
                    <input type="number" placeholder="Créditos no lead+1" ng-model="company.creditos" class="form-control">
                </md-input-container>

                <md-input-container class="md-block">
                    <label>Créditos de enriquecimento</label>
                    <input type="number" placeholder="Créditos de enriquecimento" ng-model="company.creditos_enriquecimento" class="form-control">
                </md-input-container>

                <!--
                <md-input-container class="md-block ">
                    <label>Status</label>
                    <md-select required placeholder="" ng-model="company.status" style="min-width: 200px;">
                        <md-option selected ng-value="1">Ativo</md-option>
                        <md-option ng-value="0">Inativo</md-option>
                    </md-select>
                </md-input-container>
-->

                <md-input-container class="md-block" style="margin-top: 2em;border-bottom: 1px solid #d9d9d9;margin-bottom: 2em !important;">
                    <label>Renovar créditos</label>
                    <md-select placeholder="Renovar créditos" ng-model="company.renovar" style="min-width: 200px;">
                        <md-option ng-value="1">Sim</md-option>
                        <md-option ng-value="0">Não</md-option>
                    </md-select>
                </md-input-container>

                <md-input-container class="md-block" ng-if="company.renovar == 1">
                    <label>Valor a ser renovado mensalmente</label>
                    <input type="number" placeholder="Valor a ser renovado" ng-model="company.renovar_valor" class="form-control">
                </md-input-container>

                <md-input-container class="md-block" style="margin-top: 2em;border-bottom: 1px solid #d9d9d9;margin-bottom: 2em !important;">
                    <label>Crédito acumulativos</label>
                    <md-select placeholder="Crédito acumulativos" ng-model="company.credito_acumulativo" style="min-width: 200px;">
                        <md-option ng-value="1">Sim</md-option>
                        <md-option ng-value="0">Não</md-option>
                    </md-select>
                </md-input-container>

            </div>
        </md-content>

        <md-content layout-padding ng-show="company.tp_pessoa != null">
            <section layout="row" layout-sm="column" layout-align="center" layout-wrap>
                <md-button ng-click="AddCompany()" class="md-raised md-primary btn-report block-button">
                    <span>{{company.id_company != null ? 'Atualizar' : 'Adicionar'}}</span>
                    <md-progress-circular class="white" md-mode="indeterminate" md-diameter="20">
                    </md-progress-circular>
                </md-button>
                <br /><br /><br /><br />
            </section>
        </md-content>
    </md-sidenav>

    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Permissions" style="width: 450px;" ng-cloak>
        <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">
                <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>
                <h2 flex md-truncate>Permissões</h2>
            </div>
        </md-toolbar>
        <md-content layout-padding>
            <div ng-show="permissionModalLoader" layout-align="center center" class="text-center" id="circular_loader" ng-cloak>
                <md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
                <p style="font-size: 15px;margin-bottom: 5%;">
                    <span><?php echo lang2('please_wait') ?>
                </p>
            </div>

            <table md-table md-progress="promise">
                <thead md-head>
                    <tr md-row>
                        <th md-column>
                            <md-checkbox md-no-ink aria-label="edit" ng-model="permissionModal.permission_all" ng-change="selectAll()" class="md-primary"></md-checkbox>
                        </th>
                        <th md-column>Permissão</th>
                    </tr>
                </thead>
                <tbody md-body>
                    <tr class="select_row" md-row ng-repeat="permission in permissions_all">
                        <td md-cell>
                            <md-checkbox ng-checked="permission.permitido == '1'" md-no-ink aria-label="edit" ng-model="permissionModal.permission[permission.id]" class="md-primary"></md-checkbox>
                        </td>
                        <td md-cell>
                            <span ng-bind="permission.permission_key"></span>
                        </td>
                    </tr>

                    <tr md-row>
                        <th style="padding-left: 28px;">
                            #
                        </th>
                        <th>Permissão para exportar</th>
                    </tr>

                    <tr class="select_row" md-row ng-repeat="permission in permissions_all_export">
                        <td md-cell>
                            <md-checkbox ng-checked="permission.permitido == '1'" md-no-ink aria-label="edit" ng-model="permissionModal_export.permission[permission.id]" class="md-primary"></md-checkbox>
                        </td>
                        <td md-cell>
                            <span ng-bind="permission.permission_key"></span>
                        </td>
                    </tr>
                </tbody>
            </table>


        </md-content>

        <md-content layout-padding>
            <section layout="row" layout-sm="column" layout-align="center" layout-wrap>
                <md-button ng-click="AddPermission()" class="md-raised md-primary btn-report block-button">
                    <span><?php echo lang2('create'); ?></span>
                    <md-progress-circular class="white" md-mode="indeterminate" md-diameter="20">
                    </md-progress-circular>
                </md-button>
            </section>
        </md-content>
    </md-sidenav>


    <div class="modal fade" ng-repeat="company in companies" id="modalReport{{company.id_company}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Report Automáticos
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="layout-wrap layout-row" style="padding: 0 5px;">
                        <div class="flex-xs-100 flex-gt-xs-50">
                            <div class="form-group">
                                <label for="ativo">Status</label>
                                <md-select ng-model="company.modalReport.status" class="form-control" id="status">
                                    <md-option ng-value="1">Ativo</md-option>
                                    <md-option ng-value="0">Inativo</md-option>
                                </md-select>
                            </div>
                        </div>

                        <div class="flex-xs-100 flex-gt-xs-50" style="padding: 0 5px;">
                            <div class="form-group">
                                <label for="ativo">Frequência</label>
                                <md-select ng-model="company.modalReport.frequencia" class="form-control" id="frequencia">
                                    <md-option ng-value="7">Semanal</md-option>
                                    <md-option ng-value="15">Quinzenal</md-option>
                                    <md-option ng-value="30">Mensal</md-option>
                                    <md-option ng-value="60">Bimestral</md-option>
                                    <md-option ng-value="90">Trimestral</md-option>
                                    <md-option ng-value="180">Semestral</md-option>
                                    <md-option ng-value="365">Anual</md-option>
                                </md-select>
                            </div>
                        </div>
                    </div>

                    <div class="layout-wrap layout-row" style="margin-bottom: 20px;">
                        <div class="flex-xs-100 flex-gt-xs-50" style="padding: 0 5px;">
                            <div class="form-group">
                                <label for="ativo">Data de início </label>
                                <input type="date" ng-model="company.modalReport.data" class="form-control" id="dt_inicio" placeholder="Data de início" required>
                            </div>
                        </div>

                        <div class="flex-xs-100 flex-gt-xs-50" style="padding: 0 5px;">
                            <div class="form-group">
                                <label for="emails">Emails</label>
                                <select id="emails" ng-model="company.modalReport.emails" ng-change="changeE(company)" class="form-control select2" multiple="multiple">
                                    <option ng-value="email" ng-repeat="email in company.modalReport.TodosEmails">{{email}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex-xs-100 flex-gt-xs-80" style="padding: 0 5px;">
                            <div class="form-group">
                                <label for="modelo_de_email">Modelo de email</label>
                                <md-select id="modelo_de_email" ng-model="company.modalReport.modelo_de_email" class="form-control">
                                    <md-option ng-if="template.relation == 'reports'" ng-value="template.id" ng-repeat="template in templates">{{template.name != null ? template.name : template.subject}}</md-option>
                                </md-select>
                            </div>
                        </div>
                    </div>
                    <div class="layout-wrap layout-row">
                        <div class="flex-xs-100 flex-gt-xs-100">
                            <table md-table md-progress="promise">
                                <thead md-head>
                                    <tr md-row>
                                        <th md-column>
                                            <md-checkbox md-no-ink aria-label="edit" ng-model="company.modalReport.permission.permission_all" ng-change="selectAllModal2(company)" class="md-primary"></md-checkbox>
                                        </th>
                                        <th md-column>Permissão</th>
                                    </tr>
                                </thead>

                                <tbody md-body>
                                    <tr class="select_row" ng-if="inArray(permission.id, [24,25,26])" md-row ng-repeat="permission in company.permissoes">
                                        <td md-cell>
                                            <md-checkbox ng-checked="company.modalReport.permission.permission[permission.id]" md-no-ink aria-label="edit" ng-model="company.modalReport.permission.permission[permission.id]" class="md-primary"></md-checkbox>
                                        </td>
                                        <td md-cell>
                                            <span ng-bind="permission.permission_key"></span>
                                        </td>
                                    </tr>
                                </tbody>

                            </table>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" ng-click="saveModalReport(company)">Salvar</button>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    var lang = {};
    lang.doIt = "<?php echo lang2('doIt') ?>";
    lang.cancel = "<?php echo lang2('cancel') ?>";
    lang.attention = "<?php echo lang2('attention') ?>";
</script>

<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>
<script src="<?php echo base_url('assets/lib/chartjs/dist/Chart.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/companies.js?v=2.4.13'); ?>"></script>