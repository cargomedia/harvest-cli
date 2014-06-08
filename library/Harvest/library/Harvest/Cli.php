<?php

class Harvest_Cli extends CM_Cli_Runnable_Abstract {

    /**
     * @param int           $project
     * @param DateTime|null $from
     */
    public function projectWeek($project = null, DateTime $from = null) {
        if (null === $project) {
            $project = CM_Config::get()->Harvest_Cli->defaultProject;
        }
        if (null === $from) {
            $from = new DateTime('last monday');
        }
        $to = clone $from;
        $to->add(new DateInterval('P7D'));

        /** @var DateTime[] $dayList */
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

        $columnUsers = Functional\map($users, function (array $user) {
            return $user['first_name'] . ' ' . $user['last_name'];
        });
        $table->addCol($columnUsers, 0);

        foreach ($dayList as $i => $day) {
            $columnHours = Functional\map($users, function (array $user) use ($projectHours, $day) {
                $hours = null;
                $dayKey = $day->format('Y-m-d');
                if (isset($projectHours[$user['id']][$dayKey])) {
                    $hours = round($projectHours[$user['id']][$dayKey], 1);
                    if (0 == $hours) {
                        $hours = '~';
                    }
                }
                return $hours;
            });

            $table->addCol($columnHours, $i + 1);
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
