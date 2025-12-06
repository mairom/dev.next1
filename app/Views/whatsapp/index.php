<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<?php $appconfig = get_appconfig(); ?>
<link href="<?php echo base_url('assets/lib/select2/select2.min.css'); ?>" rel="stylesheet" />
<link href="<?php echo base_url('assets/css/layout_whatsapp.css?v=1'); ?>" rel="stylesheet" />
<div class="ciuis-body-content" ng-controller="WhatsApp_Controller">
    <style>

    </style>

    <div style="display: flex;">
        <div class="contacts-numbers" ng-if="!selectedContact || sizeWidth > 900">
            <?php
            if ($user_data['super_admin'] == "1") {
            ?>
                <a style="text-align: center;width: 100%; display: block;margin-top: 10px;" href="javaScript:void(0)" ng-click="reiniciaServidor()">Reiniciar servidor whatsApp</a>
                <p style="text-align: center; margin-top: 10px; margin-bottom: 0;font-weight: 500;">Status do whatsApp: <span ng-class="statusWebsocket == 'Tudo certo' ? 'text-success' : (statusWebsocket == 'Testando' ? 'text-warning' :  'text-danger')">{{statusWebsocket}}</span></p>

            <?php
            }
            ?>

            <md-input-container class="md-block select-number">
                <label>whatsapp</label>
                <md-select ng-model="filtros.number" style="min-width: 200px;" ng-change="setWhatsapp(getWhatsapp())">
                    <md-option ng-if="number.connected == '1'" ng-value="number.number" ng-repeat="number in settings.numbers">
                        {{number.number}}
                    </md-option>
                </md-select>
            </md-input-container>

            <div ng-if="showReconnectBanner" class="ws-warning-banner">
                🔌 Conexão perdida… tentando reconectar ao WhatsApp
            </div>

            <div class="contacts-list" id="contactsList">
                <div ng-show="!whatsLoader" ng-repeat="contact in contacts" class="contact-item" ng-click="selectContact(contact)" style="padding:10px; display:flex; align-items:center; cursor:pointer; border-bottom:1px solid #f1f1f1; position: relative;">
                    <img ng-src="{{contact.avatar || 'https://www.w3schools.com/w3images/avatar2.png'}}" style="width:40px;height:40px;border-radius:50%;margin-right:10px;">
                    <div style="flex:1;">
                        <strong>{{ contact.name ? contact.name.split('@')[0] : contact.number.split('@')[0] }}</strong>
                        <small style="float: right;">{{ getData(contact.lastTimestamp != null ? contact.lastTimestamp : contact.timestamp) }}</small>
                        <br>
                        <small>{{ (contact.lastMessage || 'Sem mensagens') | limitTo:50 }}{{ contact.lastMessage && contact.lastMessage.length > 50 ? '...' : '' }}</small>
                    </div>
                    <span ng-if="contact.unreadCount > 0" class="btn-count">
                        {{contact.unreadCount}}
                    </span>
                </div>


                <div ng-if="contacts.length == 0" class="contact-item" ng-click="selectContact(contact)" style="padding:10px; display:flex; align-items:center; cursor:pointer; border-bottom:1px solid #f1f1f1;">
                    Nenhum contato
                </div>

                <div ng-show="whatsLoader || loadingMore" layout-align="center center" class="text-center" id="circular_loader">
                    <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">
                    <p style="font-size: 15px;margin-bottom: 5%;">
                        <small><strong><?php echo lang2('loading') . '...' ?></strong></small>
                    </p>
                </div>

            </div>
        </div>
        <div class="chat-container" ng-if="selectedContact && isConnected">
            <div class="chat-header" ng-if="selectedContact" style="display:flex; align-items:center; justify-content: space-between;">
                <div style="display:flex; align-items:center;flex: 1;">
                    <button class="back-button" ng-click="backContact()">&#8592;</button>
                    <img ng-src="{{selectedContact.avatar || 'https://www.w3schools.com/w3images/avatar2.png'}}" alt="Avatar" style="margin-right:10px;">
                    <div class="chat-info">
                        <span>{{selectedContact.name}}</span>
                    </div>
                </div>
                <a style="margin-right: 10px;" class="btn btn-success" ng-href="<?= base_url('leads/findLead/') ?>{{selectedContact.number}}" target="_blank">Abrir lead <i class="fas fa-share"></i></a>

                <!-- Toggle Switch -->
                <label class="switch" style="padding-top: 10px;">
                    <span>Ativar bot</span>
                    <input type="checkbox" ng-model="selectedContact.botActive" ng-change="toggleBotApi(selectedContact.botActive)" />
                    <span class="slider round"></span>
                </label>
            </div>

            <div class="chat-messages" id="chatBox">
                <div ng-repeat="msg in messages track by $index" class="message" ng-class="msg.sender === 'me' ? 'me' : 'other'">
                    <img ng-if="msg.media && msg.media.mimetype.startsWith('image')" ng-src="{{msg.media.url}}" style="max-width:200px; border-radius:8px; margin-top:5px;" />

                    <!-- Vídeo -->
                    <video ng-if="msg.media && msg.media.mimetype.startsWith('video')" ng-src="{{msg.media.url}}" controls style="max-width:250px; border-radius:8px; margin-top:5px;"></video>

                    <!-- PDF -->
                    <a ng-if="msg.media && msg.media.mimetype==='application/pdf'" ng-href="{{msg.media.url}}" target="_blank" style="display:block; margin-top:5px; color:#007bff;">
                        📄 {{msg.media.filename || 'Abrir midia'}}
                    </a>

                    <!-- Mensagem de voz -->
                    <div ng-if="msg.media && msg.media.mimetype.startsWith('audio')" class="voice-message">

                        <!-- Botão play/pause -->
                        <button class="voice-play-btn" ng-click="toggleAudio($index)">
                            <i ng-class="msg.isPlaying ? 'fa fa-pause' : 'fa fa-play'"></i>
                        </button>

                        <!-- Barra de progresso (clicável) -->
                        <div class="voice-progress" ng-click="seekAudio($event, $index)">
                            <div class="voice-progress-bar" ng-style="{'width': (msg.progress || 0) + '%'}"></div>
                        </div>

                        <!-- Tempo -->
                        <span class="voice-time">{{ msg.currentTimeDisplay || '0:00' }}</span>
                        <span class="voice-time">/ {{ msg.durationDisplay || '0:00' }}</span>

                        <!-- Controle de velocidade -->
                        <button class="voice-speed-btn" ng-click="changePlaybackRate($index)">
                            {{ msg.playbackRateDisplay || '1x' }}
                        </button>

                        <!-- Elemento de áudio escondido -->
                        <audio id="audio{{$index}}" ng-src="{{msg.media.url}}" preload="metadata"></audio>
                    </div>

                    <p style="margin: 5px 0px 10px 0px;" ng-if="msg.text" ng-bind-html="msg.text"></p>

                    <small style="display:block; font-size:11px; color:gray; margin-top:2px; text-align:right;">
                        {{msg.time}}
                    </small>

                </div>
            </div>
            <div class="chat-input" ng-if="selectedContact">
                <label class="btn upload-btn">
                    <i class="fas fa-paperclip"></i>
                    <input type="file" accept="image/*,video/*,application/pdf" style="display: none;" onchange="angular.element(this).scope().sendMedia(this.files[0], this.files[0].type.startsWith('video') ? 'video' : (this.files[0].type === 'application/pdf' ? 'pdf' : 'image'))" />
                </label>
                <input type="text" ng-model="newMessage.text" ng-keypress="$event.which === 13 && sendMessage()" placeholder="Digite uma mensagem...">
                <button ng-click="sendMessage()">&#9658;</button>
            </div>

        </div>
    </div>

</div>

<script>
    var lang = {};
    lang.doIt = "<?php echo lang2('doIt') ?>";
    lang.cancel = "<?php echo lang2('cancel') ?>";
    lang.attention = "<?php echo lang2('attention') ?>";
</script>

<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>
<script src="<?php echo base_url('assets/lib/chartjs/dist/Chart.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/whatsapp.js?v=1.1.130'); ?>"></script>