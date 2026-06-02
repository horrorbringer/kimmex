<?php

namespace App\Http\Controllers;

use App\Support\PublicStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class MediaController extends Controller
{
    public function show(Request $request, string $path): StreamedResponse|SymfonyResponse
    {
        $path = ltrim($path, '/');

        if ($path === '' || Str::contains($path, ["\0", '../', '..\\'])) {
            abort(404);
        }

        $disk = PublicStorage::disk();

        if (! $disk->exists($path)) {
            abort(404);
        }

        $lastModified = null;
        $size = null;

        try {
            $lastModified = $disk->lastModified($path);
            $size = $disk->size($path);
        } catch (\Throwable) {
            //
        }

        $etag = '"' . sha1($path . '|' . ($lastModified ?? '') . '|' . ($size ?? '')) . '"';

        if ($request->headers->get('if-none-match') === $etag) {
            return response('', 304, [
                'ETag' => $etag,
                'Cache-Control' => 'public, max-age=31536000, immutable',
            ]);
        }

        if ($lastModified) {
            $ifModifiedSince = $request->headers->get('if-modified-since');

            if ($ifModifiedSince && strtotime($ifModifiedSince) >= $lastModified) {
                return response('', 304, [
                    'ETag' => $etag,
                    'Last-Modified' => gmdate('D, d M Y H:i:s', $lastModified) . ' GMT',
                    'Cache-Control' => 'public, max-age=31536000, immutable',
                ]);
            }
        }

        $stream = $disk->readStream($path);

        if ($stream === false) {
            abort(404);
        }

        $mimeType = $disk->mimeType($path) ?: 'application/octet-stream';

        $headers = [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'ETag' => $etag,
            'X-Content-Type-Options' => 'nosniff',
        ];

        if ($lastModified) {
            $headers['Last-Modified'] = gmdate('D, d M Y H:i:s', $lastModified) . ' GMT';
        }

        if ($size) {
            $headers['Content-Length'] = (string) $size;
        }

        return Response::stream(function () use ($stream): void {
            fpassthru($stream);

            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, $headers);
    }
}
