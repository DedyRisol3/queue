<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambil Antrian - Kiosk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        /* Animasi fade in */
        .fade-in { animation: fadeIn 0.5s ease-in-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-100 h-screen w-full overflow-hidden font-sans selection:bg-none">

    <div id="screen-selection" class="h-full flex flex-col items-center justify-center p-6">
        
        <div class="mb-10 text-center">
            <h1 class="text-4xl font-extrabold text-gray-800 mb-2">SELAMAT DATANG</h1>
            <p class="text-xl text-gray-500">Silakan pilih layanan yang Anda butuhkan</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full max-w-5xl">
            <button onclick="takeTicket('A')" class="group relative bg-white border-b-8 border-blue-600 rounded-3xl p-10 shadow-xl hover:shadow-2xl active:border-b-0 active:translate-y-2 transition-all duration-150 flex flex-col items-center">
                <div class="bg-blue-100 p-6 rounded-full mb-6 group-hover:bg-blue-200 transition">
                    <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h2 class="text-4xl font-bold text-gray-800 group-hover:text-blue-600">Layanan A</h2>
                <p class="text-lg text-gray-400 mt-2">Customer Service</p>
            </button>

            <button onclick="takeTicket('B')" class="group relative bg-white border-b-8 border-green-600 rounded-3xl p-10 shadow-xl hover:shadow-2xl active:border-b-0 active:translate-y-2 transition-all duration-150 flex flex-col items-center">
                <div class="bg-green-100 p-6 rounded-full mb-6 group-hover:bg-green-200 transition">
                    <svg class="w-16 h-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h2 class="text-4xl font-bold text-gray-800 group-hover:text-green-600">Layanan B</h2>
                <p class="text-lg text-gray-400 mt-2">Teller / Kasir</p>
            </button>
        </div>

        <div class="mt-12 text-gray-400 text-sm">
            <p>Sentuh salah satu tombol di atas untuk mencetak tiket</p>
        </div>
    </div>

    <div id="screen-ticket" class="hidden h-full flex-col items-center justify-center bg-gray-900 text-white p-6 fade-in text-center">
        
        <div class="bg-white text-gray-900 rounded-3xl p-10 w-full max-w-md shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-4 bg-blue-500"></div>
            
            <h3 class="text-xl text-gray-500 uppercase tracking-widest mb-4">Nomor Antrian Anda</h3>
            
            <div id="ticket-number" class="text-8xl font-black text-gray-800 mb-6 tracking-tighter">
                ---
            </div>

            <div class="bg-gray-100 rounded-xl p-4 mb-6">
                <p class="text-gray-500 text-sm">Silakan menunggu, nomor Anda akan segera dipanggil.</p>
            </div>

            <div class="text-sm text-gray-400">
                Layar akan kembali dalam <span id="countdown" class="font-bold text-red-500 text-lg">5</span> detik
            </div>
        </div>

        <div class="mt-8 text-gray-500">
            Terima Kasih
        </div>
    </div>

    <script>
        // CONFIG
        const API_URL = 'http://localhost:8001/api/tickets/take'; // Langsung ke Service Queue
        let redirectTimer;

        async function takeTicket(session) {
            try {
                // 1. Tampilkan Loading (Opsional, atau disable tombol)
                // document.body.style.cursor = 'wait';

                // 2. Request ke API
                const response = await axios.post(API_URL, {
                    session: session
                });

                const ticket = response.data; // Data Tiket (queue_number, dll)

                // 3. Tampilkan Data di Layar Tiket
                document.getElementById('ticket-number').innerText = ticket.queue_number;

                // 4. Ganti Layar (Selection -> Ticket)
                switchScreen('ticket');

                // 5. Jalankan Countdown 5 Detik
                startCountdown(5);

            } catch (error) {
                console.error(error);
                alert('Gagal mengambil tiket. Coba lagi.');
            }
        }

        function switchScreen(screenName) {
            const selectionEl = document.getElementById('screen-selection');
            const ticketEl = document.getElementById('screen-ticket');

            if (screenName === 'ticket') {
                selectionEl.classList.add('hidden');
                ticketEl.classList.remove('hidden');
                ticketEl.classList.add('flex'); // Agar centering flexbox jalan
            } else {
                selectionEl.classList.remove('hidden');
                ticketEl.classList.add('hidden');
                ticketEl.classList.remove('flex');
            }
        }

        function startCountdown(seconds) {
            const counterEl = document.getElementById('countdown');
            let timeLeft = seconds;
            
            counterEl.innerText = timeLeft;

            // Bersihkan timer lama jika ada
            if (redirectTimer) clearInterval(redirectTimer);

            redirectTimer = setInterval(() => {
                timeLeft--;
                counterEl.innerText = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(redirectTimer);
                    // KEMBALI KE MENU UTAMA
                    switchScreen('selection');
                }
            }, 1000);
        }
    </script>
</body>
</html>
