<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>

<?php $appconfig = get_appconfig(); ?>

<md-content class="ciuis-body-content" ng-controller="Chat_Controller">
    <md-toolbar class="toolbar-white">

        <div class="md-toolbar-tools">

            <md-button class="md-icon-button" aria-label="Settings" ng-disabled="true">
                <md-icon><i class="ico-ciuis-supports text-muted"></i></md-icon>
            </md-button>
            <h2 ng-bind="chat.details.staffname"></h2>&nbsp;

        </div>

    </md-toolbar>


    <md-content class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">



        <div ng-show="chatsLoader" layout-align="center center" class="text-center" id="circular_loader">

            <md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>

            <p style="font-size: 15px;margin-bottom: 5%;">

                <span>

                    <?php echo lang2('please_wait') ?> <br>

                    <small><strong><?php echo lang2('loading') . ' ' . lang2('chat') . '...' ?></strong></small>

                </span>

            </p>

        </div>

        <md-content ng-show="!chatsLoader" layout-padding class="bg-white" style="overflow: hidden;" ng-cloak>


            <div class="main-content container-fluid col-xs-12 col-md-8 col-lg-8">
                <div class="ciuis-chat-row">
                    <div class="ciuis-chat-fieldgroup full">
                        <div class="chat-label"><strong><?php echo lang2('message') ?></strong></div>
                        <div style="padding: 10px; border-radius: 3px; margin-bottom: 10px; font-weight: 600; background: #f3f3f3;" class="chat-data">
                            <span ng-bind="chat.message"></span>
                            <span ng-show="chat.attachment" class="label label-default pull-right"><i class="ion-android-attach"></i>
                                <a download href="<?php echo base_url('uploads/attachments/{{chat.attachment}}') ?>" ng-bind="chat.attachment"></a>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="chat-replies row">

                    <div class="col-md-12">

                        <section class="ciuis-notes show-notes">

                            <article class="ciuis-note-detail" ng-repeat="reply in chat.replies">

                                <div class="ciuis-note-detail-img">
                                    <img src="<?php echo base_url('assets/img/comment.png') ?>" alt="" width="50" height="50" />
                                </div>

                                <div class="ciuis-note-detail-body">
                                    <div class="text">
                                        <p>
                                            <span ng-bind="reply.message"></span>
                                            <span ng-hide="!reply.attachment" class="label label-default pull-right"><i class="ion-android-attach"></i>
                                                <a download ng-href="<?php echo base_url('chats/attachments/{{reply.attachment}}') ?>" ng-bind="reply.attachment"></a>
                                            </span>
                                        </p>
                                    </div>
                                    <p class="attribution"><?php echo lang2('repliedby') ?>
                                        <strong ng-bind="reply.name"></strong> <?php echo lang2('ad') ?>
                                        <span ng-bind="reply.date"></span>

                                        <i style="color: #1abd07; margin-left: 10px; font-size: 11px;vertical-align: unset;" ng-if="reply.send_view == 1" class="fas fa-check-double"><md-tooltip md-direction="bottom">Visualizado</md-tooltip></i>
                                        <i style="color: #1abd07; margin-left: 10px; font-size: 11px;vertical-align: unset;" ng-if="reply.send_view == 0 || reply.send_view == '' || reply.send_view == null" class="fas fa-check"><md-tooltip md-direction="bottom">Não visualizado</md-tooltip></i>
                                    </p>
                                </div>
                            </article>
                        </section>
                    </div>

                    <?php if (check_privilege('chats', 'edit')) { ?>

                        <div class="col-md-12">

                            <section class="md-pb-30">

                                <?php helper('form');echo form_open_multipart('chats/reply/' . $id . ''); ?>

                                <md-input-container class="md-block">

                                    <label><?php echo lang2('reply') ?></label>

                                    <textarea name="message" rows="3" ng-model="reply.message" placeholder="<?php echo lang2('typeSomething'); ?>" class="form-control answer"></textarea>

                                </md-input-container>

                                <div class="form-group pull-left">

                                    <input type="file" name="attachment" id="chooseFile" file-model="reply.attachment">

                                </div>

                                <md-button ng-click="replyToChat()" class="md-raised md-primary pull-right" ng-disabled="replying == true">

                                    <span ng-hide="replying == true"><?php echo lang2('reply'); ?></span>

                                    <md-progress-circular class="white" ng-show="replying == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

                                </md-button>

                                <?php echo form_close(); ?>

                            </section>

                        </div>

                    <?php } ?>

                </div>
            </div>

        </md-content>

    </md-content>

    <script type="text/ng-template" id="insert-member-template.html">

        <md-dialog aria-label="options dialog">

			<md-dialog-content layout-padding>

				<h2 class="md-title"><?php echo lang2('assigned'); ?></h2>

				<md-select required ng-model="AssignedStaff" style="min-width: 200px;" aria-label="AddMember">

					<md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>

				</md-select>

			</md-dialog-content>

			<md-dialog-actions>

				<span flex></span>

				<md-button ng-click="close()"><?php echo lang2('cancel') ?>!</md-button>

				<md-button ng-click="AssignStaff()"><?php echo lang2('add') ?>!</md-button>

			</md-dialog-actions>

		</md-dialog>

	</script>

</md-content>





<script>
    var CHATID = "<?php echo $id; ?>";
</script>

<script type="text/javascript">
    var lang = {};
    lang.attention = "<?php echo lang2('attention') ?>";
    lang.doIt = "<?php echo lang2('doIt') ?>";
    lang.cancel = "<?php echo lang2('cancel') ?>";
    lang.chatattentiondetail = "<?php echo lang2('chatattentiondetail') ?>";
    lang.chat = "<?php echo lang2('chat') ?>";
    lang.delete = "<?php echo lang2('delete') ?>";
</script>

<?php include_once(APPPATH . 'Views/inc/footer.php'); ?>
<script type="text/javascript" src="<?php echo base_url('assets/js/chats.js?v=1.1') ?>"></script>