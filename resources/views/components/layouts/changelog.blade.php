<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
    <head>
        @include('partials.head')
    </head>
    <body class="flex min-h-full flex-col bg-white dark:bg-zinc-950">
        <div
            class="relative flex-none overflow-hidden px-6 lg:pointer-events-none lg:fixed lg:inset-0 lg:z-40 lg:flex lg:px-0"
        >
            <div
                class="absolute inset-0 -z-10 overflow-hidden bg-zinc-950 lg:right-[calc(max(2rem,50%-38rem)+40rem)] lg:min-w-lg"
            >
                <svg
                    class="absolute -bottom-48 left-[-40%] h-320 w-[180%] lg:top-[-40%] lg:-right-40 lg:bottom-auto lg:left-auto lg:h-[180%] lg:w-7xl"
                    aria-hidden="true"
                >
                    <defs>
                        <radialGradient id="changelog-bg-desktop" cx="100%">
                            <stop offset="0%" stop-color="rgba(56, 189, 248, 0.3)"></stop>
                            <stop offset="53.95%" stop-color="rgba(0, 71, 255, 0.09)"></stop>
                            <stop offset="100%" stop-color="rgba(10, 14, 23, 0)"></stop>
                        </radialGradient>
                        <radialGradient id="changelog-bg-mobile" cy="100%">
                            <stop offset="0%" stop-color="rgba(56, 189, 248, 0.3)"></stop>
                            <stop offset="53.95%" stop-color="rgba(0, 71, 255, 0.09)"></stop>
                            <stop offset="100%" stop-color="rgba(10, 14, 23, 0)"></stop>
                        </radialGradient>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#changelog-bg-desktop)" class="hidden lg:block"></rect>
                    <rect width="100%" height="100%" fill="url(#changelog-bg-mobile)" class="lg:hidden"></rect>
                </svg>
            </div>
            <div
                class="relative flex w-full lg:pointer-events-auto lg:mr-[calc(max(2rem,50%-38rem)+40rem)] lg:min-w-lg lg:overflow-x-hidden lg:overflow-y-auto lg:pl-[max(4rem,calc(50%-38rem))]"
            >
                <div
                    class="mx-auto max-w-lg lg:mx-0 lg:flex lg:w-96 lg:max-w-none lg:flex-col lg:before:flex-1 lg:before:pt-6"
                >
                    <div class="pt-20 pb-16 sm:pt-32 sm:pb-20 lg:py-20">
                        <div class="relative">
                            <div>
                                <a href="/changelog" class="flex items-baseline gap-4">
                                    <svg
                                        class="inline-block h-8 w-auto"
                                        width="32"
                                        height="32"
                                        viewBox="0 0 32 32"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path d="M21.3333 10.6667V0H0V10.6667L21.3333 10.6667Z" fill="#EAAA08" />
                                        <path
                                            d="M21.3329 21.3332C15.4419 21.3332 10.6662 26.1089 10.6662 31.9999L0.00130319 32.0003L0.00130319 31.725C0.147896 20.1153 9.56836 10.7403 21.1956 10.667L32 10.6675V32.0008H21.3333L21.3329 21.3332Z"
                                            fill="#EAAA08"
                                        />
                                    </svg>
                                    <span class="text-2xl leading-none font-semibold tracking-tight text-white">
                                        Anticonnect
                                    </span>
                                </a>
                            </div>
                            <h1 class="mt-14 text-4xl/10 font-light text-white">
                                Marketplace magic, minus the agency fees
                                <!-- -->
                                <span class="text-blue-300">for founders who hate waiting</span>
                            </h1>
                            <p class="mt-4 text-sm/6 text-zinc-300">
                                Launch a full-featured marketplace in less time than it takes to finish your coffee. No
                                dev team, no endless meetings, no nonsense—just your idea, live and ready for business.
                                It’s fast, flexible, and honestly, probably more fun than it should be.
                            </p>
                            <div class="mt-8 flex flex-wrap justify-center gap-x-1 gap-y-3 sm:gap-x-2 lg:justify-start">
                                <a
                                    class="group relative isolate flex flex-none items-center gap-x-3 rounded-lg px-2 py-0.5 text-[0.8125rem]/6 font-medium text-white/30 transition-colors hover:text-blue-300"
                                    href="/"
                                >
                                    <span
                                        class="absolute inset-0 -z-10 scale-75 rounded-lg bg-white/5 opacity-0 transition group-hover:scale-100 group-hover:opacity-100"
                                    ></span>

                                    <flux:icon.home variant="mini" class="h-4 w-4 flex-none" />
                                    <span class="self-baseline text-white">Home</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-1 items-end justify-center pb-4 lg:justify-start lg:pb-6">
                        <p class="flex items-baseline gap-x-2 text-[0.8125rem]/6 text-zinc-500">
                            Brought to you by
                            <!-- -->
                            <a
                                class="group relative isolate flex items-center gap-x-2 rounded-lg px-2 py-0.5 text-[0.8125rem]/6 font-medium text-white/30 transition-colors hover:text-blue-300"
                                href="https://x.com/oliverservinX"
                            >
                                <span
                                    class="absolute inset-0 -z-10 scale-75 rounded-lg bg-white/5 opacity-0 transition group-hover:scale-100 group-hover:opacity-100"
                                ></span>
                                <svg
                                    viewBox="0 0 16 16"
                                    aria-hidden="true"
                                    fill="currentColor"
                                    class="h-4 w-4 flex-none"
                                >
                                    <path
                                        d="M9.51762 6.77491L15.3459 0H13.9648L8.90409 5.88256L4.86212 0H0.200195L6.31244 8.89547L0.200195 16H1.58139L6.92562 9.78782L11.1942 16H15.8562L9.51728 6.77491H9.51762ZM7.62588 8.97384L7.00658 8.08805L2.07905 1.03974H4.20049L8.17706 6.72795L8.79636 7.61374L13.9654 15.0075H11.844L7.62588 8.97418V8.97384Z"
                                    ></path>
                                </svg>
                                <span class="self-baseline text-white">Oliver Servín</span>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative flex-auto">
            <div
                class="pointer-events-none absolute inset-0 z-50 overflow-hidden lg:right-[calc(max(2rem,50%-38rem)+40rem)] lg:min-w-lg lg:overflow-visible"
            >
                <svg
                    class="absolute top-0 left-[max(0px,calc(50%-18.125rem))] h-full w-1.5 lg:left-full lg:ml-1 xl:right-1 xl:left-auto xl:ml-0"
                    aria-hidden="true"
                >
                    <defs>
                        <pattern id="changelog-pattern" width="6" height="8" patternUnits="userSpaceOnUse">
                            <path
                                d="M0 0H6M0 8H6"
                                class="stroke-blue-900/10 xl:stroke-white/10 dark:stroke-white/10"
                                fill="none"
                            ></path>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#changelog-pattern)"></rect>
                </svg>
            </div>
            <main class="space-y-20 py-20 sm:space-y-32 sm:py-32">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
