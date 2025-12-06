function Emails_Controller($scope, $http, $mdSidenav, $mdDialog, $filter, $sce) {
	'use strict';
	$scope.close = function () {
		$mdDialog.hide();
	};
	$scope.template = {};
	$scope.template.loader = true;
	$scope.template.loadEmails = true;
	$http.get(BASE_URL + 'emails/get_email_templates').then(function (Templates) {
		$scope.templates = Templates.data;
		$scope.template.loader = false;
	});
	$http.get(BASE_URL + 'emails/get_emails').then(function (Emails) {
		$scope.emails = Emails.data;
		$scope.template.loadEmails = false;
		$scope.viewEmail = function (index) {
			$scope.email = $scope.emails[index];
			for (var i = 0; i < $scope.emails.length; i++) {
				if ($scope.emails[i].id == index) {
					$scope.email = $scope.emails[i];
					continue;
				}
			}
			$mdDialog.show({
				templateUrl: 'emailDialog.html',
				scope: $scope,
				preserveScope: true,
				targetEvent: $scope.email.id
			});
			$scope.email.message = $sce.trustAsHtml($scope.email.message);
		};
		$scope.MoveToTrash = function (id) {
			var posturl = BASE_URL + 'emails/move_to_trash/' + id;
			$http.post(posturl, config).then(
				function (response) {
					if (response.data.success == true) {
						showToast(NTFTITLE, response.data.message, 'success');
						$mdDialog.hide();
						$http.get(BASE_URL + 'emails/get_emails').then(function (Emails) {
							$scope.emails = Emails.data;
						});
					}
				}, function () {
				}
			);
		};
		$scope.itemsPerPage = 10;
		$scope.currentPage = 0;
		$scope.range = function () {
			var rangeSize = 10;
			var ps = [];
			var start;
			start = $scope.currentPage;
			if (start > $scope.pageCount() - rangeSize) {
				start = $scope.pageCount() - rangeSize + 1;
			}
			for (var i = start; i < start + rangeSize; i++) {
				if (i >= 0) {
					ps.push(i);
				}
			}
			return ps;
		};
		$scope.prevPage = function () {
			if ($scope.currentPage > 0) {
				$scope.currentPage--;
			}
		};
		$scope.DisablePrevPage = function () {
			return $scope.currentPage === 0 ? "disabled" : "";
		};
		$scope.nextPage = function () {
			if ($scope.currentPage < $scope.pageCount()) {
				$scope.currentPage++;
			}
		};
		$scope.DisableNextPage = function () {
			return $scope.currentPage === $scope.pageCount() ? "disabled" : "";
		};
		$scope.setPage = function (n) {
			$scope.currentPage = n;
		};
		$scope.pageCount = function () {
			return Math.ceil($scope.emails.length / $scope.itemsPerPage) - 1;
		};
	});
}


function Email_Controller($scope, $http, $mdSidenav, $mdDialog, $filter) {
	'use strict';
	$scope.template = [];
	$scope.template_loader = true;

	$http.get(BASE_URL + 'emails/get_email_template/' + TEMPLATEID).then(function (Template) {
		if (Template.data != '') {
			$scope.template = Template.data;
		}
		$scope.template_loader = false;
		tinyMCE.activeEditor.setContent($scope.template.message, { format: 'raw' });
	});

	$http.get(BASE_URL + 'emails/template_fields/' + TEMPLATEID).then(function (Data) {
		$scope.template_fields = Data.data;
	});
	//tinyMCE.activeEditor.dom.addClass(tinyMCE.activeEditor.dom, 'myclass');
	$scope.saving = false;
	$scope.UpdateTemplate = function () {
		$scope.saving = true;
		var status, attachment;
		if ($scope.template.status == true) {
			status = 1;
		} else {
			status = 0;
		}
		if ($scope.template.attachment == true) {
			attachment = 1;
		} else {
			attachment = 0;
		}
		var data = tinyMCE.activeEditor.getContent({ format: 'raw' });
		var posturl = BASE_URL + 'emails/update_template/' + TEMPLATEID;

		getBase64(document.querySelector("#anexo")).then((arqv) => {
			var dataObj = $.param({
				subject: $scope.template.subject,
				from_name: $scope.template.from_name,
				relation: $scope.template.relation,
				message: data.replace(/&nbsp;/g, ' ').replace(/;/g, '').replace(/&nbsp/g, ' '),
				status: status,
				nm_file: $('#anexo')[0].files[0] != null ? $('#anexo')[0].files[0].name : null,
				attachment: attachment,
				anexo: arqv
			});


			$http.post(posturl, dataObj, config).then(
				function (response) {
					if (response.data.success == true) {
						showToast(NTFTITLE, response.data.message, 'success');
						if (TEMPLATEID == '0') {
							window.location.assign(BASE_URL + "emails/");
						}
					} else {
						showToast(NTFTITLE, response.data.message, 'warning');
					}
					$scope.saving = false;
				}, function () {
					$scope.saving = false;
				}
			);

		});
	};

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

	$scope.Delete = function () {
		globals.deleteDialog($scope.lang.delete + ' anexo', 'Você tem certeza que deseja excluir este email?',
			TEMPLATEID, $scope.lang.delete, $scope.lang.cancel, 'emails/remove/' + TEMPLATEID, function (response) {

				if (response.success == true) {
					window.location.href = BASE_URL + 'emails';
				} else {
					globals.mdToast('error', response.message);
				}
			});

	};


	$scope.removeAnexo = function () {
		globals.deleteDialog($scope.lang.delete + ' anexo', 'Você tem certeza que deseja excluir este anexo?',
			TEMPLATEID, $scope.lang.delete, $scope.lang.cancel, 'emails/removeAnexo/' + TEMPLATEID, function (response) {
				console.log(response)
				if (response.success == true) {
					showToast(NTFTITLE, response.message, 'success');
					$scope.template.anexo = null
				} else {
					globals.mdToast('error', response.message);
				}
			});
	}

	$scope.SendEmail = function () {
		$scope.saving = true;
		var status, attachment;
		if ($scope.template.status == true) {
			status = 1;
		} else {
			status = 0;
		}
		if ($scope.template.attachment == true) {
			attachment = 1;
		} else {
			attachment = 0;
		}
		var data = tinyMCE.activeEditor.getContent({ format: 'raw' });

		getBase64(document.querySelector("#anexo")).then((arqv) => {
			var dataObj = $.param({
				subject: $scope.template.subject,
				from_name: $scope.template.from_name,
				relation: $scope.template.relation,
				message: data.replace(/&nbsp;/g, ' ').replace(/;/g, '').replace(/&nbsp/g, ' '),
				status: status,
				attachment: attachment,
				anexo: arqv,
				customer: customer,
				company_name: company_name,
				id_lead: id_lead,
				nm_file: $('#anexo')[0].files[0] != null ? $('#anexo')[0].files[0].name : null,
				email: EMAILSEND
			});


			$http.post(BASE_URL + 'emails/sendEmail', dataObj, config).then(
				function (response) {
					if (response.data.success == true) {
						showToast(NTFTITLE, response.data.message, 'success');
					} else {
						showToast(NTFTITLE, response.data.message, 'warning');
					}
					$scope.saving = false;
				}, function () {
					$scope.saving = false;
				}
			);

		});
	};
}


function EmailAss_Controller($scope, $http, $mdSidenav, $mdDialog, $filter) {
	'use strict';
	$scope.template = {};
	$scope.template_loader = true;


	$http.get(BASE_URL + 'emails/get_email_template_ass/' + TEMPLATEID).then(function (Template) {
		$scope.template = Template.data;
		$scope.template_loader = false;
		tinyMCE.activeEditor.setContent($scope.template.message, { format: 'raw' });
	});

	$http.get(BASE_URL + 'emails/template_fields/' + TEMPLATEID).then(function (Data) {
		$scope.template_fields = Data.data;
	});


	$scope.saving = false;
	$scope.UpdateTemplate = function () {
		$scope.saving = true;

		var data = tinyMCE.activeEditor.getContent({ format: 'raw' });
		var dataObj = $.param({
			message: data.replace(/&nbsp;/g, ' ').replace(/;/g, '').replace(/&nbsp/g, ' '),
		});

		$http.post(BASE_URL + 'emails/update_template_ass', dataObj, config).then(
			function (response) {
				if (response.data.success == true) {
					showToast(NTFTITLE, response.data.message, 'success');
				} else {
					showToast(NTFTITLE, response.data.message, 'warning');
				}
				$scope.saving = false;
			}, function () {
				$scope.saving = false;
			}
		);
	};
}


CiuisCRM.controller('Emails_Controller', Emails_Controller);
CiuisCRM.controller('Email_Controller', Email_Controller);
CiuisCRM.controller('EmailAss_Controller', EmailAss_Controller);