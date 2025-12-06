function Lead1_Controller($scope, $http, $mdSidenav, $filter, $mdDialog, fileUpload, $q) {
    "use strict";
    SCOPE = $scope;

    $scope.search_input = '123';
    $scope.file_ramo = [];
    $scope.filtrosL = [];
    $scope.viewFile = [];
    $scope.filtros = [];
    $scope.importar = [];
    $scope.camposAdmin = [];
    $scope.pages = [];
    $scope.importarTodos = false;

    $scope.apagar = [];
    $scope.apagarTodos = false;

    $scope.credito = 0;

    $scope.pageAtual = pageAtual;
    $scope.selecionadosAdmin = 0;
    $scope.report = [];
    $scope.report.totalLeadsGerados = '';

    $scope.filtrosL.nome_empresa = nome_empresa != '' ? [nome_empresa] : [];
    $scope.filtrosL.ramo = ramo != '' ? [ramo] : [];
    $scope.filtrosL.estado = estado != '' ? estado : "";
    $scope.filtrosL.cidade = cidade != '' ? cidade : "";
    $scope.filtrosL.porte = porte != '' ? porte : "";
    $scope.filtrosL.matrizEFilial = matrizEFilial != '' ? matrizEFilial : "";

    $scope.filtros.ramo = "";
    $scope.filtros.nome_empresa = "";
    $scope.filtros.estado = "";
    $scope.filtros.cidade = "";
    $scope.filtros.porte = "";
    $scope.filtros.faixa_capital = "";
    $scope.totalResultado = "0";

    $scope.filtro_ramo;
    $scope.todos_ramos2 = [];

    $scope.todos_ramos = [];
    $scope.todas_cidades = [];
    $scope.todos_nome_empresas = [];
    $scope.QtdSelecionados = 0;
    $scope.QtdSelecionadosManual = 0;
    $scope.selecionados = {
        ramos: ramo != '' ? [ramo] : [],
        cidades: cidade != '' ? [cidade] : [],
        nome_empresas: nome_empresa != '' ? [nome_empresa] : [],
    };


    $scope.loaderUpload = false;
    $scope.loader = false;
    $scope.etapaUpload = "";
    var deferred;
    var cancellerAdmin;

    $scope.leadssources = [{
        name: 'Lead One+',
        id: '1'
    }];

    $scope.company_list = {
        order: '',
        limit: 15,
        page: 1
    };

    $http.get(BASE_URL + 'api/staff').then(function (Staff) {
        $scope.staff = Staff.data;
    });

    $http.get(BASE_URL + 'leads/leadslist').then(function (data) {
        $scope.leadslist = data.data;
    });


    $http.get(BASE_URL + 'leads/leadslist').then(function (data) {
        $scope.leadslist = data.data;
    });


    window.mdSelectOnKeyDownOverride = function (event) {
        event.stopPropagation();
    };

    $http.get(BASE_URL + 'lead1/get_pacotes').then(function (data) {
        $scope.pacotes = data.data.data;

        if (data.data.creditos != null) {
            $scope.credito = parseFloat(data.data.creditos);
        } else {
            $scope.credito = 0;
        }

    });

    $scope.search_ramo = function (q) {

        if (q.length > 0) {
            $http.get(BASE_URL + 'api/search_customers/' + q).then(function (Customers) {
                $scope.all_customers = Customers.data;
            });
        } else {
            $scope.all_customers = [];
        }
    };



    $scope.get_lead1_backgroud = function (primeiraVez = false) {
        if (primeiraVez) {
            if ($scope.ramo_de_atividade != null) {
                return;
            }
        }
        $http.get(BASE_URL + 'lead1/get_lead1_backgroud').then(function (data) {
            $scope.ramo_de_atividade = data.data;
        });
    };

    $scope.get_nomes = function (search) {
        if (deferred) {
            deferred.resolve();
            $scope.todos_nome_empresas = [];
        }

        deferred = $q.defer();
        var url = BASE_URL + 'lead1/get_nomes?q=' + search;
        $http.get(url, { timeout: deferred.promise }).then(function (data) {
            $scope.todos_nome_empresas = data.data;
            deferred.resolve(data.data);
        });

        return deferred.promise;
    };



    $scope.get_ramos3 = function (search) {
        if (search.length >= 3) {
            if (deferred) {
                deferred.resolve();
                $scope.todos_ramos2 = [];
            }

            deferred = $q.defer();
            var url = BASE_URL + 'lead1/get_ramos?q=' + search;
            $http.get(url, { timeout: deferred.promise }).then(function (data) {
                $scope.todos_ramos2 = data.data;
                deferred.resolve(data.data);
            });

            return deferred.promise;
        }
    };

    var canceller = null;
    let requestId = 0; // usado para garantir que só a última resposta será usada

    $scope.get_ramos2 = function (search) {
        if (search.length >= 3) {
            if (canceller) {
                canceller.resolve();
            }
            $scope.carregandoResultado = true;
            canceller = $q.defer();
            const currentId = ++requestId;
            var url = BASE_URL + 'lead1/get_ramos?q=' + search;

            $http.get(url, { timeout: canceller.promise }).then(function (response) {
                if (requestId === currentId) {
                    $scope.todos_ramos = response.data;
                    $scope.carregandoResultado = false;
                }
            }, function (error) {
                if (error.status === -1) {
                    console.log('Requisição de get_ramos2 cancelada');
                    return;
                }
                $scope.carregandoResultado = false;
                globals.mdToast('error', 'Erro ao buscar ramos.');
            });

            return canceller.promise;
        }
    };

    $scope.get_cidade = function (search) {
        if (canceller) {
            canceller.resolve();
        }
        $scope.carregandoResultado = true;
        canceller = $q.defer();
        const currentId = ++requestId;
        var url = BASE_URL + 'lead1/get_cidades?q=' + search + '&uf=' + $scope.filtrosL.estado;

        $http.get(url, { timeout: canceller.promise }).then(function (response) {

            if (requestId === currentId) {
                $scope.todas_cidades = response.data;
                $scope.carregandoResultado = false;
            }
        }, function (error) {

            if (error.status === -1) {
                console.log('Requisição de get_cidade cancelada');
                return;
            }
            $scope.carregandoResultado = false;
            globals.mdToast('error', 'Erro ao buscar cidades.');
        });

        return canceller.promise;
    };

    $scope.getResultados = function () {
        if ($scope.filtrosL.nome_empresa?.text) {
            $scope.filtrosL.nome_empresa = $scope.filtrosL.nome_empresa.text;
        }
        if ($scope.filtrosL.ramo?.text) {
            $scope.filtrosL.ramo = $scope.filtrosL.ramo.text;
        }
        if ($scope.filtrosL.cidade?.text) {
            $scope.filtrosL.cidade = $scope.filtrosL.cidade.text;
        }
        if ($scope.filtrosL.porte?.text) {
            $scope.filtrosL.porte = $scope.filtrosL.porte.text;
        }

        var dataObj = $.param({
            nome_empresa: $scope.filtrosL.nome_empresa,
            ramo: $scope.filtrosL.ramo,
            estado: $scope.filtrosL.estado,
            cidade: $scope.filtrosL.cidade,
            porte: $scope.filtrosL.porte,
            matrizEFilial: $scope.filtrosL.matrizEFilial
        });

        $scope.loader = true;

        if (canceller) {
            canceller.resolve();
            console.log('Requisição anterior cancelada');
        }

        canceller = $q.defer();
        const currentId = ++requestId;

        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            },
            timeout: canceller.promise
        };

        $http.post(BASE_URL + 'lead1/get_companies', dataObj, config).then(function (data) {
            $scope.loader = false;

            if (requestId !== currentId) {
                console.log('Resposta antiga de getResultados descartada');
                return;
            }

            if (data.data) {
                $scope.limitOptions = [];
                for (var i = 15; i < data.data.r.length; i += 15) {
                    $scope.limitOptions.push(i);
                }
                $scope.limitOptions.push(data.data.r.length);

                $scope.companies = data.data.r;
                $scope.totalResultado = data.data.total;
                $scope.credito = data.data.creditos ? parseFloat(data.data.creditos) : 0;

                setTimeout(() => {
                    $('[data-src]').map((i, a) => {
                        $(a).attr('src', $(a).attr('data-src'));
                    });
                }, 2000);
            }
        }, function (erro) {
            $scope.loader = false;

            if (erro.status === -1) {
                console.log('Requisição de getResultados cancelada');
                return;
            }

            globals.mdToast('error', 'Erro ao fazer a consulta, tente novamente.');
        });
    };
    $scope.PesquisarEmpresa = function () {
        if ($scope.filtrosL.nome_empresa != null && $scope.filtrosL.nome_empresa.text != null) {
            $scope.filtrosL.nome_empresa = $scope.filtrosL.nome_empresa.text;
        }

        if ($scope.filtrosL.ramo != null && $scope.filtrosL.ramo.text != null) {
            $scope.filtrosL.ramo = $scope.filtrosL.ramo.text;
        }

        if ($scope.filtrosL.cidade != null && $scope.filtrosL.cidade.text != null) {
            $scope.filtrosL.cidade = $scope.filtrosL.cidade.text;
        }

        if ($scope.filtrosL.porte != null && $scope.filtrosL.porte.text != null) {
            $scope.filtrosL.porte = $scope.filtrosL.porte.text;
        }

        window.location.href = window.location.origin + '/lead1/pesquisa?ramo=' + $scope.filtrosL.ramo + '&estado=' + $scope.filtrosL.estado +
            '&cidade=' + $scope.filtrosL.cidade + '&porte=' + $scope.filtrosL.porte + '&matrizEFilial=' + $scope.filtrosL.porte + '&nome_empresa=' + $scope.filtrosL.nome_empresa;
    }







    $scope.getResultadosAdmin = function () {
        if ($scope.filtros.nome_empresa != null && $scope.filtros.nome_empresa.text != null) {
            $scope.filtros.nome_empresa = $scope.filtros.nome_empresa.text;
        }

        if ($scope.filtros.ramo != null && $scope.filtros.ramo.text != null) {
            $scope.filtros.ramo = $scope.filtros.ramo.text;
        }

        if ($scope.filtros.cidade != null && $scope.filtros.cidade.text != null) {
            $scope.filtros.cidade = $scope.filtros.cidade.text;
        }

        if ($scope.filtros.porte != null && $scope.filtros.porte.text != null) {
            $scope.filtros.porte = $scope.filtros.porte.text;
        }

        var dataObj = $.param({
            ramo: $scope.filtros.ramo,
            nome_empresa: $scope.filtros.nome_empresa,
            estado: $scope.filtros.estado,
            cidade: $scope.filtros.cidade,
            porte: $scope.filtros.porte,
            faixa_capital: $scope.filtros.faixa_capital,
            pageAtual: $scope.pageAtual
        });

        $scope.loader = true;

        $http.post(BASE_URL + 'lead1/get_companiesAdmin', dataObj, config).then(function (data) {
            $scope.loader = false;
            $scope.companiesAdmin = data.data;
            $scope.pages = $scope.range(parseInt($scope.pageAtual) - 3, parseInt($scope.pageAtual) + 3);
            setTimeout(a => {
                $('[data-src]').map((i, a) => {
                    $(a).attr('src', $(a).attr('data-src'))
                });
            }, 2000)
        });


    }
    $scope.carregaAdmin = function () {
        if ($scope.report.totalLeadsGerados == '') {
            $scope.getResultadosAdmin();
            $http.get(BASE_URL + 'lead1/get_totalLeadsGerados').then(function (data) {
                if (data.data != null) {
                    $scope.report.totalLeadsGerados = data.data.total;
                    $scope.camposAdmin.custoLead = parseFloat(data.data.settings.custoLead);
                    $scope.camposAdmin.custoEnriquecimento = parseFloat(data.data.settings.custoEnriquecimento);
                }
            });
        }
    }


    $scope.atualizaConta = function () {
        $http.get(BASE_URL + 'lead1/atualizaConta').then(function (data) {
            if (data.data != null) {
                $scope.faturas = data.data;
                $scope.totalGasto = 0;

                $.each($scope.faturas, (index, fatura) => {
                    $scope.totalGasto += parseFloat(fatura.valor);
                });
            }
        });
    }



    $scope.ImportCustomersNav = buildToggler('ImportCustomersNav');
    $scope.Import = buildToggler('Import');

    $scope.close = function () {
        $mdSidenav('ImportCustomersNav').close();
        $mdSidenav('Import').close();
    };

    function buildToggler(navID) {
        return function () {
            $mdSidenav(navID).toggle();
        };
    }

    $scope.range = function (min, max, step) {
        step = step || 1;
        var input = [];
        for (var i = min; i <= max; i += step) {
            input.push(i);
        }
        return input;
    };



    $scope.SalvarConfigs = function (page) {
        var dataObj = $.param({
            custoLead: $scope.camposAdmin.custoLead,
            custoEnriquecimento: $scope.camposAdmin.custoEnriquecimento,
        });

        $http.post(BASE_URL + 'lead1/SalvarConfigs', dataObj, config).then(function (data) {
            showToast('Sucesso!', 'salvo com SUCESSO!', 'success');
        });
    };

    $scope.setPage = function (page) {
        $scope.pageAtual = page;
        var dataObj = $.param({
            ramo: $scope.filtros.ramo,
            nome_empresa: $scope.filtros.nome_empresa,
            estado: $scope.filtros.estado,
            cidade: $scope.filtros.cidade,
            porte: $scope.filtros.porte,
            faixa_capital: $scope.filtros.faixa_capital,
            pageAtual: $scope.pageAtual
        });

        $http.post(BASE_URL + 'lead1/get_companiesAdmin', dataObj, config).then(function (data) {
            $scope.companiesAdmin = data.data;
            $scope.pages = $scope.range(parseInt($scope.pageAtual) - 3, parseInt($scope.pageAtual) + 3);
            setTimeout(a => {
                $('[data-src]').map((i, a) => {
                    $(a).attr('src', $(a).attr('data-src'))
                });
            }, 2000)
        });
    };

    $scope.apagarAdmin = function (todos = false) {
        var msg = 'Deseja mesmo remover?';
        var ids = '';
        if (todos) {
            msg = 'Deseja mesmo remover TODOS?'
            ids = '-1';
        } else {
            $scope.apagar.map((i, a) => {
                if (i == true) {
                    ids += (ids != '' ? '-' : '') + a;
                }
            });
        }

        globals.deleteDialog('Atenção!', msg, '1', 'Sim', 'Cancelar', 'lead1/remove/' + ids, function (response) {
            if (response.success == true) {
                var dataObj = $.param({
                    ramo: $scope.filtros.ramo,
                    nome_empresa: $scope.filtros.nome_empresa,
                    estado: $scope.filtros.estado,
                    cidade: $scope.filtros.cidade,
                    porte: $scope.filtros.porte,
                    faixa_capital: $scope.filtros.faixa_capital,
                    pageAtual: $scope.pageAtual
                });
                $scope.apagar = [];
                $scope.selecionadosAdmin = 0;
                $http.post(BASE_URL + 'lead1/get_companiesAdmin', dataObj, config).then(function (data) {
                    $scope.companiesAdmin = data.data;

                    setTimeout(a => {
                        $('[data-src]').map((i, a) => {
                            $(a).attr('src', $(a).attr('data-src'))
                        });
                    }, 2000)
                });
            } else {
                globals.mdToast('error', response.message);
            }
        });
    }

    $scope.selecionaTudoAdmin = function () {
        $scope.selecionadosAdmin = 0;
        $scope.apagar.map(a => {
            if (a == true && $scope.apagarTodos) {
                $scope.selecionadosAdmin++;
            }
        });
        $scope.companies.map((a, i) => {
            if ($scope.apagarTodos) {
                if ($scope.credito > $scope.selecionadosAdmin &&
                    ($scope.apagar[a.id_lead1] == null || !$scope.apagar[a.id_lead1])
                ) {
                    $scope.apagar[a.id_lead1] = $scope.apagarTodos;
                    $scope.selecionadosAdmin++;
                }
            } else {
                $scope.apagar[a.id_lead1] = $scope.apagarTodos;
            }
        });
    }

    $scope.selecionaMaisUmAdmin = function () {
        $scope.selecionadosAdmin = 0;
        $scope.apagar.map(a => {
            if (a == true) {
                $scope.selecionadosAdmin++;
            }
        });
    }




    $scope.SelecionaManual = function () {
        if ($scope.QtdSelecionadosManual == 0 || !$scope.QtdSelecionadosManual) {
            return;
        }

        if ($scope.QtdSelecionadosManual > $scope.credito) {
            $scope.QtdSelecionadosManual = $scope.credito;
        }

        if ($scope.QtdSelecionadosManual > $scope.companies.length) {
            $scope.QtdSelecionadosManual = $scope.companies.length;
        }

        if ($scope.QtdSelecionados < $scope.QtdSelecionadosManual) {
            $scope.companies.map((a, i) => {
                if ($scope.credito > $scope.QtdSelecionados &&
                    ($scope.importar[a.id_lead1] == null || !$scope.importar[a.id_lead1]) &&
                    $scope.QtdSelecionados < $scope.QtdSelecionadosManual
                ) {
                    $scope.importar[a.id_lead1] = true;
                    $scope.QtdSelecionados++;
                }
            });
        } else {
            $scope.companies.map((a, i) => {
                if ($scope.credito > $scope.QtdSelecionados &&
                    $scope.importar[a.id_lead1] != null &&
                    $scope.importar[a.id_lead1] &&
                    $scope.QtdSelecionados > $scope.QtdSelecionadosManual
                ) {
                    $scope.importar[a.id_lead1] = false;
                    $scope.QtdSelecionados--;
                }
            });
        }

    }

    $scope.selecionaTudo = function () {
        $scope.QtdSelecionados = 0;
        $scope.QtdSelecionadosManual = 0;

        $scope.importar.map(a => {
            if (a == true && $scope.importarTodos) {
                $scope.QtdSelecionados++;
                $scope.QtdSelecionadosManual++;
            }
        });
        $scope.companies.map((a, i) => {
            if ($scope.importarTodos) {
                if ($scope.credito > $scope.QtdSelecionados &&
                    ($scope.importar[a.id_lead1] == null || !$scope.importar[a.id_lead1])
                ) {
                    $scope.importar[a.id_lead1] = $scope.importarTodos;
                    $scope.QtdSelecionados++;
                    $scope.QtdSelecionadosManual++;
                }
            } else {
                $scope.importar[a.id_lead1] = $scope.importarTodos;
            }
        });
    }
    $scope.selecionaMaisUm = function (id) {
        $scope.QtdSelecionados = 0;
        $scope.QtdSelecionadosManual = 0;
        $scope.importar.map(a => {
            if (a == true) {
                $scope.QtdSelecionados++;
                $scope.QtdSelecionadosManual++;
            }
        });
        if ($scope.credito < $scope.QtdSelecionados) {
            $scope.importar[id] = false;
            $scope.QtdSelecionados--;
            $scope.QtdSelecionadosManual--;
            showToast('Atenção', 'Você não possui créditos suficiente!', 'dark');
        }
    }


    $scope.upload_ramo = function (i) {
        setTimeout(a => {
            console.log(i)
            var ramo = $scope.ramo_de_atividade[i];
            console.log(ramo)
            var nm_ramo = ramo.nm_ramo != null ? ramo.nm_ramo : ramo.text;

            var file = $scope.file_ramo[i];

            fileUpload.uploadFileToUrl(file, BASE_URL + 'lead1/upload_ramo?nm_ramo=' + nm_ramo, function (response) {
                if ((response.success == true) && (!response.errors)) {
                    showToast('Sucesso!', 'Upload feito com SUCESSO!', 'success');

                    $scope.get_lead1_backgroud(false);

                } else if ((response.success == false) && (response.errors)) {
                    globals.mdToast('error', response.message);
                } else {
                    globals.mdToast('error', response.error);
                }
            });
        }, 1000)

    }

    $scope.importCustomerRequest = function (iAtual, evt) {
        var fileArr = evt.target.result.split('\n');
        var titulo = fileArr[0].split(';');
        var dataArr = [];
        var limit = 700;
        var length = (limit * iAtual) <= Math.ceil(fileArr.length) ? (limit * iAtual) : Math.ceil(fileArr.length);
        $scope.etapaUpload = " Etapa " + iAtual + " de " + Math.ceil(fileArr.length / limit);

        for (var i = (iAtual > 1 ? (((iAtual - 1) * limit) + 1) : iAtual); i <= length; i++) {
            var linhaData = {};
            if (fileArr[i] != null) {
                var fileLine = fileArr[i].split(';');
                for (var j = 0; j < fileLine.length; j++) {
                    linhaData[titulo[j]] = fileLine[j].trim();
                }
                dataArr.push(linhaData);
            }
        }
        let dataObj = $.param({
            data: JSON.stringify(dataArr),
        });

        $http.post(BASE_URL + 'lead1/import_companies', dataObj, config).then(function (data) {
            if (data.data.success) {
                console.log(iAtual < Math.ceil(fileArr.length / limit), iAtual, Math.ceil(fileArr.length / limit));
                if (iAtual < Math.ceil(fileArr.length / limit)) {
                    $scope.importCustomerRequest((iAtual + 1), evt);
                } else {
                    $scope.loaderUpload = false;
                    $scope.etapaUpload = "Finalizada!";
                    showToast('Atenção', "Importado com sucesso!", ' success');

                }
            } else {
                $scope.importCustomerRequest(iAtual, evt);
            }
        }, erro => {
            $scope.importCustomerRequest(iAtual, evt);
        });
    }

    $scope.importCustomer = function () {

        var leitorDeCSV = new FileReader();
        leitorDeCSV.readAsText($("#chooseFileImportLead")[0].files[0]);
        $scope.loaderUpload = true;

        leitorDeCSV.onload = function (evt) {
            console.log('começa')
            $scope.importCustomerRequest(1, evt);
            $("#chooseFileImportLead").val('');
        }



        return;
        $scope.importing = true;
        var file = $scope.customer_file;
        var uploadUrl = BASE_URL + 'lead1/import_companies';
        fileUpload.uploadFileToUrl(file, uploadUrl, function (response) {
            if ((response.success == true) && (!response.errors)) {
                globals.mdToast('success', response.message);
                $mdSidenav('ImportCustomersNav').close();
            } else if ((response.success == false) && (response.errors)) {
                $scope.importerror = true;
                $scope.errors = response.errors;
                console.log(response.errors);
                globals.mdToast('error', response.message);
            } else {
                $scope.importerror = true;
                $scope.errors = response.errors;
                globals.mdToast('error', response.message);
                console.log(response.errors);
            }

            $scope.customerFiles = true;
            $scope.importing = false;
        });
    };

    $scope.abreEmpresa = function (id_lead1) {
        window.open(
            BASE_URL + 'lead1/view/' + id_lead1,
            '_blank'
        );
    }

    $scope.update = function (company) {
        $scope.company_update = company;
        $mdSidenav('Update_company').toggle();
    }


    $scope.ExportCsvLeads = function () {

        var leads1 = [];
        $scope.importar.map((a, i) => {
            if (a) {
                leads1.push(i);
            }
        });

        let dataObj = $.param({
            leads1,
        });

        window.open(BASE_URL + 'lead1/ExportCsvLeads?' + dataObj, '_blank')
        showToast('Atenção', "Exportado com sucesso!", ' success');
        $mdSidenav('Import').close();

    }


    $scope.ImportarLeads = function () {


        if ($scope.importassigned == null) {
            showToast('Atenção', "Informe um funcionário!", '');
            return;
        }
        if ($scope.importstatus == null) {
            showToast('Atenção', "Informe um status!", '');
            return;
        }
        if ($scope.importsource == null) {
            showToast('Atenção', "Informe uma origem!", '');
            return;
        }
        $scope.loadingLeads = true;

        /* var confirm = $mdDialog.confirm()
             .title('Atenção')
             .textContent('Você quer fazer o super enriquecimento de dados destes leads? Com ele você terá acesso a  e-mails, telefones, endereços, quadros societário e muito mais informações sobre as empresas.o super enriquecimento de dados tem um custo de R$ 2,89 por empresa que será cobrado na sua próxima fatura!')
             .ariaLabel('Atenção')
             .ok('Aceitar')
             .cancel('Cancelar');
 
         $mdDialog.show(confirm).then(function () {
             */

        var leads1 = [];
        $scope.importar.map((a, i) => {
            if (a) {
                leads1.push(i);
            }
        });

        let dataObj = $.param({
            leads1,
            importassigned: $scope.importassigned,
            importstatus: $scope.importstatus,
            enriquecer: $scope.enriquecer
        });

        $http.post(BASE_URL + 'lead1/ImportarLeads', dataObj, config).then(function (data) {
            $scope.loadingLeads = false;
            if (data.data.success == 200) {
                showToast('Atenção', "Importado com sucesso!", ' success');
                $scope.credito = data.data.credito;
                $mdSidenav('Import').close();
            }
        }, (a) => {
            $scope.loadingLeads = false;
        });

        //  });



    }


    $scope.saveCompany = function () {
        $scope.saving = true;
        let company = $scope.company_update;
        let dataObj = $.param(company);

        $http.post(BASE_URL + 'lead1/save_company', dataObj, config).then(function (data) {
            if (data.data.success == 200) {
                $mdSidenav('Update_company').close();
                showToast('Atenção', "Alterado com sucesso!", ' success');
            }
            $scope.saving = false;
        });
    }


    $scope.delete = function (id_lead1) {
        if (confirm('Você tem certeza que deseja excluir essa empresa?')) {
            let dataObj = $.param({
                id_lead1
            });
            $http.post(BASE_URL + 'lead1/delete_company', dataObj, config).then(function (data) {
                if (data.data.success == 200) {
                    $mdSidenav('Update_company').close();
                    showToast('Atenção', "Removido com sucesso!", ' success');

                    var dataObj = $.param({
                        ramo: $scope.filtros.ramo,
                        nome_empresa: $scope.filtros.nome_empresa,
                        estado: $scope.filtros.estado,
                        cidade: $scope.filtros.cidade,
                        porte: $scope.filtros.porte,
                        faixa_capital: $scope.filtros.faixa_capital,
                        pageAtual: $scope.pageAtual
                    });

                    $http.post(BASE_URL + 'lead1/get_companiesAdmin', dataObj, config).then(function (data) {
                        $scope.companiesAdmin = data.data;

                        setTimeout(a => {
                            $('[data-src]').map((i, a) => {
                                $(a).attr('src', $(a).attr('data-src'))
                            });
                        }, 2000)
                    });
                }
            });
        }
    }

    $scope.editPacote = function (pacote = null) {
        $('#pacoteModal').modal('show');
        if (pacote != null) {
            $scope.modalPacote = {
                nm_pacote: pacote.nm_pacote,
                detalhes: pacote.detalhes,
                quantidade: parseFloat(pacote.quantidade),
                valor: parseFloat(parseFloat(pacote.valor).toFixed(2)),
                id_pacote: pacote.id_pacote,
            };
        } else {
            $scope.modalPacote = [];
        }
    }

    $scope.salva_pacote = function () {
        let dataObj = $.param({
            nm_pacote: $scope.modalPacote.nm_pacote,
            quantidade: $scope.modalPacote.quanitdade,
            valor: $scope.modalPacote.valor,
            id_pacote: $scope.modalPacote.id_pacote,
            detalhes: $scope.modalPacote.detalhes,
        });

        $http.post(BASE_URL + 'lead1/salva_pacote', dataObj, config).then(function (data) {
            if (data.data.success == 200) {
                $('#pacoteModal').modal('hide');
                showToast('Atenção', "Alterado com sucesso!", ' success');
                $http.post(BASE_URL + 'lead1/get_pacotes', dataObj, config).then(function (data) {
                    $scope.pacotes = data.data;
                });
            }
        });
    }

    $scope.removePacote = function (id_pacote) {
        if (confirm('Você tem certeza que deseja excluir esse pacote?')) {
            let dataObj = $.param({
                id_pacote
            });
            $http.post(BASE_URL + 'lead1/delete_pacote', dataObj, config).then(function (data) {
                if (data.data.success == 200) {
                    $mdSidenav('Update_company').close();
                    showToast('Atenção', "Removido com sucesso!", ' success');

                    $http.post(BASE_URL + 'lead1/get_pacotes', dataObj, config).then(function (data) {
                        $scope.pacotes = data.data;
                    });
                }
            });
        }
    }

    $scope.adicionarEmpresa = function () {
        $('#listPacotesModal').modal('show');
    }

}

function Lead1View_Controller($scope, $http, $mdSidenav, $filter, $mdDialog, fileUpload, $q) {
    "use strict";

    let dataObj = $.param({
        id_lead1: IDLEAD1
    });
    $http.post(BASE_URL + 'lead1/get_company', dataObj, config).then(function (data) {
        if (data.data != null) {
            $scope.lead1 = data.data;
            $scope.lead1.aberturaBr = moment(new Date($scope.lead1.abertura)).format("DD/MM/YYYY");
            let cnpj = $scope.lead1.cnpj.toString();
            $scope.lead1.cnpj = cnpj.substr(0, 2) + '.' + cnpj.substr(2, 3) + '.' + cnpj.substr(5, 3) + '/' + cnpj.substr(8, 4) + '-' + cnpj.substr(12, 2);
        }
    });

}
CiuisCRM.controller('Lead1_Controller', Lead1_Controller);
CiuisCRM.controller('Lead1View_Controller', Lead1View_Controller);