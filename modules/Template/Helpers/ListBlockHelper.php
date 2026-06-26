<?php

namespace Modules\Template\Helpers;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Cache;

class ListBlockHelper
{
    public static function dailyRotationSetting(): array
    {
        return [
            'type'  => 'checkbox',
            'label' => __('Rotate items every 24 hours'),
            'id'    => 'daily_rotation',
        ];
    }

    public static function shouldRotateDaily(array $model): bool
    {
        if (!empty($model['custom_ids']) && array_filter((array) $model['custom_ids'])) {
            return false;
        }

        if (array_key_exists('daily_rotation', $model) && $model['daily_rotation'] !== '' && $model['daily_rotation'] !== null) {
            return !empty($model['daily_rotation']);
        }

        return (bool) setting_item('list_items_daily_rotation');
    }

    public static function applyDailyRotation($query, array $model): void
    {
        if (!self::shouldRotateDaily($model)) {
            return;
        }

        $daySeed = (int) now()->format('Ymd');

        // CRC32 is much faster than RAND() on large tables (avoids full-table random sort).
        if ($query instanceof EloquentBuilder) {
            $query->reorder()->orderByRaw('CRC32(CONCAT(id, ?))', [$daySeed]);

            return;
        }

        if ($query instanceof QueryBuilder) {
            $query->reorder()->orderByRaw('CRC32(CONCAT(id, ?))', [$daySeed]);
        }
    }

    public static function paginateList($query, array $model, int $defaultLimit = 5)
    {
        $limit = (int) ($model['number'] ?? $defaultLimit);

        if (!self::shouldRotateDaily($model)) {
            return $query->paginate($limit);
        }

        $cacheKey = self::rotationCacheKey('paginate', $query, $model, $limit);

        return Cache::remember($cacheKey, self::secondsUntilEndOfDay(), function () use ($query, $model, $limit) {
            $rotatedQuery = clone $query;
            self::applyDailyRotation($rotatedQuery, $model);

            return $rotatedQuery->paginate($limit);
        });
    }

    public static function limitList($query, array $model, int $defaultLimit = 5)
    {
        $limit = (int) ($model['number'] ?? $defaultLimit);

        if (!self::shouldRotateDaily($model)) {
            return $query->limit($limit)->get();
        }

        $cacheKey = self::rotationCacheKey('limit', $query, $model, $limit);

        return Cache::remember($cacheKey, self::secondsUntilEndOfDay(), function () use ($query, $model, $limit) {
            $rotatedQuery = clone $query;
            self::applyDailyRotation($rotatedQuery, $model);

            return $rotatedQuery->limit($limit)->get();
        });
    }

    protected static function rotationCacheKey(string $type, $query, array $model, int $limit): string
    {
        return 'list_block_rotation:' . md5(json_encode([
            'type'      => $type,
            'day'       => now()->format('Ymd'),
            'limit'     => $limit,
            'model'     => $model,
            'sql'       => $query->toSql(),
            'bindings'  => $query->getBindings(),
        ]));
    }

    protected static function secondsUntilEndOfDay(): int
    {
        return max(60, now()->diffInSeconds(now()->copy()->endOfDay()));
    }
}
