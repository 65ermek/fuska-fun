<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;

class ContentGuard {
    public function score(string $title, string $desc): array {
        $text = mb_strtolower($title.' '.$desc,'UTF-8');
        $score=0; $reasons=[];
        foreach (config('contentguard.block_keywords',[]) as $kw){
            if (mb_strpos($text, mb_strtolower($kw))!==false){ $score+=30; $reasons[]="kw:$kw"; }
        }
        foreach (config('contentguard.block_regex',[]) as $rx){
            if (preg_match($rx,$text)){ $score+=25; $reasons[]="rx"; }
        }
        if (preg_match_all('/(https?:\/\/|www\.)\S+/i',$text,$m)){
            $cnt=count($m[0]); if ($cnt>config('contentguard.limits.max_links')){ $score+=40; $reasons[]="links:$cnt"; }
        }
        foreach (config('contentguard.good_keywords',[]) as $kw){
            if (mb_strpos($text, mb_strtolower($kw))!==false){ $score-=10; }
        }
        return ['risk'=>max(0,$score),'reasons'=>$reasons];
    }
    public function makeFingerprint(string $title, string $desc): string {
        $norm=preg_replace('/\s+/',' ',mb_strtolower($title.'|'.$desc)); return substr(sha1($norm),0,20);
    }
    public function isDuplicate(string $fp): bool {
        return DB::table('job_dupes')->where('fp',$fp)->where('created_at','>=',now()->subDays(config('contentguard.limits.dup_days')))->exists();
    }
}
