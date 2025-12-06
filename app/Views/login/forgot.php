<?php $rebrand = load_config(); ?>
<!DOCTYPE html>
<html lang="<?php echo lang2('lang_code'); ?>">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="<?php echo $rebrand['meta_description'] ?>">
	<meta name="keywords" content="<?php echo $rebrand['meta_keywords'] ?>">
	<link rel="shortcut icon" href="<?php echo base_url('assets/img/images/' . $rebrand['favicon_icon'] . ''); ?>">
	<title>
		<?php echo lang2('forgotpassword') ?>
	</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/lib/material-design-icons/css/material-design-iconic-font.min.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/lib/jquery.gritter/css/jquery.gritter.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/ciuis.css" type="text/css" />
</head>

<?php

$arr = session()->getFlashdata();

if (!empty($arr['ntf1'])) {

	$html = '<div class="bg-warning container flash-message">';

	$html .= $arr['ntf1'];

	$html .= '</div>';

	echo $html;
}

?>

<body class="ciuis-body-splash-screen">

	<div class="ciuis-body-wrapper ciuis-body-login">

		<div class="ciuis-body-content">

			<div class="col-md-4 login-left hide-xs hide-sm" style="background-image: url(<?php echo base_url('assets/img/images/' . $rebrand['admin_login_image'] . ''); ?>) !important;">

				<div class="lg-content">

					<h2><?php echo $rebrand['title'] ?></h2>

					<p class="text-muted"><?php echo $rebrand['admin_login_text'] ?></p>
					<!--
	        <a href="area" class="btn btn-warning md-raised md-warn p-l-20 p-r-20"><?php echo lang2('clientarea') ?></a>
-->
				</div>

			</div>

			<div class="main-content container-fluid col-md-8 login_page_right_block">

				<div class="splash-container">

					<md-card flex-xs flex-gt-xs="100" layout="column">

						<div class="panel panel-default">

							<div class="panel-heading">

								<h3><?php echo lang2('forgotpassword') ?></h3>

								<span class="splash-description"><?php echo lang2('forgotpasswordsub') ?></span>

							</div>

							<div class="panel-body">

								<?php

								echo form_open(base_url() . 'login/forgot', array('class' => 'form-signin')); ?>

								<div class="form-group">

									<input id="email" required="" type="email" placeholder="<?php echo lang2('loginemail') ?>" name="email" autocomplete="off" class="form-control">

								</div>



								<div class="form-group login-submit">

									<button type="submit" class="login-button btn btn-ciuis btn-xl"><?php echo lang2('submit'); ?></button>

								</div>

								<?php echo form_close(); ?>

							</div>

						</div>

					</md-card>

				</div>

			</div>

		</div>

	</div>

</body>

<script src="<?php echo base_url(); ?>assets/lib/jquery/jquery.min.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>assets/lib/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>assets/lib/jquery.gritter/js/jquery.gritter.js" type="text/javascript"></script>



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