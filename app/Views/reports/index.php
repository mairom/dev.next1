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
<div class="ciuis-body-content" ng-controller="Dash_reports">
    <main class="main-content border-radius-lg ">
        <div class="container-fluid py-4">
            <div class='filtrosLeads' style="padding-bottom: 20px !important;">
                <md-input-container class="md-block">
                    <label>Período</label>
                    <input required type=date ng-model="filtros.dt_de" ng-change="getResultados()">
                </md-input-container>

                <md-input-container class="md-block">
                    <label>Á</label>
                    <input required type=date ng-model="filtros.dt_ate" ng-change="getResultados()">
                </md-input-container>

                <md-input-container class="md-block">
                    <label>Closer</label>
                    <md-select placeholder="Closer" ng-model="filtros.closer" style="min-width: 200px;" ng-change="getResultados()">
                        <md-option ng-value="-1">Todos</md-option>
                        <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
                    </md-select>
                </md-input-container>

                <md-input-container class="md-block">
                    <label>SDR</label>
                    <md-select placeholder="SDR" ng-model="filtros.sdr" style="min-width: 200px;" ng-change="getResultados()">
                        <md-option ng-value="-1">Todos</md-option>
                        <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
                    </md-select>
                </md-input-container>


                <md-input-container class="md-block">
                    <label>Tipo</label>
                    <md-select placeholder="Tipo" ng-model="filtros.tipo" style="min-width: 200px;" ng-change="getResultados()">
                        <md-option value="reunioes">Reuniões</md-option>
                        <md-option value="conversoes">Conversões</md-option>
                    </md-select>
                </md-input-container>

                <md-input-container class="md-block" ng-show="filtros.tipo == 'reunioes'">
                    <label>Tipo de Reuniões</label>
                    <md-select placeholder="Tipo de Reuniões" ng-model="filtros.tipo_reuniao" style="min-width: 200px;" ng-change="getResultados()">
                        <md-option value="-1">Todos</md-option>
                        <md-option value="Realizada">Realizadas</md-option>
                        <md-option value="Não realizada">Não realizadas</md-option>
                    </md-select>
                </md-input-container>
            </div>

            <div class="row mt-4">
                <h3 class="title-dash">Resultado de {{filtros.tipo}} ({{lista.length}})</h3>
            </div>




            <md-content class="bg-white" ng-cloak>
                <md-table-container>
                    <table md-table md-progress="promise">
                        <thead md-head md-order="customer_list.order">
                            <tr md-row>

                                <th md-column md-order-by="name">{{row.id_lead != null ? 'Nome do Lead' : 'Nome do Cliente'}}</th>
                                <th md-column md-order-by="Data">Data</th>
                                <th md-column md-order-by="Horario">Horario</th>
                                <th md-column md-order-by="Closer">Closer</th>
                                <th md-column md-order-by="Sdr">Sdr</th>
                                <th md-column md-order-by="Lead">Lead</th>
                            </tr>
                        </thead>

                        <tbody md-body>
                            <tr class="select_row" md-row ng-repeat="row in lista" class="cursor">

                                <td md-cell>

                                    <span ng-if="row.tp_pessoa == '2' && row.id_lead != null" ng-bind="row.company"></span>
                                    <span ng-if="row.tp_pessoa == '1' && row.id_lead != null" ng-bind="row.nm_lead"></span>

                                    <span ng-if="row.id_customer != null" ng-bind="row.company"></span>
                                </td>

                                <td md-cell>
                                    <span ng-if="row.data != null" ng-bind="row.data"></span>
                                </td>

                                <td md-cell>
                                    <span ng-if="row.horario != null" ng-bind="row.horario"></span>
                                    <span ng-if="row.hora != null" ng-bind="row.hora"></span>
                                </td>

                                <td md-cell>
                                    <span ng-bind="row.nm_closer"></span>
                                </td>

                                <td md-cell>
                                    <span ng-bind="row.nm_sdr"></span>
                                </td>

                                <td md-cell>
                                    <a href="{{BASE_URL}}customers/customer/{{row.id_customer}}" ng-if="row.id_customer != null" target="_blank" class="btn btn-outline-light">Abrir cliente <i class="fas fa-external-link-alt"></i></a>
                                    <a href="{{BASE_URL}}leads/lead/{{row.id_lead}}" ng-if="row.id_lead != null" target="_blank" class="btn btn-outline-light">Abrir lead <i class="fas fa-external-link-alt"></i></a>
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </md-table-container>


        </div>
    </main>

</div>

<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>
<script src="<?= base_url('assets/lib/material-dashboard/chartjs.js'); ?>" type="text/javascript"></script>
<script src="<?= base_url('assets/lib/highcharts/highcharts.js') ?>"></script>


<script type="text/javascript" src="<?= base_url('assets/js/reports_dash.js?v=1.2') ?>"></script>

<script>
    $(document).ready(function() {


    });
</script>