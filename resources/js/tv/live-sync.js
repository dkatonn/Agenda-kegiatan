document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.tv-container');

    if (!container) {
        return;
    }

    const stateUrl = container.dataset.tvStateUrl;
    const payloadUrl = container.dataset.tvPayloadUrl;
    const updateDelayMs = 1 * 60 * 1000;
    const pollingCheckMs = 5 * 1000;
    let currentRevision = container.dataset.tvRevision;
    let queuedRevision = null;
    let updateTimer = null;
    let isSyncing = false;
    let clearConsoleTimer = null;

    if (!stateUrl || !payloadUrl || !currentRevision) {
        return;
    }

    const notifyParent = (payload) => {
        if (window.parent && window.parent !== window) {
            window.parent.postMessage({
                source: 'tv-display',
                ...payload,
            }, window.location.origin);
        }
    };

    const clearQuietConsole = () => {
        window.clearTimeout(clearConsoleTimer);
        clearConsoleTimer = window.setTimeout(() => {
            console.clear();
            console.info('console cleared');
        }, 1200);
    };

    function applyPayload(payload) {
        const employeeSection = document.querySelector('#tv-employee-section');
        const videoSection = document.querySelector('#tv-video-section');
        const agendaTuSection = document.querySelector('#tv-agenda-tu-section');
        const agendaDataSection = document.querySelector('#tv-agenda-data-section');
        const runningTextSection = document.querySelector('#tv-runningtext-section');

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

        if (Object.prototype.hasOwnProperty.call(payload, 'backgroundUrl')) {
            if (payload.backgroundUrl) {
                container.style.backgroundImage = `url('${payload.backgroundUrl}')`;
                container.style.backgroundSize = 'cover';
                container.style.backgroundPosition = 'center';
                container.style.backgroundRepeat = 'no-repeat';
            } else {
                container.style.backgroundImage = '';
                container.style.backgroundSize = '';
                container.style.backgroundPosition = '';
                container.style.backgroundRepeat = '';
            }
        }

        if (typeof window.initEmployeeSlider === 'function') {
            window.initEmployeeSlider(document);
        }

        if (typeof window.initAgendaSlider === 'function') {
            window.initAgendaSlider(document);
        }
    }

    async function syncPayload(force = false) {
        if (isSyncing || document.hidden) {
            return;
        }

        isSyncing = true;

        try {
            let nextRevision = currentRevision;

            if (!force) {
                const stateResponse = await fetch(stateUrl, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        Accept: 'application/json',
                    },
                    cache: 'no-store',
                });

                if (!stateResponse.ok) {
                    return;
                }

                const statePayload = await stateResponse.json();
                nextRevision = statePayload.revision || currentRevision;

                if (nextRevision === currentRevision) {
                    return;
                }
            }

            const payloadResponse = await fetch(payloadUrl, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'application/json',
                },
                cache: 'no-store',
            });

            if (!payloadResponse.ok) {
                return;
            }

            const nextPayload = await payloadResponse.json();
            applyPayload(nextPayload);
            currentRevision = nextPayload.revision || queuedRevision || nextRevision || currentRevision;
            queuedRevision = null;
            container.dataset.tvRevision = currentRevision;
            notifyParent({ type: 'tv-sync-applied', revision: currentRevision });
            clearQuietConsole();
        } catch (error) {
            console.error('TV live sync failed:', error);
            notifyParent({ type: 'tv-sync-error', message: String(error) });
        } finally {
            isSyncing = false;
        }
    }

    function schedulePayloadSync(nextRevision, source) {
        if (!nextRevision || nextRevision === currentRevision) {
            return;
        }

        const shouldResetTimer = queuedRevision !== nextRevision;
        queuedRevision = nextRevision;

        if (shouldResetTimer && updateTimer) {
            window.clearTimeout(updateTimer);
        }

        if (!updateTimer || shouldResetTimer) {
            console.info('incoming update : refreshing in 1 minutes', {
                currentRevision,
                queuedRevision,
                source,
            });
            notifyParent({ type: 'tv-sync-queued', mode: source, revision: queuedRevision, delayMs: updateDelayMs });

            updateTimer = window.setTimeout(() => {
                updateTimer = null;
                syncPayload(true);
            }, updateDelayMs);
        }
    }

    if (window.Echo) {
        const reverbConnection = window.Echo.connector?.pusher?.connection;

        reverbConnection?.bind('connected', () => {
            console.info('reverb connected');
            notifyParent({ type: 'tv-reverb-connected' });
        });

        reverbConnection?.bind('error', (error) => {
            console.error('Reverb connection error', error);
            notifyParent({ type: 'tv-reverb-error', message: String(error) });
        });

        window.Echo.channel('tv.display')
            .listen('.tv.content.updated', (event) => {
                console.info('Reverb event received: tv.content.updated', event);
                schedulePayloadSync(event?.revision, 'reverb');
            });
    }

    window.setInterval(async () => {
        if (document.hidden || isSyncing) {
            return;
        }

        try {
            const response = await fetch(stateUrl, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'application/json',
                },
                cache: 'no-store',
            });

            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            schedulePayloadSync(payload.revision, 'polling-check');
        } catch (error) {
            console.error('TV polling check failed:', error);
        }
    }, pollingCheckMs);
});
