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
            <h2 class="mt-12 mb-10 text-2xl/8 font-medium tracking-tight text-zinc-950 first:mt-0 last:mb-0">
                November 8, 2025
            </h2>

            <h3 class="mt-12 mb-10 text-xl/8 font-medium tracking-tight text-zinc-950 first:mt-0 last:mb-0">
                Publishing listings
            </h3>

            <p class="my-10 text-base/8 first:mt-0 last:mb-0">
                Worked on the backbone for publishing listings. The process consists of five steps: entering listing details, specifying the location, setting the pricing, defining listing availability, and uploading photos.
            </p>

            <p class="my-10 text-base/8 first:mt-0 last:mb-0">
                For listing availability, users can set a default weekly schedule when the listing is offered. They can also add date range exceptions to specify when the listing is available or unavailable.
            </p>

            <p class="my-10 text-base/8 first:mt-0 last:mb-0">
                I also made photos a required step, as most visitors now expect to see visuals of the listings.
            </p>        

            <h3 class="mt-12 mb-10 text-xl/8 font-medium tracking-tight text-zinc-950 first:mt-0 last:mb-0">
                Paths overview
            </h3>

            <p class="my-10 text-base/8 first:mt-0 last:mb-0">
                Completed the overall outline of the web paths Anticonnect will use. These include paths for displaying the marketplace landing page and searching the marketplace. There are also paths for static pages such as About, Terms, and Privacy Policies. The inbox paths let users view received messages. Consumers have a path to view their orders, while providers have a path to view their sales. There are paths for displaying individual orders or sales, and for creating and viewing listings. Providers can access a path listing all their created listings.
            </p>

            <p class="my-10 text-base/8 first:mt-0 last:mb-0">
                Paths exist for editing listings, including details, location, pricing, availability, and photos. Users can view and edit their profiles, update contact information and passwords, configure payout options, and set up payment methods. There are paths for login and registration pages for new users.
            </p>

            <p class="my-10 text-base/8 first:mt-0 last:mb-0">
                On the admin or control panel site, paths display all marketplace users and allow editing their information. Admins can view and edit all listings, as well as see all platform transactions. Paths display all user reviews with options to edit them. For marketplace configuration, paths set the marketplace name, domain, email provider, localization, and access settings.
            </p>

            <p class="my-10 text-base/8 first:mt-0 last:mb-0">
                Admins can manage pages by viewing, editing, or creating them. Paths configure the top navigation links. Additional paths allow editing the footer, including social media links and content blocks. There are paths to manage branding elements like logos and layouts. Paths exist for setting up user types and customizing user fields.
            </p>

            <p class="my-10 text-base/8 first:mt-0 last:mb-0">
                Other paths handle the configuration of listing types, custom listing fields, and categories. Admins can configure search settings, transaction handling, and marketplace commissions. Lastly, there are paths to configure Stripe, the map provider, analytics, and Zapier integrations.
            </p>
        </div>
    </div>
</div>
