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

use Illuminate\Http\Request;

class UserAgentController extends Controller
{
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return View
     */
    public function noparam(Request $request)
{
    echo "111";
    echo $request->query('ua');
}

    public function detail($UserAgent)
    {
        echo "000";
        echo $UserAgent;
        echo $_SERVER['HTTP_USER_AGENT'];



        // OPTIONAL: Set version truncation to none, so full versions will be returned
        // By default only minor versions will be returned (e.g. X.Y)
        // for other options see VERSION_TRUNCATION_* constants in DeviceParserAbstract class
        DeviceParserAbstract::setVersionTruncation(DeviceParserAbstract::VERSION_TRUNCATION_NONE);

        $userAgent = $_SERVER['HTTP_USER_AGENT']; // change this to the useragent you want to parse

        // $userAgent = str_replace(' ', '', $userAgent);
        // echo $userAgent;

        $dd = new DeviceDetector($userAgent);

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

        if ($dd->isBot()) {
         // handle bots,spiders,crawlers,...
         $botInfo = $dd->getBot();
        } else {
         $clientInfo = $dd->getClient(); // holds information about browser, feed reader, media player, ...
         $osInfo = $dd->getOs();
         $device = $dd->getDeviceName();
         $brand = $dd->getBrandName();
        $model = $dd->getModel();
         print_r($osInfo);
        }
       }
}

?>