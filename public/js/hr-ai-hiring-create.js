(function () {
    const dropZone = document.getElementById('jd-drop-zone');
    const fileInput = document.getElementById('jd-file-input');
    const fileLabel = document.getElementById('jd-file-label');

    if (!dropZone || !fileInput) return;

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-[#8B5CF6]');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-[#8B5CF6]');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-[#8B5CF6]');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            updateLabel();
        }
    });

    fileInput.addEventListener('change', updateLabel);

    function updateLabel() {
        if (fileInput.files.length && fileLabel) {
            fileLabel.textContent = fileInput.files[0].name;
        }
    }
})();
