<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<link href="<?php echo base_url('assets/lib/select2/select2.min.css'); ?>" rel="stylesheet" />
<link href="<?php echo base_url('assets/css/cssCartao.css?v=1.1.2'); ?>" rel="stylesheet" />
<?php $appconfig = get_appconfig(); ?>
<div class="ciuis-body-content" ng-controller="leadsToDo_Controller">
    <style type="text/css">
        .image-container {
            text-align: center;
            margin-top: 20px;
        }

        .image-container img {
            max-width: 100%;
            height: auto;
        }

        .image-controls {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .control-btn {
            background-color: #3498db;
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-left: 5px;
        }

        .control-btn:hover {
            background-color: #2980b9;
        }

        .carLead {
            max-width: 250px;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0px 3px 2px 0px #d2d2d2;
            border-radius: 2px;
            padding: 8px !important;
            text-align: left;
        }

        .carLead img {
            height: 13px;
            width: 13px;
        }
    </style>
    <md-content class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">
        <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">
                <md-button class="md-icon-button" aria-label="Settings" ng-disabled="true">
                    <md-icon><i class="ico-ciuis-leads text-warning"></i></md-icon>
                </md-button>
                <h2 flex md-truncate><?php echo lang2('leads To Do') ?></h2>
            </div>
        </md-toolbar>
        <md-content class="">
            <div class="col-md-12 col-12">

                <div class="image-controls">
                    <button class="control-btn" ng-click="previousImage()">
                        <i class="fas fa-backward"></i>
                    </button>
                    <button class="control-btn" ng-click="stopSlideshow()">
                        <i class="fas fa-stop"></i>
                    </button>
                    <button class="control-btn" ng-click="togglePlayPause()">
                        <i ng-class="{'fas fa-play': !isPlaying, 'fas fa-pause': isPlaying}"></i>
                    </button>
                    <button class="control-btn" ng-click="nextLead()">
                        <i class="fas fa-forward"></i>
                    </button>
                </div>

                <h4>Total do dia: <b>{{leads.length}}</b></h4>

                <md-list flex class="md-p-0 sm-p-0 lg-p-0">
                    <md-list-item ng-repeat="lead in leads" aria-label="Logs">
                        <md-icon><i class="fas fa-user-tie"></i></md-icon>
                        <p><strong> {{ lead.name_lead | limitTo: 20 }}{{lead.name_lead.length > 20 ? '...' : ''}}</strong></p>
                        <md-divider></md-divider>
                    </md-list-item>
                </md-list>



            </div>

        </md-content>
    </md-content>

    <div class="modal fade" id="modalPausa" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="margin-top: 50px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Informe o motivo da pausa
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </h5>
                </div>
                <div class="modal-body">
                    <md-input-container style="width: 40%;max-width: 500px;margin-bottom: 0;">
                        <label>Motivo da pausa</label>
                        <md-select ng-model="modal.motivo">
                            <md-option ng-value="motivo.id" ng-repeat="motivo in motivos">{{motivo.text}}</md-option>
                        </md-select>
                    </md-input-container>


                </div>
                <div class="modal-footer" style="margin-top: 30px;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" ng-click="salva_pacote()">Salvar</button>
                </div>
            </div>
        </div>
    </div>


</div>

<script type="text/javascript">
    var lang = {};
    lang.payments = '<?php echo lang2('payments') ?>';
    lang.expenses = '<?php echo lang2('expenses') ?>';
    var SCOPE;
    var base_url = '<?= base_url('') ?>';
    var nome_empresa = '<?= isset($nome_empresa) ? $nome_empresa : '' ?>';
    var ramo = '<?= isset($ramo) ? $ramo : '' ?>';
    var estado = '<?= isset($estado) ? $estado : '' ?>';
    var cidade = '<?= isset($cidade) ? $cidade : '' ?>';
    var porte = '<?= isset($porte) ? $porte : '' ?>';
    var matrizEFilial = '<?= isset($matrizEFilial) ? $matrizEFilial : '' ?>';
    var is_admin = '<?= $user_data['super_admin'] ?>';
    var pageAtual = '<?= (isset($_GET['page']) ? $_GET['page'] : 1) ?>';
</script>
<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>
<script src="<?php echo base_url('assets/lib/chartjs/dist/Chart.min.js'); ?>" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/leadsToDo.js?v=1.5.4') ?>"></script>