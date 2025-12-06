<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<?php $appconfig = get_appconfig(); ?>
<div class="ciuis-body-content" ng-controller="DashRh_Controller">
    <md-content class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">
        <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">
                <md-button class="md-icon-button" aria-label="Settings" ng-disabled="true">
                    <img class="img-icon-toolbar" src="{{appurl + 'assets/img/pie2.png'}}">
                </md-button>
                <h2 flex md-truncate>Dashboard Rh</h2>
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
                                        <md-option ng-value="-1">Todos</md-option>
                                        <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
                                    </md-select>
                                </md-input-container>

                                <md-button ng-click="baixarPdf()" style="width: 100px;height: 65px;float: right;color: #858585;cursor: pointer;" class="md-icon-button" aria-label="Settings">
                                    <md-icon><i style="color: #8e44ad;" class="far fa-file-pdf"></i></md-icon><span>Gerar PDF</span>
                                </md-button>


                            </div>
                        </md-content>
                        <md-content class="widget-fullwidth ciuis-body-loading" id="contentMain" style = "overflow: hidden;">
                            <md-card flex-xs flex-gt-xs="100" layout="column">
                                <div layout-xs="column" layout="row" class="bg-white">
                                    <!--
                                    <md-card flex-xs flex-gt-xs="20" layout="column" class="text-center card1" style="position: relative;">
                                        <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['totalTempGeral'] != null && baloesGraphs['totalTempGeral'].exibir == '1')" ng-click="exibeBalao('totalTempGeral')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                        <a href="#">
                                            <md-card-title>
                                                <md-card-title-text>
                                                    <span class="md-headline"><strong ng-bind="report.totalTempGeral"></strong></span>
                                                    <span class="md-subhead">Total de tempo geral</span>
                                                </md-card-title-text>
                                            </md-card-title>
                                        </a>
                                    </md-card>

                                    <md-card flex-xs flex-gt-xs="20" layout="column" class="text-center card3" style="position: relative;">
                                        <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['totalTempoAtivoSistem'] != null && baloesGraphs['totalTempoAtivoSistem'].exibir == '1')" ng-click="exibeBalao('totalTempoAtivoSistem')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">

                                        <a href="#">
                                            <md-card-title>
                                                <md-card-title-text>
                                                    <span class="md-headline"><strong ng-bind="report.totalTempoAtivoSistem"></strong></span>
                                                    <span class="md-subhead">Total de tempo ativo no sistema</span>
                                                </md-card-title-text>
                                            </md-card-title>
                                        </a>
                                    </md-card>

                                    <md-card flex-xs flex-gt-xs="20" layout="column" class="text-center card4" style="position: relative;">
                                        <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['mediaDiariaTotal'] != null && baloesGraphs['mediaDiariaTotal'].exibir == '1')" ng-click="exibeBalao('mediaDiariaTotal')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">

                                        <a href="#">
                                            <md-card-title>
                                                <md-card-title-text>
                                                    <span class="md-headline"><strong ng-bind="report.mediaDiariaTotal"></strong></span>
                                                    <span class="md-subhead">Média diária de tempo total no sistema</span>
                                                </md-card-title-text>
                                            </md-card-title>
                                        </a>
                                    </md-card>
                                    -->

                                    <md-card flex-xs flex-gt-xs="20" layout="column" class="text-center card2" style="position: relative;">
                                        <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['totalDeAtividade'] != null && baloesGraphs['totalDeAtividade'].exibir == '1')" ng-click="exibeBalao('totalDeAtividade')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">

                                        <a href="#">
                                            <md-card-title>
                                                <md-card-title-text>
                                                    <img style="width: 50px; margin: 0 auto;" ng-show="report.totalDeAtividade === ''" src="{{appurl + 'assets/img/loading.gif'}}">
                                                    <span class="md-headline"><strong ng-bind="report.totalDeAtividade"></strong></span>
                                                    <span class="md-subhead">Total de atividades</span>
                                                </md-card-title-text>
                                            </md-card-title>
                                        </a>
                                    </md-card>

                                    <md-card flex-xs flex-gt-xs="20" layout="column" class="text-center card3" style="position: relative;">
                                        <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['mediaDiaria'] != null && baloesGraphs['mediaDiaria'].exibir == '1')" ng-click="exibeBalao('mediaDiaria')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">

                                        <a href="#">
                                            <md-card-title>
                                                <md-card-title-text>
                                                    <img style="width: 50px; margin: 0 auto;" ng-show="report.mediaDiaria === ''" src="{{appurl + 'assets/img/loading.gif'}}">

                                                    <span class="md-headline"><strong ng-bind="report.mediaDiaria"></strong></span>
                                                    <span class="md-subhead">Média diária de atividades</span>
                                                </md-card-title-text>
                                            </md-card-title>
                                        </a>
                                    </md-card>

                                    <md-card flex-xs flex-gt-xs="20" layout="column" class="text-center card4" style="position: relative;">
                                        <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['totalLeads'] != null && baloesGraphs['totalLeads'].exibir == '1')" ng-click="exibeBalao('totalLeads')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">

                                        <a href="#">
                                            <md-card-title>
                                                <md-card-title-text>
                                                    <img style="width: 50px; margin: 0 auto;" ng-show="report.totalLeads === ''" src="{{appurl + 'assets/img/loading.gif'}}">

                                                    <span class="md-headline"><strong ng-bind="report.totalLeads"></strong></span>
                                                    <span class="md-subhead">Total de leads acionados</span>
                                                </md-card-title-text>
                                            </md-card-title>
                                        </a>
                                    </md-card>

                                    <md-card flex-xs flex-gt-xs="20" layout="column" class="text-center card5" style="position: relative;">
                                        <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['totalClientes'] != null && baloesGraphs['totalClientes'].exibir == '1')" ng-click="exibeBalao('totalClientes')" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">

                                        <a href="#">
                                            <md-card-title>
                                                <md-card-title-text>
                                                    <img style="width: 50px; margin: 0 auto;" ng-show="report.totalClientes === ''" src="{{appurl + 'assets/img/loading.gif'}}">

                                                    <span class="md-headline"><strong ng-bind="report.totalClientes"></strong></span>
                                                    <span class="md-subhead">Total de clientes acionados</span>
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
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Lead acionados por funcionário</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['LeadsPorFuncionarios'] != null && baloesGraphs['LeadsPorFuncionarios'].exibir == '1')" ng-click="exibeBalao('LeadsPorFuncionarios')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-2" style="padding-bottom: 60px;">
                                                <div class="chart-wrapper" style="height:362px">
                                                    <div style="padding-top: 100px;" ng-show="LeadsPorFuncionarios == null" layout-align="center center" class="text-center" id="circular_loader">
                                                        <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">
                                                        <p style="font-size: 15px;margin-bottom: 5%;">
                                                            <span>
                                                                <small><strong><?php echo lang2('loading') . ' ' . lang2('gráfico') . '...' ?></strong></small>
                                                            </span>
                                                        </p>
                                                    </div>

                                                    <canvas height="300" style="padding-top: 25px;" id="LeadsPorFuncionarios"></canvas>
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
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Clientes acionados por funcionário</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['ClientesPorFuncionario'] != null && baloesGraphs['ClientesPorFuncionario'].exibir == '1')" ng-click="exibeBalao('ClientesPorFuncionario')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-2" style="padding-bottom: 60px;">
                                                <div class="chart-wrapper" style="height:362px">
                                                    <div style="padding-top: 100px;" ng-show="ClientesPorFuncionario == null" layout-align="center center" class="text-center" id="circular_loader">
                                                        <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">
                                                        <p style="font-size: 15px;margin-bottom: 5%;">
                                                            <span>
                                                                <small><strong><?php echo lang2('loading') . ' ' . lang2('gráfico') . '...' ?></strong></small>
                                                            </span>
                                                        </p>
                                                    </div>

                                                    <canvas height="300" style="padding-top: 25px;" id="ClientesPorFuncionario"></canvas>
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
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Tempo diário por funcionários</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['TmpDiarioPorMes'] != null && baloesGraphs['TmpDiarioPorMes'].exibir == '1')" ng-click="exibeBalao('TmpDiarioPorMes')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-2" style="padding-bottom: 60px;">
                                                <div class="chart-wrapper" style="height:362px">
                                                    <div style="padding-top: 100px;" ng-show="TmpDiarioPorMes == null" layout-align="center center" class="text-center" id="circular_loader">
                                                        <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">
                                                        <p style="font-size: 15px;margin-bottom: 5%;">
                                                            <span>
                                                                <small><strong><?php echo lang2('loading') . ' ' . lang2('gráfico') . '...' ?></strong></small>
                                                            </span>
                                                        </p>
                                                    </div>

                                                    <canvas height="300" style="padding-top: 25px;" id="TmpDiarioPorMes"></canvas>
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
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Atividades diárias funcionários</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['AtvDiariaFunc'] != null && baloesGraphs['AtvDiariaFunc'].exibir == '1')" ng-click="exibeBalao('AtvDiariaFunc')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-2" style="padding-bottom: 60px;">
                                                <div class="chart-wrapper" style="height:362px">

                                                    <div style="padding-top: 100px;" ng-show="AtvDiariaFunc == null" layout-align="center center" class="text-center" id="circular_loader">
                                                        <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">
                                                        <p style="font-size: 15px;margin-bottom: 5%;">
                                                            <span>
                                                                <small><strong><?php echo lang2('loading') . ' ' . lang2('gráfico') . '...' ?></strong></small>
                                                            </span>
                                                        </p>
                                                    </div>

                                                    <canvas height="300" style="padding-top: 25px;" id="AtvDiariaFunc"></canvas>
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
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Média de tempo logado por funcionário</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['mediaTempoLogadoPorFuncionario'] != null && baloesGraphs['mediaTempoLogadoPorFuncionario'].exibir == '1')" ng-click="exibeBalao('mediaTempoLogadoPorFuncionario')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-2" style="padding-bottom: 60px;">
                                                <div class="chart-wrapper" style="height:362px">

                                                    <div style="padding-top: 100px;" ng-show="mediaTempoLogadoPorFuncionario == null" layout-align="center center" class="text-center" id="circular_loader">
                                                        <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">
                                                        <p style="font-size: 15px;margin-bottom: 5%;">
                                                            <span>
                                                                <small><strong><?php echo lang2('loading') . ' ' . lang2('gráfico') . '...' ?></strong></small>
                                                            </span>
                                                        </p>
                                                    </div>

                                                    <canvas height="300" style="padding-top: 25px;width: 100%;" id="mediaTempoLogadoPorFuncionario"></canvas>
                                                    
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
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Média de atividades no sistema por funcionário</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['MediaAtvSistem'] != null && baloesGraphs['MediaAtvSistem'].exibir == '1')" ng-click="exibeBalao('MediaAtvSistem')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-2" style="padding-bottom: 60px;">
                                                <div class="chart-wrapper" style="height:362px">

                                                    <div style="padding-top: 100px;" ng-show="MediaAtvSistem == null" layout-align="center center" class="text-center" id="circular_loader">
                                                        <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">
                                                        <p style="font-size: 15px;margin-bottom: 5%;">
                                                            <span>
                                                                <small><strong><?php echo lang2('loading') . ' ' . lang2('gráfico') . '...' ?></strong></small>
                                                            </span>
                                                        </p>
                                                    </div>

                                                    <canvas height="300" style="padding-top: 25px;" id="MediaAtvSistem"></canvas>
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
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Top 10 atividades</b>
                                                            <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['top10Atvs'] != null && baloesGraphs['top10Atvs'].exibir == '1')" ng-click="exibeBalao('top10Atvs')" class="btn_balao" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                                                        </h4>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="my-2" style="padding-bottom: 60px;">
                                                <div class="chart-wrapper" style="height:300px">


                                                    <div style="padding-top: 100px;" ng-show="top10Atvs == null" layout-align="center center" class="text-center" id="circular_loader">
                                                        <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">
                                                        <p style="font-size: 15px;margin-bottom: 5%;">
                                                            <span>
                                                                <small><strong><?php echo lang2('loading') . ' ' . lang2('gráfico') . '...' ?></strong></small>
                                                            </span>
                                                        </p>
                                                    </div>

                                                    <canvas height="300" style="padding-top: 25px;" id="top10Atvs"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </md-card>
                                </div>
                                -->
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
<!--
<script src="<?php echo base_url('assets/lib/chartjs/dist/Chart.min.js'); ?>" type="text/javascript"></script>
-->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?php echo base_url('assets/lib/highcharts/highcharts.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/dashRh.js?v=1.6.15') ?>"></script>
<script>
    $(document).ready(function() {

    });
</script>