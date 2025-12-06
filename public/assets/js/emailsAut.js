function Emails_Aut_Controller($scope, $http, $mdSidenav, $mdDialog, $filter, $sce) {
    'use strict';
    $scope.saving = false;
    $scope.TEMPtaskAut = [];
    $scope.emailsAutLoader = true;
    $scope.leads_select = [];
    $scope.totalPeriodos = 1;

    $http.get(BASE_URL + 'api/staff').then(function (Staff) {
		$scope.staff = Staff.data;
	});


    $scope.close = function () {
        $mdSidenav('Create').close();
        $mdDialog.hide();
    };


    $scope.Update = function (taskAut) {
        $scope.TEMPtaskAut = JSON.parse(JSON.stringify(taskAut));
        //  $scope.TEMPtaskAut.hora_taskAut = new Date('2021-01-01 ' + taskAut.hora_taskAut);

        $scope.TEMPtaskAut.periodo1_taskAut = parseInt(taskAut.periodo1_taskAut);
        $scope.TEMPtaskAut.periodo2_taskAut = parseInt(taskAut.periodo2_taskAut);
        $scope.TEMPtaskAut.periodo3_taskAut = parseInt(taskAut.periodo3_taskAut);
        $scope.TEMPtaskAut.periodo4_taskAut = parseInt(taskAut.periodo4_taskAut);
        $scope.TEMPtaskAut.periodo5_taskAut = parseInt(taskAut.periodo5_taskAut);
        $scope.TEMPtaskAut.periodo6_taskAut = parseInt(taskAut.periodo6_taskAut);
        $scope.TEMPtaskAut.periodo7_taskAut = parseInt(taskAut.periodo7_taskAut);
        $scope.TEMPtaskAut.periodo8_taskAut = parseInt(taskAut.periodo8_taskAut);
        $scope.TEMPtaskAut.periodo9_taskAut = parseInt(taskAut.periodo9_taskAut);
        $scope.TEMPtaskAut.periodo10_taskAut = parseInt(taskAut.periodo10_taskAut);
        $scope.TEMPtaskAut.periodo11_taskAut = parseInt(taskAut.periodo11_taskAut);
        $scope.TEMPtaskAut.periodo12_taskAut = parseInt(taskAut.periodo12_taskAut);
        $scope.TEMPtaskAut.periodo13_taskAut = parseInt(taskAut.periodo13_taskAut);
        $scope.TEMPtaskAut.periodo14_taskAut = parseInt(taskAut.periodo14_taskAut);
        $scope.TEMPtaskAut.periodo15_taskAut = parseInt(taskAut.periodo15_taskAut);
        $scope.totalPeriodos = $scope.TEMPtaskAut.totalPeriodos;

        $mdSidenav('Create').toggle();
        //   $('#select_leads').html('');
        //   taskAut.leads.map((lead) => {
        //      $('#select_leads').append('<option value="' + lead.id_lead + '" selected="selected" >' + lead.nm_lead + '</option>');
        //   });
    };

    $scope.Create = function (taskAut) {
        $scope.totalPeriodos = 1;
        $scope.TEMPtaskAut = [];
        $mdSidenav('Create').toggle();
    }

    $scope.getTaskAut = function () {
        $http.get(BASE_URL + 'EmailsAut/getTaskAut').then(function (data) {
            $scope.emailsAut = data.data;
            $scope.emailsAutLoader = false;
        });
    }

    $scope.Remove = function (id) {
        globals.deleteDialog(lang.attention, 'Tem certeza que deseja remover?', id, lang.doIt, lang.cancel, 'EmailsAut/delete/' + id, function (response) {
            if (response.success == true) {
                globals.mdToast('success', response.message);
                $scope.getTaskAut();
            } else {
                globals.mdToast('error', response.message);
            }
        });
    }

    $scope.getTaskAut();

    $scope.AddTaskAut = function () {

        if($scope.TEMPtaskAut.tipo == null){
            globals.mdToast('error', "Informe o tipo do Roadmap");
            return;
        }

        if($scope.TEMPtaskAut.respon_email == null){
            globals.mdToast('error', "Informe o responsável do email");
            return;
        }

        $scope.saving = true;
        //   var hora_taskAut = new Date($scope.TEMPtaskAut.hora_taskAut).getHours() + ':' + new Date($scope.TEMPtaskAut.hora_taskAut).getMinutes();
        var dataObj = $.param({

            name_taskAut: $scope.TEMPtaskAut.name_taskAut,
            periodo1_taskAut: $scope.TEMPtaskAut.periodo1_taskAut,
            periodo2_taskAut: $scope.TEMPtaskAut.periodo2_taskAut,
            periodo3_taskAut: $scope.TEMPtaskAut.periodo3_taskAut,
            periodo4_taskAut: $scope.TEMPtaskAut.periodo4_taskAut,
            periodo5_taskAut: $scope.TEMPtaskAut.periodo5_taskAut,

            periodo6_taskAut: $scope.TEMPtaskAut.periodo6_taskAut,
            periodo7_taskAut: $scope.TEMPtaskAut.periodo7_taskAut,
            periodo8_taskAut: $scope.TEMPtaskAut.periodo8_taskAut,
            periodo9_taskAut: $scope.TEMPtaskAut.periodo9_taskAut,
            periodo10_taskAut: $scope.TEMPtaskAut.periodo10_taskAut,
            periodo11_taskAut: $scope.TEMPtaskAut.periodo11_taskAut,
            periodo12_taskAut: $scope.TEMPtaskAut.periodo12_taskAut,
            periodo13_taskAut: $scope.TEMPtaskAut.periodo13_taskAut,
            periodo14_taskAut: $scope.TEMPtaskAut.periodo14_taskAut,
            periodo15_taskAut: $scope.TEMPtaskAut.periodo15_taskAut,

            //     hora_taskAut: hora_taskAut, 
            id_task: $scope.TEMPtaskAut.id_task,
            copia_para_taskAut: $scope.TEMPtaskAut.copia_para_taskAut,
            respon_email: $scope.TEMPtaskAut.respon_email,
            copia_para_oculto_taskAut: $scope.TEMPtaskAut.copia_para_oculto_taskAut,
            tipo: $scope.TEMPtaskAut.tipo,

            enviar_para: $scope.TEMPtaskAut.enviar_para,

            template1: $scope.TEMPtaskAut.template1,
            template2: $scope.TEMPtaskAut.template2,
            template3: $scope.TEMPtaskAut.template3,
            template4: $scope.TEMPtaskAut.template4,
            template5: $scope.TEMPtaskAut.template5,

            template6: $scope.TEMPtaskAut.template6,
            template7: $scope.TEMPtaskAut.template7,
            template8: $scope.TEMPtaskAut.template8,
            template9: $scope.TEMPtaskAut.template9,
            template10: $scope.TEMPtaskAut.template10,
            template11: $scope.TEMPtaskAut.template11,
            template12: $scope.TEMPtaskAut.template12,
            template13: $scope.TEMPtaskAut.template13,
            template14: $scope.TEMPtaskAut.template14,
            template15: $scope.TEMPtaskAut.template15,
           // leads: $('#select_leads').val(),
        });

        $http.post(BASE_URL + 'EmailsAut/create', dataObj, config)
            .then(function (response) {
                if (response.data.success == true) {
                    globals.mdToast('success', response.data.message);
                    $mdSidenav('Create').close();
                    $scope.getTaskAut();
                    $scope.TEMPtaskAut = [];
                } else {
                    globals.mdToast('error', response.data.message);
                }

                $scope.saving = false;
            }, function (response) {
                $scope.saving = false;
            });
    }

    $http.get(BASE_URL + 'emails/get_email_templates').then(function (Templates) {
        $scope.templates = Templates.data;
    });


    $scope.AddMPeriodo = function () {
        $scope.totalPeriodos++;

        /*
        var html = '<md-input-container class="md-block" flex-gt-xs flex="70">'
        html += '<label>Modelo de email (Envio ' + $scope.totalPeriodos + ')</label>'
        html += '<md-select ng-model="TEMPtaskAut.template' + $scope.totalPeriodos + '">'
        html += '<md-option ng-value="template.id" ng-repeat="template in templates">{{template.name != null ? template.name : template.subject}}</md-option>'
        html += '</md-select>'
        html += '</md-input-container>'
        html += '<md-input-container class="md-block" flex-gt-xs flex="30">'
        html += '<label>Período (dias)</label>'
        html += '<input type="number" max="180" min="1" ng-model="TEMPtaskAut.periodo' + $scope.totalPeriodos + '_taskAut">'
        html += '</md-input-container>';
        angular.element(document.getElementById('addPeriodos')).append(html);
         */
    }
}
CiuisCRM.controller('Emails_Aut_Controller', Emails_Aut_Controller);
