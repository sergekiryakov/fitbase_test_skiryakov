<?php

namespace frontend\models\queries;

/**
 * This is the ActiveQuery class for [[\frontend\models\Client]].
 *
 * @see \frontend\models\Client
 */
class ClientQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \frontend\models\Client[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \frontend\models\Client|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
