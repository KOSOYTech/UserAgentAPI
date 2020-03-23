<?php

// namespace App\Http\Controllers;

// class UserAgentController extends ApiControllers
// {

//     public function __construct(UserAgent $model)
//     {
//         $this->model = $model;
//     }

// }

namespace App\Http\Controllers;

require_once __DIR__.'/../../../vendor/autoload.php';

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\DeviceParserAbstract;

use App\User;
use App\Http\Controllers\Controller;
use App\Client;
use App\Bot;

use Illuminate\Http\Request;

class UserAgentController extends Controller
{

    public function addClient()
    {
    $client = new Client;
    $client->type = "Testing";
    $client->name = "Testing";
    $client->short_name = "Testing";
    $client->version = "Testing";
    $client->engine = "Testing";
    $client->engine_version = "Testing";
    $client->platform = "Testing";
    $client->device = "Testing";
    $client->brand = "Testing";
    $client->model = "Testing";
    $client->save();
    }
    
    public function detail(Request $request)
    {

        if ($request->query('ua')) {
            $UserAgent = $request->query('ua');
            //echo 'The request: ' . $UserAgent . '</br>';
        } else {
            $UserAgent = $_SERVER['HTTP_USER_AGENT'];
            //echo 'No request: ' . $UserAgent . '</br>';
        }
       
        // OPTIONAL: Set version truncation to none, so full versions will be returned
        // By default only minor versions will be returned (e.g. X.Y)
        // for other options see VERSION_TRUNCATION_* constants in DeviceParserAbstract class
        DeviceParserAbstract::setVersionTruncation(DeviceParserAbstract::VERSION_TRUNCATION_NONE);

        // $userAgent = str_replace(' ', '', $userAgent);
        // echo $userAgent;

        $dd = new DeviceDetector($UserAgent);

        // OPTIONAL: Set caching method
        // By default static cache is used, which works best within one php process (memory array caching)
        // To cache across requests use caching in files or memcache
        // $dd->setCache(new Doctrine\Common\Cache\PhpFileCache('./tmp/'));



        // OPTIONAL: Set custom yaml parser
        // By default Spyc will be used for parsing yaml files. You can also use another yaml parser.
        // You may need to implement the Yaml Parser facade if you want to use another parser than Spyc or [Symfony](https://github.com/symfony/yaml)
        // $dd->setYamlParser(new DeviceDetector\Yaml\Symfony());

        // OPTIONAL: If called, getBot() will only return true if a bot was detected  (speeds up detection a bit)
        // $dd->discardBotInformation();

        // OPTIONAL: If called, bot detection will completely be skipped (bots will be detected as regular devices then)
        // $dd->skipBotDetection();

        $dd->parse();

        header('Content-Type: application/json');
        if ($dd->isBot()) {
         // handle bots,spiders,crawlers,...
         $botInfo = $dd->getBot();
        //  print_r($botInfo);
         $jsonBI = json_encode($botInfo);
         echo $jsonBI;

        function realBot($botInfo)
        {
        $bot = new Bot;
        $bot->name = $botInfo["name"];
        $bot->category = $botInfo["category"];
        $bot->url = $botInfo["url"];
        $bot->producer_name = $botInfo["producer"]["name"];
        $bot->producer_url = $botInfo["producer"]["url"];
        $bot->save();
        }
        realBot($botInfo);

        } else {
         $clientInfo = $dd->getClient(); // holds information about browser, feed reader, media player, ...
         $osInfo = $dd->getOs();
         $device = $dd->getDeviceName();
         $brand = $dd->getBrandName();
        $model = $dd->getModel();
        //  print_r($clientInfo);
        //  print_r($osInfo);
        //  print_r($device);
        //  print_r($brand);
        //  print_r($model);
         
        $resultInfo = array_merge($clientInfo, $osInfo);
        $resultInfo += ['device'=>$device];
        $resultInfo += ['brand'=>$brand];
        $resultInfo += ['model'=>$model];
        //print_r($resultInfo);
        $jsonCI = json_encode($resultInfo);
        echo $jsonCI;

        function addClient($resultInfo)
        {
        $client = new Client;
        $client->type = $resultInfo["type"];
        $client->name = $resultInfo["name"];
        $client->short_name = $resultInfo["short_name"];
        $client->version = $resultInfo["version"];
        $client->engine = $resultInfo["engine"];
        $client->engine_version = $resultInfo["engine_version"];
        $client->platform = $resultInfo["platform"];
        $client->device = $resultInfo["device"];
        $client->brand = $resultInfo["brand"];
        $client->model = $resultInfo["model"];
        $client->save();
        }
        addClient($resultInfo);

        }


    }


    
}

?>