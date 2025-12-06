function Dash_inteligence($scope, $http) {
    "use strict";
    var graphNvOportMes = null;
    var perfReuniaoMensal = null;
    var graphNovosClientes = null;
    var leadsxReunioes = null;
    var reunioesxClientes = null;
    var LeasxReunioesPorOrigem = null;
    var ClientesxReunioesPorOrigem = null;
    var reunioesPorFuncionarios = null;
    var reunioesAgendadas = null;
    
    var ConversoesPorFuncionario = null;
    var graphLeadsAcionados = null;
    // var graphReunioes = null;
    var graphReunioesRealizadas = null;
    var reunioesxConversoes = null;
    var ConversoesPorCloser = null;
    var reuniaoAgendadaxRealizada = null;
    var percetReuniaorealizadas = null;

    var graphQuantidadeLeads = null;
    var graphReunioesRealizadasFunil = null;
    var graphConversoes = null;


    var data = new Date();
    $scope.filtros = [];
    $scope.customPanel = [];
    $scope.filtros.dt_de = new Date(data.getFullYear() + '-' + (data.getMonth() + 1) + '-01 00:00');
    $scope.filtros.dt_ate = new Date(data.getFullYear() + '-' + (data.getMonth() + 1) + '-' + (data.getDate() < 10 ? '0' + data.getDate() : data.getDate()) + ' 00:00');

    if (localStorage.customInteligence != null) {
        $scope.customPanel = JSON.parse(localStorage.customInteligence);
    } else {
        $scope.customPanel = {
            "totalLeadsGerados": true, "totalReunioes": true, "totalReversao": true,
            "graphNvOportMes": true, "perfReuniaoMensal": true, "graphNovosClientes": true
        };
    }

    $scope.getRelatorioChat = function () {
       
        var dataObj = $.param({
            dt_de: $scope.filtros.dt_de != null ? moment($scope.filtros.dt_de).format("YYYY-MM-DD") : null,
            dt_ate: $scope.filtros.dt_ate != null ? moment($scope.filtros.dt_ate).format("YYYY-MM-DD") : null,
            flt_funil: $scope.filtros.flt_funil,
            flt_funcionario: $scope.filtros.flt_funcionario,
            flt_origem: $scope.filtros.flt_origem,
        })

        $http.post(BASE_URL + 'dash_inteligence/getRelatorioChat', dataObj, config).then(function (data) {
            if (data.data != null) {
                $('#modalCustom').modal('show');
                $scope.relatorio_html = data.data;
            }
        });
    }
    
    $scope.openModalCustom = function () {
        $('#modalCustom').modal('show');
    }

    $http.get(BASE_URL + 'leads/leadslist/0').then(function (data) {
        $scope.leadslist = data.data;
    });

    $http.get(BASE_URL + 'api/staff/0').then(function (Staff) {
        $scope.staff = Staff.data;
    });

    $http.get(BASE_URL + 'leads/leadsources/0').then(function (LeadSources) {
        $scope.leadssources = LeadSources.data;
    });

    $scope.getResultados = function () {
        $scope.report = [];

        var dataObj = $.param({
            dt_de: $scope.filtros.dt_de != null ? moment($scope.filtros.dt_de).format("YYYY-MM-DD") : null,
            dt_ate: $scope.filtros.dt_ate != null ? moment($scope.filtros.dt_ate).format("YYYY-MM-DD") : null,
            flt_funil: $scope.filtros.flt_funil,
            flt_funcionario: $scope.filtros.flt_funcionario,
            flt_origem: $scope.filtros.flt_origem,
            id_company: CONST_idCompany != null ? CONST_idCompany : $scope.filtros.id_company
        })

        $http.post(BASE_URL + 'dash_inteligence/getTotaisPainel', dataObj, config).then(function (data) {
            if (data.data != null) {
                $scope.totalDash = data.data;

                $scope.totalDash.totalReversao = (100 * parseFloat(data.data.totalReunioes)) / (parseFloat(data.data.totalLeadsGerados) > 0 ? parseFloat(data.data.totalLeadsGerados) : 1);
                $scope.totalDash.totalConversao = (100 * parseFloat(data.data.totalClientes)) / (parseFloat(data.data.totalReunioes) > 0 ? parseFloat(data.data.totalReunioes) : 1);
            }
        });

        $http.post(BASE_URL + 'dash_inteligence/getReports_QuantidadeLeads', dataObj, config).then(function (data3) {
            if (data3.data != null) {
                if (graphQuantidadeLeads == null) {
                    graphQuantidadeLeads = new Chart($('#graphQuantidadeLeads'), {
                        type: 'bar',
                        data: data3.data,
                        options: options3
                    });
                } else {
                    graphQuantidadeLeads.data = data3.data;
                    graphQuantidadeLeads.update();
                }
            }
        });

        $http.post(BASE_URL + 'dash_inteligence/getReports_Conversoes', dataObj, config).then(function (data3) {
            if (data3.data != null) {
                if (graphConversoes == null) {
                    graphConversoes = new Chart($('#graphConversoes'), {
                        type: 'bar',
                        data: data3.data,
                        options: options3
                    });
                } else {
                    graphConversoes.data = data3.data;
                    graphConversoes.update();
                }
            }
        });

        

        $http.post(BASE_URL + 'dash_inteligence/getReports_graphReunioesRealizadasFunil', dataObj, config).then(function (data3) {
            if (data3.data != null) {
                if (graphReunioesRealizadasFunil == null) {
                    graphReunioesRealizadasFunil = new Chart($('#graphReunioesRealizadasFunil'), {
                        type: 'bar',
                        data: data3.data,
                        options: options3
                    });
                } else {
                    graphReunioesRealizadasFunil.data = data3.data;
                    graphReunioesRealizadasFunil.update();
                }
            }
        });

        $http.post(BASE_URL + 'dash_inteligence/getReports_reunioesRealizadas', dataObj, config).then(function (data) {
            if (data.data != null) {
                if (graphReunioesRealizadas == null) {
                    graphReunioesRealizadas = new Chart($('#graphReunioesRealizadas'), {
                        type: 'bar',
                        data: data.data,
                        options: options3
                    });
                } else {
                    graphReunioesRealizadas.data = data.data;
                    graphReunioesRealizadas.update();
                }
            }
        });



        $http.post(BASE_URL + 'dash_inteligence/getPerfReuniaoMensal', dataObj, config).then(function (data2) {
            if (data2.data != null) {
                if (perfReuniaoMensal == null) {
                    perfReuniaoMensal = new Chart($('#perfReuniaoMensal'), {
                        type: 'bar',
                        data: data2.data,
                        options: options3
                    });
                } else {
                    perfReuniaoMensal.data = data2.data;
                    perfReuniaoMensal.update();
                }
            }

            $http.post(BASE_URL + 'panel/getReports_nvOportunidade_mes', dataObj, config).then(function (data) {
                if (data.data != null) {
                    if (graphNvOportMes == null) {
                        graphNvOportMes = new Chart($('#graphNvOportMes'), {
                            type: 'bar',
                            data: data.data,
                            options: options2
                        });
                    } else {
                        graphNvOportMes.data = data.data;
                        graphNvOportMes.update();
                    }
                }

                var dataTemp = JSON.parse(JSON.stringify(data2.data));
                var dataData = [];

                console.log(dataTemp)
                console.log(data.data)

                dataTemp.datasets[0].data.map((a, i) => {
                    console.log(a)
                    var l = parseFloat(data.data.datasets[0].data[i]);
                    var t = ((100 * parseFloat(a)) / (l > 0 ? l : 1)).toFixed(1);
                    dataData.push(t);
                });

                dataTemp.datasets[0].data = dataData;
                dataTemp.datasets[0].label = "%";


                if (leadsxReunioes == null) {

                    leadsxReunioes = new Chart($('#leadsxReunioes'), {
                        type: 'bar',
                        data: dataTemp,
                        options: options3
                    });
                } else {
    
                    leadsxReunioes.data = dataTemp;
                    leadsxReunioes.update();
                }

            });

            $http.post(BASE_URL + 'customersDash/getReports_graphNovosClientes', dataObj, config).then(function (data3) {
                if (data3.data != null) {
                    if (graphNovosClientes == null) {
                        graphNovosClientes = new Chart($('#graphNovosClientes'), {
                            type: 'bar',
                            data: data3.data,
                            options: options3
                        });
                    } else {
                        graphNovosClientes.data = data3.data;
                        graphNovosClientes.update();
                    }
                }

                if (reunioesxClientes == null) {
                    var dataTemp = JSON.parse(JSON.stringify(data3.data));
                    var data = [];

                    console.log(dataTemp);
                    console.log(data2.data);

                    dataTemp.datasets[0].data.map((a, i) => {
                        var l = parseFloat(data2.data.datasets[0].data[i]);
                        var t = ((100 * parseFloat(a)) / (l > 0 ? l : 1)).toFixed(1);
                        data.push(t);
                    });

                    dataTemp.datasets[0].data = data;
                    dataTemp.datasets[0].label = "%";

                    reunioesxClientes = new Chart($('#reunioesxClientes'), {
                        type: 'bar',
                        data: dataTemp,
                        options: options3
                    });
                } else {
                    var dataTemp = JSON.parse(JSON.stringify(data3.data));
                    var data = [];

                    dataTemp.datasets[0].data.map((a, i) => {
                        var l = parseFloat(data2.data.datasets[0].data[i]);
                        var t = ((100 * parseFloat(a)) / (l > 0 ? l : 1)).toFixed(1);
                        data.push(t);
                    });

                    dataTemp.datasets[0].data = data;
                    dataTemp.datasets[0].label = "%";

                    reunioesxClientes.data = dataTemp;
                    reunioesxClientes.update();
                }
            });
        });

        $http.post(BASE_URL + 'dash_inteligence/getReports_LeasxReunioesPorOrigem', dataObj, config).then(function (data3) {
            if (data3.data != null) {
                if (LeasxReunioesPorOrigem == null) {
                    LeasxReunioesPorOrigem = new Chart($('#LeasxReunioesPorOrigem'), {
                        type: 'bar',
                        data: data3.data,
                        options: options3
                    });
                } else {
                    LeasxReunioesPorOrigem.data = data3.data;
                    LeasxReunioesPorOrigem.update();
                }
            }
        });

        $http.post(BASE_URL + 'dash_inteligence/getReports_ClientesxReunioesPorOrigem', dataObj, config).then(function (data3) {
            if (data3.data != null) {
                if (ClientesxReunioesPorOrigem == null) {
                    ClientesxReunioesPorOrigem = new Chart($('#ClientesxReunioesPorOrigem'), {
                        type: 'bar',
                        data: data3.data,
                        options: options3
                    });
                } else {
                    ClientesxReunioesPorOrigem.data = data3.data;
                    ClientesxReunioesPorOrigem.update();
                }
            }
        });



        $http.post(BASE_URL + 'dash_inteligence/getReports_reunioesxConversoes', dataObj, config).then(function (data3) {
            if (data3.data != null) {
                if (reunioesxConversoes == null) {
                    reunioesxConversoes = new Chart($('#reunioesxConversoes'), {
                        type: 'bar',
                        data: data3.data,
                        options: options3
                    });
                } else {
                    reunioesxConversoes.data = data3.data;
                    reunioesxConversoes.update();
                }
            }
        });

        $http.post(BASE_URL + 'dash_inteligence/getReports_ConversoesPorCloser', dataObj, config).then(function (data3) {
            if (data3.data != null) {
                if (ConversoesPorCloser == null) {
                    ConversoesPorCloser = new Chart($('#ConversoesPorCloser'), {
                        type: 'bar',
                        data: data3.data,
                        options: options3
                    });
                } else {
                    ConversoesPorCloser.data = data3.data;
                    ConversoesPorCloser.update();
                }
            }
        });

        $http.post(BASE_URL + 'dash_inteligence/getReports_reuniaoAgendadaxRealizada', dataObj, config).then(function (data3) {
            if (data3.data != null) {
                if (reuniaoAgendadaxRealizada == null) {
                    reuniaoAgendadaxRealizada = new Chart($('#reuniaoAgendadaxRealizada'), {
                        type: 'bar',
                        data: data3.data,
                        options: options3
                    });
                } else {
                    reuniaoAgendadaxRealizada.data = data3.data;
                    reuniaoAgendadaxRealizada.update();
                }


                var dataTemp = JSON.parse(JSON.stringify(data3.data));
                var dataData = [];

                dataTemp.datasets[0].data.map((a, i) => {
                    var l = parseFloat(dataTemp.datasets[1].data[i]);
                    var a = parseFloat(a);
                    console.log(l, a)
                    var t = ((100 * parseFloat(l)) / ((l + a) > 0 ? (l + a) : 1)).toFixed(1);
                    dataData.push(t);
                });

                dataTemp.datasets[0].data = dataData;
                dataTemp.datasets[0].label = "%";

                dataTemp.datasets.splice(1, 1);


                if (percetReuniaorealizadas == null) {

                    percetReuniaorealizadas = new Chart($('#percetReuniaorealizadas'), {
                        type: 'bar',
                        data: dataTemp,
                        options: options3
                    });
                } else {
                  
                    percetReuniaorealizadas.data = dataTemp;
                    percetReuniaorealizadas.update();
                }

            }
        });

        $http.post(BASE_URL + 'dash_inteligence/getReports_ConversoesPorFuncionario', dataObj, config).then(function (data3) {
            if (data3.data != null) {
                if (ConversoesPorFuncionario == null) {
                    ConversoesPorFuncionario = new Chart($('#ConversoesPorFuncionario'), {
                        type: 'bar',
                        data: data3.data,
                        options: options3
                    });
                } else {
                    ConversoesPorFuncionario.data = data3.data;
                    ConversoesPorFuncionario.update();
                }
            }
        });

        $http.post(BASE_URL + 'dash_inteligence/getReports_graphLeadsAcionados', dataObj, config).then(function (data3) {
            if (data3.data != null) {
                if (graphLeadsAcionados == null) {
                    graphLeadsAcionados = new Chart($('#graphLeadsAcionados'), {
                        type: 'bar',
                        data: data3.data,
                        options: options3
                    });
                } else {
                    graphLeadsAcionados.data = data3.data;
                    graphLeadsAcionados.update();
                }
            }
        });

        $http.post(BASE_URL + 'dash_inteligence/getReports_reunioesPorFuncionarios', dataObj, config).then(function (data3) {
            if (data3.data != null) {
                if (reunioesPorFuncionarios == null) {
                    reunioesPorFuncionarios = new Chart($('#reunioesPorFuncionarios'), {
                        type: 'bar',
                        data: data3.data,
                        options: options3
                    });
                } else {
                    reunioesPorFuncionarios.data = data3.data;
                    reunioesPorFuncionarios.update();
                }
            }
        });

        $http.post(BASE_URL + 'dash_inteligence/getReports_reunioesAgendadas', dataObj, config).then(function (data3) {
            if (data3.data != null) {
                if (reunioesAgendadas == null) {
                    reunioesAgendadas = new Chart($('#reunioesAgendadas'), {
                        type: 'bar',
                        data: data3.data,
                        options: options3
                    });
                } else {
                    reunioesAgendadas.data = data3.data;
                    reunioesAgendadas.update();
                }
            }
        });


    };

    $scope.getResultados();

    $scope.changeSettings = function (tipo = null) {
        localStorage.customInteligence = JSON.stringify($scope.customPanel);
    }
}

CiuisCRM.controller('Dash_inteligence', Dash_inteligence);
