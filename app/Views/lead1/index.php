
<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<link href="<?php echo base_url('assets/lib/select2/select2.min.css'); ?>" rel="stylesheet" />
<link href="<?php echo base_url('assets/css/cssCartao.css?v=1.1.2'); ?>" rel="stylesheet" />
<?php $appconfig = get_appconfig(); ?>
<div class="ciuis-body-content" ng-controller="Lead1_Controller">
    <style type="text/css">
        body {
            background: url('/assets/img/<?= $back_lead1 ?>');
            background-position: center;
            background-size: 100% auto;
            background-repeat: no-repeat;
        }

        @media (max-width: 1080px) {
            body {
                background-size: auto 100%;
            }
        }

        @media (max-width: 1080px) {
            md-input-container {
                width: calc(100% - 15px) !important;
            }
        }


        md-content {
            background: transparent !important;
        }

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

        .imgLogoC {
            width: 50px;
            height: 50px;
            margin-bottom: 5px;
            margin-top: 5px;
        }

        .Presults {
            padding-left: 10px;
            padding-top: 10px;
            font-size: 15px;
        }

        a.badge:hover,
        a.badge:focus {
            color: #0f14b1;
        }

        .btnCC {
            width: 100%;
            margin: 0;
            padding: 0;
            margin-top: 10px;
            color: #28a745;
            background-color: transparent;
            box-shadow: 0px 0px 3px #28a745;
        }

        .btnCC:hover,
        .btnCC:active {
            background: #28a745;
            color: #fff;
        }

        .thumbnail {
            background: linear-gradient(155deg, #fefeff, #f0f5ff);
            box-shadow: 0px 0px 2px 1px #d4dfef;
            border: 0;
        }

        .checkLead {
            cursor: pointer;
        }

        .divBtnFix {
            position: fixed;
            right: 10px;
            bottom: 5px;
            padding: 5px;
            background: #fff;
            box-shadow: 0px 0px 3px 0px #858585;
            border-radius: 5px;
        }

        .divBtnFix button {
            padding: 6px 15px;
            box-shadow: 0px 0px 2px #126c47;
        }

        #form-checkout {
            display: flex;
            flex-direction: column;
            max-width: 600px;
        }

        .container {
            height: 18px;
            display: inline-block;
            border: 1px solid rgb(118, 118, 118);
            border-radius: 2px;
            padding: 1px 2px;
        }

        md-tab-item {
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #000 !important;
        }

        md-tabs.md-default-theme md-ink-bar,
        md-tabs md-ink-bar {
            color: rgb(92 92 92);
            background: rgb(92 92 92);
        }

        .campos-filtro label {
            text-transform: unset !important;
            font-weight: 400;
            color: #000 !important;
        }

        md-tab-item {
            text-transform: unset;
        }

        md-select-menu md-content {
            background: #fff !important;
        }

        .md-block2 {
            max-width: 200px;
            float: left;
        }

        .btn-pesq {
            padding: 30px 20px !important;
            background: #fff;
            border-radius: 50% !important;
            color: #000;
            width: auto !important;
            margin: 0 auto !important;
            font-size: 13px;
            margin-top: 20px !important;
            height: auto !important;
            box-shadow: 0px 0px 3px #000;
        }

        .btn-pesq:hover {
            background: #eee !important;
        }

        .content-main-lead1 {
            background: transparent !important;
        }
    </style>
    <md-content class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">

        <!--
        <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">
                <md-button class="md-icon-button" aria-label="Settings" ng-disabled="true">
                    <md-icon><i style="color: #6d6f70;font-size: 25px;" class="icon ion-person-add"></i></md-icon>
                </md-button>
                <h2 flex md-truncate><?php echo lang2('x_menu_lead_p_1') ?>

                    <md-menu md-position-mode="target-right target" ng-cloak style="float: right;">
                        <md-button aria-label="Open demo menu" class="md-icon-button" ng-click="$mdMenu.open($event)">
                            <md-icon><i class="ion-android-more-vertical text-muted"></i></md-icon>
                        </md-button>
                        <md-menu-content width="4">
                            <md-menu-item>
                                <md-button ng-click="ImportCustomersNav()">
                                    <div layout="row" flex>
                                        <p flex> Importar empresas</p>
                                        <md-icon md-menu-align-target class="ion-upload text-muted" style="margin: auto 3px auto 0;">
                                        </md-icon>
                                    </div>
                                </md-button>
                            </md-menu-item>
                        </md-menu-content>
                    </md-menu>
                </h2>
            </div>
        </md-toolbar>
    -->

        <h2 class="title-master-lead1">
            ENCONTRE AS EMPRESAS CERTAS <br>
            PARA SEU NEGÓCIO
        </h2>

        <h4 class="subtitle-master-lead1">Mais de de 27.000.000 de empresas aguardando seu contato!</h4>

        <md-content class="content-main-lead1">

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


            <md-content class="md-padding" ng-show="!overview.loader" style="padding: 15px 0;">
                <md-content class="widget-fullwidth ciuis-body-loading" style="min-height: 85px;">
                    <div class='filtrosLeads' style="min-height: 0;padding: 0em;">
                        <md-content class="campos-filtro" ng-cloak style="padding-bottom: 20px; text-align: center;">

                            <!--
                            <md-input-container style="width: 40%;max-width: 400px;margin-bottom: 0;">
                                <label>Ramos</label>
                                <md-select ng-model="filtrosL.ramo">
                                    <md-select-header>
                                        <input type="search" onkeydown="mdSelectOnKeyDownOverride(event);" ng-change="get_ramos2(filtrosL.ramo_input);" ng-model="filtrosL.ramo_input" class="form-control" placeholder="Digite a menos 1 caractere para buscar." />
                                    </md-select-header>
                                    <md-option ng-value="ramo.text" ng-if="!selecionados.ramos.includes(ramo.text)" ng-click="selecionados.ramos.push(ramo.text)" ng-repeat="ramo in todos_ramos">{{ramo.text}}</md-option>
                                    <md-option ng-value="ramo" ng-repeat="ramo in selecionados.ramos">{{ramo}}</md-option>
                                </md-select>
                            </md-input-container>

                            <md-input-container class="md-block">
                                <label>Porte</label>
                                <md-select placeholder="Porte" id="porte" ng-model="filtrosL.porte" ng-change="getResultados()" style="min-width: 200px;">
                                    <md-option value="GRANDE PORTE">GRANDE PORTE</md-option>
                                    <md-option value="MÉDIO PORTE">MÉDIO PORTE</md-option>
                                    <md-option value="MICRO EMPRESA">MICRO EMPRESA</md-option>
                                    <md-option value="PEQUENO PORTE">PEQUENO PORTE</md-option>
                                </md-select>
                            </md-input-container>

                            <md-input-container class="md-block">
                                <label>Matriz/Filial</label>
                                <md-select placeholder="Matriz/Filial" id="matrizEFilial" ng-model="filtrosL.matrizEFilial" style="min-width: 200px;">
                                    <md-option value="-1">Matrizes & Filiais</md-option>
                                    <md-option value="M">Somente Matrizes</md-option>
                                    <md-option value="F">Somente Filiais</md-option>
                                </md-select>
                            </md-input-container>



                            <md-input-container class="md-block">
                                <label>Estado</label>
                                <md-select placeholder="Estado" id="Estado" ng-model="filtrosL.estado" style="min-width: 200px;">
                                    <md-option value="-1">Todos</md-option>
                                    <md-option ng-value="estado" ng-repeat="estado in ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO']">{{estado}}</md-option>
                                </md-select>
                            </md-input-container>

                            <md-input-container style="width: 22%;max-width: 200px;">
                                <label>Cidade</label>
                                <md-select ng-model="filtrosL.cidade">
                                    <md-select-header>
                                        <input type="search" onkeydown="mdSelectOnKeyDownOverride(event);" ng-change="get_cidade(filtrosL.cidade_input);" ng-model="filtrosL.cidade_input" class="form-control" placeholder="Digite a menos 1 caractere para buscar." />
                                    </md-select-header>
                                    <md-option ng-value="cidade.text" ng-if="!selecionados.cidades.includes(cidade.text)" ng-click="selecionados.cidades.push(cidade.text)" ng-repeat="cidade in todas_cidades">{{cidade.text}}</md-option>
                                    <md-option ng-value="cidade" ng-repeat="cidade in selecionados.cidades">{{cidade}}</md-option>
                                </md-select>
                            </md-input-container>
                            -->

                            <md-button ng-click="PesquisarEmpresa()" style="" class="md-icon-button btn-pesq" aria-label="Settings">
                                Iniciar
                            </md-button>

                        </md-content>


                        <br>
                    </div>
                </md-content>
            </md-content>
        </md-content>
    </md-content>
</div>

<script type="text/javascript">
    var lang = {};
    var is_admin = '<?= $user_data['super_admin'] ?>';
    lang.payments = '<?php echo lang2('payments') ?>';
    lang.expenses = '<?php echo lang2('expenses') ?>';
    var SCOPE;
    var nome_empresa = '';
    var ramo = '';
    var estado = '';
    var cidade = '';
    var porte = '';
    var matrizEFilial = '';
    var pageAtual = 1;
</script>
<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>
<script src="<?php echo base_url('assets/lib/chartjs/dist/Chart.min.js'); ?>" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/lead1.js?v=1.1.21') ?>"></script>