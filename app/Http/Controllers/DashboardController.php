<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Member;

class DashboardController extends Controller
{
    // 学年別 在籍者数
    public function gradeStats()
    {
        \Log::debug(' gradeStats 呼び出し');

        $gradeLabels = config('labels.grade_categories'); // ← ここで読み込み

        $result = Member::select('grade_category', DB::raw('count(*) as count'))
            ->where('status', 1)
            ->groupBy('grade_category')
            ->orderBy('grade_category')
            ->get()
            ->map(function ($row) use ($gradeLabels) {
                $row->grade_name = $gradeLabels[$row->grade_category] ?? '不明';
                return $row;
            });

        \Log::debug(' 集計結果（名称付き）: ', $result->toArray());

        return response()->json($result);
    }

    // 有効資格の保有者数
    public function licenseStats()
    {
        \Log::debug('🎖 licenseStats 呼び出し');

        $result = DB::table('t_h_credentials as hc')
            ->join('t_licenses as l', 'hc.license_id', '=', 'l.license_id')
            ->select('l.licensekindsname', DB::raw('count(*) as count'))
            ->where('hc.valid_flg', 1)
            ->where('hc.del_flg', 0)
            ->groupBy('l.licensekindsname')
            ->orderBy('l.licensekindsname')
            ->get();

        \Log::debug('🎖 集計結果: ', $result->toArray());

        return response()->json($result);
    }
}
