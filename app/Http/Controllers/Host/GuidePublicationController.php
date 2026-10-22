<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Requests\Host\PublishGuideRequest;
use App\Models\Property;
use App\Services\GuidePublication;
use App\Services\GuideReview;
use App\Services\GuideSnapshot;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/** Consente all'host proprietario di revisionare, approvare e consultare le versioni della guida. */
class GuidePublicationController extends Controller
{
    public function show(Property $property, GuidePublication $publication, GuideSnapshot $snapshots, GuideReview $review): View
    {
        Gate::authorize('view', $property);
        // Legge revisione e contenuti sotto lo stesso lock usato dalle modifiche.
        [$workspace, $draft] = DB::transaction(fn (): array => [$publication->lock($property), $snapshots->capture($property)]);
        $published = $workspace->publishedVersion;

        return view('pages.host.guides.review', [
            'property' => $property, 'workspace' => $workspace, 'draft' => $draft,
            'published' => $published, 'changes' => $review->changes($published?->payload, $draft),
            'versions' => $property->guideVersions()->select('id', 'property_id', 'number', 'published_at', 'published_by')->with('publisher:id,name')->orderByDesc('number')->paginate(10),
        ]);
    }

    public function store(PublishGuideRequest $request, Property $property, GuidePublication $publication): RedirectResponse
    {
        Gate::authorize('update', $property);
        $data = $request->validated();
        $version = $publication->publish($property, $request->user(), (int) $data['revision'], isset($data['base_version']) ? (int) $data['base_version'] : null);

        return redirect()->route('host.guide.show', $property)->with('status', 'Guida pubblicata: versione '.$version->number.'. Gli ospiti vedono i contenuti approvati.');
    }

    public function version(Property $property, int $version): View
    {
        Gate::authorize('view', $property);
        $version = $property->guideVersions()->with('publisher:id,name')->findOrFail($version);

        return view('pages.host.guides.version', ['property' => $property, 'version' => $version, 'draft' => $version->payload]);
    }
}
