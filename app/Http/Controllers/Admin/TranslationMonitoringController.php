<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TranslationSetting;
use App\Models\TranslationUsage;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Riepiloga lo storico locale e i budget senza accedere alla fatturazione o alle credenziali Google. */
class TranslationMonitoringController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'month' => ['nullable', 'date_format:Y-m', 'regex:/^(19|20|21)[0-9]{2}-(0[1-9]|1[0-2])$/'],
            'host_id' => ['nullable', 'integer', Rule::exists(User::class, 'id')->where('role', User::ROLE_HOST)],
            'status' => ['nullable', Rule::in(array_keys(TranslationUsage::STATUSES))],
        ]);
        $month = $filters['month'] ?? CarbonImmutable::now(config('translation.timezone'))->format('Y-m');
        $period = $month.'-01';
        $settings = TranslationSetting::query()->findOrFail(1);
        $monthly = TranslationUsage::query()->where('period', $period);
        $totals = (clone $monthly)->selectRaw('status, COUNT(*) as requests, SUM(budget_characters) as total_characters')->groupBy('status')->get()->keyBy('status');
        $used = (int) $totals->sum('total_characters');
        $hosts = User::query()->where('role', User::ROLE_HOST);
        $logs = clone $monthly;
        if (! empty($filters['host_id'])) {
            $hosts->whereKey($filters['host_id']);
            $logs->where('user_id', $filters['host_id']);
        }
        if (! empty($filters['status'])) {
            $logs->where('status', $filters['status']);
        }
        $hosts->with('translationLimit')->withSum(['translationUsages as used_characters' => fn (Builder $query) => $query->where('period', $period)], 'budget_characters')
            ->withCount(['translationUsages as successful_requests' => fn (Builder $query) => $query->where('period', $period)->where('status', 'success'), 'translationUsages as failed_requests' => fn (Builder $query) => $query->where('period', $period)->whereIn('status', ['failed', 'uncertain']), 'translationUsages as blocked_requests' => fn (Builder $query) => $query->where('period', $period)->where('status', 'blocked')]);

        return view('pages.admin.monitoring.index', [
            'settings' => $settings, 'month' => $month, 'filters' => $filters, 'totals' => $totals, 'used' => $used,
            'remaining' => $settings->monthly_character_limit === null ? null : max(0, $settings->monthly_character_limit - $used),
            'estimatedCost' => max(0, $used - config('translation.free_characters')) * config('translation.usd_per_million') / 1000000,
            'hosts' => $hosts->orderBy('name')->orderBy('id')->paginate(15, ['*'], 'hosts_page')->withQueryString(),
            'hostOptions' => User::query()->where('role', User::ROLE_HOST)->orderBy('name')->orderBy('id')->get(['id', 'name', 'email']),
            'logs' => $logs->with('user:id,name')->orderByDesc('id')->paginate(25, ['*'], 'requests_page')->withQueryString(),
        ]);
    }
}
