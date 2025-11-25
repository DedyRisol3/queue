<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambil Antrian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; }
        
        /* Animasi Kertas Keluar */
        .ticket-print { animation: printScroll 1s cubic-bezier(0.68, -0.55, 0.27, 1.55) forwards; }
        @keyframes printScroll {
            0% { transform: translateY(-100%); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        /* Glassmorphism Background */
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-900 via-slate-800 to-gray-900 h-screen w-full overflow-hidden text-gray-800">

    <div id="screen-selection" class="h-full flex flex-col items-center justify-center p-6 relative">
        
        <div class="absolute top-10 left-10 w-64 h-64 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute bottom-10 right-10 w-64 h-64 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>

        <div class="z-10 text-center mb-12">
            <h1 class="text-5xl font-extrabold text-white tracking-wide drop-shadow-lg">SELAMAT DATANG</h1>
            <p class="text-xl text-blue-200 mt-2 font-light">Silakan pilih layanan untuk mengambil nomor antrian</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 w-full max-w-5xl z-10">
            <button onclick="takeTicket('A')" class="group relative bg-white/90 hover:bg-white rounded-3xl p-10 shadow-2xl transition-all duration-300 transform hover:-translate-y-2 hover:shadow-blue-500/50 border-b-8 border-blue-600 active:border-b-0 active:translate-y-1">
                <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 bg-blue-600 text-white w-12 h-12 flex items-center justify-center rounded-full text-xl font-bold shadow-lg">A</div>
                <div class="mb-4 text-blue-600">
                    <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 group-hover:text-blue-600 transition">Customer Service</h2>
                <p class="text-gray-500 mt-2">Keluhan, Informasi & Pendaftaran</p>
            </button>

            <button onclick="takeTicket('B')" class="group relative bg-white/90 hover:bg-white rounded-3xl p-10 shadow-2xl transition-all duration-300 transform hover:-translate-y-2 hover:shadow-green-500/50 border-b-8 border-green-600 active:border-b-0 active:translate-y-1">
                <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 bg-green-600 text-white w-12 h-12 flex items-center justify-center rounded-full text-xl font-bold shadow-lg">B</div>
                <div class="mb-4 text-green-600">
                    <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 group-hover:text-green-600 transition">Teller / Kasir</h2>
                <p class="text-gray-500 mt-2">Setoran, Tarikan & Pembayaran</p>
            </button>
        </div>
        

    </div>


    <div id="screen-ticket" class="hidden h-full flex-col items-center justify-center relative z-20">
        
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div class="relative z-30 flex flex-col items-center">
            
            <div class="w-80 h-4 bg-gray-800 rounded-full shadow-inner mb-[-5px] z-40 border border-gray-600"></div>

            <div id="ticket-card" class="bg-white text-gray-900 w-72 p-6 shadow-2xl transform origin-top relative ticket-print">
                <div class="absolute bottom-0 left-0 w-full h-4 bg-white" style="mask-image: radial-gradient(circle, transparent 5px, black 6px); mask-size: 15px 15px; mask-position: bottom; -webkit-mask-image: radial-gradient(circle, transparent 5px, black 6px); -webkit-mask-size: 15px 15px; -webkit-mask-position: bottom;"></div>

                <div class="text-center border-b-2 border-dashed border-gray-300 pb-4 mb-4">
                    <h3 class="font-bold text-gray-500 uppercase tracking-widest text-xs">Nomor Antrian</h3>
                    <div id="ticket-date" class="text-[10px] text-gray-400 mt-1"></div>
                </div>
                
                <div id="ticket-number" class="text-7xl font-black text-gray-800 text-center mb-2 tracking-tighter">
                    ---
                </div>
                <div id="ticket-service" class="text-center font-bold text-lg mb-6 text-blue-600">
                    ---
                </div>

                <div class="bg-gray-100 rounded p-3 text-center mb-6">
                    <p class="text-xs text-gray-500">Silakan menunggu, nomor Anda akan segera dipanggil.</p>
                </div>

                <div class="text-center mt-4 pb-4">
                    <p class="text-xs text-gray-400">Terima Kasih</p>
                </div>
            </div>

            <div class="mt-8 text-white text-lg font-light animate-pulse">
                Kembali ke menu dalam <span id="countdown" class="font-bold text-yellow-400">5</span> detik...
            </div>
        </div>


    </div>


    <script>
        const API_URL = 'http://localhost:8001/api/tickets/take';
        let redirectTimer;

        async function takeTicket(session) {
            try {
                // Tampilkan loading state jika perlu
                // Request ke API
                const response = await axios.post(API_URL, { session: session });
                const ticket = response.data;

                // Isi Data Tiket
                document.getElementById('ticket-number').innerText = ticket.queue_number;
                document.getElementById('ticket-service').innerText = session === 'A' ? 'Customer Service' : 'Teller';
                document.getElementById('ticket-date').innerText = new Date().toLocaleString('id-ID');

                // Ganti Layar
                switchScreen('ticket');

                // Countdown
                startCountdown(5);

            } catch (error) {
                console.error(error);
                alert('Gagal mengambil tiket. Cek koneksi server.');
            }
        }

        function switchScreen(screenName) {
            const selectionEl = document.getElementById('screen-selection');
            const ticketEl = document.getElementById('screen-ticket');
            const ticketCard = document.getElementById('ticket-card');

            if (screenName === 'ticket') {
                selectionEl.classList.add('hidden');
                ticketEl.classList.remove('hidden');
                ticketEl.classList.add('flex');
                
                // Reset animasi printer agar main ulang
                ticketCard.classList.remove('ticket-print');
                void ticketCard.offsetWidth; // trigger reflow
                ticketCard.classList.add('ticket-print');

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


            if (redirectTimer) clearInterval(redirectTimer);

            redirectTimer = setInterval(() => {
                timeLeft--;
                counterEl.innerText = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(redirectTimer);

                    switchScreen('selection');
                }
            }, 1000);
        }
    </script>
</body>
</html>
