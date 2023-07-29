<?php

namespace App\Classes;

use App\Classes\BasePeriod;
use App\Models\ScoreArchive;
use Illuminate\Support\Facades\Storage;

class KpiExporter
{
    public static function exportKpiArchive()
    {
        $period = BasePeriod::getLastNBasePeriods(1)[0];

        $scores = ScoreArchive::whereDate('timestamp', '<' , $period['start'])->get();
        if ($scores->isNotEmpty()) {
            $sql = self::buildSqlForExport($scores);
            $fileName = 'kpi-archive-'.date('Y-m-d').'.sql';
            $path = Storage::disk('s3')->put($fileName, $sql);
            if ($path) {
                ScoreArchive::destroy($scores->pluck('id'));
            }
        }
    }

    private static function buildSqlForExport($scores)
    {
        $dbName = config('database.connections.mysql.database');
        $sql = '';

        foreach ($scores as $score) {
            if ($scores->first() === $score) {
                $sql .= "INSERT INTO $dbName.score_archives (id, user_id, value, weight, timestamp, created_at, updated_at, created_by, updated_by) VALUES ";
            }

            $sql .= '(';
            $sql .= self::wrapInQuotes($score->id) . ', ';
            $sql .= self::wrapInQuotes($score->user_id) . ', ';
            $sql .= self::wrapInQuotes($score->value) . ', ';
            $sql .= self::wrapInQuotes($score->weight) . ', ';
            $sql .= self::wrapInQuotes($score->timestamp) . ', ';
            $sql .= (isset($score->created_at) ? self::wrapInQuotes($score->created_at) : "NULL") . ', ';
            $sql .= (isset($score->updated_at) ? self::wrapInQuotes($score->updated_at) : "NULL") . ', ';
            $sql .= (isset($score->created_by) ? self::wrapInQuotes($score->created_by) : "NULL") . ', ';
            $sql .= (isset($score->updated_by) ? self::wrapInQuotes($score->updated_by) : "NULL");
            $sql .= ')';
            if (! ($scores->last() === $score)) {
                $sql .= ', ';
            }
        }
        return $sql;
    }

    private static function wrapInQuotes($str) { return '"' . $str . '"'; }
}