document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("#document-upload-form");
    const button = form.querySelector("#summarize-button");

    button.addEventListener("click", (e) => {
        e.preventDefault();
        summarize();
    });

    async function summarize() {
        const url = form.dataset.summarizeUrl;
        const loadingOverlay = document.querySelector('#loadingOverlay');
        const loadingVideo = document.querySelector("#loadingVideo");
        const formData = new FormData(form);
        if (!url || !loadingOverlay || !loadingVideo || !formData) return;

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        if (!token) {
            console.error("No CSRF token found");
            return;
        }

        const fileInput = document.getElementById('document');
        const fileName = fileInput?.files?.[0]?.name ?? 'Uploaded document';

        loadingVideo.play?.();
        loadingOverlay.classList.remove('hidden');

        try {
            const res = await fetch(url, {
                method: "POST",
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: formData
            });

            const data = await res.json();

            if (!res.ok) {
                showError(data.message ?? 'Something went wrong. Please try again.');
                return;
            }

            showSummary(fileName, data);
        } catch (err) {
            showError('Network error. Please check your connection and try again.');
        } finally {
            loadingVideo.pause?.();
            loadingOverlay.classList.add('hidden');
        }
    }

    function showSummary(fileName, data) {
        document.getElementById('summary-title').textContent = fileName;
        document.getElementById('summary-status').textContent = 'Ready';
        document.getElementById('summary-updated-at').textContent = data.lastTimeProcessed;
        document.getElementById('languageOutputSpan').textContent = data.aiSummary.language;
        document.getElementById('avgResponseTimeSpan').textContent = data.avgResponseTime;

        const body = document.getElementById('summary-body');
        body.innerHTML = '';
        const p = document.createElement('p');
        p.style.cssText = 'white-space: pre-wrap; margin: 0;';
        p.textContent = data.aiSummary.body;
        body.appendChild(p);
    }

    function showError(message) {
        document.getElementById('summary-title').textContent = 'Error';
        document.getElementById('summary-status').textContent = 'Failed';
        document.getElementById('summary-updated-at').textContent = new Date().toLocaleTimeString();

        const body = document.getElementById('summary-body');
        body.innerHTML = '';
        const p = document.createElement('p');
        p.style.color = 'red';
        p.textContent = message;
        body.appendChild(p);
    }
});
