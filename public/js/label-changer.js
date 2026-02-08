document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('document');
    const fileNameLabel = document.getElementById('file-name-label');

    if (fileInput && fileNameLabel) {
        fileInput.addEventListener('change', function () {
            if (fileInput.files && fileInput.files.length > 0) {
                fileNameLabel.textContent = fileInput.files[0].name;
            } else {
                fileNameLabel.textContent = 'No file selected yet';
            }
        });
    }
});
