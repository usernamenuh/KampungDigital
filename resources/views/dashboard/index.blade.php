@extends('layouts.app')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang kembali, admin!')

@section('content')
<div class="p-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
        <template x-for="(card, index) in dashboardCards" :key="index">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-lg" :style="'background-color: ' + activeColor + '20'">
                        <i :data-lucide="card.icon" class="w-6 h-6" :style="'color: ' + activeColor"></i>
                    </div>
                    <span class="text-sm font-medium" 
                          :class="{
                              'text-green-600': card.changeType === 'positive',
                              'text-red-600': card.changeType === 'negative',
                              'text-gray-600': card.changeType === 'stable'
                          }" 
                          x-text="card.change"></span>
                </div>
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1" x-text="card.title"></h3>
                <p class="text-2xl font-bold text-gray-800 dark:text-white mb-1" x-text="card.value"></p>
                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="card.description"></p>
                
                <!-- Sub Cards -->
                <div x-show="card.subCards" class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <template x-for="subCard in card.subCards" :key="subCard.label">
                        <div class="flex justify-between items-center py-1">
                            <span class="text-xs text-gray-500 dark:text-gray-400" x-text="subCard.label"></span>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300" x-text="subCard.value"></span>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Gender Distribution -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Distribusi Gender</h3>
                <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i data-lucide="more-horizontal" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="relative">
                <canvas id="genderChart" width="400" height="300"></canvas>
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-800 dark:text-white" x-text="genderData.total"></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total</p>
                    </div>
                </div>
            </div>
            <div class="mt-4 flex justify-center space-x-6">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Laki-laki</span>
                    <span class="ml-2 text-sm font-medium text-gray-800 dark:text-white" x-text="genderData.male"></span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-pink-500 rounded-full mr-2"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Perempuan</span>
                    <span class="ml-2 text-sm font-medium text-gray-800 dark:text-white" x-text="genderData.female"></span>
                </div>
            </div>
        </div>

        <!-- Monthly Statistics -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Statistik Bulanan</h3>
                <select class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    <option>30 hari terakhir</option>
                    <option>60 hari terakhir</option>
                    <option>90 hari terakhir</option>
                </select>
            </div>
            <canvas id="monthlyChart" width="400" height="300"></canvas>
        </div>
    </div>

    <!-- Activities -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Aktivitas Terbaru</h3>
            <a href="#" class="text-sm text-purple-600 hover:text-purple-700 font-medium">Lihat Semua</a>
        </div>
        <div class="space-y-4">
            <template x-for="(activity, index) in activities" :key="index">
                <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <div class="w-2 h-2 rounded-full" :style="'background-color: ' + activeColor"></div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="activity.action"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="activity.time"></p>
                    </div>
                    <div class="p-2 bg-white dark:bg-gray-800 rounded-lg">
                        <i :data-lucide="activity.type === 'user' ? 'user' : activity.type === 'business' ? 'store' : activity.type === 'money' ? 'banknote' : 'file-text'" 
                           class="w-4 h-4 text-gray-400"></i>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection