<?php
namespace common\models\queries;

use yii\db\ActiveQuery;
use Yii;

/**
 * ActiveQuery helpers for status-based soft delete.
 */
class SoftDeleteQuery extends ActiveQuery
{
    public function active(): self
    {
        $id = Yii::$app->statusManager->getIdByCode('active');
        return $this->andWhere([$this->getPrimaryTableName().'.status' => $id]);
    }

    public function deleted(): self
    {
        $id = Yii::$app->statusManager->getIdByCode('deleted');
        return $this->andWhere([$this->getPrimaryTableName().'.status' => $id]);
    }

    public function notDeleted(): self
    {
        $id = Yii::$app->statusManager->getIdByCode('deleted');
        return $this->andWhere(['<>', $this->getPrimaryTableName().'.status', $id]);
    }

    protected function getPrimaryTableName(): string
    {
        // ActiveQuery::getPrimaryTableName exists in Yii2 >=2.0.14, fallback:
        $meta = $this->modelClass ? $this->modelClass::tableName() : '';
        return $meta;
    }
}
