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
    <x-changelog.entry date="2025-11-14">
        <x-changelog.img src="/assets/images/CleanShot 2025-11-14 at 12.38.50@2x.png" />

        <h2><a href="#changelog-2025-11-14">Pending booking payments</a></h2>

        <p>Worked on displaying booking transactions that are still pending payment so the customer and provider can communicate, allowing the customer to proceed with the payment and complete their booking request.</p>
    </x-changelog.entry>
    <x-changelog.entry date="2025-11-12">
        <x-changelog.img src="/assets/images/CleanShot 2025-11-12 at 18.34.39@2x.png" />

        <h2><a href="#changelog-2025-11-12">Requesting a book</a></h2>

        <p>I completed the design for requesting a book.</p>

        <p>The process begins with users selecting a date range. First, they choose the start date, then the end date. The "Request to Book" button remains disabled until users finish selecting both dates.</p>

        <x-changelog.img src="/assets/images/CleanShot 2025-11-12 at 18.37.15@2x.png" />

        <p>Next, users see a breakdown of their booking, including the start and end dates with the corresponding day of the week and calendar view, helping them confirm their choices.</p>
        
        <x-changelog.img src="/assets/images/CleanShot 2025-11-12 at 18.38.22@2x.png" />

        <p>Finally, users receive a summary of the amount to pay, calculated by the number of selected days and the total booking price. Once users review this, the "Request to Book" button becomes active, allowing them to continue to the payment process.</p>

        <hr id="changelog-2025-11-12-2">

        <x-changelog.img src="/assets/images/CleanShot 2025-11-12 at 13.18.12@2x.png" />

        <h2><a href="#changelog-2025-11-12-2">Multistep listing submission</a></h2>

        <p>Made the publishing process smoother by splitting the submission into five steps.</p>

        <h3>Details</h3>

        <x-changelog.img src="/assets/images/CleanShot 2025-11-12 at 13.19.35@2x.png" />

        <p>First, enter the listing's general details, such as the title and description, with clear indications that both fields are required.</p>

        <h3>Location</h3>

        <x-changelog.img src="/assets/images/CleanShot 2025-11-12 at 13.20.26@2x.png" />

        <p>Next, provide the listing location by entering the address and, optionally, the apartment, suite, or building number.</p>

        <h3>Pricing</h3>

        <x-changelog.img src="/assets/images/CleanShot 2025-11-12 at 13.21.02@2x.png" />

        <p>Then, set the pricing for the listing.</p>

        <h3>Availability</h3>

        <x-changelog.img src="/assets/images/CleanShot 2025-11-12 at 13.22.06@2x.png" />

        <p>After that, configure the listing's availability.</p>

        <p>Since this is for a rental property, select a time zone, set a default weekly schedule by choosing available days from Monday to Sunday, and optionally configure availability exceptions to specify date ranges when the listing is either available or unavailable.</p>

        <x-changelog.img src="/assets/images/CleanShot 2025-11-12 at 13.22.56@2x.png" />

        <h3>Photos</h3>

        <p>Finally, add photos to the listing to capture visitors' attention.</p>

        <x-changelog.img src="/assets/images/CleanShot 2025-11-12 at 13.25.27@2x.png" />
    </x-changelog.entry>

    <x-changelog.entry date="2025-11-11">
        <x-changelog.img src="/assets/images/CleanShot 2025-11-12 at 10.19.56@2x.png" />
        <h2><a href="#changelog-2025-11-11">Magic Auth</a></h2>
        <p>
            To make user sign-in to the marketplace easier and more accessible, I have implemented authentication via Magic Auth.
        </p>
        <p>
            Users no longer need to create or enter a password to register or log in. Instead, they only enter their email and receive a confirmation code to complete the registration or login process.
        </p>
        <p>
            I hope this authentication system increases the conversion rate from visitors to marketplace members.
        </p>
    </x-changelog.entry>

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

{{--     <x-changelog.entry date="2025-11-01">
        <x-changelog.img src="/assets/images/CleanShot 2025-11-12 at 10.16.41@2x.png" />
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
    </x-changelog.entry> --}}
</div>
