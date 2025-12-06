function Companies_Controller($scope, $http, $mdSidenav, $filter, $mdDialog, fileUpload, $q) {
    "use strict";

    var id_company = 0;
    $scope.companiesLoader = true;
    $scope.isContact = true;
    $scope.permissionModalLoader = false;
    $scope.permissionModal = [];
    $scope.permissionModal_export = [];
    $scope.companies = [];
    $scope.permissions_all = [];
    $scope.limitOptions = [5, 10, 15, 20];
    $scope.companies_list = {
        order: '',
        limit: 5,
        page: 1
    };

    globals.get_countries();

    $scope.inArray = function (needle, haystack) {
        var length = haystack.length;
        for (var i = 0; i < length; i++) {
            if (haystack[i] == needle) return true;
        }
        return false;
    }

    $scope.openModalReports = function (id) {
        $("#modalReport" + id + " .select2").select2({
            dropdownParent: $('#modalReport' + id),
            tags: true,
            tokenSeparators: [',', ' ']
        });
        $('#modalReport' + id).modal('show');
    }

    $scope.Create = function () {
        $scope.company = [];
        $scope.company.date_contacted = moment(new Date()).format("YYYY-MM-DD HH:mm");
        $scope.company.country_id = 32;

        buildToggler('Create');
    }
    $scope.Update = function (company) {
        console.log(company)
        $scope.company = company;
        $scope.company.vencimento = company.vencimento != null ? new Date(company.vencimento) : null;
        $scope.company.date_contacted = company.date_contacted != null ? new Date(company.date_contacted) : new Date(company.created);
        $scope.company.creditos = parseFloat(company.creditos);
        $scope.company.creditos_enriquecimento = parseFloat(company.creditos_enriquecimento);
        $scope.company.renovar_valor = parseFloat(company.renovar_valor);

        buildToggler('Create');
    }

    function buildToggler(navID) {
        $mdSidenav(navID).toggle();
    }

    $scope.get_companies = function () {
        $http.get(BASE_URL + 'companies/get_companies').then(function (Data) {
            $scope.companies = Data.data;

            $scope.companies.map(c => {
                if (c.modalReport != null) {
                    c.modalReport.data = new Date(c.modalReport.data);
                }

            })
            $scope.companiesLoader = false;
        });
    }
    $scope.get_companies();

    $http.get(BASE_URL + 'emails/get_email_templates').then(function (Templates) {
        $scope.templates = Templates.data;
    });

    $scope.Remove = function (id) {
        globals.deleteDialog(lang.attention, "Você tem certeza que deseja deletar essa empresa?", id, lang.doIt, lang.cancel,
            'companies/remove/' + id, function (response) {
                if (response.success == true) {
                    globals.mdToast('success', 'Removido com sucesso');
                    $scope.get_companies();
                } else {
                    globals.mdToast('error', response.message);
                }
            });
    }
    $scope.close = function () {
        $mdSidenav('Create').close();
        $mdSidenav('Update').close();
        $mdSidenav('Permissions').close();
        $mdDialog.hide();
    }


    $scope.AddCompany = function () {

        $scope.company.status = $scope.company.ativo ? '1' : '0';

        if ($scope.company.date_contacted) {
            $scope.company.date_contacted = moment($scope.company.date_contacted).format("YYYY-MM-DD HH:mm:ss");
        }
        if ($scope.company.dt_nascimento) {
            $scope.company.dt_nascimento = moment($scope.company.dt_nascimento).format("YYYY-MM-DD");
        }

        if ($scope.company.tp_pessoa == "2") {
            if ($scope.company.nm_company.length == 0) {
                globals.mdToast('error', "Informe o nome da empresa!");
                return;
            }
        } else if ($scope.company.tp_pessoa == "1") {
            if ($scope.company.name.length == 0) {
                globals.mdToast('error', "Informe o nome!");
                return;
            }
        }


        var dataObj = $.param({
            tp_pessoa: $scope.company.tp_pessoa,
            date_contacted: $scope.company.date_contacted,
            name: $scope.company.name,
            nm_company: $scope.company.nm_company,
            source_id: $scope.company.source_id,
            phone: $scope.company.phone,
            email: $scope.company.email,
            web_site: $scope.company.web_site,
            country_id: $scope.company.country_id,
            state_id: $scope.company.state_id,
            city: $scope.company.city,
            zip: $scope.company.zip,
            address: $scope.company.address,
            description: $scope.company.description,

            cnpj: $scope.company.cnpj,
            web_site: $scope.company.web_site,
            cpf: $scope.company.cpf,
            setor_atividade: $scope.company.setor_atividade,
            dt_nascimento: $scope.company.dt_nascimento,
            porte: $scope.company.porte,
            instagram: $scope.company.instagram,
            facebook: $scope.company.facebook,
            linkedin: $scope.company.linkedin,

            id_company: $scope.company.id_company,
            vencimento: moment($scope.company.vencimento).format("YYYY-MM-DD"),
            creditos: $scope.company.creditos,
            creditos_enriquecimento: $scope.company.creditos_enriquecimento,

            renovar: $scope.company.renovar,
            credito_acumulativo: $scope.company.credito_acumulativo,
            renovar_valor: $scope.company.renovar_valor,

            status: $scope.company.ativo ? '1' : '0',

        });


        $http.post(BASE_URL + 'companies/create', dataObj, config).then(
            function (response) {
                if (response.data.success == true) {
                    $mdSidenav('Create').close();
                    globals.mdToast('success', response.data.message);
                    //$scope.get_companies();
                } else {
                    globals.mdToast('error', response.data.message);
                }
            }, (response) => {

            }
        );
    };


    $scope.getStates = function (country) {
        $http.get(BASE_URL + 'api/get_states/' + country).then(function (States) {
            $scope.states = States.data;
        });
    };

    $http.get(BASE_URL + 'leads/leadsources').then(function (LeadSources) {
        $scope.leadssources = LeadSources.data;
    });


    $scope.get_Permissions = function (id) {
        $http.get(BASE_URL + 'companies/get_permission/' + id).then(function (data) {
            $scope.permissions_all = data.data;
            $scope.permissionModalLoader = false;
        });

        $http.get(BASE_URL + 'companies/get_permission_export/' + id).then(function (data) {
            $scope.permissions_all_export = data.data;
        });
    };

    $scope.Permissions = function (id) {
        $scope.permissions_all = [];
        $scope.permissionModalLoader = true;
        $scope.permissionModal = [];
        $scope.permissionModal_export = [];

        $scope.permissionModal.permission = [];

        id_company = id;

        $scope.get_Permissions(id);
        buildToggler('Permissions');
    };

    $scope.selectAllModal2 = function (company) {
        company.modalReport.permission.permission = [];
        company.permissoes.map((permission) => {
            if ($scope.inArray(permission.id, [24, 25, 26])) {
                company.modalReport.permission.permission[permission.id] = company.modalReport.permission.permission_all;
            }
        })
    };

    $scope.saveModalReport = function (company) {
        var dataObj = $.param({
            status: company.modalReport.status,
            frequencia: company.modalReport.frequencia,
            data: moment(company.modalReport.data).format("YYYY-MM-DD"),
            emails: company.modalReport.emails,
            permissions: company.modalReport.permission != null ? company.modalReport.permission.permission : [],
            modelo_de_email: company.modalReport.modelo_de_email,
            id_company: company.id_company
        });

        $http.post(BASE_URL + 'companies/SaveReport', dataObj, config).then(
            function (response) {
                if (response.data.success == 200) {
                    $('.modal').modal('hide');
                    globals.mdToast('success', response.data.message);
                } else {
                    globals.mdToast('error', response.data.message);
                }
            }, (response) => {

            }
        );
    }



    $scope.changeE = function (company) {
        console.log(company.modalReport.emails);
    };

    $scope.selectAll = function () {
        $scope.permissions_all.map((permission) => {
            $scope.permissionModal.permission[permission.id] = $scope.permissionModal.permission_all;
        })
    };

    $scope.AddPermission = function () {

        var dataObj = $.param({
            permissions: $scope.permissionModal.permission,
            permissions_export: $scope.permissionModal_export.permission,
            id_company: id_company,
        });

        $http.post(BASE_URL + 'companies/AddPermission', dataObj, config).then(
            function (response) {
                if (response.data.success == true) {
                    $mdSidenav('Permissions').close();
                    globals.mdToast('success', response.data.message);
                } else {
                    globals.mdToast('error', response.data.message);
                }
            }, (response) => {

            }
        );
    };
}


function Company_Controller($scope, $http, $mdSidenav, $filter, $mdDialog, fileUpload, $q) {
    "use strict";
    $scope.companiesLoader = true;
    $scope.saving = false;

    $scope.limitOptions = [5, 10, 15, 20];
    $scope.companies_list = {
        order: '',
        limit: 5,
        page: 1
    };

    $scope.get_company = function () {
        $http.get(BASE_URL + 'companies/get_company/' + COMPANYID).then(function (Data) {
            $scope.company = Data.data;
            $scope.companiesLoader = false;
        });
    }
    $scope.get_company();

    $scope.get_usuarios = function () {
        $http.get(BASE_URL + 'companies/get_usuarios/' + COMPANYID).then(function (Data) {
            $scope.usuarios = Data.data;
        });
    }
    $scope.get_usuarios();


    $http.get(BASE_URL + 'companies/get_companies').then(function (Data) {
        $scope.companies = Data.data;
    });

    $http.get(BASE_URL + 'api/departments').then(function (Departments) {
        $scope.departments = Departments.data;
    });
    $http.get(BASE_URL + 'api/languages').then(function (Languages) {
        $scope.languages = Languages.data;
    });
    $http.get(BASE_URL + 'settings/get_roles/' + COMPANYID).then(function (Roles) {
        $scope.roles = Roles.data;
    });
    $http.get(BASE_URL + 'api/timezones').then(function (Timezones) {
        $scope.timezones = Timezones.data;
    });


    $scope.Create = function (staff = []) {
        $scope.staff = staff;
        $scope.staff.password = '';
        $scope.staff.language = "portuguese_br";
        buildToggler('Create');
    }


    function buildToggler(navID) {
        $mdSidenav(navID).toggle();
    }

    $scope.AddStaff = function () {
        $scope.saving = true;
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

        var file = document.querySelector('#fotoStaff').files[0];
        getBase64(file).then((staffavatar) => {
            var dataObj = $.param({
                name: $scope.staff.staffname,
                email: $scope.staff.email,
                phone: $scope.staff.phone,
                department: $scope.staff.department_id,
                language: $scope.staff.language,
                address: $scope.staff.address,
                password: $scope.staff.password,
                timezone: $scope.staff_timezone,
                custom_fields: $scope.tempArr,
                role: $scope.staff.role_id,
                id_company: $scope.company.id_company,
                id: $scope.staff.id,
                staffavatar: staffavatar,
            });
            var posturl = BASE_URL + 'staff/createStaff';
            $http.post(posturl, dataObj, config).then(
                function (response) {
                    $scope.saving = false;
                    if (response.data.success == true) {
                        globals.mdToast('success', response.data.message);
                        $mdSidenav('Create').close();
                        $scope.get_usuarios();
                    } else {
                        globals.mdToast('error', response.data.message, 7000);
                    }
                },
                function (response) {
                    $scope.saving = false;
                }
            );

        });
    };

    function getBase64(file) {
        if (file == null) {
            return new Promise(resolve => {
                resolve(null);
            })

        } else {
            const reader = new FileReader()
            return new Promise(resolve => {
                reader.onload = ev => {
                    resolve(ev.target.result)
                }
                reader.readAsDataURL(file)
            })
        }
    }


    $http.get(BASE_URL + 'api/settings_detail/' + COMPANYID).then(function (Settings) {
        $scope.settings_detail = Settings.data;
        $scope.ViewEmpresasTrue = true;
    });

    $scope.sendTestEmail = function () {
        $scope.sendingTestEmail = true;
        var dataObj = $.param({
            email: $scope.settings_detail.testEmail
        });
        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        };
        var posturl = BASE_URL + 'settings/sendTestEmail';
        $http.post(posturl, dataObj, config)
            .then(
                function (response) {
                    $scope.sendingTestEmail = false;
                    if (response.data.success) {
                        showToast(NTFTITLE, response.data.message, ' success');
                    } else {
                        showToast(NTFTITLE, response.data.message, ' danger');
                    }
                },
                function (response) {
                    $scope.sendingTestEmail = false;
                    showToast(NTFTITLE, email_error, ' danger');
                });
    };

    $scope.seePasswordModal = function (ev) {
        $mdDialog.show({
            templateUrl: 'see_smtp_password.html',
            scope: $scope,
            preserveScope: true,
            targetEvent: ev
        });
    };

    $scope.SaveEmail = function () {
        $scope.savingSettings = true;
        var dataObj = $.param({
            smtphost: $scope.settings_detail.smtphost,
            smtpport: $scope.settings_detail.smtpport,
            emailcharset: $scope.settings_detail.emailcharset,
            email_encryption: $scope.settings_detail.email_encryption,
            smtpusername: $scope.settings_detail.smtpusername,
            smtppassoword: $scope.settings_detail.smtppassoword,
            sendermail: $scope.settings_detail.sendermail,
            sender_name: $scope.settings_detail.sender_name,
            email_type: $scope.settings_detail.email_type,
            id_company: COMPANYID
        });

        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        };

        var posturl = BASE_URL + 'settings/updateEmail/ciuis';

        $http.post(posturl, dataObj, config).then(
            function (response) {
                $scope.savingSettings = false;
                if (response.data.success == true) {
                    globals.mdToast('success', response.data.message);
                } else {
                    globals.mdToast('error', response.data.message);
                }
            },

            function (response) {
                $scope.savingSettings = false;
                showToast(NTFTITLE, 'Error', ' danger');
            }
        );
    };

    $scope.SaveEmailImap = function () {
        $scope.savingSettings = true;
        var dataObj = $.param({
            imapHost: $scope.settings_detail.imapHost,
            imapPort: $scope.settings_detail.imapPort,
            imapUsername: $scope.settings_detail.imapUsername,
            imapPassoword: $scope.settings_detail.imapPassoword,
            imap_sendermail: $scope.settings_detail.imap_sendermail,
            imap_email_type: $scope.settings_detail.imap_email_type,
            id_company: COMPANYID
        });

        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        };

        var posturl = BASE_URL + 'settings/updateEmailImap';

        $http.post(posturl, dataObj, config).then(
            function (response) {
                $scope.savingSettings = false;
                if (response.data.success == true) {
                    globals.mdToast('success', response.data.message);
                } else {
                    globals.mdToast('error', response.data.message);
                }
            },

            function (response) {
                $scope.savingSettings = false;
                showToast(NTFTITLE, 'Error', ' danger');
            }
        );
    };
}

CiuisCRM.controller('Companies_Controller', Companies_Controller);
CiuisCRM.controller('Company_Controller', Company_Controller);
