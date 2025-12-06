function Goals_Controller($scope, $http, $mdSidenav, $filter, $mdDialog, $compile, fileUpload) {
    "use strict";

    $scope.tp_metas = [
        { nome: 'Vendas em Reais', id: '1' },
        { nome: 'Novos clientes', id: '2' },
        { nome: 'Produtos', id: '3' },
        { nome: 'Reuniões/calls', id: '4' },
        { nome: 'Propostas comerciais', id: '5' },
        { nome: 'Faturamento', id: '6' },
        { nome: 'Agendamento de Reunião', id: '7' },
    ];

    $scope.periodos = [
        { nm_periodo: 'Diária', periodo: 1 },
        { nm_periodo: 'Semanal', periodo: 7 },
        { nm_periodo: 'Quinzenal', periodo: 15 },
        { nm_periodo: 'Mensal', periodo: 30 },
        { nm_periodo: 'Bimestral', periodo: 60 },
        { nm_periodo: 'Trimestral', periodo: 90 },
        { nm_periodo: 'Anual', periodo: 365 },
    ];

    $scope.AddDefinicao = function () {
        if ($scope.metaModal.definicoes == null) {
            $scope.metaModal.definicoes = [];
        }
        $scope.metaModal.definicoes.push({ tp_bonificacao: 'R$' });
    }

    $scope.getTp_meta = function (tp) {
        return $scope.tp_metas.filter(a => a.id == tp)[0].nome;
    }
    $scope.moneyEua = function (v) {
        var a = v ? parseFloat(parseFloat(v.toString().replaceAll('.', '').replace(',', '').replace(/([0-9]{2})$/g, ".$1")).toFixed(2)) : 0;
        return a;
    };
    $scope.formatReal = function (int) {
        return new Intl.NumberFormat('pt-BR', { currency: 'BRL' }).format(int);
    }

    $scope.Create = function () {
        $scope.metaModal = [];
        $scope.metaModal.tp_bonificacao = 'R$';
        buildToggler('Create');
    }

    $scope.close = function () {
        $mdSidenav('Create').close();
        $mdDialog.hide();
    }

    function buildToggler(navID) {
        $mdSidenav(navID).toggle();
    }

    $scope.salva_equipe = function () {
        var dataObj = $.param({
            nm_equipe: $scope.modalEquipe.nm_equipe,
            equipe: $scope.modalEquipe.equipe,
            id_equipe: $scope.modalEquipe.id_equipe,
        });

        $http.post(BASE_URL + 'goals/salva_equipe', dataObj, config).then(function (Data) {
            if (Data.data.success) {
                $('.modal').modal('hide');
                $scope.get_equipes();
                showToast('Sucesso!', 'Salvo com sucesso!', ' success');
            } else {
                showToast('Atenção!', 'Erro! tente novamente', ' danger');
            }
        })
    }
    $scope.addGoal = function () {
        var dataObj = $.param({
            nm_goal: $scope.metaModal.nm_goal,
            id_equipe: $scope.metaModal.id_equipe,
            id_goal: $scope.metaModal.id_goal,
            tp_meta: $scope.metaModal.tp_meta,
            inicio: moment($scope.metaModal.inicio).format("YYYY-MM-DD"),
            fim: moment($scope.metaModal.fim).format("YYYY-MM-DD"),
            observacao: $scope.metaModal.observacao,
            definicoes: $scope.metaModal.definicoes,
            periodo: $scope.metaModal.periodo
        });

        $http.post(BASE_URL + 'goals/salva_meta', dataObj, config).then(function (Data) {
            if (Data.data.success) {
                $scope.close();
                $scope.get_metas();
                showToast('Sucesso!', 'Salvo com sucesso!', ' success');
            } else {
                showToast('Atenção!', 'Erro! tente novamente', ' danger');
            }
        })
    }

    $scope.removeEquipe = function (id_equipe) {
        if (confirm('Você tem certeza que deseja excluir essa equipe?')) {
            var dataObj = $.param({
                id_equipe
            });

            $http.post(BASE_URL + 'goals/remove_equipe', dataObj, config).then(function (Data) {
                if (Data.data.success) {
                    $scope.get_equipes();
                    showToast('Sucesso!', 'Salvo com sucesso!', ' success');
                } else {
                    showToast('Atenção!', 'Erro! tente novamente', ' danger');
                }
            })
        }
    }

    $scope.removeMeta = function (id_goal) {
        if (confirm('Você tem certeza que deseja excluir essa meta?')) {
            var dataObj = $.param({
                id_goal
            });

            $http.post(BASE_URL + 'goals/remove_goal', dataObj, config).then(function (Data) {
                if (Data.data.success) {
                    $scope.get_metas();
                    showToast('Sucesso!', 'Salvo com sucesso!', ' success');
                } else {
                    showToast('Atenção!', 'Erro! tente novamente', ' danger');
                }
            })
        }
    }

    $scope.get_equipes = function () {
        $http.get(BASE_URL + 'goals/get_equipes/').then(function (Data) {
            $scope.equipes = Data.data;
        })
    }
    $scope.get_equipes();

    $scope.get_metas = function () {
        $http.get(BASE_URL + 'goals/get_metas/').then(function (Data) {
            $scope.metas = Data.data;
        })
    }
    $scope.get_metas();


    $scope.editMeta = function (meta = null) {
        $scope.metaModal = {
            nm_goal: meta.nm_goal,
            id_equipe: meta.id_equipe,
            id_goal: meta.id_goal,
            tp_meta: meta.tp_meta,
            periodo: meta.periodo,
            observacao: meta.observacao,
            periodo: meta.periodo,
            inicio: new Date(meta.inicio + ' 00:00'),
            fim: new Date(meta.fim + ' 00:00'),
        };
        $scope.metaModal.definicoes = [];
        meta.definicoes.map(m => {
            $scope.metaModal.definicoes.push({
                valor: parseFloat(m.valor),
                bonificacao: parseFloat(m.bonificacao),
                tp_bonificacao: m.tp_bonificacao
            })
        })

        buildToggler('Create');
    }

    $scope.editEquipe = function (equipe = null) {
        $('#equipeModal').html('');
        if (equipe != null) {
            $scope.modalEquipe = {
                nm_equipe: equipe.nm_equipe,
                equipe: equipe.equipe,
                id_equipe: equipe.id_equipe,
            };
            var labs = [];
            equipe.funcionarios_array.map(eqp => {
                $('#equipeModal').append(`<option value = '${eqp.id}'>${eqp.text}</option>`);
                labs.push(eqp.id);
            })
            setTimeout(a => {
                $('#equipeModal').val(labs).change();
            }, 100);
        } else {
            $scope.modalEquipe = [];
            setTimeout(a => {
                $('#equipeModal').html('');
                $('#equipeModal').val('').change();
            }, 100);
        }
    }

}

CiuisCRM.controller('Goals_Controller', Goals_Controller);
