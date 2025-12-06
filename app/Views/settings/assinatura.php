<style>
    .elementor-widget-price-list .elementor-price-list {
        list-style: none;
        padding: 0;
        margin: 0
    }

    .elementor-widget-price-list .elementor-price-list li {
        margin: 0
    }

    .elementor-price-list li:not(:last-child) {
        margin-bottom: 20px
    }

    .elementor-price-list .elementor-price-list-image {
        max-width: 50%;
        flex-shrink: 0;
        padding-right: 25px
    }

    .elementor-price-list .elementor-price-list-image img {
        width: 100%
    }

    .elementor-price-list .elementor-price-list-header,
    .elementor-price-list .elementor-price-list-item,
    .elementor-price-list .elementor-price-list-text {
        display: flex
    }

    .elementor-price-list .elementor-price-list-item {
        align-items: flex-start
    }

    .elementor-price-list .elementor-price-list-item .elementor-price-list-text {
        align-items: flex-start;
        flex-wrap: wrap;
        flex-grow: 1
    }

    .elementor-price-list .elementor-price-list-item .elementor-price-list-header {
        align-items: center;
        flex-basis: 100%;
        font-size: 19px;
        font-weight: 600;
        margin-bottom: 10px;
        justify-content: space-between
    }

    .elementor-price-list .elementor-price-list-item .elementor-price-list-title {
        max-width: 80%
    }

    .elementor-price-list .elementor-price-list-item .elementor-price-list-price {
        font-weight: 600
    }

    .elementor-price-list .elementor-price-list-item p.elementor-price-list-description {
        flex-basis: 100%;
        font-size: 14px;
        margin: 0
    }

    .elementor-price-list .elementor-price-list-item .elementor-price-list-separator {
        flex-grow: 1;
        margin-left: 10px;
        margin-right: 10px;
        border-bottom-style: dotted;
        border-bottom-width: 2px;
        height: 0
    }

    .elementor-price-table {
        text-align: center;
        float: left;
        margin-left: 10px;
    }

    .elementor-price-table .elementor-price-table__header {
        background: var(--e-price-table-header-background-color, #555);
        padding: 20px 0
    }

    .elementor-price-table .elementor-price-table__heading {
        margin: 0;
        padding: 0;
        line-height: 1.2;
        font-size: 35px;
        font-weight: 600;
        color: #fff
    }

    .elementor-price-table .elementor-price-table__subheading {
        font-size: 13px;
        font-weight: 400;
        color: #fff
    }

    .elementor-price-table .elementor-price-table__original-price {
        margin-right: 15px;
        text-decoration: line-through;
        font-size: .5em;
        line-height: 1;
        font-weight: 400;
        align-self: center
    }

    .elementor-price-table .elementor-price-table__original-price .elementor-price-table__currency {
        font-size: 1em;
        margin: 0
    }

    .elementor-price-table .elementor-price-table__price {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        flex-direction: row;
        color: #555;
        font-weight: 800;
        font-size: 65px;
        padding: 40px 0
    }

    .elementor-price-table .elementor-price-table__price .elementor-typo-excluded {
        line-height: normal;
        letter-spacing: normal;
        text-transform: none;
        font-weight: 400;
        font-size: medium;
        font-style: normal
    }

    .elementor-price-table .elementor-price-table__after-price {
        display: flex;
        flex-wrap: wrap;
        text-align: left;
        align-self: stretch;
        align-items: flex-start;
        flex-direction: column
    }

    .elementor-price-table .elementor-price-table__integer-part {
        line-height: .8
    }

    .elementor-price-table .elementor-price-table__currency,
    .elementor-price-table .elementor-price-table__fractional-part {
        line-height: 1;
        font-size: .3em
    }

    .elementor-price-table .elementor-price-table__currency {
        margin-right: 3px
    }

    .elementor-price-table .elementor-price-table__period {
        width: 100%;
        font-size: 13px;
        font-weight: 400
    }

    .elementor-price-table .elementor-price-table__features-list {
        list-style-type: none;
        margin: 0;
        padding: 0;
        line-height: 1;
        color: var(--e-price-table-features-list-color)
    }

    .elementor-price-table .elementor-price-table__features-list li {
        font-size: 14px;
        line-height: 1;
        margin: 0;
        padding: 0
    }

    .elementor-price-table .elementor-price-table__features-list li .elementor-price-table__feature-inner {
        margin-left: 30px;
        margin-right: 15px;
        text-align: left;
    }

    .elementor-price-table .elementor-price-table__features-list li:not(:first-child):before {
        content: "";
        display: block;
        border: 0 solid hsla(0, 0%, 47.8%, .3);
        margin: 10px 12.5%
    }

    .elementor-price-table .elementor-price-table__features-list i {
        margin-right: 10px;
        font-size: 1.3em
    }

    .elementor-price-table .elementor-price-table__features-list svg {
        margin-right: 10px;
        fill: var(--e-price-table-features-list-color);
        height: 1.3em;
        width: 1.3em
    }

    .elementor-price-table .elementor-price-table__features-list svg~* {
        vertical-align: text-top
    }

    .elementor-price-table .elementor-price-table__footer {
        padding: 30px 0
    }

    .elementor-price-table .elementor-price-table__additional_info {
        margin: 0;
        font-size: 13px;
        line-height: 1.4
    }

    .elementor-price-table__ribbon {
        position: absolute;
        top: 0;
        left: auto;
        right: 0;
        transform: rotate(90deg);
        width: 150px;
        overflow: hidden;
        height: 150px
    }

    .elementor-price-table__ribbon-inner {
        text-align: center;
        left: 0;
        width: 200%;
        transform: translateY(-50%) translateX(-50%) translateX(35px) rotate(-45deg);
        margin-top: 35px;
        font-size: 13px;
        line-height: 2;
        font-weight: 800;
        text-transform: uppercase;
        background: #000
    }

    .elementor-price-table__ribbon.elementor-ribbon-left {
        transform: rotate(0);
        left: 0;
        right: auto
    }

    .elementor-price-table__ribbon.elementor-ribbon-right {
        transform: rotate(90deg);
        left: auto;
        right: 0
    }

    .e-con-inner>.elementor-widget-price-list,
    .e-con>.elementor-widget-price-list {
        width: var(--container-widget-width);
        --flex-grow: var(--container-widget-flex-grow)
    }

    .elementor-price-table__header {
        --e-price-table-header-background-color: #FF5050;
    }

    .elementor-price-table__currency,
    .elementor-price-table__integer-part,
    .elementor-price-table__after-price {
        color: #F34646;
    }

    .elementor-price-table__feature-inner .check {
        color: #0BB717;
    }

    .elementor-price-table__feature-inner .Ncheck {
        color: #F20A0A;
    }

    .elementor-price-table {
        border-style: solid;
        border-width: 1px 1px 1px 1px;
        border-color: #FF5050;
        transition: background 0.3s, border 0.3s, border-radius 0.3s, box-shadow 0.3s;
        margin: 0px 20px 0px 0px;
        --e-column-margin-right: 20px;
        --e-column-margin-left: 0px;
        min-width: 270px;
    }

    .elementor-button {
        display: inline-block;
        line-height: 1;
        background-color: #69727d;
        font-size: 15px;
        padding: 12px 24px;
        border-radius: 3px;
        color: #fff;
        fill: #fff;
        text-align: center;
        transition: all .3s;
    }

    .elementor-price-table__button {
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        background: #ffbc00 !important;
        border: #ffbc00 !important;

        border-radius: 0px 0px 0px 0px;
        padding: 15px 45px 15px 45px;
        margin: 0 15px;
        width: calc(100% - 30px);
    }

    .elementor-button:hover {
        background-color: #00ce1b;
        transform: scale(1.1);
        color: #fff;
    }

    .plan_atual {
        background-color: transparent !important;
        color: #848484;
        box-shadow: 0px 0px 2px #7a7a7a;
    }
</style>
<md-content class="widget-fullwidth ciuis-body-loading" style="overflow: hidden;" id="contentMain">
    <md-card flex-xs flex-gt-xs="100" layout="column">
        <div layout-xs="column" layout="row" class="bg-white">
            <md-card flex-xs flex-gt-xs="20" layout="column" class="text-center card6" style="position: relative;">
                <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-vencimento'] != null && baloesGraphs['card-vencimento'].exibir == '1')" ng-click="exibeBalao('card-vencimento');" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                <a href="{{appurl + 'leads'}}">
                    <md-card-title>
                        <md-card-title-text>
                            <span class="md-headline"><strong ng-bind="assinatura.vencimento"></strong></span>
                            <span class="md-subhead">Vencimento</span>
                        </md-card-title-text>
                    </md-card-title>
                </a>
            </md-card>

            <md-card flex-xs flex-gt-xs="20" layout="column" class="text-center card1" style="position: relative;">
                <img ng-show="'<?= $user_data['super_admin'] ?>' == '1' || (baloesGraphs['card-nm_plano'] != null && baloesGraphs['card-nm_plano'].exibir == '1')" ng-click="exibeBalao('card-nm_plano');" class="btn_balao btn_balaoGraph" src="<?= base_url('assets/img/icons/balao.png'); ?>">
                <a href="{{appurl + 'leads'}}">
                    <md-card-title>
                        <md-card-title-text>
                            <span class="md-headline"><strong ng-bind="assinatura.nm_plan"></strong></span>
                            <span class="md-subhead">Plano</span>
                        </md-card-title-text>
                    </md-card-title>
                </a>
            </md-card>
        </div>
    </md-card>

    <md-content class="md-padding bg-white">
        <div class="elementor-container elementor-column-gap-no">
            <div class="elementor-column elementor-col-25 elementor-top-column elementor-element elementor-element-403cdfe" data-id="403cdfe" data-element_type="column">
                <div class="elementor-widget-wrap elementor-element-populated">
                    <div class="elementor-element elementor-element-68176dd2 elementor-widget elementor-widget-price-table" data-id="68176dd2" data-element_type="widget" data-widget_type="price-table.default">
                        <div class="elementor-widget-container">

                            <div class="elementor-price-table" ng-repeat="plano in planos">
                                <div class="elementor-price-table__header">
                                    <h3 class="elementor-price-table__heading">{{plano.nm_plan}}</h3>
                                    <span class="elementor-price-table__subheading">Mínimo de {{plano.min_user}} usuários</span>
                                </div>

                                <div class="elementor-price-table__price">
                                    <span class="elementor-price-table__currency">R$</span>
                                    <span class="elementor-price-table__integer-part">{{(plano.tipo == 30 ? plano.valor : plano.valor_anual).split('.')[0]}}</span>
                                    <div class="elementor-price-table__after-price">
                                        <span class="elementor-price-table__fractional-part">{{(plano.tipo == 30 ? plano.valor : plano.valor_anual).split('.')[1]}} <small ng-if = "plano.tipo == 365">/mês</small></span>
                                        
                                        <span class="elementor-price-table__period elementor-typo-excluded">
                                            <select style="border: 0; outline: 0;" ng-model="plano.tipo">
                                                <option value="30">Mensal</option>
                                                <option value="365" ng-if="plano.valor_anual != null && plano.valor_anual != '' && plano.valor_anual != '0.00'">Anual</option>
                                            </select>
                                        </span>
                                    </div>
                                </div>

                                <ul class="elementor-price-table__features-list">
                                    <li class="elementor-repeater-item-2af3de2" ng-repeat="privilegio in plano.privilegios" ng-if="privilegio.id != '20'">
                                        <div class="elementor-price-table__feature-inner">
                                            <i aria-hidden="true" class="fas fa-check-circle check"></i>
                                            <span>{{privilegio.key}}</span>
                                        </div>
                                    </li>
                                </ul>

                                <div class="elementor-price-table__footer">
                                    <a ng-if="plano.valor != '0.00' && plano.id_plan != assinatura.id_plan" class="elementor-price-table__button elementor-button elementor-size-md elementor-animation-grow" ng-click = "btnCC(plano);" id = "btnCC{{plano.id_plan}}" href="javaScript:void(0)" data-tipo="{{plano.tipo}}" data-valor="{{plano.tipo == 30 ? plano.valor : plano.valor_anual}}" data-id="{{plano.id_plan}}">
                                        Fazer upgrade
                                    </a>

                                    <a ng-if="plano.valor == '0.00' && plano.id_plan != assinatura.id_plan" class="elementor-price-table__button elementor-button elementor-size-md elementor-animation-grow" href="javaScript:void(0)">
                                        Experimente Grátis
                                    </a>

                                    <a ng-if="plano.id_plan == assinatura.id_plan" class="plan_atual elementor-price-table__button elementor-button elementor-size-md elementor-animation-grow" ng-click = "btnCC(plano);" id = "btnCC{{plano.id_plan}}" href="javaScript:void(0)" data-tipo="{{plano.tipo}}" data-valor="{{plano.tipo == 30 ? plano.valor : plano.valor_anual}}" data-id="{{plano.id_plan}}">
                                        Renovar
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </md-content>
</md-content>