
function _upload() {
    const CHUNK_SIZE = 3 * 1024 * 1024; // 3MB
    const MAX_CONCURRENT_UPLOADS = 3;
    const MAX_RETRIES = 3;
    const dropzone = document.getElementById('dropzone');
    const filesInput = document.getElementById('files');
    const progressContainer = document.getElementById('progress-container');

    const allowedExtensions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg',
        'mp4', 'avi', 'mov', 'mkv', 'webm', 'flv', 'wmv'
    ];

    // Xử lý sự kiện drag and drop
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.style.borderColor = '#2196F3';
    });

    dropzone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropzone.style.borderColor = '#ccc';
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.style.borderColor = '#ccc';
        handleFiles(e.dataTransfer.files);
    });

    dropzone.addEventListener('click', () => filesInput.click());
    filesInput.addEventListener('change', () => handleFiles(filesInput.files));

    function handleFiles(files) {
        let hasInvalidFile = false;
        
        Array.from(files).forEach(file => {
            const ext = file.name.split('.').pop().toLowerCase();
            if (!allowedExtensions.includes(ext)) {
                hasInvalidFile = true;
                return;
            }
            
            // File nhỏ (<1MB) thì upload trực tiếp, không chia chunk
            if (file.size < 3 * 1024 * 1024) {
                uploadWholeFile(file);
            } else {
                uploadFileInChunks(file);
            }
        });

        if (hasInvalidFile) {
            showInvalidFileWarning();
        }
    }

    function uploadWholeFile(file) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('fileName', file.name);
        formData.append('fileSize', file.size);
        formData.append('chunkNumber', 0);
        formData.append('totalChunks', 1);
        formData.append('folders', cf_path);
        formData.append('type', file.type);
        formData.append('act', "upload");

        const progressBar = createProgressBar(file.name);
        
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '<?php echo $u_upload;?>', true);
        
        xhr.upload.onprogress = (e) => {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                updateProgressBar(progressBar, percent, file.name);
            }
        };
        
        xhr.onload = () => {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    completeProgressBar(progressBar, file.name);
                } else {
                    showError(progressBar, response.error || 'Upload failed');
                }
            } else {
                showError(progressBar, 'Upload failed');
            }
        };
        
        xhr.onerror = () => showError(progressBar, 'Network error');
        xhr.send(formData);
    }

    function uploadFileInChunks(file) {
        const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
        let uploadedChunks = 0;
        const retries = Array(totalChunks).fill(0);
        const chunkQueue = [];
        const activeUploads = new Set();
        
        const progressBar = createProgressBar(file.name);

        // Tạo hàng đợi các chunk
        for (let i = 0; i < totalChunks; i++) {
            const start = i * CHUNK_SIZE;
            const end = Math.min(file.size, start + CHUNK_SIZE);
            chunkQueue.push({ start, end, chunkNumber: i });
        }

        function uploadChunk(chunkInfo) {
            const { start, end, chunkNumber } = chunkInfo;
            const chunk = file.slice(start, end);
            
            const formData = new FormData();
            formData.append('file', chunk);
            formData.append('fileName', file.name);
            formData.append('fileSize', file.size);
            formData.append('chunkNumber', chunkNumber);
            formData.append('totalChunks', totalChunks);
            formData.append('folders', cf_path);
            formData.append('type', file.type);
            formData.append('act', "upload");

            activeUploads.add(chunkNumber);
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo $u_upload;?>', true);
            
            xhr.onload = () => {
                activeUploads.delete(chunkNumber);
                
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        uploadedChunks++;
                        const percent = Math.round((uploadedChunks / totalChunks) * 100);
                        updateProgressBar(progressBar, percent, file.name);
                        
                        if (uploadedChunks === totalChunks) {
                            completeProgressBar(progressBar, file.name);
                        } else {
                            processNextChunk();
                        }
                    } else {
                        handleChunkError(chunkInfo);
                    }
                } else {
                    handleChunkError(chunkInfo);
                }
            };
            
            xhr.onerror = () => handleChunkError(chunkInfo);
            xhr.send(formData);
        }

        function handleChunkError(chunkInfo) {
            const { chunkNumber } = chunkInfo;
            activeUploads.delete(chunkNumber);
            
            if (retries[chunkNumber] < MAX_RETRIES) {
                retries[chunkNumber]++;
                console.log(`Retrying chunk ${chunkNumber} (attempt ${retries[chunkNumber]})`);
                uploadChunk(chunkInfo);
            } else {
                showError(progressBar, `Failed to upload chunk ${chunkNumber}`);
            }
        }

        function processNextChunk() {
            while (chunkQueue.length > 0 && activeUploads.size < MAX_CONCURRENT_UPLOADS) {
                uploadChunk(chunkQueue.shift());
            }
        }

        // Bắt đầu upload
        processNextChunk();
    }

    // Helper functions
    function createProgressBar(fileName) {
        const wrapper = document.createElement('div');
        wrapper.className = 'progress-bar';
        
        const bar = document.createElement('div');
        bar.className = 'progress-bar-fill';
        bar.style.width = '0%';
        bar.textContent = `${fileName}: 0%`;
        
        wrapper.appendChild(bar);
        progressContainer.appendChild(wrapper);
        
        return { wrapper, bar };
    }

    function updateProgressBar(progressBar, percent, fileName) {
        progressBar.bar.style.width = `${percent}%`;
        progressBar.bar.textContent = `${fileName}: ${percent}%`;
    }

    function completeProgressBar(progressBar, fileName) {
        progressBar.bar.style.width = '100%';
        progressBar.bar.textContent = `✅ ${fileName}`;
        progressBar.bar.style.backgroundColor = '#4CAF50';
    }

    function showError(progressBar, message) {
        progressBar.bar.style.backgroundColor = '#F44336';
        progressBar.bar.textContent = `❌ ${message}`;
    }

    function showInvalidFileWarning() {
        dropzone.style.backgroundColor = '#FF6B6B';
        setTimeout(() => {
            dropzone.style.backgroundColor = '';
        }, 2000);
    }
}