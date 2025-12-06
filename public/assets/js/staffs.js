function Staffs_Controller($scope, $http, $mdSidenav, $mdDialog, $filter) {
	"use strict";
	$http.get(BASE_URL + 'api/custom_fields_by_type/' + 'staff').then(function (custom_fields) {
		$scope.all_custom_fields = custom_fields.data;
		$scope.custom_fields = $filter('filter')($scope.all_custom_fields, {
			active: 'true',
		});
	});
	$http.get(BASE_URL + 'api/timezones').then(function (Timezones) {
		$scope.timezones = Timezones.data;
	});
	$scope.Create = buildToggler('Create');
	$scope.ViewStaff = function (staff_id) {
		window.location.href = BASE_URL + 'staff/staffmember/' + staff_id;
	};
	function buildToggler(navID) {
		return function () {
			$mdSidenav(navID).toggle();
		};
	}
	$scope.close = function () {
		$mdSidenav('Create').close();
		$mdDialog.hide();
	};
	$http.get(BASE_URL + 'api/departments').then(function (Departments) {
		$scope.departments = Departments.data;
		$scope.DeleteLeadStatus = function (index) {
			var status = $scope.leadstatuses[index];
			globals.deleteDialog($scope.lang.delete + ' ' + $scope.lang.status, $scope.lang.delete_meesage + ' ' + $scope.lang.status + '?', status.id, $scope.lang.delete, $scope.lang.cancel, 'leads/remove_status/' + status.id, function (response) {
				if (response.success == true) {
					globals.mdToast('success', response.message);
					$http.get(BASE_URL + 'api/departments').then(function (Departments) {
						$scope.departments = Departments.data;
					});
				} else {
					globals.mdToast('error', response.message);
				}
			});
		};
		$scope.NewDepartment = function (event) {
			globals.createDialog($scope.lang.new + ' ' + $scope.lang.department, $scope.lang.department_title + ' ' + $scope.lang.department + ' ' + $scope.lang.name, $scope.lang.department + ' ' + $scope.lang.name, event, $scope.lang.add, $scope.lang.cancel, 'staff/add_department', function (response) {
				if (response.success == true) {
					globals.mdToast('success', response.message);
					$http.get(BASE_URL + 'api/departments').then(function (Departments) {
						$scope.departments = Departments.data;
					});
				} else {
					globals.mdToast('error', response.message);
				}
			});
		};
		$scope.EditDepartment = function (index) {
			var department = $scope.departments[index];
			globals.editDialog($scope.lang.update + ' ' + $scope.lang.department, $scope.lang.department_title + ' ' + $scope.lang.department + ' ' + $scope.lang.name, $scope.lang.status + ' ' + $scope.lang.name, department.name, '', $scope.lang.save, $scope.lang.cancel, 'staff/update_department/' + department.id, function (response) {
				if (response.success == true) {
					globals.mdToast('success', response.message);
					$http.get(BASE_URL + 'api/departments').then(function (Departments) {
						$scope.departments = Departments.data;
					});
				} else {
					globals.mdToast('error', response.message);
				}
			});
		};
		$scope.DeleteDepartment = function (index) {
			var department = $scope.departments[index];
			globals.deleteDialog($scope.lang.delete + ' ' + $scope.lang.department, $scope.lang.delete_meesage + ' ' + $scope.lang.department + '?', department.id, $scope.lang.delete, $scope.lang.cancel, 'staff/remove_department/' + department.id, function (response) {
				if (response.success == true) {
					globals.mdToast('success', response.message);
					$http.get(BASE_URL + 'api/departments').then(function (Departments) {
						$scope.departments = Departments.data;
					});
				} else {
					globals.mdToast('error', response.message);
				}
			});
		};
	});
	$http.get(BASE_URL + 'api/languages').then(function (Languages) {
		$scope.languages = Languages.data;
	});
	$http.get(BASE_URL + 'settings/permission').then(function (Permissions) {
		$scope.permissions = Permissions.data;
	});
	$http.get(BASE_URL + 'settings/get_roles').then(function (Roles) {
		$scope.roles = Roles.data;
	});
	$scope.checkType = function (type) {
		$http.get(BASE_URL + 'settings/permission/' + type).then(function (Permissions) {
			$scope.permissions = Permissions.data;
		});
	};
	$scope.staff_list = {
		order: '',
		limit: 5,
		page: 1
	};
	$scope.showGrid = true;
	$scope.saving = false;
	$http.get(BASE_URL + 'staff/get_staff').then(function (Staff) {
		$scope.staff = Staff.data;
		$scope.limitOptions = [5, 10, 15, 20];
		if ($scope.staff.length > 20) {
			$scope.limitOptions = [5, 10, 15, 20, $scope.staff.length];
		}
		$scope.itemsPerPage = 6;
		$scope.currentPage = 0;
		$scope.range = function () {
			var rangeSize = 6;
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
			return Math.ceil($scope.staff.length / $scope.itemsPerPage) - 1;
		};
		$scope.AddStaff = function () {
			$scope.saving = true;
			$scope.tempArr = [];
			angular.forEach($scope.custom_fields, function (value) {
				if (value.type === 'input') {
					$scope.field_data = value.data;
				}
				if (value.type === 'textarea') {
					$scope.field_data = value.data;
				}
				if (value.type === 'date') {
					$scope.field_data = moment(value.data).format("YYYY-MM-DD");
				}
				if (value.type === 'select') {
					$scope.field_data = JSON.stringify(value.selected_opt);
				}
				$scope.tempArr.push({
					id: value.id,
					name: value.name,
					type: value.type,
					order: value.order,
					data: $scope.field_data,
					relation: value.relation,
					permission: value.permission,
				});
			});
			var file = document.querySelector('#fotoStaff').files[0];
			getBase64(file).then((staffavatar) => {
				var dataObj = $.param({
					name: $scope.staff.name,
					email: $scope.staff.email,
					phone: $scope.staff.phone,
					department: $scope.staff.department_id,
					language: $scope.staff.language,
					address: $scope.staff.address,
					password: $scope.passwordNew,
					timezone: $scope.staff_timezone,
					custom_fields: $scope.tempArr,
					role: $scope.staff.assigned_role,
					id_company: $scope.staff.id_company,
					staffavatar: staffavatar,
				});
				var posturl = BASE_URL + 'staff/createStaff';
				$http.post(posturl, dataObj, config).then(
					function (response) {
						$scope.saving = false;
						if (response.data.success == true) {
							globals.mdToast('success', response.data.message);
							$mdSidenav('Create').close();
							$http.get(BASE_URL + 'staff/get_staff').then(function (Staff) {
								$scope.staff = Staff.data;
								//$scope.staff.admin = true;
								$scope.tabIndex--;
							});
						} else {
							globals.mdToast('error', response.data.message, 7000);
						}
					},
					function (response) {
						$scope.saving = false;
					}
				);
			});
		};
	});
	$http.get(BASE_URL + 'companies/get_companies').then(function (Data) {
		$scope.companies = Data.data;
	});
	function getBase64(file) {
		if (file == null) {
			return new Promise(resolve => {
				resolve(null);
			})
		} else {
			const reader = new FileReader()
			return new Promise(resolve => {
				reader.onload = ev => {
					resolve(ev.target.result)
				}
				reader.readAsDataURL(file)
			})
		}
	}
}
function Staff_Controller($scope, $http, $mdSidenav, $mdDialog, $filter, fileUpload) {
	"use strict";
	$scope.Update = buildToggler('Update');
	$scope.filtros = [];
	var chartTime = [];
	var salarioEComissao = null;

	var data = new Date();
	var data2 = new Date();
	var data2 = new Date(data2.setDate(data.getDate() - 10));
	$scope.filtros.dt_ate = data;
	$scope.filtros.dt_de = data2;
	$scope.filtrosVenci = [];
	$scope.filtrosVenci.dt_ate = data;
	$scope.filtrosVenci.dt_de = data2;
	$scope.reportVenc = [];

	var MainChartOptions = {
		responsive: true,
		maintainAspectRatio: false
	};

	function buildToggler(navID) {
		return function () {
			$mdSidenav(navID).toggle();
		};
	}
	$scope.close = function () {
		$mdSidenav('Update').close();
		$mdDialog.hide();
	};
	$scope.staffLoader = false;
	$scope.ChangeAvatar = function (ev) {
		$mdDialog.show({
			templateUrl: 'change-avatar-template.html',
			scope: $scope,
			preserveScope: true,
			targetEvent: ev
		});
	};
	$scope.GoogleCalendar = function (ev) {
		$mdDialog.show({
			templateUrl: 'google-calendar-template.html',
			scope: $scope,
			preserveScope: true,
			targetEvent: ev
		});
	};
	$scope.changeStaff = function (data, type) {
		if (type == 'admin') {
			if (data) {
				$scope.staff.staffmember = false;
			} else {
				$scope.staff.staffmember = true;
			}
		}
		if (type == 'staff') {
			if (data) {
				$scope.staff.admin = false;
			} else {
				$scope.staff.admin = true;
			}
		}
	};
	$scope.ChangePasswordAdmin = function (ev) {
		$mdDialog.show({
			templateUrl: 'change-password-admin.html',
			scope: $scope,
			preserveScope: true,
			targetEvent: ev
		});
	};
	$scope.UpdatePasswordAdmin = function () {
		$scope.saving = true;
		if (!$scope.apassword) {
			var dataObj = $.param({
				new_password: '',
				c_new_password: ''
			});
		} else {
			var dataObj = $.param({
				new_password: $scope.apassword.newpassword,
				c_new_password: $scope.apassword.c_newpassword
			});
		}
		$http.post(BASE_URL + 'staff/changestaffpassword_admin/' + STAFFID, dataObj, config)
			.then(
				function (response) {
					$scope.saving = false;
					if (response.data.success == true) {
						globals.mdToast('success', response.data.message);
						$mdDialog.hide();
					} else {
						globals.mdToast('error', response.data.message);
					}
				},
				function (response) {
					$scope.saving = false;
					console.log(response);
				}
			);
	};
	$scope.ChangePassword = function (ev) {
		$mdDialog.show({
			templateUrl: 'change-password.html',
			scope: $scope,
			preserveScope: true,
			targetEvent: ev
		});
	};
	$scope.UpdatePassword = function () {
		$scope.saving = true;
		if (!$scope.password) {
			var dataObj = $.param({
				password: '',
				new_password: '',
				c_new_password: ''
			});
		} else {
			var dataObj = $.param({
				password: $scope.password.old,
				new_password: $scope.password.newpassword,
				c_new_password: $scope.password.c_newpassword
			});
		}
		$http.post(BASE_URL + 'staff/changestaffpassword', dataObj, config)
			.then(
				function (response) {
					$scope.saving = false;
					if (response.data.success == true) {
						showToast(NTFTITLE, response.data.message, ' success');
						$mdDialog.hide();
					} else {
						showToast(NTFTITLE, response.data.message, ' danger');
					}
				},
				function (response) {
					$scope.saving = false;
					console.log(response);
				}
			);
	};
	$scope.uploading = false;
	$scope.updateProfilePic = function () {
		$scope.uploading = true;
		var file = $scope.profile_photo;
		var uploadUrl = BASE_URL + 'staff/change_avatar/' + STAFFID;
		fileUpload.uploadFileToUrl(file, uploadUrl, function (response) {
			if (response.success == true) {
				$mdDialog.hide();
				showToast(NTFTITLE, response.message, ' success');
				$http.get(BASE_URL + 'staff/staff_detail/' + STAFFID).then(function (StaffDetail) {
					$scope.staff = StaffDetail.data;
				});
			} else {
				showToast(NTFTITLE, response.message, ' danger');
			}
			$scope.uploading = false;
		});
	};
	$scope.UpdateGoogleCalendar = function () {
		if ($scope.staff.google_calendar_enable === true) {
			$scope.Enable = 1;
		} else {
			$scope.Enable = 0;
		}
		var dataObj = $.param({
			google_calendar_id: $scope.staff.google_calendar_id,
			google_calendar_api_key: $scope.staff.google_calendar_api_key,
			google_calendar_enable: $scope.Enable,
		});
		$http.post(BASE_URL + 'staff/update_google_calendar/' + STAFFID, dataObj, config)
			.then(
				function (response) {
					console.log(response);
					$.gritter.add({
						title: '<b>' + NTFTITLE + '</b>',
						text: response.data.message,
						class_name: response.data.color,
					});
					$mdDialog.hide();
				},
				function (response) {
					console.log(response);
				}
			);
	};

	globals.get_departments();

	$http.get(BASE_URL + 'api/languages').then(function (Languages) {
		$scope.languages = Languages.data;
	});

	$http.get(BASE_URL + 'api/timezones').then(function (Timezones) {
		$scope.timezones = Timezones.data;
	});

	$http.get(BASE_URL + 'settings/get_roles').then(function (Roles) {
		$scope.roles = Roles.data;
	});

	$http.get(BASE_URL + 'api/invoices').then(function (Invoices) {
		$scope.all_invoices = Invoices.data;
		$scope.invoices = $filter('filter')($scope.all_invoices, {
			staff_id: STAFFID,
		});
	});

	$scope.GoInvoice = function (index) {
		var invoice = $scope.invoices[index];
		window.location.href = BASE_URL + 'invoices/invoice/' + invoice.id;
	};

	$http.get(BASE_URL + 'api/proposals').then(function (Proposals) {
		$scope.all_proposals = Proposals.data;
		$scope.proposals = $filter('filter')($scope.all_proposals, {
			assigned: STAFFID,
		});
	});

	$scope.GoProposal = function (index) {
		var proposal = $scope.proposals[index];
		window.location.href = BASE_URL + 'proposals/proposal/' + proposal.id;
	};

	$http.get(BASE_URL + 'api/tickets').then(function (Tickets) {
		$scope.all_tickets = Tickets.data;
		$scope.tickets = $filter('filter')($scope.all_tickets, {
			staff_id: STAFFID,
		});
	});

	$scope.GoTicket = function (index) {
		var ticket = $scope.tickets[index];
		window.location.href = BASE_URL + 'tickets/ticket/' + ticket.id;
	};

	$http.get(BASE_URL + 'api/custom_fields_data_by_type/staff/' + STAFFID).then(function (custom_fields) {
		$scope.custom_fields = custom_fields.data;
	});

	$http.get(BASE_URL + 'staff/staff_detail/' + STAFFID).then(function (StaffDetail) {
		$scope.staff = StaffDetail.data;
		$scope.View_Work = true;
		$scope.isActive = $scope.staff.staff_isActive;
		if ($scope.staff.timezone) {
			$scope.staff_timezone = $scope.staff.timezone;
		}
		$scope.savingWork = false;
		$scope.saving = false;
		var canvasTime = document.getElementById("staff_graph_time");

		var multiply = {
			beforeDatasetsDraw: function (chart, options, el) {
				chart.ctx.globalCompositeOperation = 'multiply';
			},
			afterDatasetsDraw: function (chart, options) {
				chart.ctx.globalCompositeOperation = 'source-over';
			},
		};
		var gradientThisWeekTime = canvasTime.getContext('2d').createLinearGradient(0, 0, 0, 150);
		gradientThisWeekTime.addColorStop(0, '#ffbc00');
		gradientThisWeekTime.addColorStop(1, '#fff');
		var gradientPrevWeekTime = canvasTime.getContext('2d').createLinearGradient(0, 0, 0, 150);
		gradientPrevWeekTime.addColorStop(0, '#616f8c');
		gradientPrevWeekTime.addColorStop(1, '#fff');
		var configs = {
			type: 'bar',
			data: $scope.staff.properties.chart_data_time,
			options: {
				elements: {
					point: {
						radius: 0,
						hitRadius: 5,
						hoverRadius: 5
					}
				},
				legend: {
					display: false,
				},
				scales: {
					xAxes: [{
						display: false,
					}],
					yAxes: [{
						display: false,
						ticks: {
							beginAtZero: true,
						},
					}]
				},
				legend: {
					display: true
				}
			},
			plugins: [multiply],
		};

		chartTime = new Chart(canvasTime, configs);



		var canvas = document.getElementById("staff_sales_chart");
		var multiply = {
			beforeDatasetsDraw: function (chart, options, el) {
				chart.ctx.globalCompositeOperation = 'multiply';
			},
			afterDatasetsDraw: function (chart, options) {
				chart.ctx.globalCompositeOperation = 'source-over';
			},
		};
		var gradientThisWeek = canvas.getContext('2d').createLinearGradient(0, 0, 0, 150);
		gradientThisWeek.addColorStop(0, '#ffbc00');
		gradientThisWeek.addColorStop(1, '#fff');
		var gradientPrevWeek = canvas.getContext('2d').createLinearGradient(0, 0, 0, 150);
		gradientPrevWeek.addColorStop(0, '#616f8c');
		gradientPrevWeek.addColorStop(1, '#fff');
		var configs = {
			type: 'bar',
			data: $scope.staff.properties.chart_data,
			options: {
				elements: {
					point: {
						radius: 0,
						hitRadius: 5,
						hoverRadius: 5
					}
				},
				legend: {
					display: false,
				},
				scales: {
					xAxes: [{
						display: false,
					}],
					yAxes: [{
						display: false,
						ticks: {
							beginAtZero: true,
						},
					}]
				},
				legend: {
					display: true
				}
			},
			plugins: [multiply],
		};
		window.chart = new Chart(canvas, configs);
	});

	$scope.filtrarChartTime = function (index) {
		var dt_ate = moment($scope.filtros.dt_ate).format("YYYY-MM-DD");
		var dt_de = moment($scope.filtros.dt_de).format("YYYY-MM-DD");

		var dataObj = $.param({
			dt_ate, dt_de, id: STAFFID
		});

		$http.post(BASE_URL + 'staff/get_chart_data_time', dataObj, globals.config).then(
			function (response) {
				console.log(response)
				if($scope.staff == null){
					$scope.staff = [];
				}
			
				$scope.staff.properties.chart_data_time = response.data;
				chartTime.data = response.data;
				chartTime.update();
			},
			function (response) {
			}
		);
	}

	$scope.UpdateWorkPlan = function () {
		$scope.savingWork = true;
		var dataObj = $.param({
			work_plan: JSON.stringify($scope.staff.work_plan)
		});
		var posturl = BASE_URL + 'staff/update_workplan/' + STAFFID;
		$http.post(posturl, dataObj, config)
			.then(
				function (response) {
					$scope.savingWork = false;
					if (response.data.success == true) {
						globals.mdToast('success', response.data.message);
					} else {
						globals.mdToast('error', response.data.message);
					}
				},
				function (response) {
					console.log(response);
				}
			);
	};

	$scope.UpdateStaff = function () {
		$scope.saving = true;
		$scope.type = {};
		if ($scope.staff.type == 'admin') {
			$scope.type.admin = true;
			$scope.type.staffmember = false;
			$scope.type.other = false;
		}
		if ($scope.staff.type == 'staffmember') {
			$scope.type.admin = false;
			$scope.type.staffmember = true;
			$scope.type.other = false;
		}
		if ($scope.staff.type == 'other') {
			$scope.type.admin = false;
			$scope.type.staffmember = false;
			$scope.type.other = true;
		}
		$scope.tempArr = [];
		angular.forEach($scope.custom_fields, function (value) {
			if (value.type === 'input') {
				$scope.field_data = value.data;
			}
			if (value.type === 'textarea') {
				$scope.field_data = value.data;
			}
			if (value.type === 'date') {
				$scope.field_data = moment(value.data).format("YYYY-MM-DD");
			}
			if (value.type === 'select') {
				$scope.field_data = JSON.stringify(value.selected_opt);
			}
			$scope.tempArr.push({
				id: value.id,
				name: value.name,
				type: value.type,
				order: value.order,
				data: $scope.field_data,
				relation: value.relation,
				permission: value.permission,
			});
		});
		var dataObj = $.param({
			name: $scope.staff.name,
			email: $scope.staff.email,
			phone: $scope.staff.phone,
			department: $scope.staff.department_id,
			language: $scope.staff.language,
			address: $scope.staff.address,
			admin: $scope.type.admin,
			staffmember: $scope.type.staffmember,
			timezone: $scope.staff_timezone,
			other: $scope.type.other,
			inactive: $scope.staff.active,
			custom_fields: $scope.tempArr,
			role: $scope.staff.assigned_role,
			staff_isActive: $scope.isActive,
		});
		var posturl = BASE_URL + 'staff/update/' + STAFFID;
		$http.post(posturl, dataObj, globals.config).then(
			function (response) {
				$scope.saving = false;
				if (response.data.success == true) {
					globals.mdToast('success', response.data.message);
					$mdSidenav('Update').close();
				} else {
					globals.mdToast('error', response.data.message);
				}
			},
			function (response) {
				$scope.saving = false;
				console.log(response);
			}
		);
	};


	$scope.Delete = function (index) {
		globals.deleteDialog(lang.attention, lang.delete_staff, STAFFID, lang.doIt, lang.cancel, 'staff/remove/' + STAFFID, function (response) {
			if (response.success == true) {
				window.location.href = BASE_URL + 'staff';
			} else {
				globals.mdToast('error', response.message);
			}
		});
	};



	$scope.filtrarVenci = function () {
		var dt_ate = moment($scope.filtrosVenci.dt_ate).format("YYYY-MM-DD");
		var dt_de = moment($scope.filtrosVenci.dt_de).format("YYYY-MM-DD");

		var dataObj = $.param({
			dt_ate,
			dt_de,
			id: STAFFID
		});

		$http.post(BASE_URL + 'staff/get_data_timeVenci', dataObj, globals.config).then(
			function (response) {
				$scope.reportVenc = response.data;

				console.log(response.data.salarioEComissao);
				if (salarioEComissao == null) {
					salarioEComissao = new Chart($('#salarioEComissao'), {
						type: 'bar',
						data: response.data.salarioEComissao,
						options: MainChartOptions
					});
				} else {
					salarioEComissao.data = response.data.salarioEComissao,
						salarioEComissao.update();
				}

			},
			function (response) {
			}
		);
	}
	$scope.filtrarVenci();

	$scope.logsList = [];
	$scope.openAtividades = function (index) {
		var dataObj = $.param({
			staff_id: STAFFID,
			date: $scope.staff.properties.chart_data_time.inline_graph[index].label
		});

		$http.post(BASE_URL + 'logs/get_logs_by_date', dataObj, config).then(
			function (response) {
				$scope.logsList = response.data;
			},
			function (response) {
				console.log(response);
			}
		);

	}

}
CiuisCRM.controller('Staffs_Controller', Staffs_Controller);
CiuisCRM.controller('Staff_Controller', Staff_Controller);