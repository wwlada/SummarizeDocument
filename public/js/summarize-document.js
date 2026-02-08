document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("#document-upload-form");
    const button = form.querySelector("#summarize-button");

    button.addEventListener("click", (e) => {
        e.preventDefault()

        summarize();
    })

    async function summarize() {
        const url = form.dataset.summarizeUrl;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const loadingOverlay = document.querySelector('#loadingOverlay');
        const loadingVideo = document.querySelector("#loadingVideo");
        const formData = new FormData(form);
        // i need to check if they exist

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
