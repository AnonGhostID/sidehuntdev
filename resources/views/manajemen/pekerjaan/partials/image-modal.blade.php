{{-- Custom Image Modal --}}
<div id="imageModal" class="fixed inset-0 z-50 hidden overflow-auto bg-black bg-opacity-75 flex items-center justify-center p-4">
    <div class="relative max-w-4xl w-full bg-white rounded-lg shadow-xl">
        {{-- Modal Header --}}
        <div class="flex justify-between items-center p-4 border-b">
            <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Gambar</h3>
            <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeImageModal()">
                <span class="sr-only">Tutup</span>
                <i class="fa fa-times text-xl"></i>
            </button>
        </div>
        
        {{-- Modal Body --}}
        <div class="p-4">
            <div class="relative">
                {{-- Image Container --}}
                <div class="flex justify-center">
                    <img id="modalImage" src="" alt="Preview" class="max-h-[70vh] max-w-full object-contain">
                </div>
                
                {{-- Navigation Controls --}}
                <button id="prevButton" class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-r-lg hover:bg-opacity-70 focus:outline-none" onclick="showPrevImage()">
                    <i class="fa fa-chevron-left"></i>
                </button>
                <button id="nextButton" class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-l-lg hover:bg-opacity-70 focus:outline-none" onclick="showNextImage()">
                    <i class="fa fa-chevron-right"></i>
                </button>
            </div>
            
            {{-- Image Counter --}}
            <div class="mt-4 text-center text-gray-600">
                <span id="currentImageIndex">1</span> dari <span id="totalImages">1</span>
            </div>
        </div>
    </div>
</div>

<script>
    // Variables to track current image gallery
    let currentGallery = [];
    let currentIndex = 0;
    
    // Function to open the modal with a specific image
    function openImageModal(imageSrc, title, gallery = [], index = 0) {
        // Set the image source and title
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('modalTitle').textContent = title || 'Gambar';
        
        // Set the gallery and index
        currentGallery = gallery;
        currentIndex = index;
        
        // Update navigation visibility
        updateNavigation();
        
        // Show the modal
        document.getElementById('imageModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    
    // Function to close the modal
    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    
    // Function to show the next image
    function showNextImage() {
        if (currentIndex < currentGallery.length - 1) {
            currentIndex++;
            document.getElementById('modalImage').src = currentGallery[currentIndex];
            updateNavigation();
        }
    }
    
    // Function to show the previous image
    function showPrevImage() {
        if (currentIndex > 0) {
            currentIndex--;
            document.getElementById('modalImage').src = currentGallery[currentIndex];
            updateNavigation();
        }
    }
    
    // Function to update navigation buttons and counter
    function updateNavigation() {
        // Update counter
        document.getElementById('currentImageIndex').textContent = currentIndex + 1;
        document.getElementById('totalImages').textContent = currentGallery.length;
        
        // Update button visibility
        document.getElementById('prevButton').style.display = currentIndex > 0 ? 'block' : 'none';
        document.getElementById('nextButton').style.display = currentIndex < currentGallery.length - 1 ? 'block' : 'none';
    }
    
    // Close modal when clicking outside the content
    document.getElementById('imageModal').addEventListener('click', function(event) {
        if (event.target === this) {
            closeImageModal();
        }
    });
    
    // Add keyboard navigation
    document.addEventListener('keydown', function(event) {
        if (document.getElementById('imageModal').classList.contains('hidden')) {
            return;
        }
        
        if (event.key === 'ArrowLeft') {
            showPrevImage();
        } else if (event.key === 'ArrowRight') {
            showNextImage();
        } else if (event.key === 'Escape') {
            closeImageModal();
        }
    });
</script>