<?php

namespace App\Http\Controllers\Backend;

use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function index () {
        return view('backend.setting', Setting::getSettings());
    }

    /**
     * store
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store (Request $request) {

        // we do not have an update. so we delete the old values
        Setting::where('key', '!=', NULL)->delete();

        // except _token
        foreach ($request->except('_token') as $key => $value) {
            $setting = new Setting;
            $setting->key = $key;
            $setting->value = $request->$key;
            $setting->save();
        }

        return redirect()->route('admin.setting.index');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setwebhook (Request $request) {

        $result = $this->sendTelegramData('setwebhook', [
            'query' => ['url' => $request->url . '/' . \Telegram::getAccessToken()]
        ]);

        return redirect()->route('admin.setting.index')->with('status', $result);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getwebhookinfo (Request $request) {

        $result = $this->sendTelegramData('getWebhookInfo');

        return redirect()->route('admin.setting.index')->with('status', $result);

    }

    /**
     * send telegram data
     * @param string $route
     * @param array $params
     * @param string $method
     * @return string
     */
    public function sendTelegramData ( $route = '', $params = [], $method = 'POST' ) {

        $client = new \GuzzleHttp\Client( ['base_uri' => 'https://api.telegram.org/bot' . \Telegram::getAccessToken() . '/'] );
        $result = $client->request( $method, $route, $params);

        return (string) $result->getBody();
    }
}