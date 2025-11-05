<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
    <head>
        @include('partials.head', ['title' => 'Launch your marketplace within hours, or even minutes'])
    </head>
    <body>
        <flux:main container>
            <h1
                class="mt-16 text-4xl font-medium tracking-tighter text-pretty text-zinc-950 data-dark:text-white sm:text-6xl"
            >
                Launch your marketplace within hours, or even minutes
            </h1>
            <div class="mt-16 grid grid-cols-1 gap-8 pb-24 lg:grid-cols-[15rem_1fr] xl:grid-cols-[15rem_1fr_15rem]">
                <div class="flex flex-wrap items-center gap-8 max-lg:justify-between lg:flex-col lg:items-start">
                    <a href="https://x.com/oliverservinX" class="flex items-center gap-3">
                        <img
                            alt=""
                            class="aspect-square size-6 rounded-full object-cover"
                            src="/avatar.jpg"
                        />
                        <div class="text-sm/5 text-zinc-700">Oliver Servín</div>
                    </a>
                </div>
                <div class="max-w-2xl text-zinc-700 xl:mx-auto">
                    <p class="my-10 text-base/8 first:mt-0 last:mb-0">
                        Do you want to create your own marketplace for rentals, service bookings, product sales, or
                        service offerings, but don’t want to hire a software development agency or freelancer and spend
                        too much money? You need a platform that enables you to launch your marketplace idea within
                        hours, or even minutes.
                    </p>

                    <p class="my-10 text-base/8 first:mt-0 last:mb-0">
                        I’m Oliver, a software developer, and I’m building a marketplace platform designed to save you
                        time and money. With this platform, you can quickly launch a marketplace for bookings, product
                        sales, or a service directory, and start monetizing almost immediately.
                    </p>

                    <p class="my-10 text-base/8 first:mt-0 last:mb-0">
                        The platform is easy for you, your providers, and customers to use. You can effortlessly manage
                        your marketplace, providers can publish their listings easily, and customers can book services
                        or buy products without hassle.
                    </p>

                    <p class="my-10 text-base/8 first:mt-0 last:mb-0">
                        The marketplace relies on trust, offering messaging and review systems for both customers and
                        providers to ensure quality products and reliable service.
                    </p>

                    <p class="my-10 text-base/8 first:mt-0 last:mb-0">
                        As the marketplace owner, you’ll benefit from a monetization system that lets you earn a
                        commission on every booking or purchase made through your marketplace.
                    </p>

                    <p class="my-10 text-base/8 first:mt-0 last:mb-0">
                        The platform offers flexibility, allowing you to define different user types and separate
                        sign-up processes for providers and customers. You can request additional information during
                        sign-up with custom user fields. Providers can add more details to their listings using custom
                        listing fields. You can also offer various listing types, so providers can publish bookings or
                        rental services, products for sale, or service offers.
                    </p>

                    <p class="my-10 text-base/8 first:mt-0 last:mb-0">
                        Design customization is simple, allowing you to adjust the look and feel to match your branding.
                    </p>

                    <p class="my-10 text-base/8 first:mt-0 last:mb-0">
                        I’m building this platform, and if you’re interested, you can sign up to receive updates on its
                        development and be notified when it’s ready to launch.
                    </p>
                </div>
            </div>
        </flux:main>
    </body>
</html>
