<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Display Antrian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.15.0/echo.iife.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; }
        /* Animasi kedip saat dipanggil */
        .blink-bg { animation: blinker 1s linear infinite; }
        @keyframes blinker {
            50% { background-color: #fef08a; color: #000; }
        }
    </style>
</head>

<body class="bg-gray-900 h-screen w-full flex flex-col overflow-hidden">

    <header class="bg-blue-900 text-white p-4 flex justify-between items-center shadow-lg z-10">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-blue-900 font-bold text-xl">L</div>
            <div>
                <h1 class="text-2xl font-bold tracking-wider">LAYANAN PELANGGAN</h1>
                <p class="text-sm text-blue-200">Melayani dengan sepenuh hati</p>
            </div>
        </div>
        <div class="text-right">
            <div id="clock" class="text-3xl font-mono font-bold">00:00:00</div>
            <div id="date" class="text-sm text-blue-200">Senin, 1 Januari 2024</div>
        </div>
    </header>

    <div class="flex-1 flex p-4 gap-4 overflow-hidden">
        
        <div class="w-2/3 bg-black rounded-xl overflow-hidden shadow-2xl relative flex items-center justify-center border border-gray-700">
            <div class="text-center text-gray-500">
                <svg class="w-24 h-24 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h2 class="text-2xl font-bold">VIDEO PROMOSI</h2>
                <p>Area ini untuk video profil perusahaan</p>
            </div>
        </div>

        <div class="w-1/3 flex flex-col gap-4">
            
            <div id="cardA" class="flex-1 bg-white rounded-xl shadow-xl overflow-hidden flex flex-col border-l-8 border-blue-600 relative transition-all duration-300">
                <div class="bg-gray-100 p-3 text-center border-b">
                    <h2 class="text-2xl font-bold text-gray-700">CUSTOMER SERVICE</h2>
                    <p class="text-sm text-gray-500">Layanan A</p>
                </div>
                <div class="flex-1 flex items-center justify-center bg-white">
                    <span id="sessionA" class="text-[8rem] font-black text-blue-600 leading-none tracking-tighter">--</span>
                </div>
                <div class="bg-blue-600 text-white text-center py-2 text-sm font-semibold">
                    MENUJU LOKET 1
                </div>
            </div>

            <div id="cardB" class="flex-1 bg-white rounded-xl shadow-xl overflow-hidden flex flex-col border-l-8 border-green-600 relative transition-all duration-300">
                <div class="bg-gray-100 p-3 text-center border-b">
                    <h2 class="text-2xl font-bold text-gray-700">TELLER / KASIR</h2>
                    <p class="text-sm text-gray-500">Layanan B</p>
                </div>
                <div class="flex-1 flex items-center justify-center bg-white">
                    <span id="sessionB" class="text-[8rem] font-black text-green-600 leading-none tracking-tighter">--</span>
                </div>
                <div class="bg-green-600 text-white text-center py-2 text-sm font-semibold">
                    MENUJU LOKET 2
                </div>
            </div>

        </div>
    </div>

    <div class="bg-yellow-400 text-black py-2 overflow-hidden whitespace-nowrap border-t-4 border-yellow-600">
        <div class="inline-block animate-marquee pl-[100%] font-bold text-lg uppercase">
            Selamat datang di Kantor Pelayanan Kami. Mohon menunggu panggilan sesuai nomor antrian. Jagalah kebersihan dan kenyamanan bersama. Jam operasional 08:00 - 15:00 WIB.
        </div>
    </div>

    <style>
        .animate-marquee { animation: marquee 20s linear infinite; }
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-100%); }
        }
    </style>

    <script type="module">
        // --- SOUND EFFECT ---
        // Anda bisa mengganti URL ini dengan file mp3 lokal di folder public
        const chime = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.m4a'); // Suara 'Ting'

        // --- REALTIME ECHO ---
        Echo.channel('public-queue')
            .listen('.queue.updated', (e) => {
                
                // Mainkan suara
                chime.currentTime = 0;
                chime.play().catch(err => console.log('Autoplay blocked:', err));

                const ticket = e.ticket;

                if(ticket.session === 'A') {
                    updateDisplay('sessionA', ticket.queue_number, 'cardA');
                }

                if(ticket.session === 'B') {
                    updateDisplay('sessionB', ticket.queue_number, 'cardB');
                }
            });

        function updateDisplay(elementId, number, cardId) {
            const el = document.getElementById(elementId);
            const card = document.getElementById(cardId);

            // Update Angka
            el.innerText = number;

            // Efek Berkedip Visual
            card.classList.add('blink-bg');
            setTimeout(() => {
                card.classList.remove('blink-bg');
            }, 5000);
        }

        // --- JAM REALTIME ---
        setInterval(() => {
            let now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString('id-ID', { hour12: false });
            document.getElementById('date').innerText = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }, 1000);
    </script>

</body>
</html>
