    function updateClock() {
            const now = new Date();

    document.getElementById("clock").innerText =
    now.toLocaleTimeString("id-ID");

    document.getElementById("date").innerText =
    now.toLocaleDateString("id-ID", {
        weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric"
                });
        }

    setInterval(updateClock, 1000);
    updateClock();
