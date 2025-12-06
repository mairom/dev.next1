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
</style>

<div class="ciuis-body-content" ng-controller="Chats_Controller">

    <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">



        <div class="main-content container-fluid col-xs-12 col-md-9 col-lg-9 md-p-0 lead-table">

            <md-toolbar class="toolbar-white">

                <div class="md-toolbar-tools">

                    <h2 flex md-truncate class="text-bold">Chats <small>(<span ng-bind="chats.length"></span>)</small><br>

                        <small flex md-truncate><?php echo lang2('tracktickets'); ?></small>

                    </h2>

                    <md-button ng-if="!KanbanBoard" ng-click="ShowKanban()" class="md-icon-button" aria-label="Show Kanban" ng-cloak>

                        <md-tooltip md-direction="bottom"><?php echo lang2('showkanban'); ?></md-tooltip>

                        <md-icon><i class="mdi mdi-view-week text-muted"></i></md-icon>

                    </md-button>

                    <md-button ng-if="KanbanBoard" ng-click="HideKanban()" class="md-icon-button" aria-label="Show List" ng-cloak>

                        <md-tooltip md-direction="bottom"><?php echo lang2('showlist'); ?></md-tooltip>

                        <md-icon><i class="mdi mdi-view-list text-muted"></i></md-icon>

                    </md-button>

                    <?php if (check_privilege('chats', 'create')) { ?>

                        <md-button ng-click="Create()" class="md-icon-button" aria-label="New" ng-cloak>

                            <md-tooltip md-direction="bottom">Novo chat</md-tooltip>

                            <md-icon><i class="ion-plus-round text-muted"></i></md-icon>

                        </md-button>

                    <?php } ?>

                </div>

            </md-toolbar>

            <div ng-show="chatsLoader" layout-align="center center" class="text-center" id="circular_loader">

                <!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
                <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">

                <p style="font-size: 15px;margin-bottom: 5%;">

                    <span>

                        <?php echo lang2('please_wait') ?> <br>

                        <small><strong><?php echo lang2('loading') . ' ' . lang2('chats') . '...' ?></strong></small>

                    </span>

                </p>

            </div>

            <md-content ng-show="!chatsLoader" ng-if="KanbanBoard" class="" style="padding: 0px" ng-cloak>

                <md-content class="col-md-4" style="padding: 0px; border: 1px solid #e4e4e4; height: 500px;">

                    <md-list flex style="padding-top: unset;">

                        <md-subheader class="md-no-sticky bg-white"><md-icon class="ion-android-alert text-danger"></md-icon><strong><?php echo lang2('high') ?></strong></md-subheader>

                        <md-divider></md-divider>

                        <md-list-item class="md-2-line" ng-repeat="chat in chats | filter:ChatsFilter | filter: { priority_id: '3' }" ng-click="GoTicket(chat.id)">

                            <div class="md-list-item-text">



                                <p ng-show="chat.lastreply != NULL"><small><strong><?php echo lang2('lastreply') ?></strong> <small ng-bind="chat.lastreply | date : 'MMM d, y h:mm:ss a'"></small></small></p>

                                <p ng-show="chat.lastreply == NULL"><small><strong><?php echo lang2('lastreply') ?></strong> <small><?php echo lang2('n_a') ?></small></small></p>

                            </div>

                            <md-button ng-hide="chat.status_id != 4" class="md-secondary md-icon-button" aria-label="Closed">

                                <md-tooltip md-direction="bottom"><?php echo lang2('closed') ?></md-tooltip>

                                <md-icon class="ion-happy text-success"></md-icon>

                            </md-button>

                            <md-button ng-hide="chat.status_id != 3" class="md-secondary md-icon-button" aria-label="Closed">

                                <md-tooltip md-direction="bottom"><?php echo lang2('answered') ?></md-tooltip>

                                <md-icon class="mdi mdi-mail-reply-all text-muted"></md-icon>

                            </md-button>

                            <md-button ng-hide="chat.status_id != 2" class="md-secondary md-icon-button" aria-label="Closed">

                                <md-tooltip md-direction="bottom"><?php echo lang2('inprogress') ?></md-tooltip>

                                <md-icon class="mdi mdi-hourglass-alt text-muted"></md-icon>

                            </md-button>

                            <md-button ng-hide="chat.status_id != 1" class="md-secondary md-icon-button" aria-label="Closed">

                                <md-tooltip md-direction="bottom"><?php echo lang2('open') ?></md-tooltip>

                                <md-icon class="mdi mdi-flash text-danger"></md-icon>

                            </md-button>

                            <md-divider></md-divider>

                        </md-list-item>

                    </md-list>

                </md-content>

                <md-content class="col-md-4" style="padding: 0px; border: 1px solid #e4e4e4; height: 500px;">

                    <md-list flex style="padding-top: unset;">

                        <md-subheader class="md-no-sticky bg-white"><md-icon class="ion-android-alert text-warning"></md-icon><strong><?php echo lang2('medium') ?></strong></md-subheader>

                        <md-divider></md-divider>

                        <md-list-item class="md-2-line" ng-repeat="chat in chats | filter:ChatsFilter " ng-click="GoTicket(chat.id)">

                            <div class="md-list-item-text">



                                <p ng-show="chat.lastreply != NULL"><small><strong><?php echo lang2('lastreply') ?></strong> <small ng-bind="chat.lastreply | date : 'MMM d, y h:mm:ss a'"></small></small></p>

                                <p ng-show="chat.lastreply == NULL"><small><strong><?php echo lang2('lastreply') ?></strong> <small><?php echo lang2('n_a') ?></small></small></p>

                            </div>

                            <md-button ng-hide="chat.status_id != 4" class="md-secondary md-icon-button" aria-label="Closed">

                                <md-tooltip md-direction="bottom"><?php echo lang2('closed') ?></md-tooltip>

                                <md-icon class="ion-happy text-success"></md-icon>

                            </md-button>

                            <md-button ng-hide="chat.status_id != 3" class="md-secondary md-icon-button" aria-label="Closed">

                                <md-tooltip md-direction="bottom"><?php echo lang2('answered') ?></md-tooltip>

                                <md-icon class="mdi mdi-mail-reply-all text-muted"></md-icon>

                            </md-button>

                            <md-button ng-hide="chat.status_id != 2" class="md-secondary md-icon-button" aria-label="Closed">

                                <md-tooltip md-direction="bottom"><?php echo lang2('inprogress') ?></md-tooltip>

                                <md-icon class="mdi mdi-hourglass-alt text-muted"></md-icon>

                            </md-button>

                            <md-button ng-hide="chat.status_id != 1" class="md-secondary md-icon-button" aria-label="Closed">

                                <md-tooltip md-direction="bottom"><?php echo lang2('open') ?></md-tooltip>

                                <md-icon class="mdi mdi-flash text-danger"></md-icon>

                            </md-button>

                            <md-divider></md-divider>

                        </md-list-item>

                    </md-list>

                </md-content>

                <md-content class="col-md-4" style="padding: 0px; border: 1px solid #e4e4e4; height: 500px;">

                    <md-list flex style="padding-top: unset;">

                        <md-subheader class="md-no-sticky bg-white"><md-icon class="ion-android-alert text-success"></md-icon><strong><?php echo lang2('low') ?></strong></md-subheader>

                        <md-divider></md-divider>

                        <md-list-item class="md-2-line" ng-repeat="chat in chats | filter:ChatsFilter " ng-click="GoTicket(chat.id)">

                            <div class="md-list-item-text">

                                <h3> {{ chat.subject }} </h3>

                                <p ng-show="chat.lastreply != NULL"><small><strong><?php echo lang2('lastreply') ?></strong> <small ng-bind="chat.lastreply | date : 'MMM d, y h:mm:ss a'"></small></small></p>

                                <p ng-show="chat.lastreply == NULL"><small><strong><?php echo lang2('lastreply') ?></strong> <small><?php echo lang2('n_a') ?></small></small></p>

                            </div>

                            <md-button ng-hide="chat.status_id != 4" class="md-secondary md-icon-button" aria-label="Closed">

                                <md-tooltip md-direction="bottom"><?php echo lang2('closed') ?></md-tooltip>

                                <md-icon class="ion-happy text-success"></md-icon>

                            </md-button>

                            <md-button ng-hide="chat.status_id != 3" class="md-secondary md-icon-button" aria-label="Closed">

                                <md-tooltip md-direction="bottom"><?php echo lang2('answered') ?></md-tooltip>

                                <md-icon class="mdi mdi-mail-reply-all text-muted"></md-icon>

                            </md-button>

                            <md-button ng-hide="chat.status_id != 2" class="md-secondary md-icon-button" aria-label="Closed">

                                <md-tooltip md-direction="bottom"><?php echo lang2('inprogress') ?></md-tooltip>

                                <md-icon class="mdi mdi-hourglass-alt text-muted"></md-icon>

                            </md-button>

                            <md-button ng-hide="chat.status_id != 1" class="md-secondary md-icon-button" aria-label="Closed">

                                <md-tooltip md-direction="bottom"><?php echo lang2('open') ?></md-tooltip>

                                <md-icon class="mdi mdi-flash text-danger"></md-icon>

                            </md-button>

                            <md-divider></md-divider>

                        </md-list-item>

                    </md-list>

                </md-content>

            </md-content>

            <md-content ng-show="!chatsLoader" ng-if="!KanbanBoard" class="md-pt-0 bg-white" ng-cloak>

                <md-table-container ng-show="chats.length > 0">

                    <table md-table md-progress="promise" ng-cloak>

                        <thead md-head md-order="chat_list.order">

                            <tr md-row>

                                <th md-column><span>#</span></th>

                                <th md-column><span>Nome</span></th>


                                <th md-column md-order-by="lastreply"><span><?php echo lang2('lastreply'); ?></span></th>

                            </tr>

                        </thead>

                        <tbody md-body>

                            <tr class="select_row" md-row ng-repeat="chat in chats | orderBy: chat_list.order | limitTo: chat_list.limit : (chat_list.page -1) * chat_list.limit " class="cursor" ng-click="goToLink('chats/chat/'+chat.id)">

                                <td md-cell>
                                    <strong>
                                        <a class="link" ng-href="<?php echo base_url('chats/chat/') ?>{{chat.id}}"> <strong ng-bind="chat.id"></strong></a>
                                    </strong>
                                </td>

                                <td md-cell>
                                    <strong>
                                        <a class="link" ng-href="<?php echo base_url('chats/chat/') ?>{{chat.id}}"> <strong ng-bind="chat.details.staffname"></strong></a>
                                    </strong>
                                </td>


                                <td md-cell>

                                    <span>

                                        <span ng-show="chat.lastreply == NULL" class="badge"><?php echo lang2('n_a') ?></span><span ng-show="chat.lastreply != NULL" class="badge" ng-bind="chat.lastreply | date : 'MMM d, y h:mm:ss a'"></span>

                                    </span>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </md-table-container>

                <md-table-pagination ng-show="chats.length > 0" md-limit="chat_list.limit" md-limit-options="limitOptions" md-page="chat_list.page" md-total="{{chats.length}}"></md-table-pagination>

                <md-content ng-show="!chats.length" class="md-padding no-item-data"><?php echo lang2('notdata') ?></md-content>

            </md-content>

        </div>

    </div>

    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Create" ng-cloak style="width: 450px;">
        <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">
                <md-button ng-click="close()" class="md-icon-button" aria-label="Close">
                    <i class="ion-android-arrow-forward"></i>
                </md-button>
                <md-truncate><?php echo lang2('create') ?></md-truncate>
            </div>

        </md-toolbar>
        <md-content layout-padding="">
            <md-content layout-padding>
                <?php helper('form');
                echo form_open_multipart('chat/create'); ?>


                <md-input-container class="md-block" flex-gt-xs>
                    <label>Enviar para</label>
                    <md-select required ng-model="chat.user" name="user">
                        <md-option ng-value="user.id" ng-repeat="user in users">{{user.name}}</md-option>
                    </md-select><br>
                </md-input-container>

                <md-input-container class="md-block" flex-gt-xs>
                    <label><?php echo lang2('department'); ?></label>
                    <md-select required ng-model="chat.department" name="department">
                        <md-option ng-value="department.id" ng-repeat="department in departments">{{department.name}}</md-option>
                    </md-select><br>
                </md-input-container>

                <md-input-container class="md-block" flex-gt-xs>
                    <label><?php echo lang2('priority'); ?></label>
                    <md-select ng-init="priorities = [{id: 1,name: '<?php echo lang2('low'); ?>'}, {id: 2,name: '<?php echo lang2('medium'); ?>'}, {id: 3,name: '<?php echo lang2('high'); ?>'}];" required placeholder="<?php echo lang2('priority'); ?>" ng-model="chat.priority" name="priority">
                        <md-option ng-value="priority.id" ng-repeat="priority in priorities"><span class="text-uppercase">{{priority.name}}</span></md-option>
                    </md-select><br>
                </md-input-container>

                <md-input-container class="md-block">
                    <label><?php echo lang2('message') ?></label>
                    <textarea required name="message" ng-model="chat.message" placeholder="<?php echo lang2('typeSomething'); ?>" class="form-control"></textarea>
                </md-input-container>

                <div class="file-upload">
                    <div class="file-select">
                        <div class="file-select-button" id="fileName"><span class="mdi mdi-accounts-list-alt"></span> <?php echo lang2('attachment') ?></div>
                        <div class="file-select-name" id="noFile"><?php echo lang2('nofile') ?></div>
                        <input type="file" name="attachment" id="chooseFile" file-model="chat.chat_attachment">
                    </div>
                </div>
                <br>

                <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>
                    <md-button ng-click="createChat()" class="md-raised md-primary btn-report block-button" ng-disabled="uploading == true">
                        <span ng-hide="uploading == true"><?php echo lang2('send'); ?></span>
                        <md-progress-circular class="white" ng-show="uploading == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>
                    </md-button>
                    <br /><br /><br /><br />
                </section>

                <?php echo form_close(); ?>
            </md-content>

        </md-content>
    </md-sidenav>





</div>

<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>
<script type="text/javascript" src="<?php echo base_url('assets/js/chats.js?v=1.1.1') ?>"></script>