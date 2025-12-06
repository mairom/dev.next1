<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>

<?php $appconfig = get_appconfig(); ?>

<style>
  .topRow {
    margin-bottom: 30px;
  }

  .on-drag-enter {}

  .on-drag-hover:before {
    display: block;
    color: white;
    font-size: x-large;
    font-weight: 800;
  }

  .noright2:hover {
    background: #e7e7e7;
  }

  .small {
    height: 20px;
  }

  .md-toolbar-tools>md-menu:last-child {
    margin-top: 0px !important;
  }

  .ciuis_lead_kanban_board div.items_list {
    max-height: 100vh !important;

  }

  .divSelects {
    float: left;
    width: 100%;
    margin-top: 10px;
    border-top: 1px solid rgb(209, 209, 209);
    padding-top: 10px;
  }
</style>

<div class="ciuis-body-content" ng-controller="Leads_Controller">
  <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">

    <div ng-show="leadsLoader" layout-align="center center" class="text-center" id="circular_loader">
      <!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
      <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">
      <p style="font-size: 15px;margin-bottom: 5%;">
        <span>
          <?php echo lang2('please_wait') ?> <br>
          <small><strong><?php echo lang2('loading') . ' ' . lang2('lead') . '...' ?></strong></small>
        </span>
      </p>
    </div>


    <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-12 md-p-0 lead-table" ng-show="!leadsLoader" ng-if="KanbanBoard">
      <md-toolbar class="toolbar-white toolbar-title" style="margin-left: 4px;" ng-cloak>
        <div class="md-toolbar-tools md-tools">

          <div class="col-12 col-md-8">
            <md-button class="md-icon-button" aria-label="Settings" ng-disabled="true">
              <img style="transition: all 0.1s ease-in-out;width: 100%;text-align: center;margin: 0 auto;" src="https://next1crm.com.br/assets/img/menu/Leads.svg">
            </md-button>

            <h2 flex md-truncate class="text-bold">
              <?php echo lang2('leads'); ?>
              <small>(<span ng-bind="leads.length"></span>)</small><br>
              <small flex md-truncate><?php echo lang2('leaddesc'); ?></small>
            </h2>

            <div class="search-wrapper">
              <button class="close" ng-click="openSearch()">
              </button>
              <div class="input-holder">
                <input ng-model="filtros.lead_search" ng-keyUp="filtrarLeads()" type="text" placeholder="<?php echo lang2('searchword') ?>" class="search-input">
                <button class="search-icon" ng-click="openSearch()">
                  <span></span>
                </button>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <md-button ng-click="LeadSettings()" class="md-icon-button btn_settings" aria-label="Settings" ng-cloak>
              <md-tooltip md-direction="bottom"><?php echo lang2('settings') ?></md-tooltip>
              <md-icon style="font-size: 20px;">
                <i class="fas fa-sliders-h" style="color: #3f6ad8;font-size: 15px !important;"></i>
              </md-icon>
            </md-button>

            <md-button ng-if="!KanbanBoard" ng-click="ShowKanban()" class="md-icon-button btn_settings" aria-label="Show Kanban" ng-cloak>
              <md-tooltip md-direction="bottom"><?php echo lang2('showkanban'); ?></md-tooltip>
              <md-icon aria-label="Add Source"><i style="color: #3f6ad8;" class="mdi mdi-view-week text-muted"></i></md-icon>
            </md-button>

            <md-button ng-if="KanbanBoard" ng-click="HideKanban()" class="md-icon-button btn_settings" aria-label="Show List" ng-cloak>
              <md-tooltip md-direction="bottom"><?php echo lang2('showlist'); ?></md-tooltip>
              <md-icon aria-label="Add Source"><i style="color: #3f6ad8;" class="mdi mdi-view-list text-muted"></i></md-icon>
            </md-button>

            <md-button style="margin: 0;" class="md-icon-button btn_settings collapseFiltro" aria-label="Filter" ng-cloak>
              <md-tooltip md-direction="bottom"><?php echo lang2('filter') ?></md-tooltip>
              <md-icon style="font-size: 20px;">
                <i class="fas fa-filter" style="color: #3f6ad8;font-size: 15px !important;"></i>
              </md-icon>
            </md-button>

            <?php if (check_privilege_export('leads')) { ?>
              <md-button ng-click="Import()" class="md-icon-button btn_settings" aria-label="Filter" ng-cloak>
                <md-tooltip md-direction="bottom"><?php echo lang2('importleads') ?></md-tooltip>
                <md-icon style="margin-top: -5px;;">
                  <i class="fas fa-upload" style="color: #3f6ad8;font-size: 17px !important;"></i>
                </md-icon>
              </md-button>




              <md-button ng-click="Export()" class="md-icon-button btn_settings">
                <div layout="row" flex>
                  <md-tooltip md-direction="bottom"><?= lang2('exportleads'); ?></md-tooltip>
                  <i class="fas fa-download" style="color: #3f6ad8;font-size: 17px !important;"></i>
                </div>
              </md-button>

            <?php } ?>

            <button type="button" class="btn-shadow d-inline-flex align-items-center btn2 btn-success" ng-click="Create()">
              <i class="fas fa-plus"></i> Novo Lead
            </button>
          </div>



          <?php if (check_privilege('leads', 'create')) { ?>
            <!--
            <md-button ng-click="Create()" class="md-icon-button btn_settings" aria-label="New" ng-cloak>
              <md-tooltip md-direction="bottom"><?php echo lang2('create') ?></md-tooltip>
              <md-icon aria-label="Add Source"><i class="ion-android-add-circle text-success"></i></md-icon>
            </md-button>
            -->
          <?php } ?>
          <!--
          <md-menu md-position-mode="target-right target" ng-cloak>
            <md-button aria-label="Open demo menu" class="md-icon-button btn_settings" ng-click="$mdMenu.open($event)">
              <md-icon aria-label="Add Source"><i class="ion-android-more-vertical text-muted"></i></md-icon>
            </md-button>
            <md-menu-content width="4">

              <?php if (check_privilege('leads', 'create')) { ?>

                <md-menu-item>

                  <md-button ng-click="Import()" aria-label="Add">
                    <div layout="row" flex>
                      <p flex><?php echo lang2('importleads') ?></p>
                      <md-icon aria-label="Add Source" md-menu-align-target class="ion-upload" style="margin: auto 3px auto 0;"></md-icon>
                    </div>

                  </md-button>

                </md-menu-item>

              <?php } ?>

-->
          <!--
              <?php echo form_open_multipart('leads/exportdata', array("class" => "form-horizontal")); ?>

              <md-menu-item>
                <md-button type="submit" aria-label="Add">
                  <div layout="row" flex>
                    <p flex ng-bind="lang.exportleads"></p>
                    <md-icon aria-label="Add Source" md-menu-align-target class="ion-android-download text-muted" style="margin: auto 3px auto 0;"></md-icon>
                  </div>
                </md-button>
              </md-menu-item>

              <?php echo form_close(); ?>
              

              <?php if (check_privilege('leads', 'delete')) { ?>

                <md-menu-item>

                  <md-button ng-click="RemoveConverted()" aria-label="Add">

                    <div layout="row" flex>

                      <p flex><?php echo lang2('deleteconvertedleads') ?></p>

                      <md-icon aria-label="Add Source" md-menu-align-target class="ion-android-remove-circle" style="margin: auto 3px auto 0;"></md-icon>

                    </div>

                  </md-button>

                </md-menu-item>

              <?php } ?>

              <md-menu-item>

                <md-button aria-label="Add">

                  <a ng-href="<?php echo base_url('leads/forms') ?>">

                    <div layout="row" flex>

                      <p flex ng-bind="lang.webleads"></p>

                      <md-icon aria-label="Add Source" md-menu-align-target class="ion-earth text-muted" style="margin: auto 3px auto 0;"></md-icon>

                    </div>

                  </a>

                </md-button>

              </md-menu-item>

              -->
          <!--
          </md-menu-content>
          </md-menu>
          -->

        </div>
      </md-toolbar>

      <div ng-show="eadsLoader" layout-align="center center" class="text-center" id="circular_loader">
        <!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
        <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">
        <p style="font-size: 15px;margin-bottom: 5%;">
          <span>
            <?php echo lang2('please_wait') ?> <br>
            <small><strong><?php echo lang2('loading') . ' ' . lang2('leads') . '...' ?></strong></small>
          </span>
        </p>
      </div>

      <md-content class="ciuis_lead_kanban_board" style="overflow: hidden;" ng-cloak>
        <div class='filtrosLeads collapseFiltroOpen' id="collapseFiltro">
          <md-input-container class="md-block">
            <label>Vencidos</label>
            <md-select ng-model="filtros.flt_vencidos" id="flt_vencidos" style="min-width: 200px;" ng-change="filtrarLeads()">
              <md-option ng-value="2">Vencidos</md-option>
              <md-option ng-value="3">Vencerá hoje</md-option>
              <md-option ng-value="1">No prazo</md-option>
              <md-option ng-value="-1">Todos</md-option>
            </md-select>
          </md-input-container>

          <md-input-container class="md-block">
            <label>Período</label>
            <md-select ng-model="filtros.flt_periodo" id="flt_periodo" style="min-width: 200px;" ng-change="filtrarLeads()">
              <md-option ng-value="1">Hoje</md-option>
              <md-option ng-value="7">Até 7 dias</md-option>
              <md-option ng-value="15">Até 15 dias</md-option>
              <md-option ng-value="30">Até 30 dias</md-option>
              <md-option ng-value="90">Até 3 meses</md-option>
              <md-option ng-value="180">Até 6 meses</md-option>
              <md-option ng-value="365">Até 1 ano</md-option>
              <md-option ng-selected="true" ng-value="-1">Tudo</md-option>
            </md-select>
          </md-input-container>

          <md-input-container class="md-block">
            <label>Funcionário</label>
            <md-select placeholder="<?php echo lang2('choosestaff'); ?>" id="flt_funcionario" ng-model="filtros.flt_funcionario" style="min-width: 200px;" ng-change="filtrarLeads()">
              <md-option ng-selected="true" ng-value="-1">Todos</md-option>
              <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
            </md-select>
          </md-input-container>

          <md-input-container class="md-block">
            <label><?php echo lang2('source'); ?></label>
            <md-select placeholder="<?php echo lang2('source'); ?>" id="flt_origem" ng-model="filtros.flt_origem" style="min-width: 200px;" ng-change="filtrarLeads()">
              <md-option ng-selected="true" ng-value="-1">Todos</md-option>
              <md-option ng-value="source.id" ng-repeat="source in leadssources">{{source.name}}</md-option>
            </md-select>
          </md-input-container>


          <md-input-container class="md-block">
            <i class="iconSearch ion-funnel text-muted "></i>
            <md-select placeholder="Escolha um funil" id="flt_funil" ng-model="filtros.flt_funil" ng-change="changeFunil(filtros.flt_funil);filtrarLeads()" style="min-width: 200px;">
              <md-option ng-selected='$index == funilSelect_index' ng-value="$index" ng-repeat="funil in leadslist">{{funil.nm_list}}</md-option>
            </md-select>
          </md-input-container>

          <md-input-container class="md-block">
            <label>Status</label>
            <md-select placeholder="Status" id="lost" ng-model="filtros.lost" style="min-width: 200px;" ng-change="filtrarLeads()">
              <md-option ng-value="-1">Todos</md-option>
              <md-option ng-selected="true" ng-value="0">Ativos</md-option>
              <md-option ng-value="1">Inativos</md-option>
            </md-select>
          </md-input-container>

          <md-input-container class="md-block">
            <label>Tag</label>
            <input ng-model="filtros.tags" placeholder="Tag" style="min-width: 200px;" ng-change="filtrarLeads()">
          </md-input-container>

          <md-input-container class="md-block">
            <label>Temperatura do lead</label>
            <md-select placeholder="Temperatura" id="temp" ng-model="filtros.temperatura" style="min-width: 200px;" ng-change="filtrarLeads()">
              <md-option ng-selected="true" ng-value="-1">Todos</md-option>
              <md-option value="Quente">Quente</md-option>
              <md-option value="Morno">Morno</md-option>
              <md-option value="Frio">Frio</md-option>
            </md-select>
          </md-input-container>


          <md-input-container class="md-block">
            <label>Canais disponíveis</label>
            <md-select placeholder="Canais disponíveis" id="canais" ng-model="filtros.canais" style="min-width: 200px;" ng-change="filtrarLeads()">
              <md-option ng-selected="true" ng-value="-1">Todos</md-option>
              <md-option value="1">Email</md-option>
              <md-option value="2">Telefone</md-option>
              <md-option value="3">E-mail + Telefone</md-option>
              <md-option value="4">Linkedin</md-option>
            </md-select>
          </md-input-container>

          

          <div ng-show="selectedLeads.length > 0" class="divSelects">

            <md-button ng-click="SelectSalesFlow()" class="md-icon-button btn_settings" aria-label="Filter" ng-cloak>
              <md-tooltip md-direction="bottom">Sales flow</md-tooltip>
              <md-icon style="margin-top: -5px;;">
                <img style="width: 90%; margin-left: 1px; margin-top: 2px;" src="<?php echo base_url('assets/img/menu/steps.png') ?>">
              </md-icon>
            </md-button>

            <md-button ng-click="SelectsEmailAut()" class="md-icon-button btn_settings" aria-label="Filter" ng-cloak>
              <md-tooltip md-direction="bottom">Emails automatizados</md-tooltip>
              <md-icon style="margin-top: -5px;;">
                <i class="fas fa-envelope" style="color: #3f6ad8;font-size: 17px !important;"></i>
              </md-icon>
            </md-button>

            <md-button ng-click="SelectsFunil()" class="md-icon-button btn_settings" aria-label="Filter" ng-cloak>
              <md-tooltip md-direction="bottom">Transferir para outro funil</md-tooltip>
              <md-icon style="margin-top: -5px;;">
                <i class="fas fa-sync-alt" style="color: #3f6ad8;font-size: 17px !important;"></i>
              </md-icon>
            </md-button>

            <md-button ng-click="SelectsFunc()" class="md-icon-button btn_settings" aria-label="Filter" ng-cloak>
              <md-tooltip md-direction="bottom">Atribuir a funcionário</md-tooltip>
              <md-icon style="margin-top: -5px;;">
                <i class="fas fa-user-tie" style="color: #3f6ad8;font-size: 17px !important;"></i>
              </md-icon>
            </md-button>

            <md-button ng-click="SelectsInativar()" class="md-icon-button btn_settings" aria-label="Filter" ng-cloak>
              <md-tooltip md-direction="bottom">Inativar</md-tooltip>
              <md-icon style="margin-top: -5px;;">
                <i class="fas fa-eye-slash" style="color: #3f6ad8;font-size: 17px !important;"></i>
              </md-icon>
            </md-button>

            <md-button ng-click="SelectsWhatsApp()" class="md-icon-button btn_settings" aria-label="Filter" ng-cloak>
              <md-tooltip md-direction="bottom">Enviar mensagem</md-tooltip>
              <md-icon style="margin-top: -5px;;">
                <i class="fab fa-whatsapp" style="color: #3f6ad8;font-size: 17px !important;"></i>
              </md-icon>
            </md-button>


            <md-button ng-click="SelectsExcluir()" class="md-icon-button btn_settings" aria-label="Filter" ng-cloak>
              <md-tooltip md-direction="bottom">Excluir</md-tooltip>
              <md-icon style="margin-top: -5px;;">
                <i class="fas fa-trash" style="color: #3f6ad8;font-size: 17px !important;"></i>
              </md-icon>
            </md-button>

          </div>

        </div>


        <div class="todoLeads">
          <div ng-repeat="list in leadslist" style="float: left;" ng-show="$index == filtros.flt_funil">
            <md-list class="ciuis_lead_status_card" style="width: 204px;" ng-init="parentIndex = $index" flex ng-repeat="lead_status in list.leadstatuses track by lead_status.id" ui-on-Drop="onDrop($event, $data, lead_status.id, $index)">


              <div class="div-title-funil" style="min-height: 45px;">
                <md-toolbar class="toolbar-white md-toolbar-funil" style="min-height: 0;">
                  <div class="md-toolbar-tools toolbarPerson" style="padding-bottom: 5px;padding-top: 5px;border-radius: 5px;padding-left: 10px;padding-right: 5px; border-left: 3px solid #28B8DA;">
                    <h3 flex md-truncate>
                      <input type="checkbox" class="selectStatus" value="{{lead_status.id}}">
                      {{lead_status.name}} <span>({{getLeadByStatus(lead_status.id).length}})</span>
                    </h3>
                    <md-menu md-position-mode="target-right target" style="width: 30px;">
                      <md-button aria-label="Open demo menu" style="width: 30px;margin-left: -10px;" class="md-icon-button" ng-click="$mdMenu.open($event)">
                        <md-icon aria-label="Add Source">
                          <i class="ion-android-more-vertical text-muted" style="vertical-align: super;"></i>
                        </md-icon>
                      </md-button>
                      <md-menu-content width="4">
                        <?php if (check_privilege('leads', 'edit')) { ?>
                          <md-menu-item>
                            <md-button ng-click="EditStatus(lead_status.id,lead_status.name, $event, $index)" aria-label="Add">
                              <div layout="row" flex>
                                <p flex><?php echo lang2('edit_status'); ?></p>
                                <md-icon aria-label="Add Source" md-menu-align-target class="ion-edit" style="margin: auto 3px auto 0;"></md-icon>
                              </div>
                            </md-button>
                          </md-menu-item>
                        <?php } ?>
                      </md-menu-content>
                    </md-menu>
                  </div>
                </md-toolbar>
              </div>
              <div class="items_list" id="items_list{{lead_status.id}}" style="height: 100vh;margin-top: 10px;">

                <md-list-item class="md-3-line" style="margin: 5px 0px;" ng-if="lead_status.id == lead.status && (filtros.lost == '-1' || filtros.lost == lead.lost)" ui-draggable="true" drag="lead" on-drop-success="dropSuccessHandler($event,$index,lead_status.id)" ng-repeat="lead in leads | orderBy: lead.name_lead | filter: { status: lead_status.id, view : '1'} ">
                  <div class="md-list-item-text" layout="column">
                    <div layout="row" layout-wrap>
                      <div flex-gt-xs="80" flex-xs="80">
                        <h3 flex>
                          <input type="checkbox" class="inptSel{{lead.status}}" ng-model="selectedLeads" ng-true-value="{{lead.id}}" ng-false-value="false" ng-change="updateSelection(lead.id)">

                          <a class="link" target="_blank" ng-href="<?php echo base_url('leads/lead/') ?>{{lead.id}}" style="font-weight: bold;font-size: 14px;">
                            {{ lead.name_lead | limitTo: 20 }}{{lead.name_lead.length > 20 ? '...' : ''}}
                          </a>
                        </h3>
                      </div>
                    </div>
                    <!--
                    <div class="small smallCardLe" style="width: 92%;height: 15px;font-size: 11px;" ng-if="lead.tp_pessoa == '2'">
                      <div class="blur" style="float: left;">
                      </div>
                      <div class="divemailLead">
                        <p style="font-size: 11px;">{{ lead.ctt_principal | limitTo: 14 }}{{lead.ctt_principal.length > 13 ? '...' : ''}}</p>
                      </div>
                    </div>
                        -->

                    <p class="small smallCardLe">
                      <span class="blur " style="color: #000;font-weight: 600;font-size: 11px;"><?php echo lang2('source') ?>:</span>
                      <span>{{ lead.sourcename | limitTo: 30 }}{{lead.sourcename.length > 28 ? '...' : ''}}</span>

                      <img ng-if="lead.ultimoFlowAutomatico != null" src="<?php echo base_url('assets/img/sales_robot.gif') ?>" class="md-avatar" alt="{{lead.assigned}}" style="float: right; width: 30px;height: 30px;margin-top: -15px;margin-right: -1px;margin-bottom: 5px;">

                    </p>

                    <p class="small smallCardLe">
                      <span class="blur " style="color: #000;font-weight: 600;font-size: 11px;">Dias ativo: </span>
                      <span>{{ lead.diasAtivo}}</span>


                    </p>

                    <p class="small smallCardLe">
                      <img ng-src="<?php echo base_url('assets/img/temp.png') ?>" class="md-avatar" style="float: left;">


                      <span style="color: {{ lead.tempColor}};font-size: 11px;">{{lead.temperatura}}</span>

                      <span>
                        <img ng-src="<?php echo base_url('uploads/images/') ?>{{lead.avatar}}" class="md-avatar" alt="{{lead.assigned}}" style="float: right;width: 20px; height: 20px;margin-top: -5px;margin-top: -5px;margin-right: 5px;">
                      </span>
                    </p>

                    <div class="fxVertical2" ng-style="lead.prazo == '1' && {'background':'#26b759'}  || lead.prazo == '2' && {'background':'#ef2828'} || lead.prazo == '3' && {'background':'#eead2d'} "></div>

                  </div>
                </md-list-item>

                <div ng-show="carregandoFunil" layout-align="center center" class="text-center" id="circular_loader">
                  <!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
                  <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">
                </div>

              </div>
            </md-list>
          </div>
        </div>


        <br><br><br>

      </md-content>



    </div>

    <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-12" ng-cloak>

      <div class="main-content container-fluid col-xs-12 col-md-3 col-lg-3 md-pl-0" ng-if="!KanbanBoard">

        <div class="panel-default panel-table borderten lead-manager-head">

          <div class="col-md-4 col-xs-4 border-right" style="margin-bottom: 10px;border-bottom: 2px dashed #cecece;padding-bottom: 20px;">

            <div class="tasks-status-stat">

              <h3 class="text-bold ciuis-task-stat-title"><span class="task-stat-number"><?php echo $tcl ?></span><span class="task-stat-all"> / <?php echo $tlh ?> <?php echo lang2('lead') ?></span></h3>

              <span class="ciuis-task-percent-bg"> <span class="ciuis-task-percent-fg" style="width: 40%;"></span> </span>

            </div>

            <span style="color:#989898"><?php echo lang2('converted') ?></span>

          </div>

          <div class="col-md-4 col-xs-4 border-right" style="margin-bottom: 10px;border-bottom: 2px dashed #cecece;padding-bottom: 20px;">

            <div class="tasks-status-stat">

              <h3 class="text-bold ciuis-task-stat-title"><span class="task-stat-number"><?php echo $tll ?></span><span class="task-stat-all"> / <?php echo $tlh ?> <?php echo lang2('lead') ?></span></h3>

              <span class="ciuis-task-percent-bg"> <span class="ciuis-task-percent-fg" style="width: 40%;"></span> </span>

            </div>

            <span style="color:#989898"><?php echo lang2('junk') ?></span>

          </div>

          <div class="col-md-4 col-xs-4 border-right" style="margin-bottom: 10px;border-bottom: 2px dashed #cecece;padding-bottom: 20px;">

            <div class="tasks-status-stat">

              <h3 class="text-bold ciuis-task-stat-title"><span class="task-stat-number"><?php echo $tjl ?></span><span class="task-stat-all"> / <?php echo $tlh ?> <?php echo lang2('lead') ?></span></h3>

              <span class="ciuis-task-percent-bg"> <span class="ciuis-task-percent-fg" style="width: 40%;"></span> </span>

            </div>

            <span style="color:#989898"><?php echo lang2('lost') ?></span>

          </div>

          <div class="widget-chart-container" style="border-bottom: 2px dashed #e8e8e8; margin-bottom: 20px; padding-bottom: 20px;">

            <div class="widget-counter-group widget-counter-group-right">

              <div style="width: auto" class="pull-left"> <i style="font-size: 38px;color: #bfc2c6;margin-right: 10px" class="ion-stats-bars pull-left"></i>

                <div class="pull-right" style="text-align: left;margin-top: 10px;line-height: 10px;">

                  <h4 style="padding: 0px;margin: 0px;"><b><?php echo lang2('leadsbyleadsource') ?></b></h4>

                  <small><?php echo lang2('leadstatsbysource') ?></small>

                </div>

              </div>

            </div>

            <div class="my-2">
              <div class="chart-wrapper">
                <canvas id="leads_by_leadsource"></canvas>
              </div>
            </div>

          </div>

        </div>

      </div>

      <div class="main-content container-fluid col-xs-12 col-md-9 col-lg-9 md-p-0 lead-table" ng-show="!leadsLoader" ng-if="!KanbanBoard">

        <md-toolbar class="toolbar-white" style="margin-left: 4px;" ng-cloak>

          <div class="md-toolbar-tools">

            <h2 flex md-truncate class="text-bold">
              <?php echo lang2('leads'); ?>
              <small>(<span ng-bind="leads.length"></span>)</small><br>
              <small flex md-truncate><?php echo lang2('leaddesc'); ?></small>
            </h2>

            <div class="ciuis-external-search-in-table" ng-cloak>

              <input ng-model="lead_search2" class="search-table-external" id="search" name="search" type="text" placeholder="<?php echo lang2('searchword') ?>">
              <md-button class="md-icon-button" aria-label="Search">
                <md-icon aria-label="Add Source"><i class="ion-search text-muted"></i></md-icon>
              </md-button>
            </div>

            <md-button ng-click="LeadSettings()" class="md-icon-button" aria-label="Settings" ng-cloak>

              <md-tooltip md-direction="bottom"><?php echo lang2('settings') ?></md-tooltip>

              <md-icon aria-label="Add"><i class="ion-ios-gear text-muted"></i></md-icon>

            </md-button>

            <md-button ng-if="!KanbanBoard" ng-click="ShowKanban()" class="md-icon-button" aria-label="Show Kanban" ng-cloak>

              <md-tooltip md-direction="bottom"><?php echo lang2('showkanban'); ?></md-tooltip>

              <md-icon aria-label="Add Source"><i class="mdi mdi-view-week text-muted"></i></md-icon>

            </md-button>

            <md-button ng-if="KanbanBoard" ng-click="HideKanban()" class="md-icon-button" aria-label="Show List" ng-cloak>

              <md-tooltip md-direction="bottom"><?php echo lang2('showlist'); ?></md-tooltip>

              <md-icon aria-label="Add Source"><i class="mdi mdi-view-list text-muted"></i></md-icon>

            </md-button>

            <md-button ng-click="toggleFilter()" class="md-icon-button" aria-label="Filter" ng-cloak>

              <md-tooltip md-direction="bottom"><?php echo lang2('filter') ?></md-tooltip>

              <md-icon aria-label="Add Source"><i class="ion-android-funnel text-muted"></i></md-icon>

            </md-button>

            <?php if (check_privilege('leads', 'create')) { ?>

              <md-button ng-click="Create()" class="md-icon-button" aria-label="New" ng-cloak>
                <md-tooltip md-direction="bottom"><?php echo lang2('create') ?></md-tooltip>
                <md-icon aria-label="Add Source"><i class="ion-android-add-circle text-success"></i></md-icon>
              </md-button>

            <?php } ?>

            <md-menu md-position-mode="target-right target" ng-cloak>

              <md-button aria-label="Open demo menu" class="md-icon-button" ng-click="$mdMenu.open($event)">

                <md-icon aria-label="Add Source"><i class="ion-android-more-vertical text-muted"></i></md-icon>

              </md-button>

              <md-menu-content width="4">

                <?php if (check_privilege('leads', 'create')) { ?>

                  <md-menu-item>

                    <md-button ng-click="Import()" aria-label="Add">

                      <div layout="row" flex>

                        <p flex><?php echo lang2('importleads') ?></p>

                        <md-icon aria-label="Add Source" md-menu-align-target class="ion-upload" style="margin: auto 3px auto 0;"></md-icon>

                      </div>

                    </md-button>

                  </md-menu-item>

                <?php } ?>

                <?php echo form_open_multipart('leads/exportdata', array("class" => "form-horizontal")); ?>

                <md-menu-item>

                  <md-button type="submit" aria-label="Add">

                    <div layout="row" flex>

                      <p flex ng-bind="lang.exportleads"></p>

                      <md-icon aria-label="Add Source" md-menu-align-target class="ion-android-download text-muted" style="margin: auto 3px auto 0;"></md-icon>

                    </div>

                  </md-button>

                </md-menu-item>

                <?php echo form_close(); ?>

                <?php if (check_privilege('leads', 'delete')) { ?>

                  <md-menu-item>

                    <md-button ng-click="RemoveConverted()" aria-label="Add">

                      <div layout="row" flex>

                        <p flex><?php echo lang2('deleteconvertedleads') ?></p>

                        <md-icon aria-label="Add Source" md-menu-align-target class="ion-android-remove-circle" style="margin: auto 3px auto 0;"></md-icon>

                      </div>

                    </md-button>

                  </md-menu-item>

                <?php } ?>

                <md-menu-item>

                  <md-button aria-label="Add">

                    <a ng-href="<?php echo base_url('leads/forms') ?>">

                      <div layout="row" flex>

                        <p flex ng-bind="lang.webleads"></p>

                        <md-icon aria-label="Add Source" md-menu-align-target class="ion-earth text-muted" style="margin: auto 3px auto 0;"></md-icon>

                      </div>

                    </a>

                  </md-button>

                </md-menu-item>

              </md-menu-content>

            </md-menu>

          </div>

        </md-toolbar>

        <md-content class="md-pt-0" ng-cloak>

          <md-content ng-show="!leadsLoader" class="bg-white" ng-cloak>

            <md-table-container ng-show="leads.length > 0">

              <table md-table md-progress="promise">

                <thead md-head md-order="lead_list.order">

                  <tr md-row>

                    <th md-column><span>#</span></th>

                    <th md-column md-order-by="name"><span><?php echo lang2('name'); ?></span></th>

                    <th md-column md-order-by="phone"><span><?php echo lang2('phone'); ?></span></th>

                    <th md-column md-order-by="statusname"><span><?php echo lang2('status'); ?></span></th>

                    <th md-column md-order-by="sourcename"><span><?php echo lang2('source'); ?></span></th>

                    <th md-column md-order-by="staff"><span><?php echo lang2('staff'); ?></span></th>

                  </tr>

                </thead>

                <tbody md-body>

                  <tr class="select_row" md-row ng-repeat="lead in leads | orderBy: lead_list.order | limitTo: lead_list.limit : (lead_list.page -1) * lead_list.limit | filter: FilteredData" class="cursor" ng-click="goToLink('leads/lead/'+lead.id)">

                    <td md-cell>

                      <strong>

                        <a class="link" ng-href="<?php echo base_url('leads/lead/') ?>{{lead.id}}"> <span ng-bind="lead.lead_number"></span></a>

                      </strong><br>

                    </td>

                    <td md-cell>

                      <strong><span ng-bind="lead.name"></span></strong><br>

                      <small><span class="blur" ng-bind="lead.company"></span></small>

                    </td>

                    <td md-cell>

                      <strong><span ng-bind="lead.phone"></span></strong><br>

                      <small><span class="blur" ng-bind="lead.email"></span></small>

                    </td>

                    <td md-cell>

                      <strong><span class="badge" style="border-color: #fff;background-color: {{lead.color}};" ng-bind="lead.statusname"></span></strong>

                    </td>

                    <td md-cell>

                      <strong><span class="badge" ng-bind="lead.sourcename"></span></strong>

                    </td>

                    <td md-cell>

                      <div data-toggle="tooltip" data-placement="bottom" data-container="body" title="" data-original-title="Assigned: {{lead.assigned}}" class="assigned-staff-for-this-lead user-avatar"><img src="<?php echo base_url('uploads/images/') ?>{{lead.avatar}}" alt="{{lead.assigned}}"> </div>

                    </td>

                  </tr>

                </tbody>

              </table>

            </md-table-container>

            <md-table-pagination ng-show="leads.length > 0" md-limit="lead_list.limit" md-limit-options="limitOptions" md-page="lead_list.page" md-total="{{leads.length}}"></md-table-pagination>

            <md-content ng-show="!leads.length" class="md-padding no-item-data"><?php echo lang2('notdata') ?></md-content>

          </md-content>

        </md-content>

      </div>

    </div>

    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Create" ng-cloak style="width: 450px;">
      <md-toolbar class="toolbar-white">
        <div class="md-toolbar-tools">
          <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>
          <md-truncate><?php echo lang2('create') ?></md-truncate>
        </div>
      </md-toolbar>

      <md-content>
        <md-content layout-padding>
          <md-input-container class="md-block ">
            <label>Tipo de pessoa</label>
            <md-select required placeholder="" ng-model="lead.tp_pessoa" style="min-width: 200px;">
              <md-option ng-value="1">Pessoa física</md-option>
              <md-option ng-value="2">Pessoa jurídica</md-option>
            </md-select>
          </md-input-container>

          <div ng-show="lead.tp_pessoa != null" class="addLeadDiv">

            <md-input-container class="md-block" ng-if="lead.tp_pessoa == 1">
              <label><?php echo lang2('name'); ?></label>
              <input required name="name" ng-model="lead.name">
            </md-input-container>

            <md-input-container class="md-block" ng-if="lead.tp_pessoa == 2">
              <label><?php echo lang2('company'); ?></label>
              <input required ng-model="lead.company">
            </md-input-container>

            <md-input-container class="md-block" ng-if="lead.tp_pessoa == 2">
              <label>Cnpj</label>
              <input name="cnpj" ng-model="lead.cnpj">
            </md-input-container>

            <md-input-container class="md-block" ng-if="lead.tp_pessoa == 2">
              <label>Website</label>
              <input name="web_site" ng-model="lead.web_site">
            </md-input-container>

            <md-input-container class="md-block" ng-if="lead.tp_pessoa == 1">
              <label>Cpf</label>
              <input name="cpf" ng-model="lead.cpf">
            </md-input-container>

            <md-input-container class="md-block" ng-if="lead.tp_pessoa == 2">
              <label>Setor de atividade</label>
              <input name="setor_atividade" ng-model="lead.setor_atividade">
            </md-input-container>

            <md-input-container class="md-block" ng-if="lead.tp_pessoa == 1">
              <label>Data de nascimento</label>
              <input name="dt_nascimento" type="date" ng-model="lead.dt_nascimento">
            </md-input-container>

            <md-input-container class="md-block" ng-if="lead.tp_pessoa == 2">
              <label>Porte</label>
              <input name="porte" ng-model="lead.porte">
            </md-input-container>

            <md-content>
              <md-input-container class="md-block" flex="70" style="float: left;width: 100%;">
                <label>Telefone</label>
                <input name="phone" class="phone" ng-model="lead.phone">
              </md-input-container>

              <md-input-container class="md-block" flex="25" style="float: left;margin-top: 0;margin-left: 1em;width: 100%;">
                <md-switch ng-model="lead.is_whatsApp" aria-label="Recurring"> <label>WhatsApp</label></md-switch>
              </md-input-container>
            </md-content>

            <md-input-container class="md-block">
              <label><?php echo lang2('email'); ?></label>
              <input type="email" ng-model="lead.email" minlength="10" maxlength="100" ng-pattern="/^.+@.+\..+$/">
            </md-input-container>

            <md-input-container class="md-block" ng-if="lead.tp_pessoa == 1">
              <label>Instagram</label>
              <input name="instagram" ng-model="lead.instagram">
            </md-input-container>

            <md-input-container class="md-block" ng-if="lead.tp_pessoa == 1">
              <label>Facebook</label>
              <input name="facebook" ng-model="lead.facebook">
            </md-input-container>

            <md-input-container class="md-block" ng-if="lead.tp_pessoa == 1">
              <label>Linkedin</label>
              <input name="linkedin" ng-model="lead.linkedin">
            </md-input-container>

            <md-input-container class="md-block">
              <label><?php echo lang2('address') ?></label>
              <textarea ng-model="lead.address" md-maxlength="500" rows="3" md-select-on-focus></textarea>
            </md-input-container>

            <md-input-container class="md-block">
              <label><?php echo lang2('city'); ?></label>
              <input ng-model="lead.city">
            </md-input-container>

            <md-input-container class="md-block">
              <label><?php echo lang2('zip'); ?></label>
              <input ng-model="lead.zip">
            </md-input-container>

            <md-input-container class="md-block">
              <label><?php echo lang2('country'); ?></label>
              <md-select placeholder="<?php echo lang2('country'); ?>" ng-model="lead.country_id" ng-change="getStates(lead.country_id)" name="country_id" style="min-width: 200px;">
                <md-option ng-selected="$index == 31" ng-value="country.id" ng-repeat="country in countries">{{country.shortname}}</md-option>
              </md-select>
            </md-input-container>

            <md-input-container class="md-block" style="margin-top: 3em;">
              <label><?php echo lang2('state'); ?></label>
              <md-select placeholder="<?php echo lang2('states'); ?>" ng-model="lead.state_id" name="state_id" style="min-width: 200px;">
                <md-option ng-value="state.id" ng-repeat="state in states">{{state.state_name}}</md-option>
              </md-select>
            </md-input-container>


            <md-input-container class="md-block " style="margin-top: 3em;">
              <label>Lead funil</label>
              <md-select placeholder="" ng-model="lead.funil_list" style="min-width: 200px;">
                <md-option ng-value="list.id_list" ng-repeat="list in leadslist">{{list.nm_list}}</md-option>
              </md-select>
            </md-input-container>

            <md-input-container class="md-block" style="margin-top: 3em;">
              <label>Etapa do Funil</label>
              <md-select placeholder="Etapa do Funil" ng-model="lead.status_id" style="min-width: 200px;">
                <md-option ng-value="status.id" ng-repeat="status in leadslist[lead.funil_list].leadstatuses">{{status.name}}</md-option>
              </md-select>
            </md-input-container>

            <md-input-container class="md-block" style="margin-top: 3em;">
              <label>Temperatura</label>
              <md-select placeholder="Temperatura" ng-model="lead.temperatura" style="min-width: 200px;">
                <md-option ng-value="1">Quente</md-option>
                <md-option ng-value="2">Morno</md-option>
                <md-option ng-value="3">Frio</md-option>
              </md-select>
            </md-input-container>


            <md-input-container class="md-block" style="margin-top: 3em;">
              <label><?php echo lang2('assigned'); ?></label>
              <md-select placeholder="<?php echo lang2('choosestaff'); ?>" ng-model="lead.assigned_id" style="min-width: 200px;">
                <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
              </md-select>
            </md-input-container>

            <md-input-container class="md-block" style="margin-top: 3em;">
              <label><?php echo lang2('source'); ?></label>
              <md-select placeholder="<?php echo lang2('source'); ?>" ng-change="$('#date_contacted').focus()" ng-model="lead.source_id" style="min-width: 200px;">
                <md-option ng-value="source.id" ng-repeat="source in leadssources">{{source.name}}</md-option>
              </md-select>
            </md-input-container>

            <md-input-container class="md-block" style="margin-top: 3em;">
              <label><?php echo lang2('description') ?></label>
              <textarea ng-model="lead.description" md-maxlength="500" rows="3" md-select-on-focus></textarea>
            </md-input-container>

            <md-input-container class="md-block">
              <label><?php echo lang2('date_contacted') ?></label>
              <input mdc-datetime-picker="" date="true" time="true" type="text" id="date_contacted" click-outside-to-close="true" placeholder="<?php echo lang2('chooseadate') ?>" show-todays-date="" minutes="true" today-btn="false" show-icon="true" ng-model="lead.date_contacted" class=" dtp-no-msclear dtp-input md-input">
            </md-input-container>

            <md-chips ng-model="lead.tags" md-separator-keys="keys" placeholder="Lead Tags" secondary-placeholder="Seperate with comma."></md-chips> {{lead.tags}}

          </div>

        </md-content>

        <md-content layout-padding ng-show="lead.tp_pessoa != null">
          <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>
            <md-button ng-click="AddLead()" class="md-raised md-primary btn-report block-button" ng-disabled="saving == true" aria-label="Add">
              <span ng-hide="saving == true"><?php echo lang2('create'); ?></span>
              <md-progress-circular class="white" ng-show="saving == true" md-mode="indeterminate" md-diameter="20">
              </md-progress-circular>
            </md-button>
            <br /><br /><br /><br />
          </section>
        </md-content>

      </md-content>

    </md-sidenav>

    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="ContentFilter" ng-cloak style="width: 450px;">

      <md-toolbar class="md-theme-light" style="background:#262626">

        <div class="md-toolbar-tools">

          <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>

          <md-truncate><?php echo lang2('filter') ?></md-truncate>

        </div>

      </md-toolbar>

      <md-content layout-padding="">

        <div ng-repeat="(prop, ignoredValue) in leads[0]" ng-init="filter[prop]={}" ng-if="prop != 'name' && prop != 'id' && prop != 'company' && prop != 'phone' && prop != 'color' && prop != 'status' && prop != 'source' && prop != 'assigned' && prop != 'avatar' && prop != 'staff' && prop != 'createddate' && prop != 'statusname' && prop != 'sourcename' && prop != 'lead_number' && prop != 'email' && prop != 'tags' && prop != 'date_contacted' && prop != 'tagss' ">

          <div class="filter col-md-12">

            <h4 class="text-muted text-uppercase"><strong>{{prop}}</strong></h4>

            <hr>

            <div class="labelContainer" ng-repeat="opt in getOptionsFor(prop)">

              <md-checkbox id="{{[opt]}}" ng-model="filter[prop][opt]" aria-label="{{opt}}"><span class="text-uppercase">{{opt}}</span></md-checkbox>

            </div>

          </div>

        </div>

      </md-content>

    </md-sidenav>

    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="LeadsSettings" ng-cloak style="width: 450px;">
      <md-toolbar class="toolbar-white" style="background:#262626">
        <div class="md-toolbar-tools">
          <md-button ng-click="close()" class="md-icon-button" aria-label="Close"><i class="ion-android-arrow-forward"></i></md-button>
          <md-truncate><?php echo lang2('settings') ?></md-truncate>

          <md-button aria-label="Add Status" class="md-icon-button" ng-click="NewFunil()">
            <md-tooltip md-direction="top">Adicionar novo funil</md-tooltip>
            <md-icon aria-label="Add Source"><i class="ion-plus-round text-success"></i></md-icon>
          </md-button>
        </div>
      </md-toolbar>

      <md-content>
        <div ng-repeat="list in leadslist">
          <md-toolbar class="toolbar-white" style="background:#262626">
            <div class="md-toolbar-tools">
              <h4 class="text-bold text-muted" flex ng-click="EditFunil(list.id_list,list.nm_list, $event)">{{list.nm_list}}</h4>
              <?php if (check_privilege('leads', 'edit')) { ?>
                <md-button aria-label="Converted Lead Status" class="md-icon-button" ng-click="ConvertedStatus()">
                  <md-tooltip md-direction="top"><?php echo lang2('converted_lead_status') ?></md-tooltip>
                  <md-icon aria-label="Add Source"><i class="mdi mdi-refresh-sync text-success"></i></md-icon>
                </md-button>
              <?php }
              if (check_privilege('leads', 'create')) { ?>
                <md-button aria-label="Add Status" class="md-icon-button" ng-click="NewStatus(list.id_list)">
                  <md-tooltip md-direction="top"><?php echo lang2('addstatus') ?></md-tooltip>
                  <md-icon aria-label="Add Source"><i class="ion-plus-round text-success"></i></md-icon>
                </md-button>
              <?php }
              if (check_privilege('leads', 'delete')) { ?>
                <md-button aria-label="Remove Funil" class="md-icon-button" ng-click='DeleteLeadFunil(list.id_list)'>
                  <md-tooltip md-direction="top"><?php echo lang2('delete') ?></md-tooltip>
                  <md-icon aria-label="Remove Funil"><i class="fas fa-trash" style="color: #777;font-size: 20px;"></i></md-icon>
                </md-button>
              <?php } ?>
            </div>
          </md-toolbar>

          <ul ui-sortable="sortableOptions" ui-sortable ng-model="list.leadstatuses" style="padding-left: 0px;">
            <li style="padding: 15px 10px;" ng-repeat="status in list.leadstatuses | orderBy: status.ordem" class="noright noright2" aria-label="Edit Status">
              <i class="fas fa-sort" style="margin-right: 10px;color: #9b9b9b;"></i>
              <strong ng-click="EditStatus(status.id,status.name, $event)" ng-bind="status.name"></strong>
              <?php if (check_privilege('leads', 'delete')) { ?>
                <md-icon ng-click='DeleteLeadStatus(status.id)' style="float: right;" aria-label="Remove Status" class="md-secondary md-hue-3 ion-trash-b">
                  <md-tooltip md-direction="top"><?php echo lang2('delete') ?></md-tooltip>
                </md-icon>
              <?php } ?>
            </li>
          </ul>

        </div>


        <md-toolbar class="toolbar-white" style="background:#262626">

          <div class="md-toolbar-tools">
            <h4 class="text-bold text-muted" flex><?php echo lang2('leadssources') ?></h4>
            <?php if (check_privilege('leads', 'create')) { ?>
              <md-button aria-label="Add Source" class="md-icon-button" ng-click="NewSource()">
                <md-tooltip md-direction="top"><?php echo lang2('addsource') ?></md-tooltip>
                <md-icon aria-label="Add Source"><i class="ion-plus-round text-success"></i></md-icon>
              </md-button>
            <?php } ?>
          </div>
        </md-toolbar>

        <md-list-item ng-repeat="source in leadssources" class="noright" aria-label="Edit Source">
          <strong ng-click="EditSource(source.id,source.name, $event)" ng-bind="source.name"></strong>
          <?php if (check_privilege('leads', 'delete')) { ?>
            <md-icon style="z-index: 9999999;" ng-click='DeleteLeadSource($index)' aria-label="Remove Source" class="md-secondary md-hue-3 ion-trash-b">
              <md-tooltip md-direction="top"><?php echo lang2('delete') ?></md-tooltip>
            </md-icon>
          <?php } ?>
        </md-list-item>

      </md-content>

    </md-sidenav>

    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Import" ng-cloak style="width: 450px;">
      <md-toolbar class="md-theme-light" style="background:#262626">
        <div class="md-toolbar-tools">
          <md-button ng-click="close()" class="md-icon-button" aria-label="Close"><i class="ion-android-arrow-forward"></i></md-button>
          <md-truncate><?php echo lang2('importleads') ?></md-truncate>
        </div>
      </md-toolbar>
      <md-content> <?php echo form_open_multipart('leads/import'); ?>

        <div class="modal-body">
          <div class="form-group">
            <label for="name"> <?php echo lang2('choosecsvfile'); ?> </label>
            <div class="file-upload">
              <div class="file-select">
                <div class="file-select-button" id="fileName"><span class="mdi mdi-accounts-list-alt"></span>
                  <?php echo lang2('attachment') ?> </div>
                <div class="file-select-name" id="noFile"> <?php echo lang2('notchoise') ?> </div>
                <input type="file" name="userfile" id="chooseFile" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
              </div>
            </div>
          </div>

          <br>
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
              <md-option ng-value="source.id" ng-repeat="source in leadssources">{{source.name}}</md-option>
            </md-select>
          </md-input-container>

          <br>
        </div>

        <div class="modal-footer"> <a href="<?php echo base_url('uploads/samples/leadimport_new.csv') ?>" class="btn btn-success pull-left"><?php echo lang2('downloadsample'); ?></a>
          <button type="submit" class="btn btn-default"><?php echo lang2('save'); ?></button>
        </div>

        <?php echo form_close(); ?>

      </md-content>
    </md-sidenav>

    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="SelectsFunil" ng-cloak style="width: 450px;">
      <md-toolbar class="md-theme-light" style="background:#262626">
        <div class="md-toolbar-tools">
          <md-button ng-click="close()" class="md-icon-button" aria-label="Close"><i class="ion-android-arrow-forward"></i></md-button>
          <md-truncate>Alterar para o funil</md-truncate>
        </div>
      </md-toolbar>
      <md-content>
        <md-input-container class="md-block " style="margin-top: 3em;">
          <label>Lead funil</label>
          <md-select placeholder="" ng-model="selectsFunil.funil_list" style="min-width: 200px;">
            <md-option ng-value="list.id_list" ng-repeat="list in leadslist">{{list.nm_list}}</md-option>
          </md-select>
        </md-input-container>

        <md-input-container class="md-block" style="margin-top: 3em;">
          <label>Etapa do Funil</label>
          <md-select placeholder="Etapa do Funil" ng-model="selectsFunil.status_id" style="min-width: 200px;">
            <md-option ng-value="status.id" ng-repeat="status in leadslist[selectsFunil.funil_list].leadstatuses">{{status.name}}</md-option>
          </md-select>
        </md-input-container>

        <div class="modal-footer">
          <button ng-click="saveSelectsFunil()" class="btn btn-success"><?php echo lang2('save'); ?></button>
        </div>


      </md-content>
    </md-sidenav>


    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="SelectsFunc" ng-cloak style="width: 450px;">
      <md-toolbar class="md-theme-light" style="background:#262626">
        <div class="md-toolbar-tools">
          <md-button ng-click="close()" class="md-icon-button" aria-label="Close"><i class="ion-android-arrow-forward"></i></md-button>
          <md-truncate>Alterar funcionário</md-truncate>
        </div>
      </md-toolbar>
      <md-content>

        <md-input-container class="md-block" style="margin-top: 3em;">
          <label><?php echo lang2('assigned'); ?></label>
          <md-select placeholder="<?php echo lang2('choosestaff'); ?>" ng-model="selectsFunc.assigned_id" style="min-width: 200px;">
            <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
          </md-select>
        </md-input-container>


        <div class="modal-footer">
          <button ng-click="saveSelectsFunc()" class="btn btn-success"><?php echo lang2('save'); ?></button>
        </div>
      </md-content>
    </md-sidenav>


    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="SelectsInativar" ng-cloak style="width: 450px;">
      <md-toolbar class="md-theme-light" style="background:#262626">
        <div class="md-toolbar-tools">
          <md-button ng-click="close()" class="md-icon-button" aria-label="Close"><i class="ion-android-arrow-forward"></i></md-button>
          <md-truncate>Desativar leads</md-truncate>
        </div>
      </md-toolbar>
      <md-content>

        <md-input-container class="md-block" style="margin-top: 3em;">
          <label><?php echo lang2('Motivo'); ?></label>
          <textarea ng-model="selectsInativar.motivo" md-maxlength="500" rows="3" md-select-on-focus></textarea>
        </md-input-container>


        <div class="modal-footer">
          <button ng-click="saveSelectsInativar()" class="btn btn-success"><?php echo lang2('save'); ?></button>
        </div>
      </md-content>
    </md-sidenav>



    <div class="modal fade" id="SelectsWhatsApp" style="z-index: 2000;">
      <div class="modal-dialog mdAngular">
        <div class="modal-content">
          <div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
            <button type="button" class="close" ng-click="dimiss()">
              <span aria-hidden="true">×</span> <span class="sr-only"><?php echo lang2('close') ?></span>
            </button>
            <b> Enviar whatsapp</b>
          </div>
          <div id="modalBody" class="modal-body" style="max-height: 70vh; overflow: auto; overflow-x: hidden;">

            <small><b>Variáveis:</b>
              <a href="javaScript:void(0)" ng-click="addVar('{email}')">{email}</a> ,
              <a href="javaScript:void(0)" ng-click="addVar('{numero}')">{numero}</a> ,
              <a href="javaScript:void(0)" ng-click="addVar('{empresa}')">{empresa}</a>,
              <a href="javaScript:void(0)" ng-click="addVar('{nome}')">{nome}</a>,
            </small>

            <div class="md-block">
              <label class="btn btn-success upload-btn">
                <i class="fas fa-paperclip"></i> Enviar mídia
                <input type="file" accept="image/*,video/*,application/pdf" ng-model="SelectsWhatsApp.file" onchange="angular.element(this).scope().SelectsWhatsApp.file = this.files[0]">
              </label>
            </div>

            <md-input-container class="md-block" style="margin-top: 3em;">
              <label><?php echo lang2('Mensagem'); ?></label>
              <textarea ng-model="SelectsWhatsApp.mensagem" md-maxlength="500" rows="3" md-select-on-focus></textarea>
            </md-input-container>

            <div class="modal-footer" style="margin: 0;border: 0;">
              <button type="button" class="btn btn-secondary" ng-click="dimiss()">Cancelar</button>
              <button ng-if="!loadEnvioWhatsapp" type="button" class="btn btn-success" ng-click="saveSelectsWhatsApp()">Enviar
                <i class="fab fa-whatsapp" style="font-size: 17px !important;vertical-align: middle;margin-left: 5px;"></i>
              </button>

              <button ng-if="loadEnvioWhatsapp" type="button" class="btn btn-success">Enviando...
                <i class="fas fa-spinner" style="font-size: 17px !important;vertical-align: middle;margin-left: 5px;"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>


    <div class="modal fade" id="SelectSalesFlow" style="z-index: 2000;">
      <div class="modal-dialog mdAngular" style="margin-top: 0;">
        <div class="modal-content" style="max-height: calc(100% - 75px);">
          <div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
            <button type="button" class="close" ng-click="dimiss()">
              <span aria-hidden="true">×</span> <span class="sr-only"><?php echo lang2('close') ?></span>
            </button>
            <b>Alterar sales flow para</b>
          </div>
          <div id="modalBody" class="modal-body" style="max-height: 70vh; overflow: auto; overflow-x: hidden;">
            <div class="row">
              <div class="col-md-6 col-12">
                <md-input-container class="md-block">
                  <label style="margin-bottom: 0;">Sales flow</label>
                  <md-select ng-model="fluxos_manual.sales_flow" class="form-control select-modal">
                    <md-option ng-repeat="fluxo in fluxos_manual.fluxos" ng-value="fluxo.id_fluxo">{{fluxo.name}}</md-option>
                  </md-select>
                </md-input-container>
              </div>
              <div class="col-12" style="padding-left: 15px;">
                <small ng-if="fluxos_manual.fluxos.length == 0">Nenhum sales flow manual para <b>Selecionar</b>. <a target="_blank" href="<?php echo base_url('sales_flow') ?>">Criar sales flow</a></small>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" ng-click="dimiss()">Fechar</button>
            <button type="button" class="btn btn-success" ng-click="AlterarSalesFlow()">Alterar</button>
          </div>

        </div>
      </div>
    </div>


    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="SelectsEmailAut" ng-cloak style="width: 450px;">
      <md-toolbar class="md-theme-light" style="background:#262626">
        <div class="md-toolbar-tools">
          <md-button ng-click="close()" class="md-icon-button" aria-label="Close"><i class="ion-android-arrow-forward"></i></md-button>
          <md-truncate>Emails automatizados</md-truncate>
        </div>
      </md-toolbar>
      <md-content>

        <md-table-container ng-show="emailsAut.length > 0">
          <table md-table style="width: 100%;border-top: 0;">
            <thead md-head>
              <tr md-row>
                <th md-column><span><?php echo lang2('name'); ?></span></th>
                <th md-column><span>Ativo</span></th>
              </tr>
            </thead>
            <tbody md-body>
              <tr class="select_row" md-row ng-repeat="taskAut in emailsAut " class="cursor">
                <td md-cell>
                  <strong><span ng-bind="taskAut.name_taskAut"></span></strong>
                </td>

                <td md-cell>
                  <md-switch ng-model="adicionadoTask[taskAut.id_task]" ng-change="UpdateAdicionadoTask(taskAut.id_task)" aria-label="Status" ng-cloak><strong class="text-muted">Ativo</strong></md-switch>
                </td>
              </tr>
            </tbody>
          </table>
        </md-table-container>

      </md-content>
    </md-sidenav>

    <script type="text/ng-template" id="converted-status-template.html">

      <md-dialog aria-label="options dialog">

        <md-dialog-content layout-padding>

        <label>Lead funil</label>
        <md-select placeholder="" ng-model="ConvertedLeadFunil" style="min-width: 200px;" required>
            <md-option ng-value="list.id_list" ng-repeat="list in leadslist">{{list.nm_list}}</md-option>
        </md-select>


        <h2 class="md-title"><?php echo lang2('converted_lead_status'); ?> zddxfsdfsd</h2>
          <md-select required ng-model="ConvertedLeadStatus" style="min-width: 200px;" aria-label="AddMember">
            <md-option ng-value="status.id" ng-repeat="status in leadslist[ConvertedLeadFunil].leadstatuses">{{status.name}}</md-option>
          </md-select>
        </md-dialog-content>

        <md-dialog-actions>

          <span flex></span>

          <md-button ng-click="close()" aria-label="Add"><?php echo lang2('cancel') ?>!</md-button>

          <md-button ng-click="MakeConvertedLedStatus()" aria-label="Add"><?php echo lang2('update') ?>!</md-button>

        </md-dialog-actions>

      </md-dialog>

    </script>

  </div>

</div>

<script>
  var MSG_TITLE = '<?php echo lang2('attention') ?>',
    MSG_REMOVE = '<?php echo lang2('converted_lead_remove_msg') ?>',
    MSG_CANCEL = '<?php echo lang2('cancel') ?>',
    MSG_OK = '<?php echo lang2('yes') ?>';

  $(document).ready(function() {
    $(".collapseFiltro").click(function() {
      $('#collapseFiltro').toggle(150);
    });
  });
</script>
<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>
<script src="<?php echo base_url('assets/lib/chartjs/dist/Chart.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/leads.js?v=l13.0.120'); ?>"></script>