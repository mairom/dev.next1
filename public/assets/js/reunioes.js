function Reuniao_Controller($scope, $http, $mdSidenav, $filter, $mdDialog, fileUpload, $q) {
	"use strict";

	$scope.days = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
	$scope.monthNames = [
		'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
		'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
	];
	$scope.months = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11];
	$scope.reunioes = [];
	$scope.filtro = [];
	$scope.filtro.id_funcionario = '-1';
	$scope.reuniaoModal = [];
	$scope.createReuniao = [];

	var currentDate = new Date();
	$scope.dayF = currentDate.getDate() < 10 ? '0' + currentDate.getDate() : currentDate.getDate();
	$scope.monthF = (currentDate.getMonth() + 1) < 10 ? '0' + (currentDate.getMonth() + 1) : (currentDate.getMonth() + 1);

	$scope.month = currentDate.getMonth();
	$scope.year = currentDate.getFullYear();
	$scope.monthName = $scope.monthNames[$scope.month];


	$http.get(BASE_URL + 'api/staff').then(function (Staff) {
		$scope.staff = Staff.data;
	});

	function generateCalendar(month, year) {
		var date = new Date(year, month, 1);
		var weeks = [];
		var week = [];
		var firstDay = (date.getDay() + 6) % 7;
		for (var i = 0; i < firstDay; i++) {
			week.push({
				number: '',
				month: month - 1
			});
		}
		while (date.getMonth() === month) {
			week.push({
				number: date.getDate(),
				month: month
			});
			if (week.length === 7) {
				weeks.push(week);
				week = [];
			}
			date.setDate(date.getDate() + 1);
		}
		if (week.length > 0) {
			while (week.length < 7) {
				week.push({
					number: '',
					month: month + 1
				});
			}
			weeks.push(week);
		}
		$scope.weeks = weeks;
	}

	generateCalendar($scope.month, $scope.year);

	$scope.previousMonth = function () {
		if ($scope.month === 0) {
			$scope.month = 11;
			$scope.year--;
		} else {
			$scope.month--;
		}
		$scope.monthName = $scope.monthNames[$scope.month];
		generateCalendar($scope.month, $scope.year);
		$scope.get_reunioes();
	};

	$scope.openReuniao = function (reuniao) {
		$scope.reuniaoModal = reuniao;
		$("#modalDetalhesReuniao").modal('show');
		//window.open(BASE_URL + "leads/lead/" + reuniao.id_lead, "_blank")
	};

	$scope.openCreateReuniao = function () {
		$mdSidenav('modalReuniao').toggle();
	}

	$scope.create_reuniao = function () {
		let data = null;
		let hora = null;

		if ($scope.createReuniao.date != null) {
			data = moment($scope.createReuniao.date).format("YYYY-MM-DD");
			hora = moment($scope.createReuniao.date).format("HH:mm");
		} else {
			globals.mdToast('error', "informe uma data");
		}

		if ($scope.createReuniao.id_funcionario == null) {
			globals.mdToast('error', "informe um funcionário");
		}

		var dataObj = $.param({
			data,
			hora,
			id_funcionario: $scope.createReuniao.id_funcionario
		});


		$http.post(BASE_URL + 'reuniao/salva_reuniao2', dataObj, config).then(function (response) {
			$mdSidenav('modalReuniao').toggle();
			globals.mdToast('success', 'Alterado com sucesso!');
			$scope.get_reunioes();

		});
	};

	$scope.salva_reuniao = function (id_reuniao) {

		$('#modalDetalhesReuniao').modal('hide');

		var dataObj = $.param({
			id_reuniao,
			realizada: $scope.reuniaoModal.realizada,
			problema: $scope.reuniaoModal.problema,
			motivo: $scope.reuniaoModal.motivo
		});

		$http.post(BASE_URL + 'reuniao/salva_reuniao', dataObj, config).then(function (response) {
			globals.mdToast('success', 'Alterado com sucesso!');

		});


	};



	$scope.nextMonth = function () {
		if ($scope.month === 11) {
			$scope.month = 0;
			$scope.year++;
		} else {
			$scope.month++;
		}
		$scope.monthName = $scope.monthNames[$scope.month];
		generateCalendar($scope.month, $scope.year);
		$scope.get_reunioes();
	};


	$scope.get_anos = function () {
		var anos = [];
		var data = new Date();
		var anoAtual = data.getFullYear();

		for (var i = (anoAtual - 5); i <= anoAtual + 5; i++) {
			anos.push(i);
		}

		return anos;
	}

	$scope.get_reunioes = function () {
		$scope.monthName = $scope.monthNames[$scope.month];

		var dataObj = $.param({
			month: $scope.month,
			id_funcionario: $scope.filtro.id_funcionario,
			year: $scope.year
		});
		$http.post(BASE_URL + 'reuniao/get_reunioes', dataObj, config).then(function (data) {
			$scope.reunioes = data.data;
		});
	}
	$scope.get_reunioes();

	$scope.apagaReuniao = function (reuniao) {
		$('.modal').modal('hide');
		var confirm = $mdDialog.confirm()
			.title("Atenção!")
			.textContent('Tem certeza que deseja remover essa reunião?')
			.ok("Sim")
			.cancel("Cancelar");

		$mdDialog.show(confirm).then(function () {
			var dataObj = $.param({
				id_reuniao: reuniao.id_reuniao,
			});

			$http.post(BASE_URL + 'reuniao/remove_reuniao', dataObj, config).then((data) => {
				if (data.data.result == true) {
					globals.mdToast('success', "Apagado com sucesso!");

					$scope.get_reunioes();
				}
			}, (err) => {
				console.log(response);
			});
		}, function () {
			//
		});
	}




}

CiuisCRM.controller('Reuniao_Controller', Reuniao_Controller);
