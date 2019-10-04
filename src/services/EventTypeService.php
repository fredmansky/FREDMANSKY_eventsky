<?php

namespace fredmansky\eventsky\services;

use craft\base\Component;
use craft\db\ActiveRecord;
use craft\db\Query;
use fredmansky\eventsky\models\EventType;
use fredmansky\eventsky\records\EventTypeRecord;

class EventTypeService extends Component
{
    private $eventTypes;

    public function init()
    {
        parent::init();
    }
    
    public function getAllSections()
    {
        if ($this->eventTypes !== null) {
            return $this->$eventTypes;
        }

        $results = $this->createEventTypeQuery()
            ->all();

        $this->eventTypes = [];

        foreach ($results as $result) {
//            if (!empty($result['previewTargets'])) {
//                $result['previewTargets'] = Json::decode($result['previewTargets']);
//            } else {
//                $result['previewTargets'] = [];
//            }

            $this->eventTypes[] = new EventType($result);
        }

        return $this->eventTypes;
    }

    public function byId(int $id): ?ActiveRecord
    {
        return EventTypeRecord::find()
            ->where(['=', 'id', $id])
            ->with(['fieldLayout'])
            ->one();
    }

    private function createEventTypeQuery(): Query
    {
        // todo: remove schema version condition after next beakpoint
        $condition = null;
        $joinCondition = '[[structures.id]] = [[sections.structureId]]';
        $schemaVersion = Craft::$app->getInstalledSchemaVersion();
        if (version_compare($schemaVersion, '3.1.19', '>=')) {
            $condition = ['sections.dateDeleted' => null];
            $joinCondition = [
                'and',
                $joinCondition,
                ['structures.dateDeleted' => null]
            ];
        }

        $query = (new Query())
            ->select([
                'sections.id',
                'sections.structureId',
                'sections.name',
                'sections.handle',
                'sections.type',
                'sections.enableVersioning',
                'sections.uid',
                'structures.maxLevels',
            ])
            ->leftJoin('{{%structures}} structures', $joinCondition)
            ->from(['{{%sections}} sections'])
            ->where($condition)
            ->orderBy(['name' => SORT_ASC]);

        // todo: remove schema version conditions after next beakpoint
        if (version_compare($schemaVersion, '3.2.1', '>=')) {
            $query->addSelect('sections.propagationMethod');
        }
        if (version_compare($schemaVersion, '3.2.6', '>=')) {
            $query->addSelect('sections.previewTargets');
        }

        return $query;
    }
}
