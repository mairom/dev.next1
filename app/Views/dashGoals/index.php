<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<link href="<?php echo base_url('assets/lib/select2/select2.min.css'); ?>" rel="stylesheet" />
<?php $appconfig = get_appconfig(); ?>
<div class="ciuis-body-content" ng-controller="DashGoals_Controller">
    <style type="text/css">
        rect.highcharts-background {
            fill: #f3f3f3;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-selection {
            min-height: 40px !important;
            padding: 8px 0 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b:after {
            display: none;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            margin-top: 7px;
            margin-left: -7px;
        }

        .select2-container--default .select2-selection--single {
            background-color: transparent !important;
        }

        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            border: 0 !important;
            border-bottom: 1px solid #d1cece !important;
        }

        .labelSelec2 {
            padding-top: 0;
            margin-bottom: -10px !important;
        }

        md-input-container.md-input-focused label:not(.md-no-float),
        md-input-container.md-input-has-placeholder label:not(.md-no-float),
        md-input-container.md-input-has-value label:not(.md-no-float) {
            margin-bottom: 0;
        }


        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #5897fb !important;
            color: white;
        }
    </style>
    <md-content class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">
        <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">
                <md-button class="md-icon-button" aria-label="Settings" ng-disabled="true">
                    <img class="img-icon-toolbar" src="{{appurl + 'assets/img/menu/dash_metas.svg'}}">
                </md-button>
                <h2 flex md-truncate><?php echo lang2('x_menu_dash_goals') ?></h2>
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
                                    <md-select placeholder="Mes" id="selectMes" ng-model="filtros.mes" ng-change="getResultados()" style="min-width: 200px;">
                                        <md-option ng-value="$index + 1" ng-repeat="mes in mesList">{{mes}}</md-option>
                                    </md-select>
                                </md-input-container>

                                <md-input-container class="md-block">
                                    <md-select placeholder="Ano" id="selectAno" ng-model="filtros.ano" ng-change="getResultados()" style="min-width: 200px;">
                                        <md-option ng-value="ano" ng-repeat="ano in anosList">{{ano}}</md-option>
                                    </md-select>
                                </md-input-container>

                                <md-input-container class="md-block">
                                    <md-select placeholder="Meta" ng-model="filtros.id_goal" ng-change="getResultados()" style="min-width: 200px;">
                                        <md-option ng-value="goal.id_goal" ng-repeat="goal in goals">{{goal.nm_goal}}</md-option>
                                    </md-select>
                                </md-input-container>

                                <md-input-container class="md-block">
                                    <label>Funcionário</label>
                                    <md-select placeholder="<?php echo lang2('choosestaff'); ?>" ng-model="filtros.funcionario" style="min-width: 200px;" ng-change="getResultados()">
                                        <md-option ng-value="-1">Todos</md-option>
                                        <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
                                    </md-select>
                                </md-input-container>


                                <md-input-container class="md-block">
                                    <label>Equipe</label>
                                    <md-select placeholder="Equipe" ng-model="filtros.id_equipe" ng-change="getResultados()" style="min-width: 200px;">
                                        <md-option ng-value="-1">Todos</md-option>
                                        <md-option ng-value="equipe.id_equipe" ng-repeat="equipe in equipes">{{equipe.nm_equipe}}</md-option>
                                    </md-select>
                                </md-input-container>

                                <md-input-container class="md-block">
                                    <label>Produtos</label>
                                    <md-select placeholder="Produtos" ng-model="filtros.produto" ng-change="getResultados()" style="min-width: 200px;">
                                        <md-option ng-value="-1">Todos</md-option>
                                        <md-option ng-value="product.product_id" ng-repeat="product in products">{{product.name}}</md-option>
                                    </md-select>
                                </md-input-container>


                                <md-button ng-click="baixarPdf()" style="width: 100px;height: 65px;float: right;color: #858585;cursor: pointer;" class="md-icon-button" aria-label="Settings">
                                    <md-icon><i style="color: #8e44ad;" class="far fa-file-pdf"></i></md-icon><span>Gerar PDF</span>
                                </md-button>


                            </div>
                        </md-content>
                        <md-content class="widget-fullwidth ciuis-body-loading" id="contentMain" style="overflow: hidden;">
                            <md-card flex-xs flex-gt-xs="100" layout="column">
                                <div layout-xs="column" layout="row" class="bg-white">
                                    <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card5" style="position: relative;">
                                        <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-totalMeta'] != null && baloesGraphs['card-totalMeta'].exibir == '1')" ng-click="exibeBalao('card-totalMeta')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                        <a href="customers">
                                            <md-card-title>
                                                <md-card-title-text>
                                                    <span class="md-headline"><strong ng-bind="report.totalMeta"></strong></span>
                                                    <span class="md-subhead">Total da meta</span>
                                                </md-card-title-text>
                                            </md-card-title>
                                        </a>
                                    </md-card>

                                    <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card1" style="position: relative;">
                                        <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-totalAtingido'] != null && baloesGraphs['card-totalAtingido'].exibir == '1')" ng-click="exibeBalao('card-totalAtingido')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">

                                        <a href="customers">
                                            <md-card-title>
                                                <md-card-title-text>
                                                    <span class="md-headline"><strong ng-bind="report.totalAtingido"></strong></span>
                                                    <span class="md-subhead">Total atingido</span>
                                                </md-card-title-text>
                                            </md-card-title>
                                        </a>
                                    </md-card>

                                    <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card2" style="position: relative;">
                                        <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-porcetAtingido'] != null && baloesGraphs['card-porcetAtingido'].exibir == '1')" ng-click="exibeBalao('card-porcetAtingido')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">

                                        <a href="invoices">
                                            <md-card-title>
                                                <md-card-title-text>
                                                    <span class="md-headline"><strong ng-bind="report.porcetAtingido + ' %'"></strong></span>
                                                    <span class="md-subhead">% atingido</span>
                                                </md-card-title-text>
                                            </md-card-title>
                                        </a>
                                    </md-card>

                                    <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card3" style="position: relative;">
                                        <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-totalFalta'] != null && baloesGraphs['card-totalFalta'].exibir == '1')" ng-click="exibeBalao('card-totalFalta')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                        <a href="invoices">
                                            <md-card-title>
                                                <md-card-title-text>
                                                    <span class="md-headline"><strong ng-bind="report.totalFalta"></strong></span>
                                                    <span class="md-subhead">Total que falta</span>
                                                </md-card-title-text>
                                            </md-card-title>
                                        </a>
                                    </md-card>

                                    <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card4" style="position: relative;">
                                        <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-porcentFalta'] != null && baloesGraphs['card-porcentFalta'].exibir == '1')" ng-click="exibeBalao('card-porcentFalta')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                        <a href="invoices">
                                            <md-card-title>
                                                <md-card-title-text>
                                                    <span class="md-headline"><strong ng-bind="report.porcentFalta + ' %'"></strong></span>
                                                    <span class="md-subhead">% do que falta</span>
                                                </md-card-title-text>
                                            </md-card-title>
                                        </a>
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
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Metas vs resultados</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['graphMetaVsResultado'] != null && baloesGraphs['graphMetaVsResultado'].exibir == '1')" ng-click="exibeBalao('graphMetaVsResultado')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-2">
                                                <div class="chart-wrapper" style="height:500px">
                                                    <div id="graphMetaVsResultado" class="bar-chart " style="height:500px;margin-top: 60px;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </md-card>
                                </div>
                            </div>


                            <div layout-xs="column" layout="row" class="">
                                <!--
                                <div flex-xs flex-gt-xs="50" layout="column">
                                    <md-card>
                                        <div class="widget-chart-container">
                                            <div class="widget-counter-group widget-counter-group-right">
                                                <div style="width: 100%;" class="pull-left">
                                                    <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                    <div class="titleDash">
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Comparativo ano corrente X Ano passado</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['graphcomparatvAnoCorrentVsAnoPassado'] != null && baloesGraphs['graphcomparatvAnoCorrentVsAnoPassado'].exibir == '1')" ng-click="exibeBalao('graphcomparatvAnoCorrentVsAnoPassado')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-2">
                                                <div class="chart-wrapper" style="height:500px">
                                                    <div id="graphcomparatvAnoCorrentVsAnoPassado" class="bar-chart " style="height:500px;margin-top: 60px;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </md-card>
                                </div>
    -->
                                <div flex-xs flex-gt-xs="50" layout="column">
                                    <md-card>
                                        <div class="widget-chart-container">
                                            <div class="widget-counter-group widget-counter-group-right">
                                                <div style="width: 100%;" class="pull-left">
                                                    <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                    <div class="titleDash">
                                                        <h4 style="padding: 0px;margin: 0px;"><b>% Alcançados</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['graphPorcetAlcancados'] != null && baloesGraphs['graphPorcetAlcancados'].exibir == '1')" ng-click="exibeBalao('graphPorcetAlcancados')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-2">
                                                <div class="chart-wrapper" style="height:500px">
                                                    <div id="graphPorcetAlcancados" class="bar-chart " style="height:500px;margin-top: 60px;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </md-card>
                                </div>
                                <div flex-xs flex-gt-xs="50" layout="column">
                                    <md-card style="min-height: 560px;">
                                        <div class="widget-chart-container">
                                            <div class="widget-counter-group widget-counter-group-right">
                                                <div style="width: 100%;" class="pull-left">
                                                    <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                    <div class="titleDash">
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Controle de bonificações</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['graphControleBonifica'] != null && baloesGraphs['graphControleBonifica'].exibir == '1')" ng-click="exibeBalao('graphControleBonifica')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-2" style="padding-bottom: 25px;">
                                                <div class="chart-wrapper">
                                                    <div id="graphControleBonifica">
                                                        <div class="graph-info">
                                                            <a href="javascript:void(0)" class="visitors">Controle de bonificações</a>

                                                            <a href="#" id="bars"></a>
                                                            <a href="#" id="lines" class="active"></a>
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
<script type="text/javascript" src="<?php echo base_url('assets/js/dashGoals.js?v=1.3.8') ?>"></script>
<script>
    $(document).ready(function() {
        $('.select2Func').select2({
            ajax: {
                url: '<?= base_url('api/get_staff'); ?>',
                dataType: 'json'
            }
        });
    });
</script>