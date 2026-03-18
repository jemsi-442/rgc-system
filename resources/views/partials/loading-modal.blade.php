<!-- Loading Modal -->
<div id="loadingModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 hidden z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300">
        <div class="p-6 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-primary-100 flex items-center justify-center">
                <i class="fas fa-file-export text-primary-600 text-2xl animate-pulse"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Inatengeneza Faili</h3>
            <p id="loadingMessage" class="text-sm text-gray-600 mb-4">Inasubiri...</p>
            
            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                <div id="progressBar" class="bg-primary-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
            
            <div class="text-xs text-gray-500">
                Tafadhali subiri, faili inatengenezwa...
            </div>
        </div>
    </div>
</div>
