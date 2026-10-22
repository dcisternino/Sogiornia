<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Requests\Host\TranslationSuggestionRequest;
use App\Services\GoogleTranslation;
use Illuminate\Http\JsonResponse;

/** Restituisce le bozze richieste dall'host, lasciando il salvataggio al modulo originale. */
class TranslationSuggestionController extends Controller
{
    public function store(TranslationSuggestionRequest $request, GoogleTranslation $translation): JsonResponse
    {
        return response()->json(['fields' => $translation->suggest($request->validated('fields'), $request->validated('locale'))]);
    }
}
