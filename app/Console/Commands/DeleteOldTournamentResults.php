<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TournamentResult;
use Carbon\Carbon;

class DeleteOldTournamentResults extends Command
{
    protected $signature = 'tournament-results:cleanup-old';
    protected $description = '30日以上前の del_flg=1 の大会結果を物理削除';

    public function handle()
    {
        $threshold = Carbon::now()->subDays(30);

        $deleted = TournamentResult::where('del_flg', 1)
            ->where('updated_at', '<', $threshold)
            ->delete();

        $this->info("✅ {$deleted} 件の古い大会結果を削除しました。");
    }
}
//php artisan tournament-results:cleanup-old 手動実行

//1. Linux サーバに SSH 接続する（例：VPSやAWS、Xserverなど）ssh username@your-server-ip
//2. cron を開く  crontab -e  ※ 初めての場合「エディタはどれにしますか？」と聞かれるので nano を選べばOK
//3. 以下を追記
//* * * * * cd /var/www/html/your-laravel-project && php artisan schedule:run >> /dev/null 2>&1