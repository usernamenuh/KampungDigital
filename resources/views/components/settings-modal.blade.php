<!-- Settings Modal -->
<div x-show="showSettingsModal" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
     x-cloak
     @keydown.escape="closeSettings()">
    
    <div x-show="showSettingsModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="closeSettings()"
         class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-6xl w-full p-6 max-h-[90vh] overflow-y-auto">
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Pengaturan Aplikasi</h2>
            <button @click="closeSettings()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <i data-lucide="x" class="w-5 h-5 text-gray-500 dark:text-gray-400"></i>
            </button>
        </div>

        <!-- Settings Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Theme & Colors -->
            <div class="space-y-6">
                <!-- Theme Settings -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Tema Aplikasi</h3>
                    
                    <div class="space-y-3">
                        <!-- Light Mode -->
                        <div @click="setDarkMode(false)" 
                             :class="{ 'ring-2 ring-purple-500 bg-purple-50 dark:bg-purple-900': !isDarkMode }"
                             class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-yellow-100 rounded-lg">
                                    <i data-lucide="sun" class="w-5 h-5 text-yellow-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-white">Mode Terang</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Tema terang untuk penggunaan siang hari</p>
                                </div>
                            </div>
                        </div>

                        <!-- Dark Mode -->
                        <div @click="setDarkMode(true)" 
                             :class="{ 'ring-2 ring-purple-500 bg-purple-50 dark:bg-purple-900': isDarkMode }"
                             class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-gray-800 rounded-lg">
                                    <i data-lucide="moon" class="w-5 h-5 text-white"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-white">Mode Gelap</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Tema gelap untuk penggunaan malam hari</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Color Customization -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Kustomisasi Warna</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Warna Aktif</label>
                        <div class="grid grid-cols-8 gap-3">
                            <template x-for="color in colorOptions" :key="color.value">
                                <button @click="updateActiveColor(color.value)"
                                        :style="'background-color: ' + color.value"
                                        :class="{ 'ring-4 ring-offset-2 ring-gray-300 dark:ring-gray-600 scale-110': activeColor === color.value }"
                                        class="w-12 h-12 rounded-xl shadow-sm hover:scale-110 transition-all"
                                        :title="color.name">
                                </button>
                            </template>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Warna Hover</label>
                        <div class="grid grid-cols-8 gap-3">
                            <template x-for="color in hoverColorOptions" :key="color.value">
                                <button @click="updateHoverColor(color.value)"
                                        :style="'background: ' + color.preview"
                                        :class="{ 'ring-4 ring-offset-2 ring-gray-300 dark:ring-gray-600 scale-110': hoverColor === color.value }"
                                        class="w-12 h-12 rounded-xl shadow-sm hover:scale-110 transition-all border border-gray-200 dark:border-gray-600"
                                        :title="color.name">
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Icon Color -->
                    <div class="mb-4" x-show="showIconColors">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Warna Ikon</label>
                        <div class="grid grid-cols-8 gap-3">
                            <template x-for="color in iconColorOptions" :key="color.value">
                                <button @click="updateIconColor(color.value)"
                                        :style="'background-color: ' + color.value"
                                        :class="{ 'ring-4 ring-offset-2 ring-gray-300 dark:ring-gray-600 scale-110': iconColor === color.value }"
                                        class="w-12 h-12 rounded-xl shadow-sm hover:scale-110 transition-all"
                                        :title="color.name">
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Preview:</p>
                        <div class="space-y-2">
                            <div class="flex items-center space-x-3 p-3 rounded-lg text-white font-medium"
                                 :style="'background-color: ' + activeColor + '; box-shadow: 0 2px 8px ' + activeColor + '30;'">
                                <div class="w-4 h-4 rounded-full bg-white bg-opacity-30"></div>
                                <span class="text-sm">Menu Aktif</span>
                            </div>
                            <div class="flex items-center space-x-3 p-3 rounded-lg font-medium text-gray-700 dark:text-gray-300"
                                 :style="'background: ' + hoverColor">
                                <div class="w-4 h-4 rounded-full bg-gray-400"></div>
                                <span class="text-sm">Menu Hover</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Middle Column - Interface -->
            <div class="space-y-6">
                <!-- Navigation Settings -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Navigasi</h3>
                    
                    <div class="space-y-4">
                        <!-- Show Icons Toggle -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white">Tampilkan Ikon</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Menampilkan ikon di menu navigasi</p>
                            </div>
                            <button @click="toggleShowIcons()"
                                    :class="{ 'bg-purple-600': showIcons, 'bg-gray-300 dark:bg-gray-600': !showIcons }"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                                <span :class="{ 'translate-x-6': showIcons, 'translate-x-1': !showIcons }"
                                      class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                            </button>
                        </div>

                        <!-- Show Icon Colors Toggle -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white">Warna Ikon</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Menggunakan warna khusus untuk ikon</p>
                            </div>
                            <button @click="toggleShowIconColors()"
                                    :class="{ 'bg-purple-600': showIconColors, 'bg-gray-300 dark:bg-gray-600': !showIconColors }"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                                <span :class="{ 'translate-x-6': showIconColors, 'translate-x-1': !showIconColors }"
                                      class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card Customization -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Kustomisasi Card</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Gaya Card</label>
                            <div class="grid grid-cols-2 gap-3">
                                <button @click="updateCardStyle('default')"
                                        :class="{ 'ring-2 ring-purple-500 bg-purple-50 dark:bg-purple-900': cardStyle === 'default' }"
                                        class="p-4 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                                    <div class="w-full h-16 bg-white dark:bg-gray-800 rounded-lg shadow-sm mb-2"></div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white">Default</p>
                                </button>
                                
                                <button @click="updateCardStyle('blur')"
                                        :class="{ 'ring-2 ring-purple-500 bg-purple-50 dark:bg-purple-900': cardStyle === 'blur' }"
                                        class="p-4 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                                    <div class="w-full h-16 bg-gradient-to-r from-blue-400 to-purple-500 rounded-lg shadow-sm mb-2 backdrop-blur-sm opacity-80"></div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white">Blur</p>
                                </button>
                                
                                <button @click="updateCardStyle('gradient')"
                                        :class="{ 'ring-2 ring-purple-500 bg-purple-50 dark:bg-purple-900': cardStyle === 'gradient' }"
                                        class="p-4 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                                    <div class="w-full h-16 bg-gradient-to-r from-pink-400 to-red-500 rounded-lg shadow-sm mb-2"></div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white">Gradient</p>
                                </button>
                                
                                <button @click="updateCardStyle('colored')"
                                        :class="{ 'ring-2 ring-purple-500 bg-purple-50 dark:bg-purple-900': cardStyle === 'colored' }"
                                        class="p-4 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                                    <div class="w-full h-16 bg-green-400 rounded-lg shadow-sm mb-2"></div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white">Colored</p>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart Theme -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Tema Chart</h3>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <template x-for="theme in chartThemes" :key="theme.value">
                            <button @click="updateChartTheme(theme.value)"
                                    :class="{ 'ring-2 ring-purple-500 bg-purple-50 dark:bg-purple-900': chartTheme === theme.value }"
                                    class="p-4 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                                <div class="mb-2">
                                    <div class="w-full h-12 rounded-lg flex items-end space-x-1 p-2"
                                         :style="'background: ' + theme.preview">
                                        <div class="w-2 bg-white rounded-sm opacity-80" style="height: 60%"></div>
                                        <div class="w-2 bg-white rounded-sm opacity-80" style="height: 80%"></div>
                                        <div class="w-2 bg-white rounded-sm opacity-80" style="height: 40%"></div>
                                        <div class="w-2 bg-white rounded-sm opacity-80" style="height: 90%"></div>
                                    </div>
                                </div>
                                <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="theme.name"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="theme.description"></p>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Right Column - System Status -->
            <div class="space-y-6">
                <!-- System Status -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Status Sistem</h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Status Koneksi</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Status koneksi internet real-time</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span :class="{
                                    'status-online': connectionStatus === 'online',
                                    'status-offline': connectionStatus === 'offline',
                                    'status-loading': connectionStatus === 'loading'
                                }" class="status-indicator"></span>
                                <span class="text-sm font-medium text-gray-800 dark:text-white" 
                                      x-text="connectionStatus === 'online' ? 'Online' : connectionStatus === 'offline' ? 'Offline' : 'Loading'"></span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Auto Refresh</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Data diperbarui setiap 30 detik</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-sm font-medium text-green-600 dark:text-green-400">Aktif</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Waktu Saat Ini</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Waktu sistem real-time</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="currentTime"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="currentDate"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Management -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Manajemen Data</h3>
                    
                    <div class="space-y-3">
                        <button @click="exportData()" class="w-full flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-800 transition-colors">
                            <div class="flex items-center space-x-3">
                                <i data-lucide="download" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                                <div class="text-left">
                                    <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Export Data</p>
                                    <p class="text-xs text-blue-600 dark:text-blue-400">Download data dalam format Excel</p>
                                </div>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                        </button>

                        <button @click="importData()" class="w-full flex items-center justify-between p-3 bg-green-50 dark:bg-green-900 rounded-lg hover:bg-green-100 dark:hover:bg-green-800 transition-colors">
                            <div class="flex items-center space-x-3">
                                <i data-lucide="upload" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                                <div class="text-left">
                                    <p class="text-sm font-medium text-green-800 dark:text-green-200">Import Data</p>
                                    <p class="text-xs text-green-600 dark:text-green-400">Upload data dari file Excel</p>
                                </div>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
                        </button>

                        <button @click="clearCache()" class="w-full flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-800 transition-colors">
                            <div class="flex items-center space-x-3">
                                <i data-lucide="trash-2" class="w-5 h-5 text-yellow-600 dark:text-yellow-400"></i>
                                <div class="text-left">
                                    <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Bersihkan Cache</p>
                                    <p class="text-xs text-yellow-600 dark:text-yellow-400">Hapus data cache aplikasi</p>
                                </div>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-yellow-600 dark:text-yellow-400"></i>
                        </button>
                    </div>
                </div>

                <!-- Reset Settings -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Reset Pengaturan</h3>
                    
                    <button @click="resetSettings()" class="w-full flex items-center justify-center space-x-2 p-3 bg-red-50 dark:bg-red-900 rounded-lg hover:bg-red-100 dark:hover:bg-red-800 transition-colors border border-red-200 dark:border-red-700">
                        <i data-lucide="rotate-ccw" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
                        <span class="text-sm font-medium text-red-800 dark:text-red-200">Reset ke Default</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Pengaturan disimpan secara otomatis
            </div>
            <div class="flex space-x-3">
                <button @click="resetSettings()" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                    Reset
                </button>
                <button @click="closeSettings()" class="px-6 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                    Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function settingsModalData() {
    return {
        showSettingsModal: false,
        
        // Theme settings
        isDarkMode: localStorage.getItem('darkMode') === 'true',
        activeColor: localStorage.getItem('activeColor') || '#8B5CF6',
        hoverColor: localStorage.getItem('hoverColor') || 'rgba(139, 92, 246, 0.08)',
        chartTheme: localStorage.getItem('chartTheme') || 'default',
        cardStyle: localStorage.getItem('cardStyle') || 'default',
        showIcons: localStorage.getItem('showIcons') !== 'false',
        showIconColors: localStorage.getItem('showIconColors') === 'true',
        iconColor: localStorage.getItem('iconColor') || '#FFFFFF',
        connectionStatus: 'online',
        currentTime: new Date().toLocaleTimeString(),
        currentDate: new Date().toLocaleDateString(),
        
        // Color options
        colorOptions: [
            { name: 'Purple', value: '#8B5CF6' },
            { name: 'Blue', value: '#3B82F6' },
            { name: 'Green', value: '#10B981' },
            { name: 'Red', value: '#EF4444' },
            { name: 'Yellow', value: '#F59E0B' },
            { name: 'Pink', value: '#EC4899' },
            { name: 'Indigo', value: '#6366F1' },
            { name: 'Teal', value: '#14B8A6' }
        ],
        
        hoverColorOptions: [
            { name: 'Purple Light', value: 'rgba(139, 92, 246, 0.08)', preview: 'rgba(139, 92, 246, 0.08)' },
            { name: 'Blue Light', value: 'rgba(59, 130, 246, 0.08)', preview: 'rgba(59, 130, 246, 0.08)' },
            { name: 'Green Light', value: 'rgba(16, 185, 129, 0.08)', preview: 'rgba(16, 185, 129, 0.08)' },
            { name: 'Red Light', value: 'rgba(239, 68, 68, 0.08)', preview: 'rgba(239, 68, 68, 0.08)' },
            { name: 'Yellow Light', value: 'rgba(245, 158, 11, 0.08)', preview: 'rgba(245, 158, 11, 0.08)' },
            { name: 'Pink Light', value: 'rgba(236, 72, 153, 0.08)', preview: 'rgba(236, 72, 153, 0.08)' },
            { name: 'Indigo Light', value: 'rgba(99, 102, 241, 0.08)', preview: 'rgba(99, 102, 241, 0.08)' },
            { name: 'Teal Light', value: 'rgba(20, 184, 166, 0.08)', preview: 'rgba(20, 184, 166, 0.08)' }
        ],
        
        iconColorOptions: [
            { name: 'White', value: '#FFFFFF' },
            { name: 'Yellow', value: '#FDE047' },
            { name: 'Orange', value: '#FB923C' },
            { name: 'Red', value: '#F87171' },
            { name: 'Pink', value: '#F472B6' },
            { name: 'Purple', value: '#C084FC' },
            { name: 'Blue', value: '#60A5FA' },
            { name: 'Green', value: '#4ADE80' }
        ],
        
        chartThemes: [
            { 
                name: 'Default', 
                value: 'default', 
                description: 'Tema standar dengan warna seimbang',
                preview: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
            },
            { 
                name: 'Ocean', 
                value: 'ocean', 
                description: 'Tema biru laut yang menenangkan',
                preview: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
            },
            { 
                name: 'Forest', 
                value: 'forest', 
                description: 'Tema hijau alami',
                preview: 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)'
            },
            { 
                name: 'Sunset', 
                value: 'sunset', 
                description: 'Tema hangat seperti matahari terbenam',
                preview: 'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)'
            }
        ],

        init() {
            // Update time every second
            setInterval(() => {
                this.currentTime = new Date().toLocaleTimeString();
                this.currentDate = new Date().toLocaleDateString();
            }, 1000);

            // Initialize icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        },
        
        openSettings() {
            this.showSettingsModal = true;
            document.body.style.overflow = 'hidden';
        },
        
        closeSettings() {
            this.showSettingsModal = false;
            document.body.style.overflow = '';
        },
        
        // Theme methods
        setDarkMode(value) {
            this.isDarkMode = value;
            localStorage.setItem('darkMode', value);
            document.documentElement.classList.toggle('dark', value);
            
            // Dispatch event to update global state
            window.dispatchEvent(new CustomEvent('darkModeChanged', { detail: value }));
            
            this.showNotification(value ? 'Mode gelap diaktifkan' : 'Mode terang diaktifkan', 'success');
        },
        
        updateActiveColor(color) {
            this.activeColor = color;
            localStorage.setItem('activeColor', color);
            document.documentElement.style.setProperty('--color-primary', color);
            
            // Dispatch event to update navigation
            window.dispatchEvent(new CustomEvent('activeColorChanged', { detail: color }));
            
            this.showNotification('Warna aktif berhasil diubah', 'success');
        },
        
        updateHoverColor(color) {
            this.hoverColor = color;
            localStorage.setItem('hoverColor', color);
            document.documentElement.style.setProperty('--color-hover', color);
            
            // Dispatch event to update navigation
            window.dispatchEvent(new CustomEvent('hoverColorChanged', { detail: color }));
            
            this.showNotification('Warna hover berhasil diubah', 'success');
        },
        
        updateIconColor(color) {
            this.iconColor = color;
            localStorage.setItem('iconColor', color);
            
            // Dispatch event to update navigation
            window.dispatchEvent(new CustomEvent('iconColorChanged', { detail: color }));
            
            this.showNotification('Warna ikon berhasil diubah', 'success');
        },
        
        updateChartTheme(theme) {
            this.chartTheme = theme;
            localStorage.setItem('chartTheme', theme);
            
            // Dispatch event to update charts
            window.dispatchEvent(new CustomEvent('chartThemeChanged', { detail: theme }));
            
            this.showNotification('Tema chart berhasil diubah', 'success');
        },
        
        updateCardStyle(style) {
            this.cardStyle = style;
            localStorage.setItem('cardStyle', style);
            
            // Dispatch event to update cards
            window.dispatchEvent(new CustomEvent('cardStyleChanged', { detail: style }));
            
            this.showNotification('Gaya card berhasil diubah', 'success');
        },
        
        toggleShowIcons() {
            this.showIcons = !this.showIcons;
            localStorage.setItem('showIcons', this.showIcons);
            
            // Dispatch event to update navigation
            window.dispatchEvent(new CustomEvent('showIconsChanged', { detail: this.showIcons }));
            
            this.showNotification(this.showIcons ? 'Ikon ditampilkan' : 'Ikon disembunyikan', 'success');
        },
        
        toggleShowIconColors() {
            this.showIconColors = !this.showIconColors;
            localStorage.setItem('showIconColors', this.showIconColors);
            
            // Dispatch event to update navigation
            window.dispatchEvent(new CustomEvent('showIconColorsChanged', { detail: this.showIconColors }));
            
            this.showNotification(this.showIconColors ? 'Warna ikon diaktifkan' : 'Warna ikon dinonaktifkan', 'success');
        },
        
        // Data management methods
        exportData() {
            this.showNotification('Fitur export data akan segera tersedia', 'info');
        },
        
        importData() {
            this.showNotification('Fitur import data akan segera tersedia', 'info');
        },
        
        clearCache() {
            if (confirm('Apakah Anda yakin ingin menghapus cache aplikasi?')) {
                localStorage.removeItem('dashboardCache');
                this.showNotification('Cache berhasil dihapus', 'success');
            }
        },
        
        resetSettings() {
            if (confirm('Apakah Anda yakin ingin mereset semua pengaturan ke default?')) {
                // Reset all settings
                const keysToRemove = [
                    'darkMode', 'activeColor', 'hoverColor', 'chartTheme', 
                    'cardStyle', 'showIcons', 'showIconColors', 'iconColor'
                ];
                
                keysToRemove.forEach(key => localStorage.removeItem(key));
                
                // Reset component state
                this.isDarkMode = false;
                this.activeColor = '#8B5CF6';
                this.hoverColor = 'rgba(139, 92, 246, 0.08)';
                this.chartTheme = 'default';
                this.cardStyle = 'default';
                this.showIcons = true;
                this.showIconColors = false;
                this.iconColor = '#FFFFFF';
                
                // Apply changes
                document.documentElement.classList.remove('dark');
                document.documentElement.style.setProperty('--color-primary', this.activeColor);
                document.documentElement.style.setProperty('--color-hover', this.hoverColor);
                
                // Dispatch events
                const events = [
                    ['darkModeChanged', false],
                    ['activeColorChanged', this.activeColor],
                    ['hoverColorChanged', this.hoverColor],
                    ['chartThemeChanged', this.chartTheme],
                    ['cardStyleChanged', this.cardStyle],
                    ['showIconsChanged', this.showIcons],
                    ['showIconColorsChanged', this.showIconColors],
                    ['iconColorChanged', this.iconColor]
                ];
                
                events.forEach(([event, detail]) => {
                    window.dispatchEvent(new CustomEvent(event, { detail }));
                });
                
                this.showNotification('Pengaturan berhasil direset', 'success');
            }
        },
        
        showNotification(message, type = 'info') {
            if (window.showNotification) {
                window.showNotification(message, type);
            }
        }
    }
}
</script>

<style>
.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.status-online {
    background-color: #10B981;
    animation: pulse 2s infinite;
}

.status-offline {
    background-color: #EF4444;
}

.status-loading {
    background-color: #F59E0B;
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}
</style>
