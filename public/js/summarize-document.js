document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("#document-upload-form");
    const button = form.querySelector("#summarize-button");

    button.addEventListener("click", (e) => {
        e.preventDefault()

        summarize();
    })

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

        loadingVideo.play?.();
        loadingOverlay.classList.remove('hidden');

        const res = await fetch(url, {
            method: "POST",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: formData
        });
        console.log(res)
        loadingVideo.pause?.();
        loadingOverlay.classList.add('hidden');
    }
})
