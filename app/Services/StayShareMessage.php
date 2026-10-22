<?php

namespace App\Services;

use App\Models\Stay;
use Illuminate\Support\Facades\Lang;

/** Prepara il messaggio del soggiorno nella lingua scelta, senza inviarlo automaticamente. */
class StayShareMessage
{
    public function __construct(private PublishedGuideContent $published) {}

    /** @return array{message: string, subject: string, guideUrl: string, whatsappUrl: string, emailUrl: string} */
    public function forStay(Stay $stay, string $locale): array
    {
        $stay->loadMissing('room.property');
        $content = $this->published->forRoom($stay->room);
        $url = route('guide.index', ['token' => $stay->guide_token, 'lang' => $locale]);
        $message = Lang::get('sharing.message', [
            'property' => $content['property']['name'],
            'room' => $content['room']['name'],
            'arrival' => $stay->starts_at->timezone('Europe/Rome')->format('d/m/Y H:i'),
            'departure' => $stay->ends_at->timezone('Europe/Rome')->format('d/m/Y H:i'),
            'url' => $url,
        ], $locale);
        $subject = preg_replace('/[\r\n]+/', ' ', Lang::get('sharing.subject', ['property' => $content['property']['name']], $locale));

        return ['message' => $message, 'subject' => $subject, 'guideUrl' => $url,
            'whatsappUrl' => 'https://wa.me/?text='.rawurlencode($message),
            'emailUrl' => 'mailto:?subject='.rawurlencode($subject).'&body='.rawurlencode($message)];
    }
}
