<?php namespace App\models;

require_once "app/models/Buyers.php";

use app\models\TestAlfa;
use Detection\MobileDetect;

class User extends TestAlfa
{
    public function setProps(array $params): array
    {
        $this->site = $params['page'] ?? '';

        $this->time_live = isset($params['time_live']) ? (string)$params['time_live'] : '';

        $this->scroll = $params['scroll'] ?? 0.00;

        if (isset($params['ref']) && isset($params['page'])) {
            $this->history_clicks = json_encode(['ref' => $params['ref'], 'page' => $params['page']], JSON_FORCE_OBJECT);
        }

        $this->ip = (empty($_SERVER['HTTP_CLIENT_IP'])) ? empty($_SERVER['HTTP_X_FORWARDED_FOR']) ?
            $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['HTTP_CLIENT_IP'];

        $this->user_agent = $_SERVER['HTTP_USER_AGENT'];
        $this->definedDevice();
        $this->definedBrowser();
        $this->definedPlatform();
        $this->cookie_id = $_COOKIE['cookie_id'];

        return [
            'cookie_id' => $this->cookie_id,
            'ip' => $this->ip,
        ];
    }

    private function definedDevice(): void
    {
        $detect = new MobileDetect;

        if ($detect->isMobile()) {
            $device = 'Mobile';
        } elseif ($detect->isTablet()) {
            $device = 'Tablet';
        } else {
            $device = 'Desktop';
        }

        $this->device = $device;
    }

    private function definedBrowser(): void
    {
        $detect = new MobileDetect;

        if ($detect->isChrome()) {
            $browser = 'Chrome';
        } elseif ($detect->isSafari()) {
            $browser = 'Safari';
        } elseif ($detect->isFirefox()) {
            $browser = 'Firefox';
        } elseif ($detect->isOpera()) {
            $browser = 'Opera';
        } elseif ($detect->isIE()) {
            $browser = 'Internet Explorer';
        } elseif ($detect->isEdge()) {
            $browser = 'Edge';
        } else {
            $date_of_browser = explode(";", $_SERVER['HTTP_SEC_CH_UA']);
            $date_of_browser = explode(", ", $date_of_browser[2]);
            $browser = str_replace("\"", "", $date_of_browser[1]);
        }

        $this->browser = $browser;
    }

    private function definedPlatform(): void
    {
        $detect = new MobileDetect;

        if ($detect->isAndroidOS()) {
            $os = 'Android';
        } elseif ($detect->isiOS()) {
            $os = 'iOS';
        } elseif ($detect->isWindowsPhoneOS()) {
            $os = 'Windows Phone';
        } else {
            $os = str_replace("\"", "", $_SERVER['HTTP_SEC_CH_UA_PLATFORM']);
        }

        $this->platform = $os;
    }

    public function definedUser(array $fromHeader): int
    {
        $user_id = $this->isUniqueUser($fromHeader);

        if ($user_id === 0 && !$this->hasErrors()) {

            return $this->create();
        } else if ($this->hasErrors()) {

            return 0;
        } else {
            if (empty($this->site) && $this->scroll === 0.00 && empty($this->time_live)) {
                $this->getOne($user_id);

                return $user_id;
            } else {
                if ($this->update($user_id)) {
                    $this->getOne($user_id);

                    return $user_id;
                }
            }

            return 0;
        }
    }
}