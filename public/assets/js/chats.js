function Chats_Controller($scope, $http, $mdSidenav, fileUpload, $filter, $mdToast) {
    "use strict";
    $http.get(BASE_URL + 'api/custom_fields_by_type/' + 'chat').then(function (custom_fields) {
        $scope.all_custom_fields = custom_fields.data;
        $scope.custom_fields = ($scope.all_custom_fields, {
            active: 'true',
        });
    });
    
    $http.get(BASE_URL + 'api/staff/0').then(function (Staff) {
        $scope.users = Staff.data;
    });


    $scope.Create = buildToggler('Create');
    function buildToggler(navID) {
        return function () {
            $mdSidenav(navID).toggle();
        };
    }
    $scope.close = function () {
        $mdSidenav('Create').close();
    };
    globals.get_departments();
    $scope.createChat = function () {
        $scope.uploading = true;
        if (!$scope.chat) {
            var dataObj = {
                subject: '',
                customer: '',
                contact: '',
                department: '',
                priority: '',
                message: '',
                file: ''
            };
        } else {
            var dataObj = {
                user: $scope.chat.user,
                department: $scope.chat.department,
                priority: $scope.chat.priority,
                message: $scope.chat.message,
                file: $scope.chat.chat_attachment
            };
        }
        var uploadUrl = BASE_URL + 'chats/create';
        fileUpload.uploadFileWithData(dataObj, uploadUrl, function (response) {
            if (response.success == true) {
                $mdSidenav('Create').close();
                globals.mdToast('success', response.message);
                $scope.chatsLoader = true;
                $scope.chat = [];
                
                $http.get(BASE_URL + 'api/chats').then(function (Chats) {
                    $scope.chats = Chats.data;
                    $scope.chatsLoader = false;
                });
            } else {
                globals.mdToast('error', response.message);
            }
            $scope.uploading = false;
        });
    };
    $scope.chat_list = {
        order: '',
        limit: 5,
        page: 1
    };
    $scope.chatsLoader = true;
    $http.get(BASE_URL + 'api/chats').then(function (Chats) {
        $scope.chats = Chats.data;
        $scope.limitOptions = [5, 10, 15, 20];
        if ($scope.chats.length > 20) {
            $scope.limitOptions = [5, 10, 15, 20, $scope.chats.length];
        }
        $scope.chatsLoader = false;
        $scope.GoChat = function (CHATID) {
            window.location.href = BASE_URL + 'chats/chat/' + CHATID;
        };
        $scope.search = {
            subject: '',
            message: ''
        };
        $scope.itemsPerPage = 5;
        $scope.currentPage = 0;
        $scope.range = function () {
            var rangeSize = 5;
            var ps = [];
            var start;
            start = $scope.currentPage;
            if (start > $scope.pageCount() - rangeSize) {
                start = $scope.pageCount() - rangeSize + 1;
            }
            for (var i = start; i < start + rangeSize; i++) {
                if (i >= 0) {
                    ps.push(i);
                }
            }
            return ps;
        };
        $scope.prevPage = function () {
            if ($scope.currentPage > 0) {
                $scope.currentPage--;
            }
        };
        $scope.DisablePrevPage = function () {
            return $scope.currentPage === 0 ? "disabled" : "";
        };
        $scope.nextPage = function () {
            if ($scope.currentPage < $scope.pageCount()) {
                $scope.currentPage++;
            }
        };
        $scope.DisableNextPage = function () {
            return $scope.currentPage === $scope.pageCount() ? "disabled" : "";
        };
        $scope.setPage = function (n) {
            $scope.currentPage = n;
        };
        $scope.pageCount = function () {
            return Math.ceil($scope.chats.length / $scope.itemsPerPage) - 1;
        };
    });
    $scope.ShowKanban = function () {
        $scope.KanbanBoard = true;
    };
    $scope.HideKanban = function () {
        $scope.KanbanBoard = false;
    };
    $http.get(BASE_URL + 'api/customers').then(function (Customers) {
        $scope.customers = Customers.data;
    });
    // $http.get(BASE_URL + 'api/contacts').then(function (Contacts) {
    // 	$scope.contacts = Contacts.data;
    // });
}

function Chat_Controller($scope, $http, $mdDialog, fileUpload, $mdSidenav) {
    "use strict";
    function buildToggler(navID) {
        return function () {
            $mdSidenav(navID).toggle();
        };
    }
    $scope.close = function () {
        $mdDialog.hide();
    };
    $scope.get_staff();
    $scope.AssigneStaff = function (ev) {
        $mdDialog.show({
            templateUrl: 'insert-member-template.html',
            scope: $scope,
            preserveScope: true,
            targetEvent: ev
        });
    };
    $http.get(BASE_URL + 'api/custom_fields_data_by_type/' + 'chat/' + CHATID).then(function (custom_fields) {
        $scope.custom_fields = custom_fields.data;
    });
    globals.get_departments();
    $scope.replying = false;
    $scope.replyToChat = function () {
        if($scope.replying){
            return;
        }
        $scope.replying = true;
        if (!$scope.reply) {
            var dataObj = {
                message: '',
                file: ''
            };
        } else {
            var dataObj = {
                message: $scope.reply.message,
                file: $scope.reply.attachment
            };
        }
        var uploadUrl = BASE_URL + 'chats/reply/' + CHATID;
        fileUpload.uploadFileWithData(dataObj, uploadUrl, function (response) {
            if (response.success == true) {
                $scope.replying = true;
                $('#chooseFile').val('');
                $scope.reply.message = '';
                $http.get(BASE_URL + 'chats/get_chat/' + CHATID).then(function (ChatDetails) {
                    $scope.replying = false;
                    $scope.chat = ChatDetails.data;
                });
            } else {
                showToast(NTFTITLE, response.message, ' danger');
            }
            $scope.replying = false;
        });
    };
    $scope.chatsLoader = true;
    $http.get(BASE_URL + 'chats/get_chat/' + CHATID).then(function (ChatDetails) {
        $scope.chat = ChatDetails.data;
        $scope.chatsLoader = false;
        $scope.AssignStaff = function () {
            var dataObj = $.param({
                staff: $scope.AssignedStaff,
            });
            var posturl = BASE_URL + 'chats/assign_staff/' + CHATID;
            $http.post(posturl, dataObj, config)
                .then(
                    function (response) {
                        if (response.data.success == true) {
                            $mdDialog.hide();
                            $scope.chat.assigned_staff_name = response.data.name;
                        } else {
                            $mdDialog.hide();
                            globals.mdToast('error', response.data.message);
                        }
                    },
                    function (response) {
                        console.log(response);
                    }
                );
        };
        $scope.Reply = function () {
            var dataObj = $.param({
                message: $scope.reply.message,
                attachment: $scope.reply.attachment,
            });
            var posturl = BASE_URL + 'chats/reply/' + CHATID;
            $http.post(posturl, dataObj, config)
                .then(
                    function (response) {
                        console.log(response);
                        $scope.chat.replies.push({
                            'message': $scope.reply.message,
                            'name': LOGGEDINSTAFFNAME,
                            'date': new Date(),
                            'attachment': $scope.reply.attachment,
                        });
                        $scope.reply.attachment = '';
                        $scope.reply.message = '';
                    },
                    function (response) {
                        console.log(response);
                    }
                );
        };
        $scope.Delete = function () {
            // Appending dialog to document.body to cover sidenav in docs app
            var confirm = $mdDialog.confirm()
                .title(lang.attention)
                .textContent(lang.chatattentiondetail)
                .ariaLabel(lang.delete + ' ' + lang.chat)
                .targetEvent(CHATID)
                .ok(lang.doIt)
                .cancel(lang.cancel);
            $mdDialog.show(confirm).then(function () {
                $http.post(BASE_URL + 'chats/remove/' + CHATID, config)
                    .then(
                        function (response) {
                            if (response.data.success == true) {
                                window.location.href = BASE_URL + 'chats';
                                globals.mdToast('error', response.data.message);
                            } else {
                                globals.mdToast('error', response.data.message);
                            }
                        },
                        function (response) {
                            console.log(response);
                        }
                    );
            }, function () {
                //
            });
        };
    });
    $scope.MarkAs = function (id, name) {
        var dataObj = $.param({
            status_id: id,
            chat_id: CHATID,
            name: name,
        });
        var posturl = BASE_URL + 'chats/markas';
        $http.post(posturl, dataObj, config)
            .then(
                function (response) {
                    if (response.data.success == true) {
                        globals.mdToast('success', response.data.message);
                    } else {
                        globals.mdToast('error', response.data.message);
                    }
                },
                function (response) {
                    console.log(response);
                }
            );
    };
}
CiuisCRM.controller('Chats_Controller', Chats_Controller);
CiuisCRM.controller('Chat_Controller', Chat_Controller);
