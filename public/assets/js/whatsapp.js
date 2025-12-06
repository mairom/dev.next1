function WhatsApp_Controller($scope, $http, $mdSidenav, $filter, $mdDialog, fileUpload, $q, $sce) {
    "use strict";
    $scope.whatsLoader = false;
    $scope.messages = [];
    $scope.newMessage = [];
    $scope.newMessage.text = '';
    $scope.contacts = [];
    $scope.sizeWidth = $(window).width();
    $scope.selectedContact = null;
    $scope.number = '';
    $scope.urlApi = 'apiwhats.ageup.pro:3000';
    $scope.filtros = [];
    $scope.contactsOffset = 0;
    $scope.contactsLimit = 10;
    $scope.loadingMore = false;
    $scope.hasMoreContacts = true;
    $scope.isConnected = false;
    $scope.delayReconnectAttempts = 10000;
    $scope.reconnectAttempts = 0;
    $scope.maxReconnectAttempts = 10;
    $scope.showReconnectBanner = false;
    let isReconnecting = false;
    let intervalId = null;
    let wsWhatsApp = null;
    let reconnectTimeout = null;
    let params = new URLSearchParams(document.location.search);
    $scope.statusWebsocket = "Testando";


    $http.get('https://apiwhats.ageup.pro:3000/status').then(function (response) {
        if (response.data.status == "ok") {
            $scope.statusWebsocket = response.data.message;
        } else {
            $scope.statusWebsocket = "não responde";
        }

    }, a => {
        $scope.statusWebsocket = "Erro!";
    });

    $http.get(BASE_URL + 'settings/get_settingsIa').then(function (response) {
        $scope.settings = response.data;
        if (response.data.number_atual != null && response.data.number_atual.connected == '1') {
            $scope.filtros.number = response.data.number_atual.number;
            $scope.setWhatsapp(response.data.number_atual);
        }
        setTimeout(() => {
            const el = document.getElementById('contactsList');
            if (!el) return;
            el.addEventListener('scroll', function () {
                if ($scope.loadingMore || !$scope.hasMoreContacts) return;
                if (el.scrollTop + el.clientHeight >= el.scrollHeight - 20) {
                    $scope.$apply(() => $scope.loadContacts());
                }
            });
        }, 500);
    });
    $scope.loadContacts = function (reset) {
        if ($scope.loadingMore) return;
        if (!$scope.hasMoreContacts && !reset) return;
        if (reset) {
            $scope.contactsOffset = 0;
            $scope.contacts = [];
            $scope.hasMoreContacts = true;
        }
        $scope.loadingMore = true;
        wsWhatsApp.send(JSON.stringify({
            type: 'get-contacts',
            number: $scope.number,
            offset: $scope.contactsOffset,
            limit: $scope.contactsLimit
        }));
    };
    $scope.getWhatsapp = function () {
        return $scope.settings.numbers.find(function (a) {
            return a.number == $scope.filtros.number;
        });
    };
    $scope.setWhatsapp = function (number) {
        $scope.number = number.number;
        $scope.contacts = [];
        $scope.contactsOffset = 0;
        $scope.contactsLimit = 20;
        if (wsWhatsApp != null) {
            wsWhatsApp.close(1000, 'Client initiated closure');
        }
        wsWhatsApp = new WebSocket('wss://' + $scope.urlApi);
        wsWhatsApp.onerror = () => {
            console.error("Erro no WebSocket");
            if ($scope.isConnected && !isReconnecting) {
                $scope.selectedContact = null;
                $scope.isConnected = false;
                reconnectWebSocket();
            }
        };
        wsWhatsApp.onopen = () => {
            console.log('Conectado ao WebSocket');
            isReconnecting = false;
            $scope.$applyAsync(() => {
                $scope.isConnected = true;
                $scope.reconnectAttempts = 0;
                $scope.showReconnectBanner = false;
            });
            wsWhatsApp.send(JSON.stringify({
                type: 'get-contacts',
                number: $scope.number,
                offset: $scope.contactsOffset,
                limit: $scope.contactsLimit
            }));
            $scope.whatsLoader = true;
            if (params.get("number") != null) {
                let number = params.get("number");
                let name = params.get("name");
                if (!number.startsWith("55")) {
                    number = "55" + number;
                }
                $scope.selectContact({
                    id: number + "@c.us",
                    name,
                    number
                });
            }
        };
        wsWhatsApp.onclose = function (event) {
            console.log('WebSocket fechado', event.code);
            if (event.code !== 1000) {
                if ($scope.isConnected && !isReconnecting) {
                    $scope.selectedContact = null;
                    $scope.isConnected = false;
                    reconnectWebSocket();
                }
            }
        };
        wsWhatsApp.onmessage = (event) => {
            const data = JSON.parse(event.data);
            if (data.type === 'contacts-list') {
                $scope.$apply(() => {
                    const mapped = (data.contacts || []).map(c => Object.assign({}, c, { unreadCount: c.unreadCount || 0 }));
                    if (!data.offset || data.offset === 0) {
                        $scope.contacts = mapped;
                    } else {
                        $scope.contacts = $scope.contacts.concat(mapped);
                    }
                    $scope.contactsOffset += mapped.length;
                    $scope.loadingMore = false;
                    $scope.whatsLoader = false;
                    if (mapped.length < $scope.contactsLimit) {
                        $scope.hasMoreContacts = false; // não tem mais contatos
                    }
                });
            }
            // 🔹 Recebe histórico de mensagens do contato selecionado
            if (data.type === 'messages-history' && $scope.selectedContact && data.contactId === $scope.selectedContact.id) {
                $scope.$apply(() => {
                    $scope.messages = data.messages.map(msg => {
                        const date = new Date(msg.timestamp);
                        const dia = String(date.getDate()).padStart(2, '0');
                        const mes = String(date.getMonth() + 1).padStart(2, '0');
                        const ano = date.getFullYear();
                        const hora = String(date.getHours()).padStart(2, '0');
                        const min = String(date.getMinutes()).padStart(2, '0');
                        const dataHora = `${dia}/${mes}/${ano} às ${hora}:${min}`;
                        const messageObj = {
                            sender: msg.fromMe ? 'me' : 'other',
                            text: msg.text || null,
                            html: msg.text ? formatWhatsAppToHTML(msg.text) : null,
                            media: msg.media ? {
                                mimetype: msg.media.mimetype,
                                data: msg.media.data,
                                filename: msg.media.filename,
                                url: $sce.trustAsResourceUrl(
                                    `data:${msg.media.mimetype};base64,${msg.media.data}`
                                )
                            } : null,
                            time: dataHora
                        };
                        return messageObj;
                    });
                    // Zera contador de não lidas ao abrir o contato
                    $scope.selectedContact.unreadCount = 0;
                });
                scrollToBottom();
            }
            if (data.type === 'new-message') {
                $scope.$apply(() => {
                    let contact = $scope.contacts.find(c => c.id === data.contactId);
                    // 1️⃣ Se não existe na lista, adiciona
                    if (!contact) {
                        contact = {
                            id: data.contactId,
                            name: data.contactId,
                            number: data.number,
                            lastMessage: data.text,
                            unreadCount: 0
                        };
                        $scope.contacts.push(contact);
                    }
                    // 2️⃣ Atualiza última mensagem
                    contact.lastMessage = data.text;
                    // 3️⃣ Se for do contato selecionado, mostra no chat
                    if ($scope.selectedContact && data.contactId === $scope.selectedContact.id) {
                        $scope.messages.push({
                            sender: data.fromMe ? 'me' : 'other',
                            text: data.text,
                            html: formatWhatsAppToHTML(data.text)
                        });
                        scrollToBottom();
                        contact.unreadCount = 0;
                    } else {
                        // Incrementa contador de não lidas
                        contact.unreadCount = (contact.unreadCount || 0) + 1;
                    }
                    // 🔥 4️⃣ Move apenas o contato atualizado para o topo
                    const index = $scope.contacts.indexOf(contact);
                    if (index > 0) {
                        $scope.contacts.splice(index, 1); // remove da posição atual
                        $scope.contacts.unshift(contact); // adiciona no topo
                    }
                });
            }
        };
    }
    $scope.getData = function (timestamp) {
        if (timestamp == null) {
            return "Agora";
        }
        const date = new Date(parseInt(timestamp));

        const dia = String(date.getDate()).padStart(2, '0');
        const mes = String(date.getMonth() + 1).padStart(2, '0');
        const ano = date.getFullYear();
        const hora = String(date.getHours()).padStart(2, '0');
        const min = String(date.getMinutes()).padStart(2, '0');
        const dataHora = `${dia}/${mes}/${ano} às ${hora}:${min}`;

        return dataHora;
    }
    $scope.backContact = function () {
        $scope.selectedContact = null;
    }
    // Selecionar contato
    $scope.selectContact = function (contact) {
        $scope.selectedContact = contact;
        $scope.selectedContact.botActive = true;
        $scope.messages = [];
        $http.get('https://' + $scope.urlApi + '/get-bot-toggle/' + $scope.selectedContact.number).then(function (response) {
            $scope.selectedContact.botActive = response.data.botActive;
        });
        // Zera contador de não lidas ao abrir
        contact.unreadCount = 0;
        wsWhatsApp.send(JSON.stringify({
            type: 'get-messages',
            contactId: contact.id,
            number: $scope.number,
        }));
    };
    $scope.toggleBotApi = function () {
        // Faz requisição para o backend informando o status
        let active = $scope.selectedContact.botActive;
        fetch('https://' + $scope.urlApi + '/bot-toggle', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ number: $scope.selectedContact.number, active })
        }).then(res => {
            if (!res.ok) {
                console.error('Erro ao atualizar estado do bot');
                // Opcional: reverter toggle em caso de erro
                $scope.selectedContact.botActive = !active;
                $scope.$apply();
            }
        }).catch(err => {
            console.error('Erro na requisição:', err);
            $scope.selectedContact.botActive = !active;
            $scope.$apply();
        });
    };
    // Enviar mensagem
    $scope.sendMessage = function () {
        if (!$scope.newMessage.text.trim() || !$scope.selectedContact) return;
        const msg = $scope.newMessage.text;
        const date = new Date();
        const dia = String(date.getDate()).padStart(2, '0');
        const mes = String(date.getMonth() + 1).padStart(2, '0');
        const ano = date.getFullYear();
        const hora = String(date.getHours()).padStart(2, '0');
        const min = String(date.getMinutes()).padStart(2, '0');
        const dataHora = `${dia}/${mes}/${ano} às ${hora}:${min}`;
        $scope.messages.push({ sender: 'me', text: msg, html: formatWhatsAppToHTML(msg), time: dataHora });
        wsWhatsApp.send(JSON.stringify({
            type: 'send-message',
            contactId: $scope.selectedContact.id,
            number: $scope.number,
            text: msg,
        }));
        $scope.newMessage.text = '';
        scrollToBottom();
    };
    $scope.sendMedia = function (file, type) {
        if (!$scope.selectedContact) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            const base64Data = e.target.result.split(',')[1];
            const safeUrl = $sce.trustAsResourceUrl(
                `data:${file.type};base64,${base64Data}`
            );
            $scope.$apply(() => {
                $scope.messages.push({
                    sender: 'me',
                    text: null,
                    html: null,
                    media: {
                        mimetype: file.type,
                        data: base64Data,
                        filename: file.name,
                        url: safeUrl // ✅ URL confiável
                    }
                });
                scrollToBottom();
            });
            wsWhatsApp.send(JSON.stringify({
                type: 'send-media',
                contactId: $scope.selectedContact.id,
                number: $scope.number,
                mediaType: type,
                fileName: file.name,
                mimetype: file.type,
                data: base64Data
            }));
        };
        reader.readAsDataURL(file);
    };
    function formatTime(seconds) {
        if (!seconds) return "0:00";
        const m = Math.floor(seconds / 60);
        const s = Math.floor(seconds % 60);
        return `${m}:${s < 10 ? "0" + s : s}`;
    }
    $scope.initAudio = function (index) {
        const msg = $scope.messages[index];
        const audio = document.getElementById("audio" + index);
        if (!audio) return;
        if (msg._initialized) return; // evita listeners duplicados
        msg._initialized = true;
        msg.progress = 0;
        msg.currentTimeDisplay = "0:00";
        msg.durationDisplay = "0:00";
        msg.playbackRate = 1;
        msg.playbackRateDisplay = "1x";
        msg.durationDisplay = formatTime(audio.duration);
        audio.addEventListener("timeupdate", () => {
            if (audio.duration && !isNaN(audio.duration)) {
                msg.progress = (audio.currentTime / audio.duration) * 100;
                msg.currentTimeDisplay = formatTime(audio.currentTime);
                $scope.$applyAsync();
            }
        });
        audio.addEventListener("ended", () => {
            msg.isPlaying = false;
            msg.progress = 0;
            $scope.$applyAsync();
        });
    };
    $scope.toggleAudio = function (index) {
        const msg = $scope.messages[index];
        const audio = document.getElementById("audio" + index);
        if (!audio) return;
        // Pausa outros áudios
        $scope.messages.forEach((m, i) => {
            if (i !== index && m.isPlaying) {
                const other = document.getElementById("audio" + i);
                if (other) other.pause();
                m.isPlaying = false;
            }
        });
        if (msg.isPlaying) {
            audio.pause();
            msg.isPlaying = false;
        } else {
            $scope.initAudio(index);
            audio.play();
            msg.isPlaying = true;
        }
    };
    $scope.seekAudio = function ($event, index) {
        const audio = document.getElementById("audio" + index);
        if (!audio || !audio.duration) return;
        const rect = $event.currentTarget.getBoundingClientRect();
        const clickX = $event.clientX - rect.left;
        const percent = clickX / rect.width;
        audio.currentTime = percent * audio.duration;
    };
    $scope.changePlaybackRate = function (index) {
        const msg = $scope.messages[index];
        const audio = document.getElementById("audio" + index);
        if (!audio) return;
        const speeds = [1, 1.5, 2];
        const next = (speeds.indexOf(msg.playbackRate) + 1) % speeds.length;
        msg.playbackRate = speeds[next];
        msg.playbackRateDisplay = speeds[next] + "x";
        audio.playbackRate = msg.playbackRate;
    };

    $scope.reiniciaServidor = function () {
        let userConfirmed = confirm('Tem certeza? não faça isso novamente se já fez em menos de 10 minutos');

        if (userConfirmed) {
            window.open("https://painel.ageup.pro/reinicia_app.php", "_blank", "width=400,height=300");
        }
    };

    function scrollToBottom() {
        setTimeout(() => {
            const chatBox = document.getElementById('chatBox');
            if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
        }, 50);
    }
    function formatWhatsAppToHTML(text) {
        if (!text) return '';
        return text
            .replace(/\n/g, '<br>')                 // Quebras de linha
            .replace(/\*\*(.*?)\*\*/g, '<b>$1</b>') // Negrito **texto**
            .replace(/\*(.*?)\*/g, '<b>$1</b>')     // Itálico *texto*
            .replace(/```(.*?)```/gs, '<pre>$1</pre>') // Blocos de código
            .replace(/`(.*?)`/g, '<code>$1</code>') // Código inline
            .replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank">$1</a>'); // Links clicáveis
    }
    $scope.$watch('selectedContact', function (newVal) {
        if (!newVal) {
            document.querySelector('.contacts-list').classList.remove('hidden');
            document.querySelector('.chat-container').classList.remove('active');
        }
    });
    function reconnectWebSocket() {
        isReconnecting = true;
        $scope.$applyAsync(() => {
            $scope.showReconnectBanner = true;
        });
        console.log(`Tentando reconectar em ${$scope.delayReconnectAttempts / 1000}s...`);
        intervalId = setInterval(() => {
            if ($scope.reconnectAttempts >= $scope.maxReconnectAttempts
                || !isReconnecting || $scope.isConnected
            ) {
                clearInterval(setInterval);
            }
            $scope.reconnectAttempts++;
            if (!navigator.onLine) {
                console.warn("Sem internet, aguardando...");
            } else {
                $scope.setWhatsapp({ number: $scope.number });
            }
        }, $scope.delayReconnectAttempts);
    }
}
CiuisCRM.controller('WhatsApp_Controller', WhatsApp_Controller);
