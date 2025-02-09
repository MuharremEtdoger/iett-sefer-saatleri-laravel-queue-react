<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use App\Models\iettsefersaatleri;

class iettProcess implements ShouldQueue
{
    use Queueable;
    protected $code;
    /**
     * Create a new job instance.
     */
    public function __construct($code)
    {
        $this->code=$code;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //sleep(1);
        $code=$this->code;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://iett.istanbul/tr/RouteStation/GetScheduledDepartureTimes");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 
                  http_build_query(
                    array(
                        'rstart' => '',
                        'rend' => '',
                        'timeschule' => '',
                        'freq' => '',
                        'lngid' => 1,
                        'hCode' => $code,
                    )
                  )
        );
        // Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);    
        iettsefersaatleri::where('code',$code)->update(['html'=>$server_output]);    
        Log::info($code.' Sefer Saatleri Güncellendi');
    }
}
