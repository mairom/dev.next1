<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<?php $appconfig = get_appconfig(); ?>
<link id="pagestyle" href="<?= base_url('./assets/lib/material-dashboard/material-dashboard.css?v=1.1.10') ?>" rel="stylesheet" />
<style>
    .main-content .card-header {
        min-height: 90px;
    }

    body {
        font-family: "Roboto", Helvetica, Arial, sans-serif;

    }

    .chat-responsive {
        height: 300px;
        width: 100%;
    }

    .title-dash {
        margin-bottom: 15px;
        margin-top: 20px;
        font-weight: 300;
        font-size: 1.5em;
    }
</style>

<div class="ciuis-body-content" ng-controller="RelProdutividade">
    <md-content class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">
        <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">
                <md-button class="md-icon-button" aria-label="Settings" ng-disabled="true">
                    <img class="img-icon-toolbar" src="{{appurl + 'assets/img/pie2.png'}}">
                </md-button>
                <h2 flex md-truncate>Relatório de produtividade</h2>
            </div>
        </md-toolbar>
        <md-content class="">
            <md-tabs md-dynamic-height md-border-bottom md-selected="ctrl.selectedIndex">
                <md-tab label="<?php echo lang2('overview') ?>">

                    <md-content class="md-padding">
                        <md-content class="widget-fullwidth ciuis-body-loading" style="min-height: 85px;">
                            <div class='filtrosLeads' style="min-height: 0;padding: 1em;">

                                <md-input-container class="md-block">
                                    <label>Período</label>
                                    <input required type=date ng-model="filtros.dt_de" ng-change="getResultados()">
                                </md-input-container>

                                <md-input-container class="md-block">
                                    <label>Á</label>
                                    <input required type=date ng-model="filtros.dt_ate" ng-change="getResultados()">
                                </md-input-container>

                                <md-input-container class="md-block">
                                    <label>Funcionário</label>
                                    <md-select placeholder="Funcionario" ng-model="filtros.funcionario" style="min-width: 200px;" ng-change="getResultados()">
                                        <md-option ng-selected="$index == 0" ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
                                    </md-select>
                                </md-input-container>
                            </div>
                        </md-content>

                        <md-content class="widget-fullwidth ciuis-body-loading" id="contentMain" style="overflow: hidden;">



                            <div class="row">
                                <div class="col-sm-6 col-lg mb-4 pt-3">
                                    <div class="card">
                                        <div class="card-header p-3 pt-2">
                                            <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div class="text-end pt-1">
                                                <p class="text-sm mb-0 text-capitalize">Horas trabalhadas</p>
                                                <h4 class="mb-0">{{report.horasTrabalhadas}}</h4>
                                            </div>
                                        </div>
                                        <hr class="dark horizontal my-0">
                                        <div class="card-footer p-3">
                                            <p class="mb-0"><span class="text-success text-sm font-weight-bolder"> </span></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg mb-4 pt-3">
                                    <div class="card">
                                        <div class="card-header p-3 pt-2">
                                            <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                                                <i class="fas fa-handshake"></i>
                                            </div>
                                            <div class="text-end pt-1">
                                                <p class="text-sm mb-0 text-capitalize">Horas ociosas</p>
                                                <h4 class="mb-0">{{report.HorasOciosas}}</h4>
                                            </div>
                                        </div>
                                        <hr class="dark horizontal my-0">
                                        <div class="card-footer p-3">
                                            <p class="mb-0"><span class="text-success text-sm font-weight-bolder"></span></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-lg mb-4 pt-3">
                                    <div class="card">
                                        <div class="card-header p-3 pt-2">
                                            <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                                                <i class="fas fa-laptop"></i>
                                            </div>
                                            <div class="text-end pt-1">
                                                <p class="text-sm mb-0 text-capitalize">Total de ações</p>
                                                <h4 class="mb-0">{{report.totalAcoes}}</h4>
                                            </div>
                                        </div>
                                        <hr class="dark horizontal my-0">
                                        <div class="card-footer p-3">
                                            <p class="mb-0"><span class="text-success text-sm font-weight-bolder"></span></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-lg  pt-3">
                                    <div class="card">
                                        <div class="card-header p-3 pt-2">
                                            <div class="icon icon-lg icon-shape bg-gradient-secondary shadow-secondary text-center border-radius-xl mt-n4 position-absolute">
                                                <i class="fas fa-user-tie"></i>
                                            </div>
                                            <div class="text-end pt-1">
                                                <p class="text-sm mb-0 text-capitalize">Leads acionados</p>
                                                <h4 class="mb-0">{{ report.LeadsAcionados }}</h4>
                                            </div>
                                        </div>
                                        <hr class="dark horizontal my-0">
                                        <div class="card-footer p-3">
                                            <p class="mb-0"><span class="text-success text-sm font-weight-bolder"></span></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg mb-4  pt-3">
                                    <div class="card">
                                        <div class="card-header p-3 pt-2">
                                            <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                                <i class="far fa-share-square"></i>
                                            </div>
                                            <div class="text-end pt-1">
                                                <p class="text-sm mb-0 text-capitalize">Reuniões realizadas</p>
                                                <h4 class="mb-0">{{ report.ReunioesRealizadas }}</h4>
                                            </div>
                                        </div>
                                        <hr class="dark horizontal my-0">
                                        <div class="card-footer p-3">
                                            <p class="mb-0"><span class="text-danger text-sm font-weight-bolder"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="main-content container-fluid col-xs-12 col-md-6 col-lg-6 md-pl-0 lead-left-bar">
                                <div class="panel-default panel-table borderten lead-manager-head" style="overflow: auto;">
                                    <md-toolbar class="toolbar-white">
                                        <div class="md-toolbar-tools">
                                            <h5 flex md-truncate class="text-bold">Foram gerados os seguintes leads ({{leadsGerados.length}})</h5>
                                        </div>
                                    </md-toolbar>
                                    <div class="tasks-status-stat" ng-cloak>
                                        <div class="widget-chart-container">
                                            <div class="widget-counter-group widget-counter-group-right">
                                                <md-table-container ng-show="leadsGerados.length > 0">
                                                    <table md-table md-progress="promise">
                                                        <thead md-head>
                                                            <tr md-row>
                                                                <th md-column md-order-by="id"><span>ID</span></th>
                                                                <th md-column md-order-by="name"><span>Nome</span></th>
                                                                <th md-column md-order-by="name"><span>Data</span></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody md-body>
                                                            <tr class="select_row" md-row ng-repeat="lead in leadsGerados" class="cursor">
                                                                <td md-cell>
                                                                    <strong><a href="/leads/lead/{{lead.id}}" target="_blank">{{lead.id}}</a></strong>
                                                                </td>
                                                                <td md-cell>
                                                                    <span ng-bind="lead.company != '' ? lead.company : lead.name "></span>
                                                                </td>

                                                                <td md-cell>
                                                                    <span ng-bind="lead.created | date:'dd/MM/yyyy'"></span>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </md-table-container>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="main-content container-fluid col-xs-12 col-md-6 col-lg-6 md-pl-0 lead-left-bar">
                                <div class="panel-default panel-table borderten lead-manager-head" style="overflow: auto;">
                                    <md-toolbar class="toolbar-white">
                                        <div class="md-toolbar-tools">
                                            <h5 flex md-truncate class="text-bold">Não foram gerados leads aos clientes ({{ClientesSemLead.length}})</h5>
                                        </div>
                                    </md-toolbar>
                                    <div class="tasks-status-stat" ng-cloak>
                                        <div class="widget-chart-container">
                                            <div class="widget-counter-group widget-counter-group-right">
                                                <md-table-container ng-show="ClientesSemLead.length > 0">
                                                    <table md-table md-progress="promise">
                                                        <thead md-head>
                                                            <tr md-row>
                                                                <th md-column md-order-by="id"><span>ID</span></th>
                                                                <th md-column md-order-by="name"><span>Nome</span></th>
                                                                <th md-column md-order-by="name"><span>Data</span></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody md-body>
                                                            <tr class="select_row" md-row ng-repeat="cliente in ClientesSemLead" class="cursor">
                                                                <td md-cell>
                                                                    <strong><a href="/customers/customer/{{cliente.id}}" target="_blank">{{cliente.id}}</a></strong>
                                                                </td>
                                                                <td md-cell>
                                                                    <span ng-bind="cliente.company"></span>
                                                                </td>
                                                                <td md-cell>
                                                                    <span ng-bind="cliente.created | date:'dd/MM/yyyy'"></span>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </md-table-container>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                            if ($user_data['super_admin'] == "1") {
                            ?>
                                <div class="main-content container-fluid col-xs-12 col-md-6 col-lg-6 md-pl-0 lead-left-bar">
                                    <div class="panel-default panel-table borderten lead-manager-head" style="overflow: auto;">
                                        <md-toolbar class="toolbar-white">
                                            <div class="md-toolbar-tools">
                                                <h5 flex md-truncate class="text-bold">Leads por empresas ({{leadsGerados.length}})</h5>
                                            </div>
                                        </md-toolbar>
                                        <div class="tasks-status-stat" ng-cloak>
                                            <div class="widget-chart-container">
                                                <div class="widget-counter-group widget-counter-group-right">
                                                    <md-table-container ng-show="empresas.length > 0">
                                                        <table md-table md-progress="promise">
                                                            <thead md-head>
                                                                <tr md-row>
                                                                    <th md-column md-order-by="id"><span>ID</span></th>
                                                                    <th md-column md-order-by="name"><span>Empresa</span></th>
                                                                    <th md-column md-order-by="name"><span>Leads</span></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody md-body>
                                                                <tr class="select_row" md-row ng-repeat="empresa in empresas" class="cursor">
                                                                    <td md-cell>
                                                                        <strong><a href="/companies/company/{{empresa.id_company}}" target="_blank">{{empresa.id_company}}</a></strong>
                                                                    </td>
                                                                    <td md-cell>
                                                                        <span ng-bind="empresa.nm_company"></span>
                                                                    </td>

                                                                    <td md-cell>
                                                                        <span ng-bind="empresa.total"></span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </md-table-container>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php
                            }
                            ?>





                        </md-content>
                    </md-content>
                </md-tab>
            </md-tabs>
        </md-content>
    </md-content>
</div>

<script type="text/javascript">
    var lang = {};
    var super_admin = '<?= $user_data['super_admin'] ?>';
</script>

<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?php echo base_url('assets/lib/highcharts/highcharts.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/RelProdutividade.js?v=1.1.3') ?>"></script>
<script>
    $(document).ready(function() {

    });
</script>