function leadsToDo_Controller($scope, $http, $mdSidenav, $filter, $mdDialog, fileUpload, $q) {
	"use strict";
	SCOPE = $scope;

	//$scope.lead_next = [];
	//$scope.lead_next_total = 0;
	$scope.leads = [];
	$scope.isPlaying = false;

	var ignoraLeads = [];
	if (localStorage.ignoraLeads != null) {
		ignoraLeads = JSON.parse(localStorage.ignoraLeads);
	}

	var dataObj = $.param({
		limite: 20,
		lost: '0',
		flt_vencidos: '2',
		com_fluxo: 1,
		ignoraLeads
	});
	//$http.post(BASE_URL + 'leadsToDo/get_leads_next', dataObj, config).then(function (Leads) {
	//	$scope.lead_next = Leads.data.lead_next;
	//	$scope.lead_next_total = Leads.data.lead_next_total;
	//});

	$http.post(BASE_URL + 'leadsToDo/get_leads', dataObj, config).then(function (Leads) {
		$scope.leads = Leads.data;
	});

	$scope.nextLead = function () {
		window.location.href = base_url + 'leads/lead/' + $scope.leads[0].id + '?page=1';
	};


	$scope.togglePlayPause = function () {


		if (!$scope.isPlaying) {
			$scope.isPlaying = true;
			window.location.href = base_url + 'leads/lead/' +  $scope.leads[0].id + '?page=1';
		} else {
			$scope.isPlaying = false;
			$('#modalPausa').modal('show');
		}
	};





}

CiuisCRM.controller('leadsToDo_Controller', leadsToDo_Controller);
