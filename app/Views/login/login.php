<?php $rebrand = load_config(); ?>
<!DOCTYPE html>
<html lang="<?php echo lang2('lang_code'); ?>">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="<?php echo $rebrand['meta_description'] ?>">
  <meta name="keywords" content="<?php echo $rebrand['meta_keywords'] ?>">
  <meta name="author" content="">
  <link rel="shortcut icon" href="<?php echo base_url('assets/img/images/' . $rebrand['favicon_icon'] . ''); ?>">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed:700,300,300italic" rel="stylesheet" type="text/css">
  <title><?php echo lang2('loginsystem') ?></title>
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/ciuis.css'); ?>" type="text/css" />
  <script>
    var BASE_URL = "<?php echo base_url(); ?>",
      ACTIVESTAFF = "<?php echo session()->get('usr_id'); ?>",
      SHOW_ONLY_ADMIN = "false",
      CURRENCY = "false",
      LOCATE_SELECTED = "<?php echo lang2('lang_code'); ?>",
      UPIMGURL = "<?php echo base_url('uploads/images/'); ?>",
      IMAGESURL = "<?php echo base_url('assets/img/'); ?>",
      SETFILEURL = "<?php echo base_url('uploads/ciuis_settings/') ?>",
      NTFTITLE = "<?php echo lang2('notification') ?>",
      EVENTADDEDMSG = "<?php echo lang2('eventadded') ?>",
      TODOADDEDMSG = "<?php echo lang2('todoadded') ?>",
      TODODONEMSG = "<?php echo lang2('tododone') ?>",
      REMINDERREAD = "<?php echo lang2('remindermarkasread') ?>",
      INVMARKCACELLED = "<?php echo lang2('invoicecancelled') ?>",
      TICKSTATUSCHANGE = "<?php echo lang2('ticketstatuschanced') ?>",
      LEADMARKEDAS = "<?php echo lang2('leadmarkedas') ?>",
      LEADUNMARKEDAS = "<?php echo lang2('leadunmarkedas') ?>",
      TODAYDATE = "<?php echo date('Y.m.d ') ?>",
      LOGGEDINSTAFFID = "<?php echo session()->get('usr_id'); ?>",
      LOGGEDINSTAFFNAME = "<?php echo session()->get('staffname'); ?>",
      LOGGEDINSTAFFAVATAR = "<?php echo session()->get('staffavatar'); ?>",
      VOICENOTIFICATIONLANG = "<?php echo lang2('lang_code_dash'); ?>",
      initialLocaleCode = "<?php echo lang2('initial_locale_code'); ?>";
  </script>
  <style>
    .login-button2 {
      background: transparent;
      color: #000;
      border: 1px solid #a5a5a5;
    }
  </style>
</head>

<body class="ciuis-body-splash-screen">
  <div class="ciuis-body-wrapper ciuis-body-login">
    <div class="ciuis-body-content">
      <div class="col-md-4 login-left hide-xs hide-sm" style="background-image: url(<?php echo base_url('assets/img/images/' . $rebrand['admin_login_image'] . ''); ?>) !important;">
        <div class="lg-content">
          <h2><?php echo $rebrand['title'] ?></h2>
          <p class="text-muted"><?php echo $rebrand['admin_login_text'] ?></p>
        </div>
      </div>
      <div class="main-content container-fluid col-md-8 login_page_right_block">
        <div class="splash-container">
          <md-card flex-xs flex-gt-xs="100" layout="column">
            <div class="panel panel-default">
              <div class="panel-heading"><img src="<?php echo base_url('uploads/ciuis_settings/' . $rebrand['nav_logo'] . ''); ?>" alt="logo" class="logo-img nav-logo"> <?php echo $rebrand['title'] ?><span class="splash-description"><?php echo lang2('logindescription') ?></span>
              </div>
              <div class="panel-body">
                <?php echo form_open('login/auth', array('name' => 'userForm')) ?>
                <div class="form-group">
                  <input id="email" required="" type="email" placeholder="<?php echo lang2('loginemail') ?>" name="email" autocomplete="off" class="form-control">
                </div>
                <div class="form-group">
                  <input id="password" required="" type="password" name="password" placeholder="<?php echo lang2('loginpassword') ?>" class="form-control">
                </div>
                <div class="form-group row login-tools">
                  <div class="col-xs-6 login-remember">
                    <div class="ciuis-body-checkbox">
                      <input type="checkbox" name="remember" id="remember">
                      <label for="remember"><?php echo lang2('loginremember') ?></label>
                    </div>
                  </div>
                  <div class="col-xs-6 login-forgot-password"><a href="<?php echo base_url('login/forgot') ?>"><?php echo lang2('loginforget') ?></a> </div>
                </div>
                <div class="form-group">
                  <label><b><?php echo lang2('select_language') ?>:</b></label>
                  <select class="form-control" name="language" required="" style="padding: 4px;height: 30px;">
                    <?php foreach ($languages as $language) { ?>
                      <option value="<?php echo $language['foldername'] ?>" <?php if ($language['foldername'] == LANG) echo 'selected="selected"'; ?>>
                        <?php echo $language['name'] ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
                <div class="form-group login-submit">
                  <button style="margin-bottom: 10px;" type="submit" class="login-button btn btn-ciuis btn-xl"><?php echo lang2('loginbutton') ?></button>

                  <a style="background: linear-gradient(to top right, #8187f8, #6e92c4);color: #fff;" href="<?php echo base_url('login/register') ?>" class="login-button2 btn btn-ciuis btn-xl">Cadastre-se agora</a>
                  <p>Inicie agora o seu teste grátis por 15 dias</p>
                </div>
                <?php echo '<label class="text-danger">' . session()->getFlashdata("error") . '</label>'; ?>
              </div>
            </div>
          </md-card>
        </div>
      </div>
    </div>
  </div>
  <script src="<?php echo base_url('assets/lib/jquery/jquery.min.js'); ?>" type="text/javascript"></script>
  <script src="<?php echo base_url('assets/lib/bootstrap/dist/js/bootstrap.min.js'); ?>" type="text/javascript"></script>
  <script src="<?php echo base_url('assets/lib/jquery.gritter/js/jquery.gritter.js'); ?>" type="text/javascript"></script>

  <?php if (session()->getFlashdata('ntf1')) { ?>
    <script type="text/javascript">
      $.gritter.add({
        title: '<b><?php echo lang2('notification') ?></b>',
        text: '<?php echo session()->getFlashdata('ntf1'); ?>',
        class_name: 'color success'
      });
    </script>
  <?php } ?>
  <?php if (session()->getFlashdata('ntf2')) { ?>
    <script type="text/javascript">
      $.gritter.add({
        title: '<b><?php echo lang2('notification') ?></b>',
        text: '<?php echo session()->getFlashdata('ntf2'); ?>',
        class_name: 'color primary'
      });
    </script>
  <?php } ?>
  <?php if (session()->getFlashdata('ntf3')) { ?>
    <script type="text/javascript">
      $.gritter.add({
        title: '<b><?php echo lang2('notification') ?></b>',
        text: '<?php echo session()->getFlashdata('ntf3'); ?>',
        class_name: 'color warning'
      });
    </script>
  <?php } ?>
  <?php if (session()->getFlashdata('ntf4')) { ?>
    <script type="text/javascript">
      $.gritter.add({
        title: '<b><?php echo lang2('notification') ?></b>',
        text: '<?php echo session()->getFlashdata('ntf4'); ?>',
        class_name: 'color danger'
      });
    </script>
  <?php } ?>
</body>
</html>