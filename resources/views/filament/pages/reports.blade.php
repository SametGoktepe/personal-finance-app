<x-filament-panels::page>
    @php
        $stats = [
            [
                'title' => 'Financial Summary',
                'description' => 'Complete financial overview with account balances, income/expense breakdown, and category analysis.',
                'icon' => 'heroicon-o-document-text',
                'color' => 'warning',
                'gradient' => 'from-amber-400 to-orange-500',
                'features' => [
                    'Total balance across all accounts',
                    'Income and expense summary',
                    'Category breakdown with percentages',
                    'Top 10 spending categories'
                ],
                'action' => 'Use the "Financial Summary" button above',
                'badge' => 'Most Popular'
            ],
            [
                'title' => 'Transactions Report',
                'description' => 'Detailed transaction history with custom filters and date range selection.',
                'icon' => 'heroicon-o-arrows-right-left',
                'color' => 'primary',
                'gradient' => 'from-blue-400 to-indigo-500',
                'features' => [
                    'Custom date range selection',
                    'Filter by type (income/expense/all)',
                    'Account and category details',
                    'Amount totals and net balance'
                ],
                'action' => \App\Filament\Resources\Transactions\TransactionResource::getUrl('index'),
                'badge' => null
            ],
            [
                'title' => 'Subscriptions Report',
                'description' => 'Recurring payments overview with cost projections and billing alerts.',
                'icon' => 'heroicon-o-arrow-path',
                'color' => 'success',
                'gradient' => 'from-emerald-400 to-teal-500',
                'features' => [
                    'All active subscriptions list',
                    'Monthly and yearly cost projections',
                    'Upcoming payments (next 14 days)',
                    'Overdue subscription alerts'
                ],
                'action' => \App\Filament\Resources\Subscriptions\SubscriptionResource::getUrl('index'),
                'badge' => 'New'
            ]
        ];
    @endphp

    {{-- Hero Section --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 p-8 mb-8">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-10 -left-10 w-60 h-60 bg-white/10 rounded-full blur-3xl"></div>

        <div class="relative">
            <h1 class="text-3xl font-bold text-white mb-2">Reports & Analytics</h1>
            <p class="text-white/90 text-lg">Get comprehensive insights into your financial data</p>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
        @foreach($stats as $index => $stat)
        <div
            class="group relative"
            style="animation: fadeInUp 0.5s ease-out {{ $index * 0.1 }}s both"
        >
            <div class="absolute inset-0 bg-gradient-to-r {{ $stat['gradient'] }} opacity-0 group-hover:opacity-10 rounded-2xl transition-opacity duration-300"></div>

            <x-filament::section class="relative h-full border-0 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                {{-- Badge --}}
                @if($stat['badge'])
                <div class="absolute -top-3 -right-3 z-10">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r {{ $stat['gradient'] }} text-white shadow-lg">
                        {{ $stat['badge'] }}
                    </span>
                </div>
                @endif

                <div class="space-y-6">
                    {{-- Header --}}
                    <div class="flex items-start gap-4">
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-br {{ $stat['gradient'] }} rounded-xl blur-xl opacity-50"></div>
                            <div class="relative rounded-xl p-3 bg-gradient-to-br {{ $stat['gradient'] }} shadow-lg">
                                <x-filament::icon
                                    :icon="$stat['icon']"
                                    class="h-7 w-7 text-white"
                                />
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-{{ $stat['color'] }}-600 dark:group-hover:text-{{ $stat['color'] }}-400 transition-colors">
                                {{ $stat['title'] }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 leading-relaxed">
                                {{ $stat['description'] }}
                            </p>
                        </div>
                    </div>

                    {{-- Features --}}
                    <div class="space-y-3 px-1">
                        @foreach($stat['features'] as $feature)
                        <div class="flex items-start gap-3 group/item">
                            <div class="mt-1 p-1 rounded-full bg-{{ $stat['color'] }}-100 dark:bg-{{ $stat['color'] }}-500/20 group-hover/item:scale-110 transition-transform">
                                <x-filament::icon
                                    icon="heroicon-m-check"
                                    class="h-3 w-3 text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400"
                                />
                            </div>
                            <span class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed flex-1">{{ $feature }}</span>
                        </div>
                        @endforeach
                    </div>

                    {{-- Action --}}
                    <div class="pt-4 border-t border-gray-200/50 dark:border-gray-700/50">
                        @if(filter_var($stat['action'], FILTER_VALIDATE_URL) || str_starts_with($stat['action'], '/'))
                        <a
                            href="{{ $stat['action'] }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r {{ $stat['gradient'] }} text-white font-medium rounded-lg hover:shadow-lg transform hover:scale-105 transition-all duration-200"
                        >
                            <span>Open {{ str_replace(' Report', '', $stat['title']) }}</span>
                            <x-filament::icon icon="heroicon-m-arrow-right" class="h-4 w-4" />
                        </a>
                        @else
                        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <x-filament::icon icon="heroicon-o-cursor-arrow-rays" class="h-4 w-4" />
                            <span>{{ $stat['action'] }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </x-filament::section>
        </div>
        @endforeach
    </div>

    {{-- How to Guide --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Instructions Card --}}
        <x-filament::section class="border-0 shadow-xl bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
            <x-slot name="heading">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-gradient-to-br from-blue-400 to-indigo-500 shadow-lg">
                        <x-filament::icon icon="heroicon-o-academic-cap" class="h-5 w-5 text-white" />
                    </div>
                    <span class="text-lg font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                        How to Generate Reports
                    </span>
                </div>
            </x-slot>

            <div class="space-y-4">
                @php
                    $steps = [
                        ['icon' => 'heroicon-o-cursor-arrow-rays', 'text' => 'Click the desired report button in the page header above'],
                        ['icon' => 'heroicon-o-funnel', 'text' => 'Select the date range and any filters (if applicable)'],
                        ['icon' => 'heroicon-o-arrow-down-tray', 'text' => 'Click "Export" to generate the PDF report'],
                        ['icon' => 'heroicon-o-check-circle', 'text' => 'The PDF will automatically download to your device']
                    ];
                @endphp

                @foreach($steps as $index => $step)
                <div class="flex items-start gap-4 group">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-sm shadow-lg group-hover:scale-110 transition-transform">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                            {{ $step['text'] }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </x-filament::section>

        {{-- Pro Tips Card --}}
        <x-filament::section class="border-0 shadow-xl">
            <x-slot name="heading">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 shadow-lg animate-pulse">
                        <x-filament::icon icon="heroicon-o-light-bulb" class="h-5 w-5 text-white" />
                    </div>
                    <span class="text-lg font-bold bg-gradient-to-r from-amber-600 to-orange-600 bg-clip-text text-transparent">
                        Pro Tips & Shortcuts
                    </span>
                </div>
            </x-slot>

            <div class="space-y-4">
                <div class="p-4 rounded-xl bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-500/10 dark:to-orange-500/10 border-l-4 border-amber-500">
                    <div class="flex items-start gap-3">
                        <x-filament::icon icon="heroicon-o-sparkles" class="h-5 w-5 text-amber-600 dark:text-amber-400 mt-0.5" />
                        <div>
                            <p class="text-sm font-semibold text-amber-900 dark:text-amber-300 mb-1">
                                Quick Export
                            </p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Visit <strong>Finance > Transactions</strong> or <strong>Finance > Subscriptions</strong> pages directly for quick export options with pre-configured settings.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-500/10 dark:to-pink-500/10 border-l-4 border-purple-500">
                    <div class="flex items-start gap-3">
                        <x-filament::icon icon="heroicon-o-clock" class="h-5 w-5 text-purple-600 dark:text-purple-400 mt-0.5" />
                        <div>
                            <p class="text-sm font-semibold text-purple-900 dark:text-purple-300 mb-1">
                                Schedule Reports
                            </p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Set up automated weekly or monthly reports to track your financial progress consistently.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-500/10 dark:to-teal-500/10 border-l-4 border-emerald-500">
                    <div class="flex items-start gap-3">
                        <x-filament::icon icon="heroicon-o-chart-bar" class="h-5 w-5 text-emerald-600 dark:text-emerald-400 mt-0.5" />
                        <div>
                            <p class="text-sm font-semibold text-emerald-900 dark:text-emerald-300 mb-1">
                                Custom Filters
                            </p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Use advanced filters to create highly specific reports tailored to your needs.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>
    </div>

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</x-filament-panels::page>
