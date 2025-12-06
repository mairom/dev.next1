<?php include_once(APPPATH . 'Views/inc/header.php'); ?>

<div class="ciuis-body-content" ng-controller="Email_Controller">


  <div ng-show="!template_loader" class="main-content container-fluid col-xs-12 col-md-12 col-lg-8">
    <md-toolbar class="toolbar-white">
      <div class="md-toolbar-tools">
        <h3 class="md-pl-10" flex md-truncate><?php echo lang2('email_template') ?>

        </h3>

        <?php if (check_privilege('emails', 'edit')) { ?>

          <md-switch  ng-model="template.status" aria-label="Type" ng-cloak>
            <md-tooltip md-direction="top"><?php echo lang2('template_active_message') ?></md-tooltip>
            <strong class="text-muted"><?php echo lang2('active') ?></strong>
            <md-tooltip md-direction="top"><?php echo lang2('template_active_message') ?></md-tooltip>
          </md-switch>

        <?php } ?>

        <md-switch ng-if="template.isAttachment" ng-model="template.attachment" aria-label="Type" ng-cloak>
          <md-tooltip md-direction="top"><?php echo lang2('email_with_attachment') ?></md-tooltip>
          <strong class="text-muted"><?php echo lang2('attachment') ?></strong>
          <md-tooltip md-direction="top"><?php echo lang2('email_with_attachment') ?></md-tooltip>
        </md-switch>

      </div>

    </md-toolbar>

    <md-content class="bg-white">

      <md-content class="task-detail bg-white" layout-padding ng-cloak>

        <h4>

          <strong><span ng-bind="template.name"></span></strong>

        </h4>

        <br>

        <md-input-container class="md-block">
          <label><?php echo lang2('email') . ' ' . lang2('subject') ?></label>
          <input required type="text" ng-model="template.subject" class="form-control" placeholder="<?php echo lang2('subject'); ?>" />
        </md-input-container>


        <md-input-container class="md-block">
          <label><?php echo lang2('email_body') ?></label>
          <br>
          <textarea class="tinymce" ng-model="template.message"></textarea>
        </md-input-container>


        <md-input-container class="md-block">
          <label style="margin-bottom: 17px;">Anexo</label>
          <input type="file" ng-model="template.anexo" id="anexo" name="anexo" file-model="anexo">
        </md-input-container>

        <?php if (check_privilege('emails', 'edit')) { ?>
          <md-button ng-click="SendEmail()" class="template-button" ng-disabled="saving == true">
            <span ng-hide="saving == true"><?php echo lang2('send'); ?></span>
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
<script src="<?php echo base_url('assets/js/emails.js'); ?>"></script>

<script>
  var TEMPLATEID = "<?php echo $TEMPLATEID; ?>";
  var EMAILSEND = "<?= $_GET['email']?>";
  var customer = "<?= $_GET['customer']?>";
  var company_name = "<?= $_GET['company_name']?>";
  var id_lead = "<?= $_GET['id_lead']?>";


  tinymce.init({
    selector: 'textarea',
    theme: 'modern',
    //height: 200,
    editor_selector: "mceEditor",
    theme: 'modern',
    valid_elements: '*',
    valid_styles: '*',
    plugins: 'print preview searchreplace autoresize autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking importcss anchor code insertdatetime advlist lists textcolor wordcount imagetools  contextmenu colorpicker textpattern',
    //body_class: 'my_class',
    valid_children: "+body[style]",
    valid_elements: "@[id|class|title|style],"
      +
      "a[name|href|target|title|alt],"
      +
      "#p,-ol,,div,h1,h2,h3,h4,h5,h6,strong,-ul,-li,br,img[src|unselectable],-sub,-sup,-b,-i,-u,"
      +
      "-span[data-mce-type],hr",
    valid_child_elements: "body[p,ol,ul,div,h1,h2,h3,h4,h5,h6,strong,b]"
      +
      ",p[a|span|b|i|u|sup|sub|img|hr|#text]"
      +
      ",span[a|b|i|u|sup|sub|img|#text]"
      +
      ",a[span|b|i|u|sup|sub|img|#text]"
      +
      ",b[span|a|i|u|sup|sub|img|#text]"
      +
      ",i[span|a|b|u|sup|sub|img|#text]"
      +
      ",sup[span|a|i|b|u|sub|img|#text]"
      +
      ",sub[span|a|i|b|u|sup|img|#text]"
      +
      ",li[span|a|b|i|u|sup|sub|img|ol|ul|#text]"
      +
      ",ol[li]"
      +
      ",ul[li]",

    content_css: [
      //fonts.googleapis.com/css?family=Lato:300,300i,400,400i’,
      //www.tinymce.com/css/codepen.min.css’
    ],
    toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat'
  });
</script>