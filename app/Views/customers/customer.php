<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>

<?php
$privileges_Model = new App\Models\Privileges_Model();

$appconfig = get_appconfig();

?>
<div class="ciuis-body-content" ng-controller="Customer_Controller">
    <div class="main-content container-fluid col-xs-12 col-md-9 col-lg-9">
        <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">

                <h2 class="md-pl-10" flex md-truncate ng-show="customer.type =='0'" ng-bind="customer.company"></h2>
                <h2 class="md-pl-10" flex md-truncate ng-show="customer.type =='1'" ng-bind="customer.namesurname"></h2>

                <md-button ng-click="showReuniao()" style="padding: 0 10px; min-width: 40px; width: 40px; min-height: 40px;height: 40px;">
                    <md-tooltip md-direction="bottom">Agendar reunião</md-tooltip>
                    <md-icon md-menu-align-target style="margin: auto 3px auto 0;">
                        <img style="width: 28px;text-align: center; margin-left: -6px; margin-top: -7px;" src='<?= base_url('assets/img/icons/add_reuniao.png') ?>'>
                    </md-icon>
                </md-button>

                <?php if (check_privilege('customers', 'edit')) { ?>

                    <md-button ng-click="Update()" class="md-icon-button md-primary" aria-label="Actions" ng-cloak>

                        <md-icon class="mdi mdi-edit"></md-icon>

                    </md-button>

                <?php }
                if (check_privilege('customers', 'delete')) { ?>

                    <md-button ng-click="Delete()" class="md-icon-button md-primary" aria-label="Actions" ng-cloak>

                        <md-icon class="ion-trash-b"></md-icon>

                    </md-button>

                <?php } ?>

            </div>

        </md-toolbar>

        <div ng-show="customersLoader" layout-align="center center" class="text-center" id="circular_loader">

            <md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>

            <p style="font-size: 15px;margin-bottom: 5%;">

                <span>

                    <?php echo lang2('please_wait') ?> <br>

                    <small><strong><?php echo lang2('loading') . ' ' . lang2('customer') . '...' ?></strong></small>

                </span>

            </p>

        </div>

        <section ng-show="!customersLoader" layout="row" flex ng-cloak>

            <md-content class="bg-white" flex style="margin-bottom: 30px;">
                <md-tabs md-dynamic-height md-border-bottom>
                    <md-tab label="<?php echo lang2('summary'); ?>">
                        <md-content class="md-padding bg-white layInfosCustom">
                            <div style="float: left; width: 100%;">
                                <div class="col-md-3 col-xs-6 xs-pt-20 lg-pt-0">
                                    <div class='customer-42525' style="width: 100%;text-align: center;">
                                        <div class='customer-42525__inner'>
                                            <h2>Cliente desde</h2>
                                            <h4 style="font-weight: 500;font-size: 15px;color: #04109b;" ng-bind="customer.created | date:'dd/MM/yyyy' "></h4>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-3 col-xs-6 xs-pt-20 lg-pt-0">
                                    <div class='customer-42525' style="text-align: center;">
                                        <div class='customer-42525__inner'>
                                            <h2><?php echo lang2('grossrevenue'); ?></h2>
                                            <small><?php echo lang2('grossrevenuedetail'); ?></small>
                                            <div class='stat'> <span ng-show="customer.grossrevenue" ng-bind-html="customer.grossrevenue | currencyFormat:cur_code:null:true:cur_lct"></span>
                                                <span ng-show="!customer.grossrevenue"><?php echo lang2('nosalesyet') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <div style="border-right: 1px solid rgb(234, 234, 234);" class="col-md-3 col-xs-6 xs-pt-20 lg-pt-0">
                                    <div class='customer-42525' style="text-align: center;">
                                        <div class='customer-42525__inner'>
                                            <h2><?php echo lang2('netrevenue'); ?></h2>
                                            <small><?php echo lang2('netrevenuedetail'); ?></small>
                                            <div class='stat'>
                                                <span ng-show="customer.netrevenue" ng-bind-html="customer.netrevenue | currencyFormat:cur_code:null:true:cur_lct"></span>
                                                <span class="text-success" style="font-size: 15px;" ng-show="!customer.netrevenue"><?php echo lang2('nosalesyet') ?></span>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div style="border-right: 1px solid rgb(234, 234, 234);" class="col-md-3 col-xs-6 xs-pt-20 lg-pt-0">
                                    <div class='customer-42525' style="text-align: center;">
                                        <div class='customer-42525__inner'>
                                            <h2><?php echo lang2('riskstatus'); ?></h2>
                                            <small><?php echo lang2('customerrisksubtext'); ?></small>

                                            <div ng-hide="customer.risk != '0'" class="stat">
                                                <span style="color:#eaeaea;">
                                                    <i class="text-success mdi mdi-shield-check"></i>
                                                    <?php echo lang2('norisk') ?></span>
                                            </div>

                                            <div ng-show="customer.risk > '50'" class="stat">
                                                <span ng-bind="customer.risk+'%'"></span>
                                            </div>

                                            <div ng-show="customer.risk > '50'" class="progress">
                                                <div style="width:{{customer.risk}}%" class="progress-bar progress-bar-danger"></div>
                                            </div>

                                            <div ng-show="customer.risk > '0' && customer.risk < 50" class="stat">
                                                <span ng-bind="customer.risk+'%'"></span>
                                            </div>

                                            <div ng-show="customer.risk > '0' && customer.risk < 50" class="progress">
                                                <div style="width:{{customer.risk}}%" class="progress-bar progress-bar-primary">
                                                </div>
                                            </div>
                                            <p><?php echo lang2('customerrisksubtext'); ?></p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <md-content class="bg-white ciuis-chart" style="align-self: flex-end;float: left; width: 100%;">

                                <div class="card">
                                    <div class="col-md-4 col-xs-4 xs-pt-20 lg-pt-0">
                                        <md-select placeholder="Ano" ng-model="filtroAno" name="ano" style="min-width: 200px;" ng-change="getDadosResume()">
                                            <md-option ng-value="ano" ng-repeat="ano in AnosAnteriores">{{ano}}</md-option>
                                        </md-select>
                                    </div>

                                    <div class="my-2" style="padding-bottom: 60px;">
                                        <div class="chart-wrapper" style="height:300px">
                                            <canvas height="300" style="padding-top: 25px;" id="customer_annual_sales_chart"></canvas>
                                        </div>
                                    </div>

                                </div>

                            </md-content>

                        </md-content>

                    </md-tab>
                    <md-tab label="<?php echo lang2('informations'); ?>">

                        <md-content class="infosLeads2">

                            <div layout="row" flex>
                                <div flex="50">
                                    <div class="divInfoL2">
                                        <md-icon class="mdi mdi-local-store"></md-icon>
                                        <div class="flt_left">
                                            <p class="titleInfo"><?php echo lang2('company') ?></p>
                                            <p class="nmInfo"><span ng-bind="customer.company"></span></p>
                                        </div>
                                    </div>

                                    <div class="divInfoL2" ng-if="customer.tp_pessoa == 1 && customer.cpf != null && customer.cpf != ''">
                                        <md-icon class="mdi"><i class="fas fa-archive"></i></md-icon>
                                        <div class="flt_left">
                                            <p class="titleInfo">Cpf</p>
                                            <p class="nmInfo"><span ng-bind="customer.cpf"></span></p>
                                        </div>
                                    </div>

                                    <div class="divInfoL2" ng-if="customer.tp_pessoa == 2 && customer.cnpj != null && customer.cnpj != ''">
                                        <md-icon class="mdi"><i class="fas fa-archive"></i></md-icon>
                                        <div class="flt_left">
                                            <p class="titleInfo">Cnpj</p>
                                            <p class="nmInfo"><span ng-bind="customer.cnpj"></span></p>
                                        </div>
                                    </div>

                                    <div class="divInfoL2" ng-if="customer.phone != null && customer.phone != ''">
                                        <md-icon class="mdi mdi-local-phone"></md-icon>
                                        <div class="flt_left">
                                            <p class="titleInfo"><?php echo lang2('phone') ?></p>
                                            <p class="nmInfo"><span ng-bind="customer.phone"></span></p>
                                        </div>
                                    </div>

                                    <div class="divInfoL2" ng-if="customer.address != null && customer.address != ''">
                                        <md-icon class="mdi mdi-pin-drop"></md-icon>
                                        <div class="flt_left">
                                            <p class="titleInfo"><?php echo lang2('address') ?></p>
                                            <p class="nmInfo"><span ng-bind="customer.address"></span></p>
                                        </div>
                                    </div>

                                    <div class="divInfoL2" ng-if="customer.city != null && customer.city != ''">
                                        <md-icon class="mdi mdi-city"></md-icon>
                                        <div class="flt_left">
                                            <p class="titleInfo"><?php echo lang2('city') ?></p>
                                            <p class="nmInfo"><span ng-bind="customer.city"></span></p>
                                        </div>
                                    </div>

                                    <div class="divInfoL2" ng-if="customer.state != null && customer.state != ''">
                                        <md-icon><i class="fas fa-flag-usa"></i></md-icon>
                                        <div class="flt_left">
                                            <p class="titleInfo">Estado</p>
                                            <p class="nmInfo"><span ng-bind="customer.state"></span></p>
                                        </div>
                                    </div>

                                    <div class="divInfoL2" ng-if="customer.country != null && customer.country != ''">
                                        <md-icon class="ion-earth"></md-icon>
                                        <div class="flt_left">
                                            <p class="titleInfo"><?php echo lang2('country') ?></p>
                                            <p class="nmInfo"><span ng-bind="customer.country"></span></p>
                                        </div>
                                    </div>

                                    <div class="divInfoL2" ng-if="customer.tp_pessoa == 2 && customer.web != null && customer.web != ''">
                                        <md-icon class="mdi mdi-nature-people"></md-icon>
                                        <div class="flt_left">
                                            <p class="titleInfo">Website</p>
                                            <p class="nmInfo"><span ng-bind="customer.web"></span></p>
                                        </div>
                                    </div>

                                </div>
                                <div flex="50">
                                    <div class="divInfoL2" ng-if="customer.tp_pessoa == 1 && customer.dt_nascimento != null && customer.dt_nascimento != ''">
                                        <md-icon class="mdi"><i class="fas fa-calendar-week"></i></md-icon>
                                        <div class="flt_left">
                                            <p class="titleInfo">Data de nascimento</p>
                                            <p class="nmInfo"><span ng-bind="customer.dt_nascimento | date:'dd/MM/yyyy'"></span></p>
                                        </div>
                                    </div>

                                    <div class="divInfoL2" ng-if="customer.tp_pessoa == 2 && customer.setor_atividade != null && customer.setor_atividade != ''">
                                        <md-icon class="mdi mdi-markunread-mailbox"></md-icon>
                                        <div class="flt_left">
                                            <p class="titleInfo">Setor de atividade</p>
                                            <p class="nmInfo"><span ng-bind="customer.setor_atividade"></span></p>
                                        </div>
                                    </div>

                                    <div class="divInfoL2" ng-if="customer.email != null && customer.email != ''">
                                        <md-icon class="ion-android-mail"></md-icon>
                                        <div class="flt_left">
                                            <p class="titleInfo"><?php echo lang2('email') ?></p>
                                            <p class="nmInfo"><span ng-bind="customer.email"></span></p>
                                        </div>
                                    </div>

                                    <div class="divInfoL2" ng-if="customer.tp_pessoa == 2 && customer.porte != null && customer.porte != ''">
                                        <md-icon class="mdi"> <i class="fas fa-search-dollar"></i></md-icon>
                                        <div class="flt_left">
                                            <p class="titleInfo">Porte</p>
                                            <p class="nmInfo"><span ng-bind="customer.porte"></span></p>
                                        </div>
                                    </div>


                                    <div class="divInfoL2" ng-if="customer.source != null && customer.source != ''">
                                        <md-icon class="mdi mdi-book-image"></md-icon>
                                        <div class="flt_left">
                                            <p class="titleInfo"><?php echo lang2('source') ?></p>
                                            <p class="nmInfo"><span ng-bind="customer.source"></span></p>
                                        </div>
                                    </div>

                                    <div class="divInfoL2" ng-if="customer.created != null && customer.created != ''">
                                        <md-icon class="ion-android-calendar"></md-icon>
                                        <div class="flt_left">
                                            <p class="titleInfo"><?php echo lang2('created') ?></p>
                                            <p class="nmInfo"><span ng-bind="customer.created | date:'dd/MM/yyyy'"></span></p>
                                        </div>
                                    </div>


                                    <div class="divInfoL2" ng-if="customer.funil_origem != null && customer.funil_origem != ''">
                                        <md-icon><i class="fas fa-filter"></i></md-icon>
                                        <div class="flt_left">
                                            <p class="titleInfo">Funil origem</p>
                                            <p class="nmInfo"><span ng-bind="customer.funil_origem"></span></p>
                                        </div>
                                    </div>

                                    <div class="divInfoL2" ng-if="customer.tp_pessoa == 1 && customer.instagram != null && customer.instagram != ''">
                                        <md-icon><i class="fab fa-instagram"></i></md-icon>
                                        <div class="flt_left">
                                            <p class="titleInfo">Instagram</p>
                                            <p class="nmInfo"><span ng-bind="customer.instagram"></span></p>
                                        </div>
                                    </div>

                                    <div class="divInfoL2" ng-if="customer.tp_pessoa == 1 && customer.facebook != null && customer.facebook != ''">
                                        <md-icon><i class="fab fa-facebook"></i></md-icon>
                                        <div class="flt_left">
                                            <p class="titleInfo">Facebook</p>
                                            <p class="nmInfo"><span ng-bind="customer.facebook"></span></p>
                                        </div>
                                    </div>

                                    <div class="divInfoL2" ng-if="customer.tp_pessoa == 1 && customer.linkedin != null && customer.linkedin != ''">
                                        <md-icon><i class="fab fa-linkedin-in"></i></md-icon>
                                        <div class="flt_left">
                                            <p class="titleInfo">Linkedin</p>
                                            <p class="nmInfo"><span ng-bind="customer.linkedin"></span></p>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </md-content>

                        <md-divider></md-divider>
                    </md-tab>


                    <md-tab label="Timeline">
                        <md-content class="md-padding bg-white">
                            <section class="ciuis-notes show-notes">

                                <div style="overflow-y: hidden;width: 100%;overflow-x: auto;display: inline-flex;padding-bottom: 5px;">
                                    <md-list ng-click="alterarStatusCustomer(etapa.id)" class="ciuis_lead_status_card" style="width: 175px;position: relative;padding-bottom: 0px;max-width: 175px;" flex ng-repeat="etapa in funilsCustomers[customer.funil].etapas">
                                        <div class="div-title-funil" style="min-height: 45px;width: 150px;">

                                            <md-toolbar class="toolbar-white md-toolbar-funil">
                                                <div class="md-toolbar-tools toolbarPerson" style="padding-bottom: 5px;
    padding-top: 5px;
    border-radius: 5px;
    height: 50px !important;
    cursor:pointer;
    border-left: 3px solid #28B8DA;" ng-class="{'statusSelect':etapa.id == customer.group_id}">
                                                    <h3 flex md-truncate>{{etapa.name}}</h3>
                                                </div>
                                            </md-toolbar>
                                        </div>
                                    </md-list>
                                </div>


                                <button type="button" class="btn btn-success btn_add_custom" style="float: right;margin-top: 1em;" ng-click="showDialogAddAtividade()">
                                    <span style="vertical-align: middle;">Adicionar</span>
                                    <md-icon><i style="color:white" class="ion-android-add-circle"></i></md-icon>
                                </button>

                                <a href="{{customer.instagram}}" class="btn btn-success btn_icon" target="_blank" ng-if="customer.instagram.length > 0">
                                    <img src="{{base_url + 'assets/img/icons/instagram.png'}}">
                                </a>
                                <a href="javaScript:void(0)" class="btn btn-success btn_icon" ng-click="showDialogContatoSocial('email')">
                                    <img src="{{base_url + 'assets/img/icons/email.png'}}">
                                </a>
                                <a href="javaScript:void(0)" class="btn btn-success btn_icon" ng-click="showDialogContatoSocial('linkedin')">
                                    <img src="{{base_url + 'assets/img/icons/linkedin.png'}}">
                                </a>
                                <a href="javaScript:void(0)" ng-click="showDialogContatoSocial('whatsapp')" class="btn btn-success btn_icon">
                                    <img src="{{base_url + 'assets/img/icons/whatsapp.png'}}">
                                </a>
                                <a href="javaScript:void(0)" ng-click="showDialogContatoSocial('instagram')" class="btn btn-success btn_icon">
                                    <img src="{{base_url + 'assets/img/icons/instagram.png'}}">
                                </a>
                            </section>

                            <div ng-repeat="atividade in customer.list_atividades" class="atv_cont">
                                <img src="{{base_url + atividade.atv_ft}}" class="atv_img">
                                <div class="atv_div_50">
                                    <div class="atv_title">
                                        <h4 style="margin-left: 0.5em;  width: calc(100% - 5em);">{{atividade.nm_atividade_select}} </h4>
                                        <md-button aria-label="Open demo menu" class="md-icon-button btn_atv" ng-click="showDialogAddAtividade(atividade)">
                                            <md-icon aria-label="Add Source"><i class="ion-android-more-vertical text-muted"></i></md-icon>
                                        </md-button>
                                    </div>
                                    <div class="atv_anotacoes">
                                        <p style="white-space: pre-line;" ng-bind-html="atividade.anotacoes"></p>
                                    </div>

                                    <div>
                                        <small>by <b>{{atividade.staffname}}</b> at {{converteData(atividade.dt_entrada)}}</small>
                                        <small ng-if="atividade.retorno != '0000-00-00'" class="atv_data">Agendado para: {{converteData(atividade.retorno)}}</small>
                                        <small ng-if="atividade.retorno == '0000-00-00'" class="atv_data">Sem retorno agendado</small>

                                    </div>
                                </div>

                            </div>

                            <section class="md-pb-30">
                            </section>
                        </md-content>
                    </md-tab>

                    <?php if ($privileges_Model->has_privilege('invoices')) { ?>
                        <md-tab label="<?php echo lang2('invoices'); ?>">
                            <md-content class="bg-white">
                                <md-list flex class="md-p-0 sm-p-0 lg-p-0">
                                    <md-list-item ng-repeat="invoice in invoices" ng-click="GoInvoice($index)" aria-label="Invoice">
                                        <md-icon class="ico-ciuis-invoices"></md-icon>
                                        <p><strong ng-bind="invoice.longid"></strong> - Venc: <small ng-bind="invoice.duedate"></small> - <small ng-bind="invoice.status"></small></p>
                                        <h4><strong ng-bind-html="invoice.total | currencyFormat:cur_code:null:true:cur_lct"></strong>
                                        </h4>
                                        <md-divider></md-divider>
                                    </md-list-item>
                                </md-list>

                                <md-content ng-show="!invoices.length" class="md-padding bg-white no-item-data">
                                    <?php echo lang2('notdata') ?></md-content>
                            </md-content>
                        </md-tab>
                    <?php } ?>

                    <md-tab label="Emails automatizados">
                        <md-content layout-padding>
                            <div ng-show="customersLoader" layout-align="center center" class="text-center" id="circular_loader" ng-cloak>
                                <md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
                                <p style="font-size: 15px;margin-bottom: 5%;">
                                    <span><?php echo lang2('please_wait') ?> <br>
                                        <small><strong><?php echo lang2('loading') . ' ' . lang2('customer') . '...' ?></strong></small>
                                    </span>
                                </p>
                            </div>

                            <md-content ng-show="!customersLoader" class="bg-white" ng-cloak>
                                <md-table-container ng-show="emailsAut.length > 0">
                                    <table md-table md-progress="promise" style="width: 100%;border-top: 0;">
                                        <thead md-head>
                                            <tr md-row>
                                                <th md-column><span><?php echo lang2('name'); ?></span></th>
                                                <th md-column><span>Etapa atual</span></th>
                                                <th md-column><span>Ultima verificação</span></th>
                                                <th md-column><span>Ativo</span></th>
                                            </tr>
                                        </thead>
                                        <tbody md-body>
                                            <tr class="select_row" md-row ng-repeat="taskAut in emailsAut " class="cursor">
                                                <td md-cell>
                                                    <strong><span ng-bind="taskAut.name_taskAut"></span></strong>
                                                </td>
                                                <td md-cell>
                                                    <span class="blur" ng-bind="taskAut.etapaAtual"></span>
                                                </td>
                                                <td md-cell>
                                                    <span class="blur" ng-bind="taskAut.ultima_verificacao"></span>
                                                </td>
                                                <td md-cell>
                                                    <md-switch ng-model="adicionadoTask[taskAut.id_task]" ng-change="UpdateAdicionadoTask()" aria-label="Status" ng-cloak><strong class="text-muted">Ativo</strong></md-switch>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </md-table-container>
                                <md-content ng-show="!emailsAut.length && !emailsAutLoader" class="md-padding no-item-data">
                                    <?php echo lang2('notdata') ?></md-content>
                            </md-content>
                        </md-content>
                    </md-tab>

                    <!--
                    <?php if ($privileges_Model->has_privilege('proposals')) { ?>
                        <md-tab label="<?php echo lang2('proposals'); ?>">
                            <md-content class="bg-white">

                                <md-list flex class="md-p-0 sm-p-0 lg-p-0">

                                    <md-list-item ng-repeat="proposal in proposals" ng-click="GoProposal($index)" aria-label="Proposal">

                                        <md-icon class="ico-ciuis-proposals"></md-icon>

                                        <p><strong ng-bind="proposal.longid"></strong></p>

                                        <h4><strong ng-bind-html="proposal.total | currencyFormat:cur_code:null:true:cur_lct"></strong>

                                        </h4>

                                        <md-divider></md-divider>

                                    </md-list-item>

                                </md-list>

                                <md-content ng-show="!proposals.length" class="md-padding bg-white no-item-data">

                                    <?php echo lang2('notdata') ?></md-content>

                            </md-content>

                        </md-tab>
                    <?php } ?>

                    <?php if ($privileges_Model->has_privilege('projects')) { ?>
                        <md-tab label="<?php echo lang2('projects'); ?>">

                            <md-content class="bg-white">

                                <md-list flex class="md-p-0 sm-p-0 lg-p-0">

                                    <md-list-item ng-repeat="project in projects" ng-click="GoProject($index)" aria-label="Project">

                                        <md-icon class="ico-ciuis-projects"></md-icon>

                                        <p><strong ng-bind="project.name"></strong></p>

                                        <h4><strong ng-bind="project.status"></strong></h4>

                                        <md-divider></md-divider>

                                    </md-list-item>

                                </md-list>

                                <md-content ng-show="!projects.length" class="md-padding bg-white no-item-data">

                                    <?php echo lang2('notdata') ?></md-content>

                            </md-content>

                        </md-tab>
                    <?php } ?>
-->

                    <?php if ($privileges_Model->has_privilege('tickets')) { ?>
                        <md-tab label="<?php echo lang2('tickets'); ?>">

                            <md-content class="bg-white">

                                <md-list flex class="md-p-0 sm-p-0 lg-p-0">

                                    <md-list-item ng-repeat="ticket in tickets" ng-click="GoTicket($index)" aria-label="Ticket">

                                        <md-icon class="ico-ciuis-supports"></md-icon>

                                        <p><strong ng-bind="ticket.subject"></strong></p>

                                        <p><strong ng-bind="ticket.contactname"></strong></p>

                                        <h4><strong ng-bind="ticket.priority"></strong></h4>

                                        <md-divider></md-divider>

                                    </md-list-item>

                                </md-list>

                                <md-content ng-show="!tickets.length" class="md-padding bg-white no-item-data">

                                    <?php echo lang2('notdata') ?></md-content>

                            </md-content>

                        </md-tab>
                    <?php } ?>
                    <md-tab label="<?php echo lang2('notes'); ?>">

                        <md-content class="md-padding bg-white">

                            <section class="ciuis-notes show-notes">

                                <article ng-repeat="note in notes" class="ciuis-note-detail">

                                    <div class="ciuis-note-detail-img"> <img src="<?php echo base_url('assets/img/note.png') ?>" alt="" width="50" height="50" /> </div>

                                    <div class="ciuis-note-detail-body">

                                        <div class="text">

                                            <p> <span ng-bind="note.description"></span> <a ng-click='DeleteNote($index)' style="cursor: pointer;" class="mdi ion-trash-b pull-right delete-note-button"></a> </p>

                                        </div>

                                        <p class="attribution"> by <strong><a href="<?php echo base_url('staff/staffmember/'); ?>/{{note.staffid}}" ng-bind="note.staff"></a></strong> at <span ng-bind="note.date"></span> </p>

                                    </div>

                                </article>

                            </section>

                            <section class="md-pb-30">

                                <md-input-container class="md-block">

                                    <label><?php echo lang2('description') ?></label>

                                    <textarea required name="description" ng-model="note" placeholder="<?php echo lang2('typeSomething'); ?>" class="form-control note-description"></textarea>

                                </md-input-container>

                                <div class="form-group pull-right">

                                    <button ng-click="AddNote()" type="button" class="btn btn-warning btn-xl ion-ios-paperplane" type="submit">

                                        <?php echo lang2('addnote') ?>

                                    </button>

                                </div>

                            </section>

                        </md-content>

                    </md-tab>

                    <md-tab label="<?php echo lang2('reminders'); ?>">

                        <md-list ng-cloak>

                            <md-toolbar class="toolbar-white">

                                <div class="md-toolbar-tools">

                                    <h2><?php echo lang2('reminders') ?></h2>

                                    <span flex></span>

                                    <md-button ng-click="ReminderForm()" class="md-icon-button test-tooltip" aria-label="Add Reminder">

                                        <md-tooltip md-direction="left"><?php echo lang2('addreminder') ?></md-tooltip>

                                        <md-icon><i class="ion-plus-round text-success"></i></md-icon>

                                    </md-button>

                                </div>

                            </md-toolbar>

                            <md-list-item ng-repeat="reminder in in_reminders" ng-click="goToPerson(person.name, $event)" class="noright"> <img alt="{{ reminder.staff }}" ng-src="{{ reminder.avatar }}" class="md-avatar" />

                                <p>{{ reminder.description }}</p>

                                <md-icon ng-click="" aria-label="Send Email" class="md-secondary md-hue-3">

                                    <md-tooltip md-direction="left">{{reminder.date}}</md-tooltip>

                                    <i class="ion-ios-calendar-outline"></i>

                                </md-icon>

                                <md-icon ng-click="DeleteReminder($index)" aria-label="Send Email" class="md-secondary md-hue-3">

                                    <md-tooltip md-direction="left"><?php echo lang2('delete') ?></md-tooltip>

                                    <i class="ion-ios-trash-outline"></i>

                                </md-icon>

                            </md-list-item>

                        </md-list>

                    </md-tab>
                    <!--
                    <md-tab label="<?php echo lang2('customeractivities'); ?>">
                        <md-content class="md-padding bg-white">
                            <ul class="user-timeline">
                                <li ng-repeat="log in logs | filter: { customer_id: '<?php echo $customers['id']; ?>' }">
                                    <div class="user-timeline-title" ng-bind="log.date"></div>
                                    <div class="user-timeline-description" ng-bind-html="log.detail|trustAsHtml"></div>
                                </li>
                            </ul>
                        </md-content>
                    </md-tab>
                    -->

                </md-tabs>

            </md-content>

        </section>

        <!--
        <md-content ng-show="!customersLoader" ng-show="custom_fields.length" class="time-log-project bg-white" ng-cloak>

            <md-subheader ng-if="custom_fields"><?php echo lang2('custom_fields') ?></md-subheader>

            <md-list-item ng-if="custom_fields" ng-repeat="field in custom_fields">

                <md-icon class="{{field.icon}} material-icons"></md-icon>

                <strong flex md-truncate>{{field.name}}</strong>

                <p ng-if="field.type === 'input'" class="text-right" flex md-truncate ng-bind="field.data"></p>

                <p ng-if="field.type === 'textarea'" class="text-right" flex md-truncate ng-bind="field.data"></p>

                <p ng-if="field.type === 'date'" class="text-right" flex md-truncate ng-bind="field.data | date:'dd, MMMM yyyy EEEE'"></p>

                <p ng-if="field.type === 'select'" class="text-right" flex md-truncate ng-bind="custom_fields[$index].selected_opt.name"></p>

                <md-divider ng-if="custom_fields"></md-divider>

            </md-list-item>

        </md-content>

        -->

        <md-content ng-show="customer.subsidiaries.length" class="time-log-project bg-white" ng-cloak>

            <md-divider></md-divider>

            <md-subheader class="md-primary toolbar-white">

                <md-truncate><?php echo lang2('subsidiary_companies') ?></md-truncate>

            </md-subheader>

        </md-content>

    </div>

    <div class="main-content container-fluid col-12 col-md-3 md-pl-0">

        <md-toolbar class="toolbar-white">

            <div class="md-toolbar-tools">

                <h2 flex md-truncate class="pull-left"><strong><?php echo lang2('customercontacts') ?></strong></h2>

                <?php if (check_privilege('customers', 'edit')) { ?>

                    <md-button ng-click="NewContact();get_contact_permissions()" class="md-icon-button md-primary" aria-label="Add contact" ng-cloak>

                        <md-icon class="ion-person-add"></md-icon>

                    </md-button>

                <?php } ?>

            </div>

        </md-toolbar>

        <md-content ng-show="!customersLoader" class="bg-white">

            <md-list flex ng-cloak>

                <md-list-item class="md-2-line" ng-repeat="contact in contacts" aria-label="Contact Detail">

                    <div data-letter-avatar="{{contact.name.charAt(0)+contact.surname.charAt(0)}}" class="ticket-area-av-im2 md-avatar text-uppercase"></div>

                    <div class="md-list-item-text" ng-class="{'md-offset': phone.options.offset }">

                        <h3 ng-bind="contact.name+' '+contact.surname"></h3>

                        <p ng-bind="contact.email"></p>

                    </div>

                    <?php if (check_privilege('customers', 'edit')) { ?>

                        <md-button class="md-icon-button" ng-click="ContactDetail($index)" aria-label="View" style="margin:auto">

                            <md-icon><i class="ion-compose text-muted"></i></md-icon>

                        </md-button>

                    <?php }
                    if (check_privilege('customers', 'delete')) { ?>

                        <md-button class="md-icon-button" ng-click="RemoveContact(contact.id)" aria-label="Delete" style="margin:auto">

                            <md-icon><i class="ion-android-delete text-muted"></i></md-icon>

                        </md-button>

                    <?php } ?>

                    <md-divider></md-divider>

                </md-list-item>

            </md-list>

        </md-content>

        <md-content ng-if="!contacts.length" class="text-center bg-white"><img width="100%" src="<?php echo base_url('assets/img/add_contact.png') ?>" alt=""></md-content>

    </div>



    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="NewContact" ng-cloak style="width: 450px;">

        <md-tabs md-selected="tabIndex" md-dynamic-height="" md-border-bottom="">

            <md-tab label="<?php echo lang2('create_contact_title') ?>">

                <md-content class="md-padding bg-white">

                    <div class="col-md-12 nopadding">

                        <md-content layout-padding>

                            <md-input-container class="md-block">

                                <label><?php echo lang2('contactname') ?></label>

                                <input type="text" ng-model="newcontact.name" required>

                            </md-input-container>

                            <md-input-container class="md-block">

                                <label><?php echo lang2('contactsurname') ?></label>

                                <input type="text" ng-model="newcontact.surname" required>

                            </md-input-container>

                            <md-input-container class="md-block">

                                <label><?php echo lang2('contactemail') ?></label>

                                <input type="text" ng-model="newcontact.email" required>

                            </md-input-container>

                            <md-input-container class="md-block">

                                <label><?php echo lang2('contactposition') ?></label>

                                <input type="text" ng-model="newcontact.position">

                            </md-input-container>

                            <md-input-container class="md-block">
                                <label><?php echo lang2('contactphone') ?></label>
                                <input type="text" class="phone" ng-model="newcontact.phone">
                            </md-input-container>

                            <md-input-container class="md-block">
                                <label>Data nascimento</label>
                                <md-datepicker md-hide-icons="all" require name="duedate" ng-model="newcontact.dt_nascimento" md-open-on-focus></md-datepicker>
                            </md-input-container>

                            <md-input-container class="md-block">
                                <label><?php echo lang2('extension') ?></label>
                                <input type="text" ng-model="newcontact.extension">
                            </md-input-container>

                            <md-input-container class="md-block">

                                <label><?php echo lang2('contactmobile') ?></label>

                                <input type="text" ng-model="newcontact.mobile">

                            </md-input-container>

                            <md-input-container class="md-block">

                                <label><?php echo lang2('contactskype') ?></label>

                                <input type="text" ng-model="newcontact.skype">

                            </md-input-container>

                            <md-input-container class="md-block">
                                <label><?php echo lang2('contactlinkedin') ?></label>
                                <input type="text" ng-model="newcontact.linkedin">
                            </md-input-container>

                            <md-input-container class="md-block">
                                <label>Facebook (Opicional)</label>
                                <input type="text" ng-model="newcontact.facebook">
                            </md-input-container>

                            <md-input-container class="md-block">
                                <label>Instagram (Opicional)</label>
                                <input type="text" ng-model="newcontact.instagram">
                            </md-input-container>




                            <md-input-container class="md-block">
                                <label><?php echo lang2('address') ?></label>
                                <input type="text" ng-model="newcontact.address">
                            </md-input-container>
                            <!--
                            <md-input-container class="md-block password-input" ng-show="isPrimary">
                                <label><?php echo lang2('password') ?></label>
                                <input type="text" ng-model="passwordNew" rel="gp" data-size="9" id="nc" data-character-set="a-z,A-Z,0-9,#">
                                <md-icon ng-click="getNewPass()" class="ion-refresh" style="display:inline-block;"></md-icon>
                            </md-input-container>
                            -->
                            <md-input-container class="md-block pull-left">

                                <md-checkbox ng-model="newcontact.isPrimary"><?php echo lang2('primarycontact') ?></md-checkbox>

                            </md-input-container>

                            <section>

                                <md-button ng-click="AddContact()" ng-href="#" class="md-raised md-primary btn-report block-button" ng-disabled="saving == true">
                                    <span ng-hide="saving == true"><?php echo lang2('next'); ?></span>
                                    <md-progress-circular class="white" ng-show="saving == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>
                                </md-button>

                                <br /><br /><br /><br />

                            </section>

                        </md-content>

                    </div>

                </md-content>

            </md-tab>
            <!--
            <md-tab label="<?php echo lang2('privileges') ?>">

                <md-content class="md-padding bg-white">

                    <div layout="row" layout-wrap>

                        <div flex-gt-xs="100" flex-xs="100" ng-repeat="permission in permissions">

                            <md-switch ng-model="permission.value" aria-label="Status"><strong class="text-muted"> {{permission.name}}</strong></md-switch>

                        </div>

                    </div>

                    <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>

                        <br>

                        <md-button ng-click="AddContact()" class="template-button block-button" ng-disabled="saving == true">

                            <span ng-hide="saving == true"><?php echo lang2('create'); ?></span>

                            <md-progress-circular class="white" ng-show="saving == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

                        </md-button>

                        <br><br>

                    </section>

                </md-content>

            </md-tab>

                    -->

        </md-tabs>

    </md-sidenav>

    <div style="visibility: hidden">

        <div ng-repeat="contact in contacts" class="md-dialog-container" id="ContactModal-{{contact.id}}">

            <md-dialog aria-label="ContactModal">

                <form>

                    <md-toolbar class="toolbar-white">

                        <div class="md-toolbar-tools">

                            <h2>{{contact.name}} {{contact.surname}}</h2>

                            <span flex></span>

                            <md-button class="md-icon-button" ng-click="CloseModal()">

                                <md-icon class="ion-close-round" aria-label="Close dialog" style="color:black">

                                </md-icon>

                            </md-button>

                        </div>

                    </md-toolbar>

                    <md-dialog-content style="max-width:800px;max-height:810px; ">



                        <md-content class="md-padding bg-white">

                            <div class="col-md-12 nopadding">

                                <md-input-container flex-gt-sm class="col-md-4">

                                    <label><?php echo lang2('contactname'); ?></label>

                                    <input ng-model="contact.name">

                                </md-input-container>

                                <md-input-container flex-gt-sm class="col-md-4">

                                    <label><?php echo lang2('contactsurname'); ?></label>

                                    <input ng-model="contact.surname">

                                </md-input-container>

                                <md-input-container flex-gt-sm class="col-md-4">

                                    <label><?php echo lang2('contactposition'); ?></label>

                                    <input ng-model="contact.position">

                                </md-input-container>

                            </div>

                            <div class="col-md-12 nopadding">

                                <md-input-container class="col-md-4">

                                    <label><?php echo lang2('contactphone'); ?></label>

                                    <input ng-model="contact.phone">

                                </md-input-container>

                                <md-input-container class="col-md-4">
                                    <label>Data nascimento</label>
                                    <md-datepicker md-hide-icons="all" require name="duedate" ng-model="contact.dt_nascimento" md-open-on-focus></md-datepicker>
                                </md-input-container>


                                <md-input-container class="col-md-4">

                                    <label><?php echo lang2('extension'); ?></label>

                                    <input ng-model="contact.extension">

                                </md-input-container>



                            </div>

                            <div class="col-md-12 nopadding">

                                <md-input-container class="col-md-4">

                                    <label><?php echo lang2('contactemail'); ?></label>

                                    <input ng-model="contact.email">

                                </md-input-container>

                                <md-input-container class="col-md-4">

                                    <label><?php echo lang2('contactskype'); ?></label>

                                    <input ng-model="contact.skype">

                                </md-input-container>

                                <md-input-container class="col-md-4">

                                    <label><?php echo lang2('contactlinkedin'); ?></label>

                                    <input ng-model="contact.linkedin">

                                </md-input-container>

                                <md-input-container class="col-md-4">
                                    <label>Facebook (Opicional)</label>
                                    <input type="text" ng-model="contact.facebook">
                                </md-input-container>

                                <md-input-container class="col-md-4">
                                    <label>Instagram (Opicional)</label>
                                    <input type="text" ng-model="contact.instagram">
                                </md-input-container>

                                <md-input-container class="col-md-4">
                                    <label><?php echo lang2('contactmobile'); ?></label>
                                    <input ng-model="contact.mobile">
                                </md-input-container>
                            </div>

                            <div class="col-md-12 nopadding">

                                <md-input-container class="col-md-12">

                                    <label><?php echo lang2('contactaddress'); ?></label>

                                    <input ng-model="contact.address">

                                </md-input-container>

                            </div>

                        </md-content>

                    </md-dialog-content>

                    <md-dialog-actions layout="row">


                        <span flex></span>

                        <md-button ng-click='RemoveContact(contact.id)' ng-click="answer('not useful')">

                            <?php echo lang2('delete') ?>

                        </md-button>

                        <md-button ng-click="UpdateContact($index)" ng-click="answer('useful')" style="margin-right:20px;" class="template-button" ng-disabled="updatingContact == true">

                            <span ng-hide="updatingContact == true"><?php echo lang2('update'); ?></span>

                            <md-progress-circular class="white" ng-show="updatingContact == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

                        </md-button>

                    </md-dialog-actions>

                </form>

            </md-dialog>

        </div>

    </div>

    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="ReminderForm" ng-cloak style="width: 450px;">

        <md-toolbar class="md-theme-light" style="background:#262626">

            <div class="md-toolbar-tools">

                <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>

                <md-truncate><?php echo lang2('addreminder') ?></md-truncate>

            </div>

        </md-toolbar>

        <md-content layout-padding="">

            <md-content layout-padding>

                <md-input-container class="md-block">

                    <label><?php echo lang2('datetobenotified') ?></label>

                    <input mdc-datetime-picker="" date="true" time="true" type="text" id="datetime" placeholder="<?php echo lang2('chooseadate') ?>" show-todays-date="" minutes="true" min-date="date" show-icon="true" ng-model="reminder_date" class=" dtp-no-msclear dtp-input md-input">

                </md-input-container>

                <md-input-container class="md-block">

                    <label><?php echo lang2('setreminderto'); ?></label>

                    <md-select placeholder="<?php echo lang2('setreminderto'); ?>" ng-model="reminder_staff" name="country_id" style="min-width: 200px;">
                        <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
                    </md-select>

                </md-input-container>

                <br>

                <md-input-container class="md-block">

                    <label><?php echo lang2('description') ?></label>

                    <textarea required name="description" ng-model="reminder_description" placeholder="<?php echo lang2('typeSomething'); ?>" class="form-control note-description"></textarea>

                </md-input-container>

                <div class="form-group pull-right">

                    <button ng-click="AddReminder()" type="button" class="btn btn-warning btn-xl ion-ios-paperplane" type="submit">

                        <?php echo lang2('addreminder') ?>

                    </button>

                </div>

            </md-content>

        </md-content>

    </md-sidenav>

    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Update" style="width: 450px" ng-cloak>

        <md-toolbar class="toolbar-white">

            <div class="md-toolbar-tools">
                <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>
                <md-truncate flex><?php echo lang2('update') ?></md-truncate>

                <md-switch ng-change="UpdateStatusCustumer()" ng-model="customer.customer_status_id" aria-label="Active"><strong class="text-muted"><?php echo lang2('active') ?></strong></md-switch>
            </div>

        </md-toolbar>

        <md-content layout-padding>
            <md-input-container class="md-block ">
                <label>Tipo de pessoa</label>
                <md-select required placeholder="" ng-model="customer.tp_pessoa" style="min-width: 200px;">
                    <md-option ng-value="1">Pessoa física</md-option>
                    <md-option ng-value="2">Pessoa jurídica</md-option>
                </md-select>
            </md-input-container>

            <div ng-show="customer.tp_pessoa != null" class="addLeadDiv">

                <md-input-container class="md-block" ng-if="customer.tp_pessoa == 1">
                    <label><?php echo lang2('name'); ?></label>
                    <input required name="name" ng-model="customer.company">
                </md-input-container>

                <md-input-container class="md-block" ng-if="customer.tp_pessoa == 2">
                    <label><?php echo lang2('company'); ?></label>
                    <input required ng-model="customer.company">
                </md-input-container>

                <md-input-container class="md-block" ng-if="customer.tp_pessoa == 2">
                    <label>Cnpj</label>
                    <input name="cnpj" ng-model="customer.cnpj">
                </md-input-container>

                <md-input-container class="md-block" ng-if="customer.tp_pessoa == 2">
                    <label>Website</label>
                    <input name="web_site" ng-model="customer.web">
                </md-input-container>

                <md-input-container class="md-block" ng-if="customer.tp_pessoa == 1">
                    <label>Cpf</label>
                    <input name="cpf" ng-model="customer.cpf">
                </md-input-container>

                <md-input-container class="md-block" ng-if="customer.tp_pessoa == 2">
                    <label>Setor de atividade</label>
                    <input name="setor_atividade" ng-model="customer.setor_atividade">
                </md-input-container>

                <md-input-container class="md-block" ng-if="customer.tp_pessoa == 1">
                    <label>Data de nascimento</label>
                    <md-datepicker md-hide-icons="all" name="dt_nascimento" ng-model="customer.dt_nascimento"></md-datepicker>
                </md-input-container>

                <md-input-container class="md-block" ng-if="customer.tp_pessoa == 2">
                    <label>Porte</label>
                    <input name="porte" ng-model="customer.porte">
                </md-input-container>

                <md-content>
                    <md-input-container class="md-block" flex="70" style="float: left;width: 100%;">
                        <label>Telefone</label>
                        <input name="phone" class="phone" ng-model="customer.phone">
                    </md-input-container>

                    <md-input-container class="md-block" flex="25" style="float: left;margin-top: 0;margin-left: 1em;width: 100%;">
                        <md-switch ng-model="customer.is_whatsApp" aria-label="Recurring"> <label>WhatsApp</label></md-switch>
                    </md-input-container>
                </md-content>

                <md-input-container class="md-block">
                    <label><?php echo lang2('email'); ?></label>
                    <input type="email" ng-model="customer.email" minlength="10" maxlength="100" ng-pattern="/^.+@.+\..+$/">
                </md-input-container>

                <md-input-container class="md-block" ng-if="customer.tp_pessoa == 1">
                    <label>Instagram</label>
                    <input name="instagram" ng-model="customer.instagram">
                </md-input-container>

                <md-input-container class="md-block" ng-if="customer.tp_pessoa == 1">
                    <label>Facebook</label>
                    <input name="facebook" ng-model="customer.facebook">
                </md-input-container>

                <md-input-container class="md-block" ng-if="customer.tp_pessoa == 1">
                    <label>Linkedin</label>
                    <input name="linkedin" ng-model="customer.linkedin">
                </md-input-container>

                <md-input-container class="md-block">
                    <label><?php echo lang2('address') ?></label>
                    <textarea ng-model="customer.address" md-maxlength="500" rows="3" md-select-on-focus></textarea>
                </md-input-container>

                <md-input-container class="md-block">
                    <label><?php echo lang2('city'); ?></label>
                    <input ng-model="customer.city">
                </md-input-container>

                <md-input-container class="md-block">
                    <label><?php echo lang2('zip'); ?></label>
                    <input ng-model="customer.zipcode">
                </md-input-container>

                <md-input-container class="md-block">
                    <label><?php echo lang2('country'); ?></label>
                    <md-select placeholder="<?php echo lang2('country'); ?>" ng-model="customer.country_id" ng-change="getStates(customer.country_id)" name="country_id" style="min-width: 200px;">
                        <md-option ng-selected="$index == 31" ng-value="country.id" ng-repeat="country in countries">{{country.shortname}}</md-option>
                    </md-select>
                </md-input-container>

                <md-input-container class="md-block" style="margin-top: 3em;">
                    <label><?php echo lang2('state'); ?></label>
                    <md-select placeholder="<?php echo lang2('states'); ?>" ng-model="customer.state_id" name="state_id" style="min-width: 200px;">
                        <md-option ng-value="state.id" ng-repeat="state in states">{{state.state_name}}</md-option>
                    </md-select>
                </md-input-container>

                <md-input-container class="md-block" style="margin-top: 3em;">
                    <label><?php echo lang2('assigned'); ?></label>
                    <md-select placeholder="<?php echo lang2('choosestaff'); ?>" ng-model="customer.assigned_id" style="min-width: 200px;">
                        <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
                    </md-select>
                </md-input-container>

                <md-input-container class="md-block" style="margin-top: 1em;">
                    <label><?php echo lang2('Closer'); ?></label>
                    <md-select placeholder="<?php echo lang2('Closer'); ?>" ng-model="customer.closer" style="min-width: 200px;">
                        <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
                    </md-select>
                </md-input-container>

                <md-input-container class="md-block" style="margin-top: 1em;">
                    <label><?php echo lang2('Customer Sucess'); ?></label>
                    <md-select placeholder="<?php echo lang2('Customer Sucess'); ?>" ng-model="customer.customer_sucess" style="min-width: 200px;">
                        <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
                    </md-select>
                </md-input-container>

                <md-input-container class="md-block" style="margin-top: 3em;">
                    <label><?php echo lang2('source'); ?></label>
                    <md-select placeholder="<?php echo lang2('source'); ?>" ng-model="customer.source_id" style="min-width: 200px;">
                        <md-option ng-value="source.id" ng-repeat="source in leadssources">{{source.name}}</md-option>
                    </md-select>
                </md-input-container>

                <md-input-container class="md-block" style="margin-top: 3em;">
                    <label><?php echo lang2('description') ?></label>
                    <textarea ng-model="customer.description" md-maxlength="500" rows="3" md-select-on-focus></textarea>
                </md-input-container>

                <md-input-container class="md-block">
                    <label><?php echo lang2('customer') . ' ' . lang2('group'); ?></label>
                    <md-select placeholder="<?php echo lang2('customer') . ' ' . lang2('group'); ?>" ng-model="customer.group_id" style="min-width: 200px;" required>
                        <md-select-header>
                            <md-toolbar class="toolbar-white">
                                <div class="md-toolbar-tools">
                                    <h4 flex md-truncate><?php echo lang2('groups') ?></h4>
                                    <md-button class="md-icon-button" ng-click="NewGroup()" aria-label="Create New">
                                        <md-icon><i class="mdi mdi-plus text-muted"></i></md-icon>
                                    </md-button>
                                </div>
                            </md-toolbar>
                        </md-select-header>
                        <md-option ng-value="name.id" ng-repeat="name in group">{{name.name}}</md-option>
                    </md-select>
                    <br />
                </md-input-container>

                <md-input-container class="md-block " style="margin-top: 3em;">
                    <label>Funil</label>
                    <md-select placeholder="" ng-model="customer.funil" style="min-width: 200px;">
                        <md-option ng-value="$index" ng-repeat="list in funilsCustomers">{{list.nm_funil}}</md-option>
                    </md-select>
                </md-input-container>

                <md-input-container class="md-block" style="margin-top: 3em;">
                    <label>Status</label>
                    <md-select placeholder="<?php echo lang2('status'); ?>" ng-model="customer.etapa" style="min-width: 200px;">
                        <md-option ng-value="status.id" ng-repeat="status in funilsCustomers[customer.funil].etapas">{{status.name}}</md-option>
                    </md-select>
                </md-input-container>


                <md-input-container class="md-block">
                    <label><?php echo lang2('default_payment_method'); ?></label>
                    <md-select placeholder="<?php echo lang2('default_payment_method'); ?>" ng-model="customer.default_payment_method" name="default_payment_method" style="min-width: 200px;">
                        <?php
                        $gateways = get_active_payment_methods();
                        foreach ($gateways as $gateway) { ?>
                            <md-option ng-value='"<?php echo $gateway['relation'] ?>"'><?php echo lang($gateway['relation']) ? lang($gateway['relation']) : $gateway['name'] ?></md-option>
                        <?php } ?>
                    </md-select>
                </md-input-container>
                <br>
                <md-input-container class="md-block">
                    <label><?php echo lang2('date_contacted') ?></label>
                    <input mdc-datetime-picker="" date="true" time="true" type="text" id="date_contacted" click-outside-to-close="true" placeholder="<?php echo lang2('chooseadate') ?>" show-todays-date="" minutes="true" today-btn="false" show-icon="true" ng-model="customer.created" class=" dtp-no-msclear dtp-input md-input">
                </md-input-container>
                <br>
                <md-input-container class="md-block">
                    <label>Data de inativação</label>
                    <input mdc-datetime-picker="" date="true" time="true" type="text" click-outside-to-close="true" placeholder="<?php echo lang2('chooseadate') ?>" show-todays-date="" minutes="true" today-btn="false" show-icon="true" ng-model="customer.dt_inactive" class=" dtp-no-msclear dtp-input md-input">
                </md-input-container>

                <md-slider-container> <span><?php echo lang2('riskstatus'); ?></span>
                    <md-slider flex min="0" max="100" ng-model="customer.risk" aria-label="red" id="red-slider"> </md-slider>
                    <md-input-container>
                        <input name="risk" flex type="number" ng-model="customer.risk" aria-label="red" aria-controls="red-slider">
                    </md-input-container>
                </md-slider-container>
            </div>


        </md-content>

        <md-subheader class="md-primary">

            <md-truncate><?php echo lang2('billing_address') ?></md-truncate>

            <md-button ng-click='SameAsCustomerAddress()' class="md-icon-button" aria-label="Copy Customer Address">

                <md-icon class="ion-ios-copy">

                    <md-tooltip md-direction="right"><?php echo lang2('same_as_customer') ?></md-tooltip>

                </md-icon>

            </md-button>

            <md-button class="pull-right hide-pinned-projects md-icon-button" aria-label="<?php echo lang2('billing_address') ?>">

                <a data-toggle="collapse" data-parent="#billing_address" href="#billing_address">

                    <md-icon class="ion-chevron-down">

                    </md-icon>

                </a>

            </md-button>

        </md-subheader>

        <md-content layout-padding id="billing_address" class="panel-collapse collapse out">

            <md-input-container class="md-block">

                <label><?php echo lang2('address') ?></label>

                <textarea ng-model="customer.billing_street" name="address" md-maxlength="500" rows="3" md-select-on-focus></textarea>

            </md-input-container>

            <md-input-container class="md-block">

                <label><?php echo lang2('country'); ?></label>

                <md-select placeholder="<?php echo lang2('country'); ?>" ng-model="customer.billing_country" ng-change="getBillingStates(customer.billing_country)" name="billing_country" style="min-width: 200px;">

                    <md-option ng-value="country.id" ng-repeat="country in countries">{{country.shortname}}</md-option>

                </md-select><br>

            </md-input-container>

            <md-input-container class="md-block">

                <label><?php echo lang2('state'); ?></label>

                <md-select placeholder="<?php echo lang2('state'); ?>" ng-model="customer.billing_state_id" name="billing_state_id" style="min-width: 200px;">

                    <md-option ng-value="state.id" ng-repeat="state in billingStates">{{state.state_name}}</md-option>

                </md-select>

            </md-input-container>

            <md-input-container class="md-block">

                <label><?php echo lang2('city'); ?></label>

                <input name="city" ng-model="customer.billing_city">

            </md-input-container>

            <md-input-container class="md-block">

                <label><?php echo lang2('zipcode'); ?></label>

                <input name="zipcode" ng-model="customer.billing_zip">

            </md-input-container>

        </md-content>

        <md-subheader class="md-primary">

            <md-truncate><?php echo lang2('shipping_address') ?></md-truncate>

            <md-button ng-click='SameAsBillingAddress()' class="md-icon-button" aria-label="Favorite">

                <md-icon class="ion-ios-copy">

                    <md-tooltip md-direction="right"><?php echo lang2('same_as_billing') ?></md-tooltip>

                </md-icon>

            </md-button>

            <md-button class="pull-right hide-pinned-projects md-icon-button" aria-label="<?php echo lang2('shipping_address') ?>">

                <a data-toggle="collapse" data-parent="#shipping_address" href="#shipping_address">

                    <md-icon class="ion-chevron-down">

                    </md-icon>

                </a>

            </md-button>

        </md-subheader>

        <md-content layout-padding id="shipping_address" class="panel-collapse collapse out">

            <md-input-container class="md-block">

                <label><?php echo lang2('address') ?></label>

                <textarea ng-model="customer.shipping_street" name="address" md-maxlength="500" rows="3" md-select-on-focus></textarea>

            </md-input-container>

            <md-input-container class="md-block">

                <label><?php echo lang2('country'); ?></label>

                <md-select placeholder="<?php echo lang2('country'); ?>" ng-model="customer.shipping_country" ng-change="getShippingStates(customer.shipping_country)" name="shipping_country" style="min-width: 200px;">

                    <md-option ng-value="country.id" ng-repeat="country in countries">{{country.shortname}}</md-option>

                </md-select><br>

            </md-input-container>

            <md-input-container class="md-block">

                <label><?php echo lang2('state'); ?></label>

                <md-select placeholder="<?php echo lang2('states'); ?>" ng-model="customer.shipping_state_id" name="shipping_state_id" style="min-width: 200px;">

                    <md-option ng-value="state.id" ng-repeat="state in shippingStates">{{state.state_name}}</md-option>

                </md-select>

            </md-input-container>

            <md-input-container class="md-block">

                <label><?php echo lang2('city'); ?></label>

                <input name="city" ng-model="customer.shipping_city">

            </md-input-container>

            <md-input-container class="md-block">

                <label><?php echo lang2('zipcode'); ?></label>

                <input name="zipcode" ng-model="customer.shipping_zip">

            </md-input-container>

        </md-content>

        <custom-fields-vertical></custom-fields-vertical>

        <br>

        <md-content layout-padding>



        </md-content>

        <br>

        <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>

            <md-button ng-click="UpdateCustomer()" class="md-raised md-primary btn-report block-button" ng-disabled="savingCustomer == true">

                <span ng-hide="savingCustomer == true"><?php echo lang2('update'); ?></span>

                <md-progress-circular class="white" ng-show="savingCustomer == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

            </md-button>

            <br /><br /><br /><br />

        </section>

    </md-sidenav>


    <div class="modal fade" id="modalContatoSocial" style="z-index: 2000;">
        <div class="modal-dialog mdAngular">
            <div class="modal-content">
                <div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
                    <button type="button" class="close" ng-click="dimiss()">
                        <span aria-hidden="true">×</span> <span class="sr-only"><?php echo lang2('close') ?></span>
                    </button>
                    <b>Selecione o contato</b>
                </div>
                <div id="modalBody" class="modal-body">
                    <md-table-container>
                        <table md-table md-progress="promise" style="width: 100%;border-top: 0;">
                            <thead>
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Contato</th>
                                    <th scope="col" width="50px">Acionar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="contato in contacts" ng-show="
              (contato.email.length > 0 && ShowContato == 'email') ||
              (contato.linkedin.length > 0 && ShowContato == 'linkedin') ||
              (contato.instagram.length > 0 && ShowContato == 'instagram') ||
              (contato.mobile.length > 0 && ShowContato == 'whatsapp')
              ">
                                    <th scope="row">{{contato.name}}</th>
                                    <td>{{ShowContato == 'email' ? contato.email : ShowContato == 'linkedin' ? contato.linkedin : ShowContato == 'instagram' ? contato.instagram : contato.mobile}}</td>

                                    <td>
                                        <a href="<?= base_url('emails/send_email') ?>?email={{contato.email}}&customer={{contato.name}}&company_name={{customer.company}}&customer={{customer.id}}" target="_blank" class="btn btn-success btn_icon" ng-if="contato.email.length > 0 && ShowContato == 'email'">
                                            <img src="{{base_url + 'assets/img/icons/email.png'}}">
                                        </a>
                                        <a href="{{ contato.linkedin.startsWith('http') ? contato.linkedin : 'https://' + contato.linkedin }}" class="btn btn-success btn_icon" target="_blank" ng-if="contato.linkedin.length > 0 && ShowContato == 'linkedin'">
                                            <img src="{{base_url + 'assets/img/icons/linkedin.png'}}">
                                        </a>
                                        <a href="{{contato.instagram}}" class="btn btn-success btn_icon" target="_blank" ng-if="contato.instagram.length > 0 && ShowContato == 'instagram'">
                                            <img src="{{base_url + 'assets/img/icons/instagram.png'}}">
                                        </a>
                                        <a href="https://wa.me/+55{{replaceNumero(contato.mobile)}}" ng-if="contato.mobile.length > 0 && ShowContato == 'whatsapp'" target="_blank" class="btn btn-success btn_icon">
                                            <img src="{{base_url + 'assets/img/icons/whatsapp.png'}}">
                                        </a>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </md-table-container>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAddAtividade" style="z-index: 2000;">
        <div class="modal-dialog modal-lg mdAngular">
            <div class="modal-content">
                <div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
                    <button type="button" class="close" ng-click="dimiss()">
                        <span aria-hidden="true">×</span> <span class="sr-only"><?php echo lang2('close') ?></span>
                    </button>
                    <b> Adicionar nova atividade</b>
                </div>
                <div id="modalBody" class="modal-body">
                    <div class="row">
                        <div class="col-md-8 col-12">
                            <md-input-container class="md-block" style='margin-top: 8px;'>
                                <label style="margin-bottom: 0;">Atividade</label>
                                <md-select ng-model="atividade.atividade" class="form-control select-modal">
                                    <md-option ng-value="atv.id_atv" ng-repeat="atv in leadAtvSelect">{{atv.nm_atividade_select}}</md-option>
                                </md-select>
                            </md-input-container>
                        </div>

                        <div class="col-md-4 col-12">
                            <md-input-container class="md-block">
                                <label>Data</label>

                                <input ng-model="atividade.data" type="date">
                            </md-input-container>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 1em;">
                        <div class="col-md-6 col-12">
                            <md-input-container class="md-block">
                                <label>Horário</label>
                                <input ng-model="atividade.horario" id="horarioAtv" type="time">
                            </md-input-container>
                        </div>

                        <div class="col-md-6 col-12">
                            <md-input-container class="md-block" style='margin-top: 8px;'>
                                <label style="margin-bottom: 0;">Duração</label>
                                <md-select ng-model="atividade.duracao" class="form-control select-modal">
                                    <md-option value="00:05:00">5 minutos</md-option>
                                    <md-option value="00:10:00">10 minutos</md-option>
                                    <md-option value="00:30:00">30 minutos</md-option>
                                    <md-option value="01:00:00">60 minutos</md-option>
                                </md-select>
                            </md-input-container>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 1em;">
                        <div class="col-12">
                            <md-input-container class="md-block" style="margin-left: 10px;margin-right: 10px;height: auto;">
                                <label>Anotações</label>
                                <textarea style="max-height: 205px;background: rgb(240, 248, 255) !important;" ng-model="atividade.anotacoes" class="text-area-modal"></textarea>
                            </md-input-container>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-5" style="float: right">
                            <md-input-container class="md-block">
                                <label>Retorno</label>
                                <input ng-model="atividade.retorno" type="date">
                            </md-input-container>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">

                    <div style="float: left;">
                        <a href="{{lead.instagram}}" style="padding: 0 !important;" class="btn btn-success btn_icon" target="_blank" ng-if="lead.instagram.length > 0">
                            <img src="{{base_url + 'assets/img/icons/instagram.png'}}">
                        </a>
                        <a href="javaScript:void(0)" style="padding: 0 !important;" class="btn btn-success btn_icon" ng-click="showDialogContatoSocial('email')">
                            <img src="{{base_url + 'assets/img/icons/email.png'}}">
                        </a>
                        <a href="javaScript:void(0)" style="padding: 0 !important;" class="btn btn-success btn_icon" ng-click="showDialogContatoSocial('linkedin')">
                            <img src="{{base_url + 'assets/img/icons/linkedin.png'}}">
                        </a>
                        <a href="javaScript:void(0)" style="padding: 0 !important;" ng-click="showDialogContatoSocial('whatsapp')" class="btn btn-success btn_icon">
                            <img src="{{base_url + 'assets/img/icons/whatsapp.png'}}">
                        </a>
                    </div>

                    <button type="button" class="btn btn-secondary" ng-click="dimiss()">Fechar</button>
                    <button type="button" class="btn btn-success" ng-click="salvarAtividade()">Salvar</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalAddReuniao" style="z-index: 2000;">
        <div class="modal-dialog mdAngular">
            <div class="modal-content">
                <div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
                    <button type="button" class="close" ng-click="dimiss()">
                        <span aria-hidden="true">×</span> <span class="sr-only"><?php echo lang2('close') ?></span>
                    </button>
                    <b>{{newReuniao.id_reuniao == null ? 'Adicionar reunião' : 'Editar reunião'}}</b>
                </div>
                <div id="modalBody" class="modal-body" style="max-height: 70vh; overflow: auto; overflow-x: hidden;">

                    <div class="row">
                        <div class="col-md-12 col-12" style="min-height: 60px;" ng-if="newReuniao.id_reuniao != null">
                            <a href="#" ng-click="apagaReuniao(newReuniao)" style="float: right; color: #f00;" class="btn btn-outline-light">Apagar <i class="fas fa-trash-alt"></i></a>
                        </div>

                        <div class="col-md-8 col-12">
                            <md-input-container class="md-block">
                                <label style="margin-bottom: 0;">Data</label>
                                <input ng-model="newReuniao.data" type="date">
                            </md-input-container>
                        </div>
                        <div class="col-md-4 col-12">
                            <md-input-container class="md-block">
                                <label style="margin-bottom: 0;">Hora</label>
                                <input ng-model="newReuniao.hora" type="time">
                            </md-input-container>
                        </div>

                        <div class="col-md-6 col-12">
                            <md-input-container class="md-block" style='margin-top: 17px;'>
                                <label style="margin-bottom: 0;">Selecione o closer</label>
                                <md-select multiple placeholder="<?php echo lang2('choosestaff'); ?>" ng-model="newReuniao.id_funcionario" style="min-width: 200px;">
                                    <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
                                </md-select>
                            </md-input-container>
                        </div>

                        <div class="col-md-6 col-12">
                            <md-input-container class="md-block" style='margin-top: 8px;'>
                                <label style="top: -13px;height: 18px;">Tipo</label>
                                <md-select ng-model="newReuniao.tipo" class="form-control select-modal">
                                    <md-option value="Presencial">Presencial</md-option>
                                    <md-option value="Remota">Remota</md-option>
                                </md-select>
                            </md-input-container>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <md-input-container class="md-block" style="margin-left: 10px;margin-right: 10px;height: auto;">
                                <label>Observação</label>
                                <textarea ng-model="newReuniao.observacao" class="text-area-modal" style="background: none !important;"></textarea>
                            </md-input-container>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" ng-click="dimiss()">Fechar</button>
                    <button type="button" class="btn btn-success" ng-click="salvarReuniao()">Salvar</button>
                </div>

            </div>
        </div>
    </div>


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
                        <div class="col-md-12 col-12" style="min-height: 60px;">
                            <a href="#" ng-click="showReuniao(reuniaoModal.reuniao, reuniaoModal.id_atividade)" style="float: right;" class="btn btn-outline-light">Editar <i class="fas fa-external-link-alt"></i></a>
                        </div>

                        <div class="col-md-12 col-12">
                            <md-input-container class="md-block" style='margin-top: 8px;' ng-if="reuniaoModal.reuniao.id_funcionario.split(',').includes('<?= $id_user ?>')">
                                <label style="margin-bottom: 0;">Realizada?</label>
                                <md-select ng-model="reuniaoModal.realizada" class="form-control select-modal">
                                    <md-option value="Realizada">Realizada</md-option>
                                    <md-option value="Não Realizada">Não Realizada</md-option>
                                </md-select>
                            </md-input-container>
                        </div>


                        <div class="col-md-12 col-12" ng-show="reuniaoModal.realizada == 'Não Realizada'">
                            <md-input-container class="md-block" style='margin-top: 8px;'>
                                <label style="margin-bottom: 0;">Qual o problema?</label>
                                <md-select ng-model="reuniaoModal.problema" class="form-control select-modal">
                                    <md-option value="Lead">Lead</md-option>
                                    <md-option value="Nossa empresa">Nossa empresa</md-option>
                                </md-select>
                            </md-input-container>
                        </div>

                        <div class="col-md-12 col-12" ng-show="reuniaoModal.problema == 'Lead'">
                            <md-input-container class="md-block" style='margin-top: 8px;'>
                                <label style="margin-bottom: 0;">Motivo?</label>
                                <md-select ng-model="reuniaoModal.motivo" class="form-control select-modal">
                                    <md-option value="Não compareceu">Não compareceu</md-option>
                                    <md-option value="Reagendou">Reagendou</md-option>
                                    <md-option value="Cancelou">Cancelou</md-option>
                                </md-select>
                            </md-input-container>
                        </div>


                    </div>



                    <div class="divInfoL2">
                        <div class="divInfoL2" ng-if="lead.tp_pessoa == 1">
                            <md-icon class="mdi mdi-local-store flt_left"></md-icon>
                            <div class="flt_left">
                                <p class="titleInfo"><?php echo lang2('name') ?></p>
                                <p class="nmInfo"><span ng-bind="lead.name"></span></p>
                            </div>
                        </div>

                        <div class="divInfoL2" ng-if="lead.tp_pessoa == 2">
                            <md-icon class="mdi mdi-local-store flt_left"></md-icon>
                            <div class="flt_left">
                                <p class="titleInfo"><?php echo lang2('company') ?></p>
                                <p class="nmInfo"><span ng-bind="lead.company"></span></p>
                            </div>
                        </div>

                        <div class="divInfoL2" ng-if="reuniaoModal.phone != null && reuniaoModal.phone != ''">
                            <md-icon class="mdi mdi-local-phone"></md-icon>
                            <div class="flt_left">
                                <p class="titleInfo"><?php echo lang2('phone') ?></p>
                                <p class="nmInfo"><span ng-bind="reuniaoModal.phone"></span></p>
                            </div>
                        </div>

                        <div class="divInfoL2" ng-if="reuniaoModal.email != null && reuniaoModal.email != ''">
                            <md-icon class="ion-android-mail"></md-icon>
                            <div class="flt_left">
                                <p class="titleInfo"><?php echo lang2('email') ?></p>
                                <p class="nmInfo"><span ng-bind="reuniaoModal.email"></span></p>
                            </div>
                        </div>

                        <div class="divInfoL2">
                            <md-icon class="ion-android-calendar"></md-icon>
                            <div class="flt_left">
                                <p class="titleInfo">Data da reunião</p>
                                <p class="nmInfo"><span ng-bind="reuniaoModal.data | date:'dd/MM/yyyy'"></span> <span ng-bind="reuniaoModal.horario | date:'HH:mm'"></span></p>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="modal-footer" style="margin-top: 30px;" ng-if="reuniaoModal.reuniao.id_funcionario.split(',').includes('<?= $id_user ?>')">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-success" ng-click="salva_reuniao(reuniaoModal.id_reuniao)">Salvar</button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    var CUSTOMERID = "<?php echo $customers['id']; ?>";

    var lang = {};

    lang.doIt = "<?php echo lang2('doIt') ?>";

    lang.cancel = "<?php echo lang2('cancel') ?>";

    lang.attention = "<?php echo lang2('attention') ?>";

    lang.delete_customer = "<?php echo lang2('customerattentiondetail') ?>";

    lang.delete_contact = "<?php echo lang2('contactattentiondetail') ?>";
</script>

<?php include_once(APPPATH . 'Views/inc/footer.php'); ?>

<script src="<?php echo base_url('assets/lib/chartjs/dist/Chart.min.js'); ?>" type="text/javascript"></script>

<script type="text/javascript" src="<?php echo base_url('assets/js/customers.js?v=c1.2.16') ?>"></script>