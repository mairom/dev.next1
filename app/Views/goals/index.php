<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<link href="<?php echo base_url('assets/lib/select2/select2.min.css'); ?>" rel="stylesheet" />
<?php $appconfig = get_appconfig(); ?>
<div id="pageContent">
    <div class="ciuis-body-content" ng-controller="Goals_Controller">
        <style type="text/css">
            rect.highcharts-background {
                fill: #f3f3f3;
            }

            .select2-container {
                width: 100% !important;
            }

            .select2-selection {
                min-height: 48px !important;
                padding: 8px 0 !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow b:after {
                display: none;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow b {
                margin-top: 7px;
                margin-left: -7px;
            }
        </style>
        <div class="main-content container-fluid col-xs-12 col-md-8 col-lg-8">
            <md-toolbar class="toolbar-white">
                <div class="md-toolbar-tools">
                    <h2 flex md-truncate class="text-bold"><?php echo lang2('x_menu_goal'); ?> <small>(<span ng-bind="goals.length"></span>)</small><br>
                        <small flex md-truncate><?php echo lang2('goalsdescription'); ?></small>
                    </h2>

                    <?php if (check_privilege('goals', 'create')) { ?>
                        <md-button ng-click="Create()" class="md-icon-button" aria-label="New" ng-cloak>
                            <md-icon><i class="ion-android-add-circle text-success"></i></md-icon>
                        </md-button>
                    <?php } ?>

                </div>
            </md-toolbar>
            <div ng-show="goalFile" layout-align="center center" class="text-center" id="circular_loader">
                <!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
                <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">

                <p style="font-size: 15px;margin-bottom: 5%;">
                    <span><?php echo lang2('please_wait') ?> <br>
                        <small><strong><?php echo lang2('loading') . ' ' . lang2('goals') . '...' ?></strong></small></span>
                </p>
            </div>
            <md-content ng-show="!metaFile" class="md-pt-0 bg-white">
                <md-table-container ng-show="metas.length > 0">
                    <table md-table md-progress="promise" ng-cloak>
                        <thead md-head md-order="meta_list.order">
                            <tr md-row>
                                <th md-column md-order-by="name"><span><?php echo lang2('goal'); ?></span></th>
                                <th md-column md-order-by="nm_equipe"><span>Equipe</span></th>
                                <th md-column md-order-by="tp_meta"><span>Tipo</span></th>
                                <th md-column></th>
                            </tr>
                        </thead>
                        <tbody md-body>
                            <tr class="select_row" md-row ng-repeat="meta in metas " class="cursor">

                                <td md-cell>
                                    <span ng-bind="meta.nm_goal"></span>
                                </td>
                                <td md-cell>
                                    <span ng-bind="meta.nm_equipe"></span>
                                </td>
                                <td md-cell>
                                    <span ng-bind="getTp_meta(meta.tp_meta)"></span>
                                </td>
                                <td md-cell>
                                    <md-button ng-click="removeMeta(meta.id_goal)" class="md-icon-button pull-right" aria-label="Remove" ng-cloak>
                                        <md-icon style="font-size: 21px;" class="mdi mdi-close ng-scope material-icons text-danger"></md-icon>
                                    </md-button>
                                    <md-button ng-click="editMeta(meta)" class="md-icon-button pull-right" aria-label="New" ng-cloak>
                                        <md-icon style="font-size: 21px;" class="mdi mdi-edit ng-scope material-icons text-success"></md-icon>
                                    </md-button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </md-table-container>
                <md-table-pagination ng-show="metas.length > 0" md-limit="meta_list.limit" md-limit-options="limitOptions" md-page="meta_list.page" md-total="{{metas.length}}"></md-table-pagination>
                <md-content ng-show="!metas.length" class="md-padding no-item-data" ng-cloak><?php echo lang2('notdata') ?></md-content>
            </md-content>
        </div>


        <div class="main-content container-fluid col-xs-12 col-md-4 col-lg-4 md-pl-0 lead-left-bar">
            <div class="panel-default panel-table borderten lead-manager-head">
                <md-toolbar class="toolbar-white">
                    <div class="md-toolbar-tools">
                        <h2 flex md-truncate class="text-bold">Equipe
                            <md-button data-toggle="modal" ng-click="editEquipe()" data-target="#modalEquipe" class="md-icon-button pull-right" aria-label="New" ng-cloak>
                                <md-icon><i class="ion-android-add-circle text-success"></i></md-icon>
                            </md-button>
                        </h2>
                    </div>
                </md-toolbar>
                <div class="tasks-status-stat" ng-cloak>
                    <div class="widget-chart-container">
                        <div class="widget-counter-group widget-counter-group-right">
                            <md-table-container ng-show="equipes.length > 0">
                                <table md-table md-progress="promise">
                                    <thead md-head md-order="equipes_list.order">
                                        <tr md-row>
                                            <th md-column md-order-by="nm_equipe"><span>Nome</span></th>
                                            <th md-column md-order-by="funcionarios_list"><span>Equipe</span></th>

                                            <th md-column></th>
                                        </tr>
                                    </thead>
                                    <tbody md-body>
                                        <tr class="select_row" md-row ng-repeat="equipe in equipes" class="cursor">
                                            <td md-cell>
                                                <strong><span ng-bind="equipe.nm_equipe"></span></strong>
                                            </td>
                                            <td md-cell>
                                                <span ng-bind="equipe.funcionarios_list"></span>
                                            </td>

                                            <td md-cell>
                                                <md-button ng-click="removeEquipe(equipe.id_equipe)" class="md-icon-button pull-right" aria-label="Remove" ng-cloak>
                                                    <md-icon style="font-size: 21px;" class="mdi mdi-close ng-scope material-icons text-danger"></md-icon>
                                                </md-button>
                                                <md-button ng-click="editEquipe(equipe)" data-toggle="modal" data-target="#modalEquipe" class="md-icon-button pull-right" aria-label="New" ng-cloak>
                                                    <md-icon style="font-size: 21px;" class="mdi mdi-edit ng-scope material-icons text-success"></md-icon>
                                                </md-button>
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

        <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Create" ng-cloak style="width: 450px;">
            <md-toolbar class="toolbar-white">
                <div class="md-toolbar-tools">
                    <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>
                    <md-truncate><?php echo lang2('addgoal') ?></md-truncate>
                </div>
            </md-toolbar>
            <md-content>
                <md-content layout-padding>
                    <md-input-container class="md-block">
                        <label><?php echo lang2('name') ?></label>
                        <input required type="text" ng-model="metaModal.nm_goal" class="form-control" id="name" placeholder="<?php echo lang2('name'); ?>" />
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Equipe</label>
                        <md-select placeholder="Equipe" ng-model="metaModal.id_equipe" style="min-width: 200px;">
                            <md-option ng-value="equipe.id_equipe" ng-repeat="equipe in equipes">{{equipe.nm_equipe}}</md-option>
                        </md-select>
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Tipo de Meta</label>
                        <md-select placeholder="Tipo de Meta" ng-model="metaModal.tp_meta" style="min-width: 200px;">
                            <md-option ng-value="tp_meta.id" ng-repeat="tp_meta in tp_metas">{{tp_meta.nome}}</md-option>
                        </md-select>
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Inicio</label>
                        <input required type="date" ng-max="metaModal.fim" ng-model="metaModal.inicio" class="form-control" placeholder="Inicio" />
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Fim</label>
                        <input required type="date" ng-min="metaModal.inicio" ng-model="metaModal.fim" class="form-control" placeholder="Fim" />
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Periodo</label>
                        <md-select placeholder="Periodo" ng-model="metaModal.periodo" style="min-width: 200px;">
                            <md-option ng-value="periodo.periodo" ng-repeat="periodo in periodos">{{periodo.nm_periodo}}</md-option>
                        </md-select>
                    </md-input-container>


                    <md-input-container class="md-block">
                        <label>Observação</label>
                        <textarea rows="3" required type="text" ng-model="metaModal.observacao" class="form-control" placeholder="Duração">
                        </textarea>
                    </md-input-container>

                    <h4 style="margin: 0;margin-bottom: 10px;text-align: center;font-weight: 600;">Definir metas</h4>

                    <div ng-repeat="definicao in metaModal.definicoes">
                        <md-input-container class="md-block">
                            <label>Valor</label>
                            <input type="number" ng-model="definicao.valor" class="form-control" placeholder="Valor" />
                        </md-input-container>

                        <md-input-container class="md-block" style="margin-top: 35px;">
                            <div class='col-9' style="width: 80%;float: left;">
                                <label>Bonificação</label>
                                <input type="number" ng-model="definicao.bonificacao" class="form-control" placeholder="Bonificação" />
                            </div>
                            <div class='col-3' style="width: 20%;float: left;">
                                <select ng-model="definicao.tp_bonificacao" class="form-control" style="height: 31px;padding: 0 10px;margin-left: 5px;">
                                    <option value="%">%</option>
                                    <option value="R$">R$</option>
                                </select>
                            </div>
                        </md-input-container>
                    </div>

                    <md-button style="width: 150px;display: flex;margin: 0 auto;" ng-click="AddDefinicao()" class="md-icon-button" aria-label="New" ng-cloak>
                        Adicionar definição <md-icon><i class="ion-android-add text-success"></i></md-icon>
                    </md-button>

                </md-content>

                <md-content layout-padding>
                    <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>
                        <md-button ng-click="addGoal()" class="md-raised md-primary pull-right" ng-bind="metaModal.id_goal != null ? 'Atualizar' : 'Adicionar'">
                        </md-button>
                    </section>
                </md-content>

            </md-content>
        </md-sidenav>

        <div class="modal fade" id="modalEquipe" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Equipes
                            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </h5>
                    </div>
                    <div class="modal-body">
                        <md-input-container class="md-block">
                            <label>Nome</label>
                            <input required type="text" ng-model="modalEquipe.nm_equipe" class="form-control" id="nm_equipe" placeholder="Nome" />
                        </md-input-container>

                        <md-input-container id = "equipeDiv" class="md-block" style="margin-top: 3em;">
                            <label>Equipe</label>
                            <select class="select2" id="equipeModal" placeholder="Equipe" ng-model="modalEquipe.equipe" multiple>
                            </select>
                        </md-input-container>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" ng-click="salva_equipe()">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $(".count").TouchSpin();

        $('#equipeModal').select2({
            dropdownParent: $("#equipeDiv"),
            ajax: {
                url: '<?= base_url('api/get_staff'); ?>',
                dataType: 'json'
            }
        });

    });
    var lang = {};
    lang.goal = '<?php echo lang2('goal') ?>';
    lang.categories = '<?php echo lang2('categories') ?>';
    lang.addgoalCategory = '<?php echo lang2('addgoalCategory') ?>';
    lang.type_categoryname = '<?php echo lang2('type_categoryname') ?>';
    lang.categoryname = '<?php echo lang2('categoryname') ?>';
    lang.cancel = '<?php echo lang2('cancel') ?>';
    lang.add = '<?php echo lang2('add') ?>';
    lang.categoryname = '<?php echo lang2('categoryname') ?>';
    lang.edit = '<?php echo lang2('edit') ?>';
    lang.attention = '<?php echo lang2('attention') ?>';
    lang.save = '<?php echo lang2('save') ?>';
    lang.confirm_goal_category_delete = '<?php echo lang2('confirm_goal_category_delete') ?>';
    lang.doIt = '<?php echo lang2('doIt') ?>';
    lang.delete = '<?php echo lang2('delete') ?>';
    lang.goalattentiondetail = '<?php echo lang2('goalattentiondetail') ?>';
</script>
<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>
<script src="<?php echo base_url('assets/lib/highcharts/highcharts.js') ?>"></script>
<script src="<?php echo base_url('assets/js/goals.js?v=1.1.3'); ?>"></script>