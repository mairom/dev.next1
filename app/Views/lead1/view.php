<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<?php $appconfig = get_appconfig(); ?>

<style>
    .user-display-avatar {
        top: auto;
        bottom: 10px;
        border: 0;
    }

    .user-display-avatar img {
        border: 0;
        box-shadow: 0px 0px 4px 0px #fff;
        width: 130px;
        height: 130px;
        padding: 5px;
    }

    .user-display-info {
        padding-left: 150px;
    }

    .user-display-bottom {
        position: absolute;
        bottom: 0;
        width: 100%;
    }

    .user-display-info {
        padding-left: 150px;
        background: linear-gradient(90deg, #31bfba 80%, transparent 100%);
        border-radius: 5px;
        padding-top: 5px;
        padding-bottom: 5px;
        color: #fff;
    }

    .user-display-info .nick {
        color: #fff;
    }

    .user-display-info .name {
        font-weight: 600;
        margin-bottom: 25px;
        margin-top: 10px;
        font-size: 25px;
    }

    .backComp {
        height: 300px;
        width: 100%;
        background-size: 100%;
        background-repeat: no-repeat;
        background-position: center;
        border-radius: 5px;
    }

    @media only screen and (max-width: 600px) {
        .flex-md-50 {
            flex: 1 !important;
        }

        .layout,
        .layout-column,
        .layout-row {
            display: block;
        }

        .backComp {
            background-size: auto 100%;
        }
    }
</style>
<div class="ciuis-body-content" ng-controller="Lead1View_Controller">
    <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">
        <md-content ng-show="!staffLoader" class="bg-white user-profile">
            <div class="col-md-12 user-display">
                <div class="user-display-bg" style="position: relative;">
                    <div class="backComp" style="background-image: url('{{lead1.url_background}}')"></div>

                    <div class="user-display-bottom">
                        <div class="user-display-avatar">
                            <img id="imgLogo" ng-src="{{lead1.logo_url}}">
                        </div>
                        <div class="user-display-info">
                            <div class="name" ng-bind="lead1.razao_social"></div>
                        </div>
                    </div>
                </div>
                <div layout="row" class="ng-scope layout-row">
                    <md-content class="bg-white flex-md-50 flex-100">
                        <md-list flex class="md-p-0 sm-p-0 lg-p-0">
                            <h4 style="text-align: center;font-weight: 600;">Informações</h4>
                            <md-divider></md-divider>

                            <md-list-item>
                                <md-icon><i class="fas fa-building"></i></md-icon>
                                <strong flex md-truncate>Razão Social </strong>
                                <p class="text-right" flex md-truncate ng-bind="lead1.razao_social"></p>
                            </md-list-item>
                            <md-divider></md-divider>

                            <md-list-item>
                                <md-icon></md-icon>
                                <strong flex md-truncate>Nome fantasia</strong>
                                <p class="text-right" flex md-truncate ng-bind="lead1.razao_social"></p>
                            </md-list-item>
                            <md-divider></md-divider>

                            <md-list-item>
                                <md-icon></md-icon>
                                <strong flex md-truncate>Cnpj</strong>
                                <p class="text-right" flex md-truncate ng-bind="lead1.cnpj"></p>
                            </md-list-item>
                            <md-divider></md-divider>

                            <md-list-item>
                                <md-icon class="mdi mdi-nature-people"></md-icon>
                                <strong flex md-truncate>Website</strong>
                                <p class="text-right" flex md-truncate ng-bind="lead1.website"></p>
                            </md-list-item>
                            <md-divider></md-divider>

                            <md-list-item>
                                <md-icon class="mdi mdi-markunread-mailbox"></md-icon>
                                <strong flex md-truncate>Setor de atividade</strong>
                                <p style="overflow: hidden; text-align: right;text-overflow: ellipsis;line-height: 18px;max-height: 290px;white-space: unset;padding: 10px 0px;" class="text-right" flex md-truncate ng-bind="lead1.ramo_de_atividade"></p>
                            </md-list-item>
                            <md-divider></md-divider>

                            <md-list-item>
                                <md-icon></md-icon>
                                <strong flex md-truncate>CNAE Primário</strong>
                                <p style="overflow: hidden;text-align: right;text-overflow: ellipsis;line-height: 18px;max-height: 290px;white-space: unset;padding: 10px 0px;" class="text-right" flex md-truncate ng-bind="lead1.cnae"></p>
                            </md-list-item>
                            <md-divider></md-divider>

                            <md-list-item>
                                <md-icon></md-icon>
                                <strong flex md-truncate>CNAE Secundário</strong>
                                <p class="text-right" flex md-truncate ng-bind="lead1.cnaes_secundarios"></p>
                            </md-list-item>
                            <md-divider></md-divider>

                            <md-list-item>
                                <md-icon class="mdi"><i class="fas fa-info-circle"></i></md-icon>
                                <strong flex md-truncate><?php echo lang2('status') ?></strong>
                                <p class="text-right" flex md-truncate ng-bind="lead1.situacao"></p>
                            </md-list-item>
                            <md-divider></md-divider>

                            <md-list-item>
                                <md-icon></md-icon>
                                <strong flex md-truncate>Natureza</strong>
                                <p class="text-right" flex md-truncate ng-bind="lead1.natureza_juridica"></p>
                            </md-list-item>

                            <h4 style="text-align: center;font-weight: 600;">Dados</h4>

                            <md-list-item>
                                <md-icon class="mdi"><i class="fas fa-calendar-exclamation"></i></md-icon>
                                <strong flex md-truncate>Abertura</strong>
                                <p class="text-right" flex md-truncate ng-bind="lead1.aberturaBr"></p>
                            </md-list-item>
                            <md-divider></md-divider>


                            <md-list-item>
                                <md-icon></md-icon>
                                <strong flex md-truncate>Optante pelo Simples </strong>
                                <p class="text-right" flex md-truncate ng-bind="lead1.simples_nacional"></p>
                            </md-list-item>
                            <md-divider></md-divider>

                            <md-list-item>
                                <md-icon></md-icon>
                                <strong flex md-truncate>Mei </strong>
                                <p class="text-right" flex md-truncate ng-bind="lead1.mei"></p>
                            </md-list-item>
                            <md-divider></md-divider>

                            <md-list-item>
                                <md-icon></md-icon>
                                <strong flex md-truncate>Anos de Mercado </strong>
                                <p class="text-right" flex md-truncate ng-bind="lead1.anos_abertura"></p>
                            </md-list-item>
                            <md-divider></md-divider>

                            <md-list-item>
                                <md-icon></md-icon>
                                <strong flex md-truncate>Capital Social </strong>
                                <p class="text-right" flex md-truncate ng-bind-html="lead1.capital_social | currencyFormat:cur_code:null:true:cur_lct"></p>
                            </md-list-item>
                            <md-divider></md-divider>

                            <md-list-item>
                                <md-icon></md-icon>
                                <strong flex md-truncate>Porte </strong>
                                <p class="text-right" flex md-truncate ng-bind="lead1.porte"></p>
                            </md-list-item>
                            <md-divider></md-divider>

                            <md-list-item>
                                <md-icon></md-icon>
                                <strong flex md-truncate>Faturamento Presumido </strong>
                                <p class="text-right" flex md-truncate ng-bind="lead1.faturamento_presumido"></p>
                            </md-list-item>
                            <md-divider></md-divider>

                            <md-list-item>
                                <md-icon></md-icon>
                                <strong flex md-truncate>Número de Funcionários </strong>
                                <p class="text-right" flex md-truncate ng-bind="lead1.numero_funcionarios"></p>
                            </md-list-item>
                            <md-divider></md-divider>

                            <md-list-item>
                                <md-icon></md-icon>
                                <strong flex md-truncate>Descrição</strong>
                                <p class="text-right" style="overflow: hidden; text-overflow: ellipsis; line-height: 18px; max-height: 290px; white-space: unset;padding: 10px 0px;" flex md-truncate ng-bind="lead1.descricao"></p>
                            </md-list-item>
                            <md-divider></md-divider>
                        </md-list>
                    </md-content>
                    <md-content class="bg-white flex-md-50 flex-100" style="border-left: 1px solid #e9e9e9;">
                        <md-list flex class="md-p-0 sm-p-0 lg-p-0">

                            <h4 style="text-align: center;font-weight: 600;">Contatos</h4>

                            <md-divider></md-divider>
                            <md-list-item>
                                <md-icon class="mdi ion-location"></md-icon>
                                <strong flex md-truncate>Endereço </strong>
                                <p class="text-right" style="overflow: hidden; text-overflow: ellipsis; line-height: 18px; max-height: 290px; white-space: unset;padding: 10px 0px;" flex md-truncate ng-bind="lead1.tipo_logradouro + ' ' + lead1.logradouro + ',' + lead1.numero "></p>
                            </md-list-item>
                            <md-divider></md-divider>

                            <md-list-item>
                                <md-icon class="mdi ion-location"></md-icon>
                                <strong flex md-truncate>Bairro </strong>
                                <p class="text-right" style="overflow: hidden; text-overflow: ellipsis; line-height: 18px; max-height: 290px; white-space: unset;padding: 10px 0px;" flex md-truncate ng-bind="lead1.bairro"></p>
                            </md-list-item>
                            <md-divider></md-divider>

                            <md-list-item>
                                <md-icon class="mdi ion-location"></md-icon>
                                <strong flex md-truncate>Cep </strong>
                                <p class="text-right" flex md-truncate ng-bind="lead1.cep"></p>
                            </md-list-item>
                            <md-divider></md-divider>

                            <md-list-item>
                                <md-icon class="mdi ion-location"></md-icon>
                                <strong flex md-truncate>Cidade </strong>
                                <p class="text-right" flex md-truncate ng-bind="lead1.municipio"></p>
                            </md-list-item>
                            <md-divider></md-divider>

                            <md-list-item>
                                <md-icon class="mdi ion-location"></md-icon>
                                <strong flex md-truncate>Estado </strong>
                                <p class="text-right" flex md-truncate ng-bind="lead1.uf"></p>
                            </md-list-item>
                            <md-divider></md-divider>


                            <div ng-if="lead1.telefone1 != null && lead1.telefone1 != ''">
                                <md-list-item>
                                    <md-icon class="ion-android-call"></md-icon>
                                    <strong flex md-truncate>Telefone 01 </strong>
                                    <p class="text-right" flex md-truncate ng-bind="'(' + lead1.ddd1 + ') ' + lead1.telefone1"></p>

                                    <a style="margin-left: 10px;font-size: 15px;color: #075bf5;font-weight: 600;" href="tel:+55{{lead1.ddd1 + lead1.telefone1}}"> Ligar... <i class="fas fa-phone-square-alt"></i></a>
                                </md-list-item>
                                <md-divider></md-divider>
                            </div>

                            <div ng-if="lead1.telefone2 != null && lead1.telefone2 != ''">
                                <md-list-item>
                                    <md-icon class="ion-android-call"></md-icon>
                                    <strong flex md-truncate>Telefone 02 </strong>
                                    <p class="text-right" flex md-truncate ng-bind="'(' + lead1.ddd2 + ') ' + lead1.telefone2"></p>
                                    <a style="margin-left: 10px;font-size: 15px;color: #075bf5;font-weight: 600;" href="tel:+55{{lead1.ddd2 + lead2.telefone1}}"> Ligar... <i class="fas fa-phone-square-alt"></i></a>

                                </md-list-item>
                                <md-divider></md-divider>
                            </div>

                            <div ng-if="lead1.telefone3 != null && lead1.telefone3 != ''">
                                <md-list-item>
                                    <md-icon class="ion-android-call"></md-icon>
                                    <strong flex md-truncate>Telefone 03 </strong>
                                    <p class="text-right" flex md-truncate ng-bind="'(' + lead1.ddd3 + ') ' + lead1.telefone3"></p>
                                    <a style="margin-left: 10px;font-size: 15px;color: #075bf5;font-weight: 600;" href="tel:+55{{lead1.ddd3 + lead1.telefone3}}"> Ligar... <i class="fas fa-phone-square-alt"></i></a>

                                </md-list-item>
                                <md-divider></md-divider>
                            </div>

                            <div ng-if="lead1.telefone4 != null && lead1.telefone4 != ''">
                                <md-list-item>
                                    <md-icon class="ion-android-call"></md-icon>
                                    <strong flex md-truncate>Telefone 04 </strong>
                                    <p class="text-right" flex md-truncate ng-bind="'(' + lead1.ddd4 + ') ' + lead1.telefone4"></p>
                                    <a style="margin-left: 10px;font-size: 15px;color: #075bf5;font-weight: 600;" href="tel:+55{{lead1.ddd4 + lead1.telefone4}}"> Ligar... <i class="fas fa-phone-square-alt"></i></a>

                                </md-list-item>
                                <md-divider></md-divider>
                            </div>

                            <div ng-if="lead1.telefone5 != null && lead1.telefone5 != ''">
                                <md-list-item>
                                    <md-icon class="ion-android-call"></md-icon>
                                    <strong flex md-truncate>Telefone 05 </strong>
                                    <p class="text-right" flex md-truncate ng-bind="'(' + lead1.ddd5 + ') ' + lead1.telefone5"></p>
                                    <a style="margin-left: 10px;font-size: 15px;color: #075bf5;font-weight: 600;" href="tel:+55{{lead1.ddd5 + lead1.telefone5}}"> Ligar... <i class="fas fa-phone-square-alt"></i></a>

                                </md-list-item>
                                <md-divider></md-divider>
                            </div>

                            <div ng-if="lead1.email1 != null && lead1.email1 != ''">
                                <md-list-item>
                                    <md-icon class="mdi ion-android-mail"></md-icon>
                                    <strong flex md-truncate>Email 01 </strong>
                                    <p class="text-right" flex md-truncate ng-bind="lead1.email1"></p>
                                </md-list-item>
                                <md-divider></md-divider>
                            </div>

                            <div ng-if="lead1.email2 != null && lead1.email2 != ''">
                                <md-list-item>
                                    <md-icon class="mdi ion-android-mail"></md-icon>
                                    <strong flex md-truncate>Email 02 </strong>
                                    <p class="text-right" flex md-truncate ng-bind="lead1.email2"></p>
                                </md-list-item>
                                <md-divider></md-divider>
                            </div>

                            <div ng-if="lead1.email3 != null && lead1.email3 != ''">
                                <md-list-item>
                                    <md-icon class="mdi ion-android-mail"></md-icon>
                                    <strong flex md-truncate>Email 03 </strong>
                                    <p class="text-right" flex md-truncate ng-bind="lead1.email3"></p>
                                </md-list-item>
                                <md-divider></md-divider>
                            </div>

                            <div ng-if="lead1.email4 != null && lead1.email4 != ''">
                                <md-list-item>
                                    <md-icon class="mdi ion-android-mail"></md-icon>
                                    <strong flex md-truncate>Email 04 </strong>
                                    <p class="text-right" flex md-truncate ng-bind="lead1.email4"></p>
                                </md-list-item>
                                <md-divider></md-divider>
                            </div>

                            <div ng-if="lead1.email5 != null && lead1.email5 != ''">
                                <md-list-item>
                                    <md-icon class="mdi ion-android-mail"></md-icon>
                                    <strong flex md-truncate>Email 05 </strong>
                                    <p class="text-right" flex md-truncate ng-bind="lead1.email5"></p>
                                </md-list-item>
                                <md-divider></md-divider>
                            </div>

                            <h4 style="text-align: center;font-weight: 600;">Redes sociais</h4>

                            <md-list-item>
                                <md-icon><i class="fab fa-linkedin-in"></i></md-icon>
                                <strong flex md-truncate>Linkedin</strong>
                                <p class="text-right" flex md-truncate ng-bind="lead1.url_insta"></p>
                            </md-list-item>
                            <md-divider></md-divider>

                            <md-list-item>
                                <md-icon><i class="fab fa-instagram"></i></md-icon>
                                <strong flex md-truncate>Instagram</strong>
                                <p class="text-right" flex md-truncate ng-bind="lead1.url_insta"></p>
                            </md-list-item>
                            <md-divider></md-divider>

                            <md-list-item>
                                <md-icon><i class="fab fa-facebook"></i></md-icon>
                                <strong flex md-truncate>Facebook</strong>
                                <p class="text-right" flex md-truncate ng-bind="lead1.url_face"></p>
                            </md-list-item>
                            <md-divider></md-divider>

                            <h4 style="text-align: center;font-weight: 600;">Sócios</h4>

                            <div ng-if="lead1.socio1_nome != null && lead1.socio1_nome != ''">
                                <md-list-item>
                                    <md-icon class="mdi"><i class="fas fa-calendar-week"></i></md-icon>
                                    <strong flex md-truncate>Sócio 1</strong>
                                    <p class="text-right" flex md-truncate ng-bind="lead1.socio1_nome"></p>
                                </md-list-item>
                                <md-divider></md-divider>
                            </div>
                            <div ng-if="lead1.socio2_nome != null && lead1.socio2_nome != ''">
                                <md-list-item>
                                    <md-icon class="mdi"><i class="fas fa-calendar-week"></i></md-icon>
                                    <strong flex md-truncate>Sócio 2 </strong>
                                    <p class="text-right" flex md-truncate ng-bind="lead1.socio2_nome"></p>
                                </md-list-item>
                                <md-divider></md-divider>
                            </div>
                        </md-list>
                    </md-content>
                </div>

                <md-divider></md-divider>
            </div>
        </md-content>
        <br>
        <br>
        <br>
        <br>
    </div>
</div>
<script>
    var is_admin = '<?= $user_data['super_admin'] ?>';
    var IDLEAD1 = "<?= $id_lead1; ?>"
    var lang = {};
    lang.doIt = "<?= lang2('doIt') ?>";
    lang.cancel = "<?= lang2('cancel') ?>";
    lang.attention = "<?= lang2('attention') ?>";
    lang.delete_staff = "<?= lang2('staffattentiondetail') ?>";
    var pageAtual = 1;
</script>
<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>
<script src="<?php echo base_url('assets/lib/chartjs/dist/Chart.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/lead1.js?v=1.1'); ?>"></script>

<script>
    $(document).ready(function() {
        $("#imgLogo").on("error", function() {
            console.log('erro')
            $(this).attr('src', '<?= base_url() ?>/assets/img/company.png');
        });
    });

    function aaa() {
        console.log('erro')
    }
</script>