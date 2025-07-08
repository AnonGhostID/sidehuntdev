@extends('layouts.management')

@section('title', 'Upload Laporan Hasil Pekerjaan')
@section('page-title', 'Upload Laporan Hasil Pekerjaan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Formulir Upload Laporan</h2>
        <p class="text-gray-600 mb-6">
            Silakan lengkapi formulir di bawah ini untuk mengunggah laporan hasil pekerjaan Anda. Pastikan untuk menyertakan foto selfie dan dokumentasi pekerjaan sebagai bukti.
        </p>

        <form action="{{ route('manajemen.laporan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-6">
                {{-- Pilih Pekerjaan --}}
                <div>
                    <label for="pekerjaan_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Pekerjaan</label>
                    <select id="pekerjaan_id" name="pekerjaan_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                        <option value="">-- Pilih Pekerjaan yang Dilaporkan --</option>
                        @forelse($jobs as $job)
                            <option value="{{ $job->id }}" @selected(old('pekerjaan_id') == $job->id)>
                                {{ $job->nama }}
                            </option>
                        @empty
                            <option value="" disabled>Tidak ada pekerjaan yang tersedia untuk dilaporkan</option>
                        @endforelse
                    </select>
                    @if($jobs->isEmpty())
                        <p class="mt-2 text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Tidak ada pekerjaan yang sedang berlangsung untuk dilaporkan. Pekerjaan yang sudah selesai tidak dapat dilaporkan lagi.
                        </p>
                    @endif
                </div>

                {{-- Deskripsi Laporan --}}
                <div>
                    <label for="deskripsi_laporan" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Laporan</label>
                    <textarea id="deskripsi_laporan" name="deskripsi_laporan" rows="4" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Jelaskan progres dan hasil pekerjaan Anda..." required>{{ old('deskripsi_laporan') }}</textarea>
                </div>

                {{-- Upload Foto Selfie --}}
                <div>
                    <label for="foto_selfie" class="block text-sm font-medium text-gray-700 mb-1">Upload Foto Selfie (Bukti Pengerjaan)</label>
                    <input type="file" id="foto_selfie" name="foto_selfie" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required accept="image/*">
                    <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG. Maks: 5MB.</p>
                    <div id="foto_selfie_error" class="mt-1 text-xs text-red-600 hidden"></div>
                </div>

                {{-- Upload Dokumentasi Pekerjaan --}}
                <div>
                    <label for="dokumentasi_pekerjaan" class="block text-sm font-medium text-gray-700 mb-1">Upload Dokumentasi Pekerjaan (File/Screenshot)</label>
                    <input type="file" id="dokumentasi_pekerjaan" name="dokumentasi_pekerjaan[]" multiple class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" accept="image/*" required>
                    <p class="mt-1 text-xs text-gray-500">Unggah satu atau lebih gambar bukti pekerjaan. Format: JPG atau PNG. Maks: 5MB per file. Maksimal 10 file.</p>
                    <div id="dokumentasi_pekerjaan_error" class="mt-1 text-xs text-red-600 hidden"></div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex justify-end pt-4">
                    <button type="button" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg mr-3 transition-colors duration-300">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition-colors duration-300 disabled:bg-gray-400 disabled:cursor-not-allowed" @if($jobs->isEmpty()) disabled @endif>
                        <i class="fas fa-upload mr-2"></i>Upload Laporan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script untuk halaman upload laporan
    console.log('Halaman Upload Laporan dimuat.');
    
    // Konstanta untuk ukuran file maksimum (5MB dalam bytes)
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    
    // Maksimal jumlah file dokumentasi yang dapat diunggah
    const MAX_DOCUMENTATION_FILES = 10;
    
    // Ekstensi file yang diizinkan
    const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png'];
    const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/jpg', 'image/png'];
    
    // Fungsi untuk memformat ukuran file
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Fungsi untuk mendapatkan ekstensi file
    function getFileExtension(filename) {
        return filename.slice((filename.lastIndexOf(".") - 1 >>> 0) + 2).toLowerCase();
    }
    
    // Fungsi untuk validasi ekstensi file
    function validateFileExtension(file) {
        const extension = getFileExtension(file.name);
        const mimeType = file.type.toLowerCase();
        
        return ALLOWED_EXTENSIONS.includes(extension) && ALLOWED_MIME_TYPES.includes(mimeType);
    }
    
    // Fungsi untuk validasi file lengkap (ukuran dan ekstensi)
    function validateFile(file, errorElementId) {
        const errorElement = document.getElementById(errorElementId);
        let errors = [];
        
        // Validasi ekstensi file
        if (!validateFileExtension(file)) {
            const extension = getFileExtension(file.name);
            errors.push(`Format file tidak didukung (${extension.toUpperCase()}). Hanya JPG, JPEG, dan PNG yang diizinkan.`);
        }
        
        // Validasi ukuran file
        if (file.size > MAX_FILE_SIZE) {
            errors.push(`Ukuran file terlalu besar (${formatFileSize(file.size)}). Maksimal 5MB.`);
        }
        
        if (errors.length > 0) {
            errorElement.innerHTML = errors.join('<br>');
            errorElement.classList.remove('hidden');
            return false;
        } else {
            errorElement.textContent = '';
            errorElement.classList.add('hidden');
            return true;
        }
    }
    
    // Event listener untuk foto selfie
    document.getElementById('foto_selfie').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const isValid = validateFile(file, 'foto_selfie_error');
            if (!isValid) {
                e.target.value = ''; // Reset input jika file tidak valid
            }
        }
    });
    
    // Event listener untuk dokumentasi pekerjaan
    document.getElementById('dokumentasi_pekerjaan').addEventListener('change', function(e) {
        const files = e.target.files;
        let allValid = true;
        let errorMessages = [];
        
        // Validasi jumlah file
        if (files.length > MAX_DOCUMENTATION_FILES) {
            allValid = false;
            errorMessages.push(`Terlalu banyak file dipilih (${files.length}). Maksimal ${MAX_DOCUMENTATION_FILES} file.`);
        }
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            // Validasi ekstensi file
            if (!validateFileExtension(file)) {
                const extension = getFileExtension(file.name);
                allValid = false;
                errorMessages.push(`File "${file.name}" memiliki format tidak didukung (${extension.toUpperCase()}). Hanya JPG, JPEG, dan PNG yang diizinkan.`);
            }
            
            // Validasi ukuran file
            if (file.size > MAX_FILE_SIZE) {
                allValid = false;
                errorMessages.push(`File "${file.name}" terlalu besar (${formatFileSize(file.size)}). Maksimal 5MB.`);
            }
        }
        
        const errorElement = document.getElementById('dokumentasi_pekerjaan_error');
        if (!allValid) {
            errorElement.innerHTML = errorMessages.join('<br>');
            errorElement.classList.remove('hidden');
            e.target.value = ''; // Reset input jika ada file yang tidak valid
        } else {
            errorElement.textContent = '';
            errorElement.classList.add('hidden');
        }
    });
    
    // Validasi sebelum form submission
    document.querySelector('form').addEventListener('submit', function(e) {
        const fotoSelfie = document.getElementById('foto_selfie').files[0];
        const dokumentasiFiles = document.getElementById('dokumentasi_pekerjaan').files;
        
        let hasError = false;
        let errorMessages = [];
        
        // Validasi foto selfie
        if (fotoSelfie) {
            if (!validateFileExtension(fotoSelfie)) {
                const extension = getFileExtension(fotoSelfie.name);
                errorMessages.push(`Foto selfie memiliki format tidak didukung (${extension.toUpperCase()}). Hanya JPG, JPEG, dan PNG yang diizinkan.`);
                hasError = true;
            }
            if (fotoSelfie.size > MAX_FILE_SIZE) {
                errorMessages.push(`Foto selfie terlalu besar (${formatFileSize(fotoSelfie.size)}). Maksimal 5MB.`);
                hasError = true;
            }
        }
        
        // Validasi dokumentasi pekerjaan
        if (dokumentasiFiles.length > MAX_DOCUMENTATION_FILES) {
            errorMessages.push(`Terlalu banyak file dokumentasi dipilih (${dokumentasiFiles.length}). Maksimal ${MAX_DOCUMENTATION_FILES} file.`);
            hasError = true;
        }
        
        for (let i = 0; i < dokumentasiFiles.length; i++) {
            const file = dokumentasiFiles[i];
            if (!validateFileExtension(file)) {
                const extension = getFileExtension(file.name);
                errorMessages.push(`File dokumentasi "${file.name}" memiliki format tidak didukung (${extension.toUpperCase()}). Hanya JPG, JPEG, dan PNG yang diizinkan.`);
                hasError = true;
            }
            if (file.size > MAX_FILE_SIZE) {
                errorMessages.push(`File dokumentasi "${file.name}" terlalu besar (${formatFileSize(file.size)}). Maksimal 5MB.`);
                hasError = true;
            }
        }
        
        if (hasError) {
            e.preventDefault();
            alert('Terdapat masalah dengan file yang dipilih:\n\n' + errorMessages.join('\n') + '\n\nHarap perbaiki sebelum melanjutkan.');
        }
    });
</script>
@endpush
