<?php

use yii\db\Migration;

/**
 * Class m240806_075052_add_new_permissions_to_rbac
 */
class m240806_075052_add_new_permissions_to_rbac extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        // Возможность просматривать конфиденциальные файлы
        $seeFiles = $auth->createPermission('seeFiles');
        $seeFiles->description = 'Возможность просматривать конфиденциальные файлы';
        $auth->add($seeFiles);

        $booker = $auth->getRole('booker');
        $auth->addChild($booker, $seeFiles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $auth->remove($auth->getPermission('seeFiles'));
    }
}
