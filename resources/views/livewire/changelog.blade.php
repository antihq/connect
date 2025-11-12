<?php

use App\Models\UpdateSubscription;
use App\Notifications\UpdateSubscriptionConfirmationNotification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;

new #[Layout('components.layouts.changelog'), Title('Changelog')] class extends Component
{
    //
}; ?>

<div class="space-y-20 sm:space-y-32">
    <x-changelog.entry date="2025-11-08">
        <h2><a href="#changelog-2025-11-08">Publishing listings</a></h2>
        <p>
            Worked on the backbone for publishing listings. The process consists of five steps: entering listing
            details, specifying the location, setting the pricing, defining listing availability, and uploading photos.
        </p>
        <p>
            For listing availability, users can set a default weekly schedule when the listing is offered. They can also
            add date range exceptions to specify when the listing is available or unavailable.
        </p>
        <p>I also made photos a required step, as most visitors now expect to see visuals of the listings.</p>
        <h3>Paths overview</h3>
        <p>
            Completed the overall outline of the web paths Anticonnect will use. These include paths for displaying the
            marketplace landing page and searching the marketplace. There are also paths for static pages such as About,
            Terms, and Privacy Policies. The inbox paths let users view received messages. Consumers have a path to view
            their orders, while providers have a path to view their sales. There are paths for displaying individual
            orders or sales, and for creating and viewing listings. Providers can access a path listing all their
            created listings.
        </p>

        <p>
            Paths exist for editing listings, including details, location, pricing, availability, and photos. Users can
            view and edit their profiles, update contact information and passwords, configure payout options, and set up
            payment methods. There are paths for login and registration pages for new users.
        </p>

        <p>
            On the admin or control panel site, paths display all marketplace users and allow editing their information.
            Admins can view and edit all listings, as well as see all platform transactions. Paths display all user
            reviews with options to edit them. For marketplace configuration, paths set the marketplace name, domain,
            email provider, localization, and access settings.
        </p>

        <p>
            Admins can manage pages by viewing, editing, or creating them. Paths configure the top navigation links.
            Additional paths allow editing the footer, including social media links and content blocks. There are paths
            to manage branding elements like logos and layouts. Paths exist for setting up user types and customizing
            user fields.
        </p>

        <p>
            Other paths handle the configuration of listing types, custom listing fields, and categories. Admins can
            configure search settings, transaction handling, and marketplace commissions. Lastly, there are paths to
            configure Stripe, the map provider, analytics, and Zapier integrations.
        </p>
    </x-changelog.entry>

    <x-changelog.entry date="2025-11-01">
        <x-changelog.img src="/" />
        <h2><a href="#changelog-2025-11-01">Culpa voluptate ea laborum nisi in est nulla.</a></h2>
        <p>Deserunt pariatur veniam velit elit voluptate.</p>
        <p>Ullamco magna magna incididunt voluptate quis eu sit proident.</p>
        <h3>
            <flux:icon.sparkles variant="solid" />
            Improvements
        </h3>
        <ul>
            <li>Sunt eu id cupidatat dolor ad dolore elit deserunt occaecat Lorem eiusmod aute.</li>
            <li>Dolor aliqua ut id ullamco duis duis cupidatat mollit commodo pariatur.</li>
            <li>Sunt eu id cupidatat dolor ad dolore elit deserunt occaecat Lorem eiusmod aute.</li>
            <li>Dolor aliqua ut id ullamco duis duis cupidatat mollit commodo pariatur.</li>
            <li>Sunt eu id cupidatat dolor ad dolore elit deserunt occaecat Lorem eiusmod aute.</li>
            <li>Dolor aliqua ut id ullamco duis duis cupidatat mollit commodo pariatur.</li>
        </ul>
    </x-changelog.entry>
</div>
