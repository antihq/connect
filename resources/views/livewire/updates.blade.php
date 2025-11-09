<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('components.layouts.site'), Title('Updates')] class extends Component {} ?>

<div>
    <div class="mt-16 grid grid-cols-1 gap-8 pb-24 lg:grid-cols-[15rem_1fr] xl:grid-cols-[15rem_1fr_15rem]">
        <div class="flex flex-wrap items-center gap-8 max-lg:justify-between lg:flex-col lg:items-start">
            <a href="https://x.com/oliverservinX" class="flex items-center gap-3">
                <img alt="" class="aspect-square size-6 rounded-full object-cover" src="/avatar.jpg" />
                <div class="text-sm/5 text-zinc-700">Oliver Servín</div>
            </a>
        </div>
        <div class="max-w-2xl text-zinc-700 xl:mx-auto">
            <h3 class="mt-12 mb-10 text-xl/8 font-medium tracking-tight text-zinc-950 first:mt-0 last:mb-0">
                November 8, 2025
            </h3>

            <p class="my-10 text-base/8 first:mt-0 last:mb-0">
                Do you want to create your own marketplace for rentals, service bookings, product sales, or service
                offerings, but don’t want to hire a software development agency or freelancer and spend too much money?
                You need a platform that enables you to launch your marketplace idea within hours, or even minutes.
            </p>
        </div>
    </div>
</div>
