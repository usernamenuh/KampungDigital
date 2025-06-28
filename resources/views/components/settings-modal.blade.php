<!-- Enhanced Settings Modal -->
<div x-show="showSettingsModal" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
     x-cloak>
    
    <div x-show="showSettingsModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="closeSettings()"
         class="bg-white rounded-2xl shadow-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800">Pengaturan</h2>
            <button @click="closeSettings()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <i data-lucide="x" class="w-5 h-5 text-gray-500"></i>
            </button>
        </div>

        <!-- Settings Content -->
        <div class="space-y-8">
            <!-- Theme Settings -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Tema Aplikasi</h3>
                
                <!-- Theme Options -->
                <div class="space-y-3 mb-6">
                    <!-- Light Mode -->
                    <div @click="isDarkMode = false; toggleDarkMode()" 
                         :class="{ 'ring-2 ring-purple-500 bg-purple-50': !isDarkMode }"
                         class="flex items-center p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-all">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-yellow-100 rounded-lg">
                                <i data-lucide="sun" class="w-5 h-5 text-yellow-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">Mode Terang</p>
                                <p class="text-sm text-gray-500">Tema terang untuk penggunaan siang hari</p>
                            </div>
                        </div>
                        <div class="ml-auto">
                            <div :class="{ 'bg-purple-500': !isDarkMode, 'bg-gray-300': isDarkMode }" 
                                 class="w-5 h-5 rounded-full border-2 border-white shadow-sm transition-colors">
                                <div x-show="!isDarkMode" class="w-full h-full bg-white rounded-full scale-50"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Dark Mode -->
                    <div @click="isDarkMode = true; toggleDarkMode()" 
                         :class="{ 'ring-2 ring-purple-500 bg-purple-50': isDarkMode }"
                         class="flex items-center p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-all">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-gray-800 rounded-lg">
                                <i data-lucide="moon" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">Mode Gelap</p>
                                <p class="text-sm text-gray-500">Tema gelap untuk penggunaan malam hari</p>
                            </div>
                        </div>
                        <div class="ml-auto">
                            <div :class="{ 'bg-purple-500': isDarkMode, 'bg-gray-300': !isDarkMode }" 
                                 class="w-5 h-5 rounded-full border-2 border-white shadow-sm transition-colors">
                                <div x-show="isDarkMode" class="w-full h-full bg-white rounded-full scale-50"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Color Customization -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Kustomisasi Warna</h3>
                
                <!-- Active Color -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Warna Aktif</label>
                    <div class="grid grid-cols-6 gap-3">
                        <template x-for="color in ['#8B5CF6', '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#EC4899']" :key="color">
                            <button @click="updateActiveColor(color)"
                                    :style="'background-color: ' + color"
                                    :class="{ 'ring-4 ring-offset-2 ring-gray-300': activeColor === color }"
                                    class="w-12 h-12 rounded-xl shadow-sm hover:scale-110 transition-all">
                            </button>
                        </template>
                    </div>
                    <div class="mt-3">
                        <input type="color" 
                               x-model="activeColor"
                               @change="updateActiveColor($event.target.value)"
                               class="w-full h-10 rounded-lg border border-gray-300 cursor-pointer">
                    </div>
                </div>

                <!-- Hover Color -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Warna Hover</label>
                    <div class="grid grid-cols-6 gap-3">
                        <template x-for="color in ['#F3F4F6', '#EFF6FF', '#F0FDF4', '#FFFBEB', '#FEF2F2', '#FDF2F8']" :key="color">
                            <button @click="updateHoverColor(color)"
                                    :style="'background-color: ' + color"
                                    :class="{ 'ring-4 ring-offset-2 ring-gray-300': hoverColor === color }"
                                    class="w-12 h-12 rounded-xl shadow-sm hover:scale-110 transition-all border border-gray-200">
                            </button>
                        </template>
                    </div>
                    <div class="mt-3">
                        <input type="color" 
                               x-model="hoverColor"
                               @change="updateHoverColor($event.target.value)"
                               class="w-full h-10 rounded-lg border border-gray-300 cursor-pointer">
                    </div>
                </div>

                <!-- Preview -->
                <div class="p-4 bg-gray-50 rounded-xl">
                    <p class="text-sm font-medium text-gray-700 mb-3">Preview:</p>
                    <div class="space-y-2">
                        <div class="flex items-center space-x-3 p-3 rounded-lg custom-hover transition-colors">
                            <div class="w-4 h-4 rounded-full" :style="'background-color: ' + activeColor"></div>
                            <span class="text-sm text-gray-700">Menu Normal</span>
                        </div>
                        <div class="flex items-center space-x-3 p-3 rounded-lg custom-active">
                            <div class="w-4 h-4 rounded-full" :style="'background-color: ' + activeColor"></div>
                            <span class="text-sm font-medium">Menu Aktif</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Settings -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Pengaturan Lainnya</h3>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <i data-lucide="bell" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">Notifikasi</p>
                                <p class="text-sm text-gray-500">Terima notifikasi sistem</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <i data-lucide="shield" class="w-5 h-5 text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">Keamanan</p>
                                <p class="text-sm text-gray-500">Autentikasi dua faktor</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-purple-100 rounded-lg">
                                <i data-lucide="refresh-cw" class="w-5 h-5 text-purple-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">Auto Refresh</p>
                                <p class="text-sm text-gray-500">Perbarui data otomatis</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 pt-6 border-t border-gray-200 flex space-x-3">
            <button @click="closeSettings()" 
                    class="flex-1 bg-gray-100 text-gray-700 py-3 px-4 rounded-xl hover:bg-gray-200 transition-colors font-medium">
                Batal
            </button>
            <button @click="closeSettings(); applyThemeColors()" 
                    class="flex-1 text-white py-3 px-4 rounded-xl hover:opacity-90 transition-colors font-medium"
                    :style="'background-color: ' + activeColor">
                Simpan Pengaturan
            </button>
        </div>
    </div>
</div>
