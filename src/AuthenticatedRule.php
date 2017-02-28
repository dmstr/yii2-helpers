<?php

namespace dmstr\helpers;

use yii\rbac\Rule;

class AuthenticatedRule extends Rule
{
    public function execute($user, $item, $params)
    {
        return !\Yii::$app->user->isGuest;
    }
}