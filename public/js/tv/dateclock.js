function updateClock() {
    const now = new Date();
    const clockElement = document.getElementById("clock");
    const dateElement = document.getElementById("date");

    if (clockElement) {
        const hours = String(now.getHours()).padStart(2, "0");
        const minutes = String(now.getMinutes()).padStart(2, "0");
        const seconds = String(now.getSeconds()).padStart(2, "0");

        clockElement.innerText = `${hours}:${minutes}:${seconds}`;
    }

    if (dateElement) {
        dateElement.innerText = now.toLocaleDateString("id-ID", {
            weekday: "long",
            day: "numeric",
            month: "long",
            year: "numeric",
        });
    }
}

setInterval(updateClock, 1000);
updateClock();
