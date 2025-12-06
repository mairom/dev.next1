function Tasks_Controller($scope, $http, $mdSidenav, $filter) {

	"use strict";



	$http.get(BASE_URL + 'api/custom_fields_by_type/' + 'task').then(function (custom_fields) {

		$scope.all_custom_fields = custom_fields.data;

		$scope.custom_fields = $filter('filter')($scope.all_custom_fields, {

			active: 'true',

		});

	});



	$scope.toggleFilter = buildToggler('ContentFilter');

	$scope.Create = buildToggler('Create');



	$scope.get_staff();



	function buildToggler(navID) {

		return function () {

			$mdSidenav(navID).toggle();



		};

	}

	$scope.close = function () {

		$mdSidenav('ContentFilter').close();

		$mdSidenav('Create').close();

	};

	$scope.task_list = {

		order: '',

		limit: 10,

		page: 1

	};

	$scope.taskLoader = true;

	$http.get(BASE_URL + 'tasks/get_tasks').then(function (Tasks) {

		$scope.tasks = Tasks.data;

		$scope.limitOptions = [10, 15, 20];

		if ($scope.tasks.length > 20) {

			$scope.limitOptions = [10, 15, 20, $scope.tasks.length];

		}

		$scope.taskLoader = false;



		$scope.Relation_Type = 'project';

		$scope.saving = false;

		$scope.AddTask = function () {

			$scope.saving = true;

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

			// if (!$scope.RelatedTicket) {

			// 	$scope.related_with = '';

			// } else {

			// 	if ($scope.Relation_Type === 'ticket') {

			// 		$scope.related_with = $scope.RelatedTicket.id;

			// 	}

			// }

			if ($scope.Relation_Type === 'project') {

				if (!$scope.RelatedProject) {

					$scope.related_with = '';

				} else {

					$scope.related_with = $scope.RelatedProject.id;

				}

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

			if (!$scope.task) {

				var dataObj = $.param({

					name: '',

					hourly_rate: '',

					assigned: '',

					priority: '',

					relation_type: $scope.Relation_Type, 

					relation: $scope.related_with,

					milestone: '',

					status_id: '',

					public: '',

					billable: '',

					visible: '',

					startdate: '',

					duedate: '',

					description: '',

					custom_fields: $scope.tempArr,

				});

			} else {

				if ($scope.task.startdate) {

					$scope.task.startdate = moment($scope.task.startdate).format("YYYY-MM-DD");

				}

				if ($scope.task.duedate) {

					$scope.task.duedate = moment($scope.task.duedate).format("YYYY-MM-DD");

				}

				var dataObj = $.param({

					name: $scope.task.name,

					hourly_rate: $scope.task.hourlyrate,

					assigned: $scope.task.assigned,

					priority: $scope.task.priority_id,

					relation_type: $scope.Relation_Type, 

					relation: $scope.related_with,

					milestone: $scope.SelectedMilestone,

					status_id: $scope.task.status_id,

					public: $scope.isPublicValue,

					billable: $scope.isBillableValue,

					visible: $scope.isVisibleValue,

					startdate: $scope.task.startdate,

					duedate: $scope.task.duedate,

					description: $scope.task.description,

					custom_fields: $scope.tempArr,

				});

			}

			var posturl = BASE_URL + 'tasks/create';

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						if (response.data.success == true) {

							window.location.href = BASE_URL + 'tasks/task/' + response.data.id;

						} else {

							$scope.saving = false;

							globals.mdToast('error', response.data.message );

						}						

					},

					function (response) {

						$scope.saving = false;

					}

				);

		};



		$scope.filter = {};

		$scope.getOptionsFor = function (propName) {

			return ($scope.tasks || []).map(function (item) {

				return item[propName];

			}).filter(function (item, idx, arr) {

				return arr.indexOf(item) === idx;

			}).sort();

		};



		$scope.FilteredData = function (item) {

			// Use this snippet for matching with AND

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

		// Filtered Datas

		$scope.search = {

			name: '',

		};



	});



	$http.get(BASE_URL + 'api/projects').then(function (Projects) {

		$scope.projects = Projects.data;

	});



	$http.get(BASE_URL + 'api/milestones').then(function (Milestones) {

		$scope.milestones = Milestones.data;

	});

}



function Task_Controller($scope, $http, $mdSidenav, $mdDialog, fileUpload) {

	"use strict";



	$scope.Update = buildToggler('Update');



	function buildToggler(navID) {

		return function () {

			$mdSidenav(navID).toggle();

		};

	}



	$scope.get_staff();



	$scope.close = function () {

		$mdSidenav('Update').close();

		$mdDialog.hide();

	};



	$scope.title = 'Sub Tasks';



	$scope.UploadFile = function (ev) {

		$mdDialog.show({

			templateUrl: 'addfile-template.html',

			scope: $scope,

			preserveScope: true,

			targetEvent: ev

		});

	};



	$http.get(BASE_URL + 'api/custom_fields_data_by_type/' + 'task/' + TASKID).then(function (custom_fields) {

		$scope.custom_fields = custom_fields.data;

	});



	$http.get(BASE_URL + 'tasks/get_task/' + TASKID).then(function (Task) {

		$scope.task = Task.data;



		$http.get(BASE_URL + 'projects/get_project/' + $scope.task.relation).then(function (Project) {

			$scope.task.project_data = Project.data;

		});



		$scope.startTimerforTask = function () {

			var dataObj = $.param({

				task: TASKID,

				project: $scope.task.relation,

			});

			$http.post(BASE_URL + 'tasks/starttimer', dataObj, config)

				.then(

					function (response) {

						if(response.data.success == true) {

							$scope.task.timer = true;

							globals.mdToast('success', response.data.message);

							$('#stopTaskTimer').attr('style','display: block !important');

							$('#startTaskTimer').css('display', 'none');

							$scope.timer = {};

							$scope.timer.loading = true;

							$scope.timer.start = false;

							$scope.timer.stop = true;

							$scope.timer.found = false;

						} else {

							globals.mdToast('error', response.data.message);

						}

					},

					function (response) {

						console.log(response);

					}

				);

		};



		$scope.stopTimer = function () {

			$mdDialog.show({

		      	templateUrl: 'stopTimer.html',

		      	parent: angular.element(document.body),

		      	clickOutsideToClose: false,

		      	fullscreen: false,

		      	escapeToClose: false,

		      	controller: NewTeamDialogController,

		    });

		};



		function NewTeamDialogController($scope, $mdDialog) {  

			$scope.stopTimerforTask = function () {

				var note;

				if (!$scope.stopTimer) {

					note = '';

				} else {

					note = $scope.stopTimer.note;

				}

				var dataObj = $.param({

					task: TASKID,

					note: note

				});

				$http.post(BASE_URL + 'tasks/stoptimer', dataObj, config)

					.then(

						function (response) {

							if(response.data.success == true) {

								$mdDialog.hide();

								showToast(NTFTITLE, response.data.message, 'success');

								$('#stopTaskTimer').css('display', 'none');

								$('#startTaskTimer').attr('style','display: block !important');

								if (TASKID == $scope.timer.task_id) {

									$('#timerStarted').removeClass('text-success');

									$('#timerStarted').addClass('text-muted');

								}

							} else {

								globals.mdToast('error', response.data.message);

							}

						},

						function (response) {

							console.log(response);

						}

					);

			};

			$scope.close = function(){$mdDialog.hide();}

		}





		$scope.UpdateTask = function () {

			if ($scope.task.public === true) {

				$scope.isPublic = 1;

			} else {

				$scope.isPublic = 0;

			}

			if ($scope.task.visible === true) {

				$scope.isVisible = 1;

			} else {

				$scope.isVisible = 0;

			}

			if ($scope.task.billable === true) {

				$scope.isBillable = 1;

			} else {

				$scope.isBillable = 0;

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

				name: $scope.task.name,

				hourly_rate: $scope.task.hourlyrate,

				assigned: $scope.task.assigned,

				priority: $scope.task.priority_id,

				relation_type: $scope.task.relation_type,

				relation: $scope.task.relation,

				milestone: $scope.task.milestone,

				status_id: $scope.task.status_id,

				public: $scope.isPublic,

				billable: $scope.isBillable,

				visible: $scope.isVisible,

				startdate: moment($scope.task.startdate_edit).format("YYYY-MM-DD"),

				duedate: moment($scope.task.duedate_edit).format("YYYY-MM-DD"),

				description: $scope.task.description,

				custom_fields: $scope.tempArr,

			});

			var posturl = BASE_URL + 'tasks/update/' + TASKID;

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						if(response.data.success == true) {

							$mdSidenav('Update').close();

							globals.mdToast('success', response.data.message);

							$http.get(BASE_URL + 'tasks/get_task/' + TASKID).then(function (Task) {

								$scope.task = Task.data;

							});

						} else {

							$mdSidenav('Update').close();

							globals.mdToast('error', response.data.message );

						}

					},

					function (response) {

						console.log(response);

					}

				);

		};



		$scope.Delete = function (index) {

			globals.deleteDialog(lang.attention, lang.delete_task, TASKID, lang.doIt, lang.cancel, 'tasks/remove/' + TASKID, function(response) {

				if (response.success == true) {

					globals.mdToast('success',response.message);

					window.location.href = BASE_URL + 'tasks';

				} else {

					globals.mdToast('error',response.message);

				}

			});

		};

	});



	$http.get(BASE_URL + 'tasks/tasktimelogs/' + TASKID).then(function (TimeLogs) {

		$scope.timelogs = TimeLogs.data;

		$scope.getTotal = function () {

			var total = 0;

			for (var i = 0; i < $scope.timelogs.length; i++) {

				var timelog = $scope.timelogs[i];

				total += (timelog.timed);

			}

			return total;

		};

		$scope.ProjectTotalAmount = function () {

			var total = 0;

			for (var i = 0; i < $scope.timelogs.length; i++) {

				var timelog = $scope.timelogs[i];

				total += (timelog.amount);

			}

			return total;

		};

	});



	$http.get(BASE_URL + 'api/milestones').then(function (Milestones) {

		$scope.milestones = Milestones.data;

	});



	$http.get(BASE_URL + 'tasks/taskfiles/' + TASKID).then(function (Files) {

		$scope.files = Files.data;



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

		

		$scope.ViewFile = function(index, image) {

			$scope.file = $scope.files[index];

			$mdDialog.show({

				templateUrl: 'view_image.html',

				scope: $scope,

				preserveScope: true,

				targetEvent: $scope.file.id

			});

		};



		$scope.DeleteFile = function(id) {

			var confirm = $mdDialog.confirm()

				.title($scope.lang.delete_file_title)

				.textContent($scope.lang.delete_file_message)

				.ariaLabel($scope.lang.delete_file_title)

				.targetEvent(TASKID)

				.ok($scope.lang.delete)

				.cancel($scope.lang.cancel);



			$mdDialog.show(confirm).then(function () {

				$http.post(BASE_URL + 'tasks/delete_file/' + id, config)

					.then(

						function (response) {

							if(response.data.success == true) {

								globals.mdToast('success', response.data.message);

								$http.get(BASE_URL + 'tasks/taskfiles/' + TASKID).then(function (Files) {

									$scope.files = Files.data;

								});

							} else {

								globals.mdToast('error', response.data.message);

							}

						},

						function (response) {

							console.log(response);

						}

					);



			}, function() {

				//

			});

		};

		$scope.DeleteFiles = function(id) {

			var confirm = $mdDialog.confirm()

				.title($scope.lang.delete_file_title)

				.textContent($scope.lang.delete_file_message)

				.ariaLabel($scope.lang.delete_file_title)

				.targetEvent(TASKID)

				.ok($scope.lang.delete)

				.cancel($scope.lang.cancel);



			$mdDialog.show(confirm).then(function () {

				$http.post(BASE_URL + 'tasks/delete_file/' + id, config)

					.then(

						function (response) {

							if(response.data.success == true) {

								globals.mdToast('success', response.data.message);

								$http.get(BASE_URL + 'tasks/taskfiles/'+ TASKID).then(function (Files) {

									$scope.files = Files.data;

								});

							} else {

								globals.mdToast('error', response.data.message);

							}

						},

						function (response) {

							console.log(response);

						}

					);



			}, function() {

				//

			});

		};

	});



	$scope.uploading = false; 

	$scope.uploadTaskFile = function() {

		$scope.uploading = true;

        var file = $scope.project_file;

        var uploadUrl = BASE_URL+'tasks/add_file/'+TASKID;

        fileUpload.uploadFileToUrl(file, uploadUrl, function(response) {

        	if (response.success == true) {

        		showToast(NTFTITLE, response.message, ' success');

        	} else {

        		showToast(NTFTITLE, response.message, ' danger');

        	}

        	$http.get(BASE_URL + 'tasks/taskfiles/' + TASKID).then(function (Files) {

        		$scope.files = Files.data;

        	});

        	$scope.uploading = false;

        	$mdDialog.hide();

        });

    };



	$http.get(BASE_URL + 'tasks/subtasks/' + TASKID).then(function (Subtasks) {

		$scope.subtasks = Subtasks.data;

		$scope.createTask = function () {

			var dataObj = $.param({

				description: $scope.newTitle,

				taskid: TASKID,

			});

			var posturl = BASE_URL + 'tasks/addsubtask';

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						if(response.data.success == true) {

							$scope.subtasks.unshift({

								description: $scope.newTitle,

								date: Date.now()

							});

							$scope.newTitle = '';

							console.log(response);

						} else {

							globals.mdToast('error', response.data.message);

						}

					},

					function (response) {

						console.log(response);

					}

				);

		};



		$scope.removeTask = function (index) {

			var subtask = $scope.subtasks[index];

			var dataObj = $.param({

				subtask: subtask.id

			});

			$http.post(BASE_URL + 'tasks/removesubtasks', dataObj, config)

				.then(

					function (response) {

						if(response.data.success == true){

							$scope.subtasks.splice($scope.subtasks.indexOf(subtask), 1);

						} else {

							globals.mdToast('error', response.data.message);

						}

					},

					function (response) {

						console.log(response);

					}

				);

		};



		$scope.completeTask = function (index) {

			var subtask = $scope.subtasks[index];

			var dataObj = $.param({

				subtask: subtask.id

			});

			$http.post(BASE_URL + 'tasks/completesubtasks', dataObj, config)

				.then(

					function (response) {

						if(response.data.success == true){

							subtask.complete = true;

							$scope.subtasks.splice($scope.subtasks.indexOf(subtask), 1);

							$scope.SubTasksComplete.unshift(subtask);

						} else {

							globals.mdToast('error', response.data.message);

						}

					},

					function (response) {

						console.log(response);

					}

				);

		};



		$scope.uncompleteTask = function (index) {

			var task = $scope.SubTasksComplete[index];

			var dataObj = $.param({

				task: task.id

			});

			$http.post(BASE_URL + 'tasks/uncompletesubtasks', dataObj, config)

				.then(

					function (response) {

						if(response.data.success == true) {

							var task = $scope.SubTasksComplete[index];

							$scope.SubTasksComplete.splice($scope.SubTasksComplete.indexOf(task), 1);

							$scope.subtasks.unshift(task);

						} else {

							globals.mdToast('error', response.data.message);

						}

					},

					function (response) {

						console.log(response);

					}

				);

		};



	});



	$http.get(BASE_URL + 'tasks/subtaskscomplete/' + TASKID).then(function (SubTasksComplete) {

		$scope.taskCompletionTotal = function (unit) {

			var total = $scope.taskLength();

			return Math.floor(100 / total * unit);

		};

		$scope.SubTasksComplete = SubTasksComplete.data;

		$scope.taskLength = function () {

			return $scope.subtasks.length + $scope.SubTasksComplete.length;

		};

	});



	$scope.MarkAsCompleteTask = function () {

		var dataObj = $.param({

			task: TASKID,

		});

		$http.post(BASE_URL + 'tasks/markascompletetask', dataObj, config)

			.then(

				function (response) {

					if(response.data.success == true) {

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



	$scope.MarkAsCancelled = function () {

		var dataObj = $.param({

			task: TASKID,

		});

		$http.post(BASE_URL + 'tasks/markascancelled', dataObj, config)

			.then(

				function (response) {

					if(response.data.success == true) {

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

}



CiuisCRM.controller('Tasks_Controller', Tasks_Controller);

CiuisCRM.controller('Task_Controller', Task_Controller);