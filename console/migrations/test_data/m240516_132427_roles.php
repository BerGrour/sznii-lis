<?php

use common\models\User;
use yii\db\Migration;

/**
 * Class m240516_132427_roles
 */
class m240516_132427_roles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        
        $admin = $auth->getRole('admin');
        $registration = $auth->getRole('registration');
        $laboratory = $auth->getRole('laboratory');
        $booker = $auth->getRole('booker');
        $client = $auth->getRole('client');

        $administrator = User::findByUsername('administrator');
        $auth->assign($admin, $administrator->id);
        
        $rii_3 = User::findByUsername('rii_3');
        $auth->assign($registration, $rii_3->id);

        $rps_4 = User::findByUsername('rps_4');
        $auth->assign($registration, $rps_4->id);

        $glu_5 = User::findByUsername('glu_5');
        $auth->assign($booker, $glu_5->id);

        $gvn_6 = User::findByUsername('gvn_6');
        $auth->assign($booker, $gvn_6->id);

        $bdp_7 = User::findByUsername('bdp_7');
        $auth->assign($booker, $bdp_7->id);

        $bav_8 = User::findByUsername('bav_8');
        $auth->assign($booker, $bav_8->id);

        $rak_9 = User::findByUsername('rak_9');
        $auth->assign($laboratory, $rak_9->id);

        $lyak_10 = User::findByUsername('lyak_10');
        $auth->assign($laboratory, $lyak_10->id);

        $lek_11 = User::findByUsername('lek_11');
        $auth->assign($laboratory, $lek_11->id);

        $pek_12 = User::findByUsername('pek_12');
        $auth->assign($laboratory, $pek_12->id);

        $gvm_13 = User::findByUsername('gvm_13');
        $auth->assign($laboratory, $gvm_13->id);

        $udm_14 = User::findByUsername('udm_14');
        $auth->assign($laboratory, $udm_14->id);

        $ism_15 = User::findByUsername('ism_15');
        $auth->assign($laboratory, $ism_15->id);

        $iam_16 = User::findByUsername('iam_16');
        $auth->assign($laboratory, $iam_16->id);

        $zvb_17 = User::findByUsername('zvb_17');
        $auth->assign($laboratory, $zvb_17->id);

        $bdb_18 = User::findByUsername('bdb_18');
        $auth->assign($laboratory, $bdb_18->id);

        $usb_19 = User::findByUsername('usb_19');
        $auth->assign($laboratory, $usb_19->id);

        $vnd_20 = User::findByUsername('vnd_20');
        $auth->assign($laboratory, $vnd_20->id);

        $rat_21 = User::findByUsername('rat_21');
        $auth->assign($laboratory, $rat_21->id);

        $cow_marusya_22 = User::findByUsername('cow_marusya_22');
        $auth->assign($laboratory, $cow_marusya_22->id);

        $cow_mashka_23 = User::findByUsername('cow_mashka_23');
        $auth->assign($laboratory, $cow_mashka_23->id);

        $rat_useless_24 = User::findByUsername('rat_useless_24');
        $auth->assign($laboratory, $rat_useless_24->id);

        $org_25 = User::findByUsername('org_25');
        $auth->assign($client, $org_25->id);

        $org_26 = User::findByUsername('org_26');
        $auth->assign($client, $org_26->id);

        $org_27 = User::findByUsername('org_27');
        $auth->assign($client, $org_27->id);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute(<<<SQL
            DELETE FROM auth_assignment WHERE user_id <> 1;
        SQL
        );
    }
}
