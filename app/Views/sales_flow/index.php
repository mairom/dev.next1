<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<?php $appconfig = get_appconfig(); ?>
<style>

</style>

<div class="ciuis-body-content" ng-controller="Sales_Controller">

    <div compile="template"></div>

    <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">

        <div ng-show="SalesLoader" layout-align="center center" class="text-center" id="circular_loader">
            <!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
            <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">

            <p style="font-size: 15px;margin-bottom: 5%;">
                <span>
                    <?php echo lang2('please_wait') ?> <br>
                    <small><strong><?php echo lang2('loading') . ' ' . lang2('sales flow') . '...' ?></strong></small>
                </span>
            </p>
        </div>

        <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-12 md-p-0 lead-table" ng-show="!SalesLoader">
            <md-toolbar class="toolbar-white toolbar-title" style="margin-left: 4px;" ng-cloak>
                <div class="md-toolbar-tools md-tools">
                    <div class="col-12 col-md-8">
                        <md-button class="md-icon-button" aria-label="Settings" ng-disabled="true">
                            <img style="transition: all 0.1s ease-in-out;width: 100%;text-align: center;margin: 0 auto;margin-top: 6px;" src="{{base_url + '/assets/img/menu/steps.png'}}">
                        </md-button>
                        <h2 flex md-truncate class="text-bold">
                            <?php echo lang2('Sales flow'); ?><br>
                            <small flex md-truncate><?php echo lang2('Organize seu Sales flow'); ?></small>
                        </h2>
                    </div>
                    <div class="col-12 col-md-4" style="display: block;">
                        <button style="float: right;margin-right: 10px;" type="button" class="btn-shadow d-inline-flex align-items-center btn2 btn-success" ng-click="Create()">
                            <i class="fas fa-plus"></i> Novo Flow
                        </button>
                    </div>

                </div>
            </md-toolbar>



            <md-content class="ciuis_lead_kanban_board" ng-cloak>
                <div class='filtrosLeads collapseFiltroOpen' id="collapseFiltro">
                    <!--
                    <md-input-container class="md-block">
                        <i class="iconSearch ion-funnel text-muted "></i>
                        <md-select placeholder="Escolha um funil" ng-model="filtros.flt_funil" style="min-width: 200px;">

                            <md-option ng-value="funil.id_list" ng-repeat="funil in leadslist">{{funil.nm_list}}</md-option>
                        </md-select>
                    </md-input-container>
                    -->

                    <md-input-container class="md-block">
                        <label>Selecione o Flow</label>
                        <md-select placeholder="Escolha um fluxo" ng-model="filtros.fluxo" style="min-width: 200px;">
                            <md-option ng-value="fluxo.id_fluxo" ng-repeat="fluxo in fluxos">{{fluxo.name}}</md-option>
                        </md-select>
                    </md-input-container>
                </div>
                <div>
                    <div class="ctt-fluxo" ng-repeat="fluxo in fluxos" ng-if="fluxo.id_fluxo == filtros.fluxo">
                        <div class="fluxo">
                            <div class="element-pai elementF{{$index}}" style="width: 100%;float: left;min-width: max-content;margin-top: {{getHeight($index)}}px;" ng-if="($index + 1) % 2 !== 0" ng-repeat="etapa in fluxo.etapas">
                                <div style="display: flex; flex-direction: row-reverse;">
                                    <div>
                                        <div class="element" style="float: right;">
                                            <div class="icon-element">

                                                <button type="button" class="btn_edit6" ng-click="CreateRamo(etapa)">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                <button style="right: -10px;" type="button" class="btn_edit6" ng-click="EditRamo(etapa)">
                                                    <i style="font-size: 13px;color: #3d8aac;" class="fas fa-pen"></i>
                                                </button>
                                                <p class="nm_atividade-flow">{{etapa.atividade.nm_atividade_select}}</p>
                                                <div style="background-image: url('{{base_url + etapa.atividade.atv_ft}}');" ng-click="EditRamo(etapa)"></div>

                                            </div>
                                            <div class="element-line">
                                                <span class="inf-num">{{$index + 1}}</span>
                                            </div>
                                            <!--
                                            <p class="desc-element">{{etapa.descricao}}</p>
                                            -->
                                        </div>
                                    </div>
                                </div>
                                <div style="display: flex;">
                                    <div style="display: flex; flex-direction: row-reverse;" class='element_vert' my-directive></div>
                                </div>
                            </div>
                        </div>

                        <div class="fluxo-line">
                            <h3 class="title-fluxo">{{fluxo.name}}</h3>
                            <button type="button" class="btn_edit5" ng-click="Create(fluxo)">
                                <i class="fas fa-pen"></i>
                            </button>

                            <button type="button" class="btn_edit5" ng-click="CreateRamo({id_fluxo:fluxo.id_fluxo, etapas: fluxo.etapas})" style="position: absolute;bottom: 0;left: 0;">
                                <i style="color: #fff;" class="fas fa-plus"></i>
                            </button>
                        </div>

                        <div class="fluxo-r">
                            <div class="element-pai elementF{{$index}}" style="margin-top: {{getHeight($index)}}px;" ng-if="($index + 1) % 2 === 0" ng-repeat="etapa in fluxo.etapas">
                                <div class="element">
                                    <div class="icon-element">
                                        <button type="button" class="btn_edit6" ng-click="CreateRamo(etapa)">
                                            <i class="fas fa-plus"></i>
                                        </button>

                                        <button style="right: -10px;" type="button" class="btn_edit6" ng-click="EditRamo(etapa)">
                                            <i style="font-size: 13px;color: #3d8aac;" class="fas fa-pen"></i>
                                        </button>

                                        <div style="background-image: url('{{base_url + etapa.atividade.atv_ft}}');" ng-click="EditRamo(etapa)"></div>
                                        <p class="nm_atividade-flow">{{etapa.atividade.nm_atividade_select}}</p>
                                    </div>
                                    <div class="element-line">
                                        <span class="inf-num">{{$index + 1}}</span>
                                    </div>
                                </div>

                                <div style="display: flex;">
                                    <div style="display: flex;" class='element_vert' my-directive></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </md-content>



        </div>


    </div>

    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Create" ng-cloak style="width: 450px;">
        <md-content>
            <md-content layout-padding>
                <md-toolbar class="toolbar-white">
                    <div class="md-toolbar-tools">
                        <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>
                        <md-truncate>{{fluxoModal.id_fluxo == null ? 'Adicionar' : 'Editar'}}</md-truncate>
                    </div>
                </md-toolbar>

                <md-input-container class="md-block">
                    <label><?php echo lang2('name'); ?></label>
                    <input required name="name" ng-model="fluxoModal.name">
                </md-input-container>

                <md-input-container class="md-block " style="margin-top: 3em;">
                    <label>Ativação automático</label>
                    <md-select placeholder="Ativação automático" ng-model="fluxoModal.automatico" style="min-width: 200px;">
                        <md-option value="1">Automático</md-option>
                        <md-option value="0">Manual</md-option>
                    </md-select>
                </md-input-container>

                <md-input-container class="md-block " style="margin-top: 3em;">
                    <label>Funil</label>
                    <md-select multiple placeholder="" ng-model="fluxoModal.funil" style="min-width: 200px;">
                        <md-option value="-1">Todos</md-option>
                        <md-option ng-value="list.id_list" ng-repeat="list in leadslist">{{list.nm_list}}</md-option>
                    </md-select>
                </md-input-container>

                <md-input-container class="md-block" style="margin-top: 3em;">
                    <label><?php echo lang2('assigned'); ?></label>
                    <md-select multiple placeholder="<?php echo lang2('choosestaff'); ?>" ng-model="fluxoModal.funcionario" style="min-width: 200px;">
                        <md-option value="-1">Todos</md-option>
                        <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
                    </md-select>
                </md-input-container>

                <md-input-container class="md-block" style="margin-top: 3em;">
                    <label><?php echo lang2('source'); ?></label>
                    <md-select multiple placeholder="<?php echo lang2('source'); ?>" ng-model="fluxoModal.origem" style="min-width: 200px;">
                        <md-option value="-1">Todos</md-option>
                        <md-option ng-value="source.id" ng-repeat="source in leadssources">{{source.name}}</md-option>
                    </md-select>
                </md-input-container>

                <div ng-repeat="etapa in fluxoModal.etapas">
                    <div style="display: flex;">
                        <md-input-container class="md-block" flex-gt-xs flex="65">
                            <label style="margin-bottom: 0;">Atividade</label>
                            <md-select ng-model="etapa.id_atividade" class="form-control select-modal" style="padding: 0;">
                                <md-option ng-value="atv.id_atv" ng-repeat="atv in leadAtvSelect">{{atv.nm_atividade_select}}</md-option>
                            </md-select>
                        </md-input-container>

                        <md-input-container class="md-block" flex-gt-xs flex="35">
                            <label>Intervalo (dias)</label>
                            <input type="number" max="180" min="0" ng-model="etapa.dias">
                        </md-input-container>
                    </div>

                    <md-input-container class="md-block" style='margin-top: 8px;' ng-if="findAtividadeByid(etapa.id_atividade).nm_atividade_select.toLowerCase().includes('email')
				    || findAtividadeByid(etapa.id_atividade).nm_atividade_select.toLowerCase().includes('e-mail')
				    ">
                        <label style="margin-bottom: 0;">Modelo de Email</label>
                        <md-select ng-model="etapa.email" class="form-control select-modal">
                            <md-option ng-value="atv.id" ng-repeat="atv in templates">{{atv.subject}}</md-option>
                        </md-select>
                    </md-input-container>

                    <md-input-container class="md-block" style='margin-top: 8px;' ng-if="findAtividadeByid(etapa.id_atividade).nm_atividade_select.toLowerCase().includes('whatsapp')
				    || findAtividadeByid(etapa.id_atividade).nm_atividade_select.toLowerCase().includes('whats app')
                    || findAtividadeByid(etapa.id_atividade).nm_atividade_select.toLowerCase().includes('whats-app')
				    ">
                        <label style="margin-bottom: 0;">Usar ia</label>
                        <md-select ng-model="etapa.usar_ia" class="form-control select-modal">

                            <md-option ng-value="0">Desativado</md-option>
                            <md-option ng-value="1">Ativo</md-option>
                        </md-select>
                    </md-input-container>


                    <md-input-container class="md-block">
                        <label>Descrição</label>
                        <textarea class="min_input_width" ng-model="etapa.descricao" placeholder="Descrição"></textarea>
                    </md-input-container>
                </div>

                <md-button ng-click="add()" class="md-fab pull-left" ng-disabled="false" aria-label="Add Line">
                    <md-icon class="ion-plus-round text-muted"></md-icon>
                </md-button>

            </md-content>

            <section layout="row" style="padding: 10px;" layout-sm="column" layout-align="center center" layout-wrap>

                <md-button ng-click="RmvFlow()" style="background: #f00;margin: 0; margin-bottom: 10px;" class="md-raised md-primary btn-report block-button" ng-if="fluxoModal.id_fluxo != null" aria-label="Add">
                    Remover
                </md-button>

                <md-button style="margin: 0;" ng-click="AddFluxo()" class="md-raised md-primary btn-report block-button" ng-disabled="saving == true" aria-label="Add">
                    <span ng-hide="saving == true">{{fluxoModal.id_fluxo == null ? 'Adicionar' : 'Salvar'}}</span>
                    <md-progress-circular class="white" ng-show="saving == true" md-mode="indeterminate" md-diameter="20">
                    </md-progress-circular>
                </md-button>
            </section>

        </md-content>
    </md-sidenav>

    <div class="modal fade" id="modalRamo" style="z-index: 2000;">
        <div class="modal-dialog mdAngular">
            <div class="modal-content">
                <div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
                    <button type="button" class="close" ng-click="dimiss()">
                        <span aria-hidden="true">×</span> <span class="sr-only"><?php echo lang2('close') ?></span>
                    </button>
                    <b ng-if="newEtapaRamo.id_etapa == null">Criar novo ramo</b>
                    <b ng-if="newEtapaRamo.id_etapa != null">Editar ramo</b>
                </div>
                <div id="modalBody" class="modal-body" style="max-height: 70vh; overflow: auto; overflow-x: hidden;">

                    <div class="row">
                        <md-input-container class="col-md-6">
                            <label style="margin-bottom: 0;">Atividade</label>
                            <md-select ng-model="newEtapaRamo.id_atividade" class="form-control select-modal" style="padding: 0;">
                                <md-option ng-value="atv.id_atv" ng-repeat="atv in leadAtvSelect">{{atv.nm_atividade_select}}</md-option>
                            </md-select>
                        </md-input-container>

                        <md-input-container class="col-md-6">
                            <label>Intervalo (dias)</label>
                            <input type="number" max="180" min="0" ng-model="newEtapaRamo.dias">
                        </md-input-container>

                        <md-input-container class="col-md-6">
                            <label>Canal</label>
                            <md-select ng-model="newEtapaRamo.canal" class="form-control select-modal" style="padding: 0;">
                                <md-option ng-value="1">E-mail</md-option>
                                <md-option ng-value="2">WhatsApp</md-option>
                                <md-option ng-value="3">Linkedin</md-option>
                                <md-option ng-value="4">Ligação</md-option>
                            </md-select>
                        </md-input-container>

                        <md-input-container class="col-md-6">
                            <label>Responsável pela ação</label>
                            <md-select ng-model="newEtapaRamo.responsavel" class="form-control select-modal" style="padding: 0;">
                                <md-option ng-value="1">A quem estiver atribuído</md-option>
                                <md-option ng-value="2">Closer</md-option>
                                <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
                            </md-select>
                        </md-input-container>

                        <md-input-container class="col-md-6">
                            <label>Disparo automático</label>
                            <md-select ng-model="newEtapaRamo.automatico" class="form-control select-modal" style="padding: 0;">
                                <md-option ng-value="1">Sim</md-option>
                                <md-option ng-value="0">Não</md-option>
                            </md-select>
                        </md-input-container>

                        <md-input-container class="col-md-6">
                            <label>Enviar para</label>
                            <md-select ng-model="newEtapaRamo.enviar_para" class="form-control select-modal" style="padding: 0;">
                                <md-option ng-value="1">Principal contato</md-option>
                                <md-option ng-value="2">Todos os contatos</md-option>
                                <md-option ng-value="3">Todos emails disponíveis</md-option>
                            </md-select>
                        </md-input-container>

                        <md-input-container class="col-md-6" ng-if="findAtividadeByid(newEtapaRamo.id_atividade).nm_atividade_select.includes('E-mail')
				    || findAtividadeByid(newEtapaRamo.id_atividade).nm_atividade_select.includes('e-mail')
				    || findAtividadeByid(newEtapaRamo.id_atividade).nm_atividade_select.includes('email')">
                            <label>CC</label>
                            <input type="text" ng-model="newEtapaRamo.cc">
                        </md-input-container>

                        <md-input-container class="col-md-6" ng-if="findAtividadeByid(newEtapaRamo.id_atividade).nm_atividade_select.includes('E-mail')
				    || findAtividadeByid(newEtapaRamo.id_atividade).nm_atividade_select.includes('e-mail')
				    || findAtividadeByid(newEtapaRamo.id_atividade).nm_atividade_select.includes('email')">
                            <label>CCO</label>
                            <input type="text" ng-model="newEtapaRamo.cco">
                        </md-input-container>

                    </div>


                    <md-input-container class="md-block" style='margin-top: 8px;' ng-if="findAtividadeByid(newEtapaRamo.id_atividade).nm_atividade_select.includes('E-mail')
				    || findAtividadeByid(newEtapaRamo.id_atividade).nm_atividade_select.includes('e-mail')
				    || findAtividadeByid(newEtapaRamo.id_atividade).nm_atividade_select.includes('email')">
                        <label style="margin-bottom: 0;">Modelo de Email</label>
                        <md-select ng-model="newEtapaRamo.email" class="form-control select-modal">
                            <md-option ng-value="atv.id" ng-repeat="atv in templates">{{atv.subject}}</md-option>
                        </md-select>
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Instruções</label>
                        <textarea class="min_input_width" style="min-height:100px;" id="descricaoModel" ng-model="newEtapaRamo.descricao" placeholder="Instruções"></textarea>
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Copy</label>
                        <textarea class="min_input_width" style="min-height:100px;" id="campoCopiar" ng-model="newEtapaRamo.campoCopiar" placeholder="Campo para copiar"></textarea>
                        <small><b>Variáveis:</b> <a href="javaScript:void(0)" ng-click="addVar('{nome}', 'campoCopiar')">{nome}</a> , <a href="javaScript:void(0)" ng-click="addVar('{empresa}', 'campoCopiar')">{empresa}</a></small>
                    </md-input-container>


                    <button type="button" class="btn btn-secondary" style="float: right;margin-top: 5px;border: 1px solid #e1e1e1;" ng-click="copyDescricao()"><i class="far fa-copy"></i> Copiar</button>


                    <md-input-container class="md-block">
                        <label>Anexo</label>
                        <input style="margin-left: 60px;" type="file" id="fileEtapa" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png,.jpeg" />
                    </md-input-container>
                </div>

                <div class="modal-footer">
                    <button ng-if="newEtapaRamo.id_etapa != null" type="button" class="btn btn-danger" ng-click="excluirEtapa()"><i class="fas fa-trash-alt"></i> Excluir</button>
                    <button type="button" class="btn btn-secondary" ng-click="dimiss()">Fechar</button>
                    <button type="button" class="btn btn-success" ng-click="AddRamo()">Salvar</button>
                </div>

            </div>
        </div>
    </div>



</div>

<script>
    $(document).ready(function() {

    });
</script>
<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>
<script src="<?php echo base_url('assets/lib/chartjs/dist/Chart.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/sales_flow.js?v=3.0.52'); ?>"></script>