function Timesheets_Controller($scope, $http, $mdSidenav, $mdDialog, $filter, $sce) {

	'use strict';

	$scope.close = function () {

		$mdDialog.hide();

		$http.get(BASE_URL + 'timesheets/get_timesheet_data').then(function (response) {

			$scope.timesheets = response.data.timesheet;

		});

	};



	$scope.loadingTimesheets = true;



	$scope.LogTime = function (ev) {

		$mdDialog.show({

			templateUrl: 'add-timer.html',

			scope: $scope,

			preserveScope: true,

			targetEvent: ev

		});

	};



	$http.get(BASE_URL + 'api/get_open_tasks').then(function (Tasks) {

		$scope.timerTasks = Tasks.data;

	});



	$scope.adding = false;



	$scope.timesheet = {};

	$scope.timesheet.loader = true;



	$scope.timesheet_list = {

		order: '',

		limit: 10,

		page: 1

	};



	function getTimesheets() {

		$http.get(BASE_URL + 'timesheets/get_timesheet_data').then(function (response) {

			$scope.timesheets = response.data.timesheet;

			$scope.limitOptions = [10, 15, 20];

			if ($scope.timesheets.length > 20) {

				$scope.limitOptions = [10, 15, 20, $scope.timesheets.length];

			}

			$scope.total_time = response.data.total;

			$scope.timesheet.loader = false;

			$scope.refreshing = false;

			$scope.loadingTimesheets = false;



			$scope.viewTimesheet = function(index) {

				for (var i = 0; i < $scope.timesheets.length; i++) {

					if ($scope.timesheets[i].id == index) {

						$scope.loggedtime = $scope.timesheets[i];

						continue;

					}

				}

				if ($scope.loggedtime) {

					$mdDialog.show({

						templateUrl: 'view_timesheet.html',

						scope: $scope,

						preserveScope: true,

						targetEvent: $scope.loggedtime.id

					});

				}

			}



			$scope.editTimeLog = function(index) {

				for (var i = 0; i < $scope.timesheets.length; i++) {

					if ($scope.timesheets[i].id == index) {

						$scope.updatetimer = $scope.timesheets[i];

						continue;

					}

				}

				if ($scope.updatetimer) {

					$mdDialog.show({

						templateUrl: 'update_timer.html',

						scope: $scope,

						preserveScope: true,

						targetEvent: $scope.updatetimer.id

					});

				}

			}

		});

	}



	getTimesheets();



	$scope.refreshing = false;

	$scope.refreshTimeLogs = function() {

		$scope.refreshing = true;

		getTimesheets();

	}



	$scope.CreateLogTime = function() {

		$scope.adding = true;

		if (!$scope.logtime) {

			var dataObj = $.param({

				task: '',

				start_time: '',

				end_time: '',

				note: '',

			});

		} else {

			if ($scope.logtime.start_time) {

				$scope.logtime.start_time = moment($scope.logtime.start_time).format("YYYY-MM-DD HH:mm:ss")

			}

			if ($scope.logtime.end_time) {

				$scope.logtime.end_time = moment($scope.logtime.end_time).format("YYYY-MM-DD HH:mm:ss")

			}

			var dataObj = $.param({

				task: $scope.logtime.task,

				start_time: $scope.logtime.start_time,

				end_time: $scope.logtime.end_time,

				note: $scope.logtime.description,

			});

		}

		var posturl = BASE_URL + 'timesheets/logtime';

		$http.post(posturl, dataObj, config)

			.then(

				function (response) {

					$scope.adding = false;

					if (response.data.success == true) {

						showToast(NTFTITLE, response.data.message, ' success');

						$mdDialog.hide();

						getTimesheets();

					} else {

						showToast(NTFTITLE, response.data.message, ' danger');

					}

				},

				function (response) {

					$scope.adding = false;

					showToast(NTFTITLE, 'Error', ' danger');

				}

			);

	}



	$scope.saving = false;

	$scope.UpdateLogTime = function(id) {

		$scope.saving = true;

		if (!$scope.updatetimer) {

			var dataObj = $.param({

				task: '',

				start_time: '',

				end_time: '',

				note: '',

			});

		} else {

			if ($scope.updatetimer.start_time) {

				$scope.updatetimer.start_time = moment($scope.updatetimer.start_time).format("YYYY-MM-DD HH:mm:ss")

			}

			if ($scope.updatetimer.end_time) {

				$scope.updatetimer.end_time = moment($scope.updatetimer.end_time).format("YYYY-MM-DD HH:mm:ss")

			}

			var dataObj = $.param({

				task: $scope.updatetimer.task_id,

				start_time: $scope.updatetimer.start_time,

				end_time: $scope.updatetimer.end_time,

				note: $scope.updatetimer.note,

			});

		}

		var posturl = BASE_URL + 'timesheets/update_logtime/'+id;

		$http.post(posturl, dataObj, config)

			.then(

				function (response) {

					$scope.saving = false;

					if (response.data.success == true) {

						showToast(NTFTITLE, response.data.message, ' success');

						$mdDialog.hide();

						getTimesheets();

						$scope.getTimer();

					} else {

						showToast(NTFTITLE, response.data.message, ' danger');

					}

				},

				function (response) {

					$scope.saving = false;

					showToast(NTFTITLE, 'Error', ' danger');

				}

			);

	}



	$scope.deleteTimesheet = function(id) {

		var confirm = $mdDialog.confirm()

			.title(langs.delete_timelog)

			.textContent(langs.delete_timelog_message)

			.ariaLabel(langs.delete_timelog)

			.targetEvent(id)

			.ok(langs.delete)

			.cancel(langs.cancel);



		$mdDialog.show(confirm).then(function () {

			$http.post(BASE_URL + 'timesheets/delete_log/' + id, config)

				.then(

					function (response) {

						if(response.data.success == true) {

							showToast(NTFTITLE, response.data.message, ' success');

							getTimesheets();

							$scope.getTimer();

						} else {

							showToast(NTFTITLE, response.data.message, ' danger');

						}

					},

					function (response) {

					}

				);



		}, function() {

					//

		});

	};

}



CiuisCRM.controller('Timesheets_Controller', Timesheets_Controller);

