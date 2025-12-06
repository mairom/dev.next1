<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<?php $appconfig = get_appconfig(); ?>
<div class="ciuis-body-content" ng-controller="DashCustomers_Controller">
    <md-content class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">
        <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">
                <md-button class="md-icon-button" aria-label="Settings" ng-disabled="true">
                    <img class="img-icon-toolbar" src="{{appurl + 'assets/img/pie2.png'}}">
                </md-button>
                <h2 flex md-truncate><?php echo lang2('x_menu_dash_customers') ?></h2>
            </div>
        </md-toolbar>
        <md-content class="">
            <md-tabs md-dynamic-height md-border-bottom md-selected="ctrl.selectedIndex">
                <md-tab label="<?php echo lang2('overview') ?>">
                    <div ng-show="overview.loader" layout-align="center center" class="text-center" id="circular_loader">
                        <!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
                        <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">

                        <p style="font-size: 15px;margin-bottom: 5%;">
                            <span>
                                <?php echo lang2('please_wait') ?> <br>
                                <small><strong><?php echo lang2('loading') . ' ' . lang2('overview') . '...' ?></strong></small>
                            </span>
                        </p>
                    </div>
                    <md-content class="md-padding" ng-show="!overview.loader">
                        <md-content class="widget-fullwidth ciuis-body-loading" style="min-height: 85px;">
                            <div class='filtrosLeads' style="min-height: 0;padding: 1em;">

                                <md-input-container class="md-block">
                                    <i class="iconSearch ion-funnel text-muted "></i>
                                    <md-select placeholder="Ano" id="selectAno" ng-model="filtros.ano" ng-change="getResultados()" style="min-width: 200px;">
                                        <md-option ng-value="-1">Todos</md-option>
                                        <md-option ng-value="ano" ng-repeat="ano in anosList">{{ano}}</md-option>
                                    </md-select>
                                </md-input-container>

                                <md-input-container class="md-block">
                                    <i class="iconSearch ion-funnel text-muted "></i>
                                    <md-select placeholder="Produto" ng-model="filtros.produto" ng-change="getResultados()" style="min-width: 200px;">
                                        <md-option ng-value="-1">Todos</md-option>
                                        <md-option ng-value="produto.product_id" ng-repeat="produto in produtosList">{{produto.name}}</md-option>
                                    </md-select>
                                </md-input-container>

                                <md-input-container class="md-block">
                                    <label>Funcionário</label>
                                    <md-select placeholder="Vendedor" ng-model="filtros.vendedor" style="min-width: 200px;" ng-change="getResultados()">
                                        <md-option ng-value="-1">Todos</md-option>
                                        <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
                                    </md-select>
                                </md-input-container>

                                <md-button ng-click="baixarPdf()" style="width: 100px;height: 65px;float: right;color: #858585;cursor: pointer;" class="md-icon-button" aria-label="Settings">
                                    <md-icon><i style="color: #8e44ad;" class="far fa-file-pdf"></i></md-icon><span>Gerar PDF</span>
                                </md-button>


                            </div>
                        </md-content>
                        <md-content class="widget-fullwidth ciuis-body-loading" id="contentMain">

                            <md-card flex-xs flex-gt-xs="100" layout="column">

                                <div layout-xs="column" layout="row" class="bg-white">
                                    <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card1" style="position: relative;">
                                        <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-totalClientesAtivos'] != null && baloesGraphs['card-totalClientesAtivos'].exibir == '1')" ng-click="exibeBalao('card-totalClientesAtivos')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                        <a href="customers">
                                            <md-card-title>
                                                <md-card-title-text>
                                                    <span class="md-headline"><strong ng-bind="report.totalClientesAtivos"></strong></span>
                                                    <span class="md-subhead">Clientes ativos</span>
                                                </md-card-title-text>
                                            </md-card-title>
                                        </a>
                                    </md-card>

                                    <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card3" style="position: relative;">
                                        <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-totalLTMes'] != null && baloesGraphs['card-totalLTMes'].exibir == '1')" ng-click="exibeBalao('card-totalLTMes')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">

                                        <a href="customers">
                                            <md-card-title>
                                                <md-card-title-text>
                                                    <span class="md-headline"><strong ng-bind="report.totalLTMes"></strong> <small>meses e </small><span ng-bind="report.totalLTDias"></span><small> dias</small> </span>
                                                    <span class="md-subhead">Lifetime Clientes</span>
                                                </md-card-title-text>
                                            </md-card-title>
                                        </a>
                                    </md-card>

                                    <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card4" style="position: relative;">
                                        <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-totalLTV'] != null && baloesGraphs['card-totalLTV'].exibir == '1')" ng-click="exibeBalao('card-totalLTV')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">

                                        <a href="invoices">
                                            <md-card-title>
                                                <md-card-title-text>
                                                    <span class="md-headline"><strong ng-bind-html="report.totalLTV | currencyFormat:cur_code:null:true:cur_lct"></strong></span>
                                                    <span class="md-subhead">Lifetime Value Clientes</span>
                                                </md-card-title-text>
                                            </md-card-title>
                                        </a>
                                    </md-card>

                                    <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card2" style="position: relative;">
                                        <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-totalChurn'] != null && baloesGraphs['card-totalChurn'].exibir == '1')" ng-click="exibeBalao('card-totalChurn')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">

                                        <a href="invoices">
                                            <md-card-title>
                                                <md-card-title-text>
                                                    <span class="md-headline"><strong ng-bind="report.totalChurn"></strong></span>
                                                    <span class="md-subhead">Churn rate</span>
                                                </md-card-title-text>
                                            </md-card-title>
                                        </a>
                                    </md-card>
                                </div>


                            </md-card>

                            <div layout-xs="column" layout="row" class="">
                                <div flex-xs flex-gt-xs="50" layout="column">
                                    <md-card>
                                        <div class="widget-chart-container">
                                            <div class="widget-counter-group widget-counter-group-right">
                                                <div style="width: 100%;" class="pull-left">
                                                    <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                    <div class="titleDash">
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Top 10 clientes por faturamento</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['graphFaturamentoTop10'] != null && baloesGraphs['graphFaturamentoTop10'].exibir == '1')" ng-click="exibeBalao('graphFaturamentoTop10')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-2" style="padding-bottom: 60px;">
                                                <div class="chart-wrapper" style="height:362px">
                                                    <section class="graphFaturamentoTop10" id="graphFaturamentoTop10" style="height:100%">
                                                        <svg class="graphBarra" style="padding: 15px; width: 100%;"></svg>
                                                    </section>
                                                </div>
                                            </div>
                                        </div>
                                    </md-card>
                                </div>

                                <div flex-xs flex-gt-xs="50" layout="column">
                                    <md-card>
                                        <div class="widget-chart-container">
                                            <div class="widget-counter-group widget-counter-group-right">
                                                <div style="width: 100%;" class="pull-left">
                                                    <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                    <div class="titleDash">
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Clientes ativos</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['graphClientesAtivos'] != null && baloesGraphs['graphClientesAtivos'].exibir == '1')" ng-click="exibeBalao('graphClientesAtivos')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-2" style="padding-bottom: 25px;">
                                                <div class="chart-wrapper">
                                                    <div id="graphClientesAtivos">
                                                        <div class="graph-info">
                                                            <a href="javascript:void(0)" class="visitors">Clientes</a>

                                                            <a href="#" id="bars"><span></span></a>
                                                            <a href="#" id="lines" class="active"><span></span></a>
                                                        </div>
                                                        <div class="graph-container2">
                                                            <div id="graph-lines"></div>
                                                            <div id="graph-bars"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </md-card>
                                </div>
                            </div>

                            <div layout-xs="column" layout="row" class="">
                                <div flex-xs flex-gt-xs="50" layout="column">
                                    <md-card>
                                        <div class="widget-chart-container">
                                            <div class="widget-counter-group widget-counter-group-right">
                                                <div style="width: 100%;" class="pull-left">
                                                    <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                    <div class="titleDash">
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Quantidade de reuniões</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['QuantidadeDeReunioes'] != null && baloesGraphs['QuantidadeDeReunioes'].exibir == '1')" ng-click="exibeBalao('QuantidadeDeReunioes')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-2" style="padding-bottom: 60px;">
                                                <div class="chart-wrapper" style="height:300px">
                                                    <canvas height="300" style="padding-top: 25px;" id="QuantidadeDeReunioes"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </md-card>
                                </div>
                            
                                <div flex-xs flex-gt-xs="50" layout="column">
                                    <md-card>
                                        <div class="widget-chart-container">
                                            <div class="widget-counter-group widget-counter-group-right">
                                                <div style="width: 100%" class="pull-left">
                                                    <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                    <div class="titleDash">
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Novos clientes</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['graphNovosClientes'] != null && baloesGraphs['graphNovosClientes'].exibir == '1')" ng-click="exibeBalao('graphNovosClientes')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="my-2" style="padding-bottom: 60px;">
                                                <div class="chart-wrapper" style="height:300px">
                                                    <section class="graphNovosClientes" id="graphNovosClientes" style="height:100%">
                                                        <svg class="graphBarra" style="padding: 15px; width: 100%;"></svg>
                                                    </section>
                                                </div>
                                            </div>
                                        </div>
                                    </md-card>
                                </div>

                            </div>

                            <div layout-xs="column" layout="row" class="">
                                <div flex-xs flex-gt-xs="50" layout="column">
                                    <md-card>
                                        <div class="widget-chart-container">
                                            <div class="widget-counter-group widget-counter-group-right">
                                                <div style="width: 100%;" class="pull-left">
                                                    <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                    <div class="titleDash">
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Estado</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['graphEstado'] != null && baloesGraphs['graphEstado'].exibir == '1')" ng-click="exibeBalao('graphEstado')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="my-2" style="padding-bottom: 60px;">
                                                <div class="chart-wrapper" style="height:300px">
                                                    <canvas height="300" style="padding-top: 25px;" id="graphEstado"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </md-card>
                                </div>
                                <div flex-xs flex-gt-xs="50" layout="column">
                                    <md-card>
                                        <div class="widget-chart-container">
                                            <div class="widget-counter-group widget-counter-group-right">
                                                <div style="width: 100%;" class="pull-left">
                                                    <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                    <div class="titleDash">
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Ramo de Atividade</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['graphRamoDeAtividade'] != null && baloesGraphs['graphRamoDeAtividade'].exibir == '1')" ng-click="exibeBalao('graphRamoDeAtividade')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="my-2" style="padding-bottom: 60px;">
                                                <div class="chart-wrapper" style="height:300px">
                                                    <canvas height="300" style="padding-top: 25px;" id="graphRamoDeAtividade"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </md-card>
                                </div>
                            </div>
                        </md-content>
                    </md-content>
                </md-tab>
            </md-tabs>
        </md-content>
    </md-content>


    <div class="modal fade" id="modalBalao" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloGraphBalao">Oque estou vendo?</h5>
                    <button type="button" class="close closeModal" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="textoBalao">
                    </p>

                    <?php
                    if ($user_data['super_admin'] == "1") {
                    ?>
                        <div class="form-group">
                            <label for="tituloGraph">Titulo</label>
                            <input class="form-control" id="tituloGraph" rows="3"></input>
                        </div>
                        <div class="form-group">
                            <label for="descricaoGraph">Descrição</label>
                            <textarea class="form-control" id="descricaoGraph" rows="3"></textarea>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exibirGraph">Exbibir icone?</label>
                            <select id="exibirGraph" class="form-control">
                                <option value="1">Exibir</option>
                                <option value="0">Ocultar</option>
                            </select>
                        </div>
                    <?php
                    }
                    ?>
                </div>

                <?php
                if ($user_data['super_admin'] == "1") {
                ?>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        <button type="button" ng-click="salvarBalao()" class="btn btn-primary">Salvar</button>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var lang = {};
    lang.payments = '<?php echo lang2('payments') ?>';
    lang.expenses = '<?php echo lang2('expenses') ?>';
    var send_email = '<?= $send_email ?>';
    CONSTdt_ate = null;
    CONST_idCompany = null;
    CONST_dt_ano = null;
</script>

<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>
<script src="<?php echo base_url('assets/lib/chartjs/dist/Chart.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/lib/highcharts/highcharts.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/dashCustomers.js?v=1.5.7') ?>"></script>
<script>
    $(document).ready(function() {

    });
</script>