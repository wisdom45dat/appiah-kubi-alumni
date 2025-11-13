@extends('layouts.app')

@section('title', 'Upload Media - ' . $album->title)

@section('subtitle', 'Add photos and videos to your album')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Upload to: {{ $album->title }}</h2>
        
        <form id="uploadForm" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Files</label>
                <input type="file" id="fileInput" name="files[]" multiple 
                       accept="image/*,video/*"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="mt-1 text-sm text-gray-500">Select multiple photos or videos (Max: 50MB per file)</p>
            </div>

            <div id="filePreview" class="mb-6 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Selected Files</label>
                <div id="previewContainer" class="grid grid-cols-2 md:grid-cols-3 gap-4"></div>
            </div>

            <div class="flex justify-end space-x-3">
                <!-- FIXED: Using the correct route name 'public.album' -->
                <a href="{{ route('public.album', $album) }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" id="uploadButton"
                        class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-upload mr-2"></i>
                    Upload Files
                </button>
            </div>
        </form>

        <div id="uploadProgress" class="mt-6 hidden">
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-medium mb-2">Upload Progress</h3>
                <div id="progressBar" class="w-full bg-gray-200 rounded-full h-2 mb-2">
                    <div id="progressFill" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <div id="progressText" class="text-sm text-gray-600">0%</div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('fileInput');
    const previewContainer = document.getElementById('previewContainer');
    const filePreview = document.getElementById('filePreview');
    const uploadForm = document.getElementById('uploadForm');
    const uploadButton = document.getElementById('uploadButton');
    const uploadProgress = document.getElementById('uploadProgress');
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');

    fileInput.addEventListener('change', function(e) {
        previewContainer.innerHTML = '';
        const files = e.target.files;
        
        if (files.length > 0) {
            filePreview.classList.remove('hidden');
            
            for (let file of files) {
                const reader = new FileReader();
                const previewItem = document.createElement('div');
                previewItem.className = 'border rounded-lg p-2';
                
                if (file.type.startsWith('image/')) {
                    reader.onload = function(e) {
                        previewItem.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-24 object-cover rounded mb-2">
                            <p class="text-xs text-gray-600 truncate">${file.name}</p>
                        `;
                    };
                } else if (file.type.startsWith('video/')) {
                    previewItem.innerHTML = `
                        <div class="w-full h-24 bg-gray-200 rounded mb-2 flex items-center justify-center">
                            <i class="fas fa-video text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-xs text-gray-600 truncate">${file.name}</p>
                    `;
                }
                
                previewContainer.appendChild(previewItem);
                reader.readAsDataURL(file);
            }
        } else {
            filePreview.classList.add('hidden');
        }
    });

    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const files = fileInput.files;
        if (files.length === 0) {
            alert('Please select at least one file to upload.');
            return;
        }

        uploadButton.disabled = true;
        uploadProgress.classList.remove('hidden');

        const formData = new FormData();
        for (let file of files) {
            formData.append('files[]', file);
        }

        // Simulate upload progress (in real app, you'd use XMLHttpRequest with progress event)
        let progress = 0;
        const interval = setInterval(() => {
            progress += 10;
            progressFill.style.width = progress + '%';
            progressText.textContent = progress + '%';
            
            if (progress >= 100) {
                clearInterval(interval);
                // In real app, you'd submit the form here
                alert('Upload functionality would be implemented here with proper backend handling.');
                uploadButton.disabled = false;
            }
        }, 200);
    });
});
</script>
@endsection