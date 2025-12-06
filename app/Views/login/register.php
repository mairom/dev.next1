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

        .was-validated .form-control:invalid,
        .form-control.is-invalid,
        .was-validated .custom-select:invalid,
        .custom-select.is-invalid {
            border-color: #dc3545;
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
                            <div class="panel-heading">
                                <img src="<?php echo base_url('uploads/ciuis_settings/' . $rebrand['nav_logo'] . ''); ?>" alt="logo" class="logo-img nav-logo">
                                <?php echo $rebrand['title'] ?>
                                <span class="splash-description">Informe seu dados para se registrar</span>
                            </div>
                            <div class="panel-body">
                                <?php echo form_open('login/register_form', array('name' => 'userForm', 'id' => 'formRegister')) ?>

                                <div class="form-group">
                                    <label>Tipo de pessoa</label>
                                    <select name="tp_pessoa" id="tp_pessoa" placeholder class="form-control" style="min-width: 200px;">
                                        <option value="1">Pessoa física</md-option>
                                        <option value="2">Pessoa jurídica</md-option>
                                    </select>
                                </div>

                                <div class="dvtp_pessoa pessoa_2" style="display:none">
                                    <div class="form-group">
                                        <input id="cnpj" type="text" placeholder="Cnpj" name="cnpj" autocomplete="off" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <input id="razao_social" type="text" placeholder="Razão social" name="razao_social" autocomplete="off" class="form-control">
                                    </div>
                                </div>

                                <div class="dvtp_pessoa pessoa_1">
                                    <div class="form-group">
                                        <input id="cpf" type="text" placeholder="Cpf" name="cpf" autocomplete="off" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <input id="name" type="text" placeholder="Nome" name="name" autocomplete="off" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <input id="telefone" type="text" placeholder="Telefone" name="phone" autocomplete="off" class="form-control">
                                </div>

                                <div class="form-group">
                                    <input id="email" type="text" placeholder="<?php echo lang2('loginemail') ?>" name="email" autocomplete="off" class="form-control">
                                </div>

                                <div class="form-group">
                                    <input id="password" type="password" name="password" placeholder="<?php echo lang2('loginpassword') ?>" class="form-control">
                                </div>

                                <p>
                                    <input type="checkbox" require class="form-check-input">
                                    Concordo com os <a data-toggle="modal" data-target="#modalTermosECondicoes" href="javaScript:void(0)"><b>Termos e condições</b></a>
                                </p>

                                <div class="form-group login-submit">
                                    <button type="button" class="login-button btn btn-ciuis btn-xl btn_registrar">Registrar</button>
                                </div>
                                <?php echo '<label class="text-danger">' . session()->getFlashdata("error") . '</label>'; ?>
                            </div>
                        </div>
                    </md-card>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalApresentacao" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Apresentação</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTermosECondicoes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Termos e condições</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>



    <script src="<?php echo base_url('assets/lib/jquery/jquery.min.js'); ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('assets/lib/bootstrap/dist/js/bootstrap.min.js'); ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('assets/lib/jquery.gritter/js/jquery.gritter.js'); ?>" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
          //  $('#modalApresentacao').modal('show');

            $("#tp_pessoa").change(function() {
                var val = $(this).val();
                $('.dvtp_pessoa').hide();
                $('.pessoa_' + val).show();
            });

            $(".btn_registrar").click(function() {
                console.log('ddd')
                if ($('#tp_pessoa').val() == '2') {
                    if ($('#cnpj').val().length == 0) {
                        $('#cnpj').addClass('is-invalid');
                        return;
                    } else {
                        $('#cnpj').removeClass('is-invalid');
                    }

                    if ($('#razao_social').val().length == 0) {
                        $('#razao_social').addClass('is-invalid');
                        return;
                    } else {
                        $('#razao_social').removeClass('is-invalid');
                    }
                } else {
                    if ($('#cpf').val().length == 0) {
                        $('#cpf').addClass('is-invalid');
                        return;
                    } else {
                        $('#cpf').removeClass('is-invalid');
                    }

                    if ($('#name').val().length == 0) {
                        $('#name').addClass('is-invalid');
                        return;
                    } else {
                        $('#name').removeClass('is-invalid');
                    }
                }
                if ($('#telefone').val().length == 0) {
                    $('#telefone').addClass('is-invalid');
                    return;
                } else {
                    $('#telefone').removeClass('is-invalid');
                }

                if ($('#email').val().length == 0) {
                    $('#email').addClass('is-invalid');
                    return;
                } else {
                    $('#email').removeClass('is-invalid');
                }

                if ($('#password').val().length == 0) {
                    $('#password').addClass('is-invalid');
                    return;
                } else {
                    $('#password').removeClass('is-invalid');
                }

                $('#formRegister').submit();


            });



        });
    </script>

</body>

</html>