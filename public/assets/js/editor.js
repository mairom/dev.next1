function Editor_Controller($scope, $http, $mdToast) {
	"use strict";

	$scope.editor_data = '';
	$http.get(BASE_URL + 'editor/get_data/').then(function (Data) {
		$scope.editor_data = Data.data;
		editor.session.setValue($scope.editor_data);
	});

	$scope.loadingFile = false;
	$scope.getFileContent = function(file) {
		$scope.loadingFile = true;
		if ( file == 1 || file == 2 || file == 3 || file == 4 || file == 5 || file == 6 || file == 7 || file == 8 ) {
			editor.getSession().setMode("ace/mode/php");
		} if ( file == 9 || file == 10 ) {
			editor.getSession().setMode("ace/mode/json");
		}
		$http.get(BASE_URL + 'editor/get_data/'+file).then(function (Data) {
			$scope.editor_data = Data.data;
			editor.session.setValue($scope.editor_data);
			$scope.loadingFile = false;
		});
	}
	

	$scope.restoring = false;
	$scope.RestoreDefault = function(file) {
		$scope.restoring = true;
		$scope.loadingFile = true;
		$http.get(BASE_URL + 'editor/restore_default/'+file).then(function (response) {
			$scope.editor_data = response.data;
			editor.session.setValue($scope.editor_data);
			$scope.loadingFile = false;
			$scope.restoring = false;
		});
	}

	$scope.saving = false;
	$scope.SaveFile = function(file) {
		$scope.saving = true;
		var dataObj = $.param({
			data: editor.getValue()
		});
		var config = {
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8;'
			}
		};
		var posturl = BASE_URL + 'projects/create';
		$http.post(BASE_URL + 'editor/save/'+file, dataObj, config)
		.then(
			function (response) {
				$scope.saving = false;
				if (response.data.success == true) {
					globals.mdToast('success', response.data.message);
				} else {
					globals.mdToast('error', response.data.message);
				}
			}, function(err) {
				$scope.saving = false;
				console.log(err);
			});
	}
//editor.getValue();
}
CiuisCRM.controller('Editor_Controller', Editor_Controller);