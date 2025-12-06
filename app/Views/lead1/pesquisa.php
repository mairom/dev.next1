<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<link href="<?php echo base_url('assets/lib/select2/select2.min.css'); ?>" rel="stylesheet" />
<link href="<?php echo base_url('assets/css/cssCartao.css?v=1.1.2'); ?>" rel="stylesheet" />
<?php $appconfig = get_appconfig(); ?>
<div class="ciuis-body-content" ng-controller="Lead1_Controller">
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
            background: transparent;
            border: 0;
        }

        .divBtnFix input {
            border: 0;
            padding: 5px;
            background: #ffffff9e;
            color: #000;
            outline: none;
            width: 130px;
            font-size: 14px;
            font-weight: 300;
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

        .card-content h5,
        .card-content h6 {
            margin: 0;
        }

        #cardexpiration {
            width: calc(100% - 35px);
        }

        .md-block2 {
            max-width: 200px;
            float: left;
        }
    </style>
    <md-content class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">
        <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">
                <md-button class="md-icon-button" aria-label="Settings" ng-disabled="true">
                    <md-icon><i class="ico-ciuis-leads text-warning"></i></md-icon>
                </md-button>
                <h2 flex md-truncate><?php echo lang2('x_menu_lead_p_1') ?></h2>

                <md-menu md-position-mode="target-right target" ng-cloak>
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
            </div>


        </md-toolbar>
        <md-content class="">
            <md-tabs md-dynamic-height md-border-bottom md-selected="ctrl.selectedIndex">
                <md-tab label="Lead1+">

                    <md-content class="md-padding">
                        <md-content class="widget-fullwidth ciuis-body-loading" style="min-height: 85px;">
                            <div class='filtrosLeads' style="min-height: 0;padding: 0em;">

                                <md-content class="bg-white" ng-cloak style="padding-bottom: 20px;">
                                    <md-content class="bg-white">
                                        <md-input-container class="md-block col-12 col-md-4">
                                            <label>Ramos</label>
                                            <md-select ng-model="filtrosL.ramo" multiple>
                                                <md-select-header>
                                                    <input type="search" onkeydown="mdSelectOnKeyDownOverride(event);" ng-change="get_ramos2(filtrosL.ramo_input);" ng-model="filtrosL.ramo_input" class="form-control" placeholder="Digite a menos 3 caractere para buscar." />
                                                </md-select-header>


                                                <md-option ng-if="carregandoResultado" disabled>
                                                    <span layout="row" layout-align="start center">
                                                        <img src="/uploads/images/load.gif" style="width: 20px; height: 20px; margin-right: 8px;" />
                                                        Carregando...
                                                    </span>
                                                </md-option>

                                                <md-option ng-value="ramo.text" ng-if="!selecionados.ramos.includes(ramo.text)" ng-click="selecionados.ramos.push(ramo.text)" ng-repeat="ramo in todos_ramos">{{ramo.text}}</md-option>
                                                <md-option ng-value="ramo" ng-repeat="ramo in selecionados.ramos">{{ramo}}</md-option>
                                            </md-select>
                                        </md-input-container>

                                        <md-input-container class="md-block col-12 col-md-3 ">
                                            <label>Porte</label>
                                            <md-select placeholder="Porte" id="porte" ng-model="filtrosL.porte" style="min-width: 200px;">
                                                <md-option value="-1">TODOS</md-option>
                                                <md-option value="GRANDE PORTE">GRANDE PORTE</md-option>
                                                <md-option value="MÉDIO PORTE">MÉDIO PORTE</md-option>
                                                <md-option value="MICRO EMPRESA">MICRO EMPRESA</md-option>
                                                <md-option value="PEQUENO PORTE">PEQUENO PORTE</md-option>
                                            </md-select>
                                        </md-input-container>

                                        <md-input-container class="md-block col-12 col-md-3">
                                            <label>Matriz/Filial</label>
                                            <md-select placeholder="Matriz/Filial" id="matrizEFilial" ng-model="filtrosL.matrizEFilial" style="min-width: 200px;">
                                                <md-option value="-1">Matrizes & Filiais</md-option>
                                                <md-option value="M">Somente Matrizes</md-option>
                                                <md-option value="F">Somente Filiais</md-option>
                                            </md-select>
                                        </md-input-container>
                                    </md-content>
                                    <md-content class="bg-white">


                                        <md-input-container class="md-block col-12 col-md-3">
                                            <label>Cidade</label>
                                            <md-select ng-model="filtrosL.cidade">
                                                <md-select-header>
                                                    <input type="search" onkeydown="mdSelectOnKeyDownOverride(event);" ng-change="get_cidade(filtrosL.cidade_input);" ng-model="filtrosL.cidade_input" class="form-control" placeholder="Digite a menos 1 caractere para buscar." />
                                                </md-select-header>

                                                <md-option ng-if="carregandoResultado" disabled>
                                                    <span layout="row" layout-align="start center">
                                                        <img src="/uploads/images/load.gif" style="width: 20px; height: 20px; margin-right: 8px;" />
                                                        Carregando...
                                                    </span>
                                                </md-option>


                                                <md-option value="-1">Todos</md-option>
                                                <md-option ng-value="cidade.text" ng-if="!selecionados.cidades.includes(cidade.text)" ng-click="selecionados.cidades.push(cidade.text)" ng-repeat="cidade in todas_cidades">{{cidade.text}}</md-option>
                                                <md-option ng-value="cidade" ng-repeat="cidade in selecionados.cidades">{{cidade}}</md-option>
                                            </md-select>
                                        </md-input-container>

                                        <md-input-container class="md-block col-12 col-md-2">
                                            <label>Estado</label>
                                            <md-select placeholder="Estado" id="Estado" ng-model="filtrosL.estado" style="min-width: 200px;">
                                                <md-option value="-1">Todos</md-option>
                                                <md-option ng-value="estado" ng-repeat="estado in ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO']">{{estado}}</md-option>
                                            </md-select>
                                        </md-input-container>

                                        <md-input-container class="md-block col-12 col-md-3">
                                            <label>Nome da empresa</label>
                                            <md-select ng-model="filtrosL.nome_empresa" multiple>
                                                <md-select-header>
                                                    <input type="search" onkeydown="mdSelectOnKeyDownOverride(event);" ng-change="get_nomes(filtrosL.nome_empresa_input);" ng-model="filtrosL.nome_empresa_input" class="form-control" placeholder="Digite a menos 1 caractere para buscar." />
                                                </md-select-header>
                                                <md-option value="-1">Todos</md-option>
                                                <md-option ng-value="nome_empresa.text" ng-if="!selecionados.nome_empresas.includes(nome_empresa.text)" ng-click="selecionados.nome_empresas.push(nome_empresa.text)" ng-repeat="nome_empresa in todos_nome_empresas">{{nome_empresa.text}}</md-option>
                                                <md-option ng-value="nome_empresa" ng-repeat="nome_empresa in selecionados.nome_empresas">{{nome_empresa}}</md-option>
                                            </md-select>
                                        </md-input-container>

                                        <md-button style="padding: 0px 10px;font-size: 11px;margin-top: 2em;" class="btn-shadow d-inline-flex align-items-center btn2 btn-success" ng-click="getResultados()" aria-label="New" ng-cloak>
                                            Buscar
                                            <md-icon style="vertical-align: unset;"><i class="ion-android-search"></i></md-icon>
                                        </md-button>

                                        <md-button ng-click="adicionarEmpresa()" style="margin-top: 10px;width: 160px;height: 65px;float: right;color: #efb820;cursor: pointer;" class="md-icon-button" aria-label="Settings">
                                            <md-icon><i style="color: #ffa500;" class="fa fa-plus"></i></md-icon>
                                            Adicionar Créditos
                                        </md-button>
                                    </md-content>
                                </md-content>

                                <br>
                            </div>
                        </md-content>

                        <div ng-show="loader" layout-align="center center" class="text-center" id="circular_loader">
                            <!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
                            <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">

                            <p style="font-size: 15px;margin-bottom: 5%;">
                                <span>
                                    <?php echo lang2('please_wait') ?> <br>
                                    <small>
                                        <stron>Estamos buscando seus leads</strong>
                                    </small>
                                </span>
                            </p>
                        </div>

                        <div class="row card-v2">
                            <div class="col-sm-6 col-lg mb-4 p-3 cd-body" style="max-width: 350px;">
                                <div class="card">
                                    <div class="card-header p-3 pt-2">
                                        <div style="background-image: linear-gradient(195deg, #fba528 0%, #e7c69e 100%);" class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                                            <i class="fas fa-users" style="font-size: 24px;color: #1f1f1f;"></i>
                                        </div>
                                        <div class="text-end pt-1">
                                            <p class="text-sm mb-0 text-capitalize">Total de empresas com esse filtro
                                            </p>
                                            <h4 style="font-size: 3rem; color: #f0b56b;" class="mb-0">{{totalResultado}}
                                            </h4>
                                        </div>
                                    </div>
                                    <hr class="dark horizontal my-0">

                                </div>
                            </div>
                        </div>

                        <md-content class="widget-fullwidth ciuis-body-loading bg-white" style="overflow: hidden;" id="contentMain">



                            <!--
                            <p class="Presults" style="margin-bottom: 0;">Foram localizadas <b ng-bind="totalResultado"></b> empresas com esses filtros.</p>
    -->
                            <p class="Presults" style="padding-top: 0;">Você possui <b ng-bind="credito"></b> créditos.
                            </p>
                            <md-table-container>
                                <table md-table md-progress="promise">
                                    <thead md-head md-order="company_list.order">
                                        <tr md-row>
                                            <th md-column style="text-align: center;">Importar <br>
                                                <input class="form-check-input checkLead" ng-model="importarTodos" ng-change="selecionaTudo()" type="checkbox" value="" id="defaultCheck1">
                                            </th>
                                            <!--<th md-column>Logo</th>-->
                                            <th md-column md-order-by="nm_fantasia"><?php echo lang2('name'); ?></th>
                                            <th md-column md-order-by="website">Site</th>
                                            <th md-column md-order-by="abertura">Anos de atividade</th>
                                            <th md-column md-order-by="municipio">Cidade</th>
                                            <th md-column md-order-by="uf"><span>Estado</th>
                                        </tr>
                                    </thead>

                                    <tbody md-body>
                                        <tr class="select_row" md-row ng-repeat="company in companies | orderBy: company_list.order | limitTo: company_list.limit : (company_list.page -1) * company_list.limit" class="cursor">
                                            <td md-cell style="padding-bottom: 3em;padding-top: 3em;">
                                                <div class="form-check" style="text-align: center;">
                                                    <input class="form-check-input checkLead checkL" ng-change="selecionaMaisUm(company.id_lead1)" ng-model="importar[company.id_lead1]" type="checkbox" value="" id="defaultCheck1">
                                                </div>
                                            </td>
                                            <!--
                                            <td md-cell ng-click="abreEmpresa(company.id_lead1)" ng-if="company.logo_url != null && company.logo_url != ''">
                                                <img data-src="{{company.logo_url}}" class="imgLogoC">
                                            </td>
                                            -->
                                            <td md-cell ng-click="abreEmpresa(company.id_lead1)">
                                                <strong><span style="color: #0716bf;" ng-bind="company.razao_social"></span></strong><br>
                                                <small ng-bind="company.nm_fantasia"></small>
                                            </td>
                                            <td md-cell>
                                                <a target="_blank" style="font-size: 13px;color: #0a56a1;" href="{{company.website}}" ng-bind="company.website"></a>
                                            </td>
                                            <td md-cell>
                                                <span ng-bind="company.anos_abertura"></span>
                                            </td>
                                            <td md-cell>
                                                <span ng-bind="company.municipio"></span>
                                            </td>
                                            <td md-cell>
                                                <span ng-bind="company.uf"></span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </md-table-container>
                            <md-table-pagination ng-show="companies.length > 0" md-limit="company_list.limit" md-limit-options="limitOptions" md-page="company_list.page" md-total="{{companies.length}}"></md-table-pagination>


                        </md-content>
                    </md-content>
                </md-tab>

                <md-tab label="Minha conta" ng-click="atualizaConta()">

                    <div class="row card-v2">
                        <div class="col-sm-6 col-lg mb-4 p-3 cd-body" style="max-width: 350px;">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div style="background-image: linear-gradient(195deg, #fba528 0%, #e7c69e 100%);" class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="fas fa-wallet" style="font-size: 24px;color: #1f1f1f;"></i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Total gasto</p>
                                        <h4 style="font-size: 3rem; color: #f0b56b;" class="mb-0" ng-bind-html="totalGasto | currencyFormat:cur_code:null:true:cur_lct"></h4>
                                    </div>
                                </div>
                                <hr class="dark horizontal my-0">

                            </div>
                        </div>
                    </div>


                    <md-content class="md-padding">
                        <md-table-container>
                            <table md-table md-progress="promise">
                                <thead md-head>
                                    <tr md-row>
                                        <th md-column md-order-by="nm_fantasia">#</th>
                                        <th md-column md-order-by="nm_fantasia">Data</th>
                                        <th md-column md-order-by="website">Referente</th>
                                        <th md-column md-order-by="abertura">Valor</th>
                                    </tr>
                                </thead>

                                <tbody md-body>
                                    <tr class="select_row" md-row ng-repeat="fatura in faturas" class="cursor">
                                        <td md-cell>
                                            <strong><span style="color: #0716bf;" ng-bind="fatura.id_fatura"></span></strong><br>
                                        </td>
                                        <td md-cell>
                                            <span ng-bind="fatura.data"></span>
                                        </td>
                                        <td md-cell>
                                            <span ng-bind="fatura.referencia"></span>
                                        </td>
                                        <td md-cell>
                                            <span ng-bind-html="fatura.valor | currencyFormat:cur_code:null:true:cur_lct"></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </md-table-container>
                    </md-content>

                </md-tab>
                <?php
                if ($user_data['super_admin'] == "1") {
                ?>
                    <md-tab label="Admin" ng-click="carregaAdmin()">
                        <div class="main-content container-fluid col-xs-12 col-md-8 col-lg-8">
                            <md-content class="md-padding">
                                <div class='filtrosLeads' style="min-height: 0;padding: 0em;">
                                    <md-content class="bg-white" ng-cloak>

                                        <md-input-container class="md-block col-md-3 col-12">
                                            <label>Nome da empresa</label>
                                            <md-select ng-model="filtros.nome_empresa" ng-change="getResultadosAdmin()" multiple>
                                                <md-select-header>
                                                    <input type="search" onkeydown="mdSelectOnKeyDownOverride(event);" ng-change="get_nomes(filtros.nome_empresa_input);" ng-model="filtros.nome_empresa_input" class="form-control" placeholder="Digite a menos 1 caractere para buscar." />
                                                </md-select-header>
                                                <md-option ng-value="nome_empresa.text" ng-if="!selecionados.nome_empresas.includes(nome_empresa.text)" ng-click="selecionados.nome_empresas.push(nome_empresa.text)" ng-repeat="nome_empresa in todos_nome_empresas">{{nome_empresa.text}}</md-option>
                                                <md-option ng-value="nome_empresa" ng-repeat="nome_empresa in selecionados.nome_empresas">{{nome_empresa}}</md-option>
                                            </md-select>
                                        </md-input-container>

                                        <md-input-container class="md-block col-md-3 col-12">
                                            <label>Ramos</label>
                                            <md-select ng-model="filtros.ramo" ng-change="getResultadosAdmin()" multiple>
                                                <md-select-header>
                                                    <input type="search" onkeydown="mdSelectOnKeyDownOverride(event);" ng-change="get_ramos2(filtros.ramo_input);" ng-model="filtros.ramo_input" class="form-control" placeholder="Digite a menos 3 caractere para buscar." />
                                                </md-select-header>
                                                <md-option ng-value="ramo.text" ng-if="!selecionados.ramos.includes(ramo.text)" ng-click="selecionados.ramos.push(ramo.text)" ng-repeat="ramo in todos_ramos">{{ramo.text}}</md-option>
                                                <md-option ng-value="ramo" ng-repeat="ramo in selecionados.ramos">{{ramo}}</md-option>
                                            </md-select>
                                        </md-input-container>

                                        <md-input-container class="md-block col-md-3 col-12">
                                            <label>Estado</label>
                                            <md-select placeholder="Estado" id="Estado" ng-model="filtros.estado" ng-change="getResultadosAdmin()" style="min-width: 200px;">
                                                <md-option value="-1">Todos</md-option>
                                                <md-option ng-value="estado" ng-repeat="estado in ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO']">{{estado}}</md-option>
                                            </md-select>
                                        </md-input-container>

                                        <md-input-container class="md-block col-md-3 col-12">
                                            <label>Cidade</label>
                                            <md-select ng-model="filtros.cidade" ng-change="getResultadosAdmin()">
                                                <md-select-header>
                                                    <input type="search" onkeydown="mdSelectOnKeyDownOverride(event);" ng-change="get_cidade(filtros.cidade_input);" ng-model="filtros.cidade_input" class="form-control" placeholder="Digite a menos 1 caractere para buscar." />
                                                </md-select-header>
                                                <md-option ng-value="cidade.text" ng-if="!selecionados.cidades.includes(cidade.text)" ng-click="selecionados.cidades.push(cidade.text)" ng-repeat="cidade in todas_cidades">{{cidade.text}}</md-option>
                                                <md-option ng-value="cidade" ng-repeat="cidade in selecionados.cidades">{{cidade}}</md-option>
                                            </md-select>
                                        </md-input-container>

                                        <md-input-container class="md-block col-md-3 col-12">
                                            <label>Porte</label>
                                            <md-select placeholder="Porte" id="porte" ng-model="filtros.porte" ng-change="getResultadosAdmin()" style="min-width: 200px;">
                                                <md-option value="GRANDE PORTE">GRANDE PORTE</md-option>
                                                <md-option value="MÉDIO PORTE">MÉDIO PORTE</md-option>
                                                <md-option value="MICRO EMPRESA">MICRO EMPRESA</md-option>
                                                <md-option value="PEQUENO PORTE">PEQUENO PORTE</md-option>
                                            </md-select>
                                        </md-input-container>

                                        <md-input-container class="md-block col-md-3 col-12">
                                            <label>Matriz/Filial</label>
                                            <md-select placeholder="Matriz/Filial" id="matrizEFilial" ng-model="filtros.matrizEFilial" ng-change="getResultadosAdmin()" style="min-width: 200px;">
                                                <md-option value="-1">Matrizes & Filiais</md-option>
                                                <md-option value="M">Somente Matrizes</md-option>
                                                <md-option value="F">Somente Filiais</md-option>
                                            </md-select>
                                        </md-input-container>


                                        <md-input-container class="md-block col-md-3 col-12">
                                            <md-select placeholder="Faixa de capital social" id="faixa_capital" ng-model="filtros.faixa_capital" ng-change="getResultadosAdmin()" style="min-width: 200px;">
                                                <md-option value="-1">Todos</md-option>
                                                <md-option value="0|1000">0 - 1000</md-option>
                                                <md-option value="1000|10000">1000 - 10000</md-option>
                                                <md-option value="10000|100000">10000 - 100000</md-option>
                                                <md-option value="100000|1000000">100000 - 1000000</md-option>
                                                <md-option value="1000000|10000000000000">1000000+</md-option>
                                            </md-select>
                                        </md-input-container>


                                    </md-content>
                                </div>
                            </md-content>

                            <div ng-show="loader" layout-align="center center" class="text-center" id="circular_loader">
                                <!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
                                <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">

                                <p style="font-size: 15px;margin-bottom: 5%;">
                                    <span>
                                        <?php echo lang2('please_wait') ?> <br>
                                        <small><strong><?php echo lang2('loading') . ' ' . lang2('overview') . '...' ?></strong></small>
                                    </span>
                                </p>
                            </div>

                            <md-content class="widget-fullwidth ciuis-body-loading" style="overflow: hidden;" id="contentMain">
                                <md-card flex-xs flex-gt-xs="100" layout="column">
                                    <div layout-xs="column" layout="row" class="bg-white">
                                        <md-card flex-xs flex-gt-xs="20" layout="column" class="text-center card6" style="position: relative;">
                                            <a href="#">
                                                <md-card-title>
                                                    <md-card-title-text>
                                                        <img style="width: 50px; margin: 0 auto;" id="totalLeadsGerados" ng-show="report.totalLeadsGerados == ''" style="display: none;" src="<?= base_url('assets/img/loading.gif') ?>">
                                                        <span class="md-headline"><strong ng-bind="report.totalLeadsGerados"></strong></span>
                                                        <span class="md-subhead">Total de Leads gerados</span>
                                                    </md-card-title-text>
                                                </md-card-title>
                                            </a>
                                        </md-card>
                                    </div>
                                </md-card>
                            </md-content>

                            <md-content class="widget-fullwidth ciuis-body-loading" style="overflow: hidden;" id="contentMain">
                                <md-card flex-xs flex-gt-xs="100" layout="column" style="padding: 10px;">
                                    <div layout-xs="column" layout="row" class="bg-white">
                                        <button style="padding: 10px 15px;" ng-click="apagarAdmin(true)" class="btn-shadow d-inline-flex align-items-center btn2 btn-success " aria-label="Actions" ng-cloak>
                                            <md-icon style="font-size: 21px;" class="mdi mdi-delete"></md-icon> Apagar todos
                                            leads1
                                        </button>
                                    </div>

                                    <md-content class="bg-white" ng-cloak style="margin-top: 10px;">
                                        <md-input-container class="md-block col-md-3 col-12" style="float: left;">
                                            <label>Custo por lead</label>
                                            <input type="number" placeholder="Créditos no lead+1" ng-model="camposAdmin.custoLead" class="form-control">
                                        </md-input-container>

                                        <md-input-container class="md-block col-md-3 col-12" style="float: left;">
                                            <label>Custo por enriquecimento</label>
                                            <input type="number" placeholder="Créditos de enriquecimento" ng-model="camposAdmin.custoEnriquecimento" class="form-control">
                                        </md-input-container>

                                        <md-button ng-click="SalvarConfigs()" class="md-raised md-primary btn-report">
                                            <span>Salvar</span>
                                        </md-button>


                                    </md-content>


                                </md-card>


                            </md-content>

                            <md-content class="widget-fullwidth ciuis-body-loading bg-white" style="overflow: hidden;" id="contentMain">
                                <md-table-container>
                                    <table md-table md-progress="promise">
                                        <thead md-head>
                                            <tr md-row>
                                                <th md-column style="text-align: center;">Apagar<br>
                                                    <input class="form-check-input checkLead" ng-model="apagarTodos" ng-change="selecionaTudoAdmin()" type="checkbox" value="" id="defaultCheck1">
                                                </th>
                                                <th md-column>Logo</th>
                                                <th md-column md-order-by="nm_fantasia"><?php echo lang2('name'); ?></th>
                                                <th md-column md-order-by="website">Site</th>
                                                <th md-column md-order-by="abertura">Anos de atividade</th>
                                                <th md-column md-order-by="municipio">Cidade</th>
                                                <th md-column md-order-by="uf">Estado</th>
                                                <th md-column md-order-by="uf">Ações</th>
                                            </tr>
                                        </thead>

                                        <tbody md-body>
                                            <tr class="select_row" md-row ng-repeat="company in companiesAdmin" class="cursor">
                                                <td md-cell style="padding-bottom: 3em;padding-top: 3em;">
                                                    <div class="form-check" style="text-align: center;">
                                                        <input class="form-check-input checkLead checkL" ng-change="selecionaMaisUmAdmin()" ng-model="apagar[company.id_lead1]" type="checkbox" value="" id="defaultCheck1">
                                                    </div>
                                                </td>
                                                <td md-cell ng-click="abreEmpresa(company.id_lead1)" ng-if="company.logo_url != null && company.logo_url != ''">
                                                    <img data-src="{{company.logo_url}}" class="imgLogoC">
                                                </td>
                                                <td md-cell ng-click="abreEmpresa(company.id_lead1)">
                                                    <strong><span style="color: #0716bf;" ng-bind="company.razao_social"></span></strong><br>
                                                    <small ng-bind="company.nm_fantasia"></small>
                                                </td>
                                                <td md-cell>
                                                    <a target="_blank" style="font-size: 13px;color: #0a56a1;" href="{{company.website}}" ng-bind="company.website"></a>
                                                </td>
                                                <td md-cell>
                                                    <span ng-bind="company.anos_abertura"></span>
                                                </td>
                                                <td md-cell>
                                                    <span ng-bind="company.municipio"></span>
                                                </td>
                                                <td md-cell>
                                                    <span ng-bind="company.uf"></span>
                                                </td>

                                                <td md-cell>
                                                    <md-button style="width: 30px;height: 30px;" ng-click="update(company)" class="md-icon-button md-primary" aria-label="Actions" ng-cloak>
                                                        <md-icon style="font-size: 21px;" class="mdi mdi-edit"></md-icon>
                                                    </md-button>

                                                    <md-button style="width: 30px;height: 30px;" ng-click="delete(company.id_lead1)" class="md-icon-button md-primary" aria-label="Actions" ng-cloak>
                                                        <md-icon style="font-size: 21px;" class="mdi mdi-close"></md-icon>
                                                    </md-button>

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <nav aria-label="Navegação de página exemplo">
                                        <ul class="pagination" style="float: right; margin-bottom: 50px; margin-top: 25px;">
                                            <li class="page-item"><a class="page-link" href="javaScript:void(0)" ng-click="setPage(parseInt(pageAtual) - 1)">Anterior</a></li>
                                            <li ng-repeat="n in pages" ng-if="n > 0" class="page-item {{ n == pageAtual ? 'active' : '' }}"><a class="page-link" href="javaScript:void(0)" ng-click="setPage(n)">{{n}}</a></li>
                                            <li class="page-item"><a class="page-link" href="javaScript:void(0)" ng-click="setPage(parseInt(pageAtual) + 1)">Próximo</a></li>
                                        </ul>
                                    </nav>

                                </md-table-container>
                            </md-content>
                        </div>

                        <div class="main-content container-fluid col-xs-12 col-md-4 col-lg-4 md-pl-0 lead-left-bar">
                            <md-content class="md-padding" ng-show="!loader">
                                <div class="panel-default panel-table borderten lead-manager-head">
                                    <md-toolbar class="toolbar-white">
                                        <div class="md-toolbar-tools">
                                            <h2 flex md-truncate class="text-bold">Pacotes
                                                <md-button ng-click="editPacote()" class="md-icon-button pull-right" aria-label="New" ng-cloak>
                                                    <md-icon><i class="ion-android-add-circle text-success"></i></md-icon>
                                                </md-button>
                                            </h2>
                                        </div>
                                    </md-toolbar>

                                    <div class="tasks-status-stat" ng-cloak>
                                        <div class="widget-chart-container">
                                            <div class="widget-counter-group widget-counter-group-right">
                                                <md-table-container ng-show="pacotes.length > 0">
                                                    <table md-table md-progress="promise">
                                                        <thead md-head md-order="pacotes_list.order">
                                                            <tr md-row>
                                                                <th md-column md-order-by="nm_pacote"><span>Pacote</span>
                                                                </th>
                                                                <th md-column md-order-by="quantidade">
                                                                    <span>Quantidade</span>
                                                                </th>
                                                                <th md-column md-order-by="valor"><span>Valor</span></th>
                                                                <th md-column></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody md-body>
                                                            <tr class="select_row" md-row ng-repeat="pacote in pacotes" class="cursor">

                                                                <td md-cell>
                                                                    <strong><span ng-bind="pacote.nm_pacote"></span></strong>
                                                                </td>
                                                                <td md-cell>
                                                                    <span ng-bind="pacote.quantidade"></span>
                                                                </td>
                                                                <td md-cell>
                                                                    <span ng-bind-html="pacote.valor | currencyFormat:cur_code:null:true:cur_lct"></span>
                                                                </td>

                                                                <td md-cell>
                                                                    <md-button ng-click="removePacote(pacote.id_pacote)" class="md-icon-button pull-right" aria-label="Remove" ng-cloak>
                                                                        <md-icon style="font-size: 21px;" class="mdi mdi-close ng-scope material-icons text-danger"></md-icon>
                                                                    </md-button>
                                                                    <md-button ng-click="editPacote(pacote)" class="md-icon-button pull-right" aria-label="New" ng-cloak>
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
                            </md-content>
                        </div>
                    </md-tab>

                    <md-tab label="Personalização" ng-click="get_lead1_backgroud(true)">
                        <div class="main-content container-fluid col-xs-12 col-md-8 col-lg-8">
                            <div ng-show="loader" layout-align="center center" class="text-center" id="circular_loader">
                                <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">
                                <p style="font-size: 15px;margin-bottom: 5%;">
                                    <span>
                                        <?php echo lang2('please_wait') ?> <br>
                                        <small><strong><?php echo lang2('loading') . ' ' . lang2('overview') . '...' ?></strong></small>
                                    </span>
                                </p>
                            </div>

                            <md-content class="md-padding">
                                <md-content class="widget-fullwidth ciuis-body-loading">


                                    <md-content class="bg-white" ng-cloak style="padding-bottom: 20px;">

                                        <md-input-container class="md-block col-md-4 col-12">
                                            <label>Adicionar ramo</label>
                                            <md-select ng-model="filtrosL.ramo2">
                                                <md-select-header>
                                                    <input type="search" onkeydown="mdSelectOnKeyDownOverride(event);" ng-change="get_ramos3(filtro_ramo);" ng-model="filtro_ramo" class="form-control" placeholder="Digite a menos 3 caractere para buscar." />
                                                </md-select-header>
                                                <md-option ng-value="ramo.text" ng-click="ramo_de_atividade.push(ramo)" ng-repeat="ramo in todos_ramos2">{{ramo.text}}</md-option>

                                            </md-select>
                                        </md-input-container>

                                    </md-content>

                                </md-content>
                            </md-content>

                            <h2>Editar background</h2>
                            <md-content class="widget-fullwidth ciuis-body-loading bg-white" style="overflow: hidden;">
                                <div class="tasks-status-stat" ng-cloak>
                                    <div class="widget-chart-container">
                                        <div class="widget-counter-group widget-counter-group-right">
                                            <md-table-container>
                                                <table md-table md-progress="promise">
                                                    <thead md-head md-order="pacotes_list.order">
                                                        <tr md-row>
                                                            <th md-column md-order-by="nm_pacote">Ramo de atividade</th>
                                                            <th md-column md-order-by="quantidade">Background</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody md-body>
                                                        <tr class="select_row" md-row ng-repeat="ramo in ramo_de_atividade" class="cursor">
                                                            <td md-cell>
                                                                <strong><span ng-bind="ramo.nm_ramo != null ? ramo.nm_ramo : ramo.text"></span></strong>
                                                            </td>
                                                            <td md-cell>
                                                                <span ng-if="ramo.arquivo != null && viewFile[$index] == null"><b><a href="javaScript:void(0)" ng-click="viewFile[$index] = true">Alterar
                                                                            foto:</a></b> {{ramo.arquivo}}</span>
                                                                <span ng-if="ramo.arquivo == null"><b>Adicionar: </b></span>

                                                                <input ng-show="viewFile[$index] || ramo.arquivo == null" type="file" name="file_ramo" id="file_ramo" ng-on-change="upload_ramo($index)" accept="image/*" file-model="file_ramo[$index]">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </md-table-container>
                                        </div>
                                    </div>
                                </div>
                            </md-content>
                        </div>
                    </md-tab>

                <?php
                }
                ?>
            </md-tabs>
        </md-content>
        <br><br>
    </md-content>

    <div class="divBtnFix">
        <div class="btn btn-success">
            <input class="inputImport" type="number" ng-model="QtdSelecionadosManual" ng-change="SelecionaManual()"></input>

            <button type="button" ng-click="Import()">
                Importar empresas
            </button>
        </div>
    </div>

    <div class="divBtnFix" ng-show="selecionadosAdmin > 0">
        <button type="button" style="background: #f00;color: #fff;" ng-click="apagarAdmin()" class="btn">
            Apagar empresas <span class="badge badge-light"> {{selecionadosAdmin}}</span>
        </button>
    </div>


    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Import" ng-cloak style="width: 450px;">
        <md-toolbar class="md-theme-light" style="background:#262626">
            <div class="md-toolbar-tools">
                <md-button ng-click="close()" class="md-icon-button" aria-label="Close"><i class="ion-android-arrow-forward"></i></md-button>
                <md-truncate>Importar leads do lead1+</md-truncate>
            </div>
        </md-toolbar>
        <md-content>

            <div class="modal-body">



                <a style="padding: 10px;font-size: 13px;" ng-click="ExportCsvLeads()" class="btn btn-success btn-lg btn-block">Exportar em planilha</a>

                <p style="text-align: center; margin-top: 15px;">ou...</p>

                <md-input-container class="md-block">
                    <label><?php echo lang2('assigned'); ?></label>
                    <md-select placeholder="<?php echo lang2('choosestaff'); ?>" name="importassigned" ng-model="importassigned" style="min-width: 200px;" required>
                        <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
                    </md-select>
                </md-input-container>
                <br>

                <md-input-container class="md-block">
                    <label>Lead funil</label>
                    <md-select placeholder="" ng-model="import_funil_list" style="min-width: 200px;" ng-required>
                        <md-option ng-value="list.id_list" ng-repeat="list in leadslist">{{list.nm_list}}</md-option>
                    </md-select>
                </md-input-container>
                <br>

                <md-input-container class="md-block">
                    <label><?php echo lang2('status'); ?></label>
                    <md-select placeholder="<?php echo lang2('status'); ?>" name="importstatus" ng-model="importstatus" style="min-width: 200px;" ng-required>
                        <md-option ng-value="status.id" ng-repeat="status in leadslist[import_funil_list].leadstatuses">{{status.name}}</md-option>
                    </md-select>
                </md-input-container>
                <br>

                <md-input-container class="md-block">
                    <label><?php echo lang2('source'); ?></label>
                    <md-select placeholder="<?php echo lang2('source'); ?>" name="importsource" ng-model="importsource" style="min-width: 200px;" required>
                        <md-option selected ng-value="source.id" ng-repeat="source in leadssources">{{source.name}}</md-option>
                    </md-select>
                </md-input-container>

                <br>

                <md-input-container class="md-block">
                    <label>Enriquecer dados dos leads selecionados?</label>
                    <md-select placeholder="Enriquecer dados dos leads selecionados?" name="enriquecer" ng-model="enriquecer" style="min-width: 200px;" required>
                        <md-option selected ng-value="0">Não</md-option>
                        <md-option ng-value="1">Sim</md-option>
                    </md-select>
                </md-input-container>

            </div>

            <div class="modal-footer">
                <a ng-if="!loadingLeads" ng-click="ImportarLeads()" class="btn btn-success btn-lg btn-block">Importar</a>
                <span ng-if="loadingLeads" class="btn btn-success btn-lg btn-block">Importando Leads...</span>

            </div>

        </md-content>
    </md-sidenav>

    <div class="modal fade" id="listPacotesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pacotes
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </h5>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-4 col-md-4" ng-repeat="pacote in pacotes">
                            <div class="thumbnail" style="min-height: 250px;">
                                <div class="caption">
                                    <h3 style="margin-top: 0;margin-bottom: 1em;text-align: center;">
                                        <b>{{pacote.nm_pacote}}</b>
                                    </h3>
                                    <div style="position: relative;min-height: 130px;">
                                        <p style="margin-bottom: 0;" ng-bind="pacote.detalhes"></p>
                                        <p style="font-size: 15px;position: absolute;bottom: 0;margin-bottom: 0;text-align: center;width: 100%;">
                                            Por apenas <span style="color: #f11818;font-weight: 600;" ng-bind-html="pacote.valor | currencyFormat:cur_code:null:true:cur_lct"></span>
                                        </p>
                                    </div>
                                    <button type="button" data-valor="{{pacote.valor}}" data-id="{{pacote.id_pacote}}" class="btn btn-outline-success btnCC">Contratar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pacoteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pacotes
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </h5>
                </div>
                <div class="modal-body">

                    <md-input-container class="md-block">
                        <label>Nome</label>
                        <input required type="text" ng-model="modalPacote.nm_pacote" class="form-control" placeholder="Nome" />
                    </md-input-container>


                    <div class="form-group">
                        <label>Detalhes</label>
                        <textarea required ng-model="modalPacote.detalhes" class="form-control" placeholder="Nome" rows="3"></textarea>
                    </div>

                    <md-input-container class="md-block" flex="25" style="float: left;width: 100%;margin-right: 10px;">
                        <label>Quantidade</label>
                        <input required type="number" ng-model="modalPacote.quantidade" class="form-control">
                    </md-input-container>

                    <md-input-container class="md-block" flex="70" style="float: left;width: 100%;">
                        <label>Valor</label>
                        <input required type="number" ng-model="modalPacote.valor" class="form-control">
                    </md-input-container>

                </div>
                <div class="modal-footer" style="margin-top: 30px;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" ng-click="salva_pacote()">Salvar</button>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pagar
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </h5>
                </div>
                <div class="modal-body">
                    <div class='checkout'>
                        <h2>Realizar pagamento</h2>
                        <form id="form-checkout" onsubmit="return false">

                            <div style="display: none;">
                                <input type="hidden" type="text" name="transactionAmount" id="transactionAmount" value="0" />
                                <input type="hidden" type="text" name="paymentMethodId" id="paymentMethodId" />
                                <input type="hidden" type="text" name="description" id="description" value="Venda de pacote Lead one" />
                                <input type="hidden" type="text" name="cardNumber" id="cardNumber" />

                                <select type="hidden" name="installments" id="installments" value="1">
                                </select>
                                <select type="hidden" name="docType" id="docType" value="CPF"></select>
                                <select name="issuer" id="issuer" value="1">
                                </select>

                            </div>

                            <input type="hidden" name="email" id="email" value="pagamentos@lead1crm.com" />
                            <input type="hidden" id="cardExpirationMonth" name="cardExpirationMonth" value="" />
                            <input type="hidden" id="cardExpirationYear" name="cardExpirationYear" value="" />
                            <input type='hidden' id='cardcpf2' />

                            <div id='payment' class='payment'>
                                <div class='card'>
                                    <div class='card-content'>
                                        <svg id='logo-visa' enable-background="new 0 0 50 70" height="70px" version="1.1" viewBox="0 0 50 50" width="70px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                            <g>
                                                <g>
                                                    <polygon clip-rule="evenodd" fill="#f4f5f9" fill-rule="evenodd" points="17.197,32.598 19.711,17.592 23.733,17.592     21.214,32.598   " />
                                                    <path clip-rule="evenodd" d="M35.768,17.967c-0.797-0.287-2.053-0.621-3.596-0.621    c-3.977,0-6.752,2.029-6.776,4.945c-0.023,2.154,1.987,3.358,3.507,4.08c1.568,0.738,2.096,1.201,2.076,1.861    c0,1.018-1.238,1.471-2.395,1.471c-1.604,0-2.455-0.232-3.773-0.787l-0.53-0.248l-0.547,3.348    c0.929,0.441,2.659,0.789,4.462,0.811c4.217,0,6.943-2.012,6.979-5.135c0.025-1.692-1.053-2.999-3.369-4.071    c-1.393-0.685-2.246-1.134-2.246-1.844c0-0.645,0.723-1.306,2.295-1.306c1.314-0.024,2.268,0.271,3.002,0.58l0.365,0.167    L35.768,17.967z" fill="#f4f5f9" fill-rule="evenodd" />
                                                    <path clip-rule="evenodd" d="M46.055,17.616h-3.102c-0.955,0-1.688,0.272-2.117,1.24    l-5.941,13.767h4.201c0,0,0.688-1.869,0.852-2.262c0.469,0,4.547,0,5.133,0c0.123,0.518,0.49,2.262,0.49,2.262h3.711    L46.055,17.616 M41.1,27.277c0.328-0.842,1.609-4.175,1.609-4.175c-0.041,0.043,0.328-0.871,0.529-1.43l0.256,1.281    c0,0,0.773,3.582,0.938,4.324H41.1z" fill="#f4f5f9" fill-rule="evenodd" />
                                                    <path clip-rule="evenodd" d="M13.843,17.616L9.905,27.842l-0.404-2.076    c-0.948-2.467-2.836-4.634-5.53-6.163l3.564,12.995h4.243l6.312-14.982H13.843z" fill="#f4f5f9" fill-rule="evenodd" />
                                                    <path clip-rule="evenodd" d="M7.232,17.174H0.755l-0.037,0.333    c5.014,1.242,8.358,4.237,9.742,7.841l-1.42-6.884C8.798,17.507,8.105,17.223,7.232,17.174L7.232,17.174z" fill="#f4f5f9" fill-rule="evenodd" />
                                                </g>
                                            </g>
                                        </svg>
                                        <h5>Numero do cartão</h5>
                                        <h6 id='label-cardnumber'>0000 0000 0000 0000</h6>
                                        <h5>Expiração<span>CVC</span></h5>
                                        <h6><span style="margin-left: 0;" id='label-cardexpiration'>00 /
                                                0000</span><span id="label-cvv">000</span></h6>
                                    </div>
                                    <div class='wave'></div>
                                </div>
                                <div class='card-form'>
                                    <p class='field' style="width: 100%;">
                                        <svg id='i-cardfront' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 28 28;" xml:space="preserve" width="28px" height="28px">
                                            <g>
                                                <path d="M471.5,88h-432C17.72,88,0,105.72,0,127.5v256C0,405.28,17.72,423,39.5,423h432c21.78,0,39.5-17.72,39.5-39.5v-256   C511,105.72,493.28,88,471.5,88z M496,383.5c0,13.509-10.991,24.5-24.5,24.5h-432C25.991,408,15,397.009,15,383.5v-256   c0-13.509,10.991-24.5,24.5-24.5h432c13.509,0,24.5,10.991,24.5,24.5V383.5z" fill="#dddfe6" />
                                                <path d="M239.5,352h-176c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5,7.5,7.5h176c4.142,0,7.5-3.358,7.5-7.5S243.642,352,239.5,352z" fill="#dddfe6" />
                                                <path d="M343.5,352h-72c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5,7.5,7.5h72c4.142,0,7.5-3.358,7.5-7.5S347.642,352,343.5,352z" fill="#dddfe6" />
                                                <path d="M79.5,239h48c12.958,0,23.5-10.542,23.5-23.5v-32c0-12.958-10.542-23.5-23.5-23.5h-48C66.542,160,56,170.542,56,183.5v32   C56,228.458,66.542,239,79.5,239z M136,183.5v8.5h-8.5c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5,7.5,7.5h8.5v8.5   c0,4.687-3.813,8.5-8.5,8.5H111v-49h16.5C132.187,175,136,178.813,136,183.5z M79.5,175H96v49H79.5c-4.687,0-8.5-3.813-8.5-8.5V207   h8.5c4.142,0,7.5-3.358,7.5-7.5s-3.358-7.5-7.5-7.5H71v-8.5C71,178.813,74.813,175,79.5,175z" fill="#dddfe6" />
                                                <path d="M63.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16C56,315.642,59.358,319,63.5,319   z" fill="#dddfe6" />
                                                <path d="M80,295.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5S80,291.358,80,295.5z" fill="#dddfe6" />
                                                <path d="M104,295.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5S104,291.358,104,295.5z" fill="#dddfe6" />
                                                <path d="M128,295.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5S128,291.358,128,295.5z" fill="#dddfe6" />
                                                <path d="M167.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C160,315.642,163.358,319,167.5,319z" fill="#dddfe6" />
                                                <path d="M191.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C184,315.642,187.358,319,191.5,319z" fill="#dddfe6" />
                                                <path d="M215.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C208,315.642,211.358,319,215.5,319z" fill="#dddfe6" />
                                                <path d="M239.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C247,291.358,243.642,288,239.5,288z" fill="#dddfe6" />
                                                <path d="M271.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C264,315.642,267.358,319,271.5,319z" fill="#dddfe6" />
                                                <path d="M295.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C288,315.642,291.358,319,295.5,319z" fill="#dddfe6" />
                                                <path d="M319.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C312,315.642,315.358,319,319.5,319z" fill="#dddfe6" />
                                                <path d="M343.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C351,291.358,347.642,288,343.5,288z" fill="#dddfe6" />
                                                <path d="M375.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C383,291.358,379.642,288,375.5,288z" fill="#dddfe6" />
                                                <path d="M399.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C407,291.358,403.642,288,399.5,288z" fill="#dddfe6" />
                                                <path d="M423.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C431,291.358,427.642,288,423.5,288z" fill="#dddfe6" />
                                                <path d="M447.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C455,291.358,451.642,288,447.5,288z" fill="#dddfe6" />
                                                <path d="M415.5,160h-48c-21.78,0-39.5,17.72-39.5,39.5s17.72,39.5,39.5,39.5h48c21.78,0,39.5-17.72,39.5-39.5S437.28,160,415.5,160   z M415.5,224h-48c-13.509,0-24.5-10.991-24.5-24.5s10.991-24.5,24.5-24.5h48c13.509,0,24.5,10.991,24.5,24.5S429.009,224,415.5,224   z" fill="#dddfe6" />
                                            </g>
                                        </svg>
                                        <input type='text' id='cardname' name='cardname' data-checkout="cardholderName" placeholder='Ex: joão da silva' title='Nome' />
                                    </p>

                                    <p class='field' style="width: 65%;">
                                        <svg id='i-cardfront' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 28 28;" xml:space="preserve" width="28px" height="28px">
                                            <g>
                                                <path d="M471.5,88h-432C17.72,88,0,105.72,0,127.5v256C0,405.28,17.72,423,39.5,423h432c21.78,0,39.5-17.72,39.5-39.5v-256   C511,105.72,493.28,88,471.5,88z M496,383.5c0,13.509-10.991,24.5-24.5,24.5h-432C25.991,408,15,397.009,15,383.5v-256   c0-13.509,10.991-24.5,24.5-24.5h432c13.509,0,24.5,10.991,24.5,24.5V383.5z" fill="#dddfe6" />
                                                <path d="M239.5,352h-176c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5,7.5,7.5h176c4.142,0,7.5-3.358,7.5-7.5S243.642,352,239.5,352z" fill="#dddfe6" />
                                                <path d="M343.5,352h-72c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5,7.5,7.5h72c4.142,0,7.5-3.358,7.5-7.5S347.642,352,343.5,352z" fill="#dddfe6" />
                                                <path d="M79.5,239h48c12.958,0,23.5-10.542,23.5-23.5v-32c0-12.958-10.542-23.5-23.5-23.5h-48C66.542,160,56,170.542,56,183.5v32   C56,228.458,66.542,239,79.5,239z M136,183.5v8.5h-8.5c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5,7.5,7.5h8.5v8.5   c0,4.687-3.813,8.5-8.5,8.5H111v-49h16.5C132.187,175,136,178.813,136,183.5z M79.5,175H96v49H79.5c-4.687,0-8.5-3.813-8.5-8.5V207   h8.5c4.142,0,7.5-3.358,7.5-7.5s-3.358-7.5-7.5-7.5H71v-8.5C71,178.813,74.813,175,79.5,175z" fill="#dddfe6" />
                                                <path d="M63.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16C56,315.642,59.358,319,63.5,319   z" fill="#dddfe6" />
                                                <path d="M80,295.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5S80,291.358,80,295.5z" fill="#dddfe6" />
                                                <path d="M104,295.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5S104,291.358,104,295.5z" fill="#dddfe6" />
                                                <path d="M128,295.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5S128,291.358,128,295.5z" fill="#dddfe6" />
                                                <path d="M167.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C160,315.642,163.358,319,167.5,319z" fill="#dddfe6" />
                                                <path d="M191.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C184,315.642,187.358,319,191.5,319z" fill="#dddfe6" />
                                                <path d="M215.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C208,315.642,211.358,319,215.5,319z" fill="#dddfe6" />
                                                <path d="M239.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C247,291.358,243.642,288,239.5,288z" fill="#dddfe6" />
                                                <path d="M271.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C264,315.642,267.358,319,271.5,319z" fill="#dddfe6" />
                                                <path d="M295.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C288,315.642,291.358,319,295.5,319z" fill="#dddfe6" />
                                                <path d="M319.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C312,315.642,315.358,319,319.5,319z" fill="#dddfe6" />
                                                <path d="M343.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C351,291.358,347.642,288,343.5,288z" fill="#dddfe6" />
                                                <path d="M375.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C383,291.358,379.642,288,375.5,288z" fill="#dddfe6" />
                                                <path d="M399.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C407,291.358,403.642,288,399.5,288z" fill="#dddfe6" />
                                                <path d="M423.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C431,291.358,427.642,288,423.5,288z" fill="#dddfe6" />
                                                <path d="M447.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C455,291.358,451.642,288,447.5,288z" fill="#dddfe6" />
                                                <path d="M415.5,160h-48c-21.78,0-39.5,17.72-39.5,39.5s17.72,39.5,39.5,39.5h48c21.78,0,39.5-17.72,39.5-39.5S437.28,160,415.5,160   z M415.5,224h-48c-13.509,0-24.5-10.991-24.5-24.5s10.991-24.5,24.5-24.5h48c13.509,0,24.5,10.991,24.5,24.5S429.009,224,415.5,224   z" fill="#dddfe6" />
                                            </g>
                                        </svg>
                                        <input type='text' id='cardNumber2' name='cardNumber2' placeholder='1234 5678 9123 4567' title='Numero do cartão' />
                                    </p>
                                    <p class='field space' style="width: calc(35% - 5px);margin-left: 5px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="i-calendar" x="0px" y="0px" viewBox="0 0 191.259 191.259" style="enable-background:new 0 0 191.259 191.259;" xml:space="preserve" width="28px" height="28px">
                                            <g>
                                                <circle cx="59.768" cy="83.634" r="3.985" fill="#dddfe6" />
                                                <circle cx="83.676" cy="83.634" r="3.985" fill="#dddfe6" />
                                                <circle cx="107.583" cy="83.634" r="3.985" fill="#dddfe6" />
                                                <circle cx="35.861" cy="107.541" r="3.984" fill="#dddfe6" />
                                                <circle cx="59.768" cy="107.541" r="3.984" fill="#dddfe6" />
                                                <circle cx="83.676" cy="107.541" r="3.984" fill="#dddfe6" />
                                                <circle cx="107.583" cy="107.541" r="3.984" fill="#dddfe6" />
                                                <circle cx="155.398" cy="107.541" r="3.984" fill="#dddfe6" />
                                                <circle cx="131.49" cy="83.634" r="3.985" fill="#dddfe6" />
                                                <circle cx="155.398" cy="83.634" r="3.985" fill="#dddfe6" />
                                                <circle cx="35.861" cy="131.449" r="3.985" fill="#dddfe6" />
                                                <circle cx="59.768" cy="131.449" r="3.985" fill="#dddfe6" />
                                                <circle cx="83.676" cy="131.449" r="3.985" fill="#dddfe6" />
                                                <circle cx="107.583" cy="131.449" r="3.985" fill="#dddfe6" />
                                                <circle cx="131.49" cy="131.449" r="3.985" fill="#dddfe6" />
                                                <circle cx="155.398" cy="131.449" r="3.985" fill="#dddfe6" />
                                                <circle cx="35.861" cy="155.356" r="3.985" fill="#dddfe6" />
                                                <circle cx="59.768" cy="155.356" r="3.985" fill="#dddfe6" />
                                                <circle cx="83.676" cy="155.356" r="3.985" fill="#dddfe6" />
                                                <circle cx="107.583" cy="155.356" r="3.985" fill="#dddfe6" />
                                                <path d="M131.49,119.495c6.603,0,11.954-5.351,11.954-11.954s-5.351-11.954-11.954-11.954   c-6.603,0-11.954,5.351-11.954,11.954S124.887,119.495,131.49,119.495z M131.49,103.557c2.199,0,3.985,1.786,3.985,3.984   s-1.786,3.984-3.985,3.984s-3.984-1.786-3.984-3.984S129.292,103.557,131.49,103.557z" fill="#dddfe6" />
                                                <path d="M175.321,15.98h-7.969v-3.985c0-6.601-5.354-11.954-11.954-11.954   c-6.603,0-11.954,5.352-11.954,11.954v3.985h-95.63v-3.985c0-6.601-5.354-11.954-11.954-11.954   c-6.603,0-11.954,5.352-11.954,11.954v3.985h-7.969C7.136,15.98,0,23.116,0,31.918v15.854v7.969v119.537   c0,8.802,7.136,15.938,15.938,15.938h159.382c8.802,0,15.938-7.136,15.938-15.938V55.742v-7.969V31.918   C191.259,23.116,184.123,15.98,175.321,15.98z M151.413,23.949V15.98v-3.985c0-2.201,1.782-3.985,3.985-3.985   c2.198,0,3.984,1.784,3.984,3.985v3.985v7.969v3.984c0,2.2-1.786,3.985-3.984,3.985c-2.202,0-3.985-1.784-3.985-3.985V23.949z    M31.876,23.949V15.98v-3.985c0-2.201,1.782-3.985,3.985-3.985c2.199,0,3.985,1.784,3.985,3.985v3.985v7.969v3.984   c0,2.2-1.786,3.985-3.985,3.985c-2.202,0-3.985-1.784-3.985-3.985V23.949z M183.29,175.279c0,4.399-3.564,7.969-7.969,7.969H15.938   c-4.405,0-7.969-3.57-7.969-7.969V55.742H183.29V175.279z M183.29,47.773H7.969V31.918c0-4.403,3.564-7.969,7.969-7.969h7.969   v3.984c0,6.601,5.35,11.954,11.954,11.954c6.6,0,11.954-5.352,11.954-11.954v-3.984h95.63v3.984c0,6.601,5.35,11.954,11.954,11.954   c6.599,0,11.954-5.352,11.954-11.954v-3.984h7.969c4.405,0,7.969,3.566,7.969,7.969V47.773z" fill="#dddfe6" />
                                            </g>
                                        </svg>
                                        <input type='text' id='cardexpiration' name='cardexpiration' placeholder="MM / YYYY" title='Expiração' />
                                    </p>
                                    <p class='field' style="width: 35%;">
                                        <svg id='i-cardback' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 28 28;" xml:space="preserve" width="28px" height="28px">
                                            <g>
                                                <path d="M63.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16C71,291.358,67.642,288,63.5,288   z" fill="#dddfe6" />
                                                <path d="M87.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16C95,291.358,91.642,288,87.5,288   z" fill="#dddfe6" />
                                                <path d="M111.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C119,291.358,115.642,288,111.5,288z" fill="#dddfe6" />
                                                <path d="M135.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C143,291.358,139.642,288,135.5,288z" fill="#dddfe6" />
                                                <path d="M167.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C160,315.642,163.358,319,167.5,319z" fill="#dddfe6" />
                                                <path d="M199,311.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5S199,315.642,199,311.5z" fill="#dddfe6" />
                                                <path d="M223,311.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5S223,315.642,223,311.5z" fill="#dddfe6" />
                                                <path d="M239.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C247,291.358,243.642,288,239.5,288z" fill="#dddfe6" />
                                                <path d="M271.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C264,315.642,267.358,319,271.5,319z" fill="#dddfe6" />
                                                <path d="M303,311.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5S303,315.642,303,311.5z" fill="#dddfe6" />
                                                <path d="M327,311.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5S327,315.642,327,311.5z" fill="#dddfe6" />
                                                <path d="M351,311.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5S351,315.642,351,311.5z" fill="#dddfe6" />
                                                <path d="M383,311.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5S383,315.642,383,311.5z" fill="#dddfe6" />
                                                <path d="M407,311.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5S407,315.642,407,311.5z" fill="#dddfe6" />
                                                <path d="M431,311.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5S431,315.642,431,311.5z" fill="#dddfe6" />
                                                <path d="M447.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C455,291.358,451.642,288,447.5,288z" fill="#dddfe6" />
                                                <path d="M447.5,216h-384C50.542,216,40,226.542,40,239.5v8c0,12.958,10.542,23.5,23.5,23.5h384c12.958,0,23.5-10.542,23.5-23.5v-8   C471,226.542,460.458,216,447.5,216z M456,247.5c0,4.687-3.813,8.5-8.5,8.5h-384c-4.687,0-8.5-3.813-8.5-8.5v-8   c0-4.687,3.813-8.5,8.5-8.5h384c4.687,0,8.5,3.813,8.5,8.5V247.5z" fill="#dddfe6" />
                                                <path d="M447.5,352h-176c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5,7.5,7.5h176c4.142,0,7.5-3.358,7.5-7.5S451.642,352,447.5,352z" fill="#dddfe6" />
                                                <path d="M239.5,352h-72c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5,7.5,7.5h72c4.142,0,7.5-3.358,7.5-7.5S243.642,352,239.5,352z" fill="#dddfe6" />
                                                <path d="M511,159.498V127.5c0-21.78-17.72-39.5-39.5-39.5h-432C17.72,88,0,105.72,0,127.5v47.998c0,0.001,0,0.003,0,0.005V383.5   C0,405.28,17.72,423,39.5,423h432c21.78,0,39.5-17.72,39.5-39.5V159.502C511,159.501,511,159.499,511,159.498z M496,184h-6.394   l6.394-6.394V184z M449.606,184l41-41H496v13.394L468.394,184H449.606z M409.606,184l41-41h18.787l-41,41H409.606z M369.606,184   l41-41h18.787l-41,41H369.606z M329.606,184l41-41h18.787l-41,41H329.606z M289.606,184l41-41h18.787l-41,41H289.606z M249.606,184   l41-41h18.787l-41,41H249.606z M209.606,184l41-41h18.787l-41,41H209.606z M169.606,184l41-41h18.787l-41,41H169.606z M129.606,184   l41-41h18.787l-41,41H129.606z M89.606,184l41-41h18.787l-41,41H89.606z M49.606,184l41-41h18.787l-41,41H49.606z M15,184v-5.394   L50.606,143h18.787l-41,41H15z M15,143h14.394L15,157.394V143z M39.5,103h432c13.509,0,24.5,10.991,24.5,24.5v0.5h-8.497   c-0.002,0-0.003,0-0.005,0h-39.995c-0.002,0-0.003,0-0.005,0h-39.995c-0.002,0-0.003,0-0.005,0h-39.995c-0.002,0-0.003,0-0.005,0   h-39.995c-0.002,0-0.003,0-0.005,0h-39.995c-0.002,0-0.004,0-0.005,0h-39.995c-0.001,0-0.003,0-0.005,0h-39.995   c-0.001,0-0.003,0-0.005,0h-39.995c-0.001,0-0.003,0-0.005,0h-39.995c-0.001,0-0.003,0-0.005,0H87.502c-0.001,0-0.003,0-0.005,0   H47.502c-0.001,0-0.003,0-0.005,0H15v-0.5C15,113.991,25.991,103,39.5,103z M471.5,408h-432C25.991,408,15,397.009,15,383.5V199   h481v184.5C496,397.009,485.009,408,471.5,408z" fill="#dddfe6" />
                                            </g>
                                        </svg>
                                        <input type='text' id='cardcvc' name='securityCode' data-checkout="securityCode" placeholder="123" title='CVC' />
                                    </p>

                                    <p class='field' style="width: calc(65% - 5px);margin-left: 5px;">
                                        <svg id='i-cardback' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 28 28;fill:#d9d7d7" xml:space="preserve" width="28px" height="28px">
                                            <g>
                                                <path d="m376.85 357.4 16.828 5.0938c-1.1289 4.7148-2.9062 8.6523-5.3398 11.82-2.4297 3.168-5.4531 5.5547-9.0586 7.1602-3.6016 1.6211-8.1875 2.418-13.746 2.418-6.7578 0-12.285-0.98438-16.551-2.9375-4.2812-1.9688-7.9883-5.4258-11.098-10.375-3.0977-4.9492-4.6602-11.27-4.6602-18.984 0-10.289 2.7344-18.203 8.2031-23.715 5.4688-5.5273 13.211-8.2891 23.207-8.2891 7.8281 0 13.977 1.5781 18.461 4.7461 4.4844 3.168 7.8125 8.0312 9.9844 14.586l-16.957 3.7773c-0.59375-1.8945-1.2148-3.2852-1.8672-4.168-1.0703-1.4609-2.3867-2.5898-3.9336-3.3867-1.5625-0.79688-3.3008-1.1875-5.2227-1.1875-4.3398 0-7.6836 1.75-9.9961 5.2539-1.75 2.6055-2.6328 6.6836-2.6328 12.254 0 6.9023 1.0547 11.617 3.1406 14.18 2.0977 2.5625 5.0352 3.8359 8.8125 3.8359 3.6758 0 6.4531-1.0273 8.332-3.0977 1.8789-2.0547 3.2422-5.0508 4.0938-8.9844zm26.07-36.75h31.918c6.9453 0 12.152 1.6484 15.609 4.9609 3.457 3.2969 5.1953 8.0156 5.1953 14.105 0 6.2773-1.8789 11.184-5.6562 14.715s-9.5352 5.2969-17.273 5.2969h-10.504v23.062h-19.285v-62.141zm19.285 26.492h4.7031c3.7031 0 6.293-0.63672 7.7969-1.9258 1.4922-1.2891 2.2422-2.9375 2.2422-4.9336 0-1.9531-0.65234-3.6172-1.9531-4.9609-1.2891-1.3594-3.7344-2.0391-7.3203-2.0391h-5.4688zm43.562-26.492h47.484v13.355h-28.199v10.852h24.074v12.543h-24.074v25.391h-19.285z" />
                                                <path d="m323.08 287.73h196.21c12.098 0 23.086 4.9414 31.051 12.906 7.9648 7.9648 12.906 18.953 12.906 31.051v40.113c0 12.098-4.9414 23.086-12.906 31.051s-18.953 12.906-31.051 12.906h-196.21c-12.098 0-23.086-4.9414-31.051-12.906s-12.906-18.953-12.906-31.051v-40.113c0-12.098 4.9414-23.086 12.906-31.051 7.9648-7.9648 18.953-12.906 31.051-12.906zm196.21 16.129h-196.21c-7.6445 0-14.602 3.1328-19.648 8.1797s-8.1797 12.004-8.1797 19.648v40.113c0 7.6445 3.1328 14.602 8.1797 19.648 5.0469 5.0469 12.004 8.1797 19.648 8.1797h196.21c7.6445 0 14.602-3.1328 19.648-8.1797 5.0469-5.0469 8.1797-12.004 8.1797-19.648v-40.113c0-7.6445-3.1328-14.602-8.1797-19.648s-12.004-8.1797-19.648-8.1797z" />
                                                <path d="m483.73 407.69v19.055c0 11.152-4.5547 21.281-11.895 28.621-7.3398 7.3398-17.473 11.895-28.621 11.895h-190.27c-11.152 0-21.281-4.5547-28.621-11.895-7.3398-7.3398-11.895-17.473-11.895-28.621v-293.49c0-11.152 4.5547-21.281 11.895-28.621 7.3398-7.3398 17.473-11.895 28.621-11.895h190.27c11.152 0 21.281 4.5547 28.621 11.895 7.3398 7.3398 11.895 17.473 11.895 28.621v162.54h-16.129v-162.54c0-6.6992-2.7461-12.797-7.1719-17.219-4.4258-4.4258-10.52-7.1719-17.219-7.1719h-190.27c-6.6992 0-12.797 2.7461-17.219 7.1719-4.4258 4.4258-7.1719 10.52-7.1719 17.219v293.49c0 6.6992 2.7461 12.797 7.1719 17.219 4.4258 4.4258 10.52 7.1719 17.219 7.1719h190.27c6.6992 0 12.797-2.7461 17.219-7.1719 4.4258-4.4258 7.1719-10.52 7.1719-17.219v-19.055z" />
                                                <path d="m483.73 407.69v19.055c0 11.152-4.5547 21.281-11.895 28.621-7.3398 7.3398-17.473 11.895-28.621 11.895h-190.27c-11.152 0-21.281-4.5547-28.621-11.895-7.3398-7.3398-11.895-17.473-11.895-28.621v-293.49c0-11.152 4.5547-21.281 11.895-28.621 7.3398-7.3398 17.473-11.895 28.621-11.895h190.27c11.152 0 21.281 4.5547 28.621 11.895 7.3398 7.3398 11.895 17.473 11.895 28.621v162.54h-16.129v-162.54c0-6.6992-2.7461-12.797-7.1719-17.219-4.4258-4.4258-10.52-7.1719-17.219-7.1719h-190.27c-6.6992 0-12.797 2.7461-17.219 7.1719-4.4258 4.4258-7.1719 10.52-7.1719 17.219v293.49c0 6.6992 2.7461 12.797 7.1719 17.219 4.4258 4.4258 10.52 7.1719 17.219 7.1719h190.27c6.6992 0 12.797-2.7461 17.219-7.1719 4.4258-4.4258 7.1719-10.52 7.1719-17.219v-19.055z" />
                                                <path d="m273.26 143.81c-4.4531 0-8.0625 3.6094-8.0625 8.0625s3.6094 8.0625 8.0625 8.0625h149.63c4.4531 0 8.0625-3.6094 8.0625-8.0625s-3.6094-8.0625-8.0625-8.0625z" />
                                                <path d="m448.3 462.94c-24.797 41.25-53.711 29.523-90.336 14.672-8.8086-3.5742-18.117-7.3477-27.523-10.359l4.9141-15.309c10.371 3.3203 19.766 7.1328 28.66 10.738 29.512 11.969 52.809 21.418 70.488-7.9922l13.797 8.2539zm-227.19-16.805-2.043-0.39453c-10.961-2.125-20.047-8.5234-25.855-17.121-5.8047-8.5938-8.3477-19.398-6.2266-30.332l25.477-131.47 15.812 3.0234-25.477 131.47c-1.2773 6.5859 0.26172 13.105 3.7695 18.301 3.5039 5.1875 8.9609 9.0469 15.523 10.316l2.043 0.39453zm27.328-350.02c26.629-41.625 52.969-30.27 89.391-14.566 8.7266 3.7617 18.105 7.8047 28.188 11.273l-5.2305 15.246c-10.633-3.6562-20.312-7.8281-29.32-11.711-29.16-12.57-50.254-21.664-69.48 8.3945l-13.543-8.6328zm228.63 18.152c10.961 2.125 20.047 8.5234 25.855 17.121 5.8047 8.5938 8.3477 19.395 6.2266 30.332l-25.586 132.05-15.812-3.0234 25.586-132.05c1.2773-6.5859-0.26172-13.105-3.7695-18.301-3.5039-5.1875-8.9609-9.0469-15.523-10.316z" />
                                                <path d="m273.26 189.54c-4.4531 0-8.0625 3.6094-8.0625 8.0625s3.6094 8.0625 8.0625 8.0625h149.63c4.4531 0 8.0625-3.6094 8.0625-8.0625s-3.6094-8.0625-8.0625-8.0625z" />
                                                <path d="m273.26 235.26c-4.4531 0-8.0625 3.6094-8.0625 8.0625 0 4.4531 3.6094 8.0625 8.0625 8.0625h149.63c4.4531 0 8.0625-3.6094 8.0625-8.0625 0-4.4531-3.6094-8.0625-8.0625-8.0625z" />
                                            </g>
                                        </svg>
                                        <input type='text' id='cardcpf' name="docNumber" data-checkout="docNumber" placeholder="CPF" title='CPF' />
                                    </p>


                                    <button class='button-cta' title='Confirme o pagamento'><span>Pagar</span></button>
                                </div>
                            </div>
                        </form>
                        <div id='paid' class='paid'>
                            <svg id='icon-paid' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 310.277 310.277" style="enable-background:new 0 0 310.277 310.277;" xml:space="preserve" width="180px" height="180px">
                                <g>
                                    <path d="M155.139,0C69.598,0,0,69.598,0,155.139c0,85.547,69.598,155.139,155.139,155.139   c85.547,0,155.139-69.592,155.139-155.139C310.277,69.598,240.686,0,155.139,0z M144.177,196.567L90.571,142.96l8.437-8.437   l45.169,45.169l81.34-81.34l8.437,8.437L144.177,196.567z" fill="#3ac569" />
                                </g>
                            </svg>
                            <h2>Pagamento completo!.</h2>
                            <h2>Obriado!</h2>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="ImportCustomersNav" ng-cloak style="width: 450px;">
        <md-toolbar class="md-theme-light" style="background:#262626">
            <div class="md-toolbar-tools">
                <md-button ng-click="close()" class="md-icon-button" aria-label="Close"><i class="ion-android-arrow-forward"></i></md-button>
                <md-truncate>Importar empresas</md-truncate>
            </div>
        </md-toolbar>
        <md-content>
            <?php helper('form');
            echo form_open_multipart('customers/customersimport'); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label for="name">
                        <?php echo lang2('choosecsvfile'); ?> <b>(modelo 1)</b>
                    </label>
                    <div class="file-upload">
                        <div class="file-select">
                            <div class="file-select-button" id="fileName"><span class="mdi mdi-accounts-list-alt"></span>
                                <?php echo lang2('attachment') ?>
                            </div>
                            <div class="file-select-name" id="noFile" style="font-size: 13px;font-family: arial;">
                                <?php echo lang2('notchoise') ?>
                            </div>
                            <input type="file" name="userfile" id="chooseFileImportLead" onchange="$('.file-select-name').html($(this)[0].files[0].name);" required="" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" file-model="customer_file">
                        </div>
                    </div>
                </div>

                <div ng-show="loaderUpload" layout-align="center center" class="text-center" id="circular_loader">
                    <!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
                    <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">

                    <p style="font-size: 15px;margin-bottom: 5%;">
                        <span>
                            <?php echo lang2('please_wait') ?> <br>
                            <small><strong><?php echo lang2('loading') . '...' ?> {{etapaUpload}}</strong> </small>
                        </span>
                    </p>
                </div>


            </div>
            <div class="modal-footer">
                <!--
                <a href="<?php echo base_url('uploads/samples/customerimport2.csv') ?>" class="btn btn-success pull-left"><?php echo lang2('downloadsample'); ?></a>
                -->
                <button style="font-size: 15px;" type="button" ng-click="importCustomer()" class="btn btn-default"><?php echo lang2('save'); ?></button>
            </div>
            <?php echo form_close(); ?>
            <div ng-show="importerror">
                <md-content>
                    <ul>
                        <li ng-repeat="error in errors">
                            <p><?php echo lang2('row') . ' ' ?>{{error.line}}<?php echo ' ' . lang2('importSkipError') ?>
                            </p>
                        </li>
                    </ul>
                </md-content>
            </div>
        </md-content>
    </md-sidenav>

    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Update_company" style="width: 450px;" ng-cloak>
        <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">
                <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>
                <h2 flex md-truncate><?php echo lang2('create') ?></h2>
            </div>
        </md-toolbar>
        <md-content>
            <md-content layout-padding>
                <div class="addLeadDiv">
                    <md-input-container class="md-block">
                        <label>CNPJ</label>
                        <input ng-model="company_update.cnpj">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Matriz ou Filial</label>
                        <input ng-model="company_update.matriz_ou_filial">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label><?php echo lang2('name'); ?> fantasia</label>
                        <input ng-model="company_update.nm_fantasia">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Razao social</label>
                        <input ng-model="company_update.razao_social">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Situação</label>
                        <input ng-model="company_update.situacao">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Data Situacao</label>
                        <input ng-model="company_update.data_situacao">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Natureza Juridica</label>
                        <input ng-model="company_update.natureza_juridica">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Abertura</label>
                        <input ng-model="company_update.abertura">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>CNAE</label>
                        <input ng-model="company_update.cnae">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Ramo de Atividade</label>
                        <input ng-model="company_update.ramo_de_atividade">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>CNAES Secundários</label>
                        <input ng-model="company_update.cnaes_secundarios">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Tipo Logradouro</label>
                        <input ng-model="company_update.tipo_logradouro">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Logradouro</label>
                        <input ng-model="company_update.logradouro">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Numero</label>
                        <input ng-model="company_update.numero">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Complemento</label>
                        <input ng-model="company_update.complemento">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Bairro</label>
                        <input ng-model="company_update.bairro">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>CEP</label>
                        <input ng-model="company_update.cep">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>UF</label>
                        <input ng-model="company_update.uf">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Municipio</label>
                        <input ng-model="company_update.municipio">
                    </md-input-container>

                    <md-content>
                        <md-input-container class="md-block" flex="25" style="float: left;width: 100%;margin-right: 10px;">
                            <label>DDD1</label>
                            <input ng-model="company_update.ddd1">
                        </md-input-container>

                        <md-input-container class="md-block" flex="70" style="float: left;width: 100%;">
                            <label>Telefone1</label>
                            <input ng-model="company_update.telefone1">
                        </md-input-container>
                    </md-content>
                    <md-content>
                        <md-input-container class="md-block" flex="25" style="float: left;width: 100%;margin-right: 10px;">
                            <label>DDD2</label>
                            <input ng-model="company_update.ddd2">
                        </md-input-container>

                        <md-input-container class="md-block" flex="70" style="float: left;width: 100%;">
                            <label>Telefone2</label>
                            <input ng-model="company_update.telefone2">
                        </md-input-container>
                    </md-content>
                    <md-content>
                        <md-input-container class="md-block" flex="25" style="float: left;width: 100%;margin-right: 10px;">
                            <label>DDD3</label>
                            <input ng-model="company_update.ddd3">
                        </md-input-container>

                        <md-input-container class="md-block" flex="70" style="float: left;width: 100%;">
                            <label>Telefone3</label>
                            <input ng-model="company_update.telefone3">
                        </md-input-container>
                    </md-content>
                    <md-content>
                        <md-input-container class="md-block" flex="25" style="float: left;width: 100%;margin-right: 10px;">
                            <label>DDD4</label>
                            <input ng-model="company_update.ddd4">
                        </md-input-container>

                        <md-input-container class="md-block" flex="70" style="float: left;width: 100%;">
                            <label>Telefone4</label>
                            <input ng-model="company_update.telefone4">
                        </md-input-container>
                    </md-content>
                    <md-content>
                        <md-input-container class="md-block" flex="25" style="float: left;width: 100%;margin-right: 10px;">
                            <label>DDD5</label>
                            <input ng-model="company_update.ddd5">
                        </md-input-container>

                        <md-input-container class="md-block" flex="70" style="float: left;width: 100%;">
                            <label>Telefone5</label>
                            <input ng-model="company_update.telefone5">
                        </md-input-container>
                    </md-content>

                    <md-input-container class="md-block">
                        <label>Insta</label>
                        <input ng-model="company_update.insta">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>E-mail1</label>
                        <input ng-model="company_update.email1">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>E-mail2</label>
                        <input ng-model="company_update.email2">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>E-mail3</label>
                        <input ng-model="company_update.email3">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>E-mail4</label>
                        <input ng-model="company_update.email4">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>E-mail5</label>
                        <input ng-model="company_update.email5">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Capital Social</label>
                        <input ng-model="company_update.capital_social">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Porte</label>
                        <input ng-model="company_update.porte">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Simples Nacional</label>
                        <input ng-model="company_update.simples_nacional">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>MEI</label>
                        <input ng-model="company_update.mei">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Faturamento Presumido</label>
                        <input ng-model="company_update.faturamento_presumido">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Socio1_Nome</label>
                        <input ng-model="company_update.socio1_nome">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Sócio1 CPF</label>
                        <input ng-model="company_update.socio1_cpf">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Socio2 Nome</label>
                        <input ng-model="company_update.socio2_nome">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Sócio2 CPF</label>
                        <input ng-model="company_update.socio2_cpf">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>logo url</label>
                        <input ng-model="company_update.logo_url">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Website</label>
                        <input ng-model="company_update.website">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Tagline</label>
                        <input ng-model="company_update.tagline">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Número Funcionários</label>
                        <input ng-model="company_update.numero_funcionarios">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Descrição</label>
                        <input ng-model="company_update.descricao">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Linkedin</label>
                        <input ng-model="company_update.url_linkedin">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Instagram</label>
                        <input ng-model="company_update.url_insta">
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Facebook</label>
                        <input ng-model="company_update.url_face">
                    </md-input-container>
            </md-content>

            <md-content class="layout-padding">
            </md-content>
            <md-content layout-padding>
                <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>
                    <md-button ng-click="saveCompany()" class="md-raised md-primary btn-report block-button" ng-disabled="saving == true">
                        <span ng-hide="saving == true"><?php echo lang2('save'); ?></span>
                        <md-progress-circular class="white" ng-show="saving == true" md-mode="indeterminate" md-diameter="20">
                        </md-progress-circular>
                    </md-button>
                </section>
            </md-content>
        </md-content>
    </md-sidenav>
</div>

<script type="text/javascript">
    var SCOPE;
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
<script type="text/javascript" src="<?php echo base_url('assets/js/lead1.js?v=p1.1.1012') ?>"></script>
<script src="<?php echo base_url('assets/lib/mercadopago/v2.js'); ?>"></script>
<!-- <script src="https://sdk.mercadopago.com/js/v2"></script>-->
<script>
    var Issuers = 0;
    var transactionAmount = "10.00";
    var paymentMethodId = '';
    var installments = 0;
    var token = '';
    var ID_PACOTE = 0;
    var cardForm = null;

    $(document).ready(function() {

        $('.select2Input').on('select2:selecting', function(e) {
            var id = $(this).attr('id');
            var val = $(this).val();
            var html = `<md-option value="${val}">${val}</md-option>`;
            $('#' + id).append(html);


            $('#' + id).find('[value="' + val + '"]').trigger('click');
        });



        $('.select2Func').select2({
            ajax: {
                url: '<?= base_url('api/get_staff'); ?>',
                dataType: 'json'
            }
        });



        $('#cardNumber2').mask('0000 0000 0000 0000');
        $('#cardexpiration').mask('00/00');
        $('#cardcvc').mask('000');
        $('#cardcpf').mask('000.000.000-00');

        $('#cardNumber2').keyup(function(event) {
            if ($(this).val().length == 0) {
                $('#label-cardnumber').html('0000 0000 0000 0000');
            } else {
                $('#label-cardnumber').html($(this).val());
                $('#cardNumber').val($(this).val().toString().replace(/[^0-9]/g, ''));
            }
        });

        $('#cardcpf').keyup(function(event) {
            var cpf = $('#cardcpf').val().toString().replace(/[^0-9]/g, '');
            $('#cardcpf2').val(cpf);
        });



        $('#cardexpiration').keyup(function(event) {
            if ($(this).val().length == 0) {
                $('#label-cardexpiration').html('00 / 0000');
            } else {
                if ($(this).val().length > 2) {
                    $('#cardExpirationMonth').val($(this).val().substring(0, 2));
                }
                if ($(this).val().length >= 5) {
                    $('#cardExpirationYear').val($(this).val().substring(3, 5));
                }
                $('#label-cardexpiration').html($(this).val());
            }
        });

        $('#cardcvc').keyup(function(event) {
            if ($(this).val().length == 0) {
                $('#label-cvv').html('000');
            } else {
                $('#label-cvv').html($(this).val());
            }
        });
        $(document).on('click', '.btnCC', function() {
            $('.modal').modal('hide');
            $('#checkoutModal').modal('show');
            var id = $(this).attr('data-id');
            var valor = $(this).attr('data-valor');
            console.log(id)
            ID_PACOTE = id;
            transactionAmount = valor;
            if (cardForm != null) {
                cardForm.unmount();
            }

            mountCardForm();
        });

        $('.button-cta').on('click', function() {
            var proceed = true;
            $(".field input").each(function() {
                $(this).parent().find('path').each(function() {
                    $(this).attr('fill', '#dddfe6');
                });

                if (!$.trim($(this).val())) {
                    $(this).parent().find('path').each(function() {
                        $(this).attr('fill', '#f1404b');
                        proceed = false;
                    });

                    if (!proceed) {
                        $(this).parent().find('svg').animate({
                            opacity: '0.1'
                        }, "slow");
                        $(this).parent().find('svg').animate({
                            opacity: '1'
                        }, "slow");
                        $(this).parent().find('svg').animate({
                            opacity: '0.1'
                        }, "slow");
                        $(this).parent().find('svg').animate({
                            opacity: '1'
                        }, "slow");
                    }
                }
            });

            if (proceed) {
                $('.field').find('path').each(function() {
                    $(this).attr('fill', '#3ac569');
                });
                $('#form-checkout').submit()
            }
        });


    });

    const mp = new MercadoPago("TEST-3699dbfb-4db9-4b25-bb25-043432f75f3e");

    function mountCardForm() {
        cardForm = mp.cardForm({
            amount: transactionAmount,
            iframe: false,
            form: {
                id: "form-checkout",
                cardNumber: {
                    id: "cardNumber2",
                },
                expirationDate: {
                    id: "cardexpiration",
                },
                securityCode: {
                    id: "cardcvc",
                },
                cardholderName: {
                    id: "cardname",
                },
                issuer: {
                    id: "issuer",
                },
                installments: {
                    id: "installments",
                },
                identificationType: {
                    id: "docType",
                },
                identificationNumber: {
                    id: "cardcpf2",
                },
                cardholderEmail: {
                    id: "email",
                },
            },
            callbacks: {
                onFormMounted: error => {
                    if (error) return console.warn("Form Mounted handling error: ", error);
                    console.log("Form mounted");
                },
                onSubmit: event => {
                    event.preventDefault();

                    const {
                        paymentMethodId: payment_method_id,
                        issuerId: issuer_id,
                        cardholderEmail: email,
                        amount,
                        token,
                        installments,
                        identificationNumber,
                        identificationType,
                    } = cardForm.getCardFormData();

                    data = {
                        token,
                        issuer_id,
                        payment_method_id,
                    };

                    sendRequest(data);
                    return;
                },
                onFetching: (resource) => {
                    console.log("Fetching resource: ", resource);
                }
            },
        });
    }


    function sendRequest(data) {
        $.ajax({
            url: '<?= base_url('lead1/requestCard') ?>',
            type: 'post',
            data: {
                token: data.token,
                id_pacote: ID_PACOTE,
                paymentMethodId: data.payment_method_id,
                issuer_id: data.issuer_id,
                Issuers: 1,
                cpf: $('#cardcpf').val().toString().replace(/[^0-9]/g, '')
            }
        }).done(function(msg) {
            msg = JSON.parse(msg);
            $('#cardNumber2').val('').change();
            $('#cardexpiration').val('').change();
            $('#cardcvc').val('').change();
            $('#cardname').val('').change();
            $('#cardcpf2').val('').change();
            $('#cardcpf').val('').change();
            $('#label-cardexpiration').html('00 / 0000');
            $('#label-cardnumber').html('0000 0000 0000 0000');
            $('#label-cvv').html('000');

            if (msg.success == 200) {
                SCOPE.credito = msg.saldo;
                $('.payment').fadeToggle('slow', function() {
                    $('.paid').fadeToggle('slow', 'linear');
                });

                $(this).parent().find('path').each(function() {
                    $(this).attr('fill', '#dddfe6');
                });
            } else {
                showToast('Atenção', 'Ocorreu algum erro, tente novamente!', 'danger');
            }
        }).fail(function(jqXHR, textStatus, msg) {
            alert(msg);
        });
    }
</script>