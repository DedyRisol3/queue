<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Queue System</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.15.0/echo.iife.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen p-6">

    <div class="max-w-7xl mx-auto">
        <header class="flex justify-between items-center mb-8 bg-white p-4 rounded-xl shadow-sm">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Admin Counter Dashboard</h1>
                <p class="text-sm text-gray-500">Monitoring & Control Panel</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold">Voice:</span>
                    <input type="checkbox" id="voiceToggle" checked class="w-5 h-5 text-blue-600 rounded">
                </div>
                <button onclick="logout()" class="px-4 py-2 bg-red-100 text-red-600 rounded hover:bg-red-200 text-sm font-semibold">Logout</button>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            
            <div class="lg:col-span-2 space-y-6">
                
                <div class="grid grid-cols-2 gap-4">
                    <button onclick="callNext('A')" class="group relative flex flex-col items-center justify-center p-8 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl shadow-lg transition-all transform hover:-translate-y-1">
                        <span class="text-4xl mb-2">📢</span>
                        <span class="text-2xl font-bold">CALL SESSION A</span>
                        <span class="text-blue-200 mt-2 text-sm">Customer Service</span>
                    </button>

                    <button onclick="callNext('B')" class="group relative flex flex-col items-center justify-center p-8 bg-green-600 hover:bg-green-700 text-white rounded-2xl shadow-lg transition-all transform hover:-translate-y-1">
                        <span class="text-4xl mb-2">📢</span>
                        <span class="text-2xl font-bold">CALL SESSION B</span>
                        <span class="text-green-200 mt-2 text-sm">Teller / Kasir</span>
                    </button>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 text-center">
                    <h3 class="text-gray-400 font-semibold text-sm uppercase tracking-wider mb-2">Sedang Dipanggil</h3>
                    <div id="currentNumber" class="text-7xl font-black text-gray-800 tracking-tight">-</div>
                    <div id="currentSessionName" class="text-xl text-blue-600 font-medium mt-2">-</div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                    <h3 class="font-bold text-gray-700 mb-4">Manual Action</h3>
                    <div class="flex gap-4">
                        <input type="text" id="manualInput" placeholder="Contoh: A005" class="flex-1 border p-3 rounded-lg bg-gray-50 uppercase text-lg font-bold">
                        <button onclick="manualAction('finished')" class="bg-gray-800 text-white px-6 py-3 rounded-lg font-semibold hover:bg-black">Mark Finish</button>
                        <button onclick="manualAction('skipped')" class="bg-yellow-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-yellow-600">Skip</button>
                    </div>
                </div>

            </div>

            <div class="space-y-6">
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-blue-500">
                        <div class="text-gray-500 text-xs font-bold uppercase">Waiting A</div>
                        <div id="countWaitingA" class="text-3xl font-bold text-gray-800">0</div>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-green-500">
                        <div class="text-gray-500 text-xs font-bold uppercase">Waiting B</div>
                        <div id="countWaitingB" class="text-3xl font-bold text-gray-800">0</div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm h-64 overflow-y-auto">
                    <h3 class="font-bold text-gray-700 mb-3 sticky top-0 bg-white pb-2 border-b">Next in Line</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="block font-bold text-blue-600 mb-2">Sesi A</span>
                            <ul id="listWaitingA" class="space-y-2 text-gray-600">
                                </ul>
                        </div>
                        <div>
                            <span class="block font-bold text-green-600 mb-2">Sesi B</span>
                            <ul id="listWaitingB" class="space-y-2 text-gray-600">
                                </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm">
                    <h3 class="font-bold text-gray-700 mb-3 border-b pb-2">Recent Calls</h3>
                    <ul id="historyList" class="space-y-3 text-sm">
                        </ul>
                </div>

            </div>
        </div>
    </div>

    <script>
        // KONFIGURASI API
        // Kita tembak langsung ke Service Queue (Port 8001) agar cepat & realtime
        const API_URL = 'http://localhost:8001/api/tickets'; 
        const SOCKET_HOST = 'http://localhost:8080';

        // 1. FUNGSI LOAD DATA
        async function loadDashboard() {
            try {
                // Fetch Stats
                const res = await axios.get(`${API_URL}/stats`);
                const data = res.data;

                // Update Counts
                document.getElementById('countWaitingA').innerText = data.waitingA;
                document.getElementById('countWaitingB').innerText = data.waitingB;

                // Update Current Called
                if(data.current) {
                    document.getElementById('currentNumber').innerText = data.current.queue_number;
                    document.getElementById('currentSessionName').innerText = data.current.session === 'A' ? 'Customer Service' : 'Teller';
                } else {
                    document.getElementById('currentNumber').innerText = '-';
                    document.getElementById('currentSessionName').innerText = 'Idle';
                }

                // Update Lists
                renderList('listWaitingA', data.listA);
                renderList('listWaitingB', data.listB);

                // Fetch History
                const histRes = await axios.get(`${API_URL}/history`);
                renderHistory(histRes.data);

            } catch (error) {
                console.error("Gagal load data", error);
            }
        }

        // Helper Render List
        function renderList(elementId, items) {
            const el = document.getElementById(elementId);
            el.innerHTML = '';
            items.forEach(ticket => {
                el.innerHTML += `<li class="bg-gray-50 px-2 py-1 rounded font-mono font-bold">${ticket.queue_number}</li>`;
            });
        }

        // Helper Render History
        function renderHistory(items) {
            const el = document.getElementById('historyList');
            el.innerHTML = '';
            items.forEach(item => {
                const time = new Date(item.updated_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
                const color = item.session === 'A' ? 'text-blue-600' : 'text-green-600';
                el.innerHTML += `
                    <li class="flex justify-between items-center">
                        <span class="font-bold ${color}">${item.queue_number}</span>
                        <span class="text-gray-400 text-xs">${time} <span class="capitalize ml-1 badge bg-gray-200 px-1 rounded text-black">${item.status}</span></span>
                    </li>`;
            });
        }

        // 2. FUNGSI CALL NEXT
        async function callNext(session) {
            try {
                const res = await axios.post(`${API_URL}/call-next`, { session: session });
                const ticket = res.data.data;
                
                // Mainkan Suara
                playVoice(ticket.queue_number, session);

                Swal.fire({
                    icon: 'success',
                    title: `Memanggil ${ticket.queue_number}`,
                    timer: 1500,
                    showConfirmButton: false
                });

                loadDashboard(); // Refresh data manual (backup jika socket delay)

            } catch (error) {
                if(error.response && error.response.status === 404) {
                    Swal.fire('Kosong', 'Tidak ada antrian menunggu di sesi ini', 'info');
                } else {
                    Swal.fire('Error', 'Gagal memanggil antrian', 'error');
                }
            }
        }

        // 3. FUNGSI MANUAL (SKIP/FINISH)
        async function manualAction(status) {
            const number = document.getElementById('manualInput').value.trim();
            if(!number) return Swal.fire('Ops', 'Masukkan nomor antrian dulu', 'warning');

            try {
                await axios.post(`${API_URL}/update-status`, { 
                    queue_number: number, 
                    status: status 
                });
                
                document.getElementById('manualInput').value = '';
                Swal.fire('Sukses', `Nomor ${number} berhasil di-${status}`, 'success');
                loadDashboard();
            } catch (error) {
                Swal.fire('Gagal', 'Nomor tidak ditemukan', 'error');
            }
        }

        // 4. FITUR SUARA (Text-to-Speech)
        function playVoice(number, session) {
            if(!document.getElementById('voiceToggle').checked) return;

            const sessionName = session === 'A' ? 'Customer Service' : 'Teller';
            // Pecah nomor agar dibaca jelas. misal A005 -> "Nomor Antrian A, Kosong, Kosong, Lima"
            // Tapi untuk simpelnya bahasa indonesia TTS biasanya cukup pintar membaca "A nol nol lima"
            
            const text = `Nomor antrian, ${number}, silakan menuju loket, ${sessionName}`;
            
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID'; // Bahasa Indonesia
            utterance.rate = 0.9;     // Agak lambat biar jelas
            window.speechSynthesis.speak(utterance);
        }

        // 5. REALTIME LISTENER (Laravel Reverb / Socket.io)
        // Pastikan Echo Config sesuai dengan docker-compose anda (port 8080)
        window.Echo = new Echo({
            broadcaster: 'socket.io',
            client: io,
            host: SOCKET_HOST
        });

        window.Echo.channel('public-queue')
            .listen('.queue.updated', (e) => {
                console.log("Event Received:", e);
                loadDashboard(); // Reload data otomatis saat ada event masuk
                
                // Jika statusnya 'called', bisa trigger suara otomatis juga disini jika mau
                // if(e.ticket.status === 'called') playVoice(e.ticket.queue_number, e.ticket.session);
            });

        function logout() {
            alert('Logout logic here');
        }

        // Jalankan saat load pertama
        loadDashboard();

    </script>
</body>
</html>