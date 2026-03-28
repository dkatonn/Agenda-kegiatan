document.addEventListener("DOMContentLoaded", () => {
    const container = document.querySelector(".tv-container");
    if (!container) {
        return;
    }

    const stateUrl = container.dataset.tvStateUrl;
    const payloadUrl = container.dataset.tvPayloadUrl;
    let currentRevision = container.dataset.tvRevision;
    let isSyncing = false;

    if (!stateUrl || !payloadUrl || !currentRevision) {
        return;
    }

    function applyPayload(payload) {
        const employeeSection = document.querySelector("#tv-employee-section");
        const videoSection = document.querySelector("#tv-video-section");
        const agendaTuSection = document.querySelector("#tv-agenda-tu-section");
        const agendaDataSection = document.querySelector("#tv-agenda-data-section");
        const runningTextSection = document.querySelector("#tv-runningtext-section");

        if (employeeSection && payload.employeeHtml) {
            employeeSection.innerHTML = payload.employeeHtml;
        }

        if (videoSection && payload.videoHtml) {
            videoSection.innerHTML = payload.videoHtml;
        }

        if (agendaTuSection && payload.agendaTuHtml) {
            agendaTuSection.innerHTML = payload.agendaTuHtml;
        }

        if (agendaDataSection && payload.agendaDataHtml) {
            agendaDataSection.innerHTML = payload.agendaDataHtml;
        }

        if (runningTextSection && payload.runningTextHtml) {
            runningTextSection.innerHTML = payload.runningTextHtml;
        }

        if (Object.prototype.hasOwnProperty.call(payload, "backgroundUrl")) {
            if (payload.backgroundUrl) {
                container.style.backgroundImage = `url('${payload.backgroundUrl}')`;
                container.style.backgroundSize = "cover";
                container.style.backgroundPosition = "center";
                container.style.backgroundRepeat = "no-repeat";
            } else {
                container.style.backgroundImage = "";
                container.style.backgroundSize = "";
                container.style.backgroundPosition = "";
                container.style.backgroundRepeat = "";
            }
        }

        if (typeof window.initEmployeeSlider === "function") {
            window.initEmployeeSlider(document);
        }

        if (typeof window.initAgendaSlider === "function") {
            window.initAgendaSlider(document);
        }
    }

    async function checkTvUpdates() {
        if (document.hidden || isSyncing) {
            return;
        }

        try {
            const response = await fetch(stateUrl, {
                method: "GET",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Accept": "application/json",
                },
                cache: "no-store",
            });

            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            if (payload.revision && payload.revision !== currentRevision) {
                isSyncing = true;

                const payloadResponse = await fetch(payloadUrl, {
                    method: "GET",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Accept": "application/json",
                    },
                    cache: "no-store",
                });

                if (!payloadResponse.ok) {
                    isSyncing = false;
                    return;
                }

                const nextPayload = await payloadResponse.json();
                applyPayload(nextPayload);
                currentRevision = nextPayload.revision || currentRevision;
                container.dataset.tvRevision = currentRevision;
                isSyncing = false;
            }
        } catch (error) {
            isSyncing = false;
        }
    }

    setInterval(checkTvUpdates, 3000);
});
