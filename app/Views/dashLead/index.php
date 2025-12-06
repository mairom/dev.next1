<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<div class="ciuis-body-content" ng-controller="DashLead_Controller">

    <md-content class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">
        <md-toolbar class="toolbar-white" style="border-right:1px solid #e0e0e0">
            <div class="md-toolbar-tools">
                <h4 class="text-muted" flex md-truncate><strong><?php echo lang2('Dashboard Lead') ?></strong></h4>
                <md-button class="md-icon-button" aria-label="Actions" ng-cloak>
                    <md-icon><span class="ion-flag text-muted"></span></md-icon>
                </md-button>
            </div>
        </md-toolbar>

        <md-content id="main-content">
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

            <md-content class="md-padding">
                <md-content class="widget-fullwidth ciuis-body-loading" style="min-height: 85px;padding: 1em;">
                    <div class='filtrosLeads'>

                        <?php
                        if (session()->super_admin == "1") {
                        ?>
                            <md-input-container class="md-block">
                                <label>Empresa</label>
                                <md-select placeholder="Escolha uma empresa" id="id_company" ng-model="filtros.id_company" ng-change="changeEmpresa();" style="min-width: 200px;">
                                    <md-option ng-value="-1">Todos</md-option>
                                    <md-option ng-value="company.id_company" ng-repeat="company in companies">{{company.nm_company}}</md-option>
                                </md-select>
                            </md-input-container>
                        <?php
                        }
                        ?>


                        <md-input-container class="md-block">
                            <i class="iconSearch ion-funnel text-muted "></i>
                            <md-select placeholder="Escolha um funil" id="flt_funil" ng-model="filtros.flt_funil" ng-change="getResultados()" style="min-width: 200px;">
                                <md-option ng-value="-1">Todos</md-option>
                                <md-option ng-value="funil.id_list" ng-repeat="funil in leadslist">{{funil.nm_list}}</md-option>
                            </md-select>
                        </md-input-container>

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
                            <md-select placeholder="<?php echo lang2('choosestaff'); ?>" id="flt_funcionario" ng-model="filtros.flt_funcionario" style="min-width: 200px;" ng-change="getResultados()">
                                <md-option ng-value="-1">Todos</md-option>
                                <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
                            </md-select>
                        </md-input-container>

                        <md-input-container class="md-block">
                            <label><?php echo lang2('source'); ?></label>
                            <md-select placeholder="<?php echo lang2('source'); ?>" id="flt_origem" ng-model="filtros.flt_origem" style="min-width: 200px;" ng-change="getResultados()">
                                <md-option ng-value="-1">Todos</md-option>
                                <md-option ng-value="source.id" ng-repeat="source in leadssources">{{source.name}}</md-option>
                            </md-select>
                        </md-input-container>

                        <md-button ng-click="baixarPdf()" style="width: 100px;height: 65px;float: right;color: #858585;cursor: pointer;" class="md-icon-button" aria-label="Settings">
                            <md-icon><i style="color: #8e44ad;" class="far fa-file-pdf"></i></md-icon><span>Gerar PDF</span>
                        </md-button>

                    </div>
                </md-content>
                <md-content class="widget-fullwidth ciuis-body-loading" style="overflow: hidden;" id="contentMain">
                    <md-card flex-xs flex-gt-xs="100" layout="column">
                        <div layout-xs="column" layout="row" class="bg-white">
                            <md-card flex-xs flex-gt-xs="25" layout="column" class="text-center card6" style="position: relative;">
                                <img ng-show="'<?= session()->super_admin ?>' == '1' || (baloesGraphs['card-totalLeadsGerados'] != null && baloesGraphs['card-totalLeadsGerados'].exibir == '1')" ng-click="exibeBalao('card-totalLeadsGerados');" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <a href="{{appurl + 'leads'}}">
                                    <md-card-title>
                                        <md-card-title-text>
                                            <img style="width: 50px; margin: 0 auto;" id="totalLeadsGerados" ng-show="report.totalLeadsGerados === ''" style="display: none;" src="{{appurl + 'assets/img/loading.gif'}}">
                                            <span class="md-headline"><strong ng-bind="report.totalLeadsGerados"></strong></span>
                                            <span class="md-subhead">Total de Leads gerados</span>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>

                            <md-card flex-xs flex-gt-xs="25" layout="column" class="text-center card1" style="position: relative;">
                                <img ng-show="'<?= session()->super_admin ?>' == '1' || (baloesGraphs['card-totalLeadsAtivos'] != null && baloesGraphs['card-totalLeadsAtivos'].exibir == '1')" ng-click="exibeBalao('card-totalLeadsAtivos');" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <a href="{{appurl + 'leads'}}">
                                    <md-card-title>
                                        <md-card-title-text>
                                            <img style="width: 50px; margin: 0 auto;" id="totalLeadsAtivos" ng-show="report.totalLeadsAtivos === ''" style="display: none;" src="{{appurl + 'assets/img/loading.gif'}}">
                                            <span class="md-headline"><strong ng-bind="report.totalLeadsAtivos"></strong></span>
                                            <span class="md-subhead">Total de Leads Ativos</span>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>

                            <md-card flex-xs flex-gt-xs="25" layout="column" class="text-center card5" style="position: relative;">
                                <img ng-show="'<?= session()->super_admin ?>' == '1' || (baloesGraphs['card-totalLeadsPipeline'] != null && baloesGraphs['card-totalLeadsPipeline'].exibir == '1')" ng-click="exibeBalao('card-totalLeadsPipeline')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <a href="{{appurl + 'leads'}}">
                                    <md-card-title>
                                        <md-card-title-text>
                                            <img style="width: 50px; margin: 0 auto;" id="totalLeadsPipeline" ng-show="report.totalLeadsPipeline === ''" style="display: none;" src="{{appurl + 'assets/img/loading.gif'}}">
                                            <span class="md-headline"><strong ng-bind="report.totalLeadsPipeline"></strong></span>
                                            <span class="md-subhead">Total de Leads em Pipeline</span>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>

                            <md-card flex-xs flex-gt-xs="25" layout="column" class="text-center card2" style="position: relative;">
                                <img ng-show="'<?= session()->super_admin ?>' == '1' || (baloesGraphs['card-totalLeadsEmDia'] != null && baloesGraphs['card-totalLeadsEmDia'].exibir == '1')" ng-click="exibeBalao('card-totalLeadsEmDia')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <a href="{{appurl + 'leads?flt_vencidos=1'}}">
                                    <md-card-title>
                                        <md-card-title-text>
                                            <img style="width: 50px; margin: 0 auto;" id="totalLeadsEmDia" ng-show="report.totalLeadsEmDia === ''" style="display: none;" src="{{appurl + 'assets/img/loading.gif'}}">
                                            <span class="md-headline"><strong ng-bind="report.totalLeadsEmDia"></strong></span>
                                            <span class="md-subhead">Total de Leads em dia</span>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>

                            <md-card flex-xs flex-gt-xs="25" layout="column" class="text-center card4" style="position: relative;">
                                <img ng-show="'<?= session()->super_admin ?>' == '1' || (baloesGraphs['card-closeTime'] != null && baloesGraphs['card-closeTime'].exibir == '1')" ng-click="exibeBalao('card-closeTime')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <a href="{{appurl + 'leads?flt_vencidos=1'}}">
                                    <md-card-title>
                                        <md-card-title-text>
                                            <img style="width: 50px; margin: 0 auto;" id="closeTime" ng-show="report.closeTime === ''" style="display: none;" src="{{appurl + 'assets/img/loading.gif'}}">
                                            <span class="md-headline"><strong ng-bind="report.closeTime"></strong></span>
                                            <span class="md-subhead">Closer Time (dias)</span>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>


                            <!--
                            <md-card flex-xs flex-gt-xs="25" layout="column" class="text-center card3" style="position: relative;">
                                <img ng-show="'<?= session()->super_admin ?>' == '1' || (baloesGraphs['card-totalLeadsAtrasados'] != null && baloesGraphs['card-totalLeadsAtrasados'].exibir == '1')" ng-click="exibeBalao('card-totalLeadsAtrasados')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <a href="{{appurl + 'leads?flt_vencidos=2'}}">
                                    <md-card-title>
                                        <md-card-title-text>
                                            <img style="width: 50px; margin: 0 auto;" id="totalLeadsAtrasados" ng-show="report.totalLeadsAtrasados === ''" style="display: none;" src="{{appurl + 'assets/img/loading.gif'}}">
                                            <span class="md-headline"><strong ng-bind="report.totalLeadsAtrasados"></strong></span>
                                            <span class="md-subhead">Total de Leads atrasados</span>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>
                            
                            <md-card flex-xs flex-gt-xs="25" layout="column" class="text-center card4" style="position: relative;">
                                <img ng-show="'<?= session()->super_admin ?>' == '1' || (baloesGraphs['card-totalAtividades'] != null && baloesGraphs['card-totalAtividades'].exibir == '1')" ng-click="exibeBalao('card-totalAtividades')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <a href="{{appurl + 'leads'}}">
                                    <md-card-title>
                                        <md-card-title-text>
                                            <img style="width: 50px; margin: 0 auto;" id="totalAtividades" ng-show="report.totalAtividades === ''" style="display: none;" src="{{appurl + 'assets/img/loading.gif'}}">
                                            <span class="md-headline"><strong ng-bind="report.totalAtividades"></strong></span>
                                            <span class="md-subhead">Total de atividades </span>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>
                            -->
                        </div>
                    </md-card>
                    <!--
                    <md-card flex-xs flex-gt-xs="100" layout="column">
                        <div layout-xs="column" layout="row" class="bg-white">
                            <md-card flex-xs flex-gt-xs="25" layout="column" class="text-center card6" style="position: relative;">
                                <img ng-show="'<?= session()->super_admin ?>' == '1' || (baloesGraphs['card-reunioes_agendadas'] != null && baloesGraphs['card-reunioes_agendadas'].exibir == '1')" ng-click="exibeBalao('card-reunioes_agendadas');" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <a href="{{appurl + 'leads'}}">
                                    <md-card-title>
                                        <md-card-title-text>
                                            <img style="width: 50px; margin: 0 auto;" id="reunioes_agendadas" ng-show="report.reunioes_agendadas === ''" style="display: none;" src="{{appurl + 'assets/img/loading.gif'}}">
                                            <span class="md-headline"><strong ng-bind="report.reunioes_agendadas"></strong></span>
                                            <span class="md-subhead">Reuniões agendadas</span>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>

                            <md-card flex-xs flex-gt-xs="25" layout="column" class="text-center card1" style="position: relative;">
                                <img ng-show="'<?= session()->super_admin ?>' == '1' || (baloesGraphs['card-reunioes_realizadas'] != null && baloesGraphs['card-reunioes_realizadas'].exibir == '1')" ng-click="exibeBalao('card-reunioes_realizadas');" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <a href="{{appurl + 'leads'}}">
                                    <md-card-title>
                                        <md-card-title-text>
                                            <img style="width: 50px; margin: 0 auto;" id="reunioes_realizadas" ng-show="report.reunioes_realizadas === ''" style="display: none;" src="{{appurl + 'assets/img/loading.gif'}}">
                                            <span class="md-headline"><strong ng-bind="report.reunioes_realizadas"></strong></span>
                                            <span class="md-subhead">Reuniões realizadas</span>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>

                            <md-card flex-xs flex-gt-xs="25" layout="column" class="text-center card3" style="position: relative;">
                                <img ng-show="'<?= session()->super_admin ?>' == '1' || (baloesGraphs['card-engajamento'] != null && baloesGraphs['card-engajamento'].exibir == '1')" ng-click="exibeBalao('card-engajamento');" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <a href="{{appurl + 'leads'}}">
                                    <md-card-title>
                                        <md-card-title-text>
                                            <img style="width: 50px; margin: 0 auto;" id="engajamento" ng-show="report.engajamento === ''" style="display: none;" src="{{appurl + 'assets/img/loading.gif'}}">
                                            <span class="md-headline"><strong ng-bind="report.engajamento"></strong></span>
                                            <span class="md-subhead">Engajamento</span>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>

                            <md-card flex-xs flex-gt-xs="25" layout="column" class="text-center card5" style="position: relative;">
                                <img ng-show="'<?= session()->super_admin ?>' == '1' || (baloesGraphs['card-pontualidade'] != null && baloesGraphs['card-pontualidade'].exibir == '1')" ng-click="exibeBalao('card-pontualidade');" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                <a href="{{appurl + 'leads'}}">
                                    <md-card-title>
                                        <md-card-title-text>
                                            <img style="width: 50px; margin: 0 auto;" id="pontualidade" ng-show="report.pontualidade === ''" style="display: none;" src="{{appurl + 'assets/img/loading.gif'}}">
                                            <span class="md-headline"><strong ng-bind="report.pontualidade"></strong></span>
                                            <span class="md-subhead">Pontualidade</span>
                                        </md-card-title-text>
                                    </md-card-title>
                                </a>
                            </md-card>
                        </div>
                    </md-card>

                    -->

                    <?php
                    if (session()->super_admin == "1") {
                    ?>
                        <div layout-xs="column" layout="row" class="" id="divgraphNvOportPorClientes">
                            <div flex-xs flex-gt-xs="100" layout="column">
                                <md-card>
                                    <div class="widget-chart-container">
                                        <div class="widget-counter-group widget-counter-group-right">
                                            <div style="width: 100%;" class="pull-left">
                                                <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                <div class="titleDash">
                                                    <h4 style="padding: 0px;margin: 0px;"><b>Novas oportunidades por cliente</b>
                                                        <img ng-show="'<?= session()->super_admin ?>' == '1' || (baloesGraphs['graphNvOportPorClientes'] != null && baloesGraphs['graphNvOportPorClientes'].exibir == '1')" ng-click="exibeBalao('graphNvOportPorClientes')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
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
                        <div flex-xs flex-gt-xs="100" layout="column">
                            <md-card>
                                <div class="widget-chart-container">
                                    <div class="widget-counter-group widget-counter-group-right">
                                        <div style="width: 100%;" class="pull-left">
                                            <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                            <div class="titleDash">
                                                <h4 style="padding: 0px;margin: 0px;"><b>Novas oportunidades por mês</b>
                                                    <img ng-show="'<?= session()->super_admin ?>' == '1' || (baloesGraphs['graphNvOportMes'] != null && baloesGraphs['graphNvOportMes'].exibir == '1')" ng-click="exibeBalao('graphNvOportMes')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="my-2" style="padding-bottom: 60px;">
                                        <div class="chart-wrapper" style="height:300px">
                                            <canvas height="300" style="padding-top: 25px;" id="graphNvOportMes"></canvas>
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
                                                <h4 style="padding: 0px;margin: 0px;"><b>Novas oportunidades diárias</b>
                                                    <img ng-show="'<?= session()->super_admin ?>' == '1' || (baloesGraphs['graphNvOport'] != null && baloesGraphs['graphNvOport'].exibir == '1')" ng-click="exibeBalao('graphNvOport')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">

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

                    <div layout-xs="column" layout="row" class="">
                        <div flex-xs flex-gt-xs="100" layout="column">
                            <md-card>
                                <div class="widget-chart-container">
                                    <div class="widget-counter-group widget-counter-group-right">
                                        <div style="width: 100%;" class="pull-left">
                                            <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                            <div class="titleDash">
                                                <h4 style="padding: 0px;margin: 0px;"><b>Origem</b>
                                                    <img ng-show="'<?= session()->super_admin ?>' == '1' || (baloesGraphs['graphOrigem'] != null && baloesGraphs['graphOrigem'].exibir == '1')" ng-click="exibeBalao('graphOrigem')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                </h4>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="my-2" style="padding-bottom: 60px;">
                                        <div class="chart-wrapper" style="height:300px">
                                            <canvas height="300" style="padding-top: 25px;" id="graphOrigem"></canvas>
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
                                                <h4 style="padding: 0px;margin: 0px;"><b>Estados</b>
                                                    <img ng-show="'<?= session()->super_admin ?>' == '1' || (baloesGraphs['graphEstados'] != null && baloesGraphs['graphEstados'].exibir == '1')" ng-click="exibeBalao('graphEstados')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">

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
                        <div flex-xs flex-gt-xs="50" layout="column">
                            <md-card>
                                <div class="widget-chart-container">
                                    <div class="widget-counter-group widget-counter-group-right">
                                        <div style="width: 100%;" class="pull-left">
                                            <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                            <div class="titleDash">
                                                <h4 style="padding: 0px;margin: 0px;"><b>Setor de Atividades</b>
                                                    <img ng-show="'<?= session()->super_admin ?>' == '1' || (baloesGraphs['graphSetorDeAtividades'] != null && baloesGraphs['graphSetorDeAtividades'].exibir == '1')" ng-click="exibeBalao('graphSetorDeAtividades')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
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


                    <div layout-xs="column" layout="row" class="">
                        <!--
                        <div flex-xs flex-gt-xs="50" layout="column">
                            <md-card>
                                <div class="widget-counter-group widget-counter-group-right">
                                    <div style="width: 100%;" class="pull-left">
                                        <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                        <div class="titleDash">
                                            <h4 style="padding: 0px;margin: 0px;"><b>Atividades</b>
                                                <img ng-show="'<?= session()->super_admin ?>' == '1' || (baloesGraphs['graphAtividades'] != null && baloesGraphs['graphAtividades'].exibir == '1')" ng-click="exibeBalao('graphAtividades')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">

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
                        -->

                        <div flex-xs flex-gt-xs="50" layout="column">
                            <md-card>
                                <div class="widget-chart-container">
                                    <div class="widget-counter-group widget-counter-group-right">
                                        <div style="width: 100%;" class="pull-left">
                                            <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                            <div class="titleDash">
                                                <h4 style="padding: 0px;margin: 0px;"><b>Qualificações</b>
                                                    <img ng-show="'<?= session()->super_admin ?>' == '1' || (baloesGraphs['graphQualificacoes'] != null && baloesGraphs['graphQualificacoes'].exibir == '1')" ng-click="exibeBalao('graphQualificacoes')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                </h4>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="my-2" style="padding-bottom: 60px;">
                                        <div class="chart-wrapper" style="height:300px">
                                            <canvas height="300" style="padding-top: 25px;" id="graphQualificacoes"></canvas>
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
                                                <h4 style="padding: 0px;margin: 0px;"><b>Quantidade de Lead por funil</b>
                                                    <img ng-show="'<?= session()->super_admin ?>' == '1' || (baloesGraphs['graphLeadsPorFunil'] != null && baloesGraphs['graphLeadsPorFunil'].exibir == '1')" ng-click="exibeBalao('graphLeadsPorFunil')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                </h4>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="my-2" style="padding-bottom: 60px;">
                                        <div class="chart-wrapper" style="height:300px">
                                            <canvas height="300" style="padding-top: 25px;" id="graphLeadsPorFunil"></canvas>
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
                    if (session()->super_admin == "1") {
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
                if (session()->super_admin == "1") {
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
    var is_admin = '<?= $user_data['super_admin'] ?>';
</script>

<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>
<script src="<?php echo base_url('assets/lib/chartjs/dist/Chart.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/lib/highcharts/highcharts.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/DashLead.js?v=1.2.9') ?>"></script>