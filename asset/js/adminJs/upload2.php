class AdvancedUploadManager {
    constructor() {
        // Configuration
        this.CHUNK_SIZE = 5 * 1024 * 1024; // 5MB
        this.MAX_RETRIES = 3;
        this.CONCURRENT_SMALL_FILES = 3;
        this.CONCURRENT_CHUNKS = 3;
        this.RETRY_DELAY = 1000; // 1 second
        
        // State
        this.uploadQueue = [];
        this.activeUploads = 0;
        this.paused = false;
        this.totalFiles = 0;
        this.completedFiles = 0;
        
        // Initialize UI
        this.initUI();
    }
    
    initUI() {
        this.ui = {
            fileInput: $('#fileInput'),
            uploadButton: $('#uploadButton'),
            fileList: $('#fileList'),
            pauseResumeBtn: $('#pauseResumeBtn'),
            statusDisplay: $('#statusDisplay'),
            globalProgress: $('#globalProgress'),
            globalProgressBar: $('#globalProgress .progress'),
            globalProgressText: $('#globalProgress .progress-text')
        };
        
        // Event handlers
        this.ui.fileInput.on('change', () => this.handleFileSelect());
        this.ui.uploadButton.on('click', () => this.startUpload());
        this.ui.pauseResumeBtn.on('click', () => this.togglePause());
    }
    
    handleFileSelect() {
        const files = this.ui.fileInput[0].files;
        if (!files.length) return;
        
        // Clear previous list
        this.ui.fileList.empty();
        
        // Display new file list
        Array.from(files).forEach((file, index) => {
            this.ui.fileList.append(`
                <div class="file-item" data-file-id="${index}">
                    <div class="file-name">${file.name}</div>
                    <div class="file-size">${this.formatFileSize(file.size)}</div>
                    <div class="progress-bar">
                        <div class="progress" style="width: 0%"></div>
                    </div>
                    <div class="file-status waiting">Waiting</div>
                </div>
            `);
        });
        
        // Enable/disable buttons based on selection
        this.ui.uploadButton.prop('disabled', false);
        this.ui.pauseResumeBtn.prop('disabled', true);
    }
    
    startUpload() {
        const files = this.ui.fileInput[0].files;
        const folders = $('#path_folders').val();
        
        if (!files.length) {
            this.showAlert('Please select at least one file!');
            return;
        }
        
        // Reset progress
        this.completedFiles = 0;
        this.totalFiles = files.length;
        this.updateGlobalProgress(0);
        this.ui.globalProgress.show();
        
        // Prepare upload queue
        this.uploadQueue = Array.from(files).map((file, index) => ({
            file,
            fileId: `file_${Date.now()}_${index}`,
            folderId: folders,
            isSmallFile: file.size <= this.CHUNK_SIZE,
            priority: file.size <= this.CHUNK_SIZE ? 2 : 1, // Higher priority for small files
            uploadedChunks: [],
            retries: 0,
            index,
            status: 'waiting'
        }));
        
        // Sort queue: small files first, then by size
        this.uploadQueue.sort((a, b) => {
            if (a.priority !== b.priority) return b.priority - a.priority;
            return a.file.size - b.file.size;
        });
        
        // Update UI
        this.ui.uploadButton.prop('disabled', true);
        this.ui.pauseResumeBtn.prop('disabled', false);
        this.ui.statusDisplay.text('Uploading...');
        
        // Start processing
        this.processQueue();
    }
    
    async processQueue() {
        if (this.paused) return;
        
        // Check if we can process more items
        const availableSlots = this.CONCURRENT_SMALL_FILES - this.activeUploads;
        if (availableSlots <= 0) return;
        
        // Find next items to process
        const nextItems = this.findNextItems(availableSlots);
        if (nextItems.length === 0) {
            // Check if all uploads are complete
            if (this.activeUploads === 0 && this.uploadQueue.length === 0) {
                this.uploadComplete();
            }
            return;
        }
        
        // Process items
        this.activeUploads += nextItems.length;
        nextItems.forEach(item => {
            item.status = 'uploading';
            this.updateFileStatus(item.index, 'Uploading...', 'uploading');
            
            if (item.isSmallFile) {
                this.uploadSmallFile(item)
                    .finally(() => {
                        this.activeUploads--;
                        setTimeout(() => this.processQueue(), 100);
                    });
            } else {
                this.uploadLargeFile(item)
                    .finally(() => {
                        this.activeUploads--;
                        setTimeout(() => this.processQueue(), 100);
                    });
            }
        });
    }
    
    findNextItems(count) {
        const items = [];
        let found = 0;
        
        // First look for small files with retries available
        for (const item of this.uploadQueue) {
            if (found >= count) break;
            if (item.isSmallFile && item.retries < this.MAX_RETRIES && item.status === 'waiting') {
                items.push(item);
                found++;
            }
        }
        
        // Then look for any files with retries available
        if (found < count) {
            for (const item of this.uploadQueue) {
                if (found >= count) break;
                if (item.retries < this.MAX_RETRIES && item.status === 'waiting' && !items.includes(item)) {
                    items.push(item);
                    found++;
                }
            }
        }
        
        return items;
    }
    
    async uploadSmallFile(item) {
        try {
            const formData = new FormData();
            formData.append('file', item.file);
            formData.append('fileId', item.fileId);
            formData.append('fileName', item.file.name);
            formData.append('folders', item.folderId);
            formData.append('fileSize', item.file.size);
            formData.append('type', item.file.type || item.file.name.split('.').pop());
            
            const response = await this.ajaxRequest('/ajax/upload2_1.php', formData);
            
            this.updateFileStatus(item.index, 'Completed', 'success');
            this.updateFileProgress(item.index, 100);
            this.fileCompleted();
            
            return response;
        } catch (error) {
            console.error(`Upload failed: ${error}`);
            this.updateFileStatus(item.index, `Error: ${error.message}`, 'error');
            
            if (item.retries < this.MAX_RETRIES) {
                item.retries++;
                item.status = 'waiting';
                setTimeout(() => this.processQueue(), this.RETRY_DELAY);
            } else {
                this.fileCompleted();
            }
            
            throw error;
        }
    }
    
    async uploadLargeFile(item) {
        const totalChunks = Math.ceil(item.file.size / this.CHUNK_SIZE);
        
        try {
            // Upload chunks
            for (let i = 0; i < totalChunks; i++) {
                if (this.paused) break;
                if (item.uploadedChunks.includes(i)) continue;
                
                try {
                    await this.uploadChunk(item, i, totalChunks);
                    item.uploadedChunks.push(i);
                    
                    const percent = Math.round((item.uploadedChunks.length / totalChunks) * 100);
                    this.updateFileProgress(item.index, percent);
                    this.updateFileStatus(item.index, `Uploading (${percent}%)`, 'uploading');
                } catch (error) {
                    console.error(`Chunk ${i} failed:`, error);
                    throw error;
                }
            }
            
            if (this.paused) return;
            
            // Merge file if all chunks uploaded
            if (item.uploadedChunks.length === totalChunks) {
                this.updateFileStatus(item.index, 'Merging...', 'merging');
                await this.mergeFile(item);
                this.updateFileStatus(item.index, 'Completed', 'success');
                this.fileCompleted();
            }
        } catch (error) {
            console.error(`File upload failed: ${error}`);
            this.updateFileStatus(item.index, `Error: ${error.message}`, 'error');
            
            if (item.retries < this.MAX_RETRIES) {
                item.retries++;
                item.status = 'waiting';
                setTimeout(() => this.processQueue(), this.RETRY_DELAY);
            } else {
                this.fileCompleted();
            }
            
            throw error;
        }
    }
    
    async uploadChunk(item, chunkIndex, totalChunks) {
        const start = chunkIndex * this.CHUNK_SIZE;
        const end = Math.min(start + this.CHUNK_SIZE, item.file.size);
        const chunk = item.file.slice(start, end);
        
        // Calculate chunk hash
        const chunkHash = await this.calculateHash(chunk);
        
        const formData = new FormData();
        formData.append('fileId', item.fileId);
        formData.append('chunkIndex', chunkIndex);
        formData.append('totalChunks', totalChunks);
        formData.append('fileName', item.file.name);
        formData.append('folders', item.folderId);
        formData.append('fileSize', item.file.size);
        formData.append('type', item.file.type || item.file.name.split('.').pop());
        formData.append('chunk', chunk, item.file.name);
        formData.append('chunkHash', chunkHash);
        
        return this.ajaxRequest('/ajax/upload2.php', formData);
    }
    
    async mergeFile(item) {
        const formData = new FormData();
        formData.append('fileId', item.fileId);
        formData.append('fileName', item.file.name);
        formData.append('folders', item.folderId);
        formData.append('fileSize', item.file.size);
        formData.append('type', item.file.type || item.file.name.split('.').pop());
        
        return this.ajaxRequest('/ajax/upload2_1.php', formData);
    }
    
    ajaxRequest(url, data) {
        return new Promise((resolve, reject) => {
            const isFormData = data instanceof FormData;
            
            $.ajax({
                url,
                type: 'POST',
                data,
                processData: !isFormData,
                contentType: isFormData ? false : 'application/x-www-form-urlencoded',
                timeout: 120000,
                success: (response) => {
                    if (response.error) {
                        reject(new Error(response.error));
                    } else {
                        resolve(response);
                    }
                },
                error: (xhr, status, error) => {
                    let errorMsg = error;
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    }
                    reject(new Error(errorMsg || 'Request failed'));
                }
            });
        });
    }
    
    async calculateHash(blob) {
        return new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = () => {
                const buffer = reader.result;
                const hashArray = Array.from(new Uint8Array(
                    crypto.subtle.digest('MD5', buffer)
                ));
                const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
                resolve(hashHex);
            };
            reader.readAsArrayBuffer(blob);
        });
    }
    
    togglePause() {
        this.paused = !this.paused;
        this.ui.pauseResumeBtn.text(this.paused ? 'Resume' : 'Pause');
        this.ui.statusDisplay.text(this.paused ? 'Paused' : 'Uploading...');
        
        if (!this.paused) {
            this.processQueue();
        }
    }
    
    fileCompleted() {
        this.completedFiles++;
        this.updateGlobalProgress((this.completedFiles / this.totalFiles) * 100);
    }
    
    uploadComplete() {
        this.ui.statusDisplay.text('Upload complete!');
        this.ui.pauseResumeBtn.prop('disabled', true);
        this.ui.uploadButton.prop('disabled', false);
        
        // Reset after 5 seconds
        setTimeout(() => {
            this.ui.statusDisplay.text('Ready to upload');
            this.ui.globalProgress.hide();
        }, 5000);
    }
    
    updateFileStatus(index, text, status) {
        const itemElement = $(`.file-item[data-file-id="${index}"] .file-status`);
        itemElement
            .text(text)
            .removeClass('waiting uploading merging success error')
            .addClass(status.toLowerCase());
    }
    
    updateFileProgress(index, percent) {
        $(`.file-item[data-file-id="${index}"] .progress`).css('width', `${percent}%`);
    }
    
    updateGlobalProgress(percent) {
        this.ui.globalProgressBar.css('width', `${percent}%`);
        this.ui.globalProgressText.text(`${Math.round(percent)}% complete`);
    }
    
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    showAlert(message) {
        alert(message);
    }
}

// Initialize when DOM is ready
$(document).ready(function() {
    window.uploadManager = new AdvancedUploadManager();
});