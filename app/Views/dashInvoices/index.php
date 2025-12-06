<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>

<?php $appconfig = get_appconfig(); ?>

<div class="ciuis-body-content" ng-controller="DashInvoices_Controller">

    <md-content class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">

        <md-toolbar class="toolbar-white">

            <div class="md-toolbar-tools">

                <md-button class="md-icon-button" aria-label="Settings" ng-disabled="true">

                    <md-icon><i class="ico-ciuis-leads text-warning"></i></md-icon>

                </md-button>

                <h2 flex md-truncate><?php echo lang2('x_menu_dash_invoices') ?></h2>

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
                                    <md-select placeholder="Ano" ng-model="filtros.ano" ng-change="getResultados()" style="min-width: 200px;">
                                        <md-option ng-value="ano" ng-repeat="ano in anosList">{{ano}}</md-option>
                                    </md-select>
                                </md-input-container>

                                <md-input-container class="md-block">
                                    <i class="iconSearch ion-funnel text-muted "></i>
                                    <md-select placeholder="Mês" ng-model="filtros.mes" ng-change="getResultados()" style="min-width: 200px;">
                                        <md-option ng-value="-1">Todos</md-option>
                                        <md-option ng-value="mes.numero" ng-repeat="mes in mesList">{{mes.name}}</md-option>
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

                                <md-input-container class="md-block">
                                    <label>Situação</label>
                                    <md-select placeholder="Situação" ng-model="filtros.situacao" style="min-width: 200px;" ng-change="getResultados()">
                                        <md-option ng-value="-1">Todos</md-option>
                                        <md-option ng-value="1">Pagos</md-option>
                                        <md-option ng-value="2">Pendentes</md-option>
                                        <md-option ng-value="3">Vencidos</md-option>
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
                                        <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-fatBruto'] != null && baloesGraphs['card-fatBruto'].exibir == '1')" ng-click="exibeBalao('card-fatBruto')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                        <md-card-title>
                                            <md-card-title-text>
                                                <span class="md-headline"><strong ng-bind-html="report.fatBruto | currencyFormat:cur_code:null:true:cur_lct"></strong></span>
                                                <span class="md-subhead">Faturamento bruto</span>
                                            </md-card-title-text>
                                        </md-card-title>

                                    </md-card>

                                    <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card3" style="position: relative;">
                                        <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-totalDepsECompras'] != null && baloesGraphs['card-totalDepsECompras'].exibir == '1')" ng-click="exibeBalao('card-totalDepsECompras')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                        <md-card-title>
                                            <md-card-title-text>
                                                <span class="md-headline"><strong ng-bind-html="report.totalDepsECompras | currencyFormat:cur_code:null:true:cur_lct"></strong> </span>
                                                <span class="md-subhead">Total de Despesas & Compras</span>
                                            </md-card-title-text>
                                        </md-card-title>

                                    </md-card>

                                    <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card4" style="position: relative;">
                                        <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-RestLiquid'] != null && baloesGraphs['card-RestLiquid'].exibir == '1')" ng-click="exibeBalao('card-RestLiquid')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                        <md-card-title>
                                            <md-card-title-text>
                                                <span class="md-headline"><strong ng-bind-html="report.RestLiquid | currencyFormat:cur_code:null:true:cur_lct"></strong></span>
                                                <span class="md-subhead">Resultado líquido</span>
                                            </md-card-title-text>
                                        </md-card-title>

                                    </md-card>

                                    <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card5" style="position: relative;">
                                        <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-MargemRestLiq'] != null && baloesGraphs['card-MargemRestLiq'].exibir == '1')" ng-click="exibeBalao('card-MargemRestLiq')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                        <md-card-title>
                                            <md-card-title-text>
                                                <span class="md-headline"><strong ng-bind="report.MargemRestLiq"></strong></span>
                                                <span class="md-subhead">Margem do lucro líquido</span>
                                            </md-card-title-text>
                                        </md-card-title>
                                    </md-card>
                                </div>
                            </md-card>

                            <div layout-xs="column" layout="row" class="">
                                <div flex-xs flex-gt-xs="100" layout="column">
                                    <md-card>
                                        <div class="widget-chart-container">
                                            <div class="widget-counter-group widget-counter-group-right">
                                                <div style="width: 100%;" class="pull-left">
                                                    <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                    <div class="titleDash">
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Faturamento bruto</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['graphFaturamento'] != null && baloesGraphs['graphFaturamento'].exibir == '1')" ng-click="exibeBalao('graphFaturamento')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="my-2" style="padding-bottom: 60px;">
                                                <div class="chart-wrapper" style="height:300px">
                                                    <canvas height="300" style="padding-top: 25px;" id="graphFaturamento"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </md-card>
                                </div>

                            </div>


                            <div layout-xs="column" layout="row" class="">
                                <div flex-xs flex-gt-xs="100" layout="column">
                                    <md-card>
                                        <div class="widget-chart-container">
                                            <div class="widget-counter-group widget-counter-group-right">
                                                <div style="width: 100%;" class="pull-left">
                                                    <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                    <div class="titleDash">
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Faturamento X Despesas & Compras</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['FaturamentoehDespesa'] != null && baloesGraphs['FaturamentoehDespesa'].exibir == '1')" ng-click="exibeBalao('FaturamentoehDespesa')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-2">
                                                <div class="chart-wrapper" style="height:500px">
                                                    <div id="FaturamentoehDespesa" class="bar-chart " style="height:500px;margin-top: 60px;"></div>

                                                </div>
                                            </div>
                                        </div>
                                    </md-card>
                                </div>
                            </div>

                            <div layout-xs="column" layout="row" class="">
                                <div flex-xs flex-gt-xs="100" layout="column">
                                    <md-card>
                                        <div class="widget-chart-container">
                                            <div class="widget-counter-group widget-counter-group-right" style = "min-height: 60px;">
                                                <div style="width: 100%;" class="pull-left">
                                                    <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                    <div class="titleDash">
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Faturamento por origem</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['faturamentoPorOrigem'] != null && baloesGraphs['faturamentoPorOrigem'].exibir == '1')" ng-click="exibeBalao('faturamentoPorOrigem')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-2" style="padding-bottom: 25px;">
                                                <div class="chart-wrapper">
                                                    <div id="faturamentoPorOrigem">
                                                        <div class="graph-info">
                                                            <a href="javascript:void(0)" class="visitors">Lucro</a>

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
                                <div flex-xs flex-gt-xs="100" layout="column">
                                    <md-card>
                                        <div class="widget-chart-container">
                                            <div class="widget-counter-group widget-counter-group-right">
                                                <div style="width: 100%;" class="pull-left">
                                                    <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                    <div class="titleDash">
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Lucro líquido</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['graphLucroLiquido'] != null && baloesGraphs['graphLucroLiquido'].exibir == '1')" ng-click="exibeBalao('graphLucroLiquido')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-2" style="padding-bottom: 25px;">
                                                <div class="chart-wrapper">
                                                    <div id="graphLucroLiquido">
                                                        <div class="graph-info">
                                                            <a href="javascript:void(0)" class="visitors">Lucro</a>

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
                                                <div style="width: 100%" class="pull-left">
                                                    <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                    <div class="titleDash">
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Despesas e compras</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['despesasECompras'] != null && baloesGraphs['despesasECompras'].exibir == '1')" ng-click="exibeBalao('despesasECompras')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-2">
                                                <div class="chart-wrapper" style="height:auto">
                                                    <div id="despesasECompras" class="bar-chart " style="height:500px;margin-top: 60px;"></div>
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
                                                        <h4 style="padding: 0px;margin: 0px;position: relative;"><b>Análise de Despesas</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['AnaliseDeDespesas'] != null && baloesGraphs['AnaliseDeDespesas'].exibir == '1')" style="right: 0; top: 5px; z-index: 999;position: absolute;" ng-click="exibeBalao('AnaliseDeDespesas')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-2" style="margin-bottom: 3em;margin-top: 1em; position: relative;height: 500px;    margin-top: 5em;">
                                                <div id="AnaliseDeDespesas" class="chart"></div>
                                            </div>
                                        </div>
                                    </md-card>
                                </div>

                            </div>
                            <div layout-xs="column" layout="row" class="">
                                <div flex-xs flex-gt-xs="100" layout="column">
                                    <md-card>
                                        <div class="widget-chart-container">
                                            <div class="widget-counter-group widget-counter-group-right">
                                                <div style="width: 100%" class="pull-left">
                                                    <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                    <div class="titleDash">
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Fluxo de Caixa</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['receitasXDespesas'] != null && baloesGraphs['receitasXDespesas'].exibir == '1')" ng-click="exibeBalao('receitasXDespesas')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="my-2">
                                                <div class="chart-wrapper" style="height:auto">
                                                    <div id="receitasXDespesas" class="bar-chart " style="height:500px;margin-top: 60px;"></div>
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
    CONSTdt_ate = null;
    CONST_idCompany = null;
    CONST_dt_ano = null;
    CONST_dt_mes = null
</script>

<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>
<script src="<?php echo base_url('assets/lib/chartjs/dist/Chart.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/lib/highcharts/highcharts.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/dashInvoices.js?v=1.6.2') ?>"></script>