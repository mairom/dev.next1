<md-content class="md-padding bg-white">
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

</md-content>