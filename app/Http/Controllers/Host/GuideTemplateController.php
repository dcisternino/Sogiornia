<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Services\GuideWordTemplate;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/** Presenta e scarica il modello Word esclusivamente per le strutture possedute dall'host. */
class GuideTemplateController extends Controller
{
    public function show(Property $property): View
    {
        Gate::authorize('view', $property);

        return view('pages.host.guides.template', ['property' => $property]);
    }

    public function download(Request $request, Property $property, GuideWordTemplate $templates): BinaryFileResponse
    {
        Gate::authorize('view', $property);
        $path = $templates->create($property);
        $request->session()->put('onboarding.word_templates.'.$property->id, GuideWordTemplate::VERSION);

        return response()->download($path, 'sogiornia-modello-guida.docx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ])->deleteFileAfterSend(true);
    }
}
