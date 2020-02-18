<?php
declare(strict_types=1);

namespace App\Option;

/**
 * Helper class to generate the data required to build the OPTIONS required for
 * a single HTTP Verb, in this case DELETE
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Delete extends Option
{
    static private function reset()
    {
        self::resetBase();

        self::$authentication = false;
        self::$description = null;
    }

    static public function init(): Delete
    {
        self::$instance = new Delete();
        self::$instance->reset();

        return self::$instance;
    }

    static protected function build()
    {
        // Not necessary for this simple Option
    }

    static public function option(): array
    {
        return [
            'DELETE' => [
                'description' => self::$description,
                'authentication' => [
                    'required' => self::$authentication,
                    'authenticated' => self::$authenticated
                ]
            ]
        ];
    }
}
