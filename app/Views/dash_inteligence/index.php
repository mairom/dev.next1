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
<div class="ciuis-body-content" ng-controller="Dash_inteligence">
    <main class="main-content border-radius-lg ">
        <div class="container-fluid py-4">
            <div class='filtrosLeads' style="padding-bottom: 20px !important;">
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
                    <md-select multiple placeholder="<?php echo lang2('source'); ?>" id="flt_origem" ng-model="filtros.flt_origem" style="min-width: 200px;" ng-change="getResultados()">

                        <md-option ng-value="source.id" ng-repeat="source in leadssources">{{source.name}}</md-option>
                    </md-select>
                </md-input-container>

                <md-button ng-click="openModalCustom()" class="md-raised md-primary btn-report" aria-label="Close" style="margin-right: 6%;background-color: #ffbc00;border-radius: 10px;">
                    Customize seu painel
                </md-button>
                <!--
                <md-button ng-click="getRelatorioChat()" class="md-raised md-primary btn-report" aria-label="Close" style="margin-right: 6%;background-color: #ffbc00;border-radius: 10px;">
                    Relatório de operação
                </md-button>
                -->
            </div>

            <div class="row">

                <div ng-show="customPanel.totalLeadsGerados" class="col-sm-6 col-lg mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Leads Gerados</p>
                                <h4 class="mb-0">{{totalDash.totalLeadsGerados}}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0"><span class="text-success text-sm font-weight-bolder"> </span></p>
                        </div>
                    </div>
                </div>
                <div ng-show="customPanel.totalReunioes" class="col-sm-6 col-lg mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Reuniões Realizadas</p>
                                <h4 class="mb-0">{{totalDash.totalReunioes}}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0"><span class="text-success text-sm font-weight-bolder"></span></p>
                        </div>
                    </div>
                </div>
                <div ng-show="customPanel.totalClientes" class="col-sm-6 col-lg">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-secondary shadow-secondary text-center border-radius-xl mt-n4 position-absolute">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Total de conversão</p>
                                <h4 ng-show="totalDash.totalClientes != null" class="mb-0">{{ totalDash.totalClientes }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0"><span class="text-success text-sm font-weight-bolder"></span></p>
                        </div>
                    </div>
                </div>
                <div ng-show="customPanel.totalReversao" class="col-sm-6 col-lg mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                <i class="far fa-share-square"></i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Taxa de reversão</p>
                                <h4 ng-show="totalDash.totalReversao != null" class="mb-0">{{ totalDash.totalReversao.toFixed(1) }} %</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0"><span class="text-danger text-sm font-weight-bolder"></span></p>
                        </div>
                    </div>
                </div>
                <div ng-show="customPanel.totalConversao" class="col-sm-6 col-lg">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                                <i class="far fa-star"></i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Taxa de conversão</p>
                                <h4 ng-show="totalDash.totalConversao != null" class="mb-0">{{ totalDash.totalConversao.toFixed(1) }} %</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0"><span class="text-success text-sm font-weight-bolder"></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <h3 class="title-dash">Perfomance em quantidades</h3>

                <div ng-show="customPanel.graphNvOportMes" class="col-lg-6 col-md-6 mt-4 mb-4">
                    <div class="card z-index-2 ">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                                <div class="chart chat-responsive">
                                    <canvas id="graphNvOportMes" class="chart-canvas"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-0 ">Leads Gerados</h4>

                        </div>
                    </div>
                </div>
                <div ng-show="customPanel.perfReuniaoMensal" class="col-lg-6 col-md-6 mt-4 mb-4">
                    <div class="card z-index-2  ">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1">
                                <div class="chart chat-responsive">
                                    <canvas id="perfReuniaoMensal" class="chart-canvas"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-0 ">Quantidade de reuniões</h4>
                        </div>
                    </div>
                </div>
                <div ng-show="customPanel.graphNovosClientes" class="col-lg-6 col-md-6 mt-4 mb-3">
                    <div class="card z-index-2 ">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                                <div class="chart chat-responsive">
                                    <canvas id="graphNovosClientes" class="chart-canvas"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-0 ">Quantidade de conversões</h4>
                        </div>
                    </div>
                </div>

                <div ng-show="customPanel.reuniaoAgendadaxRealizada" class="col-lg-6 col-md-6 mt-4 mb-3">
                    <div class="card z-index-2 ">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                                <div class="chart chat-responsive">
                                    <canvas id="reuniaoAgendadaxRealizada" class="chart-canvas"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-0 ">Reunião agendada x reunião realizada</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <h3 class="title-dash">Performance em percentuais</h3>

                <div ng-show="customPanel.leadsxReunioes" class="col-lg-4 mt-4 mb-3">
                    <div class="card z-index-2 ">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                                <div class="chart chat-responsive">
                                    <canvas id="leadsxReunioes" class="chart-canvas"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-0 ">Percentual de Reversão- Leads em Reuniões</h4>
                        </div>
                    </div>
                </div>

                <div ng-show="customPanel.reunioesxClientes" class="col-lg-4 mt-4 mb-3">
                    <div class="card z-index-2 ">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                                <div class="chart chat-responsive">
                                    <canvas id="reunioesxClientes" class="chart-canvas"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-0 ">Percentual de Conversão- Reuniões em Clientes</h4>
                        </div>
                    </div>
                </div>

                <div ng-show="customPanel.percetReuniaorealizadas" class="col-lg-4 mt-4 mb-3">
                    <div class="card z-index-2 ">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                                <div class="chart chat-responsive">
                                    <canvas id="percetReuniaorealizadas" class="chart-canvas"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-0 ">Percentual de reuniões realizadas</h4>
                        </div>
                    </div>
                </div>

            </div>
            <!--
            <div class="row mt-4">
                <h3 class="title-dash">Perfomance em reuniões</h3>
                <div ng-show="customPanel.graphReunioes" class="col-lg-6 col-md-6 mt-4 mb-4">
                    <div class="card z-index-2 ">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                                <div class="chart chat-responsive">
                                    <canvas id="graphReunioes" class="chart-canvas"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-0 ">Reuniões</h4>
                        </div>
                    </div>
                </div>
            </div>
-->

            <div class="row mt-4">
                <h3 class="title-dash">Performance de Canais</h3>

                <div ng-show="customPanel.LeasxReunioesPorOrigem" class="col-lg-6 mt-4 mb-3">
                    <div class="card z-index-2 ">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                                <div class="chart chat-responsive">
                                    <canvas id="LeasxReunioesPorOrigem" class="chart-canvas"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-0 ">Percentual de Reversão- Leads em Reuniões</h4>
                        </div>
                    </div>
                </div>

                <div ng-show="customPanel.ClientesxReunioesPorOrigem" class="col-lg-6 mt-4 mb-3">
                    <div class="card z-index-2 ">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                                <div class="chart chat-responsive">
                                    <canvas id="ClientesxReunioesPorOrigem" class="chart-canvas"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-0 ">Percentual de Conversão - Reuniões em Clientes</h4>
                        </div>
                    </div>
                </div>

            </div>


            <div class="row mt-4">
                <h3 class="title-dash">Performance Funil</h3>
                <div ng-show="customPanel.graphQuantidadeLeads" class="col-lg-4 col-md-4 mt-4 mb-4">
                    <div class="card z-index-2 ">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                                <div class="chart chat-responsive">
                                    <canvas id="graphQuantidadeLeads" class="chart-canvas"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-0 ">Quantidade de leads</h4>
                        </div>
                    </div>
                </div>

                <div ng-show="customPanel.graphReunioesRealizadasFunil" class="col-lg-4 col-md-4 mt-4 mb-4">
                    <div class="card z-index-2 ">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                                <div class="chart chat-responsive">
                                    <canvas id="graphReunioesRealizadasFunil" class="chart-canvas"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-0 ">Reuniões realizadas</h4>
                        </div>
                    </div>
                </div>

                <div ng-show="customPanel.graphConversoes" class="col-lg-4 col-md-4 mt-4 mb-4">
                    <div class="card z-index-2 ">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                                <div class="chart chat-responsive">
                                    <canvas id="graphConversoes" class="chart-canvas"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-0 ">Conversões</h4>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row mt-4">
                <h3 class="title-dash">Performance SDR</h3>

                <div ng-show="customPanel.reunioesAgendadas" class="col-lg-6 mt-4 mb-3">
                    <div class="card z-index-2 ">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                                <div class="chart chat-responsive">
                                    <canvas id="reunioesAgendadas" class="chart-canvas"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-0 ">Reuniões agendadas</h4>
                        </div>
                    </div>
                </div>

                <div ng-show="customPanel.reunioesPorFuncionarios" class="col-lg-6 mt-4 mb-3">
                    <div class="card z-index-2 ">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                                <div class="chart chat-responsive">
                                    <canvas id="reunioesPorFuncionarios" class="chart-canvas"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-0 ">Quantidade de reuniões realizadas</h4>
                        </div>
                    </div>
                </div>

                <div ng-show="customPanel.graphLeadsAcionados" class="col-lg-6 mt-4 mb-4">
                    <div class="card z-index-2 ">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-dark shadow-primary border-radius-lg py-3 pe-1">
                                <div class="chart chat-responsive">
                                    <canvas id="graphLeadsAcionados" class="chart-canvas"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-0 ">Leads acionados x reuniões realizadas - por funcionário</h4>
                        </div>
                    </div>
                </div>

                <div ng-show="customPanel.ConversoesPorFuncionario" class="col-lg-6 mt-4 mb-3">
                    <div class="card z-index-2 ">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                                <div class="chart chat-responsive">
                                    <canvas id="ConversoesPorFuncionario" class="chart-canvas"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-0 ">Quantidade de conversões</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <h3 class="title-dash">Performance Closer</h3>
                <div ng-show="customPanel.graphReunioesRealizadas" class="col-lg-4 col-md-4 mt-4 mb-4">
                    <div class="card z-index-2 ">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                                <div class="chart chat-responsive">
                                    <canvas id="graphReunioesRealizadas" class="chart-canvas"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-0 ">Reuniões realizadas</h4>
                        </div>
                    </div>
                </div>

                <div ng-show="customPanel.reunioesxConversoes" class="col-lg-4 col-md-4 mt-4 mb-3">
                    <div class="card z-index-2 ">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                                <div class="chart chat-responsive">
                                    <canvas id="reunioesxConversoes" class="chart-canvas"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-0 ">Reuniões realizadas x conversão para cliente </h4>
                        </div>
                    </div>
                </div>

                <div ng-show="customPanel.ConversoesPorCloser" class="col-lg-4 col-md-4 mt-4 mb-3">
                    <div class="card z-index-2 ">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                                <div class="chart chat-responsive">
                                    <canvas id="ConversoesPorCloser" class="chart-canvas"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-0 ">Conversão para cliente </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>





    <div class="modal fade" id="modalCustom" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloGraphBalao">Customizar painel</h5>
                    <button type="button" class="close closeModal" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-6">
                        <h3>Gráficos</h3>
                    </div>
                    <div class="col-md-6">
                        <h3>Boxes</h3>
                    </div>
                    <hr>

                    <div class="col-md-6 dv-modal-c">

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.graphLeadsAcionados" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="box_panel10">
                            <label class="form-check-label" for="box_panel10">Leads acionados</label>
                        </div>

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.graphReunioesRealizadas" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="box_panel11">
                            <label class="form-check-label" for="box_panel11">Reuniões realizadas</label>
                        </div>

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.reunioesxConversoes" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="box_panel12">
                            <label class="form-check-label" for="box_panel12">Reuniões realizadas x conversão para cliente</label>
                        </div>

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.ConversoesPorCloser" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="box_panel12">
                            <label class="form-check-label" for="box_panel12">Conversão para cliente</label>
                        </div>

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.reuniaoAgendadaxRealizada" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="box_panel13">
                            <label class="form-check-label" for="box_panel13">Reunião agendada x reunião realizada</label>
                        </div>

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.percetReuniaorealizadas" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="box_panel13">
                            <label class="form-check-label" for="box_panel13">Percentual de reuniões realizadas</label>
                        </div>

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.graphNvOportMes" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="box_panel1">
                            <label class="form-check-label" for="box_panel1">Leads Gerados mensal</label>
                        </div>

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.perfReuniaoMensal" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="box_panel2">
                            <label class="form-check-label" for="box_panel2">Performance de Reuniões</label>
                        </div>

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.graphNovosClientes" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="box_panel3">
                            <label class="form-check-label" for="box_panel3">Performance de Conversão Geral</label>
                        </div>

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.leadsxReunioes" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="box_panel4">
                            <label class="form-check-label" for="box_panel4">Performance de Reversão - Leads x Reuniões</label>
                        </div>

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.reunioesxClientes" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="box_panel5">
                            <label class="form-check-label" for="box_panel5">Performance de Conversão - Reunião x Clientes</label>
                        </div>

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.LeasxReunioesPorOrigem" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="box_panel6">
                            <label class="form-check-label" for="box_panel6">Performance de canais - Reversão Leads/Reuniões</label>
                        </div>

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.ClientesxReunioesPorOrigem" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="box_panel7">
                            <label class="form-check-label" for="box_panel7">Performance de Canais - Conversão Reuniões/Clientes</label>
                        </div>

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.ConversoesPorFuncionario" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="box_panel8">
                            <label class="form-check-label" for="box_panel8">Performance SDR Conversão</label>
                        </div>

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.reunioesPorFuncionarios" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="box_panel9">
                            <label class="form-check-label" for="box_panel9">Performance SDR Reversão</label>
                        </div>

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.reunioesAgendadas" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="box_panel14">
                            <label class="form-check-label" for="box_panel14">Reuniões agendadas</label>
                        </div>

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.graphQuantidadeLeads" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="box_panel15">
                            <label class="form-check-label" for="box_panel15">Quantidade de leads</label>
                        </div>

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.graphReunioesRealizadasFunil" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="box_panel16">
                            <label class="form-check-label" for="box_panel16">Reuniões realizadas</label>
                        </div>

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.graphConversoes" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="box_panel17">
                            <label class="form-check-label" for="box_panel17">Conversões</label>
                        </div>

                    </div>
                    <div class="col-md-6 dv-modal-c">

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.totalLeadsGerados" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="graph_panel2">
                            <label class="form-check-label" for="graph_panel2">Leads Gerados</label>
                        </div>

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.totalReunioes" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="graph_panel3">
                            <label class="form-check-label" for="graph_panel3">Reuniões Realizadas</label>
                        </div>

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.totalReversao" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="graph_panel4">
                            <label class="form-check-label" for="graph_panel4">Taxa de reversão</label>
                        </div>

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.totalConversao" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="graph_panel5">
                            <label class="form-check-label" for="graph_panel5">Taxa de conversão</label>
                        </div>

                        <div class="form-group form-check from-grahps">
                            <input ng-model="customPanel.totalClientes" ng-change="changeSettings()" type="checkbox" class="form-check-input" id="graph_panel6">
                            <label class="form-check-label" for="graph_panel6">Total de conversão</label>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

<script type="text/javascript">
    var lang = {};
    lang.payments = '<?= lang2('payments') ?>';
    lang.expenses = '<?= lang2('expenses') ?>';
    var send_email = '<?= $send_email ?>';
    CONSTdt_ate = null;
    CONST_idCompany = null;
    CONST_dt_ano = null;


    var options2 = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false,
            }
        },
        interaction: {
            intersect: false,
            mode: 'index',
        },
        scales: {
            y: {
                grid: {
                    drawBorder: false,
                    display: true,
                    drawOnChartArea: true,
                    drawTicks: false,
                    borderDash: [5, 5],
                    color: 'rgba(255, 255, 255, .2)'
                },
                ticks: {
                    suggestedMin: 0,
                    suggestedMax: 500,
                    beginAtZero: true,
                    padding: 10,
                    font: {
                        size: 14,
                        weight: 300,
                        family: "Roboto",
                        style: 'normal',
                        lineHeight: 2
                    },
                    color: "#fff"
                },
            },
            x: {
                grid: {
                    drawBorder: false,
                    display: true,
                    drawOnChartArea: true,
                    drawTicks: false,
                    borderDash: [5, 5],
                    color: 'rgba(255, 255, 255, .2)'
                },
                ticks: {
                    display: true,
                    color: '#f8f9fa',
                    padding: 10,
                    font: {
                        size: 14,
                        weight: 300,
                        family: "Roboto",
                        style: 'normal',
                        lineHeight: 2
                    },
                }
            },
        },
    };

    var options3 = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false,
            }
        },
        interaction: {
            intersect: false,
            mode: 'index',
        },
        scales: {
            y: {
                grid: {
                    drawBorder: false,
                    display: true,
                    drawOnChartArea: true,
                    drawTicks: false,
                    borderDash: [5, 5],
                    color: 'rgba(255, 255, 255, .2)'
                },
                ticks: {
                    display: true,
                    color: '#f8f9fa',
                    padding: 10,
                    font: {
                        size: 14,
                        weight: 300,
                        family: "Roboto",
                        style: 'normal',
                        lineHeight: 2
                    },
                }
            },
            x: {
                grid: {
                    drawBorder: false,
                    display: false,
                    drawOnChartArea: false,
                    drawTicks: false,
                    borderDash: [5, 5]
                },
                ticks: {
                    display: true,
                    color: '#f8f9fa',
                    padding: 10,
                    font: {
                        size: 14,
                        weight: 300,
                        family: "Roboto",
                        style: 'normal',
                        lineHeight: 2
                    },
                }
            },
        },
    };

    var options4 = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false,
            }
        },
        interaction: {
            intersect: false,
            mode: 'index',
        },
        scales: {
            y: {
                grid: {
                    drawBorder: false,
                    display: true,
                    drawOnChartArea: true,
                    drawTicks: false,
                    borderDash: [5, 5],
                    color: 'rgba(255, 255, 255, .2)'
                },
                ticks: {
                    display: true,
                    padding: 10,
                    color: '#f8f9fa',
                    font: {
                        size: 14,
                        weight: 300,
                        family: "Roboto",
                        style: 'normal',
                        lineHeight: 2
                    },
                }
            },
            x: {
                grid: {
                    drawBorder: false,
                    display: false,
                    drawOnChartArea: false,
                    drawTicks: false,
                    borderDash: [5, 5]
                },
                ticks: {
                    display: true,
                    color: '#f8f9fa',
                    padding: 10,
                    font: {
                        size: 14,
                        weight: 300,
                        family: "Roboto",
                        style: 'normal',
                        lineHeight: 2
                    },
                }
            },
        },
    };
</script>

<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>
<script src="<?= base_url('assets/lib/material-dashboard/chartjs.js'); ?>" type="text/javascript"></script>
<script src="<?= base_url('assets/lib/highcharts/highcharts.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/dash_inteligence.js?v=1.2.71') ?>"></script>
<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc 
<script src="<?= base_url('assets/lib/material-dashboard/material-dashboard.min.js'); ?>"></script>-->
<script>
    $(document).ready(function() {


    });
</script>