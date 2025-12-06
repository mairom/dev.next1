function Settings_Controller($scope, $http, $mdDialog, $mdSidenav, $sce, fileUpload, $q) {
	"use strict";
	let LATEST_VERSION = null;
	$scope.tp_atividade = 'leads';
	$scope.planos = [];
	$scope.assinatura = [];
	$scope.fases_funil = [];
	$scope.motivos_pausa = [];
	$scope.prioridade_modal = [];
	$scope.permissions_all = [];
	$scope.permissionModalLoader = false;
	$scope.permissionModal = [];
	$scope.permissionModal.permission = [];
	$scope.id_plan = 0;
	$scope.indexTab = indexTab;
	$scope.prioridades = [];
	$scope.icons_atv = [
		'_.png', 'activity.png', 'almoco.png', 'ancora.png', 'archive.png', 'atividade.png', 'attached.png', 'barchart.png',
		'bell.png', 'board.png', 'briefcase.png', 'circle.png', 'config.png', 'congrat.png', 'copy.png', 'creditcard.png', 'database.png',
		'document.png', 'dolar.png', 'edit.png', 'filedisk.png', 'filter.png', 'flag.png', 'followup.png', 'followup1.png', 'gift.png',
		'good.png', 'heart.png', 'house.png', 'inbox.png', 'insta.png', 'internet.png', 'key.png', 'leadone.png', 'link.png', 'linkedin.png',
		'mail.png', 'map.png', 'message.png', 'microphone.png', 'minimize.png', 'monitor.png', 'navigator.png', 'open.png', 'pen.png',
		'peopleM.png', 'phone.png', 'piechart.png', 'plus.png', 'pocket.png', 'point.png', 'poweron.png', 'repeat.png', 'send.png',
		'share.png', 'signature.png', 'skype.png', 'sliders.png', 'smile.png', 'star.png', 'sundown.png', 'sunrise.png', 'tag.png',
		'target.png', 'thermometer.png', 'time.png', 'time2.png', 'trash.png', 'triangle.png', 'truck.png', 'umbrella.png', 'unlock.png',
		'upscale.png', 'watch.png', 'website.png', 'whatsapp2.png', 'whatsapp.png'
	];
	$scope.atividadeModal = [];
	$scope.selectIcon = function (icon) {
		$scope.atividadeModal.icon_atvSelect = icon;
		$scope.atividade_photo = "";
		$('#atividade_photo').val('');
	};
	$scope.close = function () {
		$mdDialog.hide();
		$mdSidenav('CreateCustomField').close();
		$mdSidenav('FieldDetail').close();
		$mdSidenav('RestoreDatabaseNav').close();
		$mdSidenav('uploadAppFiles').close();
		$mdSidenav('RunMySQL').close();
		$scope.viewPassword = false;
	};
	$scope.getStates = function (country) {
		$http.get(BASE_URL + 'api/get_states/' + country).then(function (States) {
			$scope.states = States.data;
		});
	};
	function buildToggler(navID) {
		$mdSidenav(navID).toggle();
	}
	$scope.uploadAppFiles = function () {
		$mdSidenav('uploadAppFiles').toggle();
	};
	$scope.RunMySQL = function () {
		$mdSidenav('RunMySQL').toggle();
	};
	$http.get(BASE_URL + 'api/languages').then(function (Languages) {
		$scope.languages = Languages.data;
	});
	$http.get(BASE_URL + 'api/currencies').then(function (Currencies) {
		$scope.currencies = Currencies.data;
	});
	$http.get(BASE_URL + 'api/countries').then(function (Countries) {
		$scope.countries = Countries.data;
		var countries = JSON.stringify($scope.countries);
	});
	$http.get(BASE_URL + 'api/timezones').then(function (Timezones) {
		$scope.timezones = Timezones.data;
	});
	$http.get(BASE_URL + 'api/accounts').then(function (Accounts) {
		$scope.accounts = Accounts.data;
	});
	$http.get(BASE_URL + 'leads/leadslist').then(function (data) {
		$scope.leadslist = data.data;
	});
	$scope.systemInfo = function (ev) {
		$mdDialog.show({
			templateUrl: 'system_info.html',
			scope: $scope,
			preserveScope: true,
			targetEvent: ev
		});
	};
	$scope.UninstallLicense = function (ev) {
		$mdDialog.show({
			templateUrl: 'uninstall.html',
			scope: $scope,
			preserveScope: true,
			targetEvent: ev
		});
	};
	$scope.activateModuleToggle = function (ev) {
		$mdDialog.show({
			templateUrl: 'activate_module.html',
			scope: $scope,
			preserveScope: true,
			targetEvent: ev
		});
	};
	globals.module_settings = function () {
		$http.get(BASE_URL + 'settings/get_modules', config).then(function (response) {
			$scope.module = response.data;
		});
	};
	$scope.checkingModule = false;
	$scope.activateModule = function () {
		$scope.checkingModule = true;
		var dataObj = $.param({
			license_key: $scope.module_license_key,
		});
		$http.post(BASE_URL + 'modules/activate_module/hr', dataObj, config)
			.then(
				function (response) {
					$scope.checkingModule = false;
					if (response.data.success == true) {
						$mdDialog.cancel();
						showToast(NTFTITLE, response.data.message, ' success');
						globals.module_settings();
					} else {
						showToast(NTFTITLE, response.data.message, ' danger');
					}
				},
				function (response) {
					$scope.checkingModule = false;
				}
			);
	};
	$scope.checkingModuleLicense = false;
	$scope.enableModule = function (ev) {
		$scope.checkingModuleLicense = true;
		$http.post(BASE_URL + 'modules/enable_module/hr', config)
			.then(
				function (response) {
					$scope.checkingModuleLicense = false;
					if (response.data.success == true) {
						showToast(NTFTITLE, response.data.message, ' success');
						globals.module_settings();
					} else {
						showToast(NTFTITLE, response.data.message, ' danger');
						$mdDialog.show({
							templateUrl: 'activate_module.html',
							scope: $scope,
							preserveScope: true,
							targetEvent: ev
						});
					}
				},
				function (response) {
					$scope.checkingModuleLicense = false;
				}
			);
	};
	$scope.disablingModule = false;
	$scope.disableModule = function () {
		$scope.disablingModule = true;
		var confirm = $mdDialog.confirm()
			.title('Disable HR Module')
			.textContent('Are you sure, you want to disable HR Module')
			.ariaLabel('Disable HR Module')
			.targetEvent()
			.ok('Disable')
			.cancel('Cancel');
		$mdDialog.show(confirm).then(function () {
			$http.post(BASE_URL + 'modules/disable_module/hr', config)
				.then(
					function (response) {
						$scope.disablingModule = false;
						if (response.data.success == true) {
							showToast(NTFTITLE, response.data.message, ' success');
							globals.module_settings();
						} else {
							showToast(NTFTITLE, response.data.message, ' danger');
						}
					},
					function (response) {
						$scope.disablingModule = false;
					}
				);
		});
	};
	$scope.executing = false;
	$scope.RunMySQLQuery = function () {
		$scope.executing = true;
		if (!$scope.mysql_query) {
			var dataObj = $.param({
				mysql_query: '',
			});
		} else {
			var dataObj = $.param({
				mysql_query: $scope.mysql_query,
			});
		}
		var config = {
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
			}
		};
		$http.post(BASE_URL + 'settings/execute_mysql_query', dataObj, config)
			.then(
				function (response) {
					$scope.executing = false;
					if (response.data.success == true) {
						globals.mdToast('success', response.data.message);
						$mdSidenav('RunMySQL').close();
						$scope.mysql_query = '';
					} else if (response.data.success == 'info') {
						globals.mdToast('success', response.data.message);
						showToast(NTFTITLE, response.data.info_message, ' success');
						console.log('%cResult for your following query: ' + '%c' +
							$scope.mysql_query + '', 'font-weight: 800', 'color: blue;font-weight: bold');
						console.log(response.data.result);
						$scope.mysql_query = '';
					} else {
						globals.mdToast('error', response.data.message);
					}
				},
				function (response) {
					$scope.executing = false;
				}
			);
	};
	$scope.RunMySQLFile = function () {
		$scope.executing = true;
		var dataObj = {
			file: $scope.sql_run_file,
		};
		var uploadUrl = BASE_URL + 'settings/run_sql_file';
		fileUpload.uploadFileWithData(dataObj, uploadUrl, function (response) {
			if (response.success == true) {
				$('input[type="file"]').val('');
				globals.mdToast('success', response.message);
				if (response.error) {
					if (response.error.message) {
						showToast(NTFTITLE, response.error.message, ' warning');
					}
				}
				$mdSidenav('RunMySQL').close();
			} else {
				globals.mdToast('error', response.message);
			}
			$scope.executing = false;
		});
	};
	$scope.replying = false;
	$scope.saveCustomization = function () {
		$scope.replying = true;
		var dataObj = {
			applogo: $scope.applogo_img,
			navlogo: $scope.navlogo_img,
			admin_login_image: $scope.admin_login_image_img,
			client_login_image: $scope.client_login_image_img,
			favicon: $scope.favicon_img,
			back_lead1_img: $scope.back_lead1_img,
			title: $scope.rebrand.title,
			preloader: $scope.preloader,
			admin_login_text: $scope.rebrand.admin_login_text,
			client_login_text: $scope.rebrand.client_login_text,
			meta_keywords: $scope.rebrand.meta_keywords,
			meta_description: $scope.rebrand.meta_description,
			support_button_title: $scope.rebrand.support_button_title,
			support_button_link: $scope.rebrand.support_button_link,
			disable_preloader: ($scope.rebrand.disable_preloader == true) ? '1' : '0',
			enable_support_button_on_client: ($scope.rebrand.enable_support_button_on_client == true) ? '1' : '0',
		};
		var uploadUrl = BASE_URL + 'settings/save_config';
		fileUpload.uploadFileWithData(dataObj, uploadUrl, function (response) {
			if (response.success == true) {
				$('input[type="file"]').val('');
				$scope.admin_login_image_img = '';
				$scope.client_login_image_img = '';
				$scope.favicon_img = '';
				$scope.back_lead1_img = '';
				$scope.applogo_img = '';
				$scope.navlogo_img = '';
				$http.get(BASE_URL + 'api/load_config').then(function (Data) {
					$scope.rebrand = Data.data;
				});
				$http.get(BASE_URL + 'api/settings_detail').then(function (Settings) {
					$scope.settings_detail = Settings.data;
				});
				showToast(NTFTITLE, response.message, ' success');
			} else {
				showToast(NTFTITLE, response.message, ' danger');
			}
			$scope.replying = false;
		});
	};
	$scope.get_payment_methods = function () {
		var deferred = $q.defer();
		$http.get(BASE_URL + 'settings/get_payment_methods').then(function (Data) {
			$scope.gateways = Data.data;
			deferred.resolve();
		});
	}
	$scope.UpdatePaymentGateway = function (relation) {
		$scope.saving = true;
		var dataObj = $.param({
			input_value1: $scope.gateway.input_value1,
			input_value2: $scope.gateway.input_label2 ? $scope.gateway.input_value2 : null,
			input_value3: $scope.gateway.input_label3 ? $scope.gateway.input_value3 : null,
			payment_record_account: $scope.gateway.payment_record_account,
			active: +$scope.gateway.active,
			sandbox_account: +$scope.gateway.sandbox_account,
		});
		$http.post(BASE_URL + 'settings/update_payment_gateway/' + relation, dataObj, config)
			.then(
				function (response) {
					$scope.saving = false;
					if (response.data.success == true) {
						$mdDialog.cancel();
						showToast(NTFTITLE, response.data.message, ' success');
					} else {
						showToast(NTFTITLE, response.data.message, ' danger');
					}
				},
				function (response) {
					$scope.saving = false;
				}
			);
	};
	$scope.paymentGateway = function (gateway) {
		$scope.gateway = gateway;
		$mdDialog.show({
			templateUrl: 'update_payment_method.html',
			scope: $scope,
			preserveScope: true,
			targetEvent: gateway
		});
	};
	$scope.get_database_backup = function () {
		$scope.dbLoader = true;
		var deferred = $q.defer();
		$scope.db_list = {
			order: '',
			limit: 5,
			page: 1
		};
		$scope.promise = deferred.promise;
		$http.get(BASE_URL + 'settings/get_backup').then(function (Data) {
			$scope.db_backup = Data.data;
			deferred.resolve();
			$scope.limitOptions = [5, 10, 15, 20];
			if ($scope.db_backup.length > 20) {
				$scope.limitOptions = [5, 10, 15, 20, $scope.db_backup.length];
			}
			$scope.dbLoader = false;
			$scope.BackupDatabase = function (ev) {
				$mdDialog.show({
					templateUrl: 'backup.html',
					parent: angular.element(document.body),
					clickOutsideToClose: false,
					fullscreen: false,
					escapeToClose: false
				});
				$http.post(BASE_URL + 'settings/db_backup').then(function (Backup) {
					if (Backup.data.success == true) {
						$.gritter.add({
							title: '<b>' + NTFTITLE + '</b>',
							text: Backup.data.message,
							class_name: 'color success'
						});
						$http.get(BASE_URL + 'settings/get_backup').then(function (Data) {
							$scope.db_backup = Data.data;
						});
					} else {
						$.gritter.add({
							title: '<b>' + NTFTITLE + '</b>',
							text: Backup.data.message,
							class_name: 'color danger'
						});
					}
					$mdDialog.cancel();
				});
			};
		});
	}
	$scope.RestoreDatabase = function (ev) {
		buildToggler('RestoreDatabaseNav');
	};
	$scope.Restoring = function (id) {
		$mdDialog.show({
			templateUrl: 'restoring.html',
			parent: angular.element(document.body),
			clickOutsideToClose: false,
			fullscreen: false,
			escapeToClose: false
		});
	};
	$scope.RestoreBackup = function (id) {
		var confirm = $mdDialog.confirm()
			.title($scope.lang.restorethisfile)
			.textContent($scope.lang.restorethisfile_msg)
			.ariaLabel('Restore this file')
			.targetEvent(id)
			.ok($scope.lang.restore)
			.cancel($scope.lang.cancel);
		$mdDialog.show(confirm).then(function () {
			$mdDialog.show({
				templateUrl: 'restoring.html',
				parent: angular.element(document.body),
				clickOutsideToClose: false,
				fullscreen: false,
				escapeToClose: false
			});
			var config = {
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
				}
			};
			$http.post(BASE_URL + 'settings/restore_backup/' + id, config)
				.then(
					function (response) {
						console.log(response);
						if (response.data.success == true) {
							window.location.href = BASE_URL + 'login/logout';
						} else {
							$mdDialog.cancel();
							$.gritter.add({
								title: '<b>' + NTFTITLE + '</b>',
								text: response.data.message,
								class_name: 'color danger'
							});
						}
					},
					function (response) {
						console.log(response);
					}
				);
		}, function () { });
	};
	$scope.RemoveBackup = function (id) {
		var confirm = $mdDialog.confirm()
			.title($scope.lang.remove_database_backup)
			.textContent($scope.lang.remove_database_backup_msg)
			.ariaLabel('Delete Backup Field')
			.targetEvent(id)
			.ok($scope.lang.delete)
			.cancel($scope.lang.cancel);
		$mdDialog.show(confirm).then(function () {
			var config = {
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
				}
			};
			$http.post(BASE_URL + 'settings/remove_backup/' + id, config)
				.then(
					function (response) {
						$mdDialog.cancel();
						if (response.data.success == true) {
							$.gritter.add({
								title: '<b>' + NTFTITLE + '</b>',
								text: response.data.message,
								class_name: 'color success'
							});
							$http.get(BASE_URL + 'settings/get_backup').then(function (Data) {
								$scope.db_backup = Data.data;
							});
						} else {
							$.gritter.add({
								title: '<b>' + NTFTITLE + '</b>',
								text: response.data.danger,
								class_name: 'color success'
							});
						}
					},
					function (response) {
						console.log(response);
					}
				);
		}, function () {
			//
		});
	};
	$scope.VersionCheck = function (ev) {
		$scope.updated = false;
		$mdDialog.show({
			templateUrl: 'version-check-template.html',
			scope: $scope,
			preserveScope: true,
			targetEvent: ev
		});
		$http.get(BASE_URL + 'settings/version_details').then(function (Version) {
			$scope.versions = Version.data;
			$scope.version_number = Version.data.versions_name;
			$scope.updated = false;
		}, function (error) {
			showToast(NTFTITLE, update_error, ' danger');
		});
	};
	$scope.checkForUpdates = function (ev) {
		$mdDialog.show({
			templateUrl: 'checking.html',
			parent: angular.element(document.body),
			clickOutsideToClose: false,
			fullscreen: false,
			escapeToClose: false
		});
		$http.get(BASE_URL + 'settings/version_detail').then(function (Version) {
			$scope.Version_detail = Version.data.settings.versions_name;
			$scope.Version_latest = Version.data.version.version_number;
			if ($scope.Version_latest) {
				LATEST_VERSION = $scope.Version_latest;
			}
			$scope.msg = Version.data.msg;
			$scope.updated = Version.data.updated;
			$scope.version_log = $sce.trustAsHtml(Version.data.list_array_log);
			$scope.changeLog = $sce.trustAsHtml(Version.data.version_changelog);
			$mdDialog.cancel();
			$mdDialog.show({
				templateUrl: 'version-check-template.html',
				scope: $scope,
				preserveScope: true,
				targetEvent: ev
			});
		}, function (error) {
			showToast(NTFTITLE, update_error, ' danger');
		});
	};
	$scope.install_update = false;
	$scope.downloadUpdate = function (ev) {
		if (LATEST_VERSION) {
			$mdDialog.show({
				templateUrl: 'updating.html',
				parent: angular.element(document.body),
				clickOutsideToClose: false,
				fullscreen: false,
				escapeToClose: false
			});
			var dataObj = $.param({
				version_number: LATEST_VERSION
			});
			var config = {
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
				}
			};
			$http.post(BASE_URL + 'settings/download_update', dataObj, config).then(function (response) {
				if (response.data.success) {
					$scope.install_update = true;
					$mdDialog.show({
						templateUrl: 'install-update.html',
						scope: $scope,
						preserveScope: true,
						targetEvent: ev
					});
					globals.mdToast('success', response.data.message);
				} else {
					globals.mdToast('error', response.data.message);
				}
			});
		} else {
			showToast(NTFTITLE, 'ERROR', 'danger');
		}
	};
	$scope.installing = false;
	$scope.installed = false;
	$scope.installed_message = '';
	$scope.installUpdate = function (ev) {
		if (LATEST_VERSION) {
			$scope.installing = true;
			var dataObj = $.param({
				version_number: LATEST_VERSION
			});
			var config = {
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
				}
			};
			$http.post(BASE_URL + 'settings/install_update', dataObj, config).then(function (response) {
				if (response.data.success) {
					globals.mdToast('success', response.data.message);
					$scope.installed_message = response.data.message;
					if (response.data.error) {
						if (response.data.error.message) {
							showToast(response.data.warning, response.data.error.message, ' warning');
						}
					}
					$scope.installing = false;
					$scope.installed = true;
					setTimeout(function () {
						location.reload(true);
					}, 1500);
				} else {
					$scope.installing = false;
					globals.mdToast('error', response.data.message);
				}
			});
		} else {
			showToast(NTFTITLE, 'ERROR', 'danger');
		}
	};
	$scope.uninstall_confirm = false;
	$scope.uninstalling = false;
	$scope.RemoveLicense = function (ev) {
		$scope.uninstalling = true;
		var dataObj = $.param({
			confirm: +$scope.uninstall_confirm
		});
		var config = {
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
			}
		};
		$http.post(BASE_URL + 'settings/uninstall', dataObj, config).then(function (response) {
			if (response.data.success) {
				globals.mdToast('success', response.data.message);
				setTimeout(function () {
					location.reload(true);
				}, 1500);
			} else {
				$scope.uninstalling = false;
				globals.mdToast('error', response.data.message);
			}
		});
	};
	$http.get(BASE_URL + 'api/load_config').then(function (Data) {
		$scope.rebrand = Data.data;
		if ($scope.rebrand.enable_support_button_on_client == '1') {
			$scope.rebrand.enable_support_button_on_client = true;
		} else {
			$scope.rebrand.enable_support_button_on_client = false;
		}
	});
	$scope.settings = {};
	$scope.settings.loader = true;
	$http.get(BASE_URL + 'api/settings_detail').then(function (Settings) {
		$scope.settings_detail = Settings.data;
		$scope.motivos_pausa = Settings.data.motivos_pausa;
		$scope.settings.loader = false;
		$scope.getStates($scope.settings_detail.country_id);
		$scope.sendingTestEmail = false;
		$scope.sendTestEmail = function () {
			$scope.sendingTestEmail = true;
			var dataObj = $.param({
				email: $scope.settings_detail.testEmail
			});
			var config = {
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
				}
			};
			var posturl = BASE_URL + 'settings/sendTestEmail';
			$http.post(posturl, dataObj, config)
				.then(
					function (response) {
						$scope.sendingTestEmail = false;
						if (response.data.success) {
							showToast(NTFTITLE, response.data.message, ' success');
						} else {
							showToast(NTFTITLE, response.data.message, ' danger');
						}
					},
					function (response) {
						$scope.sendingTestEmail = false;
						showToast(NTFTITLE, email_error, ' danger');
					});
		};
		if ($scope.settings_detail.pushState === '1') {
			$scope.settings_detail.pushState = true;
		} else {
			$scope.settings_detail.pushState = false;
		}
		if ($scope.settings_detail.two_factor_authentication === '1') {
			$scope.settings_detail.two_factor_authentication = true;
		} else {
			$scope.settings_detail.two_factor_authentication = false;
		}
		if ($scope.settings_detail.voicenotification === '1') {
			$scope.settings_detail.voicenotification = true;
		} else {
			$scope.settings_detail.voicenotification = false;
		}
		if ($scope.settings_detail.paypalenable === '1') {
			$scope.settings_detail.paypalenable = true;
		} else {
			$scope.settings_detail.paypalenable = false;
		}
		if ($scope.settings_detail.paypalsandbox === '1') {
			$scope.settings_detail.paypalsandbox = true;
		} else {
			$scope.settings_detail.paypalsandbox = false;
		}
		if ($scope.settings_detail.authorize_enable === '1') {
			$scope.settings_detail.authorize_enable = true;
		} else {
			$scope.settings_detail.authorize_enable = false;
		}
		if ($scope.settings_detail.is_mysql == '1') {
			$scope.settings_detail.is_mysql = true;
		} else {
			$scope.settings_detail.is_mysql = false;
		}
		$scope.savingSettings = false;
		$scope.UpdateSettings = function () {
			$scope.savingSettings = true;
			var dataObj = $.param({
				crm_name: $scope.settings_detail.crm_name,
				company: $scope.settings_detail.company,
				email: $scope.settings_detail.email,
				address: $scope.settings_detail.address,
				city: $scope.settings_detail.city,
				town: $scope.settings_detail.town,
				state: $scope.settings_detail.state,
				country_id: $scope.settings_detail.country_id,
				zipcode: $scope.settings_detail.zipcode,

				key_gpt: $scope.settings_detail.key_gpt,
				assistent_gpt: $scope.settings_detail.assistent_gpt,
				resumo: $scope.settings_detail.resumo,

				phone: $scope.settings_detail.phone,
				fax: $scope.settings_detail.fax,
				vatnumber: $scope.settings_detail.vatnumber,
				taxoffice: $scope.settings_detail.taxoffice,
				currencyid: $scope.settings_detail.currencyid,
				termtitle: $scope.settings_detail.termtitle,
				termdescription: $scope.settings_detail.termdescription,
				dateformat: $scope.settings_detail.dateformat,
				languageid: $scope.settings_detail.languageid,
				default_timezone: $scope.settings_detail.default_timezone,
				smtphost: $scope.settings_detail.smtphost,
				smtpport: $scope.settings_detail.smtpport,
				emailcharset: $scope.settings_detail.emailcharset,
				email_encryption: $scope.settings_detail.email_encryption,
				smtpusername: $scope.settings_detail.smtpusername,
				smtppassoword: $scope.settings_detail.smtppassoword,
				sendermail: $scope.settings_detail.sendermail,
				sender_name: $scope.settings_detail.sender_name,
				accepted_files_formats: $scope.settings_detail.accepted_files_formats,
				allowed_ip_adresses: $scope.settings_detail.allowed_ip_adresses,
				pushState: $scope.settings_detail.pushState,
				voicenotification: $scope.settings_detail.voicenotification,
				state_id: $scope.settings_detail.state_id,
				is_mysql: $scope.settings_detail.is_mysql,

				ramo_atividade: $scope.settings_detail.ramo_atividade,
				descricao_empresa: $scope.settings_detail.descricao_empresa,
				descricao_operacao: $scope.settings_detail.descricao_operacao,
				descricao_equipe: $scope.settings_detail.descricao_equipe,


				inv_prefix: $scope.finance.inv_prefix,
				inv_suffix: $scope.finance.inv_suffix,
				project_prefix: $scope.finance.project_prefix,
				project_suffix: $scope.finance.project_suffix,
				order_prefix: $scope.finance.order_prefix,
				order_suffix: $scope.finance.order_suffix,
				expense_suffix: $scope.finance.expense_suffix,
				expense_prefix: $scope.finance.expense_prefix,
				proposal_suffix: $scope.finance.proposal_suffix,
				proposal_prefix: $scope.finance.proposal_prefix,
				tax_label: $scope.finance.tax_label,
				invoice_series: $scope.finance.invoice_series,
				project_series: $scope.finance.project_series,
				product_series: $scope.finance.product_series,
				order_series: $scope.finance.order_series,
				proposal_series: $scope.finance.proposal_series,
				vendor_series: $scope.finance.vendor_series,
				customer_series: $scope.finance.customer_series,
				expense_series: $scope.finance.expense_series,
				lead_series: $scope.finance.lead_series,
				ticket_series: $scope.finance.ticket_series,
				staff_series: $scope.finance.staff_series,
				task_series: $scope.finance.task_series,
				purchase_series: $scope.finance.purchase_series,
				product_prefix: $scope.finance.product_prefix,
				vendor_prefix: $scope.finance.vendor_prefix,
				customer_prefix: $scope.finance.customer_prefix,
				lead_prefix: $scope.finance.lead_prefix,
				ticket_prefix: $scope.finance.ticket_prefix,
				staff_prefix: $scope.finance.staff_prefix,
				purchase_prefix: $scope.finance.purchase_prefix,
				task_prefix: $scope.finance.task_prefix,
				deposit_series: $scope.finance.deposit_series,
				deposit_prefix: $scope.finance.deposit_prefix,
				thousand_separator: $scope.settings_detail.thousand_separator,
				decimal_separator: $scope.settings_detail.decimal_separator,
				currency_position: $scope.settings_detail.currency_position,
				currency_display: $scope.settings_detail.currency_display,
				//	email_type: $scope.settings_detail.email_type,
				imapHost: $scope.settings_detail.imapHost,
				imapPort: $scope.settings_detail.imapPort,
				imapUsername: $scope.settings_detail.imapUsername,
				imapPassoword: $scope.settings_detail.imapPassoword,
				imap_sendermail: $scope.settings_detail.imap_sendermail,
				imap_email_type: $scope.settings_detail.imap_email_type,
				motivos_pausa: $scope.motivos_pausa,
			});
			var config = {
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
				}
			};
			var posturl = BASE_URL + 'settings/update/ciuis';
			$http.post(posturl, dataObj, config)
				.then(
					function (response) {
						$scope.savingSettings = false;
						if (response.data.success == true) {
							globals.mdToast('success', response.data.message);
						} else {
							globals.mdToast('error', response.data.message);
						}
					},
					function (response) {
						$scope.savingSettings = false;
						showToast(NTFTITLE, 'Error', ' danger');
					}
				);
		};
		$scope.viewPassword = false;
		$scope.viewing = false;
		$scope.viewSMTPPassword = function () {
			$scope.viewing = true;
			var dataObj = $.param({
				password: $scope.your_login_password,
			});
			var config = {
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
				}
			};
			var posturl = BASE_URL + 'settings/get_smtp_password';
			$http.post(posturl, dataObj, config)
				.then(
					function (response) {
						$scope.viewing = false;
						if (response.data.success == true) {
							$scope.viewPassword = true;
							$scope.final_smtp_password = response.data.password;
						} else {
							$scope.viewPassword = false;
							showToast(NTFTITLE, response.data.message, ' danger');
						}
					},
					function (response) {
						$scope.viewPassword = false;
						$scope.viewing = false;
						showToast(NTFTITLE, 'Error', ' danger');
					}
				);
		};
	});
	$scope.get_assinatura = function () {
		$http.get(BASE_URL + 'settings/get_assinatura').then(function (data) {
			$scope.assinatura = data.data.result;
			$scope.planos = data.data.planos;
		});
	};
	$scope.get_planos = function () {
		$http.get(BASE_URL + 'settings/get_planos').then(function (data) {
			$scope.planos = data.data.planos;
		});
	};
	$scope.get_custom_fields = function () {
		$scope.customfieldLoader = true;
		var deferred = $q.defer();
		$scope.customfield_list = {
			order: '',
			limit: 10,
			page: 1
		};
		$scope.promise = deferred.promise;
		$http.get(BASE_URL + 'api/custom_fields').then(function (custom_fields) {
			$scope.custom_fields = custom_fields.data;
			deferred.resolve();
			$scope.limitOptions = [5, 10, 15, 20];
			if ($scope.custom_fields.length > 20) {
				$scope.limitOptions = [5, 10, 15, 20, $scope.custom_fields.length];
			}
			$scope.customfieldLoader = false;
		});
	};
	$http.get(BASE_URL + 'api/get_appconfig').then(function (Data) {
		$scope.finance = Data.data;
		$scope.finance.decimal_separator = $scope.finance.decimal_separator;
		$scope.finance.thousand_separator = $scope.finance.thousand_separator;
		$scope.finance.invoice_series = ($scope.finance.invoice_series) ? (+$scope.finance.invoice_series) : '';
		$scope.finance.project_series = ($scope.finance.project_series) ? (+$scope.finance.project_series) : '';
		$scope.finance.product_series = ($scope.finance.product_series) ? (+$scope.finance.product_series) : '';
		$scope.finance.order_series = ($scope.finance.order_series) ? (+$scope.finance.order_series) : '';
		$scope.finance.proposal_series = ($scope.finance.proposal_series) ? (+$scope.finance.proposal_series) : '';
		$scope.finance.vendor_series = ($scope.finance.vendor_series) ? (+$scope.finance.vendor_series) : '';
		$scope.finance.customer_series = ($scope.finance.customer_series) ? (+$scope.finance.customer_series) : '';
		$scope.finance.expense_series = ($scope.finance.expense_series) ? (+$scope.finance.expense_series) : '';
		$scope.finance.lead_series = ($scope.finance.lead_series) ? (+$scope.finance.lead_series) : '';
		$scope.finance.ticket_series = ($scope.finance.ticket_series) ? (+$scope.finance.ticket_series) : '';
		$scope.finance.staff_series = ($scope.finance.staff_series) ? (+$scope.finance.staff_series) : '';
		$scope.finance.purchase_series = ($scope.finance.purchase_series) ? (+$scope.finance.purchase_series) : '';
		$scope.finance.task_series = ($scope.finance.task_series) ? (+$scope.finance.task_series) : '';
		$scope.finance.deposit_series = ($scope.finance.deposit_series) ? (+$scope.finance.deposit_series) : '';
	});
	$scope.CreateCustomField = function () {
		buildToggler('CreateCustomField');
	};
	$scope.GetFieldDetail = function (id) {
		$http.get(BASE_URL + 'api/custom_field_data_by_id/' + id).then(function (selected_field) {
			$scope.selected_field = selected_field.data;
			$scope.AddOptionToField = function () {
				$scope.selected_field.data.push({
					name: $scope.selected_field.new_option_name,
				});
				for (var i = 0; i < $scope.selected_field.data.length; i++) {
					$scope.selected_field.data[i].id = i;
				}
				$scope.selected_field.new_option_name = null;
			};
			$scope.RemoveFieldOption = function (index) {
				$scope.selected_field.data.splice(index, 1);
			};
			$scope.UpdateCustomField = function () {
				$scope.saving_customfield = true;
				if ($scope.selected_field.type === 'select') {
					$scope.field_data = JSON.stringify($scope.selected_field.data);
				} else {
					$scope.field_data = null;
				}
				var dataObj = $.param({
					name: $scope.selected_field.name,
					type: $scope.selected_field.type,
					order: $scope.selected_field.order,
					data: $scope.field_data,
					relation: $scope.selected_field.relation,
					icon: '',
					permission: $scope.selected_field.permission,
				});
				var config = {
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
					}
				};
				var posturl = BASE_URL + 'settings/update_custom_field/' + $scope.selected_field.id;
				$http.post(posturl, dataObj, config)
					.then(
						function (response) {
							$scope.saving_customfield = false;
							if (response.data.success == true) {
								$mdSidenav('FieldDetail').close();
								globals.mdToast('success', response.data.message);
								$http.get(BASE_URL + 'api/custom_fields').then(function (custom_fields) {
									$scope.custom_fields = custom_fields.data;
								});
								$scope.new_custom_field = [];
							} else {
								globals.mdToast('error', response.data.message);
							}
						},
						function (response) {
							$scope.saving_customfield = true;
							console.log(response);
						}
					);
			};
		});
	};
	$scope.RemoveCustomField = function (index) {
		var field = $scope.custom_fields[index];
		var confirm = $mdDialog.confirm()
			.title($scope.lang.remove_custom_field)
			.textContent($scope.lang.custom_field_remove_msg)
			.ariaLabel('Delete Custom Field')
			.targetEvent(index)
			.ok($scope.lang.delete)
			.cancel($scope.lang.cancel);
		$mdDialog.show(confirm).then(function () {
			var config = {
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
				}
			};
			$http.post(BASE_URL + 'settings/remove_custom_field/' + index, config)
				.then(
					function (response) {
						$scope.saving_customfield = true;
						if (response.data.success == true) {
							globals.mdToast('success', response.data.message);
							$http.get(BASE_URL + 'api/custom_fields').then(function (custom_fields) {
								$scope.custom_fields = custom_fields.data;
							});
						} else {
							globals.mdToast('error', response.data.message);
						}
					},
					function (response) {
						console.log(response);
					}
				);
		}, function () { });
	};
	$scope.FieldDetail = function () {
		buildToggler('FieldDetail');
	};
	$scope.select_options = [];
	$scope.new_custom_field = {
		permission: false,
		new_option_name: ''
	};
	$scope.AddOption = function () {
		$scope.select_options.push({
			name: $scope.new_custom_field.new_option_name,
		});
		for (var i = 0; i < $scope.select_options.length; i++) {
			$scope.select_options[i].id = i;
		}
		$scope.new_custom_field.new_option_name = null;
	};
	$scope.RemoveOption = function (index) {
		$scope.select_options.splice(index, 1);
	};
	$scope.AddCustomField = function () {
		$scope.saving_customfield = true;
		if ($scope.new_custom_field.type === 'select') {
			$scope.field_data = JSON.stringify($scope.select_options);
		} else {
			$scope.field_data = null;
		}
		var dataObj = $.param({
			name: $scope.new_custom_field.name,
			type: $scope.new_custom_field.type,
			order: $scope.new_custom_field.order,
			data: $scope.field_data,
			relation: $scope.new_custom_field.relation,
			icon: '',
			permission: $scope.new_custom_field.permission,
			active: 'true',
		});
		var config = {
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
			}
		};
		var posturl = BASE_URL + 'settings/create_custom_field';
		$http.post(posturl, dataObj, config)
			.then(
				function (response) {
					$scope.saving_customfield = false;
					if (response.data.success == true) {
						$mdSidenav('CreateCustomField').close();
						globals.mdToast('success', response.data.message);
						$http.get(BASE_URL + 'api/custom_fields').then(function (custom_fields) {
							$scope.custom_fields = custom_fields.data;
						});
					} else {
						globals.mdToast('error', response.data.message);
					}
				},
				function (response) {
					$scope.saving_customfield = false;
					console.log(response);
				}
			);
	};
	$scope.UpdateCustomFieldStatus = function (id, value) {
		$http.post(BASE_URL + 'settings/update_custom_field_status/' + id + '/' + value + '')
			.then(
				function (response) {
					if (response.data.success == true) {
						$http.get(BASE_URL + 'api/custom_fields').then(function (custom_fields) {
							$scope.custom_fields = custom_fields.data;
						});
					} else {
						globals.mdToast('error', response.data.message);
					}
				},
				function (response) {
					console.log(response);
				}
			);
	};
	$scope.viewRole = true;
	$scope.editRole = false;
	$scope.createRole = false;
	$scope.getRoles = false;
	$scope.addRole = function () {
		$scope.getRoles = true;
		$http.get(BASE_URL + 'settings/get_permission').then(function (Permissions) {
			$scope.all_permissions = Permissions.data;
			$scope.viewRole = false;
			$scope.createRole = true;
			$scope.getRoles = false;
		});
	};
	$scope.get_permissions_by_type = function (type) {
		var permission_all = +$scope.permission_all;
		$http.get(BASE_URL + 'settings/get_permission/' + type + '/' + permission_all).then(function (Permissions) {
			$scope.all_permissions = Permissions.data;
		});
	};
	$scope.create_role = function () {
		$scope.creatingRole = true;
		var dataObj = $.param({
			'role': $scope.newrole.new_role_name,
			'type': $scope.newrole.usertype,
			'permissions': $scope.all_permissions
		});
		var posturl = BASE_URL + "settings/create_role";
		$http.post(posturl, dataObj, config)
			.then(
				function (response) {
					$scope.creatingRole = false;
					if (response.data.success == true) {
						$scope.get_roles();
						globals.mdToast('success', response.data.message);
						$scope.createRole = false;
						$scope.viewRole = true;
					} else {
						globals.mdToast('error', response.data.message);
					}
				});
	};
	$scope.salvarRole = function (role_id) {
		var dataObj = $.param({
			permissions_export: $scope.permissions_export.permission,
			role_id,
		});
		$http.post(BASE_URL + 'settings/savePermissonRole', dataObj, config).then(
			function (response) {
				if (response.data.success == true) {
					$('.modal').modal('hide');
					globals.mdToast('success', response.data.message);
				} else {
					globals.mdToast('error', response.data.message);
				}
			}, (response) => {
			}
		);
	};
	$scope.salvarPrioridade = function () {
		var dataObj = $.param({
			funil: $scope.prioridade_modal.funil,
			regras: $scope.prioridade_modal.regras,
			source_id: $scope.prioridade_modal.source_id,
			fases_funil: $scope.fases_funil,
			id_prioridade: $scope.prioridade_modal.id
		});
		$http.post(BASE_URL + 'settings/savePrioridade', dataObj, config).then(
			function (response) {
				if (response.data.success == true) {
					$('.modal').modal('hide');
					globals.mdToast('success', response.data.message);
					$http.get(BASE_URL + 'settings/get_prioridades').then(function (data) {
						$scope.prioridades = data.data.prioridades;
					});
				} else {
					globals.mdToast('error', response.data.message);
				}
			}, (response) => {
			}
		);
	};
	$scope.get_roles = function () {
		$http.get(BASE_URL + 'settings/get_roles').then(function (Roles) {
			$scope.roles = Roles.data;
		});
		$http.get(BASE_URL + 'settings/get_roles_export').then(function (data) {
			$scope.roles_export = data.data;
		});
	};
	$scope.modalPermissaoExport = function (role) {
		$scope.roleModal = role;
		$scope.permissions_export = [];
		$("#modalPermissaoExport").modal("show");
		$http.get(BASE_URL + 'settings/get_roles_export_by_role/' + role.role_id).then(function (data) {
			$scope.roles_export_modal = data.data;
		});
	};
	$scope.get_atividades = function () {
		$http.get(BASE_URL + 'settings/get_atividades').then(function (data) {
			$scope.atividades = data.data;
		});
		$http.get(BASE_URL + 'settings/get_atividades_customer').then(function (data) {
			$scope.atividadesCustomer = data.data;
		});
	};
	$scope.get_pagamentos = function () {
		$http.get(BASE_URL + 'api/get_frm_pagamentos').then(function (frm_pagamentos) {
			$scope.frm_pagamentos = frm_pagamentos.data;
		});
	};
	$scope.get_fila_prioridades = function () {

		$scope.selecionaFunil();
		$http.get(BASE_URL + 'leads/leadsources').then(function (LeadSources) {
			$scope.leadssources = LeadSources.data;
		});
		$http.get(BASE_URL + 'settings/get_prioridades').then(function (data) {
			$scope.prioridades = data.data.prioridades;
		});
	};
	$scope.selecionaFunil = function () {
		$scope.funil_fases = [];
		if ($scope.prioridade_modal.funil.includes("-1")) {
			$.each($scope.leadslist, (index, funil) => {
				funil.leadstatuses.map((status) => {
					$scope.funil_fases.push(status)
				});
			});
		} else {


			$.each($scope.leadslist, (index, funil) => {
				if ($scope.prioridade_modal.funil.includes(funil.id_list)) {
					funil.leadstatuses.map((status) => {
						$scope.funil_fases.push(status)
					});

				}
			});
		}
	};
	$scope.removePrioridade = function (key) {
		var dataObj = $.param({
			id_prioridade: $scope.prioridades[key].id,
		});
		$http.post(BASE_URL + 'settings/removePrioridade', dataObj, config).then(
			function (response) {
				if (response.data.success == true) {
					showToast('SUCESSO! ', response.data.message, 'success');
					$scope.prioridades.splice(key, 1);
				}
			}
		);
	};
	$scope.removeMotivo = function (key) {
		$scope.motivos_pausa.splice(key, 1);
	};
	$scope.addFase = function () {
		$scope.fases_funil.push({});
	};
	$scope.addMotivo = function () {
		var confirm = $mdDialog.prompt()
			.title('Adicionar novo motivo')
			.textContent('Informe o nome do motivo')
			.placeholder('Ex: Pausa')
			.ariaLabel('Motivo')
			.ok('Confirmar')
			.cancel('Cancelar');
		$mdDialog.show(confirm).then(function (a) {
			$scope.motivos_pausa.push({ name: a });
		});
	};
	$scope.addPagamento = function (atv = null) {
		$scope.pagamentoModal = [];
		if (atv != null) {
			$scope.pagamentoModal = atv;
		}
		$("#modalPagamento").modal("show");
	};
	$scope.addAviso = function (atv = null) {
		$scope.avisoModal = [];
		if (atv != null) {
			$scope.avisoModal = atv;
			$scope.avisoModal.tempo = parseFloat(atv.tempo);
			tinyMCE.activeEditor.setContent($scope.avisoModal.menssagem, { format: 'raw' });
			if (!Array.isArray(atv.funcionarios)) {
				if (atv.funcionarios != null) {
					$scope.avisoModal.funcionarios = atv.funcionarios.split(",");
				}
			}
			if (!Array.isArray(atv.emails)) {
				let emails = [];
				if (atv.emails != null) {
					emails = JSON.parse(JSON.stringify(atv.emails.split(",")));
				}
				$scope.avisoModal.emails = emails;
				$scope.avisoModal.emails_adicionados = emails;
			}
		}
		$("#modalAviso").modal("show");
	};
	$scope.dellAviso = function (id_aviso) {
		var dataObj = $.param({
			id_aviso,
			is_ativo: '0'
		});
		var posturl = BASE_URL + "settings/updt_aviso";
		$http.post(posturl, dataObj, config).then(function (response) {
			showToast('SUCESSO!', '', 'success');
			$scope.get_avisos();
		}, (err) => {
			showToast('Erro! ', '', 'danger');
		});
	}
	$scope.salvarAviso = function (id_aviso = null) {
		var menssagem = tinyMCE.activeEditor.getContent({ format: 'raw' });
		getBase64(document.querySelector("#anexo")).then((anexo) => {
			getBase64(document.querySelector("#alerta")).then((alerta) => {
				var dataObj = $.param({
					id_aviso,
					nm_aviso: $scope.avisoModal.nm_aviso,
					funcionarios: $scope.avisoModal.funcionarios,
					emails: $scope.avisoModal.emails,
					tempo: $scope.avisoModal.tempo,
					tipo: $scope.avisoModal.tipo,
					opcaoAlerta: $scope.avisoModal.opcaoAlerta,
					frequencia: $scope.avisoModal.frequencia,
					diaSemana: $scope.avisoModal.diaSemana,
					diaMes: $scope.avisoModal.diaMes,
					anexo,
					nm_file_anexo: $('#anexo')[0].files[0] != null ? $('#anexo')[0].files[0].name : null,
					alerta,
					nm_file_alerta: $('#alerta')[0].files[0] != null ? $('#alerta')[0].files[0].name : null,
					menssagem: menssagem.replace(/&nbsp;/g, ' ').replace(/;/g, '').replace(/&nbsp/g, ' '),
					is_ativo: '1'
				});
				var posturl = BASE_URL + "settings/updt_aviso";
				$http.post(posturl, dataObj, config).then(function (response) {
					if (response.data.success == true) {
						$scope.avisoModal = [];
						showToast('SUCESSO! ', response.message, 'success');
						$scope.dimiss();
						$scope.get_avisos();;
					} else {
						showToast('Erro! ', response.message, 'danger');
					}
				});
			});
		});
	};
	$scope.get_avisos = function () {
		$http.get(BASE_URL + 'settings/get_avisos').then(function (data) {
			$scope.avisos = data.data.result;
		});
		if ($scope.staff == null) {
			$http.get(BASE_URL + 'api/staff').then(function (Staff) {
				$scope.staff = Staff.data;
			});
		}
	};
	$scope.addPrioridade = function (prioridade = null) {
		$scope.prioridade_modal = [];
		$scope.prioridade_modal.funil = "-1";
		$scope.fases_funil = [];
		if (prioridade != null) {
			$scope.prioridade_modal = prioridade;
			prioridade.status.map((a) => {
				$scope.fases_funil.push({ id_status: a.id_status.split(",") })
			})
		}

		$scope.selecionaFunil();
		$("#modalPrioridade").modal("show");
	};
	$scope.salvarPagamentoList = function (id_forma = null) {
		var dataObj = $.param({
			id_forma,
			nm_forma: $scope.pagamentoModal.nm_forma,
			is_ativo: '1'
		});
		var posturl = BASE_URL + "api/updt_frm_pagamentos/";
		$http.post(posturl, dataObj, config).then(function (response) {
			if (response.data.success == true) {
				showToast('SUCESSO! ', response.message, 'success');
				$scope.dimiss();
				$scope.get_pagamentos();;
			} else {
				showToast('Erro! ', response.message, 'danger');
			}
		});
	};
	$scope.dellPagamentos = function (id_forma) {
		var dataObj = $.param({
			id_forma,
			is_ativo: '0'
		});
		var posturl = BASE_URL + "api/updt_frm_pagamentos/";
		$http.post(posturl, dataObj, config).then(function (response) {
			showToast('SUCESSO!', '', 'success');
			$scope.get_pagamentos();
		}, (err) => {
			showToast('Erro! ', '', 'danger');
		});
	}
	$scope.addAtividade = function (atv = null) {
		$scope.atividadeModal = [];
		$("#atividade_photo").val('');
		if (atv != null) {
			$scope.atividadeModal = atv;
		}
		$("#modalAtividades").modal("show");
	};
	$scope.dell_atividades = function (id) {
		var url = BASE_URL + 'settings/dell_atividades/' + id;
		if ($scope.tp_atividade == "clientes") {
			url = BASE_URL + 'settings/dell_atividades_custom/' + id;
		}
		$http.get(url).then(function (Role) {
			showToast('SUCESSO!', '', 'success');
			$scope.get_atividades();
		}, (err) => {
			showToast('Erro! ', '', 'danger');
		});
	}
	$scope.Permissions = function (id) {
		$scope.permissions_all = [];
		$scope.permissionModalLoader = true;
		$scope.permissionModal = [];
		$scope.permissionModal.permission = [];
		$scope.get_Permissions(id);
		buildToggler('Permissions');
	};
	$scope.get_Permissions = function (id) {
		$http.get(BASE_URL + 'settings/get_permission_planos/' + id).then(function (Role) {
			$scope.permissions_all = Role.data;
			$scope.permissionModalLoader = false;
			$scope.id_plan = id;
		});
	};
	$scope.AddPermission = function () {
		var dataObj = $.param({
			permissions: $scope.permissionModal.permission,
			id_plan: $scope.id_plan,
		});
		$http.post(BASE_URL + 'settings/AddPermission', dataObj, config).then(
			function (response) {
				if (response.data.success == true) {
					$mdSidenav('Permissions').close();
					globals.mdToast('success', response.data.message);
				} else {
					globals.mdToast('error', response.data.message);
				}
			}, (response) => {
			}
		);
	};
	$scope.salvarPlanoList = function (id_plan = null) {
		var dataObj = $.param({
			id_plan,
			nm_plan: $scope.planoModal.nm_plan,
			valor: moneyEua($scope.planoModal.valor),
			valor_anual: $scope.planoModal.valor_anual,
			min_user: $scope.planoModal.min_user,
			is_ativo: '1'
		});
		var posturl = BASE_URL + "settings/addPlano/";
		$http.post(posturl, dataObj, config).then(function (response) {
			if (response.data.success == true) {
				showToast('SUCESSO! ', response.message, 'success');
				$scope.dimiss();
				$scope.get_planos();
			} else {
				showToast('Erro! ', response.message, 'danger');
			}
		});
	};
	$scope.addPlanos = function (plano = null) {
		$scope.planoModal = [];
		if (plano != null) {
			$scope.planoModal = JSON.parse(JSON.stringify(plano));
			$scope.planoModal.valor = $scope.planoModal.valor.replace('.', ',');
			$scope.planoModal.valor_anual = parseInt($scope.planoModal.valor_anual);
			$scope.planoModal.min_user = parseInt($scope.planoModal.min_user);
		}
		$("#modalPlano").modal("show");
	};
	$scope.dell_planos = function (id) {
		var confirm = $mdDialog.confirm()
			.title('Tem certeza?')
			.textContent('Tem certeza que deseja remover esse plano?')
			.targetEvent()
			.ok('Sim')
			.cancel('Cancelar');
		$mdDialog.show(confirm).then(function () {
			$http.get(BASE_URL + 'settings/dell_planos/' + id).then(function (data) {
				showToast('SUCESSO!', '', 'success');
				$scope.get_planos();
			}, (err) => {
				showToast('Erro! ', '', 'danger');
			});
		});
	}
	$scope.salvarAtividadeList = function () {
		var file = $scope.atividade_photo;
		if (file) {
			$scope.atividadeModal.icon_atvSelect = '';
		}
		if ($scope.tp_atividade == "leads") {
			var uploadUrl = BASE_URL + 'settings/addAtividadeList?nm_atividade_select=' + $scope.atividadeModal.nm_atividade_select
				+ '&id_atv=' + ($scope.atividadeModal.id_atv != null ? $scope.atividadeModal.id_atv : '')
				+ '&icon_atvSelect=' + ($scope.atividadeModal.icon_atvSelect != null ? $scope.atividadeModal.icon_atvSelect : '');
		} else {
			var uploadUrl = BASE_URL + 'settings/addAtividadeListCustom?nm_atividade_select=' + $scope.atividadeModal.nm_atividade_select
				+ '&id_atv=' + ($scope.atividadeModal.id_atv != null ? $scope.atividadeModal.id_atv : '')
				+ '&icon_atvSelect=' + ($scope.atividadeModal.icon_atvSelect != null ? $scope.atividadeModal.icon_atvSelect : '');
		}
		fileUpload.uploadFileToUrl(file, uploadUrl, function (response) {
			if (response.success == true) {
				showToast('SUCESSO! ', response.message, 'success');
				$scope.dimiss();
				$scope.get_atividades();;
			} else {
				showToast('Erro! ', response.message, 'danger');
			}
		});
		/*
	var data = {
		file: file,
		nm_atividade_select: $scope.atividadeModal.nm_atividade_select,
		id_atv: $scope.atividadeModal.id_atv
	}
	$http.post(uploadUrl, data, config).then(function (response) {
	}, (err) => {
		showToast('Erro! ', err.message, 'danger');
	});
	*/
	};
	$scope.dimiss = function () {
		$(".modal").modal('hide');
	};
	$scope.get_role = function (id) {
		$scope.viewRole = false;
		$scope.editRole = true;
		$http.get(BASE_URL + 'settings/get_role/' + id).then(function (Role) {
			$scope.permissions = Role.data.permissions_data;
			$scope.role_name = Role.data.role_name;
			$scope.role_type = Role.data.role_type;
			$scope.role_id = id;
		});
	};
	$scope.update_role = function (id) {
		$scope.updatingRole = true;
		var dataObj = $.param({
			'role': $scope.role_name,
			'type': $scope.role_type,
			'permissions': $scope.permissions,
		});
		var posturl = BASE_URL + "settings/update_role/" + id;
		$http.post(posturl, dataObj, config).then(function (response) {
			$scope.updatingRole = false;
			if (response.data.success == true) {
				$scope.get_roles();
				globals.mdToast('success', response.data.message);
				$scope.editRole = false;
				$scope.viewRole = true;
			} else {
				globals.mdToast('error', response.data.message);
			}
		});
	};
	$scope.delete_role = function (id) {
		globals.deleteDialog(lang.attention, lang.delete_role_meesage, id, lang.doIt, lang.cancel, 'settings/delete_role/' + id, function (response) {
			if (response.success == true) {
				globals.mdToast('success', response.message);
				$scope.get_roles();
			} else {
				globals.mdToast('error', response.message);
			}
		});
	};
	$scope.cancel_role = function () {
		$scope.getRoles = false;
		$scope.viewRole = true;
		$scope.createRole = false;
		$scope.editRole = false;
		$scope.get_roles();
	};
	$scope.custom_fields_types = [{
		'id': '1',
		'type': 'input',
		'name': lang.input
	}, {
		'id': '2',
		'type': 'date',
		'name': lang.datepicker
	}, {
		'id': '3',
		'type': 'textarea',
		'name': lang.textarea
	}, {
		'id': '4',
		'type': 'select',
		'name': lang.select
	}];
	$scope.custom_fields_relation_types = [{
		'id': '1',
		'relation': 'invoice',
		'name': lang.invoice
	}, {
		'id': '2',
		'relation': 'proposal',
		'name': lang.proposal
	}, {
		'id': '3',
		'relation': 'customer',
		'name': lang.customer
	}, {
		'id': '4',
		'relation': 'task',
		'name': lang.task
	}, {
		'id': '5',
		'relation': 'project',
		'name': lang.project
	}, {
		'id': '7',
		'relation': 'expense',
		'name': lang.expense
	}, {
		'id': '8',
		'relation': 'product',
		'name': lang.product
	}, {
		'id': '9',
		'relation': 'lead',
		'name': lang.lead
	}];
	if ($scope.indexTab == 9) {
		$scope.get_assinatura();
	}
	var mp = new MercadoPago("APP_USR-e3b2ec36-4b05-4894-9aa0-18829b46369b");
	$scope.btnCC = function (plano) {
		$('.modal').modal('hide');
		$('#checkoutModal').modal('show');
		var id = plano.id_plan;
		var valor = plano.tipo == 30 ? plano.valor : (plano.valor_anual * 12);
		var tipo = plano.tipo;
		$("#spanValor").html(parseFloat(valor).toFixed(2).replace('.', ',').replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.'));
		id_plan = id;
		transactionAmount = valor;
		plan_tipo = tipo;
		if (cardForm != null) {
			cardForm.unmount();
		}
		console.log(transactionAmount);
		cardForm = mp.cardForm({
			amount: transactionAmount,
			iframe: false,
			form: {
				id: "form-checkout",
				cardNumber: {
					id: "cardNumber2",
				},
				expirationDate: {
					id: "cardexpiration",
				},
				securityCode: {
					id: "cardcvc",
				},
				cardholderName: {
					id: "cardname",
				},
				issuer: {
					id: "issuer",
				},
				installments: {
					id: "installments",
				},
				identificationType: {
					id: "docType",
				},
				identificationNumber: {
					id: "cardcpf2",
				},
				cardholderEmail: {
					id: "email",
				},
			},
			callbacks: {
				onFormMounted: error => {
					if (error) return console.warn("Form Mounted handling error: ", error);
					console.log("Form mounted");
				},
				onSubmit: event => {
					event.preventDefault();
					const {
						paymentMethodId: payment_method_id,
						issuerId: issuer_id,
						cardholderEmail: email,
						amount,
						token,
						installments,
						identificationNumber,
						identificationType,
					} = cardForm.getCardFormData();
					var data = {
						token,
						issuer_id,
						payment_method_id,
					};
					sendRequest(data);
					return;
				},
				onFetching: (resource) => {
					console.log("Fetching resource: ", resource);
				}
			},
		});
	}





	function sendRequest(data) {
		$.ajax({
			url: BASE_URL + 'settings/requestCard',
			type: 'post',
			data: {
				token: data.token,
				id_plan: id_plan,
				paymentMethodId: data.payment_method_id,
				issuer_id: data.issuer_id,
				plan_tipo,
				Issuers: 1,
				cpf: $('#cardcpf').val().toString().replace(/[^0-9]/g, '')
			}
		}).done(function (msg) {
			msg = JSON.parse(msg);
			if (msg.success == 200) {
				$('#cardNumber2').val('').change();
				$('#cardexpiration').val('').change();
				$('#cardcvc').val('').change();
				$('#cardname').val('').change();
				$('#cardcpf2').val('').change();
				$('#cardcpf').val('').change();
				$('#label-cardexpiration').html('00 / 0000');
				$('#label-cardnumber').html('0000 0000 0000 0000');
				$('#label-cvv').html('000');
				$('.payment2').hide();
				$('.payment').fadeToggle('slow', function () {
					$('.paid').fadeToggle('slow', 'linear');
				});
				$scope.get_assinatura();
			} else {
				showToast('Atenção', 'Ocorreu algum erro, tente novamente!', 'danger');
			}
		}).fail(function (jqXHR, textStatus, msg) {
			alert(msg);
		});
	}




	$scope.apiwhats = [];
	$scope.apiwhats.connected = false;
	$scope.apiwhats.connecting = false;

	$scope.apiwhats.qrCodeUrl = BASE_URL + '/uploads/load.gif';
	$scope.apiwhats.number = '';

	$scope.url_api = "https://apiwhats.ageup.pro:3000";



	$scope.salva_settings_ia = function () {

		console.log($scope.apiwhats.settings)

		if ($scope.apiwhats.settings.funil_list != null &&
			($scope.apiwhats.settings.status_id == null || $scope.apiwhats.settings.status_id == '0'
			)) {
			showToast(NTFTITLE, 'Informe um status para novos leads!', 'warning');
			return;
		}

		if ($scope.apiwhats.number != null && !$scope.apiwhats.number.startsWith("55")) {
			$scope.apiwhats.number = "55" + $scope.apiwhats.number;
		}



		var dataObj = $.param({
			connected: $scope.apiwhats.connected ? '1' : '0',
			number: $scope.apiwhats.number,
			usar_ia: $scope.apiwhats.settings.usar_ia,
			link: $scope.apiwhats.settings.link,
			key: $scope.apiwhats.settings.key,

			status_id: $scope.apiwhats.settings.status_id,
			funil_list: $scope.apiwhats.settings.funil_list,
		});
		$http.post(BASE_URL + 'settings/save_settingsIa', dataObj, config).then((response) => {
			showToast(NTFTITLE, response.data.message, 'success');
		}, (response) => {

		});

		if ($scope.apiwhats.connected) {
			$http.post($scope.url_api + '/saveSession', {
				number: $scope.apiwhats.number,
				key: $scope.apiwhats.settings.key,
				usar_ia: $scope.apiwhats.settings.usar_ia,
			}).then((response) => {
			}, (response) => {
				showToast(NTFTITLE, 'Erro ao sicronizar com a api, tente denovo mais tarde!', 'danger');
			});
		} else {
			$http.post($scope.url_api + '/disconnect', { number: $scope.apiwhats.number }).then((response) => {
			}, (response) => {
				showToast(NTFTITLE, 'Erro ao sicronizar com a api, tente denovo mais tarde!', 'danger');
			});
		}

	}


	$scope.get_settingsIa = function () {
		$scope.apiwhats.settings = [];
		$scope.apiwhats.settings.usar_ia = "0";
		$scope.apiwhats.settings.link = "";
		$scope.apiwhats.settings.key = "";
		$scope.apiwhats.settings.funil_list = '';
		$scope.apiwhats.settings.status_id = '';
		$scope.apiwhats.settings.funil_list = '';

		$http.get(BASE_URL + 'settings/get_settingsIa2').then(function (response) {
			$scope.apiwhats.settings = response.data;
			$scope.apiwhats.number = response.data.number;
			$scope.apiwhats.connected = response.data.connected == '1' ? true : false;
		});
	}



	$scope.disconnect = function () {
		$scope.apiwhats.connected = false;
		$scope.salva_settings_ia();


	}

	$scope.connect = function () {
		if (!$scope.apiwhats.number) {
			alert("Digite um número válido!");
			return;
		}

		$scope.apiwhats.connecting = true;
		var number = $scope.apiwhats.number.replace(/[+\.\s\-\(\)]/g, '');
		if (!number.startsWith("55")) {
			number = "55" + number;
		}
		$http.post($scope.url_api + '/connect', {
			number: number,
		}, {
			headers: {
				"Content-Type": "application/json",
			}
		}).then(function (response) {
			$scope.apiwhats.qrCodeUrl = BASE_URL + '/uploads/load.gif';
			document.getElementById("qrCode").style.display = "block";

			var interval = setInterval(function () {
				$http.get($scope.url_api + '/qr/' + number).then(function (qrResponse) {
					var data = qrResponse.data;
					if (data.success) {
						if (data.qrCode != null) {
							$scope.apiwhats.qrCodeUrl = data.qrCode;
						}
					} else {
						clearInterval(interval);

						if (data.connected) {
							$scope.apiwhats.connected = true;
							$scope.salva_settings_ia();


						}
					}
				}, function (error) {
					$scope.apiwhats.connecting = false;
					console.error('Erro ao buscar QR Code:', error);
					clearInterval(interval)
				});
			}, 3000);

			setTimeout(async () => {
				clearInterval(interval);
			}, 60000 * 2);

		}, function (error) {
			$scope.apiwhats.connecting = false;
			console.error('Erro ao fazer a requisição:', error);
			alert('Erro ao fazer a requisição. Tente novamente mais tarde.');
		});
	};


	$scope.get_zapier_linkedin = function () {
		$http.get(BASE_URL + 'settings/get_funis').then(function (response) {
			$scope.settings_funis.funis = response.data;
		});
	}

	$scope.settings_funis = { funis: {} };
	$scope.selectedFunil = null;
	$scope.tempStatus = null; // status temporário no modal

	$scope.leadslistZapier = angular.copy($scope.leadslist);

	// abre modal copiando os dados do funil selecionado
	$scope.openStatusModal = function (funil) {
		$scope.selectedFunil = funil;
		$scope.tempStatus = $scope.settings_funis.funis[funil.id_list] ? $scope.settings_funis.funis[funil.id_list].id : null;
		$('#modalStatus').modal('show');
	};

	// salva o funil atual no objeto e envia para o backend
	$scope.saveStatus = function () {
		if ($scope.selectedFunil && $scope.tempStatus) {
			var idList = $scope.selectedFunil.id_list;
			let name = $scope.selectedFunil.leadstatuses.find(a => a.id == $scope.tempStatus).name;
			$scope.settings_funis.funis[idList] = { id: $scope.tempStatus, name };

			// envia apenas o funil atual para o backend
			$http.post(BASE_URL + 'settings/save_funil', {
				id_list: idList,
				id_status: $scope.tempStatus
			}).then(function (response) {
				showToast(NTFTITLE, response.data.message, ' success');
			}).catch(function (err) {

				showToast(NTFTITLE, response.data.message, ' danger');

				console.error(err);
			});
		}
		$('#modalStatus').modal('hide');
	};

	$scope.copyLink = function (funil) {
		const copyText = document.createElement("textarea");
		copyText.value = BASE_URL + 'api/create_lead/' + encryptDataAlfanumerico(funil.id_list);
		copyText.select();
		copyText.setSelectionRange(0, 99999);
		navigator.clipboard.writeText(copyText.value);
		globals.mdToast('success', 'Copiado para area de transferência.');

	}


	$scope.form = {};
	$scope.docs = [];
	$scope.uploading = false;
	$scope.error = null;
	$scope.success = null;

	$scope.load_documents = function () {
		$http.get(BASE_URL + '/settings/list_documents').then(function (resp) {
			$scope.docs = resp.data;
		}, function (err) {
			console.error(err);
		});
	};

	$scope.upload_documents = function () {
		$scope.error = null;
		$scope.success = null;

		if (!$scope.form.name || !$scope.form.file) {
			$scope.error = 'Preencha nome e selecione um arquivo.';
			return;
		}

		var fd = new FormData();
		fd.append('name', $scope.form.name);
		fd.append('file', $scope.form.file);

		// adiciona CSRF token (CI4)

		$scope.uploading = true;
		$http.post(BASE_URL + '/settings/upload_documents', fd, {
			transformRequest: angular.identity,
			headers: { 'Content-Type': undefined }
		}).then(function (resp) {
			$scope.uploading = false;
			if (resp.data && resp.data.success) {
				$scope.success = 'Arquivo enviado com sucesso.';
				$scope.form = {}; // limpa
				document.querySelector('input[type=file]').value = null;
				$scope.load();
			} else {
				$scope.error = 'Resposta inesperada.';
			}
		}, function (err) {
			$scope.uploading = false;
			if (err.data && err.data.errors) {
				// concatena erros
				$scope.error = Object.values(err.data.errors).join(' | ');
			} else if (err.data && err.data.error) {
				$scope.error = err.data.error;
			} else {
				$scope.error = 'Erro no upload.';
			}
		});
	};

	$scope.remove_documents = function (id) {
		if (!confirm('Remover documento?')) return;
		$http.delete(BASE_URL + '/settings/delete_documents/' + id).then(function () {
			$scope.load();
		}, function (err) { console.error(err); alert('Erro ao excluir'); });
	};

	$scope.resetForm_documents = function () {
		$scope.form = {};
		document.querySelector('#fileInput').value = null;
		$scope.error = $scope.success = null;
		$scope.progress = 0;
	};


}
function getBase64(file) {
	file = file.files[0];
	if (file == null) {
		return new Promise(resolve => {
			resolve(null);
		})
	} else {
		var ext = file.name.split('.');
		ext = ext[ext.length - 1];
		const reader = new FileReader()
		return new Promise(resolve => {
			reader.onload = ev => {
				var result = ev.target.result;
				result = result.split('/')[0] + '/' + ext + ';' + result.split(';')[1]
				resolve(result);
			}
			reader.readAsDataURL(file)
		})
	}
}


function encryptDataAlfanumerico(data) {

	const iv = CryptoJS.enc.Utf8.parse('1234567812345678');  // IV fixo (pode gerar aleatório)
	const key = CryptoJS.enc.Utf8.parse('1245632112456321');
	// Criptografa
	const encrypted = CryptoJS.AES.encrypt(data, key, {
		iv: iv,
		mode: CryptoJS.mode.CBC,
		padding: CryptoJS.pad.Pkcs7
	});

	// Converte para hexadecimal (só 0-9 e a-f)
	return encrypted.ciphertext.toString(CryptoJS.enc.Hex);
}
CiuisCRM.controller('Settings_Controller', Settings_Controller);