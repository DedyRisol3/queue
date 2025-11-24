<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Dashboard - Queue System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.15.0/echo.iife.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">
  <div class="max-w-6xl mx-auto p-6">
    <header class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold">Admin Dashboard - Queue System</h1>
      <div class="space-x-2">
        <button id="refreshBtn" class="px-4 py-2 bg-gray-200 rounded">Refresh</button>
        <button id="logoutBtn" class="px-4 py-2 bg-red-500 text-white rounded">Logout</button>
      </div>
    </header>

    <!-- Summary cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-500">Current Called</div>
        <div class="text-3xl font-bold mt-2" id="currentCalled">-</div>
        <div class="text-sm text-gray-400 mt-1" id="currentSession">Session -</div>
      </div>

      <div class="bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-500">Waiting A</div>
        <div class="text-3xl font-bold mt-2" id="waitingA">0</div>
        <div class="text-sm text-gray-400 mt-1">Last: <span id="lastA">-</span></div>
      </div>

      <div class="bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-500">Waiting B</div>
        <div class="text-3xl font-bold mt-2" id="waitingB">0</div>
        <div class="text-sm text-gray-400 mt-1">Last: <span id="lastB">-</span></div>
      </div>
    </div>

    <!-- Controls -->
    <div class="flex gap-4 mb-6">
      <div class="flex-1 bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-500 mb-2">Actions (Call Next)</div>
        <div class="flex gap-3">
          <button id="callA" class="flex-1 py-4 bg-blue-600 text-white rounded text-xl">Panggil A</button>
          <button id="callB" class="flex-1 py-4 bg-green-600 text-white rounded text-xl">Panggil B</button>
        </div>
        <div class="mt-3 text-sm text-gray-500">Voice: <input type="checkbox" id="voiceToggle" checked></div>
      </div>

      <div class="w-80 bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-500">Skip / Finish</div>
        <div class="mt-3">
          <input id="skipInput" type="text" placeholder="Masukkan nomor (A001)" class="w-full p-2 border rounded" />
          <div class="flex gap-2 mt-3">
            <button id="skipBtn" class="flex-1 py-2 bg-yellow-500 rounded">Skip</button>
            <button id="finishBtn" class="flex-1 py-2 bg-gray-700 text-white rounded">Mark Finished</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Waiting lists and History -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold mb-3">Waiting List</h3>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <h4 class="font-medium">Session A</h4>
            <ol id="listA" class="mt-2 text-xl"></ol>
          </div>
          <div>
            <h4 class="font-medium">Session B</h4>
            <ol id="listB" class="mt-2 text-xl"></ol>
          </div>
        </div>
      </div>

      <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold mb-3">Call History (Last 20)</h3>
        <table class="w-full text-left">
          <thead>
            <tr class="text-sm text-gray-500">
              <th class="pb-2">#</th>
              <th class="pb-2">Queue</th>
              <th class="pb-2">Session</th>
              <th class="pb-2">Time</th>
            </tr>
          </thead>
          <tbody id="historyBody"></tbody>
        </table>
      </div>
    </div>

    <footer class="mt-6 text-sm text-gray-500">Admin Dashboard • Queue Microservices</footer>
  </div>

<script>
// CONFIG
const API_GATEWAY = 'http://localhost:8000'; // Gateway yang forward ke service-admin -> service-queue
const QUEUE_SERVICE_LOCAL = 'http://localhost:8001'; // direct ke service-queue (bisa digunakan oleh browser)
const VOICE_ENABLED_DEFAULT = true;

// Helpers
function toast(title, icon='success'){
  Swal.fire({
    title, icon, timer: 2000, toast: true, position: 'top-end', showConfirmButton: false
  });
}

function speak(text){
  if(!document.getElementById('voiceToggle').checked) return;
  if('speechSynthesis' in window){
    const utter = new SpeechSynthesisUtterance(text);
    utter.rate = 1.0;
    speechSynthesis.cancel();
    speechSynthesis.speak(utter);
  }
}

// Fetch helpers
async function fetchStatus(){
  try{
    const res = await axios.get(`${QUEUE_SERVICE_LOCAL}/api/tickets/status`);
    return res.data;
  }catch(e){
    console.error('fetchStatus error', e);
    return null;
  }
}

async function fetchHistory(){
  try{
    const res = await axios.get(`${QUEUE_SERVICE_LOCAL}/api/tickets/history`);
    return res.data;
  }catch(e){
    console.error('fetchHistory error', e);
    return [];
  }
}

async function callNextSession(session){
  try{
    // Call via API Gateway to keep auth / logging centralized
    const res = await axios.post(`${API_GATEWAY}/api/admin/call-next`, { session });
    toast('Called ' + res.data.data.queue_number);
    return res.data.data;
  }catch(e){
    console.error('callNextSession error', e);
    toast('Gagal memanggil', 'error');
    return null;
  }
}

async function skipOrFinish(action, queueNumber){
  try{
    const res = await axios.post(`${QUEUE_SERVICE_LOCAL}/api/tickets/${action}`, { queue_number: queueNumber });
    toast(`${action} ${queueNumber}`);
    return res.data;
  }catch(e){
    toast('Gagal: ' + action, 'error');
  }
}

// UI update
function renderWaiting(list, elId){
  const el = document.getElementById(elId);
  el.innerHTML = '';
  list.forEach(item => {
    const li = document.createElement('li');
    li.textContent = item.queue_number;
    el.appendChild(li);
  });
}

function renderHistory(history){
  const tbody = document.getElementById('historyBody');
  tbody.innerHTML = '';
  history.forEach((row, idx) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td class="py-2">${idx+1}</td><td class="py-2">${row.queue_number}</td><td class="py-2">${row.session}</td><td class="py-2">${new Date(row.updated_at).toLocaleTimeString()}</td>`;
    tbody.appendChild(tr);
  });
}

// Initial load
async function loadAll(){
  const status = await fetchStatus();
  if(status){
    document.getElementById('currentCalled').innerText = status.current?.queue_number || '-';
    document.getElementById('currentSession').innerText = 'Session ' + (status.current?.session || '-');
    document.getElementById('waitingA').innerText = status.waitingA || 0;
    document.getElementById('waitingB').innerText = status.waitingB || 0;
    document.getElementById('lastA').innerText = status.lastA || '-';
    document.getElementById('lastB').innerText = status.lastB || '-';
    renderWaiting(status.listA || [], 'listA');
    renderWaiting(status.listB || [], 'listB');
  }

  const history = await fetchHistory();
  renderHistory(history || []);
}

// Bind actions
document.getElementById('callA').addEventListener('click', async function(){
  const ticket = await callNextSession('A');
  if(ticket){
    speak(`Nomor ${ticket.queue_number}, silakan menuju loket`);
  }
});

document.getElementById('callB').addEventListener('click', async function(){
  const ticket = await callNextSession('B');
  if(ticket){
    speak(`Nomor ${ticket.queue_number}, silakan menuju loket`);
  }
});

document.getElementById('refreshBtn').addEventListener('click', loadAll);

document.getElementById('skipBtn').addEventListener('click', async function(){
  const q = document.getElementById('skipInput').value.trim();
  if(!q) return toast('Masukkan nomor');
  await skipOrFinish('skip', q);
  loadAll();
});

document.getElementById('finishBtn').addEventListener('click', async function(){
  const q = document.getElementById('skipInput').value.trim();
  if(!q) return toast('Masukkan nomor');
  await skipOrFinish('finish', q);
  loadAll();
});

// Realtime: listen to broadcasted events
(function setupEcho(){
  // Configuration: connect to Reverb server running on service-queue container exposed to host at port 8080
  const echo = new window.Echo({
    broadcaster: 'socket.io',
    client: io,
    host: 'http://localhost:8080'
  });

  echo.channel('public-queue-channel')
    .listen('.queue.updated', (e) => {
      console.log('Realtime update', e);
      // Update UI quickly
      document.getElementById('currentCalled').innerText = e.ticket.queue_number;
      document.getElementById('currentSession').innerText = 'Session ' + e.ticket.session;
      // Reload full status history
      loadAll();
      speak(`Nomor ${e.ticket.queue_number}, silakan menuju loket`);
    });
})();

// start
loadAll();
</script>

</body>
</html>
