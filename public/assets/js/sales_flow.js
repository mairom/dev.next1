function Sales_Controller($scope, $http, $mdSidenav, $mdDialog, $mdConstant, $filter, $q) {
	"use strict";
	$scope.SalesLoader = true;
	$scope.saving = false;
	$scope.filtros = [];
	$scope.fluxoModal = [];
	$scope.etapaModal = [];
	$scope.newEtapaRamo = [];
	$scope.letras = {
		'1': "A", '2': "B", '3': "C", '4': "D", '5': "E", '6': "F", '7': "G", '8': "H",
		'9': "I", '10': "J", '11': "K", '12': "L", '13': "M", '14': "N", '15': "O", '16': "P",
		'17': "Q", '18': "R", '19': "S", '20': "T", '21': "U", '22': "V", '23': "W", '24': "X", '25': "Y", '26': "Z"
	};

	$http.get(BASE_URL + 'emails/get_email_templates').then(function (Templates) {
		$scope.templates = Templates.data;
	});

	$http.get(BASE_URL + 'api/custom_fields_by_type/' + 'lead').then(function (custom_fields) {
		$scope.SalesLoader = false;
	});

	$http.get(BASE_URL + 'leads/leadslist').then(function (data) {
		$scope.leadslist = data.data;
		$scope.filtros.flt_funil = Object.values(data.data)[0].id_list;
	});

	$http.get(BASE_URL + 'api/staff').then(function (Staff) {
		$scope.staff = Staff.data;
	});

	$http.get(BASE_URL + 'leads/leadsources').then(function (LeadSources) {
		$scope.leadssources = LeadSources.data;
	});

	$http.get(BASE_URL + 'sales_flow/fluxos').then(function (data) {
		$scope.fluxos = data.data;
		$scope.filtros.fluxo = Object.values(data.data)[0].id_fluxo;
	});

	$http.get(BASE_URL + 'leads/getListSelectLeads').then(function (data) {
		$scope.leadAtvSelect = data.data;
	});

	$scope.close = function () {
		$mdSidenav('Create').close();
		$mdDialog.hide();
	};

	$scope.Create = function (fluxo = []) {
		$scope.fluxoModal = fluxo;
		$mdSidenav('Create').toggle();
	};

	$scope.CreateRamo = function (etapa = []) {
		$scope.newEtapaRamo = [];
		$scope.etapaModal = etapa;
		
		$('#modalRamo').modal('show');
	};

	$scope.EditRamo = function (etapa = []) {
		$scope.newEtapaRamo = etapa;
		
		$scope.etapaModal = [];
		$('#modalRamo').modal('show');
	};

	$scope.copyDescricao = function () {
		var copyText = document.getElementById("campoCopiar");
		copyText.select();
		copyText.setSelectionRange(0, 99999);
		navigator.clipboard.writeText(copyText.value);
		globals.mdToast('success', 'Copiado para area de transferência.');
	}

	$scope.addVar = function (text, areaId) {
		var txtarea = document.getElementById(areaId);
		if (!txtarea) {
			return;
		}

		var scrollPos = txtarea.scrollTop;
		var strPos = 0;
		var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
			"ff" : (document.selection ? "ie" : false));
		if (br == "ie") {
			txtarea.focus();
			var range = document.selection.createRange();
			range.moveStart('character', -txtarea.value.length);
			strPos = range.text.length;
		} else if (br == "ff") {
			strPos = txtarea.selectionStart;
		}

		var front = (txtarea.value).substring(0, strPos);
		var back = (txtarea.value).substring(strPos, txtarea.value.length);
		txtarea.value = front + text + back;
		strPos = strPos + text.length;
		if (br == "ie") {
			txtarea.focus();
			var ieRange = document.selection.createRange();
			ieRange.moveStart('character', -txtarea.value.length);
			ieRange.moveStart('character', strPos);
			ieRange.moveEnd('character', 0);
			ieRange.select();
		} else if (br == "ff") {
			txtarea.selectionStart = strPos;
			txtarea.selectionEnd = strPos;
			txtarea.focus();
		}

		txtarea.scrollTop = scrollPos;
	}

	$scope.excluirEtapa = function () {
		$('#modalRamo').modal('hide');
		var confirm = $mdDialog.confirm()
			.title('Tem certeza?')
			.textContent('Tem certeza que deseja apagar esta etapa?')
			.ariaLabel('Delete Converted Leads')
			.ok('Sim')
			.cancel('Cancelar');

		$mdDialog.show(confirm).then(function () {
			$http.post(BASE_URL + 'sales_flow/remove_etapa/' + $scope.newEtapaRamo.id_etapa, config).then(
				function (response) {
					globals.mdToast('success', 'Excluido com sucesso!');
					$http.get(BASE_URL + 'sales_flow/fluxos').then(function (data) {
						$scope.fluxos = data.data;
					});
				},
				function (response) {
					console.log(response);
				}
			);
		}, function () {
		});
	}

	$scope.RmvFlow = function () {
		$scope.close();

		var confirm = $mdDialog.confirm()
			.title('Tem certeza?')
			.textContent('Tem certeza que deseja apagar este flow?')
			.ok('Sim')
			.cancel('Cancelar');

		$mdDialog.show(confirm).then(function () {
			$http.post(BASE_URL + 'sales_flow/remove_flow/' + $scope.fluxoModal.id_fluxo, config).then(
				function (response) {
					globals.mdToast('success', 'Excluido com sucesso!');
					$http.get(BASE_URL + 'sales_flow/fluxos').then(function (data) {
						$scope.fluxos = data.data;
					});
				},
				function (response) {
					console.log(response);
				}
			);
		}, function () {
		});
	}

	$scope.AddRamo = function () {

		
	//	console.log($scope.etapaModal.id_etapa);
		//return;
		if ($scope.newEtapaRamo.id_atividade == null || $scope.newEtapaRamo.id_atividade == "") {
			globals.mdToast('error', "Informe uma atividade!");
			return;
		}

		if ($scope.newEtapaRamo.id_etapa != null) {
			console.log('salvando');
			$scope.newEtapaRamo.atividade = $scope.leadAtvSelect.find(a => a.id_atv == $scope.newEtapaRamo.id_atividade);

		} else if ($scope.etapaModal.id_etapa == null) {
			let fluxo = $scope.fluxos.find(a => a.id_fluxo == $scope.etapaModal.id_fluxo);
			console.log($scope.fluxos)
			if(fluxo == null || fluxo.etapas == null){
				fluxo.etapas = [];
			}
			fluxo.etapas.push({
				id_atividade: $scope.newEtapaRamo.id_atividade,
				canal: $scope.newEtapaRamo.canal,
				responsavel: $scope.newEtapaRamo.responsavel,
				automatico: $scope.newEtapaRamo.automatico,
				enviar_para: $scope.newEtapaRamo.enviar_para,
				
				cc: $scope.newEtapaRamo.cc,
				cco: $scope.newEtapaRamo.cco,
				
				id_etapa: hashCode(new Date().toISOString()),
				dias: $scope.newEtapaRamo.dias,
				descricao: $scope.newEtapaRamo.descricao,
				campoCopiar: $scope.newEtapaRamo.campoCopiar,
				email: $scope.newEtapaRamo.email,
				usar_ia: $scope.newEtapaRamo.usar_ia,
				atividade: $scope.leadAtvSelect.find(a => a.id_atv == $scope.newEtapaRamo.id_atividade),
				numero: $scope.etapaModal.etapas.length + 1
			});
		} else {
			if ($scope.etapaModal.etapas == null) {
				$scope.etapaModal.etapas = [];
			}
			$scope.etapaModal.etapas.push({
				id_atividade: $scope.newEtapaRamo.id_atividade,
				id_etapa_pai: $scope.etapaModal.id_etapa,
				canal: $scope.newEtapaRamo.canal,
				responsavel: $scope.newEtapaRamo.responsavel,
				automatico: $scope.newEtapaRamo.automatico,
				enviar_para: $scope.newEtapaRamo.enviar_para,

				cc: $scope.newEtapaRamo.cc,
				cco: $scope.newEtapaRamo.cco,
				
				id_etapa: hashCode(new Date().toISOString()),
				dias: $scope.newEtapaRamo.dias,
				descricao: $scope.newEtapaRamo.descricao,
				campoCopiar: $scope.newEtapaRamo.campoCopiar,
				email: $scope.newEtapaRamo.email,
				usar_ia: $scope.newEtapaRamo.usar_ia,
				atividade: $scope.leadAtvSelect.find(a => a.id_atv == $scope.newEtapaRamo.id_atividade),
				numero: $scope.etapaModal.numero + '-' + $scope.letras[$scope.etapaModal.etapas != null ? $scope.etapaModal.etapas.length + 1 : 0]
			});
		}

		$scope.SalvaFluxo();
		console.log($scope.etapaModal)
		$(".modal").modal('hide');
	};


	$scope.SalvaFluxo = function () {
		var fluxo = $scope.fluxos.find(a => a.id_fluxo == $scope.filtros.fluxo);
	
		var formData = new FormData();
		formData.append('fluxo', JSON.stringify(fluxo));
	
		// pegar arquivo sem diretiva
		var fileInput = document.getElementById('fileEtapa');
		if (fileInput && fileInput.files.length > 0) {
			formData.append('file', fileInput.files[0]);
		}
	
		$http.post(BASE_URL + 'sales_flow/SalvaFluxo', formData, {
			transformRequest: angular.identity,
			headers: { 'Content-Type': undefined }
		}).then(function (response) {
			globals.mdToast('success', 'Salvo com sucesso!');
			document.getElementById('fileEtapa').value = "";
		});
	};

	$scope.findAtividadeByid = function (id_atv) {
		if ($scope.leadAtvSelect != null) {
			return $scope.leadAtvSelect.find(a => a.id_atv == id_atv);
		} else {
			return [];
		}

	}



	$scope.dimiss = function (ev) {
		$(".modal").modal('hide');
	};

	$scope.add = function () {
		if ($scope.fluxoModal.etapas == null) {
			$scope.fluxoModal.etapas = [];
		}
		$scope.fluxoModal.etapas.push({
			descricao: '',
			campoCopiar: '',
			numero: $scope.fluxoModal.etapas.length + 1
		});
	};




	$scope.getHeight = function (index) {
		var height = 0;
		if (index > 0) {
			height = $('.elementF' + (index - 1)).height() - 40;
		}

		return height;
	}



	$scope.AddFluxo = function () {
		$scope.saving = true;
		var dataObj = $.param({
			name: $scope.fluxoModal.name,
			funil: $scope.fluxoModal.funil,
			automatico: $scope.fluxoModal.automatico,
			funcionario: $scope.fluxoModal.funcionario,
			origem: $scope.fluxoModal.origem,
			id_fluxo: $scope.fluxoModal.id_fluxo,
			etapas: $scope.fluxoModal.etapas
		});
		$http.post(BASE_URL + 'sales_flow/create', dataObj, config).then(
			function (response) {
				$scope.saving = false;
				if (response.data.success == true) {
					$mdSidenav('Create').close();
					globals.mdToast('success', response.data.message);

					$http.get(BASE_URL + 'sales_flow/fluxos').then(function (data) {
						$scope.fluxos = data.data;
					});
				} else {
					globals.mdToast('error', response.data.message);
				}
			},

			function (response) {
				$scope.saving = false;
			}
		);

	};
}

CiuisCRM.controller('Sales_Controller', Sales_Controller);

CiuisCRM.directive("myDirective", function () {
	return {
		template: `
		<div class="element-filho" ng-repeat="etapa in etapa.etapas | filter: { id_etapa_pai: etapa.id_etapa}">
			<div class="icon-element">
				
				<p class="inf-num" style = "float: unset;margin: 0 auto;width: max-content;padding: 2px 4px 0px 4px;min-width: 25px;">{{etapa.numero}}</p>
				<button type="button" class="btn_edit6" ng-click="CreateRamo(etapa)">
					<i class="fas fa-plus"></i>
				</button>

				<button style = "right: -10px;" type="button" class="btn_edit6" ng-click="EditRamo(etapa)">
					<i style = "font-size: 13px;color: #3d8aac;" class="fas fa-pen"></i>
				</button>
		
				<div style="background-image: url('{{base_url + etapa.atividade.atv_ft}}');" ng-click="EditRamo(etapa)"></div>
			</div>
			<div class="element-line"></div>
			<!--
                <p class="desc-element">{{etapa.descricao}}</p>
            -->

			<div style="display: flex;" ng-if = "etapa.etapas.length > 0">
				<div style = "display: flex;" class = 'element_vert' my-directive></div>
			</div>

		</div>
	
		
		`
	};
});
