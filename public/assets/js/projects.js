function Projects_Controller($scope, $http, $mdSidenav, $mdDialog, $filter, $q) {

	"use strict";



	$http.get(BASE_URL + 'api/custom_fields_by_type/' + 'project').then(function (custom_fields) {

		$scope.all_custom_fields = custom_fields.data;

		$scope.custom_fields = $filter('filter')($scope.all_custom_fields, {

			active: 'true',

		});

	});



	$scope.get_project_stat = function () {

		$http.get(BASE_URL + 'projects/projects_stats/').then(function (Stats) {

			$scope.stats = Stats.data;

		});

	};



	$scope.get_customers();

	$scope.get_project_stat();



	$scope.Create = buildToggler('Create');

	$scope.toggleFilter = buildToggler('ContentFilter');



	$scope.projectLoader = true;

	function buildToggler(navID) {

		return function () {

			$mdSidenav(navID).toggle();

		};

	}

	$scope.close = function () {

		$mdSidenav('Create').close();

		$mdDialog.hide();

	};



	$scope.updateColumns = function (column, value) {

		var dataObj = $.param({

			column: column,

			value: +value,

		});

		var posturl = BASE_URL + 'api/update_columns/projects';

		$http.post(posturl, dataObj, config)

			.then(

				function (response) {

				}, function (error) { }

			);

	};



	$scope.showGrid = true;

	$http.get(BASE_URL + 'api/table_columns/' + 'projects').then(function (Data) {

		$scope.table_columns = Data.data;

		if ($scope.table_columns.list_view == true) {

			$scope.showList = true;

			$scope.showGrid = false;

		} else {

			$scope.showList = false;

			$scope.showGrid = true;

		}

	});



	$scope.projects_list = {

		order: '',

		limit: 10,

		page: 1

	};

	var deferred = $q.defer();





	$http.get(BASE_URL + 'projects/get_projects').then(function (Projects) {

		$scope.projects = Projects.data;

		$scope.projectLoader = false;



		deferred.resolve();

		$scope.limitOptions = [5, 10, 15, 20];

		if ($scope.projects.length > 20) {

			$scope.limitOptions = [5, 10, 15, 20, $scope.projects.length];

		}



		$scope.filter = {};

		$scope.getOptionsFor = function (propName) {

			return ($scope.projects || []).map(function (item) {

				return item[propName];

			}).filter(function (item, idx, arr) {

				return arr.indexOf(item) === idx;

			}).sort();

		};

		$scope.FilteredData = function (item) {

			var matchesAND = true;

			for (var prop in $scope.filter) {

				if (noSubFilter($scope.filter[prop])) {

					continue;

				}

				if (!$scope.filter[prop][item[prop]]) {

					matchesAND = false;

					break;

				}

			}

			return matchesAND;

		};



		function noSubFilter(subFilterObj) {

			for (var key in subFilterObj) {

				if (subFilterObj[key]) {

					return false;

				}

			}

			return true;

		}

		$scope.updateDropdown = function (_prop) {

			var _opt = this.filter_select,

				_optList = this.getOptionsFor(_prop),

				len = _optList.length;

			console.log(_optList);



			if (_opt == 'all') {

				for (var j = 0; j < len; j++) {

					$scope.filter[_prop][_optList[j]] = true;

				}

			} else {

				for (var j = 0; j < len; j++) {

					console.log($scope.filter[_prop], $scope.filter[_prop][_optList[j]])

					$scope.filter[_prop][_optList[j]] = false;

				}

				$scope.filter[_prop][_opt] = true;

			}

		};



		$scope.saving = false;

		$scope.CreateNew = function () {

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

				if ($scope.project.template != true) {

					$scope.project.template = false;

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

			if (!$scope.project) {

				var dataObj = $.param({

					name: '',

					customer: '',

					value: '',

					description: '',

					start: moment('').format("YYYY-MM-DD"),

					deadline: moment('').format("YYYY-MM-DD"),

					custom_fields: '',

					template: '',

				});

			} else {

				if ($scope.project.start) {

					$scope.project.start = moment($scope.project.start).format("YYYY-MM-DD")

				}

				if ($scope.project.deadline) {

					$scope.project.deadline = moment($scope.project.deadline).format("YYYY-MM-DD")

				}

				var dataObj = $.param({

					name: $scope.project.name,

					//customer: $scope.project.customer,

				//	value: $scope.project.value,

				//	tax: $scope.project.tax,

					description: $scope.project.description,

					start: $scope.project.start,

					deadline: $scope.project.deadline,

					custom_fields: $scope.tempArr,

					template: $scope.project.template

				});

			}



			var config = {

				headers: {

					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

				}

			};

			var posturl = BASE_URL + 'projects/create';

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						if (response.data.success == true) {

							globals.mdToast('success', response.data.message);

							$scope.project.name = '';

							//$scope.project.customer = '';

							//$scope.project.value = '';

						//	$scope.project.tax = '';

							$scope.project.description = '';

							$scope.project.start = '';

							$scope.project.deadline = '';

							$mdSidenav('Create').close();

							$http.get(BASE_URL + 'projects/get_projects').then(function (Projects) {

								$scope.projects = Projects.data;

							});

							$scope.get_project_stat();

						} else {

							globals.mdToast('error', response.data.message);

						}

						$scope.saving = false;

					},

					function (response) {

						console.log(response);

						showToast(NTFTITLE, 'Error occured!', ' danger');

						$scope.saving = false;

						$http.get(BASE_URL + 'projects/get_projects').then(function (Projects) {

							$scope.projects = Projects.data;

						});

					}

				);

		};



		$scope.markasComplete = function (id) {

			var confirm = $mdDialog.confirm()

				.title(lang.attention)

				.textContent(lang.project_complete_note)

				.ariaLabel('Convert')

				.targetEvent(id)

				.ok(lang.doIt)

				.cancel(lang.cancel);

			$mdDialog.show(confirm).then(function () {

				var dataObj = $.param({

					status_id: 5,

					project_id: id

				});

				var config = {

					headers: {

						'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

					}

				};

				$http.post(BASE_URL + 'projects/markas_complete', dataObj, config)

					.then(

						function (response) {

							if (response.data.success == true) {

								showToast(NTFTITLE, response.data.message, ' success');

							} else {

								showToast(NTFTITLE, response.data.message, ' danger');

							}

							$http.get(BASE_URL + 'projects/get_projects').then(function (Projects) {

								$scope.projects = Projects.data;

							});

							$scope.get_project_stat();

						},

						function (response) {

							console.log(response);

						}

					);



			}, function () {

			});

		};



		$scope.pinnedprojects = Projects.data;



		$scope.CheckPinned = function (index) {

			var project = $scope.projects[index];

			var dataObj = $.param({

				project: project.id

			});

			var config = {

				headers: {

					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

				}

			};

			$http.post(BASE_URL + 'projects/checkpinned', dataObj, config)

				.then(

					function (response) {

						if (response.data.success == true) {

							globals.mdToast('success', response.data.message);

							$http.get(BASE_URL + 'projects/get_projects').then(function (Projects) {

								$scope.pinnedprojects = Projects.data;

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



		$scope.UnPinned = function (id) {

			var dataObj = $.param({

				pinnedproject: id

			});

			var config = {

				headers: {

					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

				}

			};

			var posturl = BASE_URL + 'projects/unpinned';

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						if (response.data.success == true) {

							globals.mdToast('success', response.data.message);

							$http.get(BASE_URL + 'projects/get_projects').then(function (Projects) {

								$scope.pinnedprojects = Projects.data;

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



		var projectId;

		$scope.copyProjectDialog = function (id) {

			projectId = id

			$mdDialog.show({

				templateUrl: 'copyProjectDialog.html',

				scope: $scope,

				preserveScope: true,

				targetEvent: ''

			});

		};



		$scope.copyProjectConfirm = function () {

			if (!$scope.copy) {

				var dataObj = $.param({

					services: false,

					expenses: false,

					milestones: false,

					tasks: false,

					peoples: false,

					files: false,

					notes: false,

					customer_id: '',

					startdate: '',

					enddate: ''

				});

			} else {

				if (!$scope.copy.service) {

					$scope.copy.service = false;

				}

				if (!$scope.copy.expenses) {

					$scope.copy.expenses = false;

				}

				if (!$scope.copy.milestones) {

					$scope.copy.milestones = false;

				}

				if (!$scope.copy.tasks) {

					$scope.copy.tasks = false;

				}

				if (!$scope.copy.peoples) {

					$scope.copy.peoples = false;

				}

				if (!$scope.copy.files) {

					$scope.copy.files = false;

				}

				if (!$scope.copy.notes) {

					$scope.copy.notes = false;

				}

				var dataObj = $.param({

					services: $scope.copy.service,

					expenses: $scope.copy.expenses,

					milestones: $scope.copy.milestones,

					tasks: $scope.copy.tasks,

					peoples: $scope.copy.peoples,

					files: $scope.copy.files,

					notes: $scope.copy.notes,

					customer_id: $scope.copy.customer,

					startdate: moment($scope.copy.start).format("YYYY-MM-DD"),

					enddate: moment($scope.copy.end).format("YYYY-MM-DD"),

				});

			}



			var config = {

				headers: {

					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

				}

			};

			if (!projectId) {

				$.gritter.add({

					title: '<b>' + NTFTITLE + '</b>',

					text: $scope.lang.errormessage,

					class_name: 'color danger'

				});

			} else {

				$mdDialog.show({

					templateUrl: 'processing.html',

					scope: $scope,

					preserveScope: true,

					targetEvent: ''

				});

				var posturl = BASE_URL + 'projects/copyProject/' + projectId;

				$http.post(posturl, dataObj, config)

					.then(

						function (response) {

							if (response.data.success == true) {

								$mdDialog.hide();

								$.gritter.add({

									title: '<b>' + NTFTITLE + '</b>',

									text: response.data.message,

									class_name: 'color success'

								});

								$http.get(BASE_URL + 'projects/get_projects').then(function (Projects) {

									$scope.projects = Projects.data;

								});

								$scope.get_project_stat();

							} else {

								$mdDialog.hide();

								$.gritter.add({

									title: '<b>' + NTFTITLE + '</b>',

									text: $scope.lang.errormessage,

									class_name: 'color danger'

								});

							}

							console.log(response);

						}, function () {



						}

					);

			}

		};



		$scope.itemsPerPage = 6;

		$scope.currentPage = 0;

		$scope.range = function () {

			var rangeSize = 6;

			var ps = [];

			var start;



			start = $scope.currentPage;

			//  console.log($scope.pageCount(),$scope.currentPage)

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

			return Math.ceil($scope.projects.length / $scope.itemsPerPage) - 1;

		};

	});

}



function Project_Controller($scope, $http, $mdSidenav, $q, $timeout, $mdDialog, $filter, $sce, fileUpload) {

	"use strict";



	$scope.NewMilestone = buildToggler('NewMilestone');

	$scope.NewTask = buildToggler('NewTask');

	$scope.NewExpense = buildToggler('NewExpense');

	$scope.NewService = buildToggler('NewService');

	$scope.Update = buildToggler('Update');

	$scope.NewTicket = buildToggler('NewTicket');



	function buildToggler(navID) {

		return function () {

			$mdSidenav(navID).toggle();

		};

	}



	$scope.get_customers();

	$scope.get_staff();

	globals.get_departments();



	$scope.close = function () {

		$mdSidenav('NewMilestone').close();

		$mdSidenav('NewTask').close();

		$mdSidenav('NewExpense').close();

		$mdSidenav('NewService').close();

		$mdSidenav('Update').close();

		$mdSidenav('UpdateService').close();

		$mdSidenav('NewTicket').close();

		$mdDialog.hide();

		$scope.invoiceButton = false;

	};



	$scope.UploadFile = function (ev) {

		$mdDialog.show({

			templateUrl: 'addfile-template.html',

			scope: $scope,

			preserveScope: true,

			targetEvent: ev

		});

	};



	$scope.uploading = false;

	$scope.uploadProjectFile = function () {

		$scope.uploading = true;

		var file = $scope.project_file;

		var uploadUrl = BASE_URL + 'projects/add_file/' + PROJECTID;

		fileUpload.uploadFileToUrl(file, uploadUrl, function (response) {

			if (response.success == true) {

				globals.mdToast('success', response.message);

			} else {

				globals.mdToast('error', response.message);

			}

			$scope.projectFiles = true;

			$http.get(BASE_URL + 'projects/projectfiles/' + PROJECTID).then(function (Files) {

				$scope.files = Files.data;

				$scope.projectFiles = false;

			});

			$scope.uploading = false;

			$mdDialog.hide();

		});

	};



	$scope.NewProposal = function (ev) {

		customer_proposals();

		$mdDialog.show({

			templateUrl: 'new_proposal.html',

			scope: $scope,

			preserveScope: true,

			targetEvent: ev

		});

	};



	$scope.projectReport = function () {

		$mdDialog.show({

			templateUrl: 'projectReport.html',

			scope: $scope,

			preserveScope: true,

			targetEvent: ''

		});

	};



	$scope.generating = false;

	$scope.generated = false;

	$scope.generated_url = '#';

	$scope.report = {};

	$scope.report.color = 'blue';

	$scope.generatePDFReport = function () {

		$scope.generating = true;

		var dataObj = $.param({

			customer: $scope.report.customer ? $scope.report.customer : false,

			services: $scope.report.services ? $scope.report.customer : false,

			expenses: $scope.report.expenses ? $scope.report.customer : false,

			proposals: $scope.report.proposals ? $scope.report.customer : false,

			tickets: $scope.report.tickets ? $scope.report.customer : false,

			notes: $scope.report.notes ? $scope.report.customer : false,

			files: $scope.report.files ? $scope.report.customer : false,

			peoples: $scope.report.peoples ? $scope.report.customer : false,

			milestones: $scope.report.milestones ? $scope.report.customer : false,

			tasks: $scope.report.tasks ? $scope.report.customer : false,

			time_logs: $scope.report.time_logs ? $scope.report.time_logs : false,

			summary: $scope.report.summary ? $scope.report.summary : false,

			color: $scope.report.color

		});

		var config = {

			headers: {

				'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

			}

		};

		var posturl = BASE_URL + 'projects/create_pdf/' + PROJECTID;

		$http.post(posturl, dataObj, config)

			.then(

				function (response) {

					$scope.generating = false;

					if (response.data.success == true) {

						$scope.generated = true;

						$scope.generated_url = BASE_URL + 'uploads/files/projects/' + PROJECTID + '/' + response.data.file_name;

						window.open(BASE_URL + 'uploads/files/projects/' + PROJECTID + '/' + response.data.file_name, '_blank');

					} else {

						globals.mdToast('error', response.data.message);

					}

				},

				function (response) {

					$scope.generating = false;

					showToast(NTFTITLE, 'Error', ' danger');

				}

			);

	}



	$scope.AddTask = function () {

		if ($scope.isPublic === true) {

			$scope.isPublicValue = 1;

		} else {

			$scope.isPublicValue = 0;

		}

		if ($scope.isBillable === true) {

			$scope.isBillableValue = 1;

		} else {

			$scope.isBillableValue = 0;

		}

		if ($scope.isVisible === true) {

			$scope.isVisibleValue = 1;

		} else {

			$scope.isVisibleValue = 0;

		}



		if (!$scope.newtask) {

			var dataObj = $.param({

				name: '',

				hourlyrate: '',

				assigned: '',

				priority: '',

				milestone: '',

				public: '',

				billable: '',

				visible: '',

				startdate: '',

				duedate: '',

				description: '',

			});

		} else {

			var dataObj = $.param({

				name: $scope.newtask.name,

				hourlyrate: $scope.newtask.hourlyrate,

				assigned: $scope.newtask.assigned,

				priority: $scope.newtask.priority,

				milestone: $scope.newtask.milestone,

				public: $scope.isPublicValue,

				billable: $scope.isBillableValue,

				visible: $scope.isVisibleValue,

				startdate: moment($scope.newtask.startdate).format("YYYY-MM-DD"),

				duedate: moment($scope.newtask.duedate).format("YYYY-MM-DD"),

				description: $scope.newtask.description,

			});

		}

		var config = {

			headers: {

				'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

			}

		};

		var posturl = BASE_URL + 'projects/addtask/' + PROJECTID;

		$http.post(posturl, dataObj, config)

			.then(

				function (response) {

					if (response.data.success == true) {

						$mdSidenav('NewTask').close();

						globals.mdToast('success', response.data.message);

						$http.get(BASE_URL + 'projects/get_project/' + PROJECTID).then(function (Project) {

							$scope.project = Project.data;

						});

						$scope.newtask.name = '';

						$scope.newtask.description = '';

					} else {

						globals.mdToast('error', response.data.message);

					}

				},

				function (response) {

					console.log(response);

				}

			);

	};



	$http.get(BASE_URL + 'api/custom_fields_data_by_type/' + 'project/' + PROJECTID).then(function (custom_fields) {

		$scope.custom_fields = custom_fields.data;

	});



	$scope.projectLoader = true;

	$http.get(BASE_URL + 'projects/get_project/' + PROJECTID).then(function (Project) {

		$scope.project = Project.data;

		//var cust = $scope.project.customer;

		//var searchCustomer = $scope.project.customer?cust.split(' ')[0]:'';

		//	$scope.search_customers(searchCustomer);

		$scope.projectLoader = false;



		if ($scope.project.pdf_report == '1') {

			$scope.generated = true;

			$scope.generated_url = BASE_URL + 'uploads/files/projects/' + PROJECTID + '/' + $scope.project.file_name;

		}



		$scope.AddProjectMember = function () {

			var dataObj = $.param({

				project: PROJECTID,

				staff: $scope.insertedStaff

			});

			console.log(dataObj, "hi")

			var config = {

				headers: {

					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

				}

			};

			var posturl = BASE_URL + 'projects/addmember';

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						if (response.data.success == true) {

							globals.mdToast('success', response.data.message);

							$mdDialog.hide();

							$scope.project.members.push({

								'id': response.data.member.staffavatar,

								'staffname': response.data.member.staffname,

								'staffavatar': response.data.member.staffavatar,

								'email': response.data.member.email,

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



		$scope.adding = false;

		$scope.AddExpense = function () {

			$scope.adding = true;

			if (!$scope.newexpense) {

				var dataObj = $.param({

					title: '',

					amount: '',

					date: '',

					category: '',

					account: '',

					description: '',

					customer: '',

				});

			} else {

				var dataObj = $.param({

					name: $scope.project.name,

					title: $scope.newexpense.title,

					amount: $scope.newexpense.amount,

					date: moment($scope.newexpense.date).format("YYYY-MM-DD"),

					category: $scope.newexpense.category,

					account: $scope.newexpense.account,

					description: $scope.newexpense.description,

					//	customer: $scope.project.customer_id,

				});

			}



			var config = {

				headers: {

					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

				}

			};



			var posturl = BASE_URL + 'projects/addexpense/' + PROJECTID;

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						$scope.adding = false;

						if (response.data.success == true) {

							globals.mdToast('success', response.data.message);

							$mdSidenav('NewExpense').close();

							$http.get(BASE_URL + 'api/expenses_by_relation/project/' + PROJECTID).then(function (Expenses) {

								$scope.expenses = Expenses.data;

								$scope.TotalExpenses = function () {

									return $scope.expenses.reduce(function (total, expense) {

										return total + (expense.amount * 1 || 0);

									}, 0);

								};

								$scope.billedexpenses = $filter('filter')($scope.expenses, {

									billstatus_code: "true"

								});

								$scope.BilledExpensesTotal = function () {

									return $scope.billedexpenses.reduce(function (total, expense) {

										return total + (expense.amount * 1 || 0);

									}, 0);

								};

								$scope.unbilledexpenses = $filter('filter')($scope.expenses, {

									billstatus_code: "false"

								});

								$scope.UnBilledExpensesTotal = function () {

									return $scope.unbilledexpenses.reduce(function (total, expense) {

										return total + (expense.amount * 1 || 0);

									}, 0);

								};

							});

						} else {

							globals.mdToast('error', response.data.message);

						}

					},

					function (response) {

						$scope.adding = false;

						console.log(response);

					}

				);

		};



		$scope.createTicket = function () {

			if (!$scope.ticket) {

				var dataObj = $.param({

					subject: '',

					customer: '',

					contact: '',

					department: '',

					priority: '',

					message: '',

				});

			} else {

				var dataObj = $.param({

					subject: $scope.ticket.subject,

					customer: $scope.ticket.customer.customer_id,

					contact: $scope.ticket.contact,

					department: $scope.ticket.department,

					priority: $scope.ticket.priority,

					message: $scope.ticket.message,

				});

			}



			var config = {

				headers: {

					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

				}

			};

			var posturl = BASE_URL + 'projects/createticket/' + PROJECTID;

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						if (response.data.success == true) {

							globals.mdToast('success', response.data.message);

							$mdSidenav('NewTicket').close();

							$http.get(BASE_URL + 'projects/tickets/' + PROJECTID).then(function (Tickets) {

								$scope.tickets = Tickets.data;

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



		$http.get(BASE_URL + 'projects/tickets/' + PROJECTID).then(function (Tickets) {

			$scope.tickets = Tickets.data;



			$scope.viewTicket = function (index) {

				$scope.ticket = $scope.tickets[index];

				$mdDialog.show({

					templateUrl: 'ticketDialog.html',

					scope: $scope,

					preserveScope: true,

					targetEvent: $scope.ticket.id

				});

			}

		});



		$scope.TicketMarkAs = function (id, name, TICKETID) {

			var dataObj = $.param({

				status_id: id,

				ticket_id: TICKETID,

			});

			var config = {

				headers: {

					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

				}

			};

			var posturl = BASE_URL + 'projects/ticket_markas';

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						if (response.data.success == true) {

							$.gritter.add({

								title: '<b>' + NTFTITLE + '</b>',

								text: '<b>' + langs.ticket + langs.marked_as + ' ' + name + '</b>',

								class_name: 'color success'

							});

							$http.get(BASE_URL + 'projects/tickets/' + PROJECTID).then(function (Tickets) {

								$scope.tickets = Tickets.data;

								$scope.ticketsData = Tickets.data;

								for (var i = 0; i < $scope.ticketsData.length; i++) {

									if ($scope.ticketsData[i].id == TICKETID) {

										$scope.ticket = $scope.ticketsData[i];

										break;

									}

								}

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



		$scope.DeleteTicket = function (TICKETID) {

			$mdDialog.hide();

			var confirm = $mdDialog.confirm()

				.title(langs.attention)

				.textContent(langs.ticketattentiondetail)

				.ariaLabel(langs.delete + ' ' + langs.ticket)

				.targetEvent(TICKETID)

				.ok(langs.doIt)

				.cancel(langs.cancel);



			$mdDialog.show(confirm).then(function () {

				var config = {

					headers: {

						'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

					}

				};

				$http.post(BASE_URL + 'projects/remove_ticket/' + TICKETID, config)

					.then(

						function (response) {

							if (response.data.success == true) {

								$mdDialog.hide();

								globals.mdToast('success', response.data.message);

								$http.get(BASE_URL + 'projects/tickets/' + PROJECTID).then(function (Tickets) {

									$scope.tickets = Tickets.data;

								});

							} else {

								globals.mdToast('error', response.data.message);

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



		var SERVICEID;

		$http.get(BASE_URL + 'projects/get_project_services/' + PROJECTID).then(function (Services) {

			$scope.projectservices = Services.data;

			$scope.DeleteService = function (index) {

				$scope.servicesData = $scope.projectservices[index];

				var id = $scope.servicesData['serviceid'];

				console.log(id, $scope.servicesData)

				var confirm = $mdDialog.confirm()

					.title(langs.attention)

					.textContent(langs.delete_service_message)

					.ariaLabel(langs.delete + langs.service)

					.targetEvent(PROJECTID)

					.ok(langs.doIt)

					.cancel(langs.cancel);



				$mdDialog.show(confirm).then(function () {

					var config = {

						headers: {

							'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

						}

					};

					$http.post(BASE_URL + 'projects/removeService/' + id, config)

						.then(

							function (response) {

								if (response.data.success == true) {

									$http.get(BASE_URL + 'projects/get_project_services/' + PROJECTID).then(function (Services) {

										$scope.projectservices = Services.data;

									});

									globals.mdToast('success', response.data.message);

								} else {

									globals.mdToast('error', response.data.message);

								}

							},

							function (response) {

								console.log(response);

							}

						);



				}, function () {

					//

				});

			}



			$scope.UpdateService = function (index) {

				SERVICEID = $scope.projectservices[index].serviceid;

				$scope.updateservice = $scope.projectservices[index];

				$scope.updateservice.category = $scope.updateservice.categoryid;

				$scope.getProducts($scope.updateservice.category);

				$scope.updateservice.product = $scope.updateservice.productid;

				$scope.updateservice.productname = $scope.updateservice.servicename;

				$scope.updateservice.price = $scope.updateservice.serviceprice;

				$scope.updateservice.tax = $scope.updateservice.servicetax;

				$scope.updateservice.unit = $scope.updateservice.unit;

				$scope.updateservice.description = $scope.updateservice.servicedescription;

				$scope.updateservice.quantity = $scope.updateservice.quantity;

				$mdSidenav('UpdateService').toggle();

			}

		});



		$http.get(BASE_URL + 'api/get_product_categories').then(function (Categories) {

			$scope.productcategories = Categories.data;

			$scope.productFound = false;

		});



		var products;

		$scope.getProducts = function (id) {

			$http.get(BASE_URL + 'projects/get_products_by_category/' + id).then(function (Products) {

				$scope.categoriesproduct = Products.data;

				products = Products.data;

				if (Products.data.length > 0) {

					$scope.productFound = false;

				} else {

					$scope.productFound = true;

				}

			});

		}



		$scope.getProductData = function (index) {

			if (products && index) {

				for (var i = 0; i < products.length; i++) {

					if (products[i].id == index) {

						$scope.newservice.productname = products[i].productname;

						$scope.newservice.tax = products[i].vat;

						$scope.newservice.price = products[i].purchase_price;

						$scope.newservice.description = products[i].description;

						$scope.newservice.quantity = products[i].quantity;



						$scope.updateservice.productname = products[i].productname;

						$scope.updateservice.price = products[i].purchase_price;

						$scope.updateservice.tax = products[i].vat;

						$scope.updateservice.unit = products[i].unit;

						$scope.updateservice.description = products[i].description;

						$scope.updateservice.quantity = products[i].quantity;

						if (parseInt(products[i].purchase_price) == 0) {

							$scope.newservice.price = products[i].sale_price;

							$scope.updateservice.price = products[i].sale_price;

						}

						break;

					}

				}

			}

		}



		$scope.AddService = function () {

			console.log($scope.newservice);

			if ($scope.newservice == undefined || !$scope.newservice) {

				var dataObj = $.param({

					categoryid: '',

					productid: '',

					servicename: '',

					serviceprice: '',

					servicetax: '',

					quantity: '',

					unit: '',

					servicedescription: '',

				});

			} else {

				var dataObj = $.param({

					categoryid: $scope.newservice.category,

					productid: $scope.newservice.product,

					servicename: $scope.newservice.productname,

					serviceprice: $scope.newservice.price,

					servicetax: $scope.newservice.tax,

					quantity: $scope.newservice.quantity,

					unit: $scope.newservice.unit,

					servicedescription: $scope.newservice.description,

					projectid: PROJECTID,

				});

			}



			var config = {

				headers: {

					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

				}

			};

			var posturl = BASE_URL + 'projects/addservice';

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						if (response.data.success == true) {

							$.gritter.add({

								title: '<b>' + NTFTITLE + '</b>',

								text: response.data.message,

								class_name: 'color success'

							});

							$mdSidenav('NewService').close();

							$http.get(BASE_URL + 'projects/get_project_services/' + PROJECTID).then(function (Services) {

								$scope.projectservices = Services.data;

							});

						} else {

							globals.mdToast('error', response.data.message);

						}

					},

					function (response) {

						console.log(response);

					}

				);

		}



		$scope.SaveService = function () {

			if ($scope.updateservice == undefined || !$scope.updateservice) {

				var dataObj = $.param({

					categoryid: '',

					productid: '',

					servicename: '',

					serviceprice: '',

					servicetax: '',

					unit: '',

					servicedescription: '',

				});

			} else {

				var dataObj = $.param({

					categoryid: $scope.updateservice.category,

					productid: $scope.updateservice.product,

					servicename: $scope.updateservice.productname,

					serviceprice: $scope.updateservice.price,

					servicetax: $scope.updateservice.tax,

					quantity: $scope.updateservice.quantity,

					unit: $scope.updateservice.unit,

					servicedescription: $scope.updateservice.description,

					projectid: PROJECTID,

				});

			}



			var config = {

				headers: {

					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

				}

			};

			var posturl = BASE_URL + 'projects/updateservice/' + SERVICEID;

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						if (response.data.success == true) {

							$.gritter.add({

								title: '<b>' + NTFTITLE + '</b>',

								text: response.data.message,

								class_name: 'color success'

							});

							$mdSidenav('UpdateService').close();

							$http.get(BASE_URL + 'projects/get_project_services/' + PROJECTID).then(function (Services) {

								$scope.projectservices = Services.data;

							});

						} else {

							globals.mdToast('error', response.data.message);

						}

					},

					function (response) {

						console.log(response);

					}

				);

		}



		$scope.saving = false;

		$scope.UpdateProject = function () {

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



			if (!$scope.project) {

				var dataObj = $.param({

					name: '',

					customer: '',

					value: '',

					description: '',

					start: moment('').format("YYYY-MM-DD"),

					deadline: moment('').format("YYYY-MM-DD"),

					custom_fields: '',

				});

			} else {

				if ($scope.project.template != true || !$scope.project.template) {

					$scope.project.template = false;

				} else {

					$scope.project.template = true;

				}

				if ($scope.project.start) {

					$scope.project.start = moment($scope.project.start).format("YYYY-MM-DD")

				}

				if ($scope.project.deadline_edit) {

					$scope.project.deadline_edit = moment($scope.project.deadline_edit).format("YYYY-MM-DD")

				}

				var dataObj = $.param({

					name: $scope.project.name,

					//	customer: $scope.project.customer_id,

					//value: $scope.project.value,

					//tax: $scope.project.tax,

					description: $scope.project.description,

					start: $scope.project.start_edit,

					deadline: $scope.project.deadline_edit,

					custom_fields: $scope.tempArr,

					template: $scope.project.template

				});

			}



			var config = {

				headers: {

					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

				}

			};

			var posturl = BASE_URL + 'projects/update/' + PROJECTID;

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						$scope.saving = false;

						if (response.data.success == true) {

							$.gritter.add({

								title: '<b>' + NTFTITLE + '</b>',

								text: response.data.message,

								class_name: 'color success'

							});

							$mdSidenav('Update').close();

							$http.get(BASE_URL + 'projects/get_project/' + PROJECTID).then(function (Projects) {

								$scope.project = Projects.data;

							});

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



		$scope.projectmembers = $scope.project.members;

		$scope.UnlinkMember = function (index) {

			var link = $scope.projectmembers[index];

			var confirm = $mdDialog.confirm()

				.title(langs.attention)

				.textContent(langs.remove_staff)

				.targetEvent(PROJECTID)

				.ok(langs.doIt)

				.cancel(langs.cancel);



			$mdDialog.show(confirm).then(function () {

				var linkid = link.id;

				var dataObj = $.param({

					linkid: linkid

				});

				var config = {

					headers: {

						'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

					}

				};

				$http.post(BASE_URL + 'projects/unlinkmember/' + linkid, dataObj, config)

					.then(

						function (response) {

							if (response.data.success == true) {

								showToast(NTFTITLE, response.data.message, ' success');

								$http.get(BASE_URL + 'projects/get_project/' + PROJECTID).then(function (Project) {

									$scope.project = Project.data;

								});

							} else {

								globals.mdToast('error', response.data.message);

							}

						},

						function (response) {

							console.log(response);

						}

					);

			});

		};



		$http.get(BASE_URL + 'projects/get_projecttimelogs/' + PROJECTID).then(function (TimeLogs) {

			$scope.timelogs = TimeLogs.data;

			$scope.getTotal = function () {

				var TotalTime = 0;

				for (var i = 0; i < $scope.timelogs.length; i++) {

					var timelog = $scope.timelogs[i];

					TotalTime += (timelog.minutes);

				}

				//console.log(TotalTime)

				//var minutes = parseInt(TotalTime % 60);

				//var hours = parseInt(Math.floor(TotalTime / 60));

				return TotalTime;

			};

			$scope.ProjectTotalAmount = function () {

				var TotalAmount = 0;

				for (var i = 0; i < $scope.timelogs.length; i++) {

					var timelog = $scope.timelogs[i];

					TotalAmount += (timelog.total_amount);

				}

				return TotalAmount;

			};

		});



		$scope.InsertMember = function (ev) {

			$mdDialog.show({

				templateUrl: 'insert-member-template.html',

				scope: $scope,

				preserveScope: true,

				targetEvent: ev

			});

		};



		$scope.ConvertDialog = function () {

			$scope.invoiceButton = false;

			$mdDialog.show({

				templateUrl: 'convertDialog.html',

				scope: $scope,

				preserveScope: true,

				targetEvent: ''

			});

		}



		$scope.Convert = function () {

			$scope.invoiceButton = true;

			var dataObj = $.param({

				total: $scope.ProjectTotalAmount,

			});

			var config = {

				headers: {

					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

				}

			};

			$http.post(BASE_URL + 'projects/convert/' + PROJECTID, dataObj, config)

				.then(

					function (response) {

						if (response.data.success == true) {

							console.log(response);

							window.location.href = BASE_URL + 'invoices/invoice/' + response.data.id;

						} else {

							globals.mdToast('error', response.data.message);

						}

					},

					function (response) {

						console.log(response);

					}

				);

		};



		$scope.ConvertWithProjectValue = function () {

			$scope.invoiceButton = true;

			var dataObj = $.param({

				total: $scope.ProjectTotalAmount,

				cost: $scope.project.value,

				name: $scope.project.name,

				description: $scope.project.description,

				//tax: $scope.project.tax,

			});

			var config = {

				headers: {

					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

				}

			};

			$http.post(BASE_URL + 'projects/convertwithcost/' + PROJECTID, dataObj, config)

				.then(

					function (response) {

						if (response.data.success == true) {

							console.log(response);

							window.location.href = BASE_URL + 'invoices/invoice/' + response.data.id;

						} else {

							globals.mdToast('error', response.data.message);

						}

					},

					function (response) {

						console.log(response);

					}

				);

		};



		$scope.Delete = function () {

			$mdDialog.show({

				templateUrl: 'delete_project.html',

				scope: $scope,

				preserveScope: true,

			});

		};



		$scope.deletingProject = false;

		$scope.DeleteProject = function () {

			$scope.deletingProject = true;

			var config = {

				headers: {

					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

				}

			};

			$http.post(BASE_URL + 'projects/remove/' + PROJECTID, config)

				.then(

					function (response) {

						if (response.data.success == true) {

							$scope.deletingProject = false;

							window.location.href = BASE_URL + 'projects';

						} else {

							globals.mdToast('error', response.data.message);

							$scope.deletingProject = false;

						}

					},

					function (response) {

						console.log(response);

						$scope.deletingProject = false;

					}

				);

		};

	});



	$scope.MarkAs = function (id, name) {

		var dataObj = $.param({

			status_id: id,

			project_id: PROJECTID,

		});

		var config = {

			headers: {

				'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

			}

		};

		var posturl = BASE_URL + 'projects/markas';

		$http.post(posturl, dataObj, config)

			.then(

				function (response) {

					if (response.data.success == true) {

						$http.get(BASE_URL + 'projects/get_project/' + PROJECTID).then(function (Project) {

							$scope.project = Project.data;

						});

						showToast(NTFTITLE, langs.marked + ' <b>' + name + '</b>', ' success');

					} else {

						globals.mdToast('error', response.data.message);

					}

				},

				function (response) {

					console.log(response);

				}

			);

	};



	$http.get(BASE_URL + 'projects/projectmilestones/' + PROJECTID).then(function (Milestones) {

		$scope.milestones = Milestones.data;



		$scope.addingMilestone = false;

		$scope.AddMilestone = function () {

			$scope.addingMilestone = true;

			if (!$scope.amilestone) {

				var dataObj = $.param({

					order: '',

					name: '',

					description: '',

					duedate: '',

				});

			} else {

				if ($scope.amilestone.duedate) {

					$scope.amilestone.duedate = moment($scope.amilestone.duedate).format("YYYY-MM-DD HH:mm:ss");

				}

				var dataObj = $.param({

					order: $scope.amilestone.order,

					name: $scope.amilestone.name,

					description: $scope.amilestone.description,

					duedate: $scope.amilestone.duedate,

				});

			}



			var config = {

				headers: {

					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

				}

			};

			var posturl = BASE_URL + 'projects/addmilestone/' + PROJECTID;

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						$scope.addingMilestone = false;

						if (response.data.success == true) {

							showToast(NTFTITLE, response.data.message, ' success');

							$mdSidenav('NewMilestone').close();

							$http.get(BASE_URL + 'projects/projectmilestones/' + PROJECTID).then(function (Milestones) {

								$scope.milestones = Milestones.data;

							});

							$scope.amilestone.order = '';

							$scope.amilestone.name = '';

							$scope.amilestone.description = '';

							$scope.amilestone.duedate = '';

						} else {

							globals.mdToast('error', response.data.message);

						}

					},

					function (response) {

						$scope.addingMilestone = false;

					}

				);

		};



		$scope.ShowMilestone = function (index) {

			console.log(index);

			var milestone = $scope.milestones[index];

			$mdDialog.show({

				contentElement: '#ShowMilestone-' + milestone.id,

				parent: angular.element(document.body),

				targetEvent: index,

				clickOutsideToClose: true

			});

		};



		$scope.savingMilestone = false;

		$scope.UpdateMilestone = function (index) {

			$scope.savingMilestone = true;

			var milestone = $scope.milestones[index];

			var milestone_id = milestone.id;

			$scope.milestone = milestone;

			if ($scope.milestone.duedate) {

				$scope.milestone.duedate = moment($scope.milestone.duedate).format("YYYY-MM-DD HH:mm:ss");

			}

			var dataObj = $.param({

				order: $scope.milestone.order,

				name: $scope.milestone.name,

				description: $scope.milestone.description,

				duedate: $scope.milestone.duedate,

			});

			var config = {

				headers: {

					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

				}

			};

			var posturl = BASE_URL + 'projects/updatemilestone/' + milestone_id;

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						$scope.savingMilestone = false;

						if (response.data.success == true) {

							showToast(NTFTITLE, response.data.message, ' success');

							$http.get(BASE_URL + 'projects/projectmilestones/' + PROJECTID).then(function (Milestones) {

								$scope.milestones = Milestones.data;

							});

							$scope.milestone.order = '';

							$scope.milestone.name = '';

							$scope.milestone.description = '';

							$scope.milestone.duedate = '';

							$mdDialog.hide();

						} else {

							globals.mdToast('error', response.data.message);

						}

					},

					function (response) {

						$scope.savingMilestone = false;

					}

				);

		};



		$scope.RemoveMilestone = function (index) {

			var confirm = $mdDialog.confirm()

				.title(langs.attention)

				.textContent(langs.delete_milestone)

				.ariaLabel('Convert')

				.targetEvent(index)

				.ok(langs.doIt)

				.cancel(langs.cancel);

			$mdDialog.show(confirm).then(function () {

				var milestone = $scope.milestones[index];

				var dataObj = $.param({

					milestone: milestone.id

				});

				var config = {

					headers: {

						'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

					}

				};

				var posturl = BASE_URL + 'projects/removemilestone';

				$http.post(posturl, dataObj, config)

					.then(

						function (response) {

							if (response.data.success == true) {

								globals.mdToast('success', response.data.message);

								$scope.milestones.splice($scope.milestones.indexOf(milestone), 1);



							} else {

								globals.mdToast('error', response.data.message);

							}

						},

						function (response) {

							console.log(response);

						}

					);

			}, function () {

			});

		};



	});



	$http.get(BASE_URL + 'api/reminders_by_type/project/' + PROJECTID).then(function (Reminders) {

		$scope.in_reminders = Reminders.data;

	});



	$scope.editNote = false;

	$scope.saveNote = false;

	$scope.addNote = false;

	$http.get(BASE_URL + 'api/notes/project/' + PROJECTID).then(function (Notes) {

		$scope.notes = Notes.data;

		$scope.AddNote = function () {

			$scope.addNote = true;

			var dataObj = $.param({

				description: $scope.note,

				relation_type: 'project',

				relation: PROJECTID,

			});

			var config = {

				headers: {

					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

				}

			};

			var posturl = BASE_URL + 'trivia/addnote';

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						$scope.addNote = false;

						if (response.data.success == true) {

							showToast(NTFTITLE, response.data.message, ' success');

							$('.note-description').val('');

							$scope.note = '';

							$http.get(BASE_URL + 'api/notes/project/' + PROJECTID).then(function (Notes) {

								$scope.notes = Notes.data;

							});

						} else {

							showToast(NTFTITLE, response.data.message, ' danger');

						}

					},

					function (response) {

						$scope.addNote = false;

					}

				);

		};



		$scope.EditNote = function (index) {

			var note = $scope.notes[index];

			$scope.editNote = true;

			$scope.edit_note = note.description;

			$scope.edit_note_id = note.id;

			$('#note_focus').focus();

			$('html, body').animate({

				scrollTop: $("#note_focus").offset().top

			}, 1000);

		}



		$scope.SaveNote = function () {

			$scope.saveNote = true;

			var id = $scope.edit_note_id;

			if (id) {

				var dataObj = $.param({

					description: $scope.edit_note,

				});

				var config = {

					headers: {

						'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

					}

				};

				var posturl = BASE_URL + 'trivia/updatenote/' + id;

				$http.post(posturl, dataObj, config)

					.then(

						function (response) {

							$scope.editNote = false;

							$scope.saveNote = false;

							$scope.edit_note = '';

							$http.get(BASE_URL + 'api/notes/project/' + PROJECTID).then(function (Notes) {

								$scope.notes = Notes.data;

							});

							showToast(NTFTITLE, response.data, ' success');

						},

						function (response) {

							$scope.editNote = false;

							$scope.saveNote = false;

						}

					);

			} else {

				$scope.editNote = false;

			}

		};



		$scope.modifyNote = false;

		$scope.DeleteNote = function (index) {

			$scope.modifyNote = true;

			var note = $scope.notes[index];

			var dataObj = $.param({

				notes: note.id

			});

			var config = {

				headers: {

					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

				}

			};

			var posturl = BASE_URL + 'trivia/removenote';

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						$scope.modifyNote = false;

						$scope.notes.splice($scope.notes.indexOf(note), 1);

					},

					function (response) {

						$scope.modifyNote = false;

					}

				);

		};

	});



	$http.get(BASE_URL + 'api/expenses_by_relation/project/' + PROJECTID).then(function (Expenses) {

		$scope.expenses = Expenses.data;

		$scope.TotalExpenses = function () {

			return $scope.expenses.reduce(function (total, expense) {

				return total + (expense.amount * 1 || 0);

			}, 0);

		};



		$scope.billedexpenses = $filter('filter')($scope.expenses, {

			billstatus_code: "true"

		});

		$scope.BilledExpensesTotal = function () {

			return $scope.billedexpenses.reduce(function (total, expense) {

				return total + (expense.amount * 1 || 0);

			}, 0);

		};



		$scope.unbilledexpenses = $filter('filter')($scope.expenses, {

			billstatus_code: "false"

		});

		$scope.UnBilledExpensesTotal = function () {

			return $scope.unbilledexpenses.reduce(function (total, expense) {

				return total + (expense.amount * 1 || 0);

			}, 0);

		};



		$scope.viewInvoice = function (index) {

			$scope.expense = $scope.expenses[index];

			$mdDialog.show({

				templateUrl: 'expenseDialog.html',

				scope: $scope,

				preserveScope: true,

				targetEvent: $scope.expense.id

			});

		};

	});



	$scope.projectFiles = true;

	$http.get(BASE_URL + 'projects/projectfiles/' + PROJECTID).then(function (Files) {

		$scope.files = Files.data;

		$scope.projectFiles = false;



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

			return Math.ceil($scope.files.length / $scope.itemsPerPage) - 1;

		};



		$scope.ViewFile = function (index, image) {

			$scope.file = $scope.files[index];

			$mdDialog.show({

				templateUrl: 'view_image.html',

				scope: $scope,

				preserveScope: true,

				targetEvent: $scope.file.id

			});

		}



		$scope.DeleteFile = function (id) {

			var confirm = $mdDialog.confirm()

				.title($scope.lang.delete_file_title)

				.textContent($scope.lang.delete_file_message)

				.ariaLabel($scope.lang.delete_file_title)

				.targetEvent(PROJECTID)

				.ok($scope.lang.delete)

				.cancel($scope.lang.cancel);



			$mdDialog.show(confirm).then(function () {

				var config = {

					headers: {

						'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

					}

				};

				$http.post(BASE_URL + 'projects/delete_file/' + id, config)

					.then(

						function (response) {

							if (response.data.success == true) {

								showToast(NTFTITLE, response.data.message, ' success');

								$http.get(BASE_URL + 'projects/projectfiles/' + PROJECTID).then(function (Files) {

									$scope.files = Files.data;

								});

							} else {

								showToast(NTFTITLE, response.data.message, ' danger');

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

	});



	$http.get(BASE_URL + 'api/accounts').then(function (Accounts) {

		$scope.accounts = Accounts.data;

	});



	$http.get(BASE_URL + 'api/expensescategories').then(function (Epxensescategories) {

		$scope.expensescategories = Epxensescategories.data;

	});



	function customer_proposals() {

		$http.get(BASE_URL + 'projects/customer_proposals/' + PROJECTID).then(function (Data) {

			$scope.proposalsList = Data.data;

		});

	}



	$scope.proposalsLoader = false;

	function get_proposals() {

		$http.get(BASE_URL + 'projects/get_proposals/' + PROJECTID).then(function (Data) {

			$scope.proposals = Data.data;

			$scope.proposalsLoader = false;



			$scope.itemsPerPage = 5;

			$scope.currentPage = 0;

			$scope.range = function () {

				var rangeSize = 5;

				var ps = [];

				var start;



				start = $scope.currentPage;

				//  console.log($scope.pageCount(),$scope.currentPage)

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

				return Math.ceil($scope.proposals.length / $scope.itemsPerPage) - 1;

			};

		});

	}



	$scope.getProposals = function () {

		$scope.proposalsLoader = true;

		get_proposals();

	}



	$http.get(BASE_URL + 'api/products').then(function (Products) {

		$scope.products = Products.data;

		$scope.proposalsLoader = false;

	});



	$scope.GetProduct = (function (search) {

		console.log(search);

		var deferred = $q.defer();

		$timeout(function () {

			deferred.resolve($scope.products);

		}, Math.random() * 500, false);

		return deferred.promise;

	});



	$scope.newproposal = {

		items: [{

			name: new_item,

			product_id: 0,

			code: '',

			description: '',

			quantity: 1,

			unit: item_unit,

			price: 0,

			tax: 0,

			discount: 0,

		}]

	};



	$scope.add = function () {

		$scope.newproposal.items.push({

			name: new_item,

			product_id: 0,

			code: '',

			description: '',

			quantity: 1,

			unit: item_unit,

			price: 0,

			tax: 0,

			discount: 0,

		});

	};



	$scope.remove = function (index) {

		$scope.newproposal.items.splice(index, 1);

	};



	$scope.subtotal = function () {

		var subtotal = 0;

		angular.forEach($scope.newproposal.items, function (item) {

			subtotal += item.quantity * item.price;

		});

		return subtotal.toFixed(2);

	};



	$scope.linediscount = function () {

		var linediscount = 0;

		angular.forEach($scope.newproposal.items, function (item) {

			linediscount += ((item.discount) / 100 * item.quantity * item.price);

		});

		return linediscount.toFixed(2);

	};



	$scope.totaltax = function () {

		var totaltax = 0;

		angular.forEach($scope.newproposal.items, function (item) {

			totaltax += ((item.tax) / 100 * item.quantity * item.price);

		});

		return totaltax.toFixed(2);

	};



	$scope.grandtotal = function () {

		var grandtotal = 0;

		angular.forEach($scope.newproposal.items, function (item) {

			grandtotal += item.quantity * item.price + ((item.tax) / 100 * item.quantity * item.price) - ((item.discount) / 100 * item.quantity * item.price);

		});

		return grandtotal.toFixed(2);

	};



	$scope.linkingProposal = false;

	$scope.LinkProposal = function () {

		$scope.linkingProposal = true;

		var dataObj = $.param({

			proposal: $scope.existing_proposal_id

		});

		var config = {

			headers: {

				'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

			}

		};

		var posturl = BASE_URL + 'projects/link_proposal/' + PROJECTID;

		$http.post(posturl, dataObj, config).then(

			function (response) {

				$scope.linkingProposal = false;

				if (response.data.success == true) {

					globals.mdToast('success', response.data.message);

					$mdDialog.hide();

					get_proposals();

				} else {

					globals.mdToast('error', response.data.message);

				}

			}, function (response) {

				$scope.linkingProposal = false;

			}

		);

	}



	$scope.savingProposal = false;

	$scope.CreateProposal = function () {

		$scope.savingProposal = true;

		if (!$scope.newproposal) {

			var dataObj = $.param({

				comment: '',

				subject: '',

				content: '',

				date: '',

				opentill: '',

				status: '',

				assigned: '',

				sub_total: '',

				total_discount: '',

				total_tax: '',

				total: '',

				items: '',

				total_items: '',

			});

		} else {

			var created = '', date = '', duedate = '';

			if ($scope.newproposal.created) {

				created = moment($scope.newproposal.created).format("YYYY-MM-DD");

			}

			if ($scope.newproposal.opentill) {

				duedate = moment($scope.newproposal.opentill).format("YYYY-MM-DD");

			}

			var dataObj = $.param({

				comment: $scope.newproposal.comment,

				subject: $scope.newproposal.subject,

				content: $scope.newproposal.content,

				date: created,

				opentill: duedate,

				status: $scope.newproposal.status,

				assigned: $scope.newproposal.assigned,

				sub_total: $scope.subtotal,

				total_discount: $scope.linediscount,

				total_tax: $scope.totaltax,

				total: $scope.grandtotal,

				items: $scope.newproposal.items,

				total_items: $scope.newproposal.items.length,

			});

		}



		var config = {

			headers: {

				'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

			}

		};

		var posturl = BASE_URL + 'projects/proposal_create/' + PROJECTID;

		$http.post(posturl, dataObj, config)

			.then(

				function (response) {

					$scope.savingProposal = false;

					if (response.data.success == true) {

						globals.mdToast('success', response.data.message);

						$mdDialog.hide();

						get_proposals();

					} else {

						globals.mdToast('error', response.data.message);

					}

				},

				function (response) {

					$scope.savingProposal = false;

				}

			);

	};

}

CiuisCRM.controller('Projects_Controller', Projects_Controller);

CiuisCRM.controller('Project_Controller', Project_Controller);
