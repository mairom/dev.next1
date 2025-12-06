<?php
$user_data = get_user();
$rebrand = load_config();
$login_Model = new App\Models\Login_Model;
?>

<!DOCTYPE html>
<html ng-app="Ciuis" lang="<?php echo lang2('lang_code'); ?>">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="<?php echo $rebrand['meta_description'] ?>">
    <meta name="keywords" content="<?php echo $rebrand['meta_keywords'] ?>">
    <link rel="shortcut icon" href="<?php echo base_url('assets/img/images/' . $rebrand['favicon_icon'] . ''); ?>">
    <title><?php echo $title; ?></title>


    <script src="<?php echo base_url('assets/lib/jquery/jquery.min.js'); ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('assets/lib/interactivegraph/js/jquery.flot.min.js'); ?>"></script>

    <script src="<?php echo base_url('assets/lib/angular/angular.min.js'); ?>"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
    <script type="text/javascript" src="<?= base_url('assets/lib/sortable/src/sortable.js'); ?>"></script>

    <script src="<?php echo base_url('assets/lib/angular/angular-animate.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/angular/angular-aria.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/angular/i18n/angular-locale_' . lang2('lang_code_dash') . '.js'); ?>"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.7.8/angular-sanitize.js"></script>
    <script src="<?php echo base_url('assets/lib/sweetalert2/dist/sweetalert2.all.min.js'); ?>"></script>


    <script src="<?php echo base_url('assets/lib/dom-to-image/dom-to-image.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/main.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/mask/jquery.mask.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/mask/jquery.mask2.min.js'); ?>"></script>

    <script src="https://d3js.org/d3.v3.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script src="<?= base_url('assets/lib/touchspin/jquery.bootstrap-touchspin.js'); ?>"></script>


    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/lib/data-table/md-data-table.min.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/fontawesome/css/all.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/lib/sweetalert2/dist/sweetalert2.min.css'); ?>">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans+Condensed:700,300,300italic' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/graph.css?v=1.6'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/lib/interactivegraph/css/graph2.css?v=2.2'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/lib/touchspin/jquery.bootstrap-touchspin.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/ciuis.css?v=1.3.4'); ?>" type="text/css" />

    <script>
        var BASE_URL = "<?php echo base_url(); ?>",
            update_error = "<?php echo lang2('update_error'); ?>",
            email_error = "<?php echo lang2('email_error'); ?>",
            ACTIVESTAFF = "<?php echo session()->get('usr_id'); ?>",
            SHOW_ONLY_ADMIN = "<?php if (session()->get('usr_id') && !$login_Model->if_admin()) {
                                    echo 'true';
                                } else echo 'false'; ?>",

            CURRENCY = "<?php echo session()->get('usr_id') ? currency : 'BRL' ?>",
            LOCATE_SELECTED = "<?php echo lang2('lang_code'); ?>",
            UPIMGURL = "<?php echo base_url('uploads/images/'); ?>",
            NTFTITLE = "<?php echo lang2('notification') ?>",
            INVMARKCACELLED = "<?php echo lang2('invoicecancelled') ?>",
            TICKSTATUSCHANGE = "<?php echo lang2('ticketstatuschanced') ?>",
            LEADMARKEDAS = "<?php echo lang2('leadmarkedas') ?>",
            LEADUNMARKEDAS = "<?php echo lang2('leadunmarkedas') ?>",
            TODAYDATE = "<?php echo date('Y.m.d ') ?>",
            LOGGEDINSTAFFID = "<?php echo session()->get('usr_id'); ?>",
            LOGGEDINSTAFFNAME = "<?php echo session()->get('staffname'); ?>",
            LOGGEDINSTAFFAVATAR = "<?php echo session()->get('staffavatar'); ?>",
            VOICENOTIFICATIONLANG = "<?php echo lang2('lang_code_dash'); ?>",
            initialLocaleCode = "<?php echo lang2('initial_locale_code'); ?>";
        var new_item = "<?php echo lang2('new'); ?>";
        var item_unit = "<?php echo lang2('unit'); ?>";

        $(document).ready(function() {
            InitMask();
        });

        function InitMask() {
            $(".money").maskMoney({
                prefix: '',
                allowNegative: true,
                thousands: '.',
                decimal: ',',
                affixesStay: false
            });
            $('.phone').mask('00000000000');
        }

        function moneyEua(v) {
            return v ? parseFloat(parseFloat(v.toString().replaceAll('.', '').replace(',', '').replace(/([0-9]{2})$/g, ".$1")).toFixed(2)) : 0;
        }
    </script>

    <style>
        @media screen and (max-width: 1355px) {
            .ciuis-body-content {
                max-width: 100% !important;
                width: 100% !important;
                padding-bottom: 0em !important;
            }

            .h1Title {
                max-width: 100% !important;
            }

            .content-main {
                max-width: 100% !important;
                width: 100% !important;
                margin-left: 0% !important;
                margin-right: 0% !important;
            }
        }

        .ciuis-body-content {
            float: left;
            width: 90%;
            margin: 0 auto;
            max-width: 1355px;
            padding-bottom: 2em;
            border-bottom: 1px solid #c9c9c9;
        }

        .h1Title {
            text-align: center;
            margin-bottom: 0;
            margin-top: 1em;
            float: left;
            width: 100%;
            max-width: 1355px;
        }

        .content-main {
            margin: 0 auto;
            width: 1355px;
            margin-left: 5%;
            margin-right: 5%;

        }

        .controlsPdf {
            position: fixed;
            width: calc(100% - 6px);
            background: #323639;
            padding: 1.1em;
            z-index: 999;
        }

        .controlsPdf button {
            float: right;
            font-size: 16px;
        }

        .btn:active,
        .btn.active {
            box-shadow: none;
            background: -webkit-linear-gradient(to top, #bfbfbf, #d5d5d5) !important;
            background: linear-gradient(to top, #bfbfbf, #d5d5d5) !important;
        }

        .btn:focus,
        .btn:active:focus,
        .btn.active:focus,
        .btn.focus,
        .btn:active.focus,
        .btn.active.focus {
            outline: 0 !important;
            outline-offset: 0 !important;
        }
    </style>

</head>

<body ng-controller="Ciuis_Controller">

    <div class='controlsPdf'>
        <button class="btn btn-secondary" onclick="gerarPdf()">Gerar pdf <i class="fa fa-print"></i></button>
    </div>

    <div style="margin-top:5em;">
        <div layout-align="center center" class="text-center" id="circular_loader" style="display:none">
            <!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
            <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">
            
            <p style="font-size: 15px;margin-bottom: 5%;">
                <span>
                    <?php echo lang2('please_wait') ?> <br>
                    <small><strong><?php echo lang2('loading') . ' ' . lang2('overview') . '...' ?></strong></small>
                </span>
            </p>
        </div>

        <div class="content-main" id="contentMain">
            <?php $appconfig = get_appconfig(); ?>
            <?php
            if (in_array('24', explode(',', $envio['permissoes']))) {
            ?>
                <h1 class="h1Title">Gráficos <b>Dash Fatura</b></h1>
                <div class="ciuis-body-content" ng-controller="DashInvoices_Controller">
                    <md-content class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">
                        <md-content class="widget-fullwidth ciuis-body-loading">
                            <md-card flex-xs flex-gt-xs="100" layout="column">
                                <div layout-xs="column" layout="row" class="bg-white">
                                    <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card1" style="position: relative;">
                                        <md-card-title>
                                            <md-card-title-text>
                                                <span class="md-headline"><strong ng-bind-html="report.fatBruto | currencyFormat:cur_code:null:true:cur_lct"></strong></span>
                                                <span class="md-subhead">Faturamento bruto</span>
                                            </md-card-title-text>
                                        </md-card-title>

                                    </md-card>

                                    <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card3" style="position: relative;">
                                        <md-card-title>
                                            <md-card-title-text>
                                                <span class="md-headline"><strong ng-bind-html="report.totalDepsECompras | currencyFormat:cur_code:null:true:cur_lct"></strong> </span>
                                                <span class="md-subhead">Total de Despesas & Compras</span>
                                            </md-card-title-text>
                                        </md-card-title>

                                    </md-card>

                                    <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card4" style="position: relative;">
                                        <md-card-title>
                                            <md-card-title-text>
                                                <span class="md-headline"><strong ng-bind-html="report.RestLiquid | currencyFormat:cur_code:null:true:cur_lct"></strong></span>
                                                <span class="md-subhead">Resultado líquido</span>
                                            </md-card-title-text>
                                        </md-card-title>

                                    </md-card>

                                    <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card5" style="position: relative;">
                                        <md-card-title>
                                            <md-card-title-text>
                                                <span class="md-headline"><strong ng-bind="report.MargemRestLiq"></strong></span>
                                                <span class="md-subhead">Margem do lucro líquido</span>
                                            </md-card-title-text>
                                        </md-card-title>

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
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Faturamento bruto</b>
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
                            </div>
                            <div layout-xs="column" layout="row" class="">
                                <div flex-xs flex-gt-xs="100" layout="column">
                                    <md-card>
                                        <div class="widget-chart-container">
                                            <div class="widget-counter-group widget-counter-group-right">
                                                <div style="width: 100%;" class="pull-left">
                                                    <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                    <div class="titleDash">
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Faturamento X Despesas & Compras</b>
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

                            <div layout-xs="column" layout="row" class="">


                                <div flex-xs flex-gt-xs="100" layout="column">
                                    <md-card>
                                        <div class="widget-chart-container">
                                            <div class="widget-counter-group widget-counter-group-right">
                                                <div style="width: 100%;" class="pull-left">
                                                    <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                    <div class="titleDash">
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Lucro líquido</b>
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
                                <div flex-xs flex-gt-xs="50" layout="column">
                                    <md-card>
                                        <div class="widget-chart-container">
                                            <div class="widget-counter-group widget-counter-group-right">
                                                <div style="width: 100%" class="pull-left">
                                                    <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                    <div class="titleDash">
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Despesas e compras</b>
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

                                <div flex-xs flex-gt-xs="50" layout="column">
                                    <md-card>
                                        <div class="widget-chart-container">
                                            <div class="widget-counter-group widget-counter-group-right">
                                                <div style="width: 100%;" class="pull-left">
                                                    <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                    <div class="titleDash">
                                                        <h4 style="padding: 0px;margin: 0px;position: relative;"><b>Análise de Despesas</b>
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
                            <div layout-xs="column" layout="row" class="">
                                <div flex-xs flex-gt-xs="100" layout="column">
                                    <md-card>
                                        <div class="widget-chart-container">
                                            <div class="widget-counter-group widget-counter-group-right">
                                                <div style="width: 100%" class="pull-left">
                                                    <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                    <div class="titleDash">
                                                        <h4 style="padding: 0px;margin: 0px;"><b>Fluxo de Caixa</b>
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

                </div>


            <?php
            }
            if (in_array('25', explode(',', $envio['permissoes']))) {
            ?>
                <h1 class="h1Title">Gráficos <b>Dash Clientes</b></h1>
                <div class="ciuis-body-content" ng-controller="DashCustomers_Controller">
                    <md-content class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">
                        <md-content class="">
                            <md-content class="widget-fullwidth ciuis-body-loading">

                                <md-card flex-xs flex-gt-xs="100" layout="column">

                                    <div layout-xs="column" layout="row" class="bg-white">
                                        <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card1" style="position: relative;">
                                            <a href="customers">
                                                <md-card-title>
                                                    <md-card-title-text>
                                                        <span class="md-headline"><strong ng-bind="report.totalClientesAtivos"></strong></span>
                                                        <span class="md-subhead">Clientes ativos</span>
                                                    </md-card-title-text>
                                                </md-card-title>
                                            </a>
                                        </md-card>

                                        <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card3" style="position: relative;">

                                            <a href="customers">
                                                <md-card-title>
                                                    <md-card-title-text>
                                                        <span class="md-headline"><strong ng-bind="report.totalLTMes"></strong> <small>meses e </small><span ng-bind="report.totalLTDias"></span><small> dias</small> </span>
                                                        <span class="md-subhead">Lifetime Clientes</span>
                                                    </md-card-title-text>
                                                </md-card-title>
                                            </a>
                                        </md-card>

                                        <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card4" style="position: relative;">

                                            <a href="invoices">
                                                <md-card-title>
                                                    <md-card-title-text>
                                                        <span class="md-headline"><strong ng-bind-html="report.totalLTV | currencyFormat:cur_code:null:true:cur_lct"></strong></span>
                                                        <span class="md-subhead">Lifetime Value Clientes</span>
                                                    </md-card-title-text>
                                                </md-card-title>
                                            </a>
                                        </md-card>

                                        <md-card flex-xs flex-gt-xs="33" layout="column" class="text-center card2" style="position: relative;">

                                            <a href="invoices">
                                                <md-card-title>
                                                    <md-card-title-text>
                                                        <span class="md-headline"><strong ng-bind="report.totalChurn"></strong></span>
                                                        <span class="md-subhead">Churn rate</span>
                                                    </md-card-title-text>
                                                </md-card-title>
                                            </a>
                                        </md-card>
                                    </div>


                                </md-card>

                                <div layout-xs="column" layout="row" class="">
                                    <div flex-xs flex-gt-xs="50" layout="column">
                                        <md-card>
                                            <div class="widget-chart-container">
                                                <div class="widget-counter-group widget-counter-group-right">
                                                    <div style="width: 100%;" class="pull-left">
                                                        <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                        <div class="titleDash">
                                                            <h4 style="padding: 0px;margin: 0px;"><b>Top 10 clientes por faturamento</b>
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

                                    <div flex-xs flex-gt-xs="50" layout="column">
                                        <md-card>
                                            <div class="widget-chart-container">
                                                <div class="widget-counter-group widget-counter-group-right">
                                                    <div style="width: 100%;" class="pull-left">
                                                        <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                        <div class="titleDash">
                                                            <h4 style="padding: 0px;margin: 0px;"><b>Clientes ativos</b>
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

                                <div layout-xs="column" layout="row" class="">
                                    <div flex-xs flex-gt-xs="100" layout="column">
                                        <md-card>
                                            <div class="widget-chart-container">
                                                <div class="widget-counter-group widget-counter-group-right">
                                                    <div style="width: 100%" class="pull-left">
                                                        <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                        <div class="titleDash">
                                                            <h4 style="padding: 0px;margin: 0px;"><b>Novos clientes</b>
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
                                    <div flex-xs flex-gt-xs="50" layout="column">
                                        <md-card>
                                            <div class="widget-chart-container">
                                                <div class="widget-counter-group widget-counter-group-right">
                                                    <div style="width: 100%;" class="pull-left">
                                                        <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                        <div class="titleDash">
                                                            <h4 style="padding: 0px;margin: 0px;"><b>Estado</b>
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
                                    <div flex-xs flex-gt-xs="50" layout="column">
                                        <md-card>
                                            <div class="widget-chart-container">
                                                <div class="widget-counter-group widget-counter-group-right">
                                                    <div style="width: 100%;" class="pull-left">
                                                        <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                        <div class="titleDash">
                                                            <h4 style="padding: 0px;margin: 0px;"><b>Ramo de Atividade</b>
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
                            </md-content>
                        </md-content>

                    </md-content>

                </div>


            <?php
            }

            if (in_array('26', explode(',', $envio['permissoes']))) {
            ?>
                <h1 class="h1Title">Gráficos <b>Dash Leads</b></h1>
                <div class="ciuis-body-content" ng-controller="Panel2_Controller">
                    <md-content class="widget-fullwidth ciuis-body-loading" style="overflow: hidden;">
                        <md-card flex-xs flex-gt-xs="100" layout="column">
                            <div layout-xs="column" layout="row" class="bg-white">
                                <md-card flex-xs flex-gt-xs="20" layout="column" class="text-center card1" style="position: relative;">
                                    <a href="{{appurl + 'leads'}}">
                                        <md-card-title>
                                            <md-card-title-text>
                                                <img style="width: 50px; margin: 0 auto;" id="totalLeadsAtivos" ng-show="report.totalLeadsAtivos == ''" style="display: none;" src="{{appurl + 'assets/img/loading.gif'}}">
                                                <span class="md-headline"><strong ng-bind="report.totalLeadsAtivos"></strong></span>
                                                <span class="md-subhead">Total de Leads Ativos</span>
                                            </md-card-title-text>
                                        </md-card-title>

                                    </a>
                                </md-card>

                                <md-card flex-xs flex-gt-xs="20" layout="column" class="text-center card5" style="position: relative;">

                                    <a href="{{appurl + 'leads'}}">
                                        <md-card-title>
                                            <md-card-title-text>
                                                <img style="width: 50px; margin: 0 auto;" id="totalLeadsPipeline" ng-show="report.totalLeadsPipeline == ''" style="display: none;" src="{{appurl + 'assets/img/loading.gif'}}">
                                                <span class="md-headline"><strong ng-bind="report.totalLeadsPipeline"></strong></span>
                                                <span class="md-subhead">Total de Leads em Pipeline</span>
                                            </md-card-title-text>
                                        </md-card-title>
                                    </a>
                                </md-card>


                                <md-card flex-xs flex-gt-xs="20" layout="column" class="text-center card2" style="position: relative;">

                                    <a href="{{appurl + 'leads?flt_vencidos=1'}}">

                                        <md-card-title>
                                            <md-card-title-text>
                                                <img style="width: 50px; margin: 0 auto;" id="totalLeadsEmDia" ng-show="report.totalLeadsEmDia == ''" style="display: none;" src="{{appurl + 'assets/img/loading.gif'}}">
                                                <span class="md-headline"><strong ng-bind="report.totalLeadsEmDia"></strong></span>
                                                <span class="md-subhead">Total de Leads em dia</span>
                                            </md-card-title-text>
                                        </md-card-title>
                                    </a>
                                </md-card>

                                <md-card flex-xs flex-gt-xs="20" layout="column" class="text-center card3" style="position: relative;">

                                    <a href="{{appurl + 'leads?flt_vencidos=2'}}">

                                        <md-card-title>
                                            <md-card-title-text>
                                                <img style="width: 50px; margin: 0 auto;" id="totalLeadsAtrasados" ng-show="report.totalLeadsAtrasados == ''" style="display: none;" src="{{appurl + 'assets/img/loading.gif'}}">
                                                <span class="md-headline"><strong ng-bind="report.totalLeadsAtrasados"></strong></span>
                                                <span class="md-subhead">Total de Leads atrasados</span>
                                            </md-card-title-text>
                                        </md-card-title>
                                    </a>
                                </md-card>

                                <md-card flex-xs flex-gt-xs="20" layout="column" class="text-center card4" style="position: relative;">

                                    <a href="{{appurl + 'leads'}}">

                                        <md-card-title>
                                            <md-card-title-text>
                                                <img style="width: 50px; margin: 0 auto;" id="totalAtividades" ng-show="report.totalAtividades == ''" style="display: none;" src="{{appurl + 'assets/img/loading.gif'}}">
                                                <span class="md-headline"><strong ng-bind="report.totalAtividades"></strong></span>
                                                <span class="md-subhead">Total de atividades </span>
                                            </md-card-title-text>
                                        </md-card-title>
                                    </a>
                                </md-card>

                            </div>

                        </md-card>



                        <?php
                        if ($user_data['super_admin'] == "1") {
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
                        <div layout-xs="column" layout="row" class="">
                            <div flex-xs flex-gt-xs="100" layout="column">
                                <md-card>

                                    <div class="widget-chart-container">
                                        <div class="widget-counter-group widget-counter-group-right">
                                            <div style="width: 100%;" class="pull-left">
                                                <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                <div class="titleDash">
                                                    <h4 style="padding: 0px;margin: 0px;"><b>Novas oportunidades diárias</b>

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
                            <div flex-xs flex-gt-xs="50" layout="column">
                                <md-card>
                                    <div class="widget-chart-container">
                                        <div class="widget-counter-group widget-counter-group-right">
                                            <div style="width: 100%;" class="pull-left">
                                                <i style="font-size: 38px;color: #eaeaea;margin-right: 10px" class="ion-stats-bars pull-left"></i>
                                                <div class="titleDash">
                                                    <h4 style="padding: 0px;margin: 0px;"><b>Estados</b>

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
                            <div flex-xs flex-gt-xs="50" layout="column">
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
                    </md-content>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</body>
<script type="text/javascript">
    var lang = {};
    lang.payments = '<?php echo lang2('payments') ?>';
    lang.expenses = '<?php echo lang2('expenses') ?>';
    var send_email = '<?= $send_email ?>';
    var gerandoPdf = false;
    var liberado = false;
    var solicitado = false;
    var pdf = null;
    var CONSTdt_ate = '<?= $envio['data'] ?>';
    var CONST_dt_mes = '<?= date('m', strtotime($envio['data'])) ?>';
    var CONST_dt_ano = '<?= date('Y', strtotime($envio['data'])) ?>';
    var CONST_idCompany = '<?= $envio['id_company'] ?>';
    var BASE_URL = 'https://<?= $_SERVER['HTTP_HOST'] ?>/';
    var UPIMGURL = BASE_URL + 'uploads/images/';
    var SHOW_ONLY_ADMIN = 'true';
    var LOGGEDINSTAFFNAME = '';
    var LOGGEDINSTAFFAVATAR = '';
    var ACTIVESTAFF = '';

    var LOCATE_SELECTED = 'pt_BR';



    setTimeout(a => {
        liberado = true;
        console.log('liberado');
        if (solicitado) {
            gerarPdf();
        }

    }, 6000);

    function gerarPdf() {
        console.log('gerarPdf');
        $('#circular_loader').show();
        if (gerandoPdf) {
            return;
        }
        if (!liberado) {
            solicitado = true;
            return;
        }

        gerandoPdf = true;
        if (pdf == null) {
            var node = document.getElementById("contentMain");
            domtoimage.toPng(node).then(function(dataUrl) {
                $('#circular_loader').hide();
                pdf = dataUrl;
                gerandoPdf = false;
                var printWindow = window.open('', '', 'height=400,width=800');
                printWindow.document.write('<html><head><title>Dashboard clientes ' + new Date().toISOString() + '</title>');
                printWindow.document.write('</head><body >');
                printWindow.document.write("<img style = 'width: 100%;margin: 0 auto;' onload = 'window.print()' src = '" + dataUrl + "'>");
                printWindow.document.write('</body></html>');
                printWindow.document.close();
            }).catch(function(error) {
                $('#circular_loader').hide();
                gerandoPdf = false;
                console.error('oops, something went wrong!', error);
            });
        } else {
            $('#circular_loader').hide();
            var printWindow = window.open('', '', 'height=400,width=800');
            printWindow.document.write('<html><head><title>Dashboard clientes ' + new Date().toISOString() + '</title>');
            printWindow.document.write('</head><body >');
            printWindow.document.write("<img style = 'width: 100%;margin: 0 auto;' onload = 'window.print()' src = '" + pdf + "'>");
            printWindow.document.write('</body></html>');
            printWindow.document.close();
        }

    }
</script>

<script src="<?php echo base_url('assets/js/Ciuis.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/lib/moment.js/min/moment.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/lib/bootstrap/dist/js/bootstrap.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/lib/jquery.gritter/js/jquery.gritter.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/lib/angular-datepicker/src/js/angular-datepicker.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/lib/material/angular-material.min.js') ?>"></script>
<script src="<?php echo base_url('assets/lib/currency-format/currency-format.min.js?v=1.2') ?>"></script>
<script src="<?php echo base_url('assets/lib/angular-datetimepicker/angular-material-datetimepicker.min.js') ?>"></script>
<script src="<?php echo base_url('assets/lib/data-table/md-data-table.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/lib/select2/select2.min.js'); ?>"></script>


<script src="<?php echo base_url('assets/lib/chartjs/dist/Chart.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/lib/highcharts/highcharts.js') ?>"></script>


<script type="text/javascript" src="<?php echo base_url('assets/js/dashCustomers.js?v=1.6.1') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/panel2.js?v=1.6.3') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/dashInvoices.js?v=1.5.2') ?>"></script>
<script>
    $(document).ready(function() {

    });
</script>