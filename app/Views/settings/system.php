<md-content class="md-padding bg-white" style="">
    <div class="col-md-12">
        <div class="form-group clearfix" style="border-bottom: 1px solid gray;">
            <h4 class="pull-left" ><strong><?php echo lang2('system').' '.lang2('settings'); ?></strong></h4>
            <?php if (check_privilege('settings', 'edit')) { ?>
                <md-button ng-click="uploadAppFiles()" class="md-raised md-primary pull-right successButton">
                    <md-icon>
                        <i class="ion-android-upload"></i>
                    </md-icon>
                    <?php echo lang2('debug');?>
                </md-button>
                <md-button ng-click="RunMySQL()" class="md-raised md-primary pull-right successButton"  ng-show="settings_detail.is_mysql == '1'">
                    <md-icon>
                        <i class="ion-social-buffer"></i>
                    </md-icon>
                    <?php echo lang2('mysql');?>
                </md-button>
                <md-button ng-click="UninstallLicense()" class="md-raised md-primary pull-right successButton">
                    <md-icon>
                        <i class="ion-unlocked"></i>
                    </md-icon>
                    <?php echo lang2('uninstall_license');?>
                </md-button>
                <md-button ng-href="<?php echo base_url('editor') ?>" class="md-raised md-primary pull-right successButton">
                    <md-icon>
                        <i class="ion-ios-compose-outline"></i>
                    </md-icon>
                    <?php echo lang2('editor');?>
                </md-button>
            <?php } ?>
            <md-button ng-click="systemInfo()" class="md-raised md-primary pull-right successButton">
                <md-icon>
                    <i class="ion-information-circled"></i>
                </md-icon>
                <?php echo lang2('system');?>
            </md-button>
        </div>
    </div>
</md-content>