@php
    $id = 'changelog-'.\Illuminate\Support\Str::slug($date);
@endphp

<article id="{{ $id }}" class="scroll-mt-16">
    <div>
        <header class="relative mb-10 xl:mb-0">
            <div
                class="pointer-events-none absolute top-0 left-[max(-0.5rem,calc(50%-18.625rem))] z-50 flex h-4 items-center justify-end gap-x-2 lg:right-[calc(max(2rem,50%-38rem)+40rem)] lg:left-0 lg:min-w-lg xl:h-8"
            >
                <a class="inline-flex" href="#{{ $id }}">
                    <time
                        datetime="{{ \Illuminate\Support\Carbon::parse($date)->toIso8601String() }}"
                        class="hidden xl:pointer-events-auto xl:block xl:text-[0.6875rem]/4 xl:font-medium xl:text-zinc-500 xl:dark:text-white/70"
                    >
                        {{ \Illuminate\Support\Carbon::parse($date)->format('M j, Y') }}
                    </time>
                </a>
                <div class="h-0.25 w-3.5 bg-zinc-400 lg:-mr-3.5 xl:mr-0 xl:bg-zinc-300"></div>
            </div>
            <div class="mx-auto max-w-7xl px-6 lg:flex lg:px-8">
                <div class="lg:ml-96 lg:flex lg:w-full lg:justify-end lg:pl-32">
                    <div class="mx-auto max-w-lg lg:mx-0 lg:w-0 lg:max-w-xl lg:flex-auto">
                        <div class="flex">
                            <a class="inline-flex" href="#{{ $id }}">
                                <time
                                    datetime="{{ \Illuminate\Support\Carbon::parse($date)->toIso8601String() }}"
                                    class="text-[0.6875rem]/4 font-medium text-zinc-500 xl:hidden dark:text-white/50"
                                >
                                    {{ \Illuminate\Support\Carbon::parse($date)->format('M j, Y') }}
                                </time>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="mx-auto max-w-7xl px-6 lg:flex lg:px-8">
            <div class="lg:ml-96 lg:flex lg:w-full lg:justify-end lg:pl-32">
                <div
                    class="mx-auto max-w-lg text-sm leading-6 text-zinc-600 lg:mx-0 lg:w-0 lg:max-w-xl lg:flex-auto dark:text-zinc-300 [&_>:first-child]:mt-0 [&_>:not(:first-child)]:mt-6 [&_a:not(h2_a)]:font-semibold [&_a:not(h2_a)]:text-blue-500 [&_a:not(h2_a)]:underline [&_a:not(h2_a)]:decoration-blue-400/40 [&_a:not(h2_a)]:underline-offset-2 [&_a:not(h2_a)]:transition-colors [&_a:not(h2_a)]:duration-200 dark:[&_a:not(h2_a)]:text-blue-400 dark:[&_a:not(h2_a)]:decoration-blue-400/40 [&_a:not(h2_a):hover]:text-blue-600 [&_a:not(h2_a):hover]:decoration-blue-600/40 dark:[&_a:not(h2_a):hover]:text-white dark:[&_a:not(h2_a):hover]:decoration-white/40 [&_blockquote]:mt-8 [&_blockquote]:border-l-4 [&_blockquote]:border-zinc-200 [&_blockquote]:pl-6 [&_blockquote]:text-zinc-500 dark:[&_blockquote]:border-zinc-800 dark:[&_blockquote]:text-zinc-400 [&_blockquote+*]:mt-8 [&_code]:font-mono [&_code:not(a_code,pre_code)]:text-zinc-900 dark:[&_code:not(a_code,pre_code)]:text-white [&_code:not(pre_code)]:text-[0.857em] [&_code:not(pre_code)]:leading-none [&_code:not(pre_code)]:font-bold [&_h2]:mt-8 [&_h2]:text-xl [&_h2]:leading-8 [&_h2]:font-semibold [&_h2]:text-zinc-900 dark:[&_h2]:text-white [&_h2+*]:mt-4 [&_h3]:mt-8 [&_h3]:flex [&_h3]:items-center [&_h3]:gap-3 [&_h3]:text-base [&_h3]:leading-6 [&_h3]:font-semibold [&_h3]:text-zinc-900 dark:[&_h3]:text-white [&_h3+*]:mt-4 [&_h3>svg]:h-4 [&_h3>svg]:w-4 [&_h3>svg]:flex-none [&_h3>svg]:text-blue-500 dark:[&_h3>svg]:text-blue-400 [&_h4]:mt-8 [&_h4]:text-sm [&_h4]:leading-6 [&_h4]:font-semibold [&_h4]:text-zinc-900 dark:[&_h4]:text-white [&_h4+*]:mt-4 [&_hr]:my-16 [&_hr]:mt-16 [&_hr]:border-zinc-900/5 dark:[&_hr]:border-white/10 [&_hr+*]:mt-16 [&_kbd]:inline-block [&_kbd]:rounded [&_kbd]:border [&_kbd]:border-zinc-200 [&_kbd]:bg-zinc-50 [&_kbd]:px-1.5 [&_kbd]:font-mono [&_kbd]:text-xs [&_kbd]:leading-5 [&_kbd]:font-normal [&_kbd]:text-zinc-600 [&_kbd]:shadow-inner dark:[&_kbd]:border-zinc-800 dark:[&_kbd]:bg-zinc-900 dark:[&_kbd]:text-white [&_li]:mt-4 [&_li]:pl-2.5 [&_li::marker]:text-zinc-400 [&_li:first-child]:mt-0 [&_li>:first-child]:mt-0 [&_li>ol]:mt-4 [&_li>p]:mt-4 [&_li>ul]:mt-4 [&_ol]:list-decimal [&_ol]:pl-5 [&_ol>li::marker]:text-xs [&_ol>li::marker]:font-semibold [&_pre]:mt-8 [&_pre]:flex [&_pre]:overflow-x-auto [&_pre]:rounded-lg [&_pre]:bg-zinc-900 [&_pre]:shadow-md dark:[&_pre]:shadow-inner dark:[&_pre]:shadow-white/10 [&_pre_code]:flex-none [&_pre_code]:p-6 [&_pre_code]:text-[0.8125rem] [&_pre_code]:leading-6 [&_pre_code]:text-zinc-300 dark:[&_pre_code]:text-zinc-400 [&_pre+*]:mt-8 [&_strong]:font-semibold [&_strong:not(a_strong)]:text-zinc-900 dark:[&_strong:not(a_strong)]:text-white [&_table]:mt-8 [&_table]:w-full [&_table]:text-left [&_table+*]:mt-8 [&_tbody_td]:pt-1 [&_tbody_td]:pb-2 [&_tbody_tr]:border-b [&_tbody_tr]:border-zinc-900/5 dark:[&_tbody_tr]:border-white/5 [&_td:first-child]:pr-2 [&_td:first-child]:pl-0 [&_td:last-child]:pr-0 [&_td:last-child]:pl-2 [&_td:not(:first-child):not(:last-child)]:px-2 [&_th:first-child]:pr-2 [&_th:first-child]:pl-0 [&_th:last-child]:pr-0 [&_th:last-child]:pl-2 [&_th:not(:first-child):not(:last-child)]:px-2 [&_thead]:border-b [&_thead]:border-zinc-900/20 dark:[&_thead]:border-white/10 [&_thead_th]:pt-0 [&_thead_th]:pb-1 [&_thead_th]:font-semibold [&_thead_th]:text-zinc-900 dark:[&_thead_th]:text-white [&_ul]:list-disc [&_ul]:pl-5"
                >
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</article>
