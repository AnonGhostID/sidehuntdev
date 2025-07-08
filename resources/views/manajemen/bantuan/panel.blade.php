@extends('layouts.management')

@section('title', 'Panel Bantuan dan Laporan Penipuan')
@section('page-title', 'Panel Bantuan dan Laporan Penipuan')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="{ activeTab: 'bantuan' }">

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 text-red-800 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 text-red-800 rounded">
            <strong class="font-bold">Terdapat kesalahan:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($user->isAdmin())
        <!-- Admin View -->
        <div class="bg-white p-6 rounded-lg shadow mb-8">
            <h2 class="text-xl font-semibold mb-4">Daftar Tiket Bantuan dan Laporan Penipuan</h2>
            
            <!-- Filter Tabs -->
            <div class="mb-4 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button @click="activeTab = 'all'" 
                            :class="activeTab === 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Semua Tiket
                    </button>
                    <button @click="activeTab = 'bantuan'" 
                            :class="activeTab === 'bantuan' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Tiket Bantuan
                    </button>
                    <button @click="activeTab = 'penipuan'" 
                            :class="activeTab === 'penipuan' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Laporan Penipuan
                    </button>
                </nav>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border">Tipe</th>
                            <th class="px-4 py-2 border">Subject</th>
                            <th class="px-4 py-2 border">Pengguna</th>
                            <th class="px-4 py-2 border">Deskripsi</th>
                            <th class="px-4 py-2 border">Status</th>
                            <th class="px-4 py-2 border">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                            <tr x-show="activeTab === 'all' || activeTab === '{{ $ticket->type }}'">
                                <td class="px-4 py-2 border">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $ticket->type === 'penipuan' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $ticket->type === 'penipuan' ? 'Laporan Penipuan' : 'Tiket Bantuan' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 border">{{ $ticket->subject }}</td>
                                <td class="px-4 py-2 border">{{ $ticket->user->nama }}</td>
                                <td class="px-4 py-2 border">
                                    <div class="max-w-xs">
                                        <p class="truncate">{{ $ticket->description }}</p>
                                        @if($ticket->type === 'penipuan')
                                            <div class="text-sm text-gray-500 mt-1">
                                                <p><strong>Pihak Terlapor:</strong> {{ $ticket->pihak_terlapor }}</p>
                                                <p><strong>Tanggal:</strong> {{ $ticket->tanggal_kejadian?->format('d/m/Y') }}</p>
                                                @if($ticket->bukti_pendukung && count($ticket->bukti_pendukung) > 0)
                                                    <div class="mt-2">
                                                        <p><strong>Bukti Pendukung:</strong></p>
                                                        <div class="mt-1 space-y-1">
                                                            @foreach($ticket->bukti_pendukung as $index => $filePath)
                                                                @php
                                                                    $fileName = basename($filePath);
                                                                    $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                                                    $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']);
                                                                    $isDocument = in_array($fileExtension, ['pdf', 'doc', 'docx', 'xls', 'xlsx']);
                                                                @endphp
                                                                <div class="flex items-center space-x-2">
                                                                    @if($isImage)
                                                                        <i class="fas fa-image text-blue-500"></i>
                                                                    @elseif($fileExtension === 'pdf')
                                                                        <i class="fas fa-file-pdf text-red-500"></i>
                                                                    @elseif(in_array($fileExtension, ['doc', 'docx']))
                                                                        <i class="fas fa-file-word text-blue-600"></i>
                                                                    @elseif(in_array($fileExtension, ['xls', 'xlsx']))
                                                                        <i class="fas fa-file-excel text-green-600"></i>
                                                                    @else
                                                                        <i class="fas fa-file text-gray-500"></i>
                                                                    @endif
                                                                    <a href="{{ asset('storage/' . $filePath) }}" 
                                                                       target="_blank" 
                                                                       class="text-blue-600 hover:text-blue-800 text-sm underline">
                                                                        {{ $fileName }}
                                                                    </a>
                                                                    @if($isImage)
                                                                        <button onclick="showImageModal('{{ asset('storage/' . $filePath) }}', '{{ $fileName }}')" 
                                                                                class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200">
                                                                            Preview
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-2 border">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $ticket->status === 'open' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst($ticket->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 border">
                                    @if($ticket->status === 'open')
                                        <form method="POST" action="{{ route('manajemen.bantuan.respond', $ticket->id) }}">
                                            @csrf
                                            <textarea name="admin_response" required class="w-full border rounded p-2 text-sm" 
                                                      placeholder="Masukkan respon admin..." rows="2"></textarea>
                                            <button type="submit" class="mt-2 bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                                                Tutup Tiket
                                            </button>
                                        </form>
                                    @else
                                        <p class="text-gray-700 text-sm">{{ $ticket->admin_response }}</p>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- User View -->
        <div class="bg-white p-6 rounded-lg shadow mb-8">
            <!-- Tab Navigation -->
            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button @click="activeTab = 'bantuan'" 
                            :class="activeTab === 'bantuan' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        <i class="fas fa-question-circle mr-2"></i>Tiket Bantuan
                    </button>
                    <button @click="activeTab = 'penipuan'" 
                            :class="activeTab === 'penipuan' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Lapor Penipuan
                    </button>
                </nav>
            </div>

            <!-- Bantuan Form -->
            <div x-show="activeTab === 'bantuan'" x-transition>
                <h2 class="text-xl font-semibold mb-4">Buat Tiket Bantuan</h2>
                <form method="POST" action="{{ route('manajemen.bantuan.store') }}">
                    @csrf
                    <input type="hidden" name="type" value="bantuan">
                    <div class="mb-4">
                        <label for="subject_bantuan" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                        <input id="subject_bantuan" name="subject" type="text" required maxlength="100"
                               class="w-full border border-gray-300 rounded-lg p-2" 
                               value="{{ old('subject') }}" placeholder="Masukkan subject bantuan">
                        <div class="text-right text-xs text-gray-500 mt-1">
                            <span id="subject_bantuan_count">0</span>/100 karakter
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="description_bantuan" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea id="description_bantuan" name="description" rows="4" required maxlength="1000"
                                  class="w-full border border-gray-300 rounded-lg p-2" 
                                  placeholder="Jelaskan masalah atau pertanyaan Anda">{{ old('description') }}</textarea>
                        <div class="text-right text-xs text-gray-500 mt-1">
                            <span id="description_bantuan_count">0</span>/1000 karakter
                        </div>
                    </div>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-paper-plane mr-2"></i>Kirim Tiket
                    </button>
                </form>
            </div>

            <!-- Penipuan Form -->
            <div x-show="activeTab === 'penipuan'" x-transition>
                <h2 class="text-xl font-semibold mb-4">Laporan Penipuan</h2>
                <form method="POST" action="{{ route('manajemen.bantuan.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type" value="penipuan">
                    
                    <div class="mb-4">
                        <label for="subject_penipuan" class="block text-sm font-medium text-gray-700 mb-1">Judul Laporan</label>
                        <input type="text" id="subject_penipuan" name="subject" required 
                               class="w-full border border-gray-300 rounded-lg p-2" 
                               value="{{ old('subject') }}" placeholder="Contoh: Penipuan oleh pengguna XXX">
                    </div>

                    <div class="mb-4">
                        <label for="pihak_terlapor" class="block text-sm font-medium text-gray-700 mb-1">Pihak Terlapor (Username atau Nama)</label>
                        <input type="text" id="pihak_terlapor" name="pihak_terlapor" required 
                               class="w-full border border-gray-300 rounded-lg p-2" 
                               value="{{ old('pihak_terlapor') }}" placeholder="Masukkan username atau nama pihak yang dilaporkan">
                    </div>

                    <div class="mb-4">
                        <label for="description_penipuan" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Kejadian</label>
                        <textarea id="description_penipuan" name="description" rows="5" required 
                                  class="w-full border border-gray-300 rounded-lg p-2" 
                                  placeholder="Jelaskan kronologi kejadian secara detail">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="tanggal_kejadian" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Perkiraan Kejadian</label>
                        <input type="date" id="tanggal_kejadian" name="tanggal_kejadian" required 
                               class="w-full border border-gray-300 rounded-lg p-2" 
                               value="{{ old('tanggal_kejadian') }}">
                    </div>

                    <div class="mb-6">
                        <label for="bukti_pendukung" class="block text-sm font-medium text-gray-700 mb-1">Bukti Pendukung (Opsional)</label>
                        <input type="file" id="bukti_pendukung" name="bukti_pendukung[]" multiple
                               accept=".pdf,.xlsx,.docx,.doc,.xls,.jpg,.jpeg,.png,.gif"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-500 mt-1">Anda dapat mengunggah file dokumen (PDF, XLSX, DOCX, DOC, XLS) dan gambar (JPG, JPEG, PNG, GIF). Maksimum 10 file, ukuran per file: 10MB.</p>
                    </div>

                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Kirim Laporan
                    </button>
                </form>
            </div>
        </div>

        <!-- User History -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Riwayat Tiket dan Laporan Anda</h2>
            
            <!-- Filter Tabs for History -->
            <div class="mb-4 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button @click="activeTab = 'history_all'" 
                            :class="activeTab === 'history_all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Semua
                    </button>
                    <button @click="activeTab = 'history_bantuan'" 
                            :class="activeTab === 'history_bantuan' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Bantuan
                    </button>
                    <button @click="activeTab = 'history_penipuan'" 
                            :class="activeTab === 'history_penipuan' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Penipuan
                    </button>
                </nav>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border">Tipe</th>
                            <th class="px-4 py-2 border">Subject</th>
                            <th class="px-4 py-2 border">Deskripsi</th>
                            <th class="px-4 py-2 border">Status</th>
                            <th class="px-4 py-2 border">Respon Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                            <tr x-show="activeTab === 'history_all' || activeTab === 'history_{{ $ticket->type }}'">
                                <td class="px-4 py-2 border">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $ticket->type === 'penipuan' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $ticket->type === 'penipuan' ? 'Penipuan' : 'Bantuan' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 border">{{ $ticket->subject }}</td>
                                <td class="px-4 py-2 border">
                                    <div class="max-w-xs">
                                        <p class="truncate">{{ $ticket->description }}</p>
                                        @if($ticket->type === 'penipuan')
                                            <div class="text-sm text-gray-500 mt-1">
                                                <p><strong>Pihak Terlapor:</strong> {{ $ticket->pihak_terlapor }}</p>
                                                <p><strong>Tanggal:</strong> {{ $ticket->tanggal_kejadian?->format('d/m/Y') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-2 border">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $ticket->status === 'open' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst($ticket->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 border">
                                    @if($ticket->status === 'closed')
                                        {{ $ticket->admin_response }}
                                    @else
                                        <span class="text-gray-500">Menunggu respon</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Image Preview Modal --}}
    <div id="imageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="modalTitle" class="text-lg font-medium text-gray-900">Preview Gambar</h3>
                    <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="text-center">
                    <img id="modalImage" src="" alt="Preview" class="max-w-full max-h-96 mx-auto rounded-lg shadow-lg">
                </div>
                <div class="mt-4 flex justify-end">
                    <button onclick="closeImageModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // File input validation for fraud reports
        const fileInput = document.getElementById('bukti_pendukung');
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                const files = this.files;
                const maxSizePerFile = 10 * 1024 * 1024; // 10MB
                const maxFiles = 10;
                const allowedTypes = ['pdf', 'xlsx', 'docx', 'doc', 'xls', 'jpg', 'jpeg', 'png', 'gif'];

                // Check number of files
                if (files.length > maxFiles) {
                    alert('Anda hanya dapat mengunggah maksimal ' + maxFiles + ' file. Anda memilih ' + files.length + ' file.');
                    this.value = ''; // Clear the input
                    return;
                }

                for (let i = 0; i < files.length; i++) {
                    // Check file size
                    if (files[i].size > maxSizePerFile) {
                        alert('Ukuran file ' + files[i].name + ' melebihi batas maksimum 10MB.');
                        this.value = ''; // Clear the input
                        return;
                    }

                    // Check file type
                    const fileExtension = files[i].name.split('.').pop().toLowerCase();
                    if (!allowedTypes.includes(fileExtension)) {
                        alert('Tipe file ' + files[i].name + ' tidak diizinkan. Hanya file PDF, XLSX, DOCX, DOC, XLS, JPG, JPEG, PNG, dan GIF yang diperbolehkan.');
                        this.value = ''; // Clear the input
                        return;
                    }
                }
            });
        }

        // Character counting for bantuan form
        const subjectBantuan = document.getElementById('subject_bantuan');
        const subjectBantuanCount = document.getElementById('subject_bantuan_count');
        const descriptionBantuan = document.getElementById('description_bantuan');
        const descriptionBantuanCount = document.getElementById('description_bantuan_count');

        // Update character count on page load
        if (subjectBantuan && subjectBantuanCount) {
            subjectBantuanCount.textContent = subjectBantuan.value.length;
            subjectBantuan.addEventListener('input', function() {
                subjectBantuanCount.textContent = this.value.length;
            });
        }

        if (descriptionBantuan && descriptionBantuanCount) {
            descriptionBantuanCount.textContent = descriptionBantuan.value.length;
            descriptionBantuan.addEventListener('input', function() {
                descriptionBantuanCount.textContent = this.value.length;
            });
        }
    });

    // Image modal functions
    function showImageModal(imageSrc, fileName) {
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('modalTitle').textContent = 'Preview: ' + fileName;
        document.getElementById('imageModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Close modal when clicking outside
    document.getElementById('imageModal').addEventListener('click', function(event) {
        if (event.target === this) {
            closeImageModal();
        }
    });

    // Close modal with ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && !document.getElementById('imageModal').classList.contains('hidden')) {
            closeImageModal();
        }
    });
</script>
@endpush
