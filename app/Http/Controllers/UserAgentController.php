<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Client;
use App\Bot;
use App\User;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\DeviceParserAbstract;

class UserAgentController extends Controller
{
    
    public function detail(Request $request)
    {

        // Проверяем, передано ли что-нибудь в качестве GET-параметра,
        // если передано - сохраняем запрос, если нет - сохраняем текущий UserAgent из HTTP-заголовка
        if ($request->query('ua')) {
            $UserAgent = $request->query('ua');
        } else {
            $UserAgent = $_SERVER['HTTP_USER_AGENT'];
        }
       
        // Разбираем сохранённое значение, передав его библиотеке Device Detector
        $dd = new DeviceDetector($UserAgent);
        $dd->parse();

        // Заранее указываем, что в ответ мы возвращаем данные формате JSON
        header('Content-Type: application/json');
        
        // Проверяем, является ли переданное значение ботом, клиентским UserAgent или чем-то другим
        // Проверяем, является ли переданное значение ботом
        if ($dd->isBot()) {

            // Получаем массив с информацией о боте
            $botInfo = $dd->getBot();

            // Преобразуем сохраненный массив с информацией о боте в формат JSON
            $jsonBI = json_encode($botInfo);

            // Возвращаем информацию о боте в формате JSON
            echo $jsonBI;

            // Записываем данные о боте в БД в таблицу с ботами
            function realBot($botInfo) {
                $bot = new Bot;

                // Если это универсальный бот (Generic Bot), у которого есть только значение NAME,
                // то сохраняем только поле NAME, а все остальные значения передаём пустыми.
                // В ином случае, сохраняем данные о боте полностью
                if ($botInfo["name"] == 'Generic Bot') {
                    $bot->name = $botInfo["name"];
                    $bot->category = "";
                    $bot->url = "";
                    $bot->producer_name = "";
                    $bot->producer_url = "";
                    $bot->save();
                } else {
                    $bot->name = $botInfo["name"];
                    $bot->category = $botInfo["category"];
                    $bot->url = $botInfo["url"];
                    $bot->producer_name = $botInfo["producer"]["name"];
                    $bot->producer_url = $botInfo["producer"]["url"];
                    $bot->save();
                }
            }
            realBot($botInfo);

        // Проверяем, является ли переданное значение клиентским UserAgent
        } else if ($dd->getClient()) {

            // Получаем информацию о клиентском UserAgent
            $clientInfo = $dd->getClient(); 
            $osInfo = $dd->getOs();
            $device = $dd->getDeviceName();
            $brand = $dd->getBrandName();
            $model = $dd->getModel();

            // Объединяем в один массив массив с информацией о клиенте и массив с информацией об ОС
            $resultInfo = array_merge($clientInfo, $osInfo);

            // Добавляем в объединенный массив строки, содержащие информацию об устройстве, бренде и модели
            $resultInfo += ['device'=>$device];
            $resultInfo += ['brand'=>$brand];
            $resultInfo += ['model'=>$model];

            // Преобразуем полученный массив с информацией о клиентском UserAgent в формат JSON
            $jsonCI = json_encode($resultInfo);

            // Возвращаем информацию о клиентском UserAgent в формате JSON
            echo $jsonCI;

            // Записываем данные о клиентском UserAgent в БД в таблицу клиентскими UserAgent
            function addClient($resultInfo) {
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

        // Если полученные Get-параметры - это не бот и не клиентский UserAgent,
        // то, соответственно, говорим, что в переданных Get-параметрах мы не обнаружили UserAgent
        } else {
            echo "Sorry, there is no UserAgent in your request";
        }

    }
}

?>