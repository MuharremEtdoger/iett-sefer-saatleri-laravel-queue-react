<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\iettProcess;
use App\Models\iettsefersaatleri;
use App\Http\Resources\iettResource;
use Artisan;

class iettController extends Controller
{
    public function startQueueIETTProcess(){
        $exitCode = Artisan::call('queue:work --stop-when-empty', []);
        echo json_encode(array('state'=>1,'desc'=>'Kuyruk Başladı'));
        exit;
    }
    public function generateIETTSeferSaatleri(){
        $data = json_decode(iettResource::collection(iettsefersaatleri::all())->toJson());
        if($data){
            foreach($data as $_data){
                iettProcess::dispatch($_data->code);
            }
        }
        echo json_encode(array('state'=>1,'desc'=>'Kuyruk Oluşturuldu'));
        exit;
    }
    public function generateIETTSeferSaatleriTable(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://iett.istanbul/tr/RouteStation/GetRouteStation?key=&langid=1");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
        $server_output = curl_exec($ch);
        $search = array(
            '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
            '/[^\S ]+\</s',     // strip whitespaces before tags, except space
            '/(\s)+/s',         // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/' // Remove HTML comments
        );
        $replace = array(
            '>',
            '<',
            '\\1',
            ''
        );
        $buffer = preg_replace($search, $replace, $server_output);
        $buffer=str_replace(array('> <'),array('><'),$buffer);
        preg_match_all('@<div class="line-item">(.*?)</div>@si',$buffer,$_items);
        $items=$_items[0];
        if($items){
            $duraklar=array();
            foreach($items as $_satir){
                preg_match_all('@<a href="(.*?)">(.*?)</a>@si',$_satir,$link);
                preg_match_all('@<span>(.*?)</span><p>@si',$_satir,$kod);
                preg_match_all('@<p>(.*?)</p>@si',$_satir,$baslik);
                $_temp=array();
                $_temp['link']=$link[1][0];
                $_temp['code']=html_entity_decode($kod[1][0]);
                $_temp['title']=html_entity_decode($baslik[1][0]);
                $_temp['html']='';
                $duraklar[]=$_temp;                
            }
        }
        if($duraklar){
            foreach($duraklar as $durak){
                $sefer=iettsefersaatleri::where('code', $durak['code'])->first();
                

                if(!$sefer){
                    $_durak = iettsefersaatleri::create([
                        'code' => $durak['code'],
                        'title' => $durak['title'],
                        'html' => $durak['html'],
                    ]);
                }
            }
        }
        echo json_encode(array('state'=>1,'desc'=>'Duraklar veritabanına eklendi'));
        exit;        
     
    }
    
}
