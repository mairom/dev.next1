<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>

<?php
/*
$rebrand = load_config();
$appconfig = get_appconfig();
$menus = get_menu();
$leftmenus = get_leftmenu();
$user_data = get_user();
$app_logo = base_url('uploads/ciuis_settings/' . $user_data['settings']['logo']);
$app_logo_alternate = base_url('assets/img/placeholder.png');
$user_image = base_url('uploads/images/' . $user_data['avatar']);
$user_image_alternate = base_url('uploads/images/n-img.jpg');

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
    <link href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed:700,300,300italic" rel="stylesheet" type="text/css">
    <title><?php echo $title; ?></title>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/ciuis.css?v=1.3.4'); ?>" type="text/css" />
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/fontawesome/css/all.css'); ?>">


    <script src="<?php echo base_url('assets/lib/jquery/jquery.min.js'); ?>" type="text/javascript"></script>

    <script src="<?php echo base_url('assets/lib/angular/angular.min.js'); ?>"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
    <script type="text/javascript" src="<?= base_url('assets/lib/sortable/src/sortable.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/angular/angular-animate.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/angular/angular-aria.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/angular/i18n/angular-locale_' . lang2('lang_code_dash') . '.js'); ?>"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.7.8/angular-sanitize.js"></script>

    <script src="<?php echo base_url('assets/lib/sweetalert2/dist/sweetalert2.all.min.js'); ?>"></script>
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/lib/sweetalert2/dist/sweetalert2.min.css'); ?>">

    <script src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
    <script src="<?php echo base_url('assets/js/main.js?v=1.2.1'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/mask/jquery.mask.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/mask/jquery.mask2.min.js'); ?>"></script>

    <script>
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

    <script>
        var BASE_URL = "<?php echo base_url(); ?>",
            update_error = "<?php echo lang2('update_error'); ?>",
            email_error = "<?php echo lang2('email_error'); ?>",
            ACTIVESTAFF = "<?php echo session()->get('usr_id'); ?>",
            SHOW_ONLY_ADMIN = "<?php if (!if_admin) {
                                    echo 'true';
                                } else echo 'false'; ?>",
            CURRENCY = "<?php echo currency ?>",
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
    </script>

</head>

<?php $settings = $this->Settings_Model->get_settings_ciuis();?>

<body ng-controller="Ciuis_Controller">

    <md-toolbar class="toolbar-ciuis-top">

        <div class="md-toolbar-tools">

            <!-- CRM NAME -->

            <div md-truncate class="crm-name crm-nm"><span><?php echo $user_data['settings']['crm_name'] ?></span></div>

            <md-button ng-click="OpenMenu()" class="md-icon-button hidden-lg hidden-md" aria-label="Menu">

                <md-icon><i class="ion-navicon-round text-muted"></i></md-icon>

            </md-button>

            <!-- CRM NAME -->

            <!-- NAVBAR MENU -->

            <ul flex class="ciuis-v3-menu hidden-xs">



                <?php foreach ($menus as $menu) { ?>

                    <?php if ($menu['url'] != '#' || sizeof($menu['sub_menu']) > 0) { ?>

                        <li><a href="<?php echo $menu['url'] ?>"><?php echo $menu['name'] ?></a>

                            <?php if (sizeof($menu['sub_menu']) > 0) { ?>

                                <ul>

                                    <?php foreach ($menu['sub_menu'] as $submenu) { ?>

                                        <li><a href="<?php echo $submenu['url'] ?>">
                                                <?php
                                                if ($submenu['icon_svg'] == "") {
                                                ?>
                                                    <i class="icon <?php echo $submenu['icon'] ?>"></i>
                                                <?php
                                                } else {
                                                ?>
                                                    <img style="position: absolute;transition: all 0.1s ease-in-out;width: 55px;text-align: center;left: 15px;top: 3px;" src='<?= base_url($submenu['icon_svg']) ?>'>
                                                <?php
                                                }
                                                ?>
                                                <span class="title"><?php echo $submenu['name'] ?></span> <span class="descr"><?php echo $submenu['description'] ?></span> </a>

                                        </li>

                                    <?php } ?>

                                </ul>

                            <?php } ?>

                        </li>

                    <?php } ?>

                <?php } ?>

                <?php
                if ($user_data['super_admin'] == "1") {
                ?>
                    <li><a href="/companies">Empresas</a></li>
                <?php
                }
                ?>

            </ul>

            <!-- NAVBAR MENU -->

            <md-button class="md-icon-button" ng-click="searchNav()" aria-label="search" ng-cloak>

                <md-tooltip md-direction="left" ng-bind='lang.search'></md-tooltip>

                <md-icon><i class="ion-search text-muted"></i></md-icon>

            </md-button>

            <?php if (!session()->get('other')) { ?>

                <div class="dropdown-container timer" ng-cloak>

                    <md-button ng-click="getTimersList()" id="getTimer" class="md-icon-button dropdown-toggle" data-toggle="dropdown" aria-label="search">

                        <md-tooltip md-direction="left" ng-bind='lang.timer'></md-tooltip>

                        <md-icon>

                            <i class="ion-ios-clock text-muted" id="timerStart" ng-hide="settings.timers == true || settings.timers == 'true'"></i>

                            <i id="timerStarted" ng-show="settings.timers==true || settings.timers=='true'" class="ion-ios-clock text-success"></i>

                        </md-icon>

                    </md-button>

                </div>

            <?php } ?>

            <md-button ng-hide="ONLYADMIN != 'true'" class="md-icon-button" ng-href="{{appurl + 'settings'}}" aria-label="Settings" ng-cloak>

                <md-tooltip md-direction="left" ng-bind='lang.settings'></md-tooltip>

                <md-icon><i class="ion-gear-a text-muted"></i></md-icon>

            </md-button>

            <md-button ng-click="Todo();get_todo()" class="md-icon-button" aria-label="Todo" ng-cloak>

                <md-tooltip md-direction="left" ng-bind='lang.todo'></md-tooltip>

                <md-icon><i class="ion-clipboard text-muted"></i></md-icon>

            </md-button>

            <md-button ng-click="Notifications();get_notifications()" class="md-icon-button" aria-label="Notifications" ng-cloak>

                <md-tooltip md-direction="left" ng-bind='lang.notifications'></md-tooltip>

                <div ng-show="settings.newnotification == true" class="notify"> <span class="heartbit"></span> <span class="point"></span> </div>

                <md-icon><i class="ion-ios-bell text-muted"></i></md-icon>

            </md-button>

            <md-button ng-click="Profile();get_meetings();get_appointments();" class="md-icon-button avatar-button-ciuis" aria-label="User Profile" ng-cloak>

                <img height="100%" src="<?php echo $user_image ?>" class="md-avatar" style="max-height: 36px;height: 100%;max-width: 40px;" onerror="this.onerror=null; this.src='<?php echo $user_image_alternate ?>'">

            </md-button>

            <div ng-click="Profile();get_meetings();get_appointments();" md-truncate class="user-informations hidden-xs" ng-cloak> <span class="user-name-in"><?php echo $user_data['name'] ?></span><br>

                <span class="user-email-in"><?php echo $user_data['email'] ?></span>

            </div>

        </div>

    </md-toolbar>

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

                            <?php foreach ($menus as $menu) { ?>
                                <span>
                                    <?php if ($menu['url'] != '#') { ?>
                                        <ul>
                                            <li class="nav-item">
                                                <div class="mobile-menu-item"><a href="<?php echo $menu['url'] ?>"><?php echo $menu['name'] . "-"  . $menu['url']  ?></a>
                                                </div>
                                            </li>
                                        </ul>
                                    <?php } ?>
                                    <ul>
                                        <?php foreach ($menu['sub_menu'] as $submenu) { ?>
                                            <li class="nav-item">
                                                <div class="mobile-menu-item"><a href="<?php echo $submenu['url'] ?>"><?php echo $submenu['name'] ?></a>
                                                </div>
                                            </li>
                                        <?php } ?>

                                    </ul>

                                </span>

                            <?php } ?>

                            <?php
                            if ($user_data['super_admin'] == "1") {
                            ?>
                                <span>
                                    <li class="nav-item">
                                        <div class="mobile-menu-item"><a href="/companies">Empresas</a>
                                        </div>
                                    </li>
                                </span>
                            <?php
                            }
                            ?>

                        </div>

                        <div class="clear"></div>

                    </div>

                </div>

            </div>

        </md-content>

    </md-content>

    <header id="mainHeader" role="banner" class="hidden-xs">

        <nav role="navigation">

            <div class="top-header">

                <div class="navBurger">

                    <a href="{{appurl + 'panel'}}">

                        <img class="transform_logo" width="34px" height="34px" src="<?php echo $app_logo ?>" onerror="this.onerror=null; this.src='<?php echo $app_logo_alternate ?>'">

                    </a>

                </div>

            </div>

            <ul id="menu-vertical-menu icon" class="nav">

                <?php foreach ($leftmenus as $leftmenu) { ?>

                    <?php if ($leftmenu['show_staff'] == '0') { ?>

                        <li class="material-icons <?php echo $leftmenu['icon'] ?>"><a href="<?php echo $leftmenu['url'] ?>"><?php echo $leftmenu['title'] ?></a></li>

                    <?php } ?>

                <?php } ?>

            </ul>

        </nav>

    </header>

    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="taskTimer" ng-cloak style="width: 450px;">

        <md-toolbar class="md-theme-light" style="background:#262626">

            <div class="md-toolbar-tools">

                <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>

                <md-truncate flex><?php echo lang2('task') . ' ' . lang2('timer') ?></md-truncate>

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

                                <span ng-show="time.task_id"><?php echo lang2('task') ?>: </span> <a ng-show="time.task_id" href="{{appurl + 'tasks/task/' + time.task_id}}" class="assigned"><strong ng-bind="time.task"></strong></a>

                                <a ng-show="!time.task_id" class="label label-info assign" ng-click="stopTimerWithTask('assign',time.id)"><?php echo lang2('assign_task') ?>

                                    &nbsp;&nbsp;<i class="ion-compose"></i></a>

                                <br>

                                <span class="text-muted"><?php echo lang2('started_at') ?>: {{time.started}}</span>

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

                    <p ng-show="!timers.length" class="not-found"><?php echo lang2('not_found') ?></p>

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

                    <textarea required ng-model="onsite_visit.description" placeholder="<?php echo lang2('typeSomething'); ?>" class="form-control note-description"></textarea>

                </md-input-container>

                <div class="pull-right">

                    <md-button ng-click="AddOnsiteVisit()" class="md-raised md-primary md-button md-ink-ripple" ng-disabled="addingOnsite == true" aria-label='Add Onsite Visit'>

                        <span ng-hide="addingOnsite == true"><?php echo lang2('set'); ?></span>

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

                <label><?php echo lang2('searchhere'); ?></label>

                <input ng-submit="searchInput(search_input)" name="search" ng-model="search_input" ng-keyup="searchInput(search_input)">

            </md-input-container>

            <p class="text-center text-muted" ng-show="searchResult == 1"><?php echo lang2('not_found'); ?></p>

            <p class="text-center text-muted" ng-show="searchInputMsg == 1"><?php echo lang2('type_something'); ?></p>

            <div ng-show="searchLoader == 1">

                <md-progress-circular md-mode="indeterminate" md-diameter="20" style="margin-left: auto;margin-right: auto;">

                </md-progress-circular>

                <p class="text-center">

                    <strong><?php echo lang2('searching'); ?></strong>

                </p>

            </div>

            <section ng-show="searchStaff.length > 0">

                <md-subheader class="md-accent"><span class="material-icons ico-ciuis-staff search-icon pull-right"></span>

                    <?php echo lang2('staff_members'); ?>

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

                                    <strong><?php echo lang2('email'); ?>:</strong>

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

                    <?php echo lang2('projects'); ?>

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

                                    <strong><?php echo lang2('status'); ?>:</strong>

                                    <span ng-switch="project.status">

                                        <span ng-switch-when="1"><?php echo lang2('notstarted'); ?></span>

                                        <span ng-switch-when="2"><?php echo lang2('started'); ?></span>

                                        <span ng-switch-when="3"><?php echo lang2('percentage'); ?></span>

                                        <span ng-switch-when="4"><?php echo lang2('cancelled'); ?></span>

                                        <span ng-switch-when="5"><?php echo lang2('complete'); ?></span>

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

                    <span class="material-icons ico-ciuis-invoices search-icon pull-right"></span>

                    <?php echo lang2('invoices'); ?>

                </md-subheader>

                <md-list layout-padding>

                    <md-list-item class="md-3-line search-item" ng-repeat="invoice in searchInvoices">

                        <a href="{{appurl + 'invoices/invoice/' + invoice.invoice_id}}">

                            <div class="md-list-item-text">

                                <h4 class="blur5" ng-bind='invoice.invoice_number?(invoice.invoice_number):("<?php echo $appconfig['inv_prefix'] . '' . '{{invoice.invoice_id}}' ?>")'>

                                </h4>

                                <p>

                                    <strong><?php echo lang2('customer'); ?>:</strong>

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

                    <?php echo lang2('proposals'); ?>

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

                                    <strong><?php echo lang2('customer'); ?>:</strong>

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

                    <?php echo lang2('customers'); ?>

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

                                    <strong><?php echo lang2('email'); ?>:</strong>

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

                    <?php echo lang2('leads'); ?>

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

                                    <strong><?php echo lang2('customer'); ?>:</strong>

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

                    <?php echo lang2('expenses'); ?>

                </md-subheader>

                <md-list layout-padding>

                    <md-list-item class="md-3-line search-item" ng-repeat="expense in searchExpenses">

                        <a href="{{appurl + 'expenses/receipt/' + expense.id}}">

                            <div class="md-list-item-text">

                                <h4 class="blur5" ng-bind='expense.expense_number?(expense.expense_number):("<?php echo $appconfig['expense_prefix'] . '' . '{{expense.id}}' ?>")'>

                                </h4>

                                <p>

                                    <strong><?php echo lang2('title'); ?>:</strong>

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

                    <?php echo lang2('products'); ?></md-subheader>

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

                                    <strong><?php echo lang2('status'); ?>:</strong>

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

                    <?php echo lang2('tickets'); ?>

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

                                    <strong><?php echo lang2('message'); ?>:</strong>

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

                    <?php echo lang2('tasks'); ?>

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

                                    <strong><?php echo lang2('assigned'); ?>:</strong>

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

                    <?php echo lang2('orders'); ?>

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

                                    <strong><?php echo lang2('customer'); ?>:</strong>

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

                    <?php echo lang2('vendors'); ?>

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

                                    <strong><?php echo lang2('vendor') . ' ' . lang2('name'); ?>:</strong>

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

                    <?php echo lang2('purchases'); ?>

                </md-subheader>

                <md-list layout-padding>

                    <md-list-item class="md-3-line search-item" ng-repeat="purchase in searchPurchases">

                        <a href="{{appurl + 'purchases/purchase/' + purchase.purchase_id}}">

                            <div class="md-list-item-text">

                                <h4 class="blur5" ng-bind='purchase.purchase_number?(purchase.purchase_number):("<?php echo $appconfig['purchase_prefix'] . '' . '{{purchase.purchase_id}}' ?>")'></h4>

                                <p>

                                    <strong><?php echo lang2('vendor'); ?>:</strong>

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

                    <?php echo lang2('deposits'); ?>

                </md-subheader>

                <md-list layout-padding>

                    <md-list-item class="md-3-line search-item" ng-repeat="deposit in searchDeposits">

                        <a href="{{appurl + 'deposits/deposit/' + deposit.id}}">

                            <div class="md-list-item-text">

                                <h4 class="blur5" ng-bind='deposit.deposit_number?(deposit.deposit_number):("<?php echo $appconfig['deposit_prefix'] . '' . '{{deposit.id}}' ?>")'>

                                </h4>

                                <p>

                                    <strong><?php echo lang2('title'); ?>:</strong>

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

            <md-content ng-show="!loadingtodo" layout-padding="">

                <md-input-container class="md-icon-float md-icon-right md-block">

                    <textarea ng-model="tododetail" placeholder="<?php echo lang2('type_todo') ?>" class="tododetail"></textarea>

                    <md-icon class="" aria-label='Add Todo'>

                        <md-tooltip md-direction="bottom"><?php echo lang2('add') ?></md-tooltip>

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

                                            <md-tooltip md-direction="top"><?php echo lang2('mark_as_done') ?>

                                            </md-tooltip>

                                        </button>

                                        <button data-id='{{todo.id}}' ng-click='DeleteTodo($index)' class="btn btn-default btn-sm ion-trash-a">

                                            <md-tooltip md-direction="top"><?php echo lang2('delete') ?></md-tooltip>

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

                                            <md-tooltip md-direction="top"><?php echo lang2('mark_as_undone') ?>

                                            </md-tooltip>

                                        </button>

                                        <button data-id='{{todo.id}}' ng-click='DeleteTodoDone($index)' class="btn btn-default btn-sm ion-trash-a">

                                            <md-tooltip md-direction="top"><?php echo lang2('delete') ?></md-tooltip>

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

                <md-truncate><?php echo lang2('notifications') ?></md-truncate>

                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                <a ng-click="markAllAsRead()" class="cursor" style="font-size: 14px;"><?php echo lang2('mark_all_as_read') ?></a>

            </div>

        </md-toolbar>

        <div ng-show="loadingnotification">
            <br>
            <md-progress-circular md-mode="indeterminate" md-diameter="20" style="margin-left: auto;margin-right: auto;">
            </md-progress-circular>
        </div>

        <md-content ng-show="!loadingnotification">
            <md-list flex>
                <?php
                
                if (session()->get('admin')) {
                    if (is_update_available() == true) { ?>
                        <md-list-item class="md-3-line new_notification" ng-href="{{appurl + 'settings'}}" aria-label="Read">
                            <img ng-src="{{appurl + 'assets/img/update.png'}}" class="update-ntf img" alt="NTF" on-error-src="<?php echo base_url('assets/img/placeholder.png') ?>" />
                            <div class="md-list-item-text" layout="column">
                                <h4><?php echo lang2('update_available') ?></h4>
                                <p><?php echo lang2('update_available_msg') ?></p>
                            </div>
                        </md-list-item>
                <?php }
                }
                 
                ?>
                <md-list-item class="md-3-line" ng-repeat="ntf in notifications.notifications" ng-click="NotificationRead($index)" ng-class="{new_notification: ntf.read == true}" aria-label="Read">
                    <img ng-src="{{appurl + 'uploads/images/' + ntf.avatar}}" class="md-avatar" alt="NTF" on-error-src="<?php echo base_url('assets/img/placeholder.png') ?>" />
                    <div class="md-list-item-text" layout="column">
                        <h4 ng-bind="ntf.detail"></h4>
                        <p ng-bind="ntf.date"></p>
                    </div>
                </md-list-item>

                <div class="events" style="margin-top: 0;margin-bottom: 0;">
                    <md-list-item ng-repeat="reminder in notifications.reminders" class="next reminder">
                        <md-tooltip md-direction="bottom"><?php echo lang2('reminder_created_by') ?> {{reminder.creator}}</md-tooltip>
                        <div class="row" style="width: 100%;margin-top: 10px;">
                            <div style="width: 20%;float: left;margin-left: 1em;">
                                <label class="detail"><i class="ion-ios-bell"></i></label>
                            </div>
                            <div class="col-10">
                                <h3 class="text-bold" style="margin-bottom: 5px"><b>{{reminder.title}}</b></h3>
                                <span class="reminder-detail" style="display: table-cell;" ng-bind="reminder.description"></span>
                                <p style="display: table-footer-group;"><span style="color:black;" class='duration text-bold'><span ng-bind="reminder.date | date : 'MMM d, y h:mm:ss a'"></span></span></p>
                            </div>
                        </div>
                    </md-list-item>

                    <md-list-item ng-repeat="row in notifications.leadsAtrasados" class="next reminder">
                        <md-tooltip md-direction="bottom">Lead atrasado!</md-tooltip>
                        <div class="row" style="width: 100%;margin-top: 10px;">
                            <div style="width: 20%;float: left;margin-left: 1em;">
                                <label class="detail"><i class="fas fa-user"></i></i></label>
                            </div>
                            <div class="col-10">
                                <h3 class="text-bold" style="margin-bottom: 5px"><b>Lead com atraso</b></h3>
                                <span class="reminder-detail" style="display: table-cell;">
                                    <a href="{{appurl + 'leads/lead/' + row.id_lead}}" style="font-weight: 100;color: #001fff;">
                                        <b>{{row.nm_lead}}</b>
                                        <i class="fas fa-share" style="    color: #001fff;font-size: 15px;"></i>
                                    </a>
                                </span>

                                <p style="display: table-footer-group;"><span style="color:black;" class='duration text-bold'><span ng-bind="reminder.date | date : 'MMM d, y h:mm:ss a'"></span></span></p>
                            </div>
                        </div>
                    </md-list-item>
                </div>

                <p ng-show="!notifications.notifications.length && !notifications.reminders.length && !notifications.leadsAtrasados.length" class="not-found"><?php echo lang2('not_found') ?></p>
            </md-list>



        </md-content>

    </md-sidenav>

    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Profile" ng-cloak style="width: 450px;">

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

                    <!--
                    <?php if (!session()->get('other')) { ?>
                        <md-content layout-padding>
                            <md-switch ng-model="appointment_availability" ng-change="ChangeAppointmentAvailability(user.id,appointment_availability)" aria-label="Status"><strong class="text-muted" ng-bind='lang.appointment_availability'></strong></md-switch>
                        </md-content>
                    <?php } ?>

                    <md-tabs>

                        <?php if (!session()->get('other')) { ?>

                            <md-tab label="<?php echo lang2('onsite_visits') ?> ">

                                <md-list class="md-dense" flex>

                                    <md-list-item class="md-3-line" ng-repeat="meet in meetings">

                                        <div class="md-list-item-text" layout="column">

                                            <h3 ng-bind="meet.title"></h3>

                                            <h4 ng-bind="meet.customer"></h4>

                                            <p>

                                                <span ng-bind="meet.date | date : 'MMM d, y h:mm:ss a'"></span><br>

                                                <span ng-bind="meet.staff"></span>

                                            </p>

                                        </div>

                                        <md-divider></md-divider>

                                    </md-list-item>

                                    <p ng-show="!meetings.length" class="not-found"><?php echo lang2('not_found') ?></p>

                                </md-list>

                            </md-tab>

                        <?php } ?>

                        <?php if (!session()->get('other')) { ?>

                            <md-tab label="<?php echo lang2('appointments'); ?>">
                                <md-content>
                                    <md-list class="md-dense" flex>
                                        <md-list-item class="md-3-line" ng-repeat="appointment in dashboard_appointments">
                                            <div class="md-avatar a64"><span ng-bind="appointment.day"></span><br>
                                                <span class="a65" ng-bind="appointment.aday"></span>
                                            </div>
                                            <div class="md-list-item-text" layout="column">
                                                <h3 ng-bind="appointment.title"></h3>
                                                <p><span ng-bind="appointment.start_iso_date | date : 'MMM d, y h:mm:ss a'"></span><br>
                                                    <span ng-bind="appointment.staff"></span>
                                                </p>
                                            </div>
                                        </md-list-item>
                                        <p ng-show="!dashboard_appointments.length" class="not-found"><?php echo lang2('not_found') ?></p>
                                    </md-list>
                                </md-content>
                            </md-tab>
                        <?php } ?>
                    </md-tabs>
 -->
                </md-tab>
            </md-tabs>
        </md-content>
    </md-sidenav>
    <md-content class="ciuis-body-wrapper ciuis-body-fixed-sidebar" ciuis-ready>
*/