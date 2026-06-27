<x-layouts.app :title="'Terms of use'">
<x-seo-head-tags :locale="$locale" :pageKey="$pageKey" />

<div class="container mx-auto p-5 px-2 lg:px-10 max-w-3xl">
    <!-- Header with back button and language switcher -->
    <div class="flex flex-row justify-between items-center flex-nowrap mb-6 gap-4">
      <a href="{{ url()->previous() ?: route('home') }}" onclick="event.preventDefault(); history.back();">
        ← Retour
      </a>

      <noscript>
        <a href="{{ route('home') }}">
          ← Accueil
        </a>
      </noscript>

      <div class="w-48">
        @include('partials.language-switcher')
      </div>
    </div>

    <h1 class="text-3xl font-bold mb-6">Terms of use</h1>
    <p class="mb-8 leading-relaxed">
        By submitting an image via this service, you agree to these Terms of Use. These terms apply only to visitors submitting images to a Wall.
    </p>

    <h2 class="text-2xl font-bold mt-8 mb-4">1. Purpose of the service</h2>
    <p class="mb-6 leading-relaxed">
        This service allows visitors to submit an image to be displayed on a Wall, i.e., an image wall broadcast during an event, online or on a screen.
    </p>

    <h2 class="text-2xl font-bold mt-8 mb-4">2. Visitor responsibility</h2>
    <p class="mb-6 leading-relaxed">
        By submitting an image, you guarantee that:<br>
        - you hold the necessary rights to share it;<br>
        - the image does not infringe on any third-party rights;<br>
        - the image complies with the law and does not contain any illegal, discriminatory, violent, pornographic, defamatory content, or violates someone’s privacy.
    </p>

    <h2 class="text-2xl font-bold mt-8 mb-4">3. Moderation and display</h2>
    <p class="mb-6 leading-relaxed">
        Event organizers and/or the service team may moderate, reject, or delete any submitted image without notice and for any reason (inappropriate content, poor quality, rule violations, etc.).<br>
        Submitting an image does not guarantee it will be displayed on the Wall.
    </p>

    <h2 class="text-2xl font-bold mt-8 mb-4">4. Granted usage rights</h2>
    <p class="mb-6 leading-relaxed">
        By submitting an image, you authorize the event organizer to:<br>
        - display the image on the relevant Wall;<br>
        - publicly broadcast it as part of the event (screen, streaming, projection).<br>
        This authorization is non-exclusive and limited to the duration of the event. No other use will be made without your consent.
    </p>

    <h2 class="text-2xl font-bold mt-8 mb-4">5. Personal data and retention</h2>
    <p class="mb-6 leading-relaxed">
        Your image may be temporarily stored to allow its display and Wall management. It may be automatically or manually deleted after the event. No commercial exploitation is performed.<br>
        An anonymous identifier (visitor cookie) may be used to prevent abuse and limit the number of submission per device.
    </p>

    <h2 class="text-2xl font-bold mt-8 mb-4">6. Prohibited content</h2>
    <p class="mb-6 leading-relaxed">
        It is strictly forbidden to submit images containing:<br>
        - nudity or sexual content;<br>
        - violence or shocking scenes;<br>
        - hateful or discriminatory statements;<br>
        - logos or protected content you do not own;<br>
        - personal data of others without consent;<br>
        - illegal content under applicable law.
    </p>

    <h2 class="text-2xl font-bold mt-8 mb-4">7. Limitation of liability</h2>
    <p class="mb-6 leading-relaxed">
        The service disclaims any responsibility regarding:<br>
        - the use of images by the organizers;<br>
        - delays, deletion, or display errors;<br>
        - any damage resulting from submitting non-compliant content.<br>
        You remain solely responsible for the images you submit.
    </p>

    <h2 class="text-2xl font-bold mt-8 mb-4">8. Changes to the terms</h2>
    <p class="mb-6 leading-relaxed">
        The service reserves the right to modify these Terms of Use at any time. The version in effect is the one displayed at the time of image submission.
    </p>

    <h2 class="text-2xl font-bold mt-8 mb-4">9. Acceptance</h2>
    <p class="mb-8 leading-relaxed">
        By clicking "Submit" and sending your image, you confirm that you have read and accepted these Terms of Use.
    </p>

</div>
</x-layouts.app>