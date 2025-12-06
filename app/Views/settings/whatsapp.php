<md-content class="md-padding bg-white">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 400px;
            margin: 50px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
        }

        h1 {
            color: #25D366;
            margin-bottom: 10px;
        }

        p {
            color: #555;
            font-size: 15px;
            margin-bottom: 25px;
        }

        img.qrcode {
            width: 250px;
            height: 250px;
            margin-bottom: 20px;
        }

        button {
            background-color: #d9534f;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 15px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #c9302c;
        }

        .status {
            font-weight: bold;
            margin-top: 15px;
            color: #666;
        }
    </style>


    <div class="row">
        <div class="col-12 col-md-6">
            <div class="container">


                <h1>Conecte nosso robô ao WhatsApp</h1>

                <p ng-if="!apiwhats.connected">
                    Escaneie o QR Code abaixo para conectar nosso robô ao seu WhatsApp
                    <i class="la la-whatsapp"></i>:
                </p>

                <div ng-if="!apiwhats.connected && !apiwhats.connecting">
                    <!-- Campo para inserir o número -->
                    <label>WhatsApp</label>
                    <input ng-model="apiwhats.number" class="phone-input ng-pristine ng-valid md-input ng-empty ng-touched" type="text" id="number" placeholder="Ex: 11 987654321" />
                    <button class="btn-success" ng-click="connect()">Conectar</button>
                </div>

                <!-- QR Code -->
                <div class="qr-code" ng-if="!apiwhats.connected">
                    <img ng-src="{{apiwhats.qrCodeUrl}}" alt="QR Code para conexão" width="200" height="200" id="qrCode" style="display: none;margin: 0 auto;">
                </div>

                <div class="status-wpp" ng-if="apiwhats.connected">
                    <p><strong>✅ Conectado à API do WhatsApp!</strong></p>

                    <button class="btn-danger" ng-click="disconnect()">Desconectar - {{apiwhats.number}}</button>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">

            <md-input-container class="md-block" flex-gt-xs>
                <label>Usar a IA para ações ativas e receptivas</label>
                <md-select placeholder="Usar a IA para ações ativas e receptivas" ng-model="apiwhats.settings.usar_ia">
                    <md-option ng-value="1">Sim</md-option>
                    <md-option ng-value="0">Não</md-option>
                </md-select>
            </md-input-container>

            <md-input-container class="md-block">
                <label>Link</label>
                <input ng-model="apiwhats.settings.link">
            </md-input-container>

            <md-input-container class="md-block">
                <label>key do assistente</label>
                <input ng-model="apiwhats.settings.key">
            </md-input-container>

            <md-input-container class="md-block " style="margin-top: 3em;">
              <label>Funil para novos leads</label>
              <md-select placeholder="" ng-model="apiwhats.settings.funil_list" style="min-width: 200px;">
                <md-option ng-value="list.id_list" ng-repeat="list in leadslist">{{list.nm_list}}</md-option>
              </md-select>
            </md-input-container>

            <md-input-container class="md-block" style="margin-top: 3em;">
              <label>Etapa do Funil para novos leads</label>
              <md-select placeholder="Etapa do Funil" ng-model="apiwhats.settings.status_id" style="min-width: 200px;">
                <md-option ng-value="status.id" ng-repeat="status in leadslist[apiwhats.settings.funil_list].leadstatuses">{{status.name}}</md-option>
              </md-select>
            </md-input-container>


            <md-content class="md-padding bg-white">
                <md-button ng-click="salva_settings_ia()" class="md-raised md-primary pull-right successButton">
                    <span>Salvar</span>
                </md-button>
            </md-content>


        </div>
    </div>
</md-content>