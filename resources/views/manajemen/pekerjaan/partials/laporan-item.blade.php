{{-- Laporan Item Partial --}}
<div class="border rounded-lg p-4 bg-white shadow-sm hover:shadow-md transition-shadow">
    <div class="flex justify-between items-start mb-2">
        <h4 class="font-semibold text-gray-700">{{ $laporan->user->nama ?? 'Pekerja' }}</h4>
        <span class="text-sm text-gray-500">{{ $laporan->created_at->format('d M Y H:i') }}</span>
    </div>
    
    <p class="text-gray-600 mb-4">{{ $laporan->deskripsi }}</p>
    
    <div class="mb-4">
        <span class="font-medium text-gray-700 block mb-2">Foto Selfie:</span>
        @if($laporan->foto_selfie)
            <div 
                class="block w-32 h-32 cursor-pointer transition-transform hover:scale-[1.02] relative group"
                onclick="openImageModal('{{ asset('storage/'.$laporan->foto_selfie) }}', 'Selfie {{ $laporan->user->nama ?? 'Pekerja' }}')">
                <img src="{{ asset('storage/'.$laporan->foto_selfie) }}" 
                    alt="Selfie {{ $laporan->user->nama ?? 'Pekerja' }}" 
                    class="w-full h-full object-cover rounded-md border shadow-sm"
                    onerror="this.src='{{ asset('img/failed.svg') }}'; this.classList.add('p-2');">
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 flex items-center justify-center transition-all duration-200 rounded-md">
                    <i class="fa fa-search-plus text-white opacity-0 group-hover:opacity-100 text-xl drop-shadow-lg"></i>
                </div>
            </div>
        @else
            <div class="w-32 h-32 bg-gray-200 rounded-md flex items-center justify-center">
                <span class="text-gray-400 text-sm">Tidak ada foto</span>
            </div>
        @endif
    </div>
    
    <div>
        <span class="font-medium text-gray-700 block mb-2">Dokumentasi:</span>
        @if($laporan->foto_dokumentasi && json_decode($laporan->foto_dokumentasi, true))
            @php
                $dokumentasiImages = json_decode($laporan->foto_dokumentasi, true);
                $dokumentasiUrls = array_map(function($foto) {
                    return asset('storage/'.$foto);
                }, $dokumentasiImages);
            @endphp
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($dokumentasiImages as $index => $foto)
                    <div 
                        class="block cursor-pointer transition-transform hover:scale-[1.02] relative group"
                        onclick="openImageModal('{{ asset('storage/'.$foto) }}', 'Dokumentasi {{ $index + 1 }}', {{ json_encode($dokumentasiUrls) }}, {{ $index }})">
                        <img src="{{ asset('storage/'.$foto) }}" 
                            alt="Dokumentasi {{ $index + 1 }}" 
                            class="w-full h-32 object-cover rounded-md border shadow-sm"
                            onerror="this.src='{{ asset('img/failed.svg') }}'; this.classList.add('p-2');">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 flex items-center justify-center transition-all duration-200 rounded-md">
                            <i class="fa fa-search-plus text-white opacity-0 group-hover:opacity-100 text-xl drop-shadow-lg"></i>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-gray-500 text-sm">Tidak ada dokumentasi</div>
        @endif
    </div>
</div>