<md-content class="md-padding bg-white">
    
    <h4 style="margin: 20px 0 10px 15px; color: #333; font-weight: 600;">Servidor de Entrada (IMAP)</h4>
    
    <div class="col-md-6">
        <md-input-container class="md-block" flex-gt-xs>
            <label><?php echo lang2('email') . ' ' . lang2('type') ?></label>
            <md-select required placeholder="<?php echo lang2('email') . ' ' . lang2('type') ?>" ng-model="settings_detail.imap_email_type" style="min-width: 200px;">
                <md-option value="1" ng-selected="true"><span><?php echo 'IMAP' ?></span></md-option>
            </md-select><br>
        </md-input-container>

        <md-input-container class="md-block">
            <label>Usuário</label>
            <input required ng-model="settings_detail.imapUsername">
        </md-input-container>

        <md-input-container class="md-block">
            <label>Servidor</label>
            <input required ng-model="settings_detail.imapHost">
        </md-input-container>
    </div>

    <div class="col-md-6">
        <md-input-container class="md-block password-input">
            <label><?php echo lang2('password') ?></label>
            <input type="text" required ng-model="settings_detail.imapPassoword">
        </md-input-container>

        <md-input-container class="md-block">
            <label>Porta</label>
            <input required ng-model="settings_detail.imapPort">
        </md-input-container>

        <md-input-container class="md-block">
            <label>Remetente</label>
            <input required ng-model="settings_detail.imap_sendermail">
        </md-input-container>
    </div>

    <div style="clear: both;"></div>
    
    <hr style="margin: 30px 15px; border-top: 2px solid #e0e0e0;">
    
    <h4 style="margin: 20px 0 10px 15px; color: #333; font-weight: 600;">Servidor de Saída (SMTP)</h4>

    <div class="col-md-6">

        <md-input-container class="md-block">
            <label><?php echo lang2('smtpusername') ?></label>
            <input required ng-model="settings_detail.smtpusername">
        </md-input-container>

        <md-input-container class="md-block">
            <label><?php echo lang2('smtphost') ?></label>
            <input required ng-model="settings_detail.smtphost">
        </md-input-container>

        <md-input-container class="md-block">
            <label><?php echo lang2('sender_name') ?></label>
            <input required ng-model="settings_detail.sender_name">
        </md-input-container>

    </div>

    <div class="col-md-6">

        <md-input-container class="md-block" flex-gt-xs>
            <label><span class="text-bold"><?php echo lang2('email_encryption'); ?></span></label>
            <md-select ng-init="email_encryptions = [{value: '0',name: 'NONE'},{value: '1',name: 'SSL'}, {value: '2',name: 'TLS'}];" placeholder="<?php echo lang2('email_encryption'); ?>" ng-model="settings_detail.email_encryption">
                <md-option ng-value="email_encryption.value" ng-repeat="email_encryption in email_encryptions">
                    <span class="text-uppercase">{{email_encryption.name}}</span>
                </md-option>
            </md-select>
            <br>
        </md-input-container>

        <md-input-container class="md-block password-input">
            <label><?php echo lang2('password') ?></label>
            <input type="text" required ng-model="settings_detail.smtppassoword">
        </md-input-container>

        <div layout="row" layout-wrap>
            <div flex-gt-xs="50" flex-xs="100">
                <md-input-container class="md-block">
                    <label><?php echo lang2('smtpport') ?></label>
                    <input required ng-model="settings_detail.smtpport">
                </md-input-container>
            </div>

            <div flex-gt-xs="50" flex-xs="100">
                <md-input-container class="md-block">
                    <label><?php echo lang2('emailcharset') ?></label>
                    <input required ng-model="settings_detail.emailcharset">
                </md-input-container>
            </div>
        </div>

        <md-input-container class="md-block">
            <label><?php echo lang2('sender_email') ?></label>
            <input required ng-model="settings_detail.sendermail">
        </md-input-container>

    </div>

    <div style="clear: both;"></div>

    <md-button ng-if="ViewEmpresasTrue" style="background: #11b21b;color: #fff;margin: 20px 15px;" ng-click="SaveEmail()" class="md-raised md-success">Salvar</md-button>

</md-content>

<md-card flex-xs flex-gt-xs="30" layout="column" class="md-padding" style="margin-left: 1.8%;">

    <md-input-container class="md-block">
        <label><?php echo lang2('email') ?></label>
        <input type="email" required ng-model="settings_detail.testEmail" minlength="10" maxlength="100" ng-pattern="/^.+@.+\..+$/">
    </md-input-container>

    <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>
        <?php if (check_privilege('settings', 'edit')) { ?>
            <md-button ng-click="sendTestEmail()" class="md-raised md-primary pull-right" ng-disabled="sendingTestEmail == true">
                <span ng-hide="sendingTestEmail == true"><?php echo lang2('sendtestmail'); ?></span>
                <md-progress-circular class="white" ng-show="sendingTestEmail == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>
            </md-button>
        <?php } ?>
    </section>

</md-card>
