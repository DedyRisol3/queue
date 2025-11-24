<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Ambil Nomor Antrian</title>
</head>
<body>

<h1>Ambil Nomor Antrian</h1>

<button id="btn" style="padding:20px; font-size:20px;">
    Ambil Antrian
</button>

<h2 id="result"></h2>

<script>
document.getElementById('btn').onclick = async function () {
    const res = await fetch("http://localhost:8000/api/queue/take", {
        method: "POST"
    });

    const data = await res.json();

    document.getElementById('result').innerText =
        "Nomor Antrian Anda: " + data.queue_number;
};
</script>

</body>
</html>
