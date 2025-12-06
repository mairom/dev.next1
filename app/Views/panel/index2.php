<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<?php
$appconfig = get_appconfig();
$user_data = get_user();
?>


<style>
    md-card {
        box-shadow: none;
    }

    md-card md-card-title {
        padding: 10px;
    }

    .md-headline {
        font-size: 26px;
    }

    @media (min-width: 768px) and (max-width: 991px) {
        .hidden-sm {
            display: block !important;
        }
    }

    @media (max-width: 991px) {
        .hidden-xs {
            display: block !important;
        }

        .content-saud {
            width: 100% !important;
        }

        .titleSaud {
            width: 100% !important;
        }
    }

    @media (min-width: 991px) {
        .cards-home md-card {
            margin: 0 !important;
        }

        .cards-home md-card:not(:nth-child(1)) {
            margin-left: 10px !important;
        }
    }

    #main-content md-card {
        margin: 0;
        margin-top: 10px;
        position: relative;
    }



    .btn_balaoGraph {
        top: 10px;
    }

    .btn_add_user {
        margin-right: 0;
        background-color: #26ccd3 !important;
        border-radius: 5px;
        width: calc(100% - 10px);
        margin-left: 0;
    }
</style>
<div class="ciuis-body-content" ng-controller="Panel2_Controller">

    <div ng-show="overviewLoader" layout-align="center center" class="text-center" id="circular_loader">
        <!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
        <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">

        <p style="font-size: 15px;margin-bottom: 5%;">
            <span>
                <?php echo lang2('please_wait') ?> <br>
                <small><strong><?php echo lang2('loading') . ' ' . lang2('overview') . '...' ?></strong></small>
            </span>
        </p>
    </div>

    <div ng-show="!overviewLoader" class="icon-wrapper-alt sidebar-header-hom main-home-bottom ">
        <div class="img-home2">
            <div class="img-home" style="background-image: url('<?= $user_image ?>')"></div>
        </div>
        <h4 class="bemVindoNome"><?= $saudacao ?><b> <?= $user_data['name'] ?></b></h4>
    </div>

    <md-content ng-show="!overviewLoader" class="main-content container-fluid  col-lg-3 main-home main-home-top ">
        <div class="icon-wrapper-alt sidebar-header-hom">
            <div class="img-home2">
                <div class="img-home" style="background-image: url('<?= $user_image ?>')"></div>
            </div>
            <h4 class="bemVindoNome"><?= $saudacao ?><b> <?= $user_data['name'] ?></b></h4>
        </div>
        <ciuis-sidebar class="sidebar-home"></ciuis-sidebar>
    </md-content>

    <md-content ng-show="!overviewLoader" class="main-content container-fluid col-lg-9 content-main-home ">
        <md-toolbar class="toolbar-white" style="border-right:1px solid #e0e0e0;background-color: transparent !important;">
            <div class="md-toolbar-tools">
                <md-button class="md-icon-button" aria-label="Settings" ng-disabled="true">
                    <img class="img-icon-toolbar" src="{{appurl + 'assets/img/menu/Dashboard Leads.svg'}}">
                </md-button>
                <h4 class="text-muted" flex md-truncate><strong>Painel de resumo de operação</strong></h4>

                <md-button ng-click="openModalCustom()" class="md-raised md-primary btn-report" aria-label="Close" style="margin-right: 6%;background-color: #ffbc00;border-radius: 10px;">
                    Customize seu painel
                </md-button>

            </div>
        </md-toolbar>

        <md-content id="main-content">
            <md-content class="md-padding" style="background: transparent; padding: 0;margin-top: 10px;">
                <md-content class="widget-fullwidth ciuis-body-loading" style="overflow: hidden;background: #f1f4f663;" id="contentMain">
                    <md-card flex-xs flex-gt-xs="100" layout="column">
                        <div layout-xs="column" layout="row" class=" cards-home" style="padding: 10px;">

                            <md-card style="margin: 0;" flex-xs layout="column" class="text-center card6 md-card-home" style="position: relative;" ng-show="customPanel.box_panel1">
                                <img style="z-index: 9;" ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-totalLeadsGerados'] != null && baloesGraphs['card-totalLeadsGerados'].exibir == '1')" ng-click="exibeBalao('card-totalLeadsGerados');" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <a href="{{appurl + 'leads'}}">
                                    <md-card-title>
                                        <md-card-title-text>
                                            <img style="width: 50px; margin: 0 auto;" id="totalLeadsGerados" ng-show="report.totalLeadsGerados === ''" style="display: none;" src="{{appurl + 'assets/img/loading.gif'}}">
                                            <div class="col-md-6">
                                                <span class="md-subhead">Total de Leads gerados</span>
                                            </div>
                                            <div class="col-md-6" style="padding-top: 5px;">
                                                <span class="md-headline"><strong ng-bind="report.totalLeadsGerados"></strong></span>
                                            </div>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>

                            <md-card flex-xs layout="column" class="text-center card1" style="position: relative;" ng-show="customPanel.box_panel2">
                                <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-totalLeadsAtivos'] != null && baloesGraphs['card-totalLeadsAtivos'].exibir == '1')" ng-click="exibeBalao('card-totalLeadsAtivos');" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <a href="{{appurl + 'leads'}}">
                                    <md-card-title>
                                        <md-card-title-text>
                                            <img style="width: 50px; margin: 0 auto;" id="totalLeadsAtivos" ng-show="report.totalLeadsAtivos === ''" style="display: none;" src="{{appurl + 'assets/img/loading.gif'}}">


                                            <div class="col-md-6">
                                                <span class="md-subhead">Total de Leads Ativos</span>
                                            </div>

                                            <div class="col-md-6" style="padding-top: 5px;">
                                                <span class="md-headline"><strong ng-bind="report.totalLeadsAtivos"></strong></span>
                                            </div>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>

                            <md-card flex-xs layout="column" class="text-center card5" style="position: relative;" ng-show="customPanel.box_panel3">
                                <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-totalLeadsPipeline'] != null && baloesGraphs['card-totalLeadsPipeline'].exibir == '1')" ng-click="exibeBalao('card-totalLeadsPipeline')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">

                                <a href="{{appurl + 'leads'}}">
                                    <md-card-title>
                                        <md-card-title-text>
                                            <img style="width: 50px; margin: 0 auto;" id="totalLeadsPipeline" ng-show="report.totalLeadsPipeline === ''" style="display: none;" src="{{appurl + 'assets/img/loading.gif'}}">

                                            <div class="col-md-6">
                                                <span class="md-subhead">Total de Leads em Pipeline</span>
                                            </div>

                                            <div class="col-md-6" style="padding-top: 5px;">
                                                <span class="md-headline"><strong ng-bind="report.totalLeadsPipeline"></strong></span>
                                            </div>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>


                            <md-card flex-xs layout="column" class="text-center card2" style="position: relative;" ng-show="customPanel.box_panel4">
                                <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-totalLeadsEmDia'] != null && baloesGraphs['card-totalLeadsEmDia'].exibir == '1')" ng-click="exibeBalao('card-totalLeadsEmDia')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">

                                <a href="{{appurl + 'leads?flt_vencidos=1'}}">

                                    <md-card-title>
                                        <md-card-title-text>
                                            <img style="width: 50px; margin: 0 auto;" id="totalLeadsEmDia" ng-show="report.totalLeadsEmDia === ''" style="display: none;" src="{{appurl + 'assets/img/loading.gif'}}">

                                            <div class="col-md-6">
                                                <span class="md-subhead">Total de Leads em dia</span>
                                            </div>

                                            <div class="col-md-6" style="padding-top: 5px;">
                                                <span class="md-headline"><strong ng-bind="report.totalLeadsEmDia"></strong></span>
                                            </div>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>

                            <md-card flex-xs layout="column" class="text-center card3" style="position: relative;" ng-show="customPanel.box_panel5">
                                <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-totalLeadsAtrasados'] != null && baloesGraphs['card-totalLeadsAtrasados'].exibir == '1')" ng-click="exibeBalao('card-totalLeadsAtrasados')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">

                                <a href="{{appurl + 'leads?flt_vencidos=2'}}">

                                    <md-card-title>
                                        <md-card-title-text>
                                            <img style="width: 50px; margin: 0 auto;" id="totalLeadsAtrasados" ng-show="report.totalLeadsAtrasados === ''" style="display: none;" src="{{appurl + 'assets/img/loading.gif'}}">

                                            <div class="col-md-6">
                                                <span class="md-subhead">Total de Leads atrasados</span>
                                            </div>

                                            <div class="col-md-6" style="padding-top: 5px;">
                                                <span class="md-headline"><strong ng-bind="report.totalLeadsAtrasados"></strong></span>
                                            </div>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>

                            <md-card flex-xs layout="column" class="text-center card4" style="position: relative;" ng-show="customPanel.box_panel6">
                                <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-totalAtividades'] != null && baloesGraphs['card-totalAtividades'].exibir == '1')" ng-click="exibeBalao('card-totalAtividades')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <a href="{{appurl + 'leads'}}">
                                    <md-card-title>
                                        <md-card-title-text>
                                            <img style="width: 50px; margin: 0 auto;" id="totalAtividades" ng-show="report.totalAtividades === ''" style="display: none;" src="{{appurl + 'assets/img/loading.gif'}}">

                                            <div class="col-md-6">
                                                <span class="md-subhead">Total de atividades </span>
                                            </div>

                                            <div class="col-md-6" style="padding-top: 5px;">
                                                <span class="md-headline"><strong ng-bind="report.totalAtividades"></strong></span>
                                            </div>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>

                            <md-card flex-xs layout="column" class="text-center card5" style="position: relative;" ng-show="customPanel.box_metas1">
                                <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-totalMeta'] != null && baloesGraphs['card-totalMeta'].exibir == '1')" ng-click="exibeBalao('card-totalMeta')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <a href="customers">
                                    <md-card-title>
                                        <md-card-title-text>

                                            <div class="col-md-6">
                                                <span class="md-subhead" style="vertical-align: sub;">Total da meta</span>
                                            </div>

                                            <div class="col-md-6" style="padding-top: 5px;">
                                                <span class="md-headline"><strong ng-bind="report.totalMeta"></strong></span>
                                            </div>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>

                            <md-card flex-xs layout="column" class="text-center card1" style="position: relative;" ng-show="customPanel.box_metas2">
                                <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-totalAtingido'] != null && baloesGraphs['card-totalAtingido'].exibir == '1')" ng-click="exibeBalao('card-totalAtingido')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">

                                <a href="customers">
                                    <md-card-title>
                                        <md-card-title-text>

                                            <div class="col-md-6">
                                                <span class="md-subhead" style="vertical-align: sub;">Total atingido</span>
                                            </div>

                                            <div class="col-md-6" style="padding-top: 5px;">
                                                <span class="md-headline"><strong ng-bind="report.totalAtingido"></strong></span>
                                            </div>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>

                            <md-card flex-xs layout="column" class="text-center card2" style="position: relative;" ng-show="customPanel.box_metas3">
                                <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-porcetAtingido'] != null && baloesGraphs['card-porcetAtingido'].exibir == '1')" ng-click="exibeBalao('card-porcetAtingido')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">

                                <a href="invoices">
                                    <md-card-title>
                                        <md-card-title-text>

                                            <div class="col-md-6">
                                                <span class="md-subhead" style="vertical-align: sub;">% atingido</span>
                                            </div>

                                            <div class="col-md-6" style="padding-top: 5px;">
                                                <span class="md-headline"><strong ng-bind="report.porcetAtingido + ' %'"></strong></span>
                                            </div>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>

                            <md-card flex-xs layout="column" class="text-center card3" style="position: relative;" ng-show="customPanel.box_metas4">
                                <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-totalFalta'] != null && baloesGraphs['card-totalFalta'].exibir == '1')" ng-click="exibeBalao('card-totalFalta')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <a href="invoices">
                                    <md-card-title>
                                        <md-card-title-text>

                                            <div class="col-md-6">
                                                <span class="md-subhead" style="vertical-align: sub;">Total que falta</span>
                                            </div>

                                            <div class="col-md-6" style="padding-top: 5px;">
                                                <span class="md-headline"><strong ng-bind="report.totalFalta"></strong></span>
                                            </div>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>

                            <md-card flex-xs layout="column" class="text-center card4" style="position: relative;" ng-show="customPanel.box_metas5">
                                <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-porcentFalta'] != null && baloesGraphs['card-porcentFalta'].exibir == '1')" ng-click="exibeBalao('card-porcentFalta')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <a href="invoices">
                                    <md-card-title>
                                        <md-card-title-text>

                                            <div class="col-md-6">
                                                <span class="md-subhead" style="vertical-align: sub;">% do que falta</span>
                                            </div>

                                            <div class="col-md-6" style="padding-top: 5px;">
                                                <span class="md-headline"><strong ng-bind="report.porcentFalta + ' %'"></strong></span>
                                            </div>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>

                            <md-card flex-xs layout="column" class="text-center card1" style="position: relative;" ng-show="customPanel.box_clientes1">
                                <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-totalClientesAtivos'] != null && baloesGraphs['card-totalClientesAtivos'].exibir == '1')" ng-click="exibeBalao('card-totalClientesAtivos')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <a href="customers">
                                    <md-card-title>
                                        <md-card-title-text>

                                            <div class="col-md-6">
                                                <span class="md-subhead" style="vertical-align: sub;">Clientes ativos</span>
                                            </div>

                                            <div class="col-md-6" style="padding-top: 5px;">
                                                <span class="md-headline"><strong ng-bind="report.totalClientesAtivos"></strong></span>
                                            </div>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>

                            <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card3" style="position: relative;" ng-show="customPanel.box_clientes2">
                                <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-totalLTMes'] != null && baloesGraphs['card-totalLTMes'].exibir == '1')" ng-click="exibeBalao('card-totalLTMes')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <a href="customers">
                                    <md-card-title>
                                        <md-card-title-text>

                                            <div class="col-md-6">
                                                <span class="md-subhead" style="vertical-align: sub;">Lifetime Clientes</span>
                                            </div>

                                            <div class="col-md-6" style="padding-top: 5px;">
                                                <span class="md-headline"><strong ng-bind="report.totalLTMes"></strong> <small>meses e </small><span ng-bind="report.totalLTDias"></span><small> dias</small> </span>
                                            </div>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>

                            <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card4" style="position: relative;" ng-show="customPanel.box_clientes3">
                                <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-totalLTV'] != null && baloesGraphs['card-totalLTV'].exibir == '1')" ng-click="exibeBalao('card-totalLTV')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <a href="invoices">
                                    <md-card-title>
                                        <md-card-title-text>

                                            <div class="col-md-6">
                                                <span class="md-subhead">Lifetime Value Clientes</span>
                                            </div>

                                            <div class="col-md-6" style="padding-top: 5px;">
                                                <span class="md-headline"><strong ng-bind="report.totalLTV "></strong></span>
                                            </div>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>

                            <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card2" style="position: relative;" ng-show="customPanel.box_clientes4">
                                <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-totalChurn'] != null && baloesGraphs['card-totalChurn'].exibir == '1')" ng-click="exibeBalao('card-totalChurn')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <a href="invoices">
                                    <md-card-title>
                                        <md-card-title-text>

                                            <div class="col-md-6">
                                                <span class="md-subhead" style="vertical-align: sub;">Churn rate</span>
                                            </div>

                                            <div class="col-md-6" style="padding-top: 5px;">
                                                <span class="md-headline"><strong ng-bind="report.totalChurn"></strong></span>
                                            </div>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>

                            <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card1" style="position: relative;" ng-show="customPanel.box_financeiro1">
                                <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-fatBruto'] != null && baloesGraphs['card-fatBruto'].exibir == '1')" ng-click="exibeBalao('card-fatBruto')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <md-card-title>
                                    <md-card-title-text>
                                        <div class="col-md-6">
                                            <span class="md-subhead" style="vertical-align: sub;">Faturamento bruto</span>
                                        </div>
                                        <div class="col-md-6">
                                            <span class="md-headline"><strong ng-bind="report.fatBruto"></strong></span>
                                        </div>
                                    </md-card-title-text>
                                </md-card-title>
                            </md-card>

                            <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card3" style="position: relative;" ng-show="customPanel.box_financeiro2">
                                <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-totalDepsECompras'] != null && baloesGraphs['card-totalDepsECompras'].exibir == '1')" ng-click="exibeBalao('card-totalDepsECompras')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <md-card-title>
                                    <md-card-title-text>
                                        <div class="col-md-6">
                                            <span class="md-subhead">Total de Despesas & Compras</span>
                                        </div>
                                        <div class="col-md-6">
                                            <span class="md-headline"><strong ng-bind="report.totalDepsECompras"></strong> </span>
                                        </div>

                                    </md-card-title-text>
                                </md-card-title>
                            </md-card>

                            <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card4" style="position: relative;" ng-show="customPanel.box_financeiro3">
                                <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-RestLiquid'] != null && baloesGraphs['card-RestLiquid'].exibir == '1')" ng-click="exibeBalao('card-RestLiquid')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <md-card-title>
                                    <md-card-title-text>
                                        <div class="col-md-6">
                                            <span class="md-subhead" style="vertical-align: sub;">Resultado líquido</span>
                                        </div>
                                        <div class="col-md-6">
                                            <span class="md-headline"><strong ng-bind="report.RestLiquid"></strong></span>
                                        </div>

                                    </md-card-title-text>
                                </md-card-title>
                            </md-card>

                            <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card5" style="position: relative;" ng-show="customPanel.box_financeiro4">
                                <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-MargemRestLiq'] != null && baloesGraphs['card-MargemRestLiq'].exibir == '1')" ng-click="exibeBalao('card-MargemRestLiq')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <md-card-title>
                                    <md-card-title-text>
                                        <div class="col-md-6">
                                            <span class="md-subhead">Margem do lucro líquido</span>
                                        </div>
                                        <div class="col-md-6">
                                            <span class="md-headline"><strong ng-bind="report.MargemRestLiq"></strong></span>
                                        </div>

                                    </md-card-title-text>
                                </md-card-title>
                            </md-card>

                        </div>
                    </md-card>

                    <?php
                    if ($user_data['super_admin'] == "1") {
                    ?>
                        <div layout-xs="column" layout="row" class="" id="divgraphNvOportPorClientes" ng-show="customPanel.graph_panel1">
                            <div flex-xs flex-gt-xs="100" layout="column">
                                <md-card>
                                    <div class="widget-chart-container">
                                        <div class="widget-counter-group widget-counter-group-right">
                                            <div style="width: 100%;" class="pull-left">
                                                <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                <div class="titleDash">
                                                    <h4 style="padding: 0px;margin: 0px;"><b>Novas oportunidades por cliente</b>
                                                        <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['graphNvOportPorClientes'] != null && baloesGraphs['graphNvOportPorClientes'].exibir == '1')" ng-click="exibeBalao('graphNvOportPorClientes')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                    </h4>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="my-2" style="padding-bottom: 60px;">
                                            <div class="chart-wrapper" style="height:300px">
                                                <canvas height="300" style="padding-top: 25px;" id="graphNvOportPorClientes"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </md-card>
                            </div>
                        </div>
                    <?php
                    }
                    ?>

                    <div layout-xs="column" layout="row" class="">
                        <div flex-xs flex-gt-xs="100" layout="column" ng-show="customPanel.graph_panel2">
                            <md-card>
                                <div class="widget-chart-container">
                                    <div class="widget-counter-group widget-counter-group-right">
                                        <div style="width: 100%;" class="pull-left">
                                            <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                            <div class="titleDash">
                                                <h4 style="padding: 0px;margin: 0px;"><b>Novas oportunidades por mês</b>
                                                    <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['graphNvOportMes'] != null && baloesGraphs['graphNvOportMes'].exibir == '1')" ng-click="exibeBalao('graphNvOportMes')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="my-2" style="margin-top: 50px;margin-bottom: 40px;">
                                        <div class="chart-wrapper" style="height:320px">
                                            <section class="main">
                                                <ul class="graph-container graph-rainbow">
                                                    <li ng-repeat="graph in graphNvOportMes.data">
                                                        <span>{{graph.label}}</span>
                                                        <div class="bar-wrapper">
                                                            <div class="bar-container">
                                                                <div class="bar-background"></div>
                                                                <span class="exibeData">{{graph.data}}</span>
                                                                <div class="bar-inner" style="height:{{graph.porcent}}%;bottom: 0;">{{graph.data}}</div>
                                                                <div class="bar-foreground"></div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <ul class="graph-marker-container">
                                                            <li style="bottom:{{graph.porcent}}%;" ng-repeat="graph in graphNvOportMes.labels"><span>{{graph.label}}</span></li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </section>
                                        </div>
                                    </div>
                                </div>
                            </md-card>
                        </div>
                    </div>
                    <div layout-xs="column" layout="row" class="" ng-show="customPanel.graph_panel3">
                        <div flex-xs flex-gt-xs="100" layout="column">
                            <md-card>

                                <div class="widget-chart-container">
                                    <div class="widget-counter-group widget-counter-group-right">
                                        <div style="width: 100%;" class="pull-left">
                                            <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                            <div class="titleDash">
                                                <h4 style="padding: 0px;margin: 0px;"><b>Novas oportunidades diárias</b>
                                                    <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['graphNvOport'] != null && baloesGraphs['graphNvOport'].exibir == '1')" ng-click="exibeBalao('graphNvOport')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">

                                                </h4>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="my-2" style="padding-bottom: 60px;">
                                        <div class="chart-wrapper" style="height:300px">
                                            <section class="graphNvOport" id="graphNvOport" style="height:300px">
                                                <svg class="graphBarra" style="padding: 15px; width: 100%;"></svg>
                                            </section>
                                        </div>
                                    </div>
                                </div>
                            </md-card>
                        </div>
                    </div>

                    <div layout-xs="column" layout="row" class="" ng-show="customPanel.graph_panel4">
                        <div flex-xs flex-gt-xs="100" layout="column">
                            <md-card>
                                <div class="widget-chart-container">
                                    <div class="widget-counter-group widget-counter-group-right">
                                        <div style="width: 100%;" class="pull-left">
                                            <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                            <div class="titleDash">
                                                <h4 style="padding: 0px;margin: 0px;"><b>Origem</b>
                                                    <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['graphOrigem'] != null && baloesGraphs['graphOrigem'].exibir == '1')" ng-click="exibeBalao('graphOrigem')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">

                                                </h4>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="my-2" style="margin-top: 50px;margin-bottom: 40px;">
                                        <div class="chart-wrapper" style="height:320px">
                                            <section class="main">
                                                <ul class="graph-container graph-rainbow">
                                                    <li ng-repeat="graph in graphOrigem.data">
                                                        <span>{{graph.label}}</span>
                                                        <div class="bar-wrapper">
                                                            <div class="bar-container">
                                                                <div class="bar-background"></div>
                                                                <span class="exibeData">{{graph.data}}</span>
                                                                <div class="bar-inner" style="height:{{graph.porcent}}%;bottom: 0;">{{graph.data}}</div>
                                                                <div class="bar-foreground"></div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <ul class="graph-marker-container">
                                                            <li style="bottom:{{graph.porcent}}%;" ng-repeat="graph in graphOrigem.labels"><span>{{graph.label}}</span></li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </section>
                                        </div>
                                    </div>
                                </div>
                            </md-card>
                        </div>

                    </div>

                    <div layout-xs="column" layout="row" class="">
                        <div flex-xs flex-gt-xs="100" layout="column" ng-show="customPanel.graph_panel5">
                            <md-card>
                                <div class="widget-chart-container">
                                    <div class="widget-counter-group widget-counter-group-right">
                                        <div style="width: 100%;" class="pull-left">
                                            <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                            <div class="titleDash">
                                                <h4 style="padding: 0px;margin: 0px;"><b>Estados</b>
                                                    <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['graphEstados'] != null && baloesGraphs['graphEstados'].exibir == '1')" ng-click="exibeBalao('graphEstados')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">

                                                </h4>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="my-2" style="padding-bottom: 60px;">
                                        <div class="chart-wrapper" style="height:300px">
                                            <canvas height="300" style="padding-top: 25px;" id="graphEstados"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </md-card>
                        </div>
                    </div>
                    <div layout-xs="column" layout="row" class="">
                        <div flex-xs flex-gt-xs="100" layout="column" ng-show="customPanel.graph_panel6">
                            <md-card>
                                <div class="widget-chart-container">
                                    <div class="widget-counter-group widget-counter-group-right">
                                        <div style="width: 100%;" class="pull-left">
                                            <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                            <div class="titleDash">
                                                <h4 style="padding: 0px;margin: 0px;"><b>Setor de Atividades</b>
                                                    <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['graphSetorDeAtividades'] != null && baloesGraphs['graphSetorDeAtividades'].exibir == '1')" ng-click="exibeBalao('graphSetorDeAtividades')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">

                                                </h4>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="my-2" style="padding-bottom: 60px;">
                                        <div class="chart-wrapper" style="height:300px">
                                            <canvas height="300" style="padding-top: 25px;" id="graphSetorDeAtividades"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </md-card>
                        </div>
                    </div>

                    <div layout-xs="column" layout="row" class="" ng-show="customPanel.graph_panel7">
                        <div flex-xs flex-gt-xs="100" layout="column">
                            <md-card>
                                <div layout-align="start" flex>
                                    <!--
                                    <md-input-container flex="50">
                                        <?php
                                        echo '<md-select ng-model="LeadReportMonth" placeholder="Select a state" ng-change="LeadMonthChanged()">' . PHP_EOL;
                                        for ($m = 1; $m <= 12; $m++) {
                                            $_selected = '';
                                            if ($m == date('m')) {
                                                $_selected = ' selected';
                                            }
                                            echo '<md-option ng-value="' . $m . '"' . $_selected . '>' . (date('F', mktime(0, 0, 0, $m, 1))) . '</md-option>' . PHP_EOL;
                                        }
                                        echo '</md-select>' . PHP_EOL;
                                        ?>
                                    </md-input-container>
                                    -->
                                </div>

                                <div class="widget-counter-group widget-counter-group-right">
                                    <div style="width: 100%;" class="pull-left">
                                        <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                        <div class="titleDash">
                                            <h4 style="padding: 0px;margin: 0px;"><b>Atividades</b>
                                                <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['graphAtividades'] != null && baloesGraphs['graphAtividades'].exibir == '1')" ng-click="exibeBalao('graphAtividades')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">

                                            </h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="my-2">
                                    <div class="chart-wrapper" style="height: 445px;overflow: auto;">
                                        <canvas class="graph03" id="atv_graph"></canvas>
                                    </div>
                                </div>

                            </md-card>
                        </div>

                    </div>


                    <div layout-xs="column" layout="row" class="" ng-show="customPanel.graph_metas1">
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

                    <div layout-xs="column" layout="row" class="" ng-show="customPanel.graph_metas2">
                        <div flex-xs flex-gt-xs="100" layout="column">
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
                    </div>
                    <div layout-xs="column" layout="row" class="">
                        <div flex-xs flex-gt-xs="100" layout="column" ng-show="customPanel.graph_metas2">
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

                    <div layout-xs="column" layout="row" class="">
                        <div flex-xs flex-gt-xs="100" layout="column" ng-show="customPanel.graph_clientes1">
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
                    </div>
                    <div layout-xs="column" layout="row" class="">
                        <div flex-xs flex-gt-xs="100" layout="column" ng-show="customPanel.graph_clientes2">
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

                    <div layout-xs="column" layout="row" class="" ng-show="customPanel.graph_clientes3">
                        <div flex-xs flex-gt-xs="100" layout="column">
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
                                            <section class="graphFaturamentoTop10" id="graphNovosClientes" style="height:100%">
                                                <svg class="graphBarra" style="padding: 15px; width: 100%;"></svg>
                                            </section>
                                        </div>
                                    </div>
                                </div>
                            </md-card>
                        </div>

                    </div>

                    <div layout-xs="column" layout="row" class="">
                        <div flex-xs flex-gt-xs="100" layout="column" ng-show="customPanel.graph_clientes4">
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
                    </div>
                    <div layout-xs="column" layout="row" class="">
                        <div flex-xs flex-gt-xs="100" layout="column" ng-show="customPanel.graph_clientes5">
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

                    <div flex-xs flex-gt-xs="100" layout="column" ng-show="customPanel.graph_financeiro1">
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

                                <div class="my-2" style="margin-bottom: 3em;margin-top: 1em;">
                                    <div id="graphFaturamento" class="chart-wrapper" style="height:340px;float: left;width: 100%;">
                                        <section class="main">
                                            <ul class="graph-container graph-blue">
                                                <li ng-repeat="graph in graphFaturamento.data">
                                                    <span>{{graph.label}}</span>
                                                    <div class="bar-wrapper">
                                                        <div class="bar-container">
                                                            <div class="bar-background"></div>
                                                            <span class="exibeData" ng-bind-html="graph.data | currencyFormat:cur_code:null:true:cur_lct"></span>
                                                            <div class="bar-inner" style="height:{{graph.porcent}}%;bottom: 0;" ng-bind-html="graph.data | currencyFormat:cur_code:null:true:cur_lct"></div>
                                                            <div class="bar-foreground"></div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <ul class="graph-marker-container">
                                                        <li style="bottom:{{graph.porcent}}%;" ng-repeat="graph in graphFaturamento.labels"><span>{{graph.label}}</span></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </section>
                                    </div>
                                </div>
                            </div>
                        </md-card>
                    </div>

                    <div layout-xs="column" layout="row" class="" ng-show="customPanel.graph_financeiro2">
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

                    <div layout-xs="column" layout="row" class="" ng-show="customPanel.graph_financeiro3">
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
                        <div flex-xs flex-gt-xs="100" layout="column" ng-show="customPanel.graph_financeiro4">
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
                    </div>
                    <div layout-xs="column" layout="row" class="">
                        <div flex-xs flex-gt-xs="100" layout="column" ng-show="customPanel.graph_financeiro5">
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


                    <div layout-xs="column" layout="row" class="" ng-show="customPanel.graph_financeiro6">
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
        </md-content>
    </md-content>

    <md-content class="saudacao " ng-show="!overviewLoader">


        <div class="titleSaud">Next Message</div>

        <md-button ng-show="'<?= $user_data['admin'] ?>' == '1'" ng-click="Create()" class="md-raised md-primary btn-report btn_add_user" aria-label="Close">
            Adicionar usuário
        </md-button>

        <div class="content-saud">
            <div class="input-group mb-3 input-search">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
                </div>
                <input type="text" ng-model="searchPanel" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
            </div>

            <div ng-click="setChat(chat)" class="loadMensagem" ng-repeat="chat in chats | filter : searchPanel">
                <img src="{{appurl + 'uploads/images/' + chat.details.staffavatar }}">
                <a style="max-width: 100%;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;" href="javaScript:void(0)"><b ng-bind="chat.details.staffname"></b></a>
                <p style="max-width: calc(100% - 75px);max-width: calc(100% - 75px); overflow: hidden;text-overflow: ellipsis;display: -webkit-box;-webkit-line-clamp: 2;-webkit-box-orient: vertical;" ng-bind="chat.ultimaMsg"></p>
                <i ng-if="chat.msg_nova == 1" style="color: #17c93e;font-size: 11px;float: right;" class="fas fa-circle"></i>
            </div>

        </div>

    </md-content>
    <md-content class="main-content container-fluid  col-lg-3 main-home main-home-bottom">
        <ciuis-sidebar class="sidebar-home"></ciuis-sidebar>
    </md-content>

    <div class="modal fade" id="modalCustom" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloGraphBalao">Customizar painel</h5>
                    <button type="button" class="close closeModal" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <a href="javaScript:void(0)" class="btn-collapse-home" data-toggle="collapse" data-target="#collapseLeads" role="button" aria-expanded="false" aria-controls="collapseLeads">
                        Leads <i class="fas fa-angle-down"></i>
                    </a>

                    <div class="row collapse" id="collapseLeads">
                        <div class="col-md-6">
                            <h3>Gráficos de leads</h3>
                        </div>

                        <div class="col-md-6">
                            <h3>Boxes de leads</h3>
                        </div>

                        <hr>

                        <div class="col-md-6 dv-modal-c">
                            <?php
                            if ($user_data['super_admin'] == "1") {
                            ?>
                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.graph_panel1" ng-change="verificaCustomPanel('G', customPanel.graph_panel1, 'graph_panel1'); changeCustomPanel('leads');" type="checkbox" class="form-check-input" id="graph_panel1">
                                    <label class="form-check-label" for="graph_panel1">Novas oportunidades por cliente</label>
                                </div>
                            <?php
                            }
                            ?>
                            <div class="form-group form-check from-grahps">
                                <input ng-model="customPanel.graph_panel2" ng-change="verificaCustomPanel('G', customPanel.graph_panel2, 'graph_panel2'); changeCustomPanel('leads');" type="checkbox" class="form-check-input" id="graph_panel2">
                                <label class="form-check-label" for="graph_panel2">Novas oportunidades por mês</label>
                            </div>

                            <div class="form-group form-check from-grahps">
                                <input ng-model="customPanel.graph_panel3" ng-change="verificaCustomPanel('G', customPanel.graph_panel3, 'graph_panel3'); changeCustomPanel('leads');" type="checkbox" class="form-check-input" id="graph_panel3">
                                <label class="form-check-label" for="graph_panel3">Novas oportunidades diárias</label>
                            </div>

                            <div class="form-group form-check from-grahps">
                                <input ng-model="customPanel.graph_panel4" ng-change="verificaCustomPanel('G', customPanel.graph_panel4, 'graph_panel4'); changeCustomPanel('leads');" type="checkbox" class="form-check-input" id="graph_panel4">
                                <label class="form-check-label" for="graph_panel4">Origem</label>
                            </div>

                            <div class="form-group form-check from-grahps">
                                <input ng-model="customPanel.graph_panel5" ng-change="verificaCustomPanel('G', customPanel.graph_panel5, 'graph_panel5'); changeCustomPanel('leads');" type="checkbox" class="form-check-input" id="graph_panel5">
                                <label class="form-check-label" for="graph_panel5">Estados</label>
                            </div>

                            <div class="form-group form-check from-grahps">
                                <input ng-model="customPanel.graph_panel6" ng-change="verificaCustomPanel('G', customPanel.graph_panel6, 'graph_panel6'); changeCustomPanel('leads');" type="checkbox" class="form-check-input" id="graph_panel6">
                                <label class="form-check-label" for="graph_panel6">Setor de Atividades</label>
                            </div>

                            <div class="form-group form-check from-grahps">
                                <input ng-model="customPanel.graph_panel7" ng-change="verificaCustomPanel('G', customPanel.graph_panel7, 'graph_panel7'); changeCustomPanel('leads');" type="checkbox" class="form-check-input" id="graph_panel7">
                                <label class="form-check-label" for="graph_panel7">Atividades</label>
                            </div>
                        </div>
                        <div class="col-md-6 dv-modal-c">
                            <div class="form-group form-check from-grahps">
                                <input ng-model="customPanel.box_panel1" ng-change="verificaCustomPanel('B', customPanel.box_panel1, 'box_panel1'); changeCustomPanel('leads');" type="checkbox" class="form-check-input" id="box_panel1">
                                <label class="form-check-label" for="box_panel1">Total de Leads gerados</label>
                            </div>

                            <div class="form-group form-check from-grahps">
                                <input ng-model="customPanel.box_panel2" ng-change="verificaCustomPanel('B', customPanel.box_panel2, 'box_panel2'); changeCustomPanel('leads');" type="checkbox" class="form-check-input" id="box_panel2">
                                <label class="form-check-label" for="box_panel2"> Total de Leads Ativos</label>
                            </div>

                            <div class="form-group form-check from-grahps">
                                <input ng-model="customPanel.box_panel3" ng-change="verificaCustomPanel('B', customPanel.box_panel3, 'box_panel3'); changeCustomPanel('leads');" type="checkbox" class="form-check-input" id="box_panel3">
                                <label class="form-check-label" for="box_panel3"> Total de Leads em Pipeline</label>
                            </div>

                            <div class="form-group form-check from-grahps">
                                <input ng-model="customPanel.box_panel4" ng-change="verificaCustomPanel('B', customPanel.box_panel4, 'box_panel4'); changeCustomPanel('leads');" type="checkbox" class="form-check-input" id="box_panel4">
                                <label class="form-check-label" for="box_panel4">Total de Leads em dia</label>
                            </div>

                            <div class="form-group form-check from-grahps">
                                <input ng-model="customPanel.box_panel5" ng-change="verificaCustomPanel('B', customPanel.box_panel5, 'box_panel5'); changeCustomPanel('leads');" type="checkbox" class="form-check-input" id="box_panel5">
                                <label class="form-check-label" for="box_panel5">Total de Leads atrasados</label>
                            </div>

                            <div class="form-group form-check from-grahps">
                                <input ng-model="customPanel.box_panel6" ng-change="verificaCustomPanel('B', customPanel.box_panel6, 'box_panel6'); changeCustomPanel('leads');" type="checkbox" class="form-check-input" id="box_panel6">
                                <label class="form-check-label" for="box_panel6">Total de atividades</label>
                            </div>
                        </div>
                    </div>
                    <?php
                    if ($acessoMeta == 1) {
                    ?>

                        <a href="javaScript:void(0)" class="btn-collapse-home" data-toggle="collapse" data-target="#collapseMetas" role="button" aria-expanded="false" aria-controls="collapseMetas">
                            Metas <i class="fas fa-angle-down"></i>
                        </a>

                        <div class="row collapse" id="collapseMetas">
                            <div class="col-md-6">
                                <h3>Gráficos de metas</h3>
                            </div>
                            <div class="col-md-6">
                                <h3>Boxes de metas</h3>
                            </div>
                            <hr>

                            <div class="col-md-6 dv-modal-c">
                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.graph_metas1" ng-change="verificaCustomPanel('G', customPanel.graph_metas1, 'graph_metas1'); changeCustomPanel('metas');" type="checkbox" class="form-check-input" id="graph_metas1">
                                    <label class="form-check-label" for="graph_metas1">Metas vs resultados</label>
                                </div>

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.graph_metas2" ng-change="verificaCustomPanel('G', customPanel.graph_metas2, 'graph_metas2'); changeCustomPanel('metas');" type="checkbox" class="form-check-input" id="graph_metas2">
                                    <label class="form-check-label" for="graph_metas2">% Alcançados</label>
                                </div>

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.graph_metas3" ng-change="verificaCustomPanel('G', customPanel.graph_metas3, 'graph_metas3'); changeCustomPanel('metas');" type="checkbox" class="form-check-input" id="graph_metas3">
                                    <label class="form-check-label" for="graph_metas3">Controle de bonificações</label>
                                </div>
                            </div>

                            <div class="col-md-6 dv-modal-c">

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.box_metas1" ng-change="verificaCustomPanel('B', customPanel.box_metas1, 'box_metas1'); changeCustomPanel('metas');" type="checkbox" class="form-check-input" id="box_metas1">
                                    <label class="form-check-label" for="box_metas1">Total da meta</label>
                                </div>

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.box_metas2" ng-change="verificaCustomPanel('B', customPanel.box_metas2, 'box_metas2'); changeCustomPanel('metas');" type="checkbox" class="form-check-input" id="box_metas2">
                                    <label class="form-check-label" for="box_metas2">Total atingido</label>
                                </div>

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.box_metas3" ng-change="verificaCustomPanel('B', customPanel.box_metas3, 'box_metas3'); changeCustomPanel('metas');" type="checkbox" class="form-check-input" id="box_metas3">
                                    <label class="form-check-label" for="box_metas3">% atingido</label>
                                </div>

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.box_metas4" ng-change="verificaCustomPanel('B', customPanel.box_metas4, 'box_metas4'); changeCustomPanel('metas');" type="checkbox" class="form-check-input" id="box_metas4">
                                    <label class="form-check-label" for="box_metas4">Total que falta</label>
                                </div>

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.box_metas5" ng-change="verificaCustomPanel('B', customPanel.box_metas5, 'box_metas5'); changeCustomPanel('metas');" type="checkbox" class="form-check-input" id="box_metas5">
                                    <label class="form-check-label" for="box_metas5">% do que falta</label>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    if ($acessoCliente == 1) {
                    ?>
                        <a href="javaScript:void(0)" class="btn-collapse-home" data-toggle="collapse" data-target="#collapseClientes" role="button" aria-expanded="false" aria-controls="collapseClientes">
                            Clientes <i class="fas fa-angle-down"></i>
                        </a>

                        <div class="row collapse" id="collapseClientes">
                            <div class="col-md-6">
                                <h3>Gráficos de clientes</h3>
                            </div>
                            <div class="col-md-6">
                                <h3>Boxes de clientes</h3>
                            </div>
                            <hr>

                            <div class="col-md-6 dv-modal-c">
                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.graph_clientes1" ng-change="verificaCustomPanel('G', customPanel.graph_clientes1, 'graph_clientes1'); changeCustomPanel('clientes');" type="checkbox" class="form-check-input" id="graph_clientes1">
                                    <label class="form-check-label" for="graph_clientes1">Top 10 clientes por faturamento</label>
                                </div>

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.graph_clientes2" ng-change="verificaCustomPanel('G', customPanel.graph_clientes2, 'graph_clientes2'); changeCustomPanel('clientes');" type="checkbox" class="form-check-input" id="graph_clientes2">
                                    <label class="form-check-label" for="graph_clientes2">Clientes ativos</label>
                                </div>

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.graph_clientes3" ng-change="verificaCustomPanel('G', customPanel.graph_clientes3, 'graph_clientes3'); changeCustomPanel('clientes');" type="checkbox" class="form-check-input" id="graph_clientes3">
                                    <label class="form-check-label" for="graph_clientes3">Novos clientes</label>
                                </div>

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.graph_clientes4" ng-change="verificaCustomPanel('G', customPanel.graph_clientes4, 'graph_clientes4'); changeCustomPanel('clientes');" type="checkbox" class="form-check-input" id="graph_clientes4">
                                    <label class="form-check-label" for="graph_clientes4">Estado</label>
                                </div>

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.graph_clientes5" ng-change="verificaCustomPanel('G', customPanel.graph_clientes5, 'graph_clientes5'); changeCustomPanel('clientes');" type="checkbox" class="form-check-input" id="graph_clientes5">
                                    <label class="form-check-label" for="graph_clientes5">Ramo de Atividade</label>
                                </div>
                            </div>

                            <div class="col-md-6 dv-modal-c">
                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.box_clientes1" ng-change="verificaCustomPanel('B', customPanel.box_clientes1, 'box_clientes1'); changeCustomPanel('clientes');" type="checkbox" class="form-check-input" id="box_clientes1">
                                    <label class="form-check-label" for="box_clientes1">Clientes ativos</label>
                                </div>

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.box_clientes2" ng-change="verificaCustomPanel('B', customPanel.box_clientes2, 'box_clientes2'); changeCustomPanel('clientes');" type="checkbox" class="form-check-input" id="box_clientes2">
                                    <label class="form-check-label" for="box_clientes2">Lifetime Clientes</label>
                                </div>

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.box_clientes3" ng-change="verificaCustomPanel('B', customPanel.box_clientes3, 'box_clientes3'); changeCustomPanel('clientes');" type="checkbox" class="form-check-input" id="box_clientes3">
                                    <label class="form-check-label" for="box_clientes3">Lifetime Value Clientes</label>
                                </div>

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.box_clientes4" ng-change="verificaCustomPanel('B', customPanel.box_clientes4, 'box_clientes4'); changeCustomPanel('clientes');" type="checkbox" class="form-check-input" id="box_clientes4">
                                    <label class="form-check-label" for="box_clientes4">Churn rate</label>
                                </div>
                            </div>

                        </div>
                    <?php

                    }
                    if ($acessoFinanceiro == 1) {
                    ?>
                        <a href="javaScript:void(0)" class="btn-collapse-home" data-toggle="collapse" data-target="#collapseFinanceiro" role="button" aria-expanded="false" aria-controls="collapseFinanceiro">
                            Financeiro <i class="fas fa-angle-down"></i>
                        </a>

                        <div class="row collapse" id="collapseFinanceiro">
                            <div class="col-md-6">
                                <h3>Gráficos de Financeiro</h3>
                            </div>
                            <div class="col-md-6">
                                <h3>Boxes de Financeiro</h3>
                            </div>
                            <hr>

                            <div class="col-md-6 dv-modal-c">

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.graph_financeiro1" ng-change="verificaCustomPanel('G', customPanel.graph_financeiro1, 'graph_financeiro1'); changeCustomPanel('financeiro');" type="checkbox" class="form-check-input" id="graph_financeiro1">
                                    <label class="form-check-label" for="graph_financeiro1">Faturamento bruto</label>
                                </div>

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.graph_financeiro2" ng-change="verificaCustomPanel('G', customPanel.graph_financeiro2, 'graph_financeiro2'); changeCustomPanel('financeiro');" type="checkbox" class="form-check-input" id="graph_financeiro2">
                                    <label class="form-check-label" for="graph_financeiro2">Faturamento X Despesas & Compras</label>
                                </div>

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.graph_financeiro3" ng-change="verificaCustomPanel('G', customPanel.graph_financeiro3, 'graph_financeiro3'); changeCustomPanel('financeiro');" type="checkbox" class="form-check-input" id="graph_financeiro3">
                                    <label class="form-check-label" for="graph_financeiro3">Lucro líquido</label>
                                </div>

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.graph_financeiro4" ng-change="verificaCustomPanel('G', customPanel.graph_financeiro4, 'graph_financeiro4'); changeCustomPanel('financeiro');" type="checkbox" class="form-check-input" id="graph_financeiro4">
                                    <label class="form-check-label" for="graph_financeiro4">Despesas e compras</label>
                                </div>

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.graph_financeiro5" ng-change="verificaCustomPanel('G', customPanel.graph_financeiro5, 'graph_financeiro5'); changeCustomPanel('financeiro');" type="checkbox" class="form-check-input" id="graph_financeiro5">
                                    <label class="form-check-label" for="graph_financeiro5">Análise de Despesas</label>
                                </div>

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.graph_financeiro6" ng-change="verificaCustomPanel('G', customPanel.graph_financeiro6, 'graph_financeiro6'); changeCustomPanel('financeiro');" type="checkbox" class="form-check-input" id="graph_financeiro6">
                                    <label class="form-check-label" for="graph_financeiro6">Fluxo de Caixa</label>
                                </div>


                            </div>

                            <div class="col-md-6 dv-modal-c">

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.box_financeiro1" ng-change="verificaCustomPanel('B', customPanel.box_financeiro1, 'box_financeiro1'); changeCustomPanel('financeiro');" type="checkbox" class="form-check-input" id="box_financeiro1">
                                    <label class="form-check-label" for="box_financeiro1">Faturamento bruto</label>
                                </div>

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.box_financeiro2" ng-change="verificaCustomPanel('B', customPanel.box_financeiro2, 'box_financeiro2'); changeCustomPanel('financeiro');" type="checkbox" class="form-check-input" id="box_financeiro2">
                                    <label class="form-check-label" for="box_financeiro2">Total de Despesas & Compras</label>
                                </div>

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.box_financeiro3" ng-change="verificaCustomPanel('B', customPanel.box_financeiro3, 'box_financeiro3'); changeCustomPanel('financeiro');" type="checkbox" class="form-check-input" id="box_financeiro3">
                                    <label class="form-check-label" for="box_financeiro3">Resultado líquido</label>
                                </div>

                                <div class="form-group form-check from-grahps">
                                    <input ng-model="customPanel.box_financeiro4" ng-change="verificaCustomPanel('B', customPanel.box_financeiro4, 'box_financeiro4'); changeCustomPanel('financeiro');" type="checkbox" class="form-check-input" id="box_financeiro4">
                                    <label class="form-check-label" for="box_financeiro4">Margem do lucro líquido</label>
                                </div>

                            </div>
                        </div>

                    <?php
                    }

                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalBalao" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloGraphBalao">Selecione os boxes e gráficos que você quer na sua Home</h5>
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
    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Create" ng-cloak style="width: 450px;">

        <md-toolbar class="toolbar-white">

            <div class="md-toolbar-tools">

                <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>

                <h2 flex md-truncate><?php echo lang2('create') ?></h2>

            </div>

        </md-toolbar>

        <md-content>

            <md-content layout-padding>

                <md-input-container class="md-block">

                    <label><?php echo lang2('name') ?></label>
                    <input required type="text" ng-model="new_staff.name" class="form-control" id="title">
                </md-input-container>

                <?php
                if (session()->super_admin == "1") {
                ?>
                    <md-input-container class="md-block" flex-gt-xs>
                        <label>Empresa Designada</label>
                        <md-select required ng-model="new_staff.id_company" name="assigned" style="min-width: 200px;">
                            <md-option ng-value="companty.id_company" ng-repeat="companty in companies">{{companty.nm_company != null ? companty.nm_company : companty.name}}</md-option>
                        </md-select>
                    </md-input-container>

                <?php
                }
                ?>

                <md-input-container class="md-block">
                    <label><?php echo lang2('email') ?></label>
                    <input required type="text" ng-model="new_staff.email" class="form-control" id="title" minlength="10" maxlength="100" ng-pattern="/^.+@.+\..+$/">
                </md-input-container>

                <md-input-container class="md-block password-input">
                    <label><?php echo lang2('password') ?></label>
                    <input type="text" ng-model="passwordNew" rel="gp" data-size="9" id="nc" data-character-set="a-z,A-Z,0-9,#">
                    <md-icon ng-click="getNewPass()" class="ion-refresh" style="display:inline-block;"></md-icon>
                </md-input-container>

                <md-input-container class="md-block">
                    <label><?php echo lang2('phone') ?></label>
                    <input type="text" ng-model="new_staff.phone" class="form-control phone" id="title">
                </md-input-container>

                <md-input-container class="md-block" flex-gt-xs>
                    <label><?php echo lang2('staffdepartment'); ?></label>
                    <md-select required ng-model="new_staff.department_id" name="assigned" style="min-width: 200px;">
                        <md-option ng-value="department.id" ng-repeat="department in departments">{{department.name}}</md-option>
                    </md-select>
                </md-input-container>

                <md-input-container class="md-block" flex-gt-xs>
                    <label><?php echo lang2('language'); ?></label>
                    <md-select required ng-model="new_staff.language" name="assigned" style="min-width: 200px;">
                        <md-option ng-value="language.foldername" ng-repeat="language in languages">{{language.name}}</md-option>
                    </md-select>
                </md-input-container>

                <md-input-container class="md-block" flex-gt-xs>
                    <label><?php echo lang2('roles'); ?></label>
                    <md-select required ng-model="new_staff.assigned_role" name="assigned_role" style="min-width: 200px;">
                        <md-option ng-value="role.role_id" ng-repeat="role in roles">{{role.role_name}} <span class="badge">{{role.role_type}}</span></md-option>
                    </md-select>
                </md-input-container>

                <md-input-container class="md-block">
                    <label><?php echo lang2('staff') . ' ' . lang2('timezone') ?></label>
                    <md-select ng-model="staff_timezone">
                        <md-optgroup ng-repeat="timezone in timezones" label="{{timezone.group}}">
                            <md-option ng-value="zone.value" ng-repeat="zone in timezone.zones">{{zone.value}}</md-option>
                        </md-optgroup>
                    </md-select>
                </md-input-container>

                <md-input-container class="md-block">
                    <label><?php echo lang2('address') ?></label>
                    <textarea rows="2" ng-model="new_staff.address" class="form-control"></textarea>
                </md-input-container>

                <md-input-container class="md-block">
                    <label style="margin-bottom: 17px;">Foto</label>
                    <input type="file" ng-model="new_staff.fotoStaff" id="fotoStaff" required name="profile_photo" file-model="profile_photo" accept="image/*">
                </md-input-container>

            </md-content>
            <md-content>
                <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>

                    <?php if (check_privilege('staff', 'create')) { ?>

                        <md-button ng-click="AddStaff()" class="md-raised md-primary btn-report block-button" ng-disabled="saving == true">

                            <span ng-hide="saving == true"><?php echo lang2('add'); ?></span>

                            <md-progress-circular class="white" ng-show="saving == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

                        </md-button>

                    <?php } ?>

                    <br /><br /><br /><br />

                </section>

            </md-content>

        </md-content>

    </md-sidenav>

</div>



<script type="text/javascript">
    var is_admin = '<?= session()->super_admin  ?>'
    var lang = {};
    lang.payments = '<?php echo lang2('payments') ?>';
    lang.expenses = '<?php echo lang2('expenses') ?>';
    CONSTdt_ate = null;
    CONST_idCompany = null;

    var acessoCliente = '<?= $acessoCliente ?>';
    var acessoMeta = '<?= $acessoMeta ?>';
    var acessoFinanceiro = '<?= $acessoFinanceiro ?>';

    $(document).ready(function() {

        if ($(window).width() < 700) {
            $('.main-home-top').remove();
        } else {
            $('.main-home-bottom').remove();
        }

    });
</script>

<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>
<script src="<?php echo base_url('assets/lib/chartjs/dist/Chart.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/lib/highcharts/highcharts.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/panel2.js?v=1.6.32') ?>"></script>