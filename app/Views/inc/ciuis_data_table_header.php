<?php
$rebrand = load_config();
$appconfig = get_appconfig();
$menus = get_menu();
$leftmenus = get_leftmenu();
$user_data = get_user();
$app_logo = base_url('uploads/ciuis_settings/' . $user_data['settings']['logo']);
$app_logo_alternate = base_url('assets/img/placeholder.png');
$user_image = base_url('uploads/images/' . $user_data['avatar']);
$user_image_alternate = base_url('uploads/images/n-img.jpg');
$login_Model = new App\Models\Login_Model;
?>

<!DOCTYPE html>

<html ng-app="Ciuis" lang="<?php echo lang('lang_code'); ?>">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="<?php echo $rebrand['meta_description'] ?>">
    <meta name="keywords" content="<?php echo $rebrand['meta_keywords'] ?>">
    <link rel="shortcut icon" href="<?php echo base_url('assets/img/images/' . $rebrand['favicon_icon'] . ''); ?>">
    <title><?= isset($title) ? $title : 'Next1'; ?></title>


    <script src="<?php echo base_url('assets/lib/jquery/jquery.min.js'); ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('assets/lib/interactivegraph/js/jquery.flot.min.js'); ?>"></script>

    <script src="<?php echo base_url('assets/lib/angular/angular.min.js'); ?>"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
    <script type="text/javascript" src="<?= base_url('assets/lib/sortable/src/sortable.js'); ?>"></script>

    <script src="<?php echo base_url('assets/lib/angular/angular-animate.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/angular/angular-aria.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/angular/i18n/angular-locale_' . lang('lang_code_dash') . '.js'); ?>"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.7.8/angular-sanitize.js"></script>
    <script src="<?php echo base_url('assets/lib/sweetalert2/dist/sweetalert2.all.min.js'); ?>"></script>


    <script src="<?php echo base_url('assets/lib/dom-to-image/dom-to-image.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/main.js?v=1.3.3'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/mask/jquery.mask.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/mask/jquery.mask2.min.js'); ?>"></script>

    <script src="https://d3js.org/d3.v3.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script src="<?= base_url('assets/lib/touchspin/jquery.bootstrap-touchspin.js'); ?>"></script>
    <script src="<?= base_url('assets/js/chat_header.js?v=1.1.2'); ?>"></script>


    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/lib/data-table/md-data-table.min.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/fontawesome/css/all.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/lib/sweetalert2/dist/sweetalert2.min.css'); ?>">
    <link crossorigin="anonymous" href='https://fonts.googleapis.com/css?family=Open+Sans+Condensed:700,300,300italic' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/graph.css?v=1.6.4'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/lib/interactivegraph/css/graph2.css?v=2.2'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/lib/touchspin/jquery.bootstrap-touchspin.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/ciuis.css?v=1.3.12'); ?>" type="text/css" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/layout2.0.css?v=') . date('Ymdhis'); ?>" type="text/css" />

    <link rel="stylesheet" href="<?php echo base_url('assets/lib/pe-icon-7-stroke/css/pe-icon-7-stroke.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/lib/pe-icon-7-stroke/css/helper.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/chat.css?v=1.1'); ?>">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .triangle_aviso {
            position: absolute;
            color: #FFC107;
            font-size: 12px;
            right: 5px;
            top: 7px;
        }
    </style>
    <script>
        var BASE_URL = "<?php echo base_url(); ?>",
            update_error = "<?php echo lang('update_error'); ?>",
            email_error = "<?php echo lang('email_error'); ?>",
            ACTIVESTAFF = "<?php echo session()->get('usr_id'); ?>",
            SHOW_ONLY_ADMIN = "<?php if (!$login_Model->if_admin()) {
                                    echo 'true';
                                } else echo 'false'; ?>",
            CURRENCY = "<?php echo currency ?>",
            LOCATE_SELECTED = "<?php echo lang('lang_code'); ?>",
            UPIMGURL = "<?php echo base_url('uploads/images/'); ?>",
            NTFTITLE = "<?php echo lang('notification') ?>",
            INVMARKCACELLED = "<?php echo lang('invoicecancelled') ?>",
            TICKSTATUSCHANGE = "<?php echo lang('ticketstatuschanced') ?>",
            LEADMARKEDAS = "<?php echo lang('leadmarkedas') ?>",
            LEADUNMARKEDAS = "<?php echo lang('leadunmarkedas') ?>",
            TODAYDATE = "<?php echo date('Y.m.d ') ?>",
            LOGGEDINSTAFFID = "<?php echo session()->get('usr_id'); ?>",
            LOGGEDINSTAFFNAME = "<?php echo session()->get('staffname'); ?>",
            LOGGEDINSTAFFAVATAR = "<?php echo session()->get('staffavatar'); ?>",
            VOICENOTIFICATIONLANG = "<?php echo lang('lang_code_dash'); ?>",
            initialLocaleCode = "<?php echo lang('initial_locale_code'); ?>";
        var new_item = "<?php echo lang('new'); ?>";
        var item_unit = "<?php echo lang('unit'); ?>";

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

        function hashCode(s) {
            return s.split("").reduce(function(a, b) {
                a = ((a << 5) - a) + b.charCodeAt(0);
                return a & a;
            }, 0);
        }

        if (!getCookie('tempoInativo')) {
            setCookie('tempoInativo', 3);
        }

        // var tempoInativo = 2;
        var timeOut = setInterval(() => {
            let tempoInativo = parseFloat(getCookie('tempoInativo'));
            tempoInativo--;

            if (tempoInativo == 0) {
                showToast('Você será considerado inativo daqui a 2 minutos');
            } else if (tempoInativo == -1) {
                /*  var dataObj = $.param({
                      detail: '<a href="staff/staffmember/' + LOGGEDINSTAFFID + '"> ' + LOGGEDINSTAFFNAME + '</a> Sessão expirada!',
                      key_log: 'inactive'
                  });*/

                // $http.post(BASE_URL + 'logs/insert', dataObj, config).then(function(response) {}, function(response) {});

                $.ajax({
                    url: BASE_URL + 'logs/insert',
                    type: 'post',
                    data: {
                        detail: '<a href="staff/staffmember/' + LOGGEDINSTAFFID + '"> ' + LOGGEDINSTAFFNAME + '</a> Sessão expirada!',
                        key_log: 'inactive'
                    }
                }).done(function(msg) {});

                Swal.fire({
                    title: 'Você ainda está ai?',
                    text: "No momento você está considerado como inativo!",
                    icon: 'warning',
                    confirmButtonText: 'Estou aqui!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed || result.isDismissed) {
                        tempoInativo = 3;
                        setCookie('tempoInativo', tempoInativo);
                        /*   var dataObj = $.param({
                               detail: '<a href="staff/staffmember/' + LOGGEDINSTAFFID + '"> ' + LOGGEDINSTAFFNAME + '</a> Renovou a sessão!',
                               key_log: 'login'
                            });
                            */

                        $.ajax({
                            url: BASE_URL + 'logs/insert',
                            type: 'post',
                            data: {
                                detail: '<a href="staff/staffmember/' + LOGGEDINSTAFFID + '"> ' + LOGGEDINSTAFFNAME + '</a> Renovou a sessão!',
                                key_log: 'login'
                            }
                        }).done(function(msg) {});

                        // $http.post(BASE_URL + 'logs/insert', dataObj, config).then(function(response) {}, function(response) {});
                    }
                })
            } else if (tempoInativo > 0) {
                /*
                var dataObj = $.param({
                    detail: '<a href="staff/staffmember/' + LOGGEDINSTAFFID + '"> ' + LOGGEDINSTAFFNAME + '</a> Está ativo.',
                    key_log: 'operating'
                });*/

                $.ajax({
                    url: BASE_URL + 'logs/insert',
                    type: 'post',
                    data: {
                        detail: '<a href="staff/staffmember/' + LOGGEDINSTAFFID + '"> ' + LOGGEDINSTAFFNAME + '</a> Está ativo.',
                        key_log: 'operating'
                    }
                }).done(function(msg) {});

                //  $http.post(BASE_URL + 'logs/insert', dataObj, config).then(function(response) {}, function(response) {});
            }
            setCookie('tempoInativo', tempoInativo);

        }, (2 * 60000));


        $("body").click(function() {
            setCookie('tempoInativo', 4);
        });

        function setCookie(cname, cvalue, exdays = 365) {
            const d = new Date();
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
            let expires = "expires=" + d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }

        function getCookie(cname) {
            let name = cname + "=";
            let decodedCookie = decodeURIComponent(document.cookie);
            let ca = decodedCookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }
    </script>

</head>

<style>
    md-backdrop.md-select-backdrop {
        z-index: 9976;
    }

    .select2-container {
        width: 100% !important;
    }



    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #ffffff !important;
    }

    .select2-results__options .select2-results__option--highlighted[aria-selected] {
        background-color: #5897fb !important;
    }
</style>

<?php $settingsM = new App\Models\Settings_Model();
$settings = $settingsM->get_settings_ciuis();

?>

<body ng-controller="Ciuis_Controller">

    <?php if ($rebrand['disable_preloader'] == '0') {

        $preloader =  base_url('assets/img/' . $rebrand['preloader']); ?>

        <div style="background-color: #b5b5b5;" id="ciuisloader" style="background-image: url(<?php echo $preloader ?>);"></div>

    <?php } ?>

    <md-content id="mobile-menu" class="" style="left: 0px; opacity: 1; display: none">

        <md-toolbar class="toolbar-white">

            <div class="md-toolbar-tools">

                <div flex md-truncate class="crm-name"><span ng-bind="settings.crm_name"></span></div>

                <md-button ng-click="close()" class="md-icon-button" aria-label="Close">

                    <md-icon><i class="ion-close-circled text-muted"></i></md-icon>

                </md-button>

            </div>

        </md-toolbar>

        <md-content class="mobile-menu-box bg-white">

            <div class="mobile-menu-wrapper-inner">

                <div class="mobile-menu-wrapper">

                    <div class="mobile-menu-slider" style="left: 0px;">

                        <div class="mobile-menu">
                            <ul>
                                <?php foreach ($menus as $menu) { ?>
                                    <?php if ($menu['url'] != '#') { ?>
                                        <li class="nav-item">
                                            <div>
                                                <a href="<?php echo $menu['url'] ?>"><?php echo $menu['name']  ?></a>
                                            </div>
                                        </li>

                                    <?php } ?>

                                    <?php foreach ($menu['sub_menu'] as $submenu) { ?>
                                        <li>
                                            <a href="<?php echo $submenu['url'] ?>">
                                                <span class="title"><?php echo $submenu['name'] ?></span>
                                            </a>
                                        </li>
                                    <?php } ?>

                                <?php } ?>



                                <?php
                                if ($user_data['super_admin'] == "1") {
                                ?>
                                    <li>
                                        <a href="/companies">Empresas</a>
                                    </li>
                                <?php
                                }
                                ?>



                            </ul>
                        </div>

                        <div class="clear"></div>

                    </div>

                </div>

            </div>

        </md-content>


    </md-content>

    <div class="ng-tns-c143-0 app-container app-theme-white closed-sidebar">
        <app-header class="ng-tns-c143-0">
            <div class="app-header header-shadow">
                <div class="logo-src">
                    <img class="transform_logo" width="34px" src="<?php echo $app_logo ?>" height="34px" onerror="this.onerror=null; this.src='<?php echo $app_logo_alternate ?>'">
                </div>
                <div class="app-header__content">
                    <div class="app-header-right dataMenu3">
                        <app-user-box>
                            <div class="d-flex">


                                <div class="header-btn-lg pr-0 dataMenu2">
                                    <?php
                                    if (session()->admin_user != null && !empty(session()->admin_user)) {
                                    ?>
                                        <div class="widget-content-left">
                                            <a href="<?= base_url('companies/login_admin'); ?>" type="button" class="btn btn-outline-primary"><i class="fas fa-undo-alt"></i> Retomar como admin</a>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <div class="widget-content p-0">
                                        <div class="widget-content-wrapper">

                                            <div class="widget-content-left widget2" style="width: 250px;">
                                                <div ngbdropdown="" placement="bottom-right" class="btn-group dropdown" ng-reflect-placement="bottom-right">

                                                    <button aria-haspopup="true" type="button" ngbdropdowntoggle="" class="dropdown-toggle btn btn-link p-0 mr-2" aria-expanded="false" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <div class="icon-wrapper icon-wrapper-alt rounded-circle">
                                                            <img src="<?php echo $user_image ?>" class="rounded-circle" style="height: 42px;width: 42px;" onerror="this.onerror=null; this.src='<?php echo $user_image_alternate ?>'">
                                                        </div>
                                                    </button>

                                                    <div id="areaCliente" ngbdropdownmenu="" class="dropdown-menu-lg dropdown-menu" x-placement="bottom-right" aria-labelledby="dropdownMenuButton">
                                                        <div class="dropdown-menu-header">
                                                            <div class="dropdown-menu-header-inner bg-info">
                                                                <div class="menu-header-image opacity-2 dd-header-bg-6" style="background-image: url('<?= base_url('') ?>assets/img/back5.jpeg');"></div>
                                                                <div class="menu-header-content text-left">
                                                                    <div class="widget-content p-0">
                                                                        <div class="widget-content-wrapper">
                                                                            <div class="widget-content-left mr-3">
                                                                                <img src="<?php echo $user_image ?>" class="rounded-circle" style="height: 42px;width: 42px;" onerror="this.onerror=null; this.src='<?php echo $user_image_alternate ?>'">
                                                                            </div>
                                                                            <div class="widget-content-left">
                                                                                <div class="widget-heading"><?= $user_data['name'] ?></div>
                                                                                <div class="widget-subheading opacity-8"><?= $user_data['email'] ?></div>
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <div class="grid-menu grid-menu-2col overflow-hidden">
                                                            <div class="no-gutters row">
                                                                <div class="col-sm-6">
                                                                    <md-button ng-show="ONLYADMIN != 'true'" ng-href="{{appurl + 'staff/profile'}}" class="btn-icon-vertical btn-transition btn-transition-alt pt-2 pb-2 btn btn-outline-primary">
                                                                        <i class="pe-7s-user icon-gradient btn-icon-wrapper mb-2"></i> Perfil
                                                                    </md-button>

                                                                    <md-button ng-show="ONLYADMIN == 'true'" ng-href="{{appurl + 'staff/staffmember/' + activestaff}}" class="btn-icon-vertical btn-transition btn-transition-alt pt-2 pb-2 btn btn-outline-primary">
                                                                        <i class="pe-7s-user icon-gradient btn-icon-wrapper mb-2"></i> Perfil
                                                                    </md-button>

                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <md-button class="btn-icon-vertical btn-transition btn-transition-alt pt-2 pb-2 btn btn-outline-danger" ng-href="{{appurl + 'login/logout'}}">
                                                                        <i class="pe-7s-download icon-gradient bg-love-kiss btn-icon-wrapper mb-2"></i><b ng-bind='lang.logout'></b>
                                                                    </md-button>

                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div style="padding-top: 10px;cursor: pointer;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="widget-content-left ml-3 header-user-info header-user-info5 ">
                                                        <div class="widget-heading"><?= $user_data['name'] ?></div>
                                                        <div class="widget-subheading"><?= $user_data['email'] ?></div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="widget-content-right header-user-info header-user-info3" style="padding: 0;margin-left: 5px;">

                                                <a href="{{appurl + 'reuniao'}}" class="md-icon-button icon-button-home" style="padding-top: 8px;margin-right: 10px;">
                                                    <md-tooltip md-direction="left">Agenda</md-tooltip>
                                                    <img style="width: 26px;" src='<?= base_url('assets/img/menu/calendar.png') ?>'>
                                                </a>

                                                <!--
                                                <md-button ng-click="Todo();get_todo()" class="md-icon-button icon-button-home" aria-label="Todo" ng-cloak>
                                                    <md-tooltip md-direction="left" ng-bind='lang.todo'></md-tooltip>
                                                    <md-icon><i class="ion-clipboard text-muted"></i></md-icon>
                                                </md-button>
                                                -->

                                                <a href="<?= base_url('whatsapp') ?>" class="md-icon-button icon-button-home" aria-label="Todo" ng-cloak style="padding: 6px 0px;">
                                                    <md-tooltip md-direction="left">WhatsApp</md-tooltip>

                                                    <div ng-show="settings.settings_ia.newnotificationWhatsapp > 0"  style = "position: relative;" >
                                                        <span ng-bind = "settings.settings_ia.newnotificationWhatsapp" class="notifyWhats"></span>
                                                    </div>
                                                    <md-icon><i class="fab fa-whatsapp"></i></md-icon>
                                                </a>

                                                <md-button ng-click="open_messages()" class="md-icon-button icon-button-home" aria-label="Notifications" ng-cloak style="padding-top: 3px !important;">
                                                    <md-tooltip md-direction="left">Mensagens</md-tooltip>
                                                    <md-icon><i style="font-size: 19px;vertical-align: top;margin-top: 2px;margin-right: 2px;" class="fas fa-paper-plane"></i></md-icon>
                                                </md-button>

                                                <md-button ng-click="Notifications();" class="md-icon-button icon-button-home" aria-label="Notifications" ng-cloak style="padding-top: 3px !important;">
                                                    <md-tooltip md-direction="left" ng-bind='lang.notifications'></md-tooltip>
                                                    <div ng-show="settings.newnotification == true" class="notify"> <span class="heartbit"></span> <span class="point"></span> </div>
                                                    <i ng-show="settings.newnotification == true" class="fas fa-exclamation-triangle triangle_aviso"></i>
                                                    <md-icon><i style="font-size: 19px;vertical-align: top;margin-top: 2px;margin-right: 2px;" class="fas fa-bell"></i></md-icon>
                                                </md-button>

                                                <md-button ng-hide="ONLYADMIN != 'true'" class="md-icon-button icon-button-home" ng-href="{{appurl + 'settings'}}" aria-label="Settings" ng-cloak style="padding-top: 3px;">
                                                    <md-tooltip md-direction="left" ng-bind='lang.settings'></md-tooltip>
                                                    <md-icon><i class="ion-gear-a text-muted" style="color:#e78787"></i></md-icon>
                                                </md-button>


                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </app-user-box>
                    </div>
                </div>
            </div>
        </app-header>

        <div class="app-sidebar sidebar-shadow">
            <div class="app-header__logo">
                <div class="logo-src">
                    <img class="transform_logo logo-main" width="34px" src="<?php echo $app_logo ?>" height="34px" onerror="this.onerror=null; this.src='<?php echo $app_logo_alternate ?>'">


                    <div class="btn-menu-mobile">
                        <button type="button" class="hamburger close-sidebar-btn hamburger--elastic">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>

                </div>
                <div class="header__pane ml-auto">
                    <button type="button" class="hamburger close-sidebar-btn hamburger--elastic is-active">
                        <span class="hamburger-box">
                            <span class="hamburger-inner"></span>
                        </span>
                    </button>
                </div>
            </div>
            <div class="app-sidebar-content">
                <div class="app-sidebar-scroll">
                    <perfect-scrollbar class="scroll-container ps-show-limits ps-show-active" style="max-width: 600px;" ng-reflect-auto-propagation="true" ng-reflect-config="[object Object]" ng-reflect-scroll-indicators="true">
                        <div style="position: static;" class="ps ps--active-y" ng-reflect-config="[object Object]" ng-reflect-disabled="false">
                            <div class="ps-content">
                                <div class="v-sidebar-menu vsm-default">
                                    <div class="vsm-list">
                                        <ngb-accordion role="tablist" class="accordion" ng-reflect-active-ids="dashboards" ng-reflect-destroy-on-hide="false" ng-reflect-close-other-panels="true" aria-multiselectable="false">

                                            <div class="card ng-star-inserted">
                                                <div role="tab" class="card-header" id="ngb-panel-0-header">
                                                    <div class="vsm-header ng-star-inserted">Menu</div>
                                                </div>
                                                <div role="tabpanel" class="collapse ng-star-inserted" id="ngb-panel-0" aria-labelledby="ngb-panel-0-header">
                                                    <div class="card-body">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card ng-star-inserted" style="overflow-y: auto;max-height: calc(100vh - 7.5em);">

                                                <?php
                                                foreach ($menus as $menu) { ?>
                                                    <?php if ($menu['url'] != '#' || sizeof($menu['sub_menu']) > 0) { ?>

                                                        <div role="tab" class="card-header" id="dashboards-header<?= $menu['id'] ?>">
                                                            <button type="button" data-url="<?= $menu['url'] ?>" ngbpaneltoggle="" class="vsm-link ng-star-inserted collapsed url-click" ng-reflect-ngb-panel-toggle="" aria-expanded="true" data-toggle="collapse" data-target="#dashboards<?= $menu['id'] ?>">
                                                                <i class="material-icons <?php echo $menu['icon'] ?>"></i>

                                                                <span class="vsm-title"><?php echo $menu['name'] ?></span>

                                                                <?php if ($menu['url'] == "#") { ?>
                                                                    <i class="fas fa-chevron-down"></i>
                                                                <?php } ?>

                                                            </button>
                                                        </div>


                                                        <?php if (sizeof($menu['sub_menu']) > 0) { ?>

                                                            <div role="tabpanel" class=" ng-star-inserted collapse" id="dashboards<?= $menu['id'] ?>" aria-labelledby="dashboards-header<?= $menu['id'] ?>">
                                                                <div class="card-body">
                                                                    <div class="vsm-dropdown ng-star-inserted">
                                                                        <div class="vsm-list">

                                                                            <div class="vsm-item">
                                                                                <?php
                                                                                foreach ($menu['sub_menu'] as $submenu) {
                                                                                    //active-item
                                                                                ?>

                                                                                    <a href="<?= $submenu['url'] ?>" routerlinkactive="active-item" class="vsm-link " ng-reflect-router-link-active="active-item">
                                                                                        <?php
                                                                                        if (!empty($submenu['icon_svg'])) {
                                                                                        ?>
                                                                                            <img style="position: absolute;transition: all 0.1s ease-in-out;width: 30px;text-align: center;left: -18px;top: 5px;" src='<?= base_url($submenu['icon_svg']) ?>'>
                                                                                        <?php
                                                                                        } else {
                                                                                        ?>
                                                                                            <i style="font-size: 23px; position: absolute;left: -12px;top: 9px;" class="icon <?php echo $submenu['icon'] ?>"></i>
                                                                                        <?php
                                                                                        }
                                                                                        ?>
                                                                                        <span class="vsm-title"><?= lang2($submenu['name']) ?></span>
                                                                                    </a>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>


                                                <?php
                                                if ($user_data['super_admin'] == "1") {
                                                ?>
                                                    <div role="tab" class="card-header" id="dashboards-header0">
                                                        <button type="button" data-url="/companies" ngbpaneltoggle="" class="vsm-link ng-star-inserted collapsed url-click" ng-reflect-ngb-panel-toggle="" aria-expanded="true" data-toggle="collapse" data-target="#dashboards0">
                                                            <i class="material-icons far fa-building"></i>
                                                            <span class="vsm-title">Empresas</span>
                                                        </button>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                                <div role="tab" class="card-header" id="dashboards-header0">
                                                    <button type="button" ng-click="setChat(0)" class="vsm-link ng-star-inserted collapsed">
                                                        <i class="material-icons fa fa-question-circle"></i>

                                                        <span class="vsm-title">Suporte</span>
                                                    </button>
                                                </div>




                                            </div>
                                        </ngb-accordion>
                                    </div>
                                </div>
                            </div>
                            <div class="ps-overlay ng-star-inserted ps-at-top ps-at-right">
                                <div class="ps-indicator-top"></div>
                                <div class="ps-indicator-left"></div>
                                <div class="ps-indicator-right"></div>
                                <div class="ps-indicator-bottom"></div>
                            </div>
                            <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                                <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                            </div>
                            <div class="ps__rail-y" style="top: 0px; right: 0px; height: 600px;">
                                <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 290px;"></div>
                            </div>
                        </div>
                    </perfect-scrollbar>
                </div>
            </div>
        </div>
    </div>


    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="taskTimer" ng-cloak style="width: 450px;">

        <md-toolbar class="md-theme-light" style="background:#262626">

            <div class="md-toolbar-tools">

                <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>

                <md-truncate flex><?php echo lang('task') . ' ' . lang('timer') ?></md-truncate>

                <div class="task-timer">

                    <md-button class="task-timer md-icon-button">

                        <md-progress-circular ng-show="startingTimer == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

                        <md-icon ng-hide="startingTimer ==true">

                            <md-tooltip md-direction="left" ng-bind='lang.start_timer'></md-tooltip>

                            <i class="ion-ios-play" ng-click="startTimer('start')"></i>

                        </md-icon>

                    </md-button>

                </div>

            </div>

        </md-toolbar>

        <md-content>

            <md-content>

                <md-progress-circular ng-if="timers.loading" class="" md-mode="indeterminate" md-diameter="25">

                </md-progress-circular>

                <ul class="" style="padding: unset;">

                    <li class="" ng-repeat="time in timers">

                        <div layout="row" layout-wrap class="timer-section">

                            <div flex-gt-xs="60" flex-xs="60">

                                <span ng-show="time.task_id"><?php echo lang('task') ?>: </span> <a ng-show="time.task_id" href="{{appurl + 'tasks/task/' + time.task_id}}" class="assigned"><strong ng-bind="time.task"></strong></a>

                                <a ng-show="!time.task_id" class="label label-info assign" ng-click="stopTimerWithTask('assign',time.id)"><?php echo lang('assign_task') ?>

                                    &nbsp;&nbsp;<i class="ion-compose"></i></a>

                                <br>

                                <span class="text-muted"><?php echo lang('started_at') ?>: {{time.started}}</span>

                            </div>

                            <div class="text-right" flex-gt-xs="40" flex-xs="40" layout="row">

                                <div class="text-right" flex-gt-xs="90" flex-xs="90">

                                    <span class="totalTime" id="totalTime{{time.id}}"></span>

                                    <div style="display: inline-flex;">

                                        <md-icon>

                                            <md-tooltip md-direction="top" ng-bind='lang.stop_timer'></md-tooltip>

                                            <i class="ion-ios-pause" ng-click="startTimer('stop', time.id)"></i>

                                            <md-tooltip md-direction="top" ng-bind='lang.stop_timer'></md-tooltip>

                                        </md-icon>

                                    </div>

                                </div>

                                <div class="text-right" flex-gt-xs="10" flex-xs="10">

                                    <div>

                                        <md-button class="md-icon-button timer-menu" ng-click="DeleteMenuTimer(time.id)" aria-label="Delete">

                                            <md-icon><i class="ion-trash-b text-muted"></i></md-icon>

                                        </md-button>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </li>

                    <p ng-show="!timers.length" class="not-found"><?php echo lang('not_found') ?></p>

                </ul>

            </md-content>

        </md-content>

    </md-sidenav>



    <md-sidenav class="md-sidenav-left md-whiteframe-4dp" md-component-id="PickUpTo" ng-cloak style="width: 450px;"></md-sidenav>

    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="SetOnsiteVisit" ng-cloak style="width: 450px;">

        <md-toolbar class="md-theme-light" style="background:#262626">

            <div class="md-toolbar-tools">

                <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i></md-button>

                <md-truncate ng-bind='lang.set_onsite_visit'></md-truncate>

            </div>

        </md-toolbar>

        <md-content layout-padding="">

            <md-content layout-padding>

                <md-input-container class="md-block">

                    <label ng-bind='lang.title'></label>

                    <input ng-model="onsite_visit.title">

                </md-input-container>

                <md-input-container class="md-block" flex-gt-xs>

                    <label ng-bind='lang.customer'></label>

                    <md-select required placeholder="{{lang.choisecustomer}}" ng-model="onsite_visit.customer_id" style="min-width: 200px;" aria-label='Customer'>

                        <md-option ng-repeat="customer in all_customers" ng-value="customer.id">{{customer.name}}

                        </md-option>

                    </md-select>

                </md-input-container>

                <md-input-container class="md-block">

                    <label ng-bind='lang.assigned'></label>

                    <md-select placeholder="{{lang.choosestaff}}" ng-model="onsite_visit.staff_id" style="min-width: 200px;" aria-label='Staff'>

                        <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>

                    </md-select>

                </md-input-container>

                <br>

                <md-input-container class="md-block">

                    <label ng-bind='lang.start'></label>

                    <input mdc-datetime-picker="" date="true" time="true" type="text" id="datetime" placeholder="{{lang.chooseadate}}" show-todays-date="" minutes="true" min-date="date" show-icon="true" ng-model="onsite_visit.start" class=" dtp-no-msclear dtp-input md-input">

                </md-input-container>

                <md-input-container class="md-block">

                    <label ng-bind='lang.end'></label>

                    <input mdc-datetime-picker="" date="true" time="true" type="text" id="datetime" placeholder="{{lang.chooseadate}}" show-todays-date="" minutes="true" min-date="onsite_visit.start" show-icon="true" ng-model="onsite_visit.end" class=" dtp-no-msclear dtp-input md-input">

                </md-input-container>

                <md-input-container class="md-block">

                    <label ng-bind='lang.description'></label>

                    <textarea required ng-model="onsite_visit.description" placeholder="<?php echo lang('typeSomething'); ?>" class="form-control note-description"></textarea>

                </md-input-container>

                <div class="pull-right">

                    <md-button ng-click="AddOnsiteVisit()" class="md-raised md-primary md-button md-ink-ripple" ng-disabled="addingOnsite == true" aria-label='Add Onsite Visit'>

                        <span ng-hide="addingOnsite == true"><?php echo lang('set'); ?></span>

                        <md-progress-circular class="white" ng-show="addingOnsite == true" md-mode="indeterminate" md-diameter="20">

                        </md-progress-circular>

                    </md-button>

                </div>

            </md-content>

        </md-content>

    </md-sidenav>



    <md-sidenav class="md-sidenav-right md-whiteframe-5dp" md-component-id="searchNav" ng-cloak style="width: 450px;" md-disable-close-events style="width: 650px;" ng-cloak>

        <md-toolbar class="md-theme-light" style="background:#262626">

            <div class="md-toolbar-tools">

                <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i></md-button>

                <md-truncate ng-bind='lang.search'></md-truncate>

            </div>

        </md-toolbar>

        <md-content><br>

            <md-input-container ng-submit="searchInput(search_input)" class="md-block" style="margin-bottom: unset;">

                <label><?php echo lang('searchhere'); ?></label>

                <input ng-submit="searchInput(search_input)" name="search" ng-model="search_input" ng-keyup="searchInput(search_input)">

            </md-input-container>

            <p class="text-center text-muted" ng-show="searchResult == 1"><?php echo lang('not_found'); ?></p>

            <p class="text-center text-muted" ng-show="searchInputMsg == 1"><?php echo lang('type_something'); ?></p>

            <div ng-show="searchLoader == 1">

                <md-progress-circular md-mode="indeterminate" md-diameter="20" style="margin-left: auto;margin-right: auto;">

                </md-progress-circular>

                <p class="text-center">

                    <strong><?php echo lang('searching'); ?></strong>

                </p>

            </div>

            <section ng-show="searchStaff.length > 0">

                <md-subheader class="md-accent"><span class="material-icons ico-ciuis-staff search-icon pull-right"></span>

                    <?php echo lang('staff_members'); ?>

                </md-subheader>

                <md-list layout-padding>

                    <md-list-item class="md-3-line search-item" ng-repeat="staff in searchStaff">

                        <a href="{{appurl + 'staff/staffmember/' + staff.staff_id}}">

                            <div class="md-list-item-text">

                                <p>

                                    <span class="blur5" ng-bind='staff.staff_number?("<?php echo '{{staff.staff_number}}' ?>") : ("<?php echo $appconfig['staff_prefix'] . '' . '{{staff.staff_id}}' ?>")'>

                                    </span>

                                    <span ng-bind='staff.name?("<?php echo '{{staff.name | limitTo :20}}'  ?>") : ("")'>

                                    </span>

                                </p>

                                <p>

                                    <strong><?php echo lang('email'); ?>:</strong>

                                    {{ staff.email | limitTo: 30 }}{{staff.email.length > 30 ? '...' : ''}}

                                </p>

                            </div>

                        </a>

                        <md-divider inset></md-divider>

                    </md-list-item>

                </md-list>

            </section>

            <section ng-show="searchProjects.length > 0">

                <md-subheader class="md-accent">

                    <span class="material-icons ico-ciuis-projects search-icon pull-right"></span>

                    <?php echo lang('projects'); ?>

                </md-subheader>

                <md-list layout-padding>

                    <md-list-item class="md-3-line search-item" ng-repeat="project in searchProjects">

                        <a href="{{appurl + 'projects/project/' + project.id}}">

                            <div class="md-list-item-text">

                                <p>

                                    <span class="blur5" ng-bind='project.project_number?("<?php echo '{{project.project_number}}' ?>") : ("<?php echo $appconfig['project_prefix'] . '' . '{{project.id}}' ?>")'>

                                    </span>

                                    <span ng-bind='project.name?("<?php echo '{{project.name | limitTo :20}}'  ?>") : ("")'>

                                    </span>

                                </p>

                                <p>

                                    <strong><?php echo lang('status'); ?>:</strong>

                                    <span ng-switch="project.status">

                                        <span ng-switch-when="1"><?php echo lang('notstarted'); ?></span>

                                        <span ng-switch-when="2"><?php echo lang('started'); ?></span>

                                        <span ng-switch-when="3"><?php echo lang('percentage'); ?></span>

                                        <span ng-switch-when="4"><?php echo lang('cancelled'); ?></span>

                                        <span ng-switch-when="5"><?php echo lang('complete'); ?></span>

                                    </span>

                                </p>

                            </div>

                        </a>

                        <md-divider inset></md-divider>

                    </md-list-item>

                </md-list>

            </section>

            <section ng-show="searchInvoices.length > 0">

                <md-subheader class="md-accent">


                    <i class="far fa-clipboard material-icons search-icon pull-right"></i>
                    <?php echo lang('invoices'); ?>

                </md-subheader>

                <md-list layout-padding>

                    <md-list-item class="md-3-line search-item" ng-repeat="invoice in searchInvoices">

                        <a href="{{appurl + 'invoices/invoice/' + invoice.invoice_id}}">

                            <div class="md-list-item-text">

                                <h4 class="blur5" ng-bind='invoice.invoice_number?(invoice.invoice_number):("<?php echo $appconfig['inv_prefix'] . '' . '{{invoice.invoice_id}}' ?>")'>

                                </h4>

                                <p>

                                    <strong><?php echo lang('customer'); ?>:</strong>

                                    <span>{{invoice.namesurname?(invoice.namesurname):(invoice.company) | limitTo: 30 }}{{invoice.namesurname.length > 30 ? '...' : ''}}</span>

                                </p>

                            </div>

                        </a>

                        <md-divider inset></md-divider>

                    </md-list-item>

                </md-list>

            </section>

            <section ng-show="searchProposals.length > 0">

                <md-subheader class="md-accent">

                    <span class="material-icons ico-ciuis-proposals search-icon pull-right"></span>

                    <?php echo lang('proposals'); ?>

                </md-subheader>

                <md-list layout-padding>

                    <md-list-item class="md-3-line search-item" ng-repeat="proposal in searchProposals">

                        <a href="{{appurl + 'proposals/proposal/' + proposal.proposal_id}}">

                            <div class="md-list-item-text">

                                <p>

                                    <span class="blur5" ng-bind='proposal.proposal_number?("<?php echo '{{proposal.proposal_number}}' ?>") : ("<?php echo $appconfig['proposal_prefix'] . '' . '{{proposal.proposal_id}}' ?>")'>

                                    </span>

                                    <span ng-bind='proposal.subject?("<?php echo '{{proposal.subject | limitTo :20}}'  ?>") : ("<?php echo '{{ proposal.subject | limitTo: 20 }}' ?>")'>

                                    </span>

                                </p>

                                <p>

                                    <strong><?php echo lang('customer'); ?>:</strong>

                                    <span>{{ proposal.email | limitTo: 30 }}{{proposal.email.length > 30 ? '...' : ''}}</span>

                                </p>

                            </div>

                        </a>

                        <md-divider inset></md-divider>

                    </md-list-item>

                </md-list>

            </section>

            <section ng-show="searchCustomers.length > 0">

                <md-subheader class="md-accent">

                    <span class="material-icons ico-ciuis-customers search-icon pull-right"></span>

                    <?php echo lang('customers'); ?>

                </md-subheader>

                <md-list layout-padding>

                    <md-list-item class="md-3-line search-item" ng-repeat="customer in searchCustomers">

                        <a href="{{appurl + 'customers/customer/' + customer.id}}">

                            <div class="md-list-item-text">

                                <p>

                                    <span class="blur5" ng-bind='customer.customer_number?("<?php echo '{{customer.customer_number}}' ?>") : ("<?php echo $appconfig['customer_prefix'] . '' . '{{customer.id}}' ?>")'>

                                    </span>

                                    <span ng-bind='customer.name?("<?php echo '{{customer.name | limitTo :20}}'  ?>") : ("<?php echo '{{ customer.company | limitTo: 20 }}' ?>")'>

                                    </span>

                                </p>

                                <p>

                                    <strong><?php echo lang('email'); ?>:</strong>

                                    <span>{{ customer.email | limitTo: 30 }}{{customer.email.length > 30 ? '...' : ''}}</span>

                                </p>

                            </div>

                        </a>

                        <md-divider inset></md-divider>

                    </md-list-item>

                </md-list>

            </section>

            <section ng-show="searchLeads.length > 0">

                <md-subheader class="md-accent">

                    <span class="material-icons ico-ciuis-leads search-icon pull-right"></span>

                    <?php echo lang('leads'); ?>

                </md-subheader>

                <md-list layout-padding>

                    <md-list-item class="md-3-line search-item" ng-repeat="lead in searchLeads">

                        <a href="{{appurl + 'leads/lead/' + lead.id}}">

                            <div class="md-list-item-text">

                                <p>

                                    <span class="blur5" ng-bind='lead.lead_number?("<?php echo '{{lead.lead_number}}' ?>") : ("<?php echo $appconfig['lead_prefix'] . '' . '{{lead.id}}' ?>")'>

                                    </span>

                                    <span ng-bind='lead.name?("<?php echo '{{lead.name | limitTo :20}}'  ?>") : ("")'>

                                    </span>

                                </p>

                                <p>

                                    <strong><?php echo lang('customer'); ?>:</strong>

                                    <span>{{ lead.company | limitTo: 30 }}{{lead.company.length > 30 ? '...' : ''}}</span>

                                </p>

                            </div>

                        </a>

                        <md-divider inset></md-divider>

                    </md-list-item>

                </md-list>

            </section>

            <section ng-show="searchExpenses.length > 0">

                <md-subheader class="md-accent">

                    <span class="material-icons ico-ciuis-expenses search-icon pull-right"></span>

                    <?php echo lang('expenses'); ?>

                </md-subheader>

                <md-list layout-padding>

                    <md-list-item class="md-3-line search-item" ng-repeat="expense in searchExpenses">

                        <a href="{{appurl + 'expenses/receipt/' + expense.id}}">

                            <div class="md-list-item-text">

                                <h4 class="blur5" ng-bind='expense.expense_number?(expense.expense_number):("<?php echo $appconfig['expense_prefix'] . '' . '{{expense.id}}' ?>")'>

                                </h4>

                                <p>

                                    <strong><?php echo lang('title'); ?>:</strong>

                                    <span>{{ expense.title | limitTo: 30 }}{{expense.title.length > 30 ? '...' : ''}}</span>

                                </p>

                            </div>

                        </a>

                        <md-divider inset></md-divider>

                    </md-list-item>

                </md-list>

            </section>

            <section ng-show="searchProducts.length > 0">

                <md-subheader class="md-accent"><span class="material-icons ico-ciuis-products search-icon pull-right"></span>

                    <?php echo lang('products'); ?></md-subheader>

                <md-list layout-padding>

                    <md-list-item class="md-3-line search-item" ng-repeat="product in searchProducts">

                        <a href="{{appurl + 'products/product/' + product.id}}">

                            <div class="md-list-item-text">

                                <p>

                                    <span class="blur5" ng-bind='product.product_number?("<?php echo '{{product.product_number}}' ?>") : ("<?php echo $appconfig['product_prefix'] . '' . '{{product.id}}' ?>")'>

                                    </span>

                                    <span ng-bind='product.name?("<?php echo '{{product.name | limitTo :20}}'  ?>") : ("")'>

                                    </span>

                                </p>

                                <p>

                                    <strong><?php echo lang('status'); ?>:</strong>

                                    <span>{{ product.description | limitTo: 30 }}{{product.description.length > 30 ? '...' : ''}}</span>

                                </p>

                            </div>

                        </a>

                        <md-divider inset></md-divider>

                    </md-list-item>

                </md-list>

            </section>

            <section ng-show="searchTickets.length > 0">

                <md-subheader class="md-accent">

                    <span class="material-icons ico-ciuis-supports search-icon pull-right"></span>

                    <?php echo lang('tickets'); ?>

                </md-subheader>

                <md-list layout-padding>

                    <md-list-item class="md-3-line search-item" ng-repeat="ticket in searchTickets">

                        <a href="{{appurl + 'tickets/ticket/' + ticket.id}}">

                            <div class="md-list-item-text">

                                <p>

                                    <span class="blur5" ng-bind='ticket.ticket_number?("<?php echo '{{ticket.ticket_number}}' ?>") : ("<?php echo $appconfig['ticket_prefix'] . '' . '{{ticket.id}}' ?>")'>

                                    </span>

                                    <span ng-bind='ticket.subject?("<?php echo '{{ticket.subject | limitTo :20}}'  ?>") : ("")'>

                                    </span>

                                </p>

                                <p>

                                    <strong><?php echo lang('message'); ?>:</strong>

                                    <span>{{ ticket.message | limitTo: 30 }}{{ticket.message.length > 30 ? '...' : ''}}</span>

                                </p>

                            </div>

                        </a>

                        <md-divider inset></md-divider>

                    </md-list-item>

                </md-list>

            </section>

            <section ng-show="searchTasks.length > 0">

                <md-subheader class="md-accent">

                    <span class="material-icons ico-ciuis-tasks search-icon pull-right"></span>

                    <?php echo lang('tasks'); ?>

                </md-subheader>

                <md-list layout-padding>

                    <md-list-item class="md-3-line search-item" ng-repeat="task in searchTasks">

                        <a href="{{appurl + 'tasks/task/' + task.id}}">

                            <div class="md-list-item-text">

                                <p>

                                    <span class="blur5" ng-bind='task.task_number?("<?php echo '{{task.task_number}}' ?>") : ("<?php echo $appconfig['task_prefix'] . '' . '{{task.id}}' ?>")'>

                                    </span>

                                    <span ng-bind='task.name?("<?php echo '{{task.name | limitTo :20}}'  ?>") : ("")'>

                                    </span>

                                </p>

                                <p>

                                    <strong><?php echo lang('assigned'); ?>:</strong>

                                    <span>{{ task.staff | limitTo: 30 }}{{task.staff.length > 30 ? '...' : ''}}</span>

                                </p>

                            </div>

                        </a>

                        <md-divider inset></md-divider>

                    </md-list-item>

                </md-list>

            </section>

            <section ng-show="searchOrders.length > 0">

                <md-subheader class="md-accent">

                    <span class="material-icons ion-ios-filing-outline search-icon pull-right"></span>

                    <?php echo lang('orders'); ?>

                </md-subheader>

                <md-list layout-padding>

                    <md-list-item class="md-3-line search-item" ng-repeat="order in searchOrders">

                        <a href="{{appurl + 'orders/order/' + order.id}}">

                            <div class="md-list-item-text">

                                <p>

                                    <span class="blur5" ng-bind='order.order_number?("<?php echo '{{order.order_number}}' ?>") : ("<?php echo $appconfig['order_prefix'] . '' . '{{order.id}}' ?>")'>

                                    </span>

                                    <span ng-bind='order.subject?("<?php echo '{{order.subject | limitTo :20}}'  ?>") : ("")'>

                                    </span>

                                </p>

                                <p>

                                    <strong><?php echo lang('customer'); ?>:</strong>

                                    <span>{{order.name?(order.name):(order.company) | limitTo: 30 }}{{order.name.length > 30 ? '...' : ''}}</span>

                                </p>

                            </div>

                        </a>

                        <md-divider inset></md-divider>

                    </md-list-item>

                </md-list>

            </section>

            <section ng-show="searchVendors.length > 0">

                <md-subheader class="md-accent">

                    <span class="material-icons ion-social-buffer-outline search-icon pull-right"></span>

                    <?php echo lang('vendors'); ?>

                </md-subheader>

                <md-list layout-padding>

                    <md-list-item class="md-3-line search-item" ng-repeat="vendor in searchVendors">

                        <a href="{{appurl + 'vendors/vendor/' + vendor.id}}">

                            <div class="md-list-item-text">

                                <p>

                                    <span class="blur5" ng-bind='vendor.vendor_number?("<?php echo '{{vendor.vendor_number}}' ?>") : ("<?php echo $appconfig['vendor_prefix'] . '' . '{{vendor.id}}' ?>")'>

                                    </span>

                                    <span ng-bind='vendor.company?("<?php echo '{{vendor.company | limitTo :20}}'  ?>") : ("")'>

                                    </span>

                                </p>

                                <p>

                                    <strong><?php echo lang('vendor') . ' ' . lang('name'); ?>:</strong>

                                    <span>{{ vendor.company | limitTo: 30 }}{{vendor.company.length > 30 ? '...' : ''}}</span>

                                </p>

                            </div>

                        </a>

                        <md-divider inset></md-divider>

                    </md-list-item>

                </md-list>

            </section>

            <section ng-show="searchPurchases.length > 0">

                <md-subheader class="md-accent">

                    <span class="material-icons ion-ios-cart-outline search-icon pull-right"></span>

                    <?php echo lang('purchases'); ?>

                </md-subheader>

                <md-list layout-padding>

                    <md-list-item class="md-3-line search-item" ng-repeat="purchase in searchPurchases">

                        <a href="{{appurl + 'purchases/purchase/' + purchase.purchase_id}}">

                            <div class="md-list-item-text">

                                <h4 class="blur5" ng-bind='purchase.purchase_number?(purchase.purchase_number):("<?php echo $appconfig['purchase_prefix'] . '' . '{{purchase.purchase_id}}' ?>")'></h4>

                                <p>

                                    <strong><?php echo lang('vendor'); ?>:</strong>

                                    <span>{{ purchase.company | limitTo: 30 }}{{purchase.company.length > 30 ? '...' : ''}}</span>

                                </p>

                            </div>

                        </a>

                        <md-divider inset></md-divider>

                    </md-list-item>

                </md-list>

            </section>

            <section ng-show="searchDeposits.length > 0">

                <md-subheader class="md-accent">

                    <span class="material-icons ion-ios-paper-outline search-icon pull-right"></span>

                    <?php echo lang('deposits'); ?>

                </md-subheader>

                <md-list layout-padding>

                    <md-list-item class="md-3-line search-item" ng-repeat="deposit in searchDeposits">

                        <a href="{{appurl + 'deposits/deposit/' + deposit.id}}">

                            <div class="md-list-item-text">

                                <h4 class="blur5" ng-bind='deposit.deposit_number?(deposit.deposit_number):("<?php echo $appconfig['deposit_prefix'] . '' . '{{deposit.id}}' ?>")'>

                                </h4>

                                <p>

                                    <strong><?php echo lang('title'); ?>:</strong>

                                    <span>{{ deposit.title | limitTo: 30 }}{{deposit.title.length > 30 ? '...' : ''}}</span>

                                </p>

                            </div>

                        </a>

                        <md-divider inset></md-divider>

                    </md-list-item>

                </md-list>

            </section>

        </md-content>

    </md-sidenav>

    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Todo" ng-cloak style="width: 450px;">

        <md-content layout-padding="">

            <div ng-show="loadingtodo">

                <br>

                <md-progress-circular md-mode="indeterminate" md-diameter="20" style="margin-left: auto;margin-right: auto;">

                </md-progress-circular>

            </div>

            <md-content ng-hide="loadingtodo" layout-padding="">

                <md-input-container class="md-icon-float md-icon-right md-block">

                    <textarea ng-model="tododetail" placeholder="<?php echo lang('type_todo') ?>" class="tododetail"></textarea>

                    <md-icon class="" aria-label='Add Todo'>

                        <md-tooltip md-direction="bottom"><?php echo lang('add') ?></md-tooltip>

                        <md-progress-circular ng-show="addingTodo == true" md-mode="indeterminate" md-diameter="18"></md-progress-circular>

                        <i ng-hide="addingTodo == true" ng-click="AddTodo()" class="ion-android-send text-success cursor"></i>

                    </md-icon>

                </md-input-container>

                <h4 md-truncate class=" text-muted text-uppercase"><strong ng-bind='lang.new'></strong></h4>

                <md-content layout-padding="">

                    <ul class="todo-item">

                        <li ng-repeat="todo in todos" class="todo-alt-item todo">

                            <div class="todo-c" style="display: grid;margin-top: 10px;">

                                <div class="todo-item-header">

                                    <div class="btn-group-sm btn-space pull-right">

                                        <button data-id='{{todo.id}}' ng-click='TodoAsDone($index)' class="btn btn-default btn-sm ion-checkmark">

                                            <md-tooltip md-direction="top"><?php echo lang('mark_as_done') ?>

                                            </md-tooltip>

                                        </button>

                                        <button data-id='{{todo.id}}' ng-click='DeleteTodo($index)' class="btn btn-default btn-sm ion-trash-a">

                                            <md-tooltip md-direction="top"><?php echo lang('delete') ?></md-tooltip>

                                        </button>

                                    </div>

                                    <span style="padding:5px;" class="pull-left label label-default" ng-bind="todo.date | date : 'MMM d, y h:mm:ss a'"></span>

                                </div>

                                <br>

                                <p class="todo-desc" ng-bind="todo.description"></p>

                            </div>

                        </li>

                    </ul>

                </md-content>

                <h4 md-truncate class=" text-success"><strong ng-bind='lang.donetodo'></strong></h4>

                <md-content layout-padding="">

                    <ul class="todo-item-done">

                        <li ng-class="{ 'donetodo-x' : todo.done }" ng-repeat="done in tododone" class="todo-alt-item-done todo">

                            <div class="todo-c" style="display: grid;margin-top: 10px;">

                                <div class="todo-item-header">

                                    <div class="btn-group-sm btn-space pull-right">

                                        <button data-id='{{todo.id}}' ng-click='TodoAsUnDone($index)' class="btn btn-default btn-sm ion-refresh">

                                            <md-tooltip md-direction="top"><?php echo lang('mark_as_undone') ?>

                                            </md-tooltip>

                                        </button>

                                        <button data-id='{{todo.id}}' ng-click='DeleteTodoDone($index)' class="btn btn-default btn-sm ion-trash-a">

                                            <md-tooltip md-direction="top"><?php echo lang('delete') ?></md-tooltip>

                                        </button>

                                    </div>

                                    <span style="padding:5px;" class="pull-left label label-success" ng-bind="done.date | date : 'MMM d, y h:mm:ss a'"></span>

                                </div>

                                <br>

                                <p class="todo-desc" ng-bind="done.description"></p>

                            </div>

                        </li>

                    </ul>

                </md-content>

            </md-content>

        </md-content>

    </md-sidenav>



    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Notifications" ng-cloak style="width: 450px;">
        <md-toolbar class="md-theme-light" style="background:#262626">
            <div class="md-toolbar-tools">
                <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>
                <md-truncate></md-truncate>
            </div>
        </md-toolbar>
        <md-content class="saudacao" style="max-width: 100%;">

            <div class="titleSaud" style="margin: 0 auto;margin-top: 5px;">Avisos & Alertas
                <?php if (check_privilege('tickets', 'create')) { ?>
                    <!--
                    <md-button ng-click="close();CreateChat();atualiza_departament();" class="md-icon-button" aria-label="New" ng-cloak style="float: right;margin-top: -8px;">
                        <md-tooltip md-direction="bottom">Novo Chat</md-tooltip>
                        <md-icon><i style="color: #fff;" class="ion-plus-round text-muted"></i></md-icon>
                    </md-button>
                    -->
                <?php } ?>
            </div>

            <div class="content-saud">

                <ul>
                    <li ng-repeat="notification in settings.notifications" ng-click="openAviso(notification)" ng-class="{ 'saudacao_lido' : notification.markread == 1 }">
                        <img class="avatar" ng-src="{{appurl + 'uploads/images/' + notification.staffavatar}}" alt="{{appurl + 'uploads/images/' + notification.staffname}}">
                        <div class="notification-detail"><span ng-bind-html="notification.detail"></div>
                        <div class="notification-date">{{notification.date | date:"dd/MM/yyyy HH:mm:ss"}}</div>
                    </li>
                </ul>

                <p style="text-align: center;font-size: 13px;color: #545454;" ng-show="settings.notifications.length == 0">Nenhuma notificação por enquanto!</p>

            </div>

        </md-content>
    </md-sidenav>


    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Mensagens_chat" ng-cloak style="width: 450px;">
        <md-toolbar class="md-theme-light" style="background:#262626">
            <div class="md-toolbar-tools">
                <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>
                <md-truncate></md-truncate>
            </div>
        </md-toolbar>
        <md-content class="saudacao" style="max-width: 100%;">
            <div class="titleSaud" style="margin: 0 auto;margin-top: 5px;">Next Message
            </div>

            <div class="content-saud">
                <div class="input-group mb-3 input-search" style="margin-top: 0px;">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" ng-model="searchNot" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
                </div>

                <div ng-click="setChat(chat)" class="loadMensagem" ng-repeat="chat in chats | filter : searchPanel">
                    <img src="{{appurl + 'uploads/images/' + chat.details.staffavatar}}">
                    <a style="max-width: 100%;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;" href="javaScript:void(0)"><b ng-bind="chat.details.staffname"></b></a>
                    <p style="max-width: calc(100% - 75px);max-width: calc(100% - 75px); overflow: hidden;text-overflow: ellipsis;display: -webkit-box;-webkit-line-clamp: 2;-webkit-box-orient: vertical;" ng-bind="chat.ultimaMsg"></p>
                    <i ng-if="chat.msg_nova == 1" style="color: #17c93e;font-size: 11px;float: right;" class="fas fa-circle"></i>
                </div>

            </div>

        </md-content>
    </md-sidenav>

    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="CreateChat" ng-cloak style="width: 450px;">
        <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">
                <md-button ng-click="close()" class="md-icon-button" aria-label="Close">
                    <i class="ion-android-arrow-forward"></i>
                </md-button>
                <md-truncate><?php echo lang('create') ?></md-truncate>
            </div>

        </md-toolbar>
        <md-content layout-padding="">
            <md-content layout-padding>
                <?php
                helper('form');
                echo form_open_multipart('chat/create'); ?>

                <md-input-container class="md-block" flex-gt-xs>
                    <label>Enviar para</label>
                    <md-select required ng-model="chat.user" name="user">
                        <md-option ng-value="user.id" ng-repeat="user in users">{{user.name}}</md-option>
                    </md-select><br>
                </md-input-container>

                <md-input-container class="md-block" flex-gt-xs>
                    <label><?php echo lang('department'); ?></label>
                    <md-select required ng-model="chat.department" name="department">
                        <md-option ng-value="department.id" ng-repeat="department in departments">{{department.name}}</md-option>
                    </md-select><br>
                </md-input-container>

                <md-input-container class="md-block" flex-gt-xs>
                    <label><?php echo lang('priority'); ?></label>
                    <md-select ng-init="priorities = [{id: 1,name: '<?php echo lang('low'); ?>'}, {id: 2,name: '<?php echo lang('medium'); ?>'}, {id: 3,name: '<?php echo lang('high'); ?>'}];" required placeholder="<?php echo lang('priority'); ?>" ng-model="chat.priority" name="priority">
                        <md-option ng-value="priority.id" ng-repeat="priority in priorities"><span class="text-uppercase">{{priority.name}}</span></md-option>
                    </md-select><br>
                </md-input-container>

                <md-input-container class="md-block">
                    <label><?php echo lang('message') ?></label>
                    <textarea required name="message" ng-model="chat.message" placeholder="<?php echo lang('typeSomething'); ?>" class="form-control"></textarea>
                </md-input-container>

                <div class="file-upload">
                    <div class="file-select">
                        <div class="file-select-button" id="fileName"><span class="mdi mdi-accounts-list-alt"></span> <?php echo lang('attachment') ?></div>
                        <div class="file-select-name" id="noFile"><?php echo lang('nofile') ?></div>
                        <input type="file" name="attachment" id="chooseFile" file-model="chat.chat_attachment">
                    </div>
                </div>
                <br>

                <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>
                    <md-button ng-click="createChat()" class="md-raised md-primary btn-report block-button" ng-disabled="uploading == true">
                        <span ng-hide="uploading == true"><?php echo lang('send'); ?></span>
                        <md-progress-circular class="white" ng-show="uploading == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>
                    </md-button>
                    <br /><br /><br /><br />
                </section>

                <?php echo form_close(); ?>
            </md-content>

        </md-content>
    </md-sidenav>

    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Profile" ng-cloak style="width: 450px;top: 0 !important;">

        <md-content>

            <md-tabs md-dynamic-height md-border-bottom>

                <md-tab label="Profile">

                    <md-content layout-padding class="md-mt-10 text-center" style="line-height: 0px;height:200px"> <img style="border-radius: 50%; box-shadow: 0 0 20px 0px #00000014;" height="100px" width="auto" ng-src="{{appurl + 'uploads/images/' + user.avatar}}" class="md-avatar" alt="{{user.name}}" />

                        <h3><strong ng-bind="user.name"></strong></h3>

                        <br>

                        <span ng-bind="user.email"></span>

                    </md-content>

                    <md-content class="md-mt-30 text-center">

                        <md-button ng-show="ONLYADMIN != 'true'" ng-href="{{appurl + 'staff/profile'}}" class="md-raised" ng-bind='lang.profile' aria-label='Profile'></md-button>

                        <md-button ng-show="ONLYADMIN == 'true'" ng-href="{{appurl + 'staff/staffmember/' + activestaff}}" class="md-raised" ng-bind='lang.profile' aria-label='Profile'></md-button>

                        <md-button ng-href="{{appurl + 'login/logout'}}" class="md-raised" ng-bind='lang.logout' aria-label='LogOut'></md-button>

                    </md-content>

                </md-tab>

                <md-tabs>

        </md-content>
    </md-sidenav>

    <div id="background-menu"></div>

    <div class="fabs fabs54" ng-if="CHAT_ID != null">
        <div class="chat">
            <div class="chat_header">
                <div class="chat_option">
                    <div class="header_img">
                        <img ng-if="CHAT_ID != 0" src="{{appurl + 'uploads/images/' + chatAtual.details.staffavatar}}" />
                        <img ng-if="CHAT_ID == 0" src="{{appurl + 'uploads/images/avatar-ia.png'}}" />
                    </div>
                    <span ng-if="CHAT_ID != 0" id="chat_head">{{chatAtual.details.staffname}}</span>
                    <span ng-if="CHAT_ID == 0" id="chat_head">Suporte Next1Bot</span>

                    <span id="chat_close_loader" class="chat_fullscreen_loader">
                        <i class=" zmdi zmdi-close"></i>
                    </span>

                    <span id="chat_fullscreen_loader" class="chat_fullscreen_loader">
                        <i class="fullscreen zmdi zmdi-window-maximize"></i>
                    </span>
                </div>
            </div>

            <div id="chat_form" class="chat_converse chat_form">


                <div ng-repeat="msg in chatAtual.replies">
                    <div ng-if="msg.send_staff == chatAtual.details.id">
                        <div style="float: left;width: 100%;">
                            <span class="chat_msg_item chat_msg_item_admin">
                                <div class="chat_avatar">
                                    <img ng-if="CHAT_ID != 0" src="{{appurl + 'uploads/images/' + chatAtual.details.staffavatar}}" />
                                    <img ng-if="CHAT_ID == 0" src="{{appurl + 'uploads/images/avatar-ia.png'}}" />
                                </div>
                                <span ng-bind-html="msg.message"></span>
                            </span>
                        </div>
                        <span style="float: left;margin-top: -8px;" class="status">{{msg.date}}</span>
                    </div>
                    <div ng-if="msg.send_staff != chatAtual.details.id">
                        <div style="float: left;width: 100%;">
                            <span class="chat_msg_item chat_msg_item_user">
                                <span ng-bind-html="msg.message"></span>
                            </span>
                        </div>
                        <span class="status">{{msg.date}}</span>
                    </div>
                </div>

                <div ng-if="CHAT_ID == 0 && processando_chat">
                    <div style="float: left;width: 100%;">
                        <span class="chat_msg_item chat_msg_item_admin">
                            <div class="chat_avatar">
                                <img src="{{appurl + 'uploads/images/avatar-ia.png'}}" />
                            </div>
                            <span><img style="width: 30px;" src="{{appurl + 'assets/img/dots.gif'}}" /></span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="fab_field">
                <input style="display:none" type="file" name="attachment" id="chooseFileChat" file-model="reply_chat.attachment">
                <a ng-if="CHAT_ID != 0" id="btn_file_chat" class="fab fab_camera"><i class="zmdi zmdi-camera"></i></a>
                <a ng-click="replyToChat()" class="fab fab_send"><i class="zmdi zmdi-mail-send"></i></a>
                <textarea ng-model="reply_chat.message" ng-keydown="checkEnter($event)" id="chatSend" name="chat_message" placeholder="Envie uma mensagem" class="chat_field chat_message"></textarea>
            </div>
        </div>
        <a id="prime" class="fab"><i class="prime zmdi zmdi-comment-outline "></i></a>
    </div>

    <div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-body">
                    <button type="button" class="close" ng-click="closeModal()" style="padding: 10px;position: absolute;right: 0;top: 0;background: #fff;opacity: 1;">
                        <span aria-hidden="true">×</span> <span class="sr-only"><?php echo lang('close') ?></span>
                    </button>

                    <div class="notification-container">
                        <div class="content-saud">
                            <img class="avatar" ng-src="{{appurl + 'uploads/images/' + notificationModal.staffavatar}}" alt="{{notificationModal.staffname}}">
                            <div class="notification-detail"><span ng-bind-html="notificationModal.detail"></span></div>
                            <div class="notification-date">{{notificationModal.date | date:"dd/MM/yyyy HH:mm:ss"}}</div>
                        </div>
                        <div style="text-align: center;">
                            <img class="fotoNotificacao" ng-if="notificationModal.anexo != null" ng-src="{{appurl + 'uploads/anexos/' + notificationModal.anexo}}" alt="Anexo da Notificação">

                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>


    <md-content class="ciuis-body-wrapper ciuis-body-fixed-sidebar" ciuis-ready>
        <script>
            var menu_open = false;
            $(document).ready(function() {
                $("#background-menu").click(function() {
                    $(this).addClass('is-active');
                    $('.app-container').addClass('closed-sidebar');
                    $('.ciuis-body-wrapper').removeClass('menuOpen');
                    $('.ciuis-body-wrapper').css('margin-left', '80px');
                    $('.ciuis-body-wrapper').css('width', 'calc(100% - 80px)');
                    $('.btn-menu-mobile').attr('style', "display:block !important");
                    $('#background-menu').hide();
                    setTimeout(a => {
                        menu_open = false;
                    }, 300)
                })

                $(".url-click").click(function() {
                    var url = $(this).attr("data-url");
                    if (url != '#') {
                        window.location.href = url;
                    }
                });

                $(".app-sidebar").hover(function() {
                    if ($(window).width() > 700) {
                        if (!menu_open) {
                            if ($('.app-container').hasClass('closed-sidebar')) {
                                $('.app-container').removeClass('closed-sidebar');

                            } else {
                                $('.app-container').addClass('closed-sidebar');
                            }
                        }
                    }
                });



                $(".close-sidebar-btn").click(function() {

                    if (!menu_open) {
                        $(this).removeClass('is-active');
                        $('.app-container').removeClass('closed-sidebar');
                        $('.ciuis-body-wrapper').addClass('menuOpen');

                        menu_open = true;
                        $('.ciuis-body-wrapper').css('margin-left', '280px');
                        $('.ciuis-body-wrapper').css('width', 'calc(100% - 280px)');
                        if ($(window).width() < 700) {
                            $('.btn-menu-mobile').attr('style', "display:none !important");
                            $('#background-menu').show();
                        }


                        //$('.card-header').show();
                    } else {
                        $(this).addClass('is-active');
                        $('.app-container').addClass('closed-sidebar');
                        $('.ciuis-body-wrapper').removeClass('menuOpen');
                        $('#background-menu').hide();

                        $('.ciuis-body-wrapper').css('margin-left', '80px');
                        $('.ciuis-body-wrapper').css('width', 'calc(100% - 80px)');
                        if ($(window).width() < 700) {
                            $('.btn-menu-mobile').attr('style', "display:block !important");
                        }
                        //$('.card-header').hide();
                        setTimeout(a => {
                            menu_open = false;
                        }, 300)

                    }

                })


            });
        </script>