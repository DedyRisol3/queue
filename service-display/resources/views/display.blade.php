<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Display Antrian</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.15.0/echo.iife.js"></script>

</head>

<body class="bg-red-800 w-full h-screen flex items-center justify-center">

    <div class="w-[90%] max-w-6xl bg-red-800 rounded-xl p-6">

        <!-- JUDUL -->
        <div class="bg-gray-100 text-center py-4 rounded-lg mb-8">
            <h1 class="text-4xl font-bold tracking-wide">NO ANTRIAN</h1>
        </div>

        <div class="grid grid-cols-3 gap-8">

            <!-- SESSION A -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl overflow-hidden shadow-lg">
                    <div class="bg-black text-white text-center py-3 text-xl font-semibold">
                        Session A
                    </div>
                    <div id="sessionA" class="text-center text-[90px] font-extrabold py-10">
                        00
                    </div>
                </div>

                <!-- SESSION B -->
                <div class="bg-white rounded-xl overflow-hidden shadow-lg">
                    <div class="bg-black text-white text-center py-3 text-xl font-semibold">
                        Session B
                    </div>
                    <div id="sessionB" class="text-center text-[90px] font-extrabold py-10">
                        00
                    </div>
                </div>
            </div>

            <!-- VIDEO / IKLAN -->
            <div class="col-span-2">
                <div class="bg-white h-[350px] rounded-xl shadow-lg flex items-center justify-center text-6xl font-bold">
                    Video
                </div>

                <div class="bg-white rounded-xl shadow-lg py-4 mt-6 text-center text-xl font-semibold">
                    <span id="clock">Jam tanggal dan lain lain</span>
                </div>
            </div>
        </div>
    </div>

    <!-- REALTIME JS -->
    <script type="module">
        // Realtime update dari Laravel Echo
        Echo.channel('public-queue')
            .listen('.queue.updated', (e) => {
                
                // Jika ticket.session = 'A' atau 'B'
                if(e.ticket.session === 'A') {
                    document.getElementById('sessionA').innerText = e.ticket.queue_number;
                }

                if(e.ticket.session === 'B') {
                    document.getElementById('sessionB').innerText = e.ticket.queue_number;
                }
            });

        // Clock realtime
        setInterval(() => {
            let now = new Date();
            document.getElementById('clock').innerText =
                now.toLocaleString('id-ID', { dateStyle: 'full', timeStyle: 'medium' });
        }, 1000);
    </script>

</body>
</html>
