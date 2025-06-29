<!-- Enhanced Settings Modal with Active Indicator Customization -->
<div x-show="showSettingsModal" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
     x-cloak
     @keydown.escape="closeSettings && closeSettings()">
    
    <div x-show="showSettingsModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="closeSettings && closeSettings()"
         class="bg-white rounded-2xl shadow-xl max-w-3xl w-full p-6 max-h-[90vh] overflow-y-auto">
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800">Pengaturan Aplikasi</h2>
            <button @click="closeSettings && closeSettings()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
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
                    <div @click="isDarkMode = false; toggleDarkMode && toggleDarkMode()" 
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
                    <div @click="isDarkMode = true; toggleDarkMode && toggleDarkMode()" 
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
                    <label class="block text-sm font-medium text-gray-700 mb-3">Warna Aktif & Indikator Menu</label>
                    <p class="text-xs text-gray-500 mb-4">Warna ini akan digunakan untuk menu aktif dan garis indikator di sebelah kiri</p>
                    <div class="grid grid-cols-6 gap-3 mb-4">
                        <template x-for="color in ['#8B5CF6', '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#EC4899']" :key="color">
                            <button @click="updateActiveColor && updateActiveColor(color)"
                                    :style="'background-color: ' + color"
                                    :class="{ 'ring-4 ring-offset-2 ring-gray-300 scale-110': (activeColor || '#8B5CF6') === color }"
                                    class="w-12 h-12 rounded-xl shadow-sm hover:scale-110 transition-all relative">
                                <div x-show="(activeColor || '#8B5CF6') === color" class="absolute inset-0 flex items-center justify-center">
                                    <i data-lucide="check" class="w-5 h-5 text-white"></i>
                                </div>
                            </button>
                        </template>
                    </div>
                    <div class="mt-3">
                        <input type="color" 
                               :value="activeColor || '#8B5CF6'"
                               @change="updateActiveColor && updateActiveColor($event.target.value)"
                               class="w-full h-10 rounded-lg border border-gray-300 cursor-pointer">
                    </div>
                </div>

                <!-- Hover Color -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Warna Hover</label>
                    <p class="text-xs text-gray-500 mb-4">Warna latar belakang saat menu di-hover</p>
                    <div class="grid grid-cols-6 gap-3 mb-4">
                        <template x-for="color in ['#F3F4F6', '#EFF6FF', '#F0FDF4', '#FFFBEB', '#FEF2F2', '#FDF2F8']" :key="color">
                            <button @click="updateHoverColor && updateHoverColor(color)"
                                    :style="'background-color: ' + color"
                                    :class="{ 'ring-4 ring-offset-2 ring-gray-300 scale-110': (hoverColor || '#F3F4F6') === color }"
                                    class="w-12 h-12 rounded-xl shadow-sm hover:scale-110 transition-all border border-gray-200 relative">
                                <div x-show="(hoverColor || '#F3F4F6') === color" class="absolute inset-0 flex items-center justify-center">
                                    <i data-lucide="check" class="w-5 h-5 text-gray-600"></i>
                                </div>
                            </button>
                        </template>
                    </div>
                    <div class="mt-3">
                        <input type="color" 
                               :value="hoverColor || '#F3F4F6'"
                               @change="updateHoverColor && updateHoverColor($event.target.value)"
                               class="w-full h-10 rounded-lg border border-gray-300 cursor-pointer">
                    </div>
                </div>

                <!-- Preview Section -->
                <div class="p-6 bg-gray-50 rounded-xl">
                    <p class="text-sm font-medium text-gray-700 mb-4">Preview Menu:</p>
                    <div class="space-y-2 max-w-xs">
                        <!-- Normal Menu Item -->
                        <div class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors relative">
                            <div class="w-4 h-4 rounded-full bg-gray-400"></div>
                            <span class="text-sm text-gray-700">Menu Normal</span>
                        </div>
                        
                        <!-- Active Menu Item with Left Border -->
                        <div class="relative">
                            <div class="absolute left-0 top-0 bottom-0 w-1 rounded-r-full"
                                 :style="'background: linear-gradient(135deg, ' + (activeColor || '#8B5CF6') + ', ' + lightenColor((activeColor || '#8B5CF6'), 20) + ')'">
                            </div>
                            <div class="flex items-center space-x-3 p-3 ml-1 rounded-lg text-white font-medium shadow-md"
                                 :style="'background: linear-gradient(135deg, ' + (activeColor || '#8B5CF6') + ', ' + lightenColor((activeColor || '#8B5CF6'), 10) + ')'">
                                <div class="w-4 h-4 rounded-full bg-white bg-opacity-30"></div>
                                <span class="text-sm">Menu Aktif</span>
                            </div>
                        </div>
                        
                        <!-- Hover Menu Item -->
                        <div class="flex items-center space-x-3 p-3 rounded-lg transition-colors"
                             :style="'background-color: ' + (hoverColor || '#F3F4F6')">
                            <div class="w-4 h-4 rounded-full bg-gray-400"></div>
                            <span class="text-sm text-gray-700">Menu Hover</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Menu Indicator Settings -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Pengaturan Indikator Menu</h3>
                
                <div class="space-y-4">
                    <!-- Indicator Style -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Gaya Indikator</label>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-all">
                                <div class="flex items-center space-x-3">
                                    <div class="w-1 h-8 bg-purple-500 rounded-r-full"></div>
                                    <div>
                                        <p class="font-medium text-gray-800">Garis Kiri</p>
                                        <p class="text-xs text-gray-500">Indikator garis di sisi kiri</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-all opacity-50">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-1 bg-purple-500 rounded-full"></div>
                                    <div>
                                        <p class="font-medium text-gray-800">Garis Bawah</p>
                                        <p class="text-xs text-gray-500">Segera hadir</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Indicator Width -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Lebar Indikator</label>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-500">Tipis</span>
                            <input type="range" min="1" max="4" step="1" value="1" 
                                   class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                            <span class="text-sm text-gray-500">Tebal</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 pt-6 border-t border-gray-200 flex space-x-3">
            <button @click="closeSettings && closeSettings()" 
                    class="flex-1 bg-gray-100 text-gray-700 py-3 px-4 rounded-xl hover:bg-gray-200 transition-colors font-medium">
                Batal
            </button>
            <button @click="closeSettings && closeSettings(); applyThemeColors && applyThemeColors(); showNotification && showNotification('Pengaturan berhasil disimpan!', 'success')" 
                    class="flex-1 text-white py-3 px-4 rounded-xl hover:opacity-90 transition-colors font-medium"
                    :style="'background-color: ' + (activeColor || '#8B5CF6')">
                Simpan Pengaturan
            </button>
        </div>
    </div>
</div>
