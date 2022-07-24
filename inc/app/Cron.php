<?php
/**
 * Created by seonarnia.com.
 * User: gerasymenkoph@gmail.com
 */

declare(strict_types=1);

namespace Foks;

use Foks\Export\Export;
use Foks\Import\Import;
use Foks\Log\Logger;

class Cron
{
    public function __construct()
    {
        self::declaration();
        add_filter('cron_schedules', array(__CLASS__, 'cronTimes'));
        add_action('wp', array(__CLASS__, 'registration'));
    }

    /**
     * @param $schedules
     * @return mixed
     */
    public static function cronTimes($schedules)
    {
        $schedules['one_min'] = [
            'interval' => 60,
            'display' => 'Every 1 min'
        ];
        $schedules['one_hour'] = [
            'interval' => 3600,
            'display' => 'Every 1 hour'
        ];
        $schedules['four_hour'] = [
            'interval' => 14400,
            'display' => 'Every 4 hour'
        ];
        $schedules['one_day'] = [
            'interval' => 43200,
            'display' => 'Every 1 day'
        ];
        $schedules['three_day'] = [
            'interval' => 129600,
            'display' => 'Every 3 day'
        ];

        return $schedules;
    }

    public static function registration(): void
    {
        $option_time = (int)get_option('foks_update');

        if (!wp_next_scheduled('ImportProducts')) {
            switch ($option_time) {
                case 1:
                    wp_schedule_event(time(), 'one_hour', 'ImportProducts');
                    break;
                case 4:
                    wp_schedule_event(time(), 'four_hour', 'ImportProducts');
                    break;
                default:
                    wp_schedule_event(time(), 'one_day', 'ImportProducts');
                    break;
            }
        }

    }

    /**
     * @return void
     */
    public static function declaration(): void
    {
        $pref = 'action_';
        $class_methods = get_class_methods(get_called_class());

        foreach ($class_methods as $name) {
            $need = strpos($name, $pref);
            $short = str_replace($pref, '', $name);

            if ($need === 0) {
                add_action($short, array(get_called_class(), $name));
            }
        }
    }

    /**
     * @throws \Exception
     */
    public static function action_ImportProducts(): void
    {
        $file = get_option('foks_import');

        if ($file) {
            $xml = file_get_contents($file);
            Logger::file($xml, 'foks_import', 'xml');
            $file_path = FOKS_URL . '/logs/foks_import.xml';
            Import::importData($file_path);
            Export::generateXML();
        }
    }
}
