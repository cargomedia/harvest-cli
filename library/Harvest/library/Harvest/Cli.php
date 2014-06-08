<?php

class Harvest_Cli extends CM_Cli_Runnable_Abstract {

    /**
     * @param int $project
     */
    public function projectWeek($project = null) {
        if (null === $project) {
            $project = CM_Config::get()->Harvest_Cli->defaultProject;
        }
        $project = (int) $project;
        $client = new Harvest_Api_Client();
        $data = $client->sendRequest('/projects/' . $project . '/entries', ['from' => '20140602', 'to' => '20140608']);

        $entryList = Functional\map($data, function (array $entry) {
            return $entry['day_entry'];
        });
        $groupedUser = Functional\group($entryList, function (array $entry) {
            return $entry['user_id'];
        });
        foreach ($groupedUser as &$entryListUser) {
            $entryListUser = Functional\group($entryListUser, function (array $entry) {
                return $entry['spent_at'];
            });
            foreach ($entryListUser as &$day) {
                $day = Functional\reduce_left($day, function (array $entry, $index, $collection, $reduction) {
                    return $reduction + $entry['hours'];
                }, 0);
            }
        }

        print_r($groupedUser);
    }

    public static function getPackageName() {
        return 'harvest';
    }
}
