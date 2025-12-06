<md-content class="md-padding bg-white">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 40px;
            font-family: 'Segoe UI', sans-serif;
        }

        h5 {
            margin: 0;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .text-white {
            color: #fff;
        }

        .file-label {
            display: inline-block;
            background: #0d6efd;
            color: #fff !important;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 8px;
        }

        .file-label:hover {
            background: #0b5ed7;
        }

        input[type="file"] {
            display: none;
        }

        .doc-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        .doc-item:last-child {
            border: none;
        }
    </style>

    <div class="col-md-6">
        <md-input-container class="md-block">
            <label>PROMPT IA</label>
            <textarea required name="resumo" style="min-height: 100px;" ng-model="settings_detail.resumo" rows="3"></textarea>
        </md-input-container>


    </div>

    <div class="col-md-6">

        <md-input-container class="md-block">
            <label><?php echo lang2('Key para Integração com GPT') ?></label>
            <input required name="key_gpt" ng-model="settings_detail.key_gpt"></input>
        </md-input-container>

        <md-input-container class="md-block">
            <label><?php echo lang2('Assistente para Integração com GPT') ?></label>
            <input required name="assistent_gpt" ng-model="settings_detail.assistent_gpt"></input>
        </md-input-container>

        <br>


        <!-- Formulário de Upload -->
        <div class="card mb-4">
            <div class="card-header bg-primary ">
                <h5 class="mb-0 text-white"><i class="bi bi-upload"></i> Enviar Documento</h5>
            </div>
            <div class="card-body">
                <form ng-submit="upload_documents()" novalidate>

                    <div class="mb-3">
                        <label class="form-label">Nome do Documento</label>
                        <input type="text" class="form-control" ng-model="form.name" placeholder="Ex: RG, Contrato, Comprovante..." required>
                    </div>

                    <div class="mb-3">
                        <label class="file-label" for="fileInput">
                            <i class="fas fa-paperclip"></i> Escolher arquivo
                        </label>
                        <input type="file" id="fileInput" file-model="form.file">
                        <span class="ms-2" ng-if="form.file">{{form.file.name}}</span>
                    </div>

                    <div class="progress mb-3" ng-if="progress > 0">
                        <div class="progress-bar" role="progressbar" style="width: {{progress}}%">
                            {{progress | number:0}}%
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-success" ng-disabled="uploading">
                            <i class="bi bi-cloud-arrow-up"></i> {{ uploading ? 'Enviando...' : 'Enviar' }}
                        </button>
                        <button type="button" class="btn btn-secondary ms-2" ng-click="resetForm_documents()" ng-disabled="uploading">
                            Limpar
                        </button>
                    </div>

                </form>

                <!-- Alertas -->
                <div class="alert alert-danger mt-3" ng-if="error">{{error}}</div>
                <div class="alert alert-success mt-3" ng-if="success">{{success}}</div>
            </div>
        </div>

        <!-- Lista de Documentos -->
        <div class="card">
            <div class="card-header bg-light">

                <h5 class="mb-0" style="flex: 1;"><i class="bi bi-folder2-open"></i> Documentos Enviados</h5>
                <button class="btn btn-outline-primary btn-sm" ng-click="load_documents()">
                    <i class="fas fa-sync-alt"></i> Atualizar
                </button>

            </div>
            <div class="card-body">
                <div ng-if="docs.length == 0" class="text-muted text-center">
                    Nenhum documento enviado ainda.
                </div>
                <div ng-repeat="d in docs" class="doc-item d-flex justify-content-between align-items-center">
                    <div style="width: 100%;">
                        <strong>{{d.name}}</strong><br>
                        <small class="text-muted">{{d.original_name}} — {{d.size | number}} bytes</small>
                    </div>
                    <div style="min-width: 70px;">
                        <a ng-href="/uploads/documents/{{d.filename}}" target="_blank" class="btn btn-sm btn-outline-success me-1">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button class="btn btn-sm btn-outline-danger" ng-click="remove_documents(d.id)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>



    </div>
</md-content>