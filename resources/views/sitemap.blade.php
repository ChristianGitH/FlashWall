<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/plans') }}</loc>
    </url>
    <url>
        <loc>{{ url('/contact') }}</loc>
    </url>
@foreach ($pages as $locale => $localizedPages)
@foreach ($localizedPages as $slug)
    <url>
        <loc>{{ url('/' . $locale . '/' . $slug) }}</loc>
    </url>
@endforeach
@endforeach
</urlset>