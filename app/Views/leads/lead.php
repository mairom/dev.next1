<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<?php
$appconfig = get_appconfig();

$privileges_Model = new App\Models\Privileges_Model();
?>

<div class="ciuis-body-content" ng-controller="Lead_Controller">

  <style type="text/css">
    md-input-container {
      height: 50px;
      float: left;
      width: 100%;
    }

    md-content {
      width: 100%;
    }

    .menuOpen .main-content {
      width: calc(75% - 50px);
    }

    @media (max-width: 1200px) {
      .saudacao {
        display: none;
      }
    }

    .btn_info {
      font-size: 11px;
      color: #15667d;
      cursor: pointer;
    }

    .image-container {
      text-align: center;
    }

    .image-container img {
      max-width: 100%;
      height: auto;
    }

    .image-controls {
      display: flex;
      justify-content: center;
      flex: 1;
    }

    .control-btn {
      background-color: #3498db;
      border: none;
      color: white;
      padding: 5px 10px;
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

    .control-btn i {
      font-size: 15px !important;
    }

    .btn-reuniao {
      background: #1ab620;
      color: #fff;
      margin-top: 10px;
      margin-left: 10px;
      padding: 5px 15px;
      font-size: 11px;
    }

    .btn-reuniao i {
      color: white;
      font-size: 16px;
      vertical-align: super;
      margin-top: 3px;
    }

    .infosAdd .flt_left {
      padding: 10px;
    }

    .infosAdd .flt_left p {
      margin-bottom: 0;
      color: #ce9905;
    }

    .infosAdd .flt_left .nmInfo {
      color: #2d2d2d;
      font-weight: 500;
      font-size: 14px;
      margin-top: 5px;
    }

    .row {
      float: left;
      width: 100%;
      margin-left: 0;
      margin-right: 0;
    }

    .h5Title2 {
      margin: 0;
      margin-bottom: 10px;
      padding: 10px;
      float: left;
      width: 100%;
      font-size: 17px;
    }
  </style>

  <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-9">
    <md-toolbar class="toolbar-white">
      <div class="md-toolbar-tools">

        <a class="md-icon-button" href="<?= base_url('leads') ?>" style="margin-right: 1em;">
          <i class="fas fa-arrow-left" style="color: #5c5c5c;font-size: 22px;"></i>
        </a>

        <md-button class="md-icon-button" aria-label="Settings" ng-disabled="true" style="background: #5c5c5c;">
          <md-icon ng-if="lead.tp_pessoa == 2" style="margin-top: -8px;"><i class="far fa-building" style="color: #ffffff;font-size: 22px;"></i></md-icon>
          <md-icon ng-if="lead.tp_pessoa == 1" style="margin-top: -8px;"><i class="fas fa-user-tie" style="color: #ffffff;font-size: 22px;"></i></md-icon>
        </md-button>

        <!-- <h2 ng-bind="lead.lead_number" class="lead_name"></h2>&nbsp; -->

        <h2 flex md-truncate ng-bind="lead.name != '' ? lead.name : lead.company" class="lead_name"></h2>

        <div class="image-controls" ng-if="selectedIndex == 1">
          <button style="border: 0;background: none;padding: 0;" ng-click="stopLead()">
            <img style="width: 35px;" src="<?= base_url('assets/img/stop-button-red-icon.svg') ?>">
          </button>
          <button style="border: 0;background: none;padding: 0;margin-left: 5px;" ng-click="togglePlayPause()">
            <img ng-if="!isPlaying" style="width: 35px;" src="<?= base_url('assets/img/pause-button-red-icon.svg') ?>">
            <img ng-if="isPlaying" style="width: 35px;" src="<?= base_url('assets/img/play-button-red-icon.svg') ?>">
          </button>
          <button style="border: 0;background: none;padding: 0;margin-left: 5px;" ng-click="nextLead()">
            <img style="width: 35px;" src="<?= base_url('assets/img/forward-button-red-icon.svg') ?>">
          </button>
        </div>


        <?php if (check_privilege('customers', 'create')) { ?>
          <!--
          <md-button ng-show="ONLYADMIN == 'true' || lead.assigned_id == user.id" ng-if="!lead.dateconverted" ng-click="Convert()" class="md-icon-button" aria-label="Convert" ng-cloak>
            <md-tooltip md-direction="bottom"><?php echo lang2('convert') ?></md-tooltip>
            <md-icon><i class="ion-loop text-success"></i></md-icon>
          </md-button>
              -->
        <?php } ?>

        <md-button ng-show="lead.lost == '1'" class="md-icon-button mark-lost" aria-label="Lost" ng-cloak>

          <md-tooltip md-direction="bottom"><?php echo lang2('lost') ?></md-tooltip>

          <md-icon><i class="text-black"><img style="width: 35px;text-align: center;margin-left: -8px;margin-top: -2px;" src='<?= base_url('assets/img/menu/Marcar_como_perdido.svg') ?>'></i></md-icon>

        </md-button>

        <md-button ng-show="lead.junk == '1'" class="md-icon-button mark-junk" aria-label="Junk" ng-cloak>
          <md-tooltip md-direction="bottom"><?php echo lang2('junk') ?></md-tooltip>
          <md-icon><i class="text-black"><img style="width: 35px;text-align: center;margin-left: -8px;margin-top: -2px;" src='<?= base_url('assets/img/menu/Marcar_como_perdido.svg') ?>'></i></md-icon>
        </md-button>


        <md-button ng-if="lead.dateconverted" class="md-icon-button" aria-label="Converted" ng-cloak>
          <md-tooltip md-direction="bottom"><?php echo lang2('converted') ?></md-tooltip>
          <md-icon><i class="ion-trophy text-success"></i></md-icon>
        </md-button>



        <?php if (check_privilege('leads', 'edit') || check_privilege('leads', 'delete')) { ?>
          <!--
          <md-menu md-position-mode="target-right target" ng-cloak>
            <md-button aria-label="Open demo menu" class="md-icon-button" ng-click="$mdMenu.open($event)">
              <md-icon><i class="ion-android-more-vertical text-muted"></i></md-icon>
            </md-button>

            <md-menu-content width="4">
        -->


          <?php
          if (check_privilege('leads', 'edit')) { ?>

            <md-button ng-click="showReuniao()" style="padding: 0 10px; min-width: 40px; width: 40px; min-height: 40px;height: 40px;">
              <md-tooltip md-direction="bottom">Agendar reunião</md-tooltip>
              <md-icon md-menu-align-target style="margin: auto 3px auto 0;">
                <img style="width: 28px;text-align: center; margin-left: -6px; margin-top: -7px;" src='<?= base_url('assets/img/icons/add_reuniao.png') ?>'>
              </md-icon>
            </md-button>


            <md-button ng-click="Update(lead)" aria-label="update" style="padding: 0 10px; min-width: 40px; width: 40px; min-height: 40px;height: 40px;">
              <md-tooltip md-direction="bottom"><?php echo lang2('update') ?></md-tooltip>
              <md-icon md-menu-align-target style="margin: auto 3px auto 0;">
                <img style="width: 35px;text-align: center;margin-left: -8px;margin-top: -10px;" src='<?= base_url('assets/img/menu/Editar_Lead.svg') ?>'>
              </md-icon>
            </md-button>

          <?php }

          if (check_privilege('customers', 'create')) { ?>
            <md-button ng-show="ONLYADMIN == 'true' || lead.assigned_id == user.id" ng-if="!lead.dateconverted" ng-click="Convert()" aria-label="update" style="padding: 0 10px; min-width: 40px; width: 40px; min-height: 40px;height: 40px;">
              <md-tooltip md-direction="bottom">Converter para cliente</md-tooltip>
              <!--<div layout="row" flex>
       <p flex>Converter para cliente</p> -->
              <md-icon style="margin: auto 3px auto 0;">
                <img style="width: 35px;text-align: center;margin-left: -8px;margin-top: -10px;" src='<?= base_url('assets/img/menu/Converter_para_cliente.svg') ?>'>
              </md-icon>
              <!--</div>-->
            </md-button>

          <?php }

          if (check_privilege('leads', 'edit')) { ?>

            <md-button ng-show="lead.lost == '0'" ng-click="OpenMarkLeadAs(1);" aria-label="Not Started" style="padding: 0 10px; min-width: 40px; width: 40px; min-height: 40px;height: 40px;">
              <md-tooltip md-direction="bottom"><?php echo lang2('markleadaslost') ?></md-tooltip>
              <div layout="row" flex>
                <!--<p flex ng-bind="lang.markleadaslost"></p>-->
                <md-icon md-menu-align-target class="text-black" style="margin: auto 3px auto 0;">
                  <img style="width: 35px;text-align: center;margin-left: -8px;margin-top: -4px;" src='<?= base_url('assets/img/menu/Marcar_como_perdido.svg') ?>'>
                </md-icon>
              </div>
            </md-button>


            <md-button ng-show="lead.lost != '0'" ng-click="MarkLeadAs(2);" aria-label="Started" style="padding: 0 10px; min-width: 40px; width: 40px; min-height: 40px;height: 40px;">
              <md-tooltip md-direction="bottom"><?php echo lang2('unmarkleadaslost') ?></md-tooltip>
              <!--<div layout="row" flex>
                  <p flex ng-bind="lang.unmarkleadaslost"></p>-->
              <md-icon md-menu-align-target class="text-muted" style="margin: auto 3px auto 0;">
                <img style="width: 35px;text-align: center;margin-left: -8px;margin-top: -4px;" src='<?= base_url('assets/img/menu/Marcar_como_perdido.svg') ?>'>
              </md-icon>
              <!--</div>-->
            </md-button>

            <!--
                <md-menu-item ng-show="lead.junk == '0'">
                  <md-button ng-click="MarkLeadAs(3)" aria-label="Percentage">
                    <div layout="row" flex>
                      <p flex ng-bind="lang.markleadasjunk"></p>
                      <md-icon md-menu-align-target class="ion-sad-outline text-warning" style="margin: auto 3px auto 0;"></md-icon>
                    </div>
                  </md-button>
                </md-menu-item>
                <md-menu-item ng-show="lead.junk != '0'">
                  <md-button ng-click="MarkLeadAs(4)" aria-label="Cancelled">
                    <div layout="row" flex>
                      <p flex ng-bind="lang.unmarkleadasjunk"></p>
                      <md-icon md-menu-align-target class="ion-sad-outline text-muted" style="margin: auto 3px auto 0;"></md-icon>
                    </div>
                  </md-button>
                </md-menu-item>
              -->

          <?php }

          if (check_privilege('leads', 'delete')) { ?>

            <md-button ng-show="user_logado.admin == '1'" ng-if="!lead.dateconverted" ng-click="Delete()" aria-label="Delete" style="padding: 0 10px; min-width: 40px; width: 40px; min-height: 40px;height: 40px;">
              <md-tooltip md-direction="bottom"><?php echo lang2('delete') ?></md-tooltip>
              <!--<div layout="row" flex>
    <div layout="row" flex>
    <p flex ng-bind="lang.delete"></p>-->
              <md-icon md-menu-align-target style="margin: auto 3px auto 0;">
                <img style="width: 35px;text-align: center;margin-left: -8px;margin-top: -10px;" src='<?= base_url('assets/img/menu/Remover_Lead.svg') ?>'>
              </md-icon>
              <!--</div>-->
            </md-button>

          <?php }
          ?>

          <!--
            </md-menu-content>

          </md-menu>
              -->

        <?php } ?>

      </div>

    </md-toolbar>

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

    <md-content ng-show="!leadsLoader" class="bg-white" ng-cloak>
      <md-tabs md-dynamic-height md-border-bottom md-selected="selectedIndex">
        <md-tab label="TIMELINE">
          <md-content class="md-padding bg-white" style="margin-top: 20px;">
            <md-content class="bg-white" flex="100" style="margin-bottom: 10px;">
              <div class="col" style="margin-left: 20px;float: left;margin-top: 10px;">
                <p style="margin-bottom: 5px;">Qualificação</p>
                <button ng-click="alterarQualificacao(4)" type="button" style="{{ lead.qualificacao == '4' ? 'background:#796eed; color:#fff;border-color: #796eed;' : ''}}" class="btn btn-outline-light">Muito bom</button>
                <button ng-click="alterarQualificacao(3)" type="button" style="{{ lead.qualificacao == '3' ? 'background:#26c281; color:#fff;border-color: #26c281;' : ''}}" class="btn btn-outline-light">Bom</button>
                <button ng-click="alterarQualificacao(2)" type="button" style="{{ lead.qualificacao == '2' ? 'background:#ffb000; color:#fff;border-color: #ffb000;' : ''}}" class="btn btn-outline-light">Médio</button>
                <button ng-click="alterarQualificacao(1)" type="button" style="{{ lead.qualificacao == '1' ? 'background:#ff0000; color:#fff;border-color: #ff0000;' : ''}}" class="btn btn-outline-light">Ruim</button>
              </div>

              <div class="col" style="margin-left: 20px;float: left;margin-top: 10px;">
                <p style="margin-bottom: 5px;">TEMPERATURA</p>
                <button ng-click="alterarTemperatura(3)" type="button" style="{{ lead.temperatura == '3' ? 'background:#796eed; color:#fff;border-color: #796eed;' : ''}}" class="btn btn-outline-light">Frio</button>
                <button ng-click="alterarTemperatura(2)" type="button" style="{{ lead.temperatura == '2' ? 'background:#ffb000; color:#fff;border-color: #ffb000;' : ''}}" class="btn btn-outline-light">Morno</button>
                <button ng-click="alterarTemperatura(1)" type="button" style="{{ lead.temperatura == '1' ? 'background:#ff0000; color:#fff;border-color: #ff0000;' : ''}}" class="btn btn-outline-light">Quente</button>
              </div>



              <div class="col" style="margin-left: 20px;float: left;margin-top: 10px;">
                <p style="margin-bottom: 5px;">TAGS <i style="cursor: pointer;color: #1bcf18;" data-toggle="modal" data-target="#modalAddTags" class="fas fa-plus-circle"></i></p>
                <button ng-repeat="(i, tag) in lead.tags" type="button" style="margin-left: 5px;" class="btn btn-outline-light w3-theme-{{i}}" ng-bind="tag"></button>
              </div>
            </md-content>


            <section class="ciuis-notes show-notes">
              <div style="overflow-y: hidden;width: 100%;overflow-x: auto;display: inline-flex;padding-bottom: 5px;">
                <md-list ng-click="alterarStatusLead(lead_status.id)" class="ciuis_lead_status_card" style="width: 175px;position: relative;padding-bottom: 0px;max-width: 175px;" flex ng-repeat="lead_status in lead.status_list">
                  <div class="div-title-funil" style="min-height: 45px;width: 150px;">

                    <md-toolbar class="toolbar-white md-toolbar-funil">
                      <div class="md-toolbar-tools toolbarPerson" style="padding-bottom: 5px;
    padding-top: 5px;
    border-radius: 5px;
    height: 50px !important;
    cursor:pointer;
    border-left: 3px solid #28B8DA;" ng-class="{'statusSelect':lead_status.id == lead.status_id}">
                        <h3 flex md-truncate>{{lead_status.name}}</h3>
                      </div>
                    </md-toolbar>
                  </div>
                </md-list>
              </div>

              <button type="button" class="btn btn-success btn_add_custom" style="float: right;margin-top: 1em;" ng-click="showDialogAddAtividade()">
                <span style="vertical-align: middle;">Adicionar</span>
                <md-icon><i style="color:white" class="ion-android-add-circle"></i></md-icon>
              </button>

              <a href="{{lead.instagram}}" class="btn btn-success btn_icon" target="_blank" ng-if="lead.instagram.length > 0">
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
            </section>


            <div ng-repeat="atividade in lead.list_atividades" class="atv_cont">

              <img src="{{base_url + atividade.atv_ft}}" class="atv_img">
              <div class="atv_div_50">
                <div class="atv_title" style="padding:0;">
                  <h4 style="margin-left: 0.5em;margin-top: 8px;">{{atividade.nm_atividade_select}} </h4>

                  <md-button ng-if="atividade.email_enviado != null" style="margin-top: 5px;" aria-label="Open demo menu" class="md-icon-button btn_atv" ng-click="reply(atividade)">
                    <md-icon style="font-size: 17px;margin-top: 5px;color: #fff;" aria-label="Add Source"><i class="fas fa-reply"></i></md-icon>
                  </md-button>

                  <md-button ng-if="atividade.email_enviado == null" style="margin-top: 5px;" aria-label="Open demo menu" class="md-icon-button btn_atv" ng-click="geraRespostaIaAtividade(atividade)">
                    <md-icon style="font-size: 17px;margin-top: 5px;color: #fff;" aria-label="Add Source"><i class="fas fa-robot"></i></md-icon>
                  </md-button>


                  <md-button style="margin-top: 5px;" aria-label="Open demo menu" class="md-icon-button btn_atv" ng-click="showDialogAddAtividade(atividade)">
                    <md-icon aria-label="Add Source"><i class="ion-android-more-vertical text-muted"></i></md-icon>
                  </md-button>

                </div>

                <div class="atv_anotacoes">
                  <p style="white-space: pre-line;" ng-bind-html="atividade.anotacoes | trustAsHtml"></p>
                </div>

                <div>
                  <small>by <b>{{atividade.staffname}}</b> at {{converteData(atividade.dt_entrada)}}</small>
                  <small ng-if="atividade.retorno != '0000-00-00'" class="atv_data">Agendado para:
                    {{converteData(atividade.retorno)}}</small>
                  <small ng-if="atividade.retorno == '0000-00-00'" class="atv_data">Sem retorno agendado</small>
                </div>
              </div>
            </div>

            <section class="md-pb-30">
            </section>
          </md-content>
        </md-tab>

        <?php if ($privileges_Model->has_privilege('sales_flow')) { ?>
          <md-tab label="Sales flow" ng-click="get_fluxo()">
            <div class="col-md-12 col-12" style="margin-top: 20px;overflow-y: auto !important;">
              
              <!-- Switch para ativar/desativar Sales Flow Automático -->
              <div style="padding: 15px; background: #f5f5f5; margin-bottom: 20px; border-radius: 5px;" ng-if="fluxo_lead != null">
                <md-switch ng-model="salesFlowAtivo" ng-change="UpdateSalesFlowAtivo(salesFlowAtivo)" aria-label="Sales Flow Automático" ng-cloak>
                  <strong>Sales Flow Automático</strong>
                  <span class="text-muted" style="font-size: 12px; display: block;">
                    {{salesFlowAtivo ? 'Ativado - O cron enviará emails automaticamente' : 'Desativado - Emails não serão enviados automaticamente'}}
                  </span>
                </md-switch>
              </div>

              <div class="ctt-fluxo" ng-if="fluxo_lead != null">
                <div class="fluxo">
                  <div class="element-pai elementF{{$index}}" style="width: 100%;float: left;min-width: max-content;margin-top: {{getHeight($index)}}px;" ng-if="($index + 1) % 2 !== 0" ng-repeat="(key, etapa) in fluxo_lead.etapas">
                    <div style="display: flex; flex-direction: row-reverse;">
                      <div>
                        <div class="element" style="float: right;">
                          <div class="icon-element">
                            <button type="button" class="btn_edit6" style="background: transparent;">
                              <i ng-if="etapa.feito != '1'" style="color: #979797;font-size: 20px;" class="far fa-check-circle"></i>
                              <i ng-if="etapa.feito == '1'" style="font-size: 20px;" class="fas fa-check-circle"></i>

                              <img ng-if="(fluxo_lead.etapas[key - 1] == null || fluxo_lead.etapas[key - 1].feito == '1' ) && etapa.feito != '1'" style="width: 40px;position: absolute;right: 22px;top: 40px;" src="<?= base_url('assets/img/click-sales-flow2.gif') ?>">

                            </button>

                            <p class="nm_atividade-flow">
                              <i class="fa fa-info-circle btn_info" title="Ver instruções" ng-click="openInfo(etapa.descricao)"></i> {{etapa.atividade.nm_atividade_select}}
                            </p>
                            <div style="background-image: url('{{base_url + etapa.atividade.atv_ft}}');" ng-click="showDialogAddAtividade(null, etapa)"></div>
                          </div>
                          <div class="element-line">
                            <span class="inf-num">{{$index + 1}}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div style="display: flex;">
                      <div style="display: flex; flex-direction: row-reverse;" class='element_vert' my-directive></div>

                    </div>
                  </div>
                </div>

                <div class="fluxo-line">
                  <h3 class="title-fluxo">{{fluxo.name}}</h3>
                </div>

                <div class="fluxo-r">
                  <div class="element-pai elementF{{$index}}" style="margin-top: {{getHeight($index)}}px;" ng-if="($index + 1) % 2 === 0" ng-repeat="(key, etapa) in fluxo_lead.etapas">
                    <div class="element">
                      <div class="icon-element">
                        <button type="button" class="btn_edit6" style="background: transparent;">
                          <i ng-if="etapa.feito != '1'" style="color: #979797;font-size: 20px;" class="far fa-check-circle"></i>
                          <i ng-if="etapa.feito == '1'" style="font-size: 20px;" class="fas fa-check-circle"></i>
                          <img ng-if="(fluxo_lead.etapas[key - 1] == null || fluxo_lead.etapas[key - 1].feito == '1' ) && etapa.feito != '1'" style="width: 40px;position: absolute;right: 22px;top: 40px;" src="<?= base_url('assets/img/click-sales-flow2.gif') ?>">
                        </button>

                        <div style="background-image: url('{{base_url + etapa.atividade.atv_ft}}');" ng-click="showDialogAddAtividade(null, etapa)"></div>
                        <p class="nm_atividade-flow">
                          <i class="fa fa-info-circle btn_info" title="Ver instruções" ng-click="openInfo(etapa.descricao)"></i>
                          {{etapa.atividade.nm_atividade_select}}
                        </p>
                      </div>
                      <div class="element-line">
                        <span class="inf-num">{{$index + 1}}</span>
                      </div>
                    </div>

                    <div style="display: flex;">
                      <div style="display: flex;" class='element_vert' my-directive></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="ctt-fluxo">
                <p ng-if="load_fluxo && fluxo_lead == null">Nenhum flow foi atribuido a esse funil</p>

                <div ng-if="!load_fluxo && fluxo_lead == null" layout-align="center center" class="text-center" id="circular_loader" ng-cloak>
                  <md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
                  <p style="font-size: 15px;margin-bottom: 5%;">
                    <span><?php echo lang2('please_wait') ?> <br>
                      <small><strong><?php echo lang2('loading') . ' Sales flow...' ?></strong></small></span>
                  </p>
                </div>

              </div>
            </div>
          </md-tab>
        <?php
        }
        ?>

        <md-tab label="<?php echo lang2('lead') ?>">
          <md-content class="bg-white" flex="100" style="margin-top: 10px;">
            <div class="col" style="margin-left: 20px;float: left;margin-top: 10px;">
              <p style="margin-bottom: 5px;">Qualificação</p>
              <button ng-click="alterarQualificacao(4)" type="button" style="{{ lead.qualificacao == '4' ? 'background:#796eed; color:#fff;border-color: #796eed;' : ''}}" class="btn btn-outline-light">Muito bom</button>
              <button ng-click="alterarQualificacao(3)" type="button" style="{{ lead.qualificacao == '3' ? 'background:#26c281; color:#fff;border-color: #26c281;' : ''}}" class="btn btn-outline-light">Bom</button>
              <button ng-click="alterarQualificacao(2)" type="button" style="{{ lead.qualificacao == '2' ? 'background:#ffb000; color:#fff;border-color: #ffb000;' : ''}}" class="btn btn-outline-light">Médio</button>
              <button ng-click="alterarQualificacao(1)" type="button" style="{{ lead.qualificacao == '1' ? 'background:#ff0000; color:#fff;border-color: #ff0000;' : ''}}" class="btn btn-outline-light">Ruim</button>
            </div>

            <div class="col" style="margin-left: 20px;float: left;margin-top: 10px;">
              <p style="margin-bottom: 5px;">TEMPERATURA</p>
              <!-- <button ng-if="lead.temperatura == null" type="button" style="{{ lead.temperatura == null ? 'background:#000; color:#fff;border-color: #000;' : ''}}" class="btn btn-outline-light">Indefinido</button> -->
              <button ng-click="alterarTemperatura(3)" type="button" style="{{ lead.temperatura == '3' ? 'background:#796eed; color:#fff;border-color: #796eed;' : ''}}" class="btn btn-outline-light">Frio</button>
              <button ng-click="alterarTemperatura(2)" type="button" style="{{ lead.temperatura == '2' ? 'background:#ffb000; color:#fff;border-color: #ffb000;' : ''}}" class="btn btn-outline-light">Morno</button>
              <button ng-click="alterarTemperatura(1)" type="button" style="{{ lead.temperatura == '1' ? 'background:#ff0000; color:#fff;border-color: #ff0000;' : ''}}" class="btn btn-outline-light">Quente</button>
            </div>

            <div class="col" style="margin-left: 20px;float: left;margin-top: 10px;">
              <p style="margin-bottom: 5px;">TAGS <i style="cursor: pointer;color: #1bcf18;" data-toggle="modal" data-target="#modalAddTags" class="fas fa-plus-circle"></i></p>
              <button ng-repeat="(i, tag) in lead.tags" type="button" style="margin-left: 5px;" class="btn btn-outline-light w3-theme-{{i}}" ng-bind="tag"></button>
            </div>

            <div class="col" style="margin-left: 20px;float: left;margin-top: 10px;">
              <md-button ng-click="SearcLead(lead)" style="padding: 0 10px; min-width: 40px; width: 40px; min-height: 40px;height: 40px;">
                <md-tooltip md-direction="bottom">Buscar dados de leads</md-tooltip>
                <md-icon md-menu-align-target style="margin: auto 3px auto 0;">
                  <img style="width: 28px;text-align: center; margin-left: -6px; margin-top: -7px;" src='<?= base_url('assets/img/menu/busca_leads.png') ?>'>
                </md-icon>
              </md-button>
            </div>


          </md-content>

          <md-content class="infosLeads2">
            <h5 ng-if="lead.tp_pessoa == 1"><strong ng-bind="lead.name"></strong></h5>
            <h5 ng-if="lead.tp_pessoa == 2"><strong ng-bind="lead.company"></strong></h5>

            <div layout="row" flex>
              <div flex="50">
                <div class="divInfoL2" ng-if="lead.tp_pessoa == 1">
                  <md-icon class="mdi mdi-local-store flt_left"></md-icon>
                  <div class="flt_left">
                    <p class="titleInfo"><?php echo lang2('name') ?></p>
                    <p class="nmInfo"><span ng-bind="lead.name"></span></p>
                  </div>
                </div>

                <div class="divInfoL2" ng-if="lead.tp_pessoa == 2">
                  <md-icon class="mdi mdi-local-store"></md-icon>
                  <div class="flt_left">
                    <p class="titleInfo"><?php echo lang2('company') ?></p>
                    <p class="nmInfo"><span ng-bind="lead.company"></span></p>
                  </div>
                </div>

                <div class="divInfoL2" ng-if="lead.tp_pessoa == 2 && lead.cnpj != null && lead.cnpj != ''">
                  <md-icon class="mdi"><i class="far fa-building"></i></md-icon>
                  <div class="flt_left">
                    <p class="titleInfo">Cnpj</p>
                    <p class="nmInfo"><span ng-bind="lead.cnpj"></span></p>
                  </div>
                </div>

                <div class="divInfoL2" ng-if="lead.tp_pessoa == 1 && lead.cpf != null && lead.cpf != ''">
                  <md-icon class="mdi"><i class="fas fa-archive"></i></md-icon>
                  <div class="flt_left">
                    <p class="titleInfo">Cpf</p>
                    <p class="nmInfo"><span ng-bind="lead.cpf"></span></p>
                  </div>
                </div>

                <div class="divInfoL2">
                  <md-icon class="mdi"><i class="fas fa-info-circle"></i></md-icon>
                  <div class="flt_left">
                    <p class="titleInfo"><?php echo lang2('status') ?></p>
                    <p class="nmInfo"><span ng-bind="lead.status"></span></p>
                  </div>
                </div>

                <div class="divInfoL2" ng-if="lead.phone != null && lead.phone != ''">
                  <md-icon class="mdi mdi-local-phone"></md-icon>
                  <div class="flt_left">
                    <p class="titleInfo"><?php echo lang2('phone') ?></p>
                    <p class="nmInfo"><span ng-bind="lead.phone"></span></p>
                  </div>
                </div>

                <div class="divInfoL2" ng-if="lead.email != null && lead.email != ''">
                  <md-icon class="ion-android-mail"></md-icon>
                  <div class="flt_left">
                    <p class="titleInfo"><?php echo lang2('email') ?></p>
                    <p class="nmInfo"><span ng-bind="lead.email"></span></p>
                  </div>
                </div>

                <div class="divInfoL2" ng-if="lead.address != null && lead.address != ''">
                  <md-icon class="mdi mdi-pin-drop"></md-icon>
                  <div class="flt_left">
                    <p class="titleInfo"><?php echo lang2('address') ?></p>
                    <p class="nmInfo"><span ng-bind="lead.address"></span></p>
                  </div>
                </div>

                <div class="divInfoL2" ng-if="lead.city != null && lead.city != ''">
                  <md-icon class="mdi mdi-city"></md-icon>
                  <div class="flt_left">
                    <p class="titleInfo"><?php echo lang2('city') ?></p>
                    <p class="nmInfo"><span ng-bind="lead.city"></span></p>
                  </div>
                </div>

                <div class="divInfoL2" ng-if="lead.state != null && lead.state != ''">
                  <md-icon><i class="fas fa-flag-usa"></i></md-icon>
                  <div class="flt_left">
                    <p class="titleInfo">Estado</p>
                    <p class="nmInfo"><span ng-bind="lead.state"></span></p>
                  </div>
                </div>

                <div class="divInfoL2" ng-if="lead.country != null && lead.country != ''">
                  <md-icon class="ion-earth"></md-icon>
                  <div class="flt_left">
                    <p class="titleInfo"><?php echo lang2('country') ?></p>
                    <p class="nmInfo"><span ng-bind="lead.country"></span></p>
                  </div>
                </div>

                <div class="divInfoL2" ng-if="lead.web_site != null && lead.web_site != ''">
                  <md-icon class="mdi mdi-nature-people"></md-icon>
                  <div class="flt_left">
                    <p class="titleInfo">Website</p>
                    <p class="nmInfo"><a ng-href="lead.web_site" target="_blank" ng-bind="lead.web_site"></a></p>
                  </div>
                </div>

              </div>
              <div flex="50">
                <div class="divInfoL2" ng-if="lead.tp_pessoa == 1 && lead.dt_nascimento != null && lead.dt_nascimento != ''">
                  <md-icon class="mdi"><i class="fas fa-calendar-week"></i></md-icon>
                  <div class="flt_left">
                    <p class="titleInfo">Data de nascimento</p>
                    <p class="nmInfo"><span ng-bind="lead.dt_nascimento | date:'dd/MM/yyyy'"></span></p>
                  </div>
                </div>

                <div class="divInfoL2" ng-if="lead.tp_pessoa == 2 && lead.setor_atividade != null && lead.setor_atividade != ''">
                  <md-icon class="mdi mdi-markunread-mailbox"></md-icon>
                  <div class="flt_left">
                    <p class="titleInfo">Setor de atividade</p>
                    <p class="nmInfo"><span ng-bind="lead.setor_atividade"></span></p>
                  </div>
                </div>

                <div class="divInfoL2" ng-if="lead.closer != null">
                  <md-icon class="mdi mdi-assignment-account"></md-icon>
                  <div class="flt_left">
                    <p class="titleInfo">Closer</p>
                    <p class="nmInfo"><span ng-bind="getStaff(lead.closer).name"></span></p>
                  </div>
                </div>


                <div class="divInfoL2" ng-if="lead.tp_pessoa == 2 && lead.porte != null && lead.porte != ''">
                  <md-icon class="mdi"> <i class="fas fa-search-dollar"></i></md-icon>
                  <div class="flt_left">
                    <p class="titleInfo">Porte</p>
                    <p class="nmInfo"><span ng-bind="lead.porte"></span></p>
                  </div>
                </div>


                <div class="divInfoL2" ng-if="lead.assigned != null && lead.assigned != ''">
                  <md-icon class="mdi mdi-assignment-account"></md-icon>
                  <div class="flt_left">
                    <p class="titleInfo"><?php echo lang2('assigned') ?></p>
                    <p class="nmInfo"><span ng-bind="lead.assigned"></span></p>
                  </div>
                </div>


                <div class="divInfoL2" ng-if="lead.source != null && lead.source != ''">
                  <md-icon class="mdi mdi-book-image"></md-icon>
                  <div class="flt_left">
                    <p class="titleInfo"><?php echo lang2('source') ?></p>
                    <p class="nmInfo"><span ng-bind="lead.source"></span></p>
                  </div>
                </div>

                <div class="divInfoL2" ng-if="lead.date_contacted != null && lead.date_contacted != ''">
                  <md-icon class="ion-android-calendar"></md-icon>
                  <div class="flt_left">
                    <p class="titleInfo"><?php echo lang2('date_contacted') ?></p>
                    <p class="nmInfo"><span ng-bind="lead.date_contacted | date:'dd/MM/yyyy HH:mm'"></span></p>
                  </div>
                </div>

                <div class="divInfoL2" ng-if="lead.tp_pessoa == 1 && lead.instagram != null && lead.instagram != ''">
                  <md-icon><i class="fab fa-instagram"></i></md-icon>
                  <div class="flt_left">
                    <p class="titleInfo">Instagram</p>
                    <p class="nmInfo"><span ng-bind="lead.instagram"></span></p>
                  </div>
                </div>

                <div class="divInfoL2" ng-if="lead.tp_pessoa == 1 && lead.facebook != null && lead.facebook != ''">
                  <md-icon><i class="fab fa-facebook"></i></md-icon>
                  <div class="flt_left">
                    <p class="titleInfo">Facebook</p>
                    <p class="nmInfo"><span ng-bind="lead.facebook"></span></p>
                  </div>
                </div>

                <div class="divInfoL2" ng-if="lead.linkedin != null && lead.linkedin != ''">
                  <md-icon><i class="fab fa-linkedin-in"></i></md-icon>
                  <div class="flt_left">
                    <p class="titleInfo">Linkedin</p>
                    <p class="nmInfo"><a href="{{ lead.linkedin.startsWith('http') ? lead.linkedin : 'https://' + lead.linkedin }}" target="_blank" ng-bind="lead.linkedin"></a></p>
                  </div>
                </div>

                <div class="divInfoL2" ng-if="lead.campanha_linkedin != null && lead.campanha_linkedin != ''">
                  <md-icon><i class="fas fa-tv"></i></md-icon>
                  <div class="flt_left">
                    <p class="titleInfo">Campanha</p>
                    <p class="nmInfo"><a ng-href="lead.campanha_linkedin" target="_blank" ng-bind="lead.campanha_linkedin"></a></p>
                  </div>
                </div>



              </div>
            </div>

            <div class="accordion infosAdd" id="accordionExample" ng-if="lead.leads_data != null" style="margin-top: 20px;">
              <div class="card">
                <div class="card-header" id="headingThree">
                  <h3 class="btn btn-link collapsed h3Title" data-toggle="collapse" data-target="#collapseInfo" aria-expanded="false" aria-controls="collapseInfo">
                    Informações
                  </h3>
                </div>
                <div id="collapseInfo" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">

                  <div class="col-md-6 col-12">
                    <div class="flt_left">
                      <p class="titleInfo">Razão social</p>
                      <p class="nmInfo"><span ng-bind="lead.leads_data.Name"></span></p>
                    </div>
                  </div>

                  <div class="col-md-6 col-12">
                    <div class="flt_left">
                      <p class="titleInfo">Cnpj</p>
                      <p class="nmInfo"><span ng-bind="lead.leads_data.DocumentFormatted"></span></p>
                    </div>
                  </div>


                  <div class="col-md-6 col-12">
                    <div class="flt_left">
                      <p class="titleInfo">Modelo fiscal</p>
                      <p class="nmInfo"><span ng-bind="TaxModel[lead.leads_data.Company.TaxModel]"></span></p>
                    </div>
                  </div>

                  <div class="col-md-6 col-12">
                    <div class="flt_left">
                      <p class="titleInfo">Tamanho da empresa</p>
                      <p class="nmInfo"><span ng-bind="BusinessSize[lead.leads_data.Company.BusinessSize]"></span></p>
                    </div>
                  </div>

                  <div class="col-md-6 col-12">
                    <div class="flt_left">
                      <p class="titleInfo">Quantidade de funcionários</p>
                      <p class="nmInfo"><span ng-bind="lead.leads_data.Company.TotalEmployees"></span></p>
                    </div>
                  </div>

                  <div class="col-md-6 col-12">
                    <div class="flt_left">
                      <p class="titleInfo">Situação</p>
                      <p class="nmInfo"><span ng-bind="lead.leads_data.FiscalSituation.Description"></span></p>
                    </div>
                  </div>

                  <div class="col-md-6 col-12">

                    <div class="flt_left">
                      <p class="titleInfo">Número de sócios</p>
                      <p class="nmInfo"><span ng-bind="lead.leads_data.Company.TotalPartners"></span></p>
                    </div>


                  </div>

                  <div class="col-md-6 col-12">
                    <div class="flt_left">
                      <p class="titleInfo">Total de empresas</p>
                      <p class="nmInfo"><span ng-bind="lead.leads_data.Company.TotalCompanyPartners"></span></p>
                    </div>
                  </div>

                  <h5 class="h5Title2">Atividade</h5>
                  <div class="row" ng-repeat="(i, active) in lead.leads_data.Activities" style="margin-bottom: 30px;padding-bottom: 10px;">
                    <div class="col-md-6 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">Descrição cnae {{active.IsPrimary != null && active.IsPrimary ?
                          '(Primário)' : '(Secundários)'}}</p>
                        <p class="nmInfo"><span ng-bind="active.Description"></span></p>
                      </div>
                    </div>

                    <div class="col-md-3 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">Código</p>
                        <p class="nmInfo"><span ng-bind="active.Code"></span></p>
                      </div>
                    </div>


                  </div>
                </div>
              </div>

              <div class="card">
                <div class="card-header" id="headingThree">
                  <h3 class="btn btn-link collapsed h3Title" data-toggle="collapse" data-target="#collapseTelefone" aria-expanded="false" aria-controls="collapseThree">
                    Telefones
                  </h3>
                </div>
                <div id="collapseTelefone" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                  <h5 class="h5Title2" ng-if="lead.leads_data.Phones.length > 0">Telefones fixos</h5>
                  <div class="row" ng-repeat="(i, telefone) in lead.leads_data.Phones" ng-if="!telefone.IsMobile" style="margin-bottom: 30px;padding-bottom: 10px;">
                    <div class="col-md-3 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">Numero</p>
                        <p class="nmInfo"><span ng-bind="telefone.FormattedNumber"></span></p>
                      </div>
                    </div>

                    <div class="col-md-3 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">Ativo</p>
                        <md-switch ng-model="telefone.IsValido" ng-change="UpdateAtivo(lead.leads_data)" aria-label="Status" ng-cloak><strong class="text-muted">Ativo</strong></md-switch>

                      </div>
                    </div>

                    <div class="col-md-3 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">É facebook?</p>
                        <p class="nmInfo"><span ng-bind="telefone.IsFacebook ? 'Sim' : 'Não'"></span></p>
                      </div>
                    </div>

                    <div class="col-md-3 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">É WhatsApp?</p>
                        <p class="nmInfo"><span ng-bind="telefone.IsWhatsapp ? 'Sim' : 'Não'"></span></p>
                      </div>
                    </div>

                    <div class="col-md-3 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">É Procon?</p>
                        <p class="nmInfo"><span ng-bind="telefone.IsProcon ? 'Sim' : 'Não'"></span></p>
                      </div>
                    </div>
                  </div>

                  <h5 class="h5Title2" ng-if="lead.leads_data.Phones.length > 0">Telefones celular</h5>
                  <div class="row" ng-repeat="(i, telefone) in lead.leads_data.Phones" ng-if="telefone.IsMobile" style="margin-bottom: 30px;padding-bottom: 10px;">
                    <div class="col-md-3 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">Numero</p>
                        <p class="nmInfo"><span ng-bind="telefone.FormattedNumber"></span></p>
                      </div>
                    </div>

                    <div class="col-md-3 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">Ativo</p>
                        <md-switch ng-model="telefone.IsValido" ng-change="UpdateAtivo(lead.leads_data)" aria-label="Status" ng-cloak><strong class="text-muted">Ativo</strong></md-switch>

                      </div>
                    </div>


                    <div class="col-md-3 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">É facebook?</p>
                        <p class="nmInfo"><span ng-bind="telefone.IsFacebook ? 'Sim' : 'Não'"></span></p>
                      </div>
                    </div>

                    <div class="col-md-3 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">É WhatsApp?</p>
                        <p class="nmInfo"><span ng-bind="telefone.IsWhatsapp ? 'Sim' : 'Não'"></span></p>
                      </div>
                    </div>

                    <div class="col-md-3 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">É Procon?</p>
                        <p class="nmInfo"><span ng-bind="telefone.IsProcon ? 'Sim' : 'Não'"></span></p>
                      </div>
                    </div>
                  </div>

                </div>
              </div>

              <div class="card">
                <div class="card-header" id="headingThree">
                  <h3 class="btn btn-link collapsed h3Title" data-toggle="collapse" data-target="#collapseEmail" aria-expanded="false" aria-controls="collapseThree">
                    E-mails
                  </h3>
                </div>
                <div id="collapseEmail" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">


                  <div class="row" ng-repeat="(i, email) in lead.leads_data.Emails" style="margin-bottom: 30px;padding-bottom: 10px;">
                    <div class="col-md-6 col-12">

                      <div class="flt_left">
                        <p class="titleInfo">Email</p>
                        <p class="nmInfo"><span ng-bind="email.Email"></span></p>
                      </div>
                    </div>

                    <div class="col-md-3 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">Ativo</p>
                        <md-switch ng-model="email.IsValido" ng-change="UpdateAtivo(lead.leads_data)" aria-label="Status" ng-cloak><strong class="text-muted">Ativo</strong></md-switch>

                      </div>
                    </div>

                    <div class="col-md-3 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">É facebook?</p>
                        <p class="nmInfo"><span ng-bind="email.IsFacebook ? 'Sim' : 'Não'"></span></p>
                      </div>
                    </div>

                    <div class="col-md-3 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">É linkedin?</p>
                        <p class="nmInfo"><span ng-bind="email.IsLinkedIn ? 'Sim' : 'Não'"></span></p>
                      </div>
                    </div>

                    <div class="col-md-3 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">Presença digital?</p>
                        <p class="nmInfo"><span ng-bind="email.IsDigitalBehavior ? 'Sim' : 'Não'"></span></p>
                      </div>

                    </div>

                    <div class="col-md-3 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">E-mail de sócio?</p>
                        <p class="nmInfo"><span ng-bind="email.IsFromPartner ? 'Sim' : 'Não'"></span></p>
                      </div>


                    </div>

                    <div class="col-md-3 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">Score</p>
                        <p class="nmInfo"><span ng-bind="email.Score"></span></p>
                      </div>
                    </div>



                  </div>

                </div>
              </div>

              <div class="card">
                <div class="card-header" id="headingThree">
                  <h3 class="btn btn-link collapsed h3Title" data-toggle="collapse" data-target="#collapseSocios" aria-expanded="false" aria-controls="collapseThree">
                    Sócios & pessoas ligadas
                  </h3>
                </div>
                <div id="collapseSocios" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">

                  <h5 class="h5Title2" ng-if="lead.leads_data.Relateds.length > 0">Pessoas ligadas</h5>
                  <div class="row" ng-repeat="(i, pessoa) in lead.leads_data.Relateds" style="margin-bottom: 30px;padding-bottom: 10px;">
                    <div class="col-md-8 col-12">


                      <div class="flt_left">
                        <p class="titleInfo">Nome</p>
                        <p class="nmInfo"><span ng-bind="pessoa.Name"></span></p>
                      </div>
                    </div>

                    <div class="col-md-4 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">Cpf</p>
                        <p class="nmInfo"><span ng-bind="pessoa.Document"></span></p>
                      </div>
                    </div>
                  </div>

                  <h5 class="h5Title2" ng-if="lead.leads_data.PartnerShips.length > 0">Sócios</h5>
                  <div class="row" ng-repeat="(i, socios) in lead.leads_data.PartnerShips" ng-if="socios.Status == '1'" style="margin-bottom: 30px;padding-bottom: 10px;">
                    <div class="col-md-8 col-12">


                      <div class="flt_left">
                        <p class="titleInfo">Nome</p>
                        <p class="nmInfo"><span ng-bind="socios.Name"></span></p>
                      </div>
                    </div>

                    <div class="col-md-4 col-12">

                      <div class="flt_left">
                        <p class="titleInfo">Cpf</p>
                        <p class="nmInfo"><span ng-bind="socios.Document"></span></p>
                      </div>

                    </div>
                  </div>
                </div>
              </div>

              <div class="card">
                <div class="card-header" id="headingThree">
                  <h3 class="btn btn-link collapsed h3Title" data-toggle="collapse" data-target="#collapseEnderecos" aria-expanded="false" aria-controls="collapseThree">
                    Endereços
                  </h3>
                </div>
                <div id="collapseEnderecos" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                  <div class="row" ng-repeat="(i, endereco) in lead.leads_data.Addresses" style="margin-bottom: 30px;padding-bottom: 10px;">
                    <div class="col-md-6 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">Rua</p>
                        <p class="nmInfo"><span ng-bind="endereco.Street"></span></p>
                      </div>


                    </div>

                    <div class="col-md-3 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">Numero</p>
                        <p class="nmInfo"><span ng-bind="endereco.Number"></span></p>
                      </div>
                    </div>

                    <div class="col-md-3 col-12">

                      <div class="flt_left">
                        <p class="titleInfo">Cep</p>
                        <p class="nmInfo"><span ng-bind="endereco.ZipCode"></span></p>
                      </div>


                    </div>

                    <div class="col-md-4 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">Cidade</p>
                        <p class="nmInfo"><span ng-bind="endereco.City"></span></p>
                      </div>


                    </div>

                    <div class="col-md-4 col-12">

                      <div class="flt_left">
                        <p class="titleInfo">Estado</p>
                        <p class="nmInfo"><span ng-bind="endereco.State"></span></p>
                      </div>


                    </div>

                  </div>
                </div>
              </div>

              <div class="card">
                <div class="card-header" id="headingThree">
                  <h3 class="btn btn-link collapsed h3Title" data-toggle="collapse" data-target="#collapseInformacoesAdc" aria-expanded="false" aria-controls="collapseThree">
                    Informações adicionais
                  </h3>
                </div>
                <div id="collapseInformacoesAdc" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                  <h5 class="h5Title2" ng-if="lead.leads_data.Vehicles.length > 0">Bens móveis</h5>
                  <div class="row" ng-repeat="(i, bens) in lead.leads_data.Vehicles" ng-if="bens.Status == '1'" style="margin-bottom: 30px;padding-bottom: 10px;">
                    <div class="col-md-4 col-12">

                      <div class="flt_left">
                        <p class="titleInfo">Placa</p>
                        <p class="nmInfo"><span ng-bind="bens.LicensePlate"></span></p>
                      </div>


                    </div>

                    <div class="col-md-3 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">Renavan</p>
                        <p class="nmInfo"><span ng-bind="bens.Renavan"></span></p>
                      </div>


                    </div>

                    <div class="col-md-3 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">Ano de fabricação</p>
                        <p class="nmInfo"><span ng-bind="bens.YearManuFacturing"></span></p>
                      </div>
                    </div>

                    <div class="col-md-3 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">Ano modelo</p>
                        <p class="nmInfo"><span ng-bind="bens.YearModel"></span></p>
                      </div>

                    </div>

                    <div class="col-md-3 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">Marca</p>
                        <p class="nmInfo"><span ng-bind="bens.Brand"></span></p>
                      </div>
                    </div>

                    <div class="col-md-3 col-12">

                      <div class="flt_left">
                        <p class="titleInfo">Modelo</p>
                        <p class="nmInfo"><span ng-bind="bens.Model"></span></p>
                      </div>

                    </div>

                    <div class="col-md-3 col-12">
                      <div class="flt_left">
                        <p class="titleInfo">Valor de mercado</p>
                        <p class="nmInfo"><span ng-bind="bens.FipePrice"></span></p>
                      </div>


                    </div>
                  </div>

                  <h5 class="h5Title2">Score</h5>
                  <div class="col-md-6 col-12">
                    <div class="flt_left">
                      <p class="titleInfo">Score de crédito</p>
                      <p class="nmInfo"><span ng-bind="lead.leads_data.CreditScore.D90"></span></p>
                    </div>
                  </div>

                  <div class="col-md-6 col-12">
                    <div class="flt_left">
                      <p class="titleInfo">Score de marketing</p>
                      <p class="nmInfo"><span ng-bind="lead.leads_data.MarketingScore.D90"></span></p>
                    </div>
                  </div>

                </div>
              </div>

            </div>

          </md-content>

          <md-divider></md-divider>

          <!--
          <md-content class="bg-white infosLeads" layout-padding>
            <md-list-item>
              <md-icon class="mdi mdi-sort-desc"></md-icon>
              <p class="text-left" flex ng-bind="lead.description"></p>
            </md-list-item>
          </md-content>
          -->

          <div class="divContatos">
            <div class="titlePessoaAdd" style="color: #6a6a6a;">
              <h4 style="margin: 0;"><i style="font-size: 25px;" class="far fa-user"></i> Pessoas de contatos

                <button type="button" class="btn" style="margin-top: -2px;float: right;background: #6a6a6a;color: #fff;" ng-click="showDialogAddContato()">
                  <span style="vertical-align: middle;">Adicionar</span>
                  <md-icon><i style="color:white" class="ion-android-add-circle"></i></md-icon>
                </button>
              </h4>
            </div>


            <div class="contatosLead" ng-repeat="contato in contatos">
              <div class="capaContato" style="background-image:url('<?= base_url('assets/img/wallpaperLinkedin.jpg') ?>')">
                <i style="float: right;margin-right: 1em;margin-top: 1em;color: #fff;" class="fas fa-pen" ng-click="showDialogAddContato(contato)"></i>
              </div>
              <div class="fotoContato" style="background-image:url('<?= base_url('assets/img/perfilContato.jpg') ?>')">
              </div>

              <div class="dadosContato">
                <h4 style="margin: 0;font-weight: 600;margin-top: 0.5em;">{{contato.nm_contato}}</h4>
                <p style="margin: 0;">{{contato.dt_aniversario }}</p>
                <small>{{contato.cargo}}</small>

                <p sng-if="contato.email != null && contato.email != ''" style="margin-top: 1em;margin-bottom: 0;"><i style="color: #0d3d9f;" class="fas fa-envelope"></i>
                  <b>Email:</b> {{contato.email}}
                </p>
                <p ng-if="contato.telefone != null" style="margin-bottom: 0;"><i style="color: #0d3d9f;" class="fas fa-phone-alt"></i>
                  <b>Telefone:</b> {{contato.telefone}}
                </p>
                <p ng-if="contato.linkedin != null" style="margin-bottom: 0;"><i style="color: #0d3d9f;" class="fab fa-linkedin-in"></i>
                  <b>Linkedin:</b> {{contato.linkedin}}
                </p>
                <p ng-if="contato.observacao != null" style="margin-bottom: 0;"><i style="color: #0d3d9f;" class="far fa-comment-alt"></i>
                  <b>Observação:</b> {{contato.observacao}}
                </p>

                <p ng-if="contato.sobre != null" style="margin-bottom: 0;">
                  <i style="color: #0d3d9f;" class="fas fa-info"></i>
                  <b>Sobre:</b> {{contato.sobre}}
                </p>

                <p ng-if="contato.inicio_empresa != null" style="margin-bottom: 0;">
                  <i style="color: #0d3d9f;" class="fas fa-info"></i>
                  <b>Inicio empresa:</b> {{contato.inicio_empresa }}
                </p>

                <p ng-if="contato.data_conexao_linkedin_contato != null" style="margin-bottom: 0;">
                  <i style="color: #0d3d9f;" class="fas fa-info"></i>
                  <b>Data conexão linkedin:</b> {{contato.data_conexao_linkedin_contato}}
                </p>

                <p ng-if="contato.conexoes_em_comum_contato != null" style="margin-bottom: 0;">
                  <i style="color: #0d3d9f;" class="fas fa-info"></i>
                  <b>Conexões em comum:</b> {{contato.conexoes_em_comum_contato }}
                </p>

                <p ng-if="contato.conexoes_no_linkedin_contato != null" style="margin-bottom: 0;">
                  <i style="color: #0d3d9f;" class="fas fa-info"></i>
                  <b>Conexões no linkedin:</b> {{contato.conexoes_no_linkedin_contato }}
                </p>



              </div>

            </div>

          </div>

        </md-tab>

        <md-tab label="Emails automatizados">
          <md-content layout-padding>
            <div ng-show="emailsAutLoader" layout-align="center center" class="text-center" id="circular_loader" ng-cloak>
              <md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
              <p style="font-size: 15px;margin-bottom: 5%;">
                <span><?php echo lang2('please_wait') ?> <br>
                  <small><strong><?php echo lang2('loading') . ' ' . lang2('x_menu_emails_aut') . '...' ?></strong></small></span>
              </p>
            </div>

            <md-content ng-show="!emailsAutLoader" class="bg-white" ng-cloak>
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
                        <md-switch ng-model="adicionadoTask[taskAut.id_task]" ng-change="UpdateAdicionadoTask(taskAut.id_task)" aria-label="Status" ng-cloak><strong class="text-muted">Ativo</strong></md-switch>
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

        <?php if ($privileges_Model->has_privilege('proposals')) { ?>
          <!--
          <md-tab label="<?php echo lang2('proposals') ?>">
            <md-content class="bg-white">
              <md-list flex class="md-p-0 sm-p-0 lg-p-0">
                <md-list-item ng-repeat="proposal in proposals" ng-click="GoProposal($index)" aria-label="Proposal">
                  <md-icon class="ico-ciuis-proposals"></md-icon>
                  <p><strong ng-bind="proposal.longid"></strong></p>
                  <h4><strong ng-bind-html="proposal.total | currencyFormat:cur_code:null:true:cur_lct"></strong></h4>
                  <md-divider></md-divider>
                </md-list-item>
              </md-list>
              <md-content ng-show="!proposals.length" class="md-padding bg-white no-item-data"><?php echo lang2('notdata') ?></md-content>
            </md-content>
          </md-tab>
          -->
        <?php
        }
        ?>


        <!--
        <md-tab label="<?php echo lang2('notes') ?>">

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

              <section layout="row" layout-sm="column" layout-wrap class="pull-right">

                <md-button ng-click="AddNote()" class="md-raised md-primary"><?php echo lang2('addnote'); ?></md-button>

              </section>

            </section>

          </md-content>

        </md-tab>

              -->
        <!--
        <md-tab label="<?php echo lang2('reminders') ?>">

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
        -->




      </md-tabs>
      <!--
      <md-subheader ng-if="custom_fields.length > 0"><?php echo lang2('custom_fields'); ?></md-subheader>
      <md-list-item ng-if="custom_fields.length > 0" ng-repeat="field in custom_fields">
        <md-icon class="{{field.icon}} material-icons"></md-icon>
        <strong flex md-truncate>{{field.name}}</strong>
        <p ng-if="field.type === 'input'" class="text-right" flex md-truncate ng-bind="field.data"></p>
        <p ng-if="field.type === 'textarea'" class="text-right" flex md-truncate ng-bind="field.data"></p>
        <p ng-if="field.type === 'date'" class="text-right" flex md-truncate ng-bind="field.data | date:'dd, MMMM yyyy EEEE'"></p>
        <p ng-if="field.type === 'select'" class="text-right" flex md-truncate ng-bind="custom_fields[$index].selected_opt.name"></p>
        <md-divider ng-if="custom_fields"></md-divider>
      </md-list-item>
      -->


    </md-content>

  </div>

  <md-content class="saudacao col-xs-3 col-lg-3" ng-show="!overviewLoader">
    <div class="titleSaud">Next Message</div>

    <div class="content-saud">
      <!--<h6 style="font-size:14px;margin-top: 28px;"><b>Time</b></h6>-->

      <div class="input-group mb-3 input-search">
        <div class="input-group-prepend">
          <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
        </div>
        <input type="text" ng-model="searchPanel" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
      </div>

      <div ng-click="setChat(chat)" class="loadMensagem" ng-repeat="chat in chats | filter : searchPanel">
        <img src="{{appurl + 'uploads/images/' + chat.details.staffavatar }}">
        <a style="max-width: 100%;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;" href="javaScript:void(0)"><b ng-bind="chat.details.staffname"></b></a>
        <p style="max-width: calc(100% - 75px);max-width: calc(100% - 75px); overflow: hidden;text-overflow: ellipsis;display: -webkit-box;-webkit-line-clamp: 2;-webkit-box-orient: vertical;" ng-bind="chat.ultimaMsg"></p>
        <i ng-if="chat.msg_nova == 1" style="color: #17c93e;font-size: 11px;float: right;" class="fas fa-circle"></i>
      </div>

    </div>

  </md-content>



  <div class="modal fade" id="modalContatoSocial" style="z-index: 2000;">
    <div class="modal-dialog mdAngular">
      <div class="modal-content">
        <div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
          <button type="button" class="close" ng-click="dimiss()">
            <span aria-hidden="true">×</span> <span class="sr-only"><?php echo lang2('close') ?></span>
          </button>
          <b>Selecione o contato</b>
        </div>
        <div id="modalBody" class="modal-body" style="max-height: 70vh; overflow: auto; overflow-x: hidden;">
          <md-table-container>
            <table md-table md-progress="promise" style="width: 100%;border-top: 0;">
              <thead>
                <tr>
                  <th scope="col">Nome</th>
                  <th scope="col">Contato</th>
                  <th scope="col" width="50px">Enviar</th>
                </tr>
              </thead>
              <tbody>
                <tr ng-repeat="contato in contatos" ng-show="
              (contato.email.length > 0 && ShowContato == 'email') ||
              (contato.linkedin.length > 0 && ShowContato == 'linkedin') ||
              (contato.telefone.length > 0 && contato.is_whatsApp == '1' && ShowContato == 'whatsapp')
              ">
                  <th scope="row">{{contato.nm_contato}}</th>
                  <td>{{ShowContato == 'email' ? contato.email : ShowContato == 'linkedin' ? contato.linkedin :
                    contato.telefone}}</td>

                  <td>
                    <a href="<?= base_url('emails/send_email') ?>?email={{contato.email}}&customer={{contato.nm_contato}}&company_name={{lead.name!= ''? lead.name : lead.company}}&id_lead={{lead.id}}" target="_blank" class="btn btn-success btn_icon" ng-if="contato.email.length > 0 && ShowContato == 'email' && !enviaAutomatico">
                      <img src="{{base_url + 'assets/img/icons/email.png'}}">
                    </a>

                    <a href="javaScript:void(0)" ng-click="enviaEmail(contato.email)" class="btn btn-success btn_icon" ng-if="contato.email.length > 0 && ShowContato == 'email' && enviaAutomatico">
                      <img src="{{base_url + 'assets/img/icons/email.png'}}">
                    </a>

                    <a href="{{ contato.linkedin.startsWith('http') ? contato.linkedin : 'https://' + contato.linkedin }}" class="btn btn-success btn_icon" target="_blank" ng-if="contato.linkedin.length > 0 && ShowContato == 'linkedin'">
                      <img src="{{base_url + 'assets/img/icons/linkedin.png'}}">
                    </a>
                    <a href="https://wa.me/+55{{replaceNumero(contato.telefone)}}" ng-if="contato.telefone.length > 0 && contato.is_whatsApp == '1' && ShowContato == 'whatsapp'" target="_blank" class="btn btn-success btn_icon">
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

  <div class="modal fade" id="modalAddTags" style="z-index: 2000;">
    <div class="modal-dialog mdAngular">
      <div class="modal-content">
        <div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
          <button type="button" class="close" ng-click="dimiss()">
            <span aria-hidden="true">×</span> <span class="sr-only"><?php echo lang2('close') ?></span>
          </button>
          <b> Alterar tag</b>
        </div>
        <div id="modalBody" class="modal-body" style="max-height: 70vh; overflow: auto; overflow-x: hidden;">
          <md-chips ng-model="lead.tags" md-separator-keys="keys" placeholder="Lead Tags" secondary-placeholder="Seperate with comma."></md-chips> {{lead.tags}}

          <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>
            <md-button ng-click="UpdateLead()" class="template-button" ng-disabled="saving == true">
              <span ng-hide="saving == true"><?php echo lang2('update'); ?></span>
              <md-progress-circular class="white" ng-show="saving == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>
            </md-button>
          </section>
        </div>
      </div>
    </div>
  </div>





  <div class="modal fade" id="modalAddAtividade" style="z-index: 2000;">
    <div class="modal-dialog mdAngular modal-lg" style="margin-top: 0;">
      <div class="modal-content" style="max-height: calc(100% - 75px);">
        <div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
          <button type="button" class="close" ng-click="dimiss()">
            <span aria-hidden="true">×</span> <span class="sr-only"><?php echo lang2('close') ?></span>
          </button>
          <b> Adicionar nova atividade</b>
        </div>
        <div id="modalBody" class="modal-body" style="max-height: 70vh; overflow: auto; overflow-x: hidden;">
          <div class="row">
            <div class="col-md-8 col-12">
              <md-input-container class="md-block" style='margin-top: 8px;'>
                <label style="margin-bottom: 0;">Atividade</label>
                <md-select ng-model="atividade.atividade" class="form-control select-modal">
                  <md-option ng-value="atv.id_atv" ng-repeat="atv in leadAtvSelect">{{atv.nm_atividade_select}}</md-option>
                </md-select>
              </md-input-container>
            </div>

            <div class="col-md-8 col-12" ng-if="findAtividadeByid(atividade.atividade).nm_atividade_select.includes('E-mail')
            || findAtividadeByid(atividade.atividade).nm_atividade_select.includes('e-mail')
            || findAtividadeByid(atividade.atividade).nm_atividade_select.includes('email')
            ">
              <md-input-container class="md-block" style='margin-top: 8px;'>
                <label style="margin-bottom: 0;">Modelo de Email</label>
                <md-select ng-model="atividade.email" class="form-control select-modal" ng-change="changeEmail()">
                  <md-option ng-value="0">Selecione</md-option>
                  <md-option ng-value="atv.id" ng-repeat="atv in templates">{{atv.subject}}</md-option>
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
          <!--
          <div class="row" style="margin-top: 1em;">
            <div class="col-md-4 col-12">
              <md-input-container class="md-block" style='margin-top: 8px;'>
                <label style="margin-bottom: 0;">Reunião/Call realizada?</label>
                <md-select ng-model="atividade.reuniao_call" class="form-control select-modal">
                  <md-option value="0">Não</md-option>
                  <md-option value="1">Sim</md-option>
                </md-select>
              </md-input-container>
            </div>
          </div>
          -->

          <div class="row" style="margin-top: 1em;">
            <div class="col-12">
              <md-input-container class="md-block" style="margin-left: 10px;margin-right: 10px;height: auto;" ng-show="modalEtapa != null">
                <label>Instruções</label>
                <textarea style="min-height: 50px !important;" readonly ng-model="atividade.descricao" style="background:#F0F8FF !important" class="text-area-modal"></textarea>
              </md-input-container>


              <md-input-container class="md-block" style="margin-left: 10px;margin-right: 10px;height: auto;" ng-show="!anotacoesAtv">
                <label ng-if="modalEtapa == null">Anotações</label>
                <label ng-if="modalEtapa != null">Campo para copiar</label>
                <textarea ng-model="atividade.anotacoes" style="background:#F0F8FF !important" class="text-area-modal"></textarea>
              </md-input-container>

              <md-input-container class="md-block" style="margin-left: 10px;margin-right: 10px;height: auto;" ng-show="anotacoesAtv">
                <textarea ng-model="atividade.anotacoes" id="anotacoesAtv" style="background:#F0F8FF !important" class="text-area-modal"></textarea>
              </md-input-container>



            </div>
          </div>


          <div class="row">
            <div class="col-md-4 col-12" ng-if="atividade.campoCopiar != null">
              <input type="text" style="display: none;" id="campoCopiar" value="{{replaceVariaveis(atividade.campoCopiar)}}">

              <button type="button" class="btn btn-secondary" style="float: left;margin-top: 5px;border: 1px solid #e1e1e1;padding: 8px 12px;font-size: 12px;" ng-click="copyDescricao()"><i class="far fa-copy"></i> Copiar</button>

            </div>

            <div class="col-12 col-md-5" style="float: right">
              <md-input-container class="md-block">
                <label>Retorno</label>
                <input ng-model="atividade.retorno" type="date">
              </md-input-container>
            </div>
          </div>

        </div>
        <div class="modal-footer" style="margin: 0;">
          <div ng-if="exibeIcon" style="float: left;">
            <a href="{{lead.instagram}}" style="padding: 0 !important;" class="btn btn-success btn_icon" target="_blank" ng-if="lead.instagram.length > 0">
              <img src="{{base_url + 'assets/img/icons/instagram.png'}}">
            </a>
            <a href="javaScript:void(0)" style="padding: 0 !important;" class="btn btn-success btn_icon" ng-click="showDialogContatoSocial('email', true)">
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
                <label style="margin-bottom: 0;">Tipo</label>
                <md-select ng-model="newReuniao.tipo" class="form-control select-modal">
                  <md-option value="Presencial">Presencial</md-option>
                  <md-option value="Remota">Remota</md-option>
                </md-select>
              </md-input-container>
            </div>
          </div>
          <div class="row">
            <!--
            <div class="col-md-6 col-12">
              <md-input-container class="md-block" style='margin-top: 8px;'>
                <label style="margin-bottom: 0;">Realizada?</label>
                <md-select ng-model="newReuniao.realizada" class="form-control select-modal">
                  <md-option value="Realizada">Realizada</md-option>
                  <md-option value="Não Realizada">Não Realizada</md-option>
                </md-select>
              </md-input-container>
            </div>
            

            <div class="col-md-6 col-12" ng-show="newReuniao.realizada == 'Não Realizada'">
              <md-input-container class="md-block" style='margin-top: 8px;'>
                <label style="margin-bottom: 0;">Qual o problema?</label>
                <md-select ng-model="newReuniao.problema" class="form-control select-modal">
                  <md-option value="Lead">Lead</md-option>
                  <md-option value="Nossa empresa">Nossa empresa</md-option>
                </md-select>
              </md-input-container>
            </div>

            <div class="col-md-6 col-12" ng-show="newReuniao.problema == 'Lead'">
              <md-input-container class="md-block" style='margin-top: 8px;'>
                <label style="margin-bottom: 0;">Motivo?</label>
                <md-select ng-model="newReuniao.motivo" class="form-control select-modal">
                  <md-option value="Não compareceu">Não compareceu</md-option>
                  <md-option value="Reagendou">Reagendou</md-option>
                  <md-option value="Cancelou">Cancelou</md-option>
                </md-select>
              </md-input-container>
            </div>
            -->

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

  <div class="modal fade" id="modalAddContato" style="z-index: 2000;">
    <div class="modal-dialog mdAngular">
      <div class="modal-content">
        <div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
          <button type="button" class="close" ng-click="dimiss()">
            <span aria-hidden="true">×</span> <span class="sr-only"><?php echo lang2('close') ?></span>
          </button>
          <b> Adicionar/editar novo contato</b>
        </div>
        <div id="modalBody" class="modal-body modal-person" style="max-height: 70vh; overflow: auto; overflow-x: hidden;">
          <div class="row">
            <div class="col-md-8 col-12">
              <md-input-container class="md-block">
                <label style="margin-bottom: 0;">Nome</label>
                <input ng-model="newContato.nm_contato" type="text">
              </md-input-container>
            </div>

            <div class="col-md-4 col-12">
              <md-input-container class="md-block">
                <label style="margin-bottom: 0;">Data de nascimento</label>
                <input value="{{newContato.dt_aniversario}}" ng-model="newContato.dt_aniversario" type="date">
              </md-input-container>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 col-12">
              <md-input-container class="md-block">
                <label style="margin-bottom: 0;">Cargo</label>
                <input ng-model="newContato.cargo" type="text">
              </md-input-container>
            </div>

            <div class="col-md-6 col-12">
              <md-input-container class="md-block">
                <label style="margin-bottom: 0;">Email</label>
                <input ng-model="newContato.email" type="text">
              </md-input-container>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 col-12">
              <md-input-container class="md-block">
                <label style="margin-bottom: 0;">Telefone</label>
                <input class="phone" ng-model="newContato.telefone" type="text">
              </md-input-container>
            </div>

            <div class="col-md-6 col-12">
              <md-input-container class="md-block">
                <label style="margin-bottom: 0;">Linkedin</label>
                <input ng-model="newContato.linkedin  " type="text">
              </md-input-container>
            </div>

            <div class="col-4" style="float: left;">
              <md-input-container class="md-block pull-left">
                <md-checkbox ng-model="newContato.ctt_principal" aria-label="add" style="margin: 0;margin-left: 1em;">Contato principal</md-checkbox>
              </md-input-container>
            </div>

            <div class="col-4" style="float: left;">
              <md-input-container class="md-block pull-left">
                <md-checkbox ng-model="newContato.is_whatsApp" aria-label="add" style="margin: 0;margin-left: 1em;">É WhatsApp</md-checkbox>
              </md-input-container>
            </div>
          </div>

          <div class="row">
            <p style="margin-bottom: 0;" ng-if="newContato.telefone2 != null"><b>Telefone 2:</b> {{ newContato.telefone2 }}</p>
            <p style="margin-bottom: 0;" ng-if="newContato.messenger1 != null"><b>Messenger 1:</b>{{ newContato.messenger1 }}</p>
            <p style="margin-bottom: 0;" ng-if="newContato.messenger2 != null"><b>Messenger 2:</b>{{ newContato.messenger2 }}</p>
            <p style="margin-bottom: 0;" ng-if="newContato.email2 != null"><b>Email 2:</b>{{ newContato.email2 }}</p>
            <p style="margin-bottom: 0;" ng-if="newContato.email3 != null"><b>Email 3:</b>{{ newContato.email3 }}</p>
            <p style="margin-bottom: 0;" ng-if="newContato.email4 != null"><b>Email 4:</b>{{ newContato.email4 }}</p>
            <p style="margin-bottom: 0;" ng-if="newContato.sobre != null"><b>Sobre:</b>{{ newContato.sobre }}</p>
            <p style="margin-bottom: 0;" ng-if="newContato.resumo_contato != null"><b>Resumo:</b>{{ newContato.resumo_contato }}</p>
            <p style="margin-bottom: 0;" ng-if="newContato.id_empresa != null"><b>Id empresa:</b>{{ newContato.id_empresa }}</p>

            <p style="margin-bottom: 0;" ng-if="newContato.inicio_empresa != null"><b>Inicio empresa:</b>{{ newContato.inicio_empresa }}</p>

            <p style="margin-bottom: 0;" ng-if="newContato.data_conexao_linkedin_contato != null"><b>Data conexão linkedin:</b>{{ newContato.data_conexao_linkedin_contato }}</p>
            <p style="margin-bottom: 0;" ng-if="newContato.conexoes_em_comum_contato != null"><b>Conexões em comum:</b>{{ newContato.conexoes_em_comum_contato }}</p>
            <p style="margin-bottom: 0;" ng-if="newContato.conexoes_no_linkedin_contato != null"><b>Conexões no linkedin:</b>{{ newContato.conexoes_no_linkedin_contato }}</p>

          </div>

          <div class="row" style="margin-top: 1em;">
            <div class="col-12">
              <md-input-container class="md-block" style="margin-left: 10px;margin-right: 10px;height: auto;">
                <label>Observação</label>
                <textarea ng-model="newContato.observacao" class="text-area-modal" style="background: none !important;"></textarea>
              </md-input-container>
            </div>
          </div>


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" ng-click="dimiss()">Fechar</button>
          <button type="button" class="btn btn-success" ng-click="salvarContato()">Salvar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalMarcaPerdido" style="z-index: 2000;">
    <div class="modal-dialog mdAngular">
      <div class="modal-content">
        <div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
          <button type="button" class="close" ng-click="dimiss()">
            <span aria-hidden="true">×</span> <span class="sr-only"><?php echo lang2('close') ?></span>
          </button>
          <b> Perder oportunidade</b>
        </div>
        <div id="modalBody" class="modal-body modal-person">
          <div class="row">
            <div class="col-md-12 col-12">
              <md-input-container class="md-block" style='margin-top: 8px;'>
                <label style="margin-bottom: 0;">Motivo</label>
                <md-select ng-model="perdeOportu.motivo" class="form-control select-modal">
                  <md-option value="Já tem fornecedor">Já tem fornecedor</md-option>
                  <md-option value="Pediu prazo">Pediu prazo</md-option>
                  <md-option value="Não retornou mais nossos contatos">Não retornou mais nossos contatos</md-option>
                  <md-option value="Não tem interesse">Não tem interesse</md-option>
                  <md-option value="Não tem perfil">Não tem perfil</md-option>
                  <md-option value="-1">Outro</md-option>
                </md-select>
              </md-input-container>
            </div>
          </div>

          <div class="row" ng-show="perdeOportu.motivo == '-1'" style='margin-top: 1em;'>
            <div class="col-md-12 col-12">
              <md-input-container class="md-block">
                <label>Qual?</label>
                <textarea ng-model="perdeOportu.outro" md-maxlength="500" rows="3" md-select-on-focus></textarea>
              </md-input-container>
            </div>
          </div>

          <div class="row" style="margin-top: 5px;">
            <div class="col-md-4 col-12">
              <md-input-container class="md-block">
                <label style="margin-bottom: 0;">Retornará no futuro?</label>
                <md-select ng-model="perdeOportu.retorn_futuro" class="form-control select-modal">
                  <md-option value="1">Sim</md-option>
                  <md-option value="0">Não</md-option>
                </md-select>
              </md-input-container>
            </div>

            <div class="col-md-4 col-12" ng-show="perdeOportu.retorn_futuro == 1" style="margin-top: 10px;">
              <md-input-container class="md-block">
                <label style="margin-bottom: 0;">Quando</label>
                <input ng-model="perdeOportu.quando" type="date">
              </md-input-container>
            </div>

            <div class="col-md-4 col-12" ng-show="perdeOportu.retorn_futuro == 1">
              <md-input-container class="md-block">
                <label style="margin-bottom: 0;">Qual etapa?</label>
                <md-select ng-model="perdeOportu.etapa" class="form-control select-modal">
                  <md-option ng-repeat="status in lead.status_list" ng-value="status.id">{{status.name}}</md-option>
                </md-select>
              </md-input-container>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" ng-click="dimiss()">Fechar</button>
          <button type="button" class="btn btn-success" ng-click="MarkLeadAs(MarkLeadAsVal, perdeOportu)">Salvar</button>
        </div>
      </div>
    </div>
  </div>



  <div class="modal fade" id="modalRespostaIa" style="z-index: 2000;">
    <div class="modal-dialog mdAngular" style="margin-top: 0;">
      <div class="modal-content" style="max-height: calc(100% - 75px);">
        <div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
          <button type="button" class="close" ng-click="dimiss()">
            <span aria-hidden="true">×</span> <span class="sr-only"><?php echo lang2('close') ?></span>
          </button>
          <b>Gerar resposta Ia</b>
        </div>
        <div id="modalBody" class="modal-body" style="max-height: 70vh; overflow: auto; overflow-x: hidden;">
          <div class="row">
            <md-input-container class="md-block" style="height: auto;">

              <div ng-show="leadRespostaIa" layout-align="center center" class="text-center" id="circular_loader">
                <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">

                <p style="font-size: 15px;margin-bottom: 5%;">
                  <span>
                    <?php echo lang2('please_wait') ?> <br>
                    <small><strong><?php echo lang2('loading') . '...' ?></strong></small>
                  </span>
                </p>
              </div>


              <textarea style = "overflow: hidden !important;" id="body_resposta" ng-model="respostaIa.message"></textarea>

              <button type="button" class="btn btn-secondary" style="float: left;margin-top: 5px;border: 1px solid #e1e1e1;padding: 8px 12px;font-size: 12px;" ng-click="copyRespostaIa()"><i class="far fa-copy"></i> Copiar</button>

            </md-input-container>
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="modal fade" id="modalEnviaEmail" style="z-index: 2000;">
    <div class="modal-dialog mdAngular modal-lg" style="margin-top: 0;">
      <div class="modal-content" style="max-height: calc(100% - 75px);">
        <div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
          <button type="button" class="close" ng-click="dimiss()">
            <span aria-hidden="true">×</span> <span class="sr-only"><?php echo lang2('close') ?></span>
          </button>
          <b>Enviar email</b>

          <md-button style="margin-top: -3px;float: right;margin-right: 50px;" class=" btn btn-success" ng-click="geraRespostaIa()">
            <i class="fas fa-robot"></i>
            Gerar resposta com IA
          </md-button>

        </div>
        <div id="modalBody" class="modal-body" style="max-height: 70vh; overflow: auto; overflow-x: hidden;">
          <div class="row">
            <md-input-container class="md-block">
              <label><?php echo lang2('email') . ' ' . lang2('subject') ?></label>
              <input required type="text" ng-model="template.subject" class="form-control" placeholder="<?php echo lang2('subject'); ?>" />
            </md-input-container>

            <md-input-container class="md-block">
              <label><?php echo lang2('email') ?></label>
              <input required type="text" ng-model="template.email" class="form-control" placeholder="<?php echo lang2('Email'); ?>" />
            </md-input-container>

            <md-input-container class="md-block">
              <label><?php echo lang2('cc') ?></label>
              <input type="text" ng-model="template.cc" class="form-control" placeholder="<?php echo lang2('cc'); ?>" />
            </md-input-container>

            <md-input-container class="md-block">
              <label><?php echo lang2('cco') ?></label>
              <input type="text" ng-model="template.cco" class="form-control" placeholder="<?php echo lang2('cco'); ?>" />
            </md-input-container>


            <md-input-container class="md-block" style="height: auto;">

              <textarea id="email_body" ng-model="template.message"></textarea>
            </md-input-container>

            <small><b>Variáveis:</b>
              <a href="javaScript:void(0)" ng-click="addVar('{email}')">{email}</a> ,
              <a href="javaScript:void(0)" ng-click="addVar('{empresa}')">{empresa}</a>,
              <a href="javaScript:void(0)" ng-click="addVar('{nome}')">{nome}</a>,
            </small>

            <md-input-container class="md-block" style="margin-top: 25px;">
              <label style="margin-bottom: 17px;">Anexo</label><br>
              <input type="file" ng-model="template.anexo" id="anexo" name="anexo" file-model="anexo">
            </md-input-container>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" ng-click="dimiss()">Fechar</button>
          <button type="button" class="btn btn-success" ng-click="enviaEmail()">Enviar</button>
        </div>
      </div>
    </div>
  </div>

  <!--
  <ciuis-sidebar>

  </ciuis-sidebar>

      -->

  <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Update" ng-cloak style="width: 450px;">
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
            <!-- <md-icon md-svg-src="<?php echo base_url('assets/img/icons/individual.svg') ?>" aria-label="Add Source">
              </md-icon> -->
            <input required name="name" ng-model="lead.name">
          </md-input-container>

          <md-input-container class="md-block" ng-if="lead.tp_pessoa == 2">
            <label><?php echo lang2('company'); ?></label>
            <!--  <md-icon md-svg-src="<?php echo base_url('assets/img/icons/company.svg') ?>" aria-label="Add Source">
              </md-icon> -->
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

            <input type="date" id="dt_nascimento" ng-model="lead.dt_nascimento" class=" dtp-no-msclear dtp-input md-input">

          </md-input-container>

          <md-input-container class="md-block" ng-if="lead.tp_pessoa == 2">
            <label>Porte</label>
            <input name="porte" ng-model="lead.porte">
          </md-input-container>

          <md-content style="padding: 5px 0;">
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

          <md-input-container class="md-block" style="height: auto;">
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

          <md-input-container class="md-block" style="margin-top: 1em;">
            <label><?php echo lang2('state'); ?></label>
            <md-select placeholder="<?php echo lang2('states'); ?>" ng-model="lead.state_id" name="state_id" style="min-width: 200px;">
              <md-option ng-value="state.id" ng-repeat="state in states">{{state.state_name}}</md-option>
            </md-select>
          </md-input-container>


          <md-input-container class="md-block " style="margin-top: 1em;">
            <label>Lead funil</label>
            <md-select placeholder="" ng-model="lead.funil_list" style="min-width: 200px;">
              <md-option ng-value="list.id_list" ng-repeat="list in leadslist">{{list.nm_list}}</md-option>
            </md-select>
          </md-input-container>

          <md-input-container class="md-block" style="margin-top: 1em;">
            <label><?php echo lang2('status'); ?></label>
            <md-select placeholder="<?php echo lang2('status'); ?>" ng-model="lead.status_id" style="min-width: 200px;">
              <md-option ng-value="status.id" ng-repeat="status in leadslist[lead.funil_list].leadstatuses">{{status.name}}</md-option>
            </md-select>
          </md-input-container>

          <md-input-container class="md-block" style="margin-top: 1em;">
            <label>Temperatura</label>
            <md-select placeholder="Temperatura" ng-model="lead.temperatura" style="min-width: 200px;">
              <md-option ng-value="1">Quente</md-option>
              <md-option ng-value="2">Morno</md-option>
              <md-option ng-value="3">Frio</md-option>
            </md-select>
          </md-input-container>


          <md-input-container class="md-block" style="margin-top: 1em;">
            <label><?php echo lang2('assigned'); ?></label>
            <md-select placeholder="<?php echo lang2('choosestaff'); ?>" ng-model="lead.assigned_id" style="min-width: 200px;">
              <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
            </md-select>
          </md-input-container>

          <md-input-container class="md-block" style="margin-top: 1em;">
            <label><?php echo lang2('Closer'); ?></label>
            <md-select placeholder="<?php echo lang2('Closer'); ?>" ng-model="lead.closer" style="min-width: 200px;">
              <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
            </md-select>
          </md-input-container>

          <md-input-container class="md-block" style="margin-top: 1em;">
            <label><?php echo lang2('source'); ?></label>
            <md-select placeholder="<?php echo lang2('source'); ?>" ng-model="lead.source_id" style="min-width: 200px;">
              <md-option ng-value="source.id" ng-repeat="source in sources">{{source.name}}</md-option>
            </md-select>
          </md-input-container>

          <md-input-container class="md-block" style="margin-top: 1em;margin-bottom: 4em !important;">
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

      <md-content>
        <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>
          <md-button ng-click="UpdateLead()" class="template-button" ng-disabled="saving == true">
            <span ng-hide="saving == true"><?php echo lang2('update'); ?></span>
            <md-progress-circular class="white" ng-show="saving == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>
          </md-button>
        </section>
      </md-content>
    </md-content>
  </md-sidenav>

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

        <section layout="row" layout-sm="column" layout-wrap class="pull-right">

          <md-button ng-click="AddReminder()" class="md-raised md-primary"><?php echo lang2('add'); ?></md-button>

        </section>

      </md-content>
    </md-content>
  </md-sidenav>



  <div class="modal fade" id="modalInfo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Instruções</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="modalInfoContent">

        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalPausa" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
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
            <md-select ng-model="modal_pausa.motivo">
              <md-option ng-value="motivo.name" ng-repeat="motivo in motivos">{{motivo.name}}</md-option>
            </md-select>
          </md-input-container>
        </div>
        <div class="modal-footer" style="margin-top: 30px;">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
          <button type="button" class="btn btn-success" ng-click="salva_pausa()">Salvar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalEmPausa" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Em pausa</h5>
        </div>
        <div class="modal-body">
          <h4 style="text-align: center;margin: 0;font-weight: 400;">Tempo em pausa <b>{{ displayTime }}</b></h4>
        </div>
        <div class="modal-footer" style="margin-top: 30px;">
          <button type="button" class="btn btn-success" ng-click="togglePlayPause()">Retomar</button>
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


  <!--

  <div class="modal fade" id="modalLeadData" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Dados adicionais</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <div class="accordion" id="accordionExample">
            <div class="card">
              <div class="card-header" id="headingThree">
                <h3 class="btn btn-link collapsed h3Title" data-toggle="collapse" data-target="#collapseInfo" aria-expanded="false" aria-controls="collapseInfo">
                  Informações
                </h3>
              </div>
              <div id="collapseInfo" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                <div class="col-md-6 col-12">
                  <md-input-container class="md-block">
                    <label>Modelo fiscal</label>
                    <input ng-model="lead.leads_data.Company.TaxModel" type="text">
                  </md-input-container>
                </div>

                <div class="col-md-6 col-12">
                  <md-input-container class="md-block">
                    <label>Tamanho da empresa</label>
                    <input ng-model="lead.leads_data.Company.BusinessSize" type="text">
                  </md-input-container>
                </div>

                <div class="col-md-6 col-12">
                  <md-input-container class="md-block">
                    <label>Número de sócios</label>
                    <input ng-model="lead.leads_data.Company.TotalPartners" type="text">
                  </md-input-container>
                </div>

                <div class="col-md-6 col-12">
                  <md-input-container class="md-block">
                    <label>Total de empresas</label>
                    <input ng-model="lead.leads_data.Company.TotalCompanyPartners" type="text">
                  </md-input-container>
                </div>

                <h5 class="h5Title">Atividade</h5>
                <div class="row" ng-repeat="(i, active) in lead.leads_data.Activities" style="margin-bottom: 30px;border-bottom: 1px solid #eee;padding-bottom: 10px;">
                  <div class="col-md-9 col-12">
                    <md-input-container class="md-block">
                      <label>Descrição cnae {{active.IsPrimary != null && active.IsPrimary ? '(Primário)' : ''}}</label>
                      <input ng-model="active.Description" type="text">
                    </md-input-container>
                  </div>

                  <div class="col-md-3 col-12">
                    <md-input-container class="md-block">
                      <label>Código</label>
                      <input ng-model="active.Code" type="text">
                    </md-input-container>
                  </div>
                </div>




              </div>
            </div>

            <div class="card">
              <div class="card-header" id="headingThree">
                <h3 class="btn btn-link collapsed h3Title" data-toggle="collapse" data-target="#collapseTelefone" aria-expanded="false" aria-controls="collapseThree">
                  Telefones
                </h3>
              </div>
              <div id="collapseTelefone" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                <h5 class="h5Title">Telefones fixos</h5>
                <div class="row" ng-repeat="(i, telefone) in lead.leads_data.Phones" ng-if="!telefone.IsMobile" style="margin-bottom: 30px;border-bottom: 1px solid #eee;padding-bottom: 10px;">
                  <div class="col-md-3 col-12">
                    <md-input-container class="md-block">
                      <label>Numero</label>
                      <input ng-model="telefone.FormattedNumber" type="text">
                    </md-input-container>
                  </div>

                  <div class="col-md-3 col-12">
                    <md-input-container class="md-block">
                      <label>É facebook?</label>
                      <md-select ng-model="telefone.IsFacebook" class="form-control select-modal">
                        <md-option ng-value="opcao.v" ng-repeat="opcao in [{nm : 'Sim', v:true}, {nm : 'Não', v :false}]">{{opcao.nm}}</md-option>
                      </md-select>
                    </md-input-container>
                  </div>

                  <div class="col-md-3 col-12">
                    <md-input-container class="md-block">
                      <label>É WhatsApp?</label>
                      <md-select ng-model="telefone.IsWhatsapp" class="form-control select-modal">
                        <md-option ng-value="opcao.v" ng-repeat="opcao in [{nm : 'Sim', v:true}, {nm : 'Não', v :false}]">{{opcao.nm}}</md-option>
                      </md-select>
                    </md-input-container>
                  </div>

                  <div class="col-md-3 col-12">
                    <md-input-container class="md-block">
                      <label>É Procon?</label>
                      <md-select ng-model="telefone.IsProcon" class="form-control select-modal">
                        <md-option ng-value="opcao.v" ng-repeat="opcao in [{nm : 'Sim', v:true}, {nm : 'Não', v :false}]">{{opcao.nm}}</md-option>
                      </md-select>
                    </md-input-container>
                  </div>
                </div>

                <h5 class="h5Title">Telefones celular</h5>
                <div class="row" ng-repeat="(i, telefone) in lead.leads_data.Phones" ng-if="telefone.IsMobile" style="margin-bottom: 30px;border-bottom: 1px solid #eee;padding-bottom: 10px;">
                  <div class="col-md-4 col-12">
                    <md-input-container class="md-block">
                      <label>Numero</label>
                      <input ng-model="telefone.FormattedNumber" type="text">
                    </md-input-container>
                  </div>

                  <div class="col-md-3 col-12">
                    <md-input-container class="md-block">
                      <label>É facebook?</label>
                      <md-select ng-model="telefone.IsFacebook" class="form-control select-modal">
                        <md-option ng-value="opcao.v" ng-repeat="opcao in [{nm : 'Sim', v:true}, {nm : 'Não', v :false}]">{{opcao.nm}}</md-option>
                      </md-select>
                    </md-input-container>
                  </div>

                  <div class="col-md-3 col-12">
                    <md-input-container class="md-block">
                      <label>É WhatsApp?</label>
                      <md-select ng-model="telefone.IsWhatsapp" class="form-control select-modal">
                        <md-option ng-value="opcao.v" ng-repeat="opcao in [{nm : 'Sim', v:true}, {nm : 'Não', v :false}]">{{opcao.nm}}</md-option>
                      </md-select>
                    </md-input-container>
                  </div>

                  <div class="col-md-3 col-12">
                    <md-input-container class="md-block">
                      <label>É Procon?</label>
                      <md-select ng-model="telefone.IsProcon" class="form-control select-modal">
                        <md-option ng-value="opcao.v" ng-repeat="opcao in [{nm : 'Sim', v:true}, {nm : 'Não', v :false}]">{{opcao.nm}}</md-option>
                      </md-select>
                    </md-input-container>
                  </div>
                </div>

              </div>
            </div>

            <div class="card">
              <div class="card-header" id="headingThree">
                <h3 class="btn btn-link collapsed h3Title" data-toggle="collapse" data-target="#collapseEmail" aria-expanded="false" aria-controls="collapseThree">
                  E-mails
                </h3>
              </div>
              <div id="collapseEmail" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">


                <div class="row" ng-repeat="(i, email) in lead.leads_data.Emails" style="margin-bottom: 30px;border-bottom: 1px solid #eee;padding-bottom: 10px;">
                  <div class="col-md-6 col-12">
                    <md-input-container class="md-block">
                      <label>Email</label>
                      <input ng-model="email.Email" type="text">
                    </md-input-container>
                  </div>

                  <div class="col-md-3 col-12">
                    <md-input-container class="md-block">
                      <label>É facebook?</label>
                      <md-select ng-model="email.IsFacebook" class="form-control select-modal">
                        <md-option ng-value="opcao.v" ng-repeat="opcao in [{nm : 'Sim', v:true}, {nm : 'Não', v :false}]">{{opcao.nm}}</md-option>
                      </md-select>
                    </md-input-container>
                  </div>

                  <div class="col-md-3 col-12">
                    <md-input-container class="md-block">
                      <label>É linkedin?</label>
                      <md-select ng-model="email.IsLinkedIn" class="form-control select-modal">
                        <md-option ng-value="opcao.v" ng-repeat="opcao in [{nm : 'Sim', v:true}, {nm : 'Não', v :false}]">{{opcao.nm}}</md-option>
                      </md-select>
                    </md-input-container>
                  </div>

                  <div class="col-md-3 col-12">
                    <md-input-container class="md-block">
                      <label>Presença digital?</label>
                      <md-select ng-model="email.IsDigitalBehavior" class="form-control select-modal">
                        <md-option ng-value="opcao.v" ng-repeat="opcao in [{nm : 'Sim', v:true}, {nm : 'Não', v :false}]">{{opcao.nm}}</md-option>
                      </md-select>
                    </md-input-container>
                  </div>

                  <div class="col-md-3 col-12">
                    <md-input-container class="md-block">
                      <label>E-mail de sócio?</label>
                      <md-select ng-model="email.IsFromPartner" class="form-control select-modal">
                        <md-option ng-value="opcao.v" ng-repeat="opcao in [{nm : 'Sim', v:true}, {nm : 'Não', v :false}]">{{opcao.nm}}</md-option>
                      </md-select>
                    </md-input-container>
                  </div>

                  <div class="col-md-3 col-12">
                    <md-input-container class="md-block">
                      <label>Score</label>
                      <input ng-model="email.Score" type="text">
                    </md-input-container>
                  </div>
                </div>

              </div>
            </div>

            <div class="card">
              <div class="card-header" id="headingThree">
                <h3 class="btn btn-link collapsed h3Title" data-toggle="collapse" data-target="#collapseSocios" aria-expanded="false" aria-controls="collapseThree">
                  Sócios & pessoas ligadas
                </h3>
              </div>
              <div id="collapseSocios" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">

                <h5 class="h5Title">Pessoas ligadas</h5>
                <div class="row" ng-repeat="(i, pessoa) in lead.leads_data.Relateds" style="margin-bottom: 30px;border-bottom: 1px solid #eee;padding-bottom: 10px;">
                  <div class="col-md-8 col-12">
                    <md-input-container class="md-block">
                      <label>Nome</label>
                      <input ng-model="pessoa.Name" type="text">
                    </md-input-container>
                  </div>

                  <div class="col-md-4 col-12">
                    <md-input-container class="md-block">
                      <label>Cpf</label>
                      <input ng-model="pessoa.Document" type="text">
                    </md-input-container>
                  </div>
                </div>

                <h5 class="h5Title">Sócios</h5>
                <div class="row" ng-repeat="(i, socios) in lead.leads_data.PartnerShips" ng-if="socios.Status == '1'" style="margin-bottom: 30px;border-bottom: 1px solid #eee;padding-bottom: 10px;">
                  <div class="col-md-8 col-12">
                    <md-input-container class="md-block">
                      <label>Nome</label>
                      <input ng-model="socios.Name" type="text">
                    </md-input-container>
                  </div>

                  <div class="col-md-4 col-12">
                    <md-input-container class="md-block">
                      <label>Cpf</label>
                      <input ng-model="socios.Document" type="text">
                    </md-input-container>
                  </div>
                </div>
              </div>
            </div>

            <div class="card">
              <div class="card-header" id="headingThree">
                <h3 class="btn btn-link collapsed h3Title" data-toggle="collapse" data-target="#collapseEnderecos" aria-expanded="false" aria-controls="collapseThree">
                  Endereços
                </h3>
              </div>
              <div id="collapseEnderecos" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                <div class="row" ng-repeat="(i, endereco) in lead.leads_data.Addresses" style="margin-bottom: 30px;border-bottom: 1px solid #eee;padding-bottom: 10px;">
                  <div class="col-md-6 col-12">
                    <md-input-container class="md-block">
                      <label>Rua</label>
                      <input ng-model="endereco.Street" type="text">
                    </md-input-container>
                  </div>

                  <div class="col-md-3 col-12">
                    <md-input-container class="md-block">
                      <label>Numero</label>
                      <input ng-model="endereco.Number" type="text">
                    </md-input-container>
                  </div>

                  <div class="col-md-3 col-12">
                    <md-input-container class="md-block">
                      <label>Cep</label>
                      <input ng-model="endereco.ZipCode" type="text">
                    </md-input-container>
                  </div>

                  <div class="col-md-4 col-12">
                    <md-input-container class="md-block">
                      <label>Cidade</label>
                      <input ng-model="endereco.City" type="text">
                    </md-input-container>
                  </div>

                  <div class="col-md-4 col-12">
                    <md-input-container class="md-block">
                      <label>Estado</label>
                      <input ng-model="endereco.State" type="text">
                    </md-input-container>
                  </div>

                </div>
              </div>
            </div>

            <div class="card">
              <div class="card-header" id="headingThree">
                <h3 class="btn btn-link collapsed h3Title" data-toggle="collapse" data-target="#collapseInformacoesAdc" aria-expanded="false" aria-controls="collapseThree">
                  Informações adicionais
                </h3>
              </div>
              <div id="collapseInformacoesAdc" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                <h5 class="h5Title">Bens móveis</h5>
                <div class="row" ng-repeat="(i, bens) in lead.leads_data.Vehicles" ng-if="bens.Status == '1'" style="margin-bottom: 30px;border-bottom: 1px solid #eee;padding-bottom: 10px;">
                  <div class="col-md-4 col-12">
                    <md-input-container class="md-block">
                      <label>Placa</label>
                      <input ng-model="bens.LicensePlate" type="text">
                    </md-input-container>
                  </div>

                  <div class="col-md-3 col-12">
                    <md-input-container class="md-block">
                      <label>Renavan</label>
                      <input ng-model="bens.Renavan" type="text">
                    </md-input-container>
                  </div>

                  <div class="col-md-3 col-12">
                    <md-input-container class="md-block">
                      <label>Ano de fabricação</label>
                      <input ng-model="bens.YearManuFacturing" type="text">
                    </md-input-container>
                  </div>

                  <div class="col-md-3 col-12">
                    <md-input-container class="md-block">
                      <label>Ano modelo</label>
                      <input ng-model="bens.YearModel" type="text">
                    </md-input-container>
                  </div>

                  <div class="col-md-3 col-12">
                    <md-input-container class="md-block">
                      <label>Marca</label>
                      <input ng-model="bens.Brand" type="text">
                    </md-input-container>
                  </div>

                  <div class="col-md-3 col-12">
                    <md-input-container class="md-block">
                      <label>Modelo</label>
                      <input ng-model="bens.Model" type="text">
                    </md-input-container>
                  </div>

                  <div class="col-md-3 col-12">
                    <md-input-container class="md-block">
                      <label>Valor de mercado</label>
                      <input ng-model="bens.FipePrice" type="text">
                    </md-input-container>
                  </div>
                </div>

                <h5 class="h5Title">Score</h5>
                <div class="col-md-6 col-12">
                  <md-input-container class="md-block">
                    <label>Score de crédito</label>
                    <input ng-model="lead.leads_data.CreditScore.D90" type="text">
                  </md-input-container>
                </div>

                <div class="col-md-6 col-12">
                  <md-input-container class="md-block">
                    <label>Score de marketing</label>
                    <input ng-model="lead.leads_data.MarketingScore.D90" type="text">
                  </md-input-container>
                </div>

              </div>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal" style="font-size: 15px;padding: 10px 20px;">Fechar</button>
          <button type="button" class="btn btn-success" data-dismiss="modal" style="font-size: 15px;padding: 10px 20px;" ng-click="salva_busca()">Salvar</button>
        </div>
      </div>
    </div>
  </div>
  -->

</div>


<script>
  var LEADID = "<?php echo $lead['id']; ?>";
  var ACCOUNTID = <?= session()->usr_id ?>;
  var USERLOGADO = (<?= json_encode(session()->get()) ?>);

  var selectedIndex = <?= request()->getGet('page') ? request()->getGet('page') : 0 ?>;

  var lang = {};
  lang.doIt = "<?php echo lang2('doIt') ?>";
  lang.cancel = "<?php echo lang2('cancel') ?>";
  lang.attention = "<?php echo lang2('attention') ?>";
  lang.delete_lead = "<?php echo lang2('leadattentiondetail') ?>";
  lang.convert_title = "<?php echo lang2('convert') . ' ' . lang2('lead') . ' ' . lang2('to') . ' ' . lang2('customer') ?>";
  lang.convert_text = "<?php echo lang2('convertmsg') . ' ' . lang2('lead') . ' ' . lang2('to') . ' ' . lang2('customer') ?>";
  lang.convert = "<?php echo lang2('convert') ?>";
</script>
<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>
<script src="<?php echo base_url('assets/js/leads.js?v=l1.8.106'); ?>"></script>
<script src="<?php echo base_url('assets/lib/tinymce/tinymce.min.js') ?>"></script>
<script>
  tinymce.init({
    selector: '#email_body',
    theme: 'modern',
    content_style: "body p, h1, h2, h3, h4, h5, h6{margin: 2px 5px;}",
    editor_selector: "mceEditor",
    theme: 'modern',
    valid_elements: '*',
    valid_styles: '*',
    plugins: 'print preview searchreplace autoresize autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking importcss anchor code insertdatetime advlist lists textcolor wordcount imagetools  contextmenu colorpicker textpattern',
    valid_children: "+body[style]",
    valid_elements: "@[id|class|title|style]," +
      "a[name|href|target|title|alt]," +
      "#p,-ol,,div,h1,h2,h3,h4,h5,h6,strong,-ul,-li,br,img[src|unselectable],-sub,-sup,-b,-i,-u," +
      "-span[data-mce-type],hr",

    valid_child_elements: "body[p,ol,ul,div,h1,h2,h3,h4,h5,h6,strong,b]" + ",p[a|span|b|i|u|sup|sub|img|hr|#text]" +
      ",span[a|b|i|u|sup|sub|img|#text]" + ",a[span|b|i|u|sup|sub|img|#text]" + ",b[span|a|i|u|sup|sub|img|#text]" +
      ",i[span|a|b|u|sup|sub|img|#text]" + ",sup[span|a|i|b|u|sub|img|#text]" + ",sub[span|a|i|b|u|sup|img|#text]" +
      ",li[span|a|b|i|u|sup|sub|img|ol|ul|#text]" + ",ol[li]" + ",ul[li]",
    toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat'
  });


  tinymce.init({
    selector: '#anotacoesAtv',
    theme: 'modern',
    content_style: "body p, h1, h2, h3, h4, h5, h6{margin: 2px 5px;}",
    editor_selector: "mceEditor",
    theme: 'modern',
    valid_elements: '*',
    valid_styles: '*',
    plugins: 'print preview searchreplace autoresize autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking importcss anchor code insertdatetime advlist lists textcolor wordcount imagetools  contextmenu colorpicker textpattern',
    valid_children: "+body[style]",
    valid_elements: "@[id|class|title|style]," +
      "a[name|href|target|title|alt]," +
      "#p,-ol,,div,h1,h2,h3,h4,h5,h6,strong,-ul,-li,br,img[src|unselectable],-sub,-sup,-b,-i,-u," +
      "-span[data-mce-type],hr",

    valid_child_elements: "body[p,ol,ul,div,h1,h2,h3,h4,h5,h6,strong,b]" + ",p[a|span|b|i|u|sup|sub|img|hr|#text]" +
      ",span[a|b|i|u|sup|sub|img|#text]" + ",a[span|b|i|u|sup|sub|img|#text]" + ",b[span|a|i|u|sup|sub|img|#text]" +
      ",i[span|a|b|u|sup|sub|img|#text]" + ",sup[span|a|i|b|u|sub|img|#text]" + ",sub[span|a|i|b|u|sup|img|#text]" +
      ",li[span|a|b|i|u|sup|sub|img|ol|ul|#text]" + ",ol[li]" + ",ul[li]",
    toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat'
  });
</script>