<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
    <head>
        @include('partials.head')
    </head>
    <body>
        <flux:header container class="[&>div]:max-w-2xl! min-h-18">
            <flux:brand href="route('dashboard')" name="Flowpilot" class="[&>div]:first:hidden" />

            <flux:spacer />

            <div class="flex gap-2">
                @guest
                    <flux:button :href="route('login')" variant="ghost" size="sm">{{ __('Sign in') }}</flux:button>
                    <flux:button :href="route('register')" size="sm">{{ __('Get Started') }}</flux:button>
                @else
                    <flux:button :href="route('dashboard')" size="sm">{{ __('Dashboard') }}</flux:button>
                @endguest
            </div>
        </flux:header>

        <flux:main class="[:where(&)]:max-w-2xl!" container>
            <flux:heading level="1" size="xl" class="font-serif">Supercharge Your Team’s Productivity</flux:heading>

            <flux:text variant="strong" size="lg" class="mt-6">
                Effortlessly automate your workflows and collaborate in real time, so your team can focus on what matters most and achieve more together.
            </flux:text>

            <flux:button :href="route('register')" variant="primary" class="mt-6">{{ __('Get Started') }}</flux:button>

            <flux:text variant="strong" size="lg" class="mt-20">
                This cloud-based solution streamlines your team’s daily operations. It enables you to automate repetitive tasks and centralize project management.
            </flux:text>

            <flux:text variant="strong" size="lg" class="mt-6">
                With less time spent on manual processes, your team can focus on delivering results. Every member works more efficiently and stays aligned on shared goals.
            </flux:text>

            <flux:heading size="lg" level="2" class="font-serif mt-20">Everything You Need, All in One Place</flux:heading>

            <flux:text variant="strong" size="lg" class="mt-6">
                At the heart of the platform is a <strong>visual workflow builder</strong> that lets you create, customize, and deploy automated workflows using a simple drag-and-drop interface.
            </flux:text>

            <flux:text variant="strong" size="lg" class="mt-6">
                <strong>Real-time collaboration</strong> is built in, allowing you to share tasks, files, and updates instantly with your team. The solution integrates seamlessly with popular tools such as Slack, Google Workspace, and Trello, making it easy to fit into your existing workflow.
            </flux:text>

            <flux:text variant="strong" size="lg" class="mt-6">
                An intuitive <strong>analytics dashboard</strong> provides insights into productivity, workflow efficiency, and team performance, helping you make data-driven decisions every day.
            </flux:text>

            <flux:heading size="lg" level="2" class="font-serif mt-20">Get Up and Running in Minutes</flux:heading>

            <flux:text variant="strong" size="lg" class="mt-6">
                Getting started is simple. After signing up, you can invite your team and begin building workflows within minutes.
            </flux:text>

            <flux:text variant="strong" size="lg" class="mt-6">
                The platform enables you to automate approvals, notifications, and data entry <strong>without any coding required</strong>. Its user-friendly dashboard keeps everyone on the same page, ensuring that projects move forward smoothly and nothing falls through the cracks.
            </flux:text>

            <flux:heading size="lg" level="2" class="font-serif mt-20">Unlock Your Team’s Full Potential</flux:heading>

            <flux:text variant="strong" size="lg" class="mt-6">
                By automating repetitive tasks, the platform <strong>saves your team valuable time</strong> and reduces manual work.
            </flux:text>

            <flux:text variant="strong" size="lg" class="mt-6">
                Collaboration is enhanced as everyone stays in sync with shared boards and real-time updates. Transparency increases as you monitor progress and quickly identify bottlenecks through powerful analytics.
            </flux:text>

            <flux:text variant="strong" size="lg" class="mt-6">
                Whether you’re a startup or a large enterprise, this solution is designed to scale with your team and support your growth every step of the way.
            </flux:text>

            <flux:heading size="lg" level="2" class="font-serif mt-20">Start Your Journey Today</flux:heading>

            <flux:text variant="strong" size="lg" class="mt-6">
                If you’re ready to transform the way your team works, <strong>sign up for a free 14-day trial</strong> and experience the benefits for yourself.
            </flux:text>

            <flux:text variant="strong" size="lg" class="mt-6">
                There’s no credit card required, and you’ll discover how easy it is to boost productivity and streamline your workflow from day one.
            </flux:text>

            <flux:button :href="route('register')" variant="primary" class="mt-8">
                Get started
            </flux:button>

            <div class="mt-64 flex items-center justify-between">
                <flux:text variant="subtle" class="flex items-center gap-2">
                    <x-app-logo-icon class="size-4" />
                    <span><strong>flowpilot</strong>.com</span>
                </flux:text>
                <flux:text variant="subtle">by <strong>Oliver Servín</strong></flux:text>
            </div>
        </flux:main>
    </body>
</html>
