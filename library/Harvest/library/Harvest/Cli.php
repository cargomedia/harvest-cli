<?php

class Harvest_Cli extends CM_Cli_Runnable_Abstract {

    /**
     * @param int $project
     */
    public function projectWeek($project = null) {
        if (null === $project) {
            $project = CM_Config::get()->Harvest_Cli->defaultProject;
        }

        $from = new DateTime('2014-06-02');
        $to = new DateTime('2014-06-08');
        $dayList = array();
        $day = clone $from;
        while ($day <= $to) {
            $dayList[] = clone $day;
            $day->add(new DateInterval('P1D'));
        }

        $users = $this->_getUsers();
        $projectHours = $this->_getProjectHoursByUser($project, $from, $to);

        $table = new Console_Table();
        $dayHeaderList = Functional\map($dayList, function (DateTime $day) {
            return $day->format('D j.n.');
        });
        $table->setHeaders(array_merge(array(null), $dayHeaderList));
        foreach ($users as $user) {
            $userId = $user['id'];
            $userFullname = $user['first_name'] . ' ' . $user['last_name'];
            if (isset($projectHours[$userId])) {
                $hours = $projectHours[$userId];
            } else {
                $hours = array();
            }
            $hoursByDayList = Functional\map($dayList, function (DateTime $day) use ($hours) {
                $dayKey = $day->format('Y-m-d');
                if (isset($hours[$dayKey])) {
                    $hours = (int) $hours[$dayKey];
                    if (0 === $hours) {
                        return '~';
                    }
                    return round($hours, 1);
                }
                return null;
            });
            $table->addRow(array_merge(array($userFullname), $hoursByDayList));
        }
        foreach ($dayList as $i => $day) {
            $table->setAlign(1 + $i, CONSOLE_TABLE_ALIGN_RIGHT);
        }
        $this->_getOutput()->write($table->getTable());
    }

    public static function getPackageName() {
        return 'harvest';
    }

    /**
     * @return Harvest_Api_Client
     */
    private function _getClient() {
        return new Harvest_Api_Client();
    }

    /**
     * @return array
     */
    private function _getUsers() {
        $data = $this->_getClient()->sendRequest('/people');
        $result = array();
        foreach ($data as $dataItem) {
            $user = $dataItem['user'];
            $result[$user['id']] = $user;
        }
        $result = Functional\select($result, function (array $user) {
            return (bool) $user['is_active'];
        });
        return $result;
    }

    /**
     * @param int      $project
     * @param DateTime $from
     * @param DateTime $to
     * @return array
     */
    private function _getProjectHoursByUser($project, DateTime $from, DateTime $to) {
        $project = (int) $project;
        $data = $this->_getClient()->sendRequest('/projects/' . $project . '/entries', ['from' => $from->format('Ymd'), 'to' => $to->format('Ymd')]);
        $data = Functional\map($data, function (array $entry) {
            return $entry['day_entry'];
        });

        $result = Functional\group($data, function (array $entry) {
            return $entry['user_id'];
        });
        foreach ($result as &$entryListUser) {
            $entryListUser = Functional\group($entryListUser, function (array $entry) {
                return $entry['spent_at'];
            });
            foreach ($entryListUser as &$day) {
                $day = Functional\reduce_left($day, function (array $entry, $index, $collection, $reduction) {
                    return $reduction + $entry['hours'];
                }, 0);
            }
        }
        return $result;
    }
}
