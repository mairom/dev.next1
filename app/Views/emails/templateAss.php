<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>

<div class="ciuis-body-content" ng-controller="EmailAss_Controller">
    <div ng-show="!template_loader" class="main-content container-fluid col-xs-12 col-md-12 col-lg-8">
        <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">
                <h3 class="md-pl-10" flex md-truncate>Assinatura de email <?= $user_data['super_admin'] == "1" ? "(Irá servir de exemplo para novas empresas)" : "" ?></h3>
            </div>
        </md-toolbar>

        <md-content class="bg-white">
            <md-content class="task-detail bg-white" layout-padding ng-cloak>
                <md-input-container class="md-block">
                    <label>Assinatura</label>
                    <br>
                    <textarea class="tinymce" ng-model="template.message"></textarea>
                </md-input-container>

                <?php if (check_privilege('emails', 'edit')) { ?>
                    <md-button ng-click="UpdateTemplate()" class="template-button" ng-disabled="saving == true">
                        <span ng-hide="saving == true"><?php echo lang2('save'); ?></span>
                        <md-progress-circular class="white" ng-show="saving == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>
                    </md-button>
                <?php } ?>
            </md-content>
        </md-content>
        <md-divider></md-divider>


    </div>

    <div ng-show="!template_loader" class="main-content container-fluid col-xs-12 col-md-12 col-lg-4 md-pl-0 lead-left-bar">
        <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">
                <md-button class="md-icon-button" aria-label="Task">
                    <md-icon><i class="ion-ios-email-outline text-muted"></i></md-icon>
                </md-button>
                <md-truncate><?php echo lang2('email_fields') ?></md-truncate>
            </div>
        </md-toolbar>
        <div class="col-md-12 col-xs-12 md-pr-0 md-pl-0 md-pb-10 bg-white" ng-cloak>
            <div class="col-xs-12 task-sidebar-item">
                <ul class="list-inline task-dates">
                    <li class="col-md-6 col-xs-6" ng-repeat="field in template_fields">
                        <h5><strong ng-bind="field.name"></strong></h5>
                        <span style="color: #007eff;">
                            {<span ng-bind="field.value"></span>}
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include_once(APPPATH . 'Views/inc/footer.php'); ?>

<script src="<?php echo base_url('assets/lib/tinymce/tinymce.min.js') ?>"></script>
<script src="<?php echo base_url('assets/js/emails.js?v=1.2.1'); ?>"></script>

<script>
    var TEMPLATEID = 0;

    tinymce.init({
        selector: '.tinymce',
        image_title: true,
        automatic_uploads: true,
        file_picker_types: 'image',
        file_picker_callback: function(cb, value, meta) {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');

            input.onchange = function() {
                var file = this.files[0];
                var reader = new FileReader();

                reader.onload = function() {
                    var id = 'blobid' + (new Date()).getTime();
                    var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                    var base64 = reader.result.split(',')[1];
                    var blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);
                    cb(blobInfo.blobUri(), {
                        title: file.name
                    });
                };

                reader.readAsDataURL(file);
            };

            input.click();
        },

        // Adicionado para upload real ao servidor
        images_upload_url: '/settings/updateImage',
        images_upload_handler: function(blobInfo, success, failure) {
            var xhr, formData;

            xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', '/settings/updateImage');

            xhr.onload = function() {
                if (xhr.status !== 200) {
                    failure('Erro ao fazer upload: ' + xhr.responseText);
                    return;
                }

                var json;
                try {
                    json = JSON.parse(xhr.responseText);
                } catch (e) {
                    failure('Erro ao interpretar resposta JSON: ' + e);
                    return;
                }

                if (!json || typeof json.location !== 'string') {
                    failure('Resposta inválida do servidor');
                    return;
                }

                success(json.location);
            };

            formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());

            xhr.send(formData);
        },

        content_style: "body p, h1, h2, h3, h4, h5, h6{margin: 2px 5px;}",
        theme: 'modern',
        editor_selector: "mceEditor",
        valid_elements: '*',
        valid_styles: '*',
        plugins: 'print preview searchreplace autoresize autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking importcss anchor code insertdatetime advlist lists textcolor wordcount imagetools contextmenu colorpicker textpattern',
        valid_children: "+body[style]",
        valid_elements: "@[id|class|title|style],a[name|href|target|title|alt],#p,-ol,,div,h1,h2,h3,h4,h5,h6,strong,-ul,-li,br,img[src|unselectable],-sub,-sup,-b,-i,-u,-span[data-mce-type],hr",
        valid_child_elements: "body[p,ol,ul,div,h1,h2,h3,h4,h5,h6,strong,b]" +
            ",p[a|span|b|i|u|sup|sub|img|hr|#text]" +
            ",span[a|b|i|u|sup|sub|img|#text]" +
            ",a[span|b|i|u|sup|sub|img|#text]" +
            ",b[span|a|i|u|sup|sub|img|#text]" +
            ",i[span|a|b|u|sup|sub|img|#text]" +
            ",sup[span|a|i|b|u|sub|img|#text]" +
            ",sub[span|a|i|b|u|sup|img|#text]" +
            ",li[span|a|b|i|u|sup|sub|img|ol|ul|#text]" +
            ",ol[li]" +
            ",ul[li]",
        toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | removeformat'
    });
</script>