<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<link href="<?php echo base_url('assets/lib/select2/select2.min.css'); ?>" rel="stylesheet" />
<link href="<?php echo base_url('assets/css/cssCartao.css?v=1.1.2'); ?>" rel="stylesheet" />
<?php $appconfig = get_appconfig(); ?>
<div class="ciuis-body-content" ng-controller="Reuniao_Controller">
    <style>
        .reunioes {
            margin: 0;
            background: #b2bada;
            color: #4d5472;
            border-radius: 2px;
            margin-top: 5px;
            width: 100%;
            float: left;

            max-width: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            min-width: 100%;
        }

        .calendar {
            border-collapse: collapse;
            margin: 20px auto;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            width: 80%;
            height: 80%;
            min-height: 500px;
            min-width: 500px;
            max-width: 100%;
        }

        .calendar th,
        .calendar td {
            border: 1px solid #e0e0e0;
            padding: 10px;
            text-align: center;
        }

        .different-month {
            color: #ccc;
        }

        /* Estilo para hover nos dias */
        .calendar td:hover {
            background-color: #f5f5f5;
            cursor: pointer;
        }

        /* Cores modernas */
        .calendar th {
            background-color: #f7f9fa;
            color: #6d6d6d;
        }

        .calendar td {
            background-color: #fff;
            max-width: 60px;
            width: 100px;
            height: 100px;
            position: relative;
            padding-top: 25px;
        }

        .number-calendar {
            position: absolute;
            top: 3px;
            left: 5px;
        }

        /* Estilo para os dias do mês atual */
        .calendar td:not(.different-month) {
            color: #333;
        }

        /* Estilo para os botões */
        .calendar .navigation-buttons {
            text-align: center;
            margin-bottom: 10px;
        }

        .calendar .navigation-buttons button {
            padding: 8px;
            margin: 0 5px;
            font-size: 18px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            background: transparent;
        }

        .calendar .navigation-buttons button:hover {
            background-color: #4CAF50;
            color: white;
        }

        .reunioes:hover {
            color: #010d43;
        }

        .bg-warning {
            background-color: #ff9800 !important;
            color: #fff;
        }

        .bg-danger {
            background-color: #FF3B30 !important;
            color: #fff;
        }

        .bg-success {
            background-color: #4caf50 !important;
            color: #fff;
        }
    </style>

    <md-content class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">
        <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">
                <md-button class="md-icon-button" aria-label="Settings" ng-disabled="true">
                    <md-icon><i class="ico-ciuis-leads text-warning"></i></md-icon>
                </md-button>
                <h2 flex md-truncate><?php echo lang2('Reuniões') ?></h2>

                <md-button ng-click="openCreateReuniao()" class="md-icon-button" aria-label="New" ng-cloak>
                    <md-tooltip md-direction="bottom">Nova reunião</md-tooltip>
                    <md-icon aria-label="Add Source"><i class="ion-android-add-circle text-success"></i></md-icon>
                </md-button>
            </div>


        </md-toolbar>
        <md-content class="">

            <div class="col-md-4 col-12">
                <md-input-container class="md-block" style='margin-top: 17px; margin-bottom: 20px !important;'>
                    <label style="margin-bottom: 0;">Funcionário</label>
                    <md-select placeholder="<?php echo lang2('choosestaff'); ?>" ng-change="get_reunioes()" ng-model="filtro.id_funcionario" style="min-width: 200px;">
                        <md-option ng-value="-1">Todos</md-option>
                        <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
                    </md-select>
                </md-input-container>
            </div>

            <div class="col-md-4 col-12">
                <md-input-container class="md-block" style='margin-top: 17px; margin-bottom: 20px !important;'>
                    <label style="margin-bottom: 0;">Mês</label>
                    <md-select placeholder="selecione um mês" ng-change="get_reunioes()" ng-model="month" style="min-width: 200px;">
                        <md-option ng-value="month" ng-repeat="month in months">{{monthNames[month]}}</md-option>
                    </md-select>
                </md-input-container>
            </div>

            <div class="col-md-4 col-12">
                <md-input-container class="md-block" style='margin-top: 17px; margin-bottom: 20px !important;'>
                    <label style="margin-bottom: 0;">Ano</label>
                    <md-select placeholder="selecione um ano" ng-change="get_reunioes()" ng-model="year" style="min-width: 200px;">
                        <md-option ng-value="ano" ng-repeat="ano in get_anos()">{{ano}}</md-option>
                    </md-select>
                </md-input-container>
            </div>


            <table class="calendar">
                <thead>
                    <tr>
                        <th colspan="7">
                            <div class="navigation-buttons">
                                <button ng-click="previousMonth()"><i class="fas fa-chevron-left"></i></button>
                                {{ monthName }} {{ year }}
                                <button ng-click="nextMonth()"><i class="fas fa-chevron-right"></i></button>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th ng-repeat="day in days">{{ day }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="week in weeks">
                        <td ng-repeat="day in week" ng-class="{ 'different-month': day.month !== month }">
                            <span class="number-calendar">{{ day.number}}</span>
                            <a class="reunioes" ng-class="{'bg-warning': reuniao.data ==  year + '-' + monthF + '-' + dayF, 'bg-danger': reuniao.realizada == 'Não Realizada', 'bg-success': reuniao.realizada == 'Realizada'}" ng-click="openReuniao(reuniao)" href="javaScript:void(0)" ng-repeat="reuniao in  reunioes[year + '-' + (month < 9 ? '0' + (month + 1) : (month + 1)) + '-' + (day.number <= 9 ? '0' + day.number : day.number)  ]">Ás {{reuniao.hora_format}} - {{reuniao.nm_lead != null ? reuniao.nm_lead : reuniao.nm_customer}} {{reuniao.staffname}} </a>
                        </td>
                    </tr>
                </tbody>
            </table>

        </md-content>
    </md-content>



    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="modalReuniao" ng-cloak style="width: 450px;">
        <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">
                <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>
                <md-truncate>Nova reunião interna</md-truncate>
            </div>
        </md-toolbar>

        <md-content>


            <md-input-container class="md-block" style = "padding: 10px;">
                <label><?php echo lang2('date_contacted') ?></label>
                <input mdc-datetime-picker="" date="true" time="true" type="text" click-outside-to-close="true" placeholder="<?php echo lang2('chooseadate') ?>" show-todays-date="" minutes="true" today-btn="false" show-icon="true" ng-model="createReuniao.date" class=" dtp-no-msclear dtp-input md-input">
            </md-input-container>

            <md-input-container class="md-block" style='margin-top: 17px; margin-bottom: 20px !important;padding: 10px;'>
                <label style="margin-bottom: 0;">Funcionário</label>
                <md-select placeholder="<?php echo lang2('choosestaff'); ?>" ng-model="createReuniao.id_funcionario" style="min-width: 200px;">

                    <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
                </md-select>
            </md-input-container>

            <div class="modal-footer">
                <button ng-click="create_reuniao()" class="btn btn-success"><?php echo lang2('save'); ?></button>
            </div>
        </md-content>
    </md-sidenav>






    <div class="modal fade" id="modalDetalhesReuniao" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detalhes da reunião
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="col-12">
                        <div class="col-md-12 col-12" style="min-height: 50px;">
                            <a ng-if="reuniaoModal.id_lead != null" href="{{BASE_URL}}leads/lead/{{reuniaoModal.id_lead}}" target="_blank" style="float: right;" class="btn btn-outline-light">Abrir Lead <i class="fas fa-external-link-alt"></i></a>
                            <a ng-if="reuniaoModal.id_customer != null" href="{{BASE_URL}}customers/customer/{{reuniaoModal.id_customer}}" target="_blank" style="float: right;" class="btn btn-outline-light">Abrir Cliente <i class="fas fa-external-link-alt"></i></a>
                            <a href="javaScript:void(0)" ng-click="apagaReuniao(reuniaoModal)" style="float: right;color: #f00; margin-right: 5px;" class="btn btn-outline-light">Apagar <i class="fas fa-trash-alt"></i></a>

                        </div>

                        <div ng-if="reuniaoModal.id_funcionario.split(',').includes('<?= $id_user ?>') && reuniaoModal.confirmado == null" style="margin-top: 20px;">
                            <div class="col-md-12 col-12">
                                <md-input-container class="md-block">
                                    <label style="margin-bottom: 0;">Realizada?</label>
                                    <md-select ng-model="reuniaoModal.realizada" class="form-control select-modal" style="padding: 0;">
                                        <md-option value="Realizada">Realizada</md-option>
                                        <md-option value="Não Realizada">Não Realizada</md-option>
                                    </md-select>
                                </md-input-container>
                            </div>

                            <div class="col-md-12 col-12" ng-show="reuniaoModal.realizada == 'Não Realizada'" style="margin-top: 20px;">
                                <md-input-container class="md-block">
                                    <label style="margin-bottom: 0;">Qual o problema?</label>
                                    <md-select ng-model="reuniaoModal.problema" class="form-control select-modal" style="padding: 0;">
                                        <md-option value="Lead">Lead</md-option>
                                        <md-option value="Nossa empresa">Nossa empresa</md-option>
                                    </md-select>
                                </md-input-container>
                            </div>

                            <div class="col-md-12 col-12" ng-show="reuniaoModal.problema == 'Lead'" style="margin-top: 20px;">
                                <md-input-container class="md-block">
                                    <label style="margin-bottom: 0;">Motivo?</label>
                                    <md-select ng-model="reuniaoModal.motivo" class="form-control select-modal" style="padding: 0;">
                                        <md-option value="Não compareceu">Não compareceu</md-option>
                                        <md-option value="Reagendou">Reagendou</md-option>
                                        <md-option value="Cancelou">Cancelou</md-option>
                                    </md-select>
                                </md-input-container>
                            </div>

                        </div>


                        <div class="col-md-12 col-12" ng-if="!reuniaoModal.id_funcionario.split(',').includes('<?= $id_user ?>') || reuniaoModal.confirmado == 1">
                            <p style="margin-bottom: 0;">Realizada: <b>{{reuniaoModal.realizada}}</b>
                                <i ng-if="reuniaoModal.realizada == 'Realizada'" class="fas fa-check-square" style="color: #4CAF50;font-size: 16px;vertical-align: text-bottom;"></i>
                                <i ng-if="reuniaoModal.realizada == 'Não Realizada'" class="fas fa-times" style="color: #f00;font-size: 16px;vertical-align: text-bottom;"></i>
                            </p>

                            <p ng-show="reuniaoModal.realizada == 'Não Realizada'" style="margin-bottom: 0;">Problema: <b>{{reuniaoModal.problema}}</b></p>
                            <p ng-show="reuniaoModal.problema == 'Lead'" style="margin-bottom: 0;">Motivo: <b>{{reuniaoModal.motivo}}</b></p>
                        </div>



                    </div>
                    <div class="divInfoL2">
                        <div class="col-md-6 col-12" ng-if="reuniaoModal.tp_pessoa == 1">
                            <md-icon class="mdi mdi-local-store flt_left"></md-icon>
                            <div class="flt_left">
                                <p class="titleInfo"><?php echo lang2('name') ?></p>
                                <p class="nmInfo"><span ng-bind="reuniaoModal.name"></span></p>
                            </div>
                        </div>

                        <div class="col-md-6 col-12" ng-if="reuniaoModal.tp_pessoa == 2">
                            <md-icon class="mdi mdi-local-store flt_left"></md-icon>
                            <div class="flt_left">
                                <p class="titleInfo"><?php echo lang2('company') ?></p>
                                <p class="nmInfo"><span ng-bind="reuniaoModal.company"></span></p>
                            </div>
                        </div>

                        <div class="col-md-6 col-12" ng-if="reuniaoModal.tp_pessoa == null && reuniaoModal.id_customer != null">
                            <md-icon class="mdi mdi-local-store flt_left"></md-icon>
                            <div class="flt_left">
                                <p class="titleInfo"><?php echo lang2('company') ?></p>
                                <p class="nmInfo"><span ng-bind="reuniaoModal.nm_customer"></span></p>
                            </div>
                        </div>

                        <div class="col-md-6 col-12" ng-if="(reuniaoModal.phone != null && reuniaoModal.phone != '') || reuniaoModal.phone_c != null && reuniaoModal.phone_c != ''">
                            <md-icon class="mdi mdi-local-phone"></md-icon>
                            <div class="flt_left">
                                <p class="titleInfo"><?php echo lang2('phone') ?></p>
                                <p class="nmInfo"><span ng-bind="reuniaoModal.phone"></span></p>
                                <p class="nmInfo"><span ng-bind="reuniaoModal.phone_c"></span></p>
                            </div>
                        </div>

                        <div class="col-md-6 col-12" ng-if="(reuniaoModal.email != null && reuniaoModal.email != '') || reuniaoModal.email_c != null && reuniaoModal.email_c != ''">
                            <md-icon class="ion-android-mail"></md-icon>
                            <div class="flt_left">
                                <p class="titleInfo"><?php echo lang2('email') ?></p>
                                <p class="nmInfo"><span ng-bind="reuniaoModal.email"></span></p>
                                <p class="nmInfo"><span ng-bind="reuniaoModal.email_c"></span></p>
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <md-icon class="ion-android-calendar"></md-icon>
                            <div class="flt_left">
                                <p class="titleInfo">Data da reunião</p>
                                <p class="nmInfo"><span ng-bind="reuniaoModal.data | date:'dd/MM/yyyy'"></span> <span ng-bind="reuniaoModal.hora | date:'HH:mm'"></span></p>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer" style="margin-top: 30px;" ng-if="reuniaoModal.id_funcionario.split(',').includes('<?= $id_user ?>')">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-success" ng-click="salva_reuniao(reuniaoModal.id_reuniao, reuniaoModal)">Salvar</button>
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
<script type="text/javascript" src="<?php echo base_url('assets/js/reunioes.js?v=1.5.14') ?>"></script>