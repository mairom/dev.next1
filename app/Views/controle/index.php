
<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<link href="<?php echo base_url('assets/lib/select2/select2.min.css'); ?>" rel="stylesheet" />
<link href="<?php echo base_url('assets/css/cssCartao.css?v=1.1.2'); ?>" rel="stylesheet" />
<?php $appconfig = get_appconfig(); ?>
<div class="ciuis-body-content" ng-controller="Controle_Controller">
    <md-content class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">
        <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">
                <md-button class="md-icon-button" aria-label="Settings" ng-disabled="true">
                    <md-icon><i class="ico-ciuis-leads text-warning"></i></md-icon>
                </md-button>
                <h2 flex md-truncate><?= $title ?></h2>


            </div>


        </md-toolbar>
        <md-content id="contentMain">
            <md-content class="widget-fullwidth ciuis-body-loading" style="min-height: 85px;">
                <div class='filtrosLeads' style="min-height: 0;padding: 1em;">

                    <md-input-container class="md-block">
                        <md-select placeholder="Ano" ng-model="filtros.ano" ng-change="getResultados()" style="min-width: 200px;">
                            <md-option ng-value="ano" ng-repeat="ano in anosList">{{ano}}</md-option>
                        </md-select>
                    </md-input-container>

                    <md-input-container class="md-block">
                        <md-select placeholder="Mês" ng-model="filtros.mes" ng-change="getResultados()" style="min-width: 200px;">
                            <md-option ng-value="-1">Todos</md-option>
                            <md-option ng-value="mes.numero" ng-repeat="mes in mesList">{{mes.name}}</md-option>
                        </md-select>
                    </md-input-container>

                    <?php
                    if (session()->super_admin == "1") {
                    ?>
                        <md-input-container class="md-block">
                            <label>Empresa</label>
                            <md-select placeholder="Escolha uma empresa" id="id_company" ng-model="filtros.id_company" ng-change="getResultados();" style="min-width: 200px;">
                                <md-option ng-value="-1">Todos</md-option>
                                <md-option ng-value="company.id_company" ng-repeat="company in companies">{{company.nm_company}}</md-option>
                            </md-select>
                        </md-input-container>
                    <?php
                    }
                    ?>


                    <md-button ng-click="baixarPdf()" style="width: 100px;height: 65px;float: right;color: #858585;cursor: pointer;" class="md-icon-button" aria-label="Settings">
                        <md-icon><i style="color: #8e44ad;" class="far fa-file-pdf"></i></md-icon><span>Gerar PDF</span>
                    </md-button>
                </div>
            </md-content>



            <md-content class="md-padding bg-white">

                <div class="row card-v2">
                    <div class="col-sm-6 col-lg mb-4 p-3 cd-body" style="max-width: 350px;">
                        <div class="card">
                            <div class="card-header p-3 pt-2">
                                <div style="background-image: linear-gradient(195deg, #fba528 0%, #e7c69e 100%);" class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                                    <i class="fas fa-wallet" style="font-size: 24px;color: #1f1f1f;"></i>
                                </div>
                                <div class="text-end pt-1">
                                    <p class="text-sm mb-0 text-capitalize">Total gasto</p>
                                    <h4 style="font-size: 3rem; color: #f0b56b;" class="mb-0" ng-bind-html="totalGasto | currencyFormat:cur_code:null:true:cur_lct"></h4>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">

                        </div>
                    </div>
                </div>


                <h3 style="margin: 0 0px 15px 0;">Compras da empresa</h3>
                <md-table-container>
                    <table md-table md-progress="promise">
                        <thead md-head>
                            <tr md-row>
                                <th md-column>#</th>
                                <th md-column md-order-by="empresa">Empresa</th>
                                <th md-column md-order-by="data">Data</th>
                                <th md-column md-order-by="referente">Referente</th>
                                <th md-column md-order-by="valor">Valor</th>
                            </tr>
                        </thead>

                        <tbody md-body>
                            <tr class="select_row" md-row ng-repeat="fatura in faturas" class="cursor">
                                <td md-cell>
                                    <strong><span style="color: #0716bf;" ng-bind="fatura.id_fatura"></span></strong><br>
                                </td>
                                <td md-cell>
                                    <span ng-bind="fatura.nm_company"></span>
                                </td>
                                <td md-cell>
                                    <span ng-bind="fatura.data"></span>
                                </td>
                                <td md-cell>
                                    <span ng-bind="fatura.referencia"></span>
                                </td>
                                <td md-cell>
                                    <span ng-bind-html="fatura.valor | currencyFormat:cur_code:null:true:cur_lct"></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </md-table-container>

            </md-content>
        </md-content>
    </md-content>
</div>

<script type="text/javascript">


</script>
<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>
<script src="<?php echo base_url('assets/lib/chartjs/dist/Chart.min.js'); ?>" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/controle.js?v=p1.1.1') ?>"></script>