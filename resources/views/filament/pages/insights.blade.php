<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Hero Section --}}
        <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 p-6">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-lg bg-white/20 backdrop-blur-sm">
                        <x-filament::icon icon="heroicon-o-sparkles" class="h-6 w-6 text-white" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Financial Insights</h1>
                        <p class="text-white/90 text-sm">Advanced analytics and spending patterns</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Widgets --}}
        <x-filament-widgets::widgets
            :widgets="$this->getHeaderWidgets()"
            :columns="[
                'md' => 2,
                'xl' => 3,
            ]"
        />
    </div>
</x-filament-panels::page>

