<x-layouts.app :title="'Legal Notice'">
<x-seo-head-tags :locale="$locale" :pageKey="$pageKey" />

<div class="container mx-auto px-4 max-w-3xl">
    <!-- Header with back button and language switcher -->
    <div class="flex flex-row justify-between items-center flex-nowrap mb-6 gap-4">
      <a href="{{ url()->previous() ?: route('home') }}" onclick="event.preventDefault(); history.back();">
        ← Go back
      </a>

      <noscript>
        <a href="{{ route('home') }}">
          ← Home
        </a>
      </noscript>

      <div class="w-48">
        @include('partials.language-switcher')
      </div>
    </div>

    <!-- Main title -->
    <h1 class="text-3xl font-bold mb-6">Legal Notice</h1>

    <!-- Editor section -->
    <h2 class="text-2xl font-bold mt-8 mb-4">Editor</h2>
    <p class="mb-6 leading-relaxed">
        The website editor is : contact@flashwall.app
    </p>

    <!-- Intellectual property section -->
    <h2 class="text-2xl font-bold mt-8 mb-4">Intellectual property</h2>
    <p class="mb-6 leading-relaxed">
        All content on this website (texts, images, illustrations, photographs, videos, logos, icons…) is the property of the publisher or used with the authors’ permission.<br>
        Any reproduction, distribution, modification, adaptation, retransmission, or publication of these elements is strictly prohibited without the publisher’s written consent.<br>
        For texts and images from third parties, sources are cited and permissions are obtained when necessary.
    </p>

    <!-- Hosting section -->
    <h2 class="text-2xl font-bold mt-8 mb-4">Hosting</h2>
    <p class="mb-6 leading-relaxed">
        OVH<br>
        OVH SAS<br>
        2 rue Kellermann - 59100 Roubaix - France<br>
    </p>

    <!-- Liability section -->
    <h2 class="text-2xl font-bold mt-8 mb-4">Liability</h2>
    <p class="mb-6 leading-relaxed">
        The publisher cannot be held responsible for direct or indirect, material or immaterial damages resulting from access to or use of the website, as well as for the inability to access the website or use its services.
    </p>

    <!-- Personal data section -->
    <h2 class="text-2xl font-bold mt-8 mb-4">Personal data</h2>
    <p class="mb-6 leading-relaxed">
        In general, you can visit our website without disclosing your identity or providing personal information. However, we may sometimes request information, for example if you want to create an account or share files. In this case, you must accept the terms of use related to the operations you wish to perform.<br>
        In accordance with the French Data Protection Act of January 6, 1978, you have the right to access, modify, rectify, and delete data concerning you.<br>
        To exercise this right, you can contact the publisher at the email address provided above.
    </p>

    <!-- Contact section -->
    <h2 class="text-2xl font-bold mt-8 mb-4">Contact</h2>
    <p class="mb-6 leading-relaxed">
        For any questions regarding the website or the services offered, you can contact the publisher at the contact details provided above.
    </p>
</div>
</x-layouts.app>