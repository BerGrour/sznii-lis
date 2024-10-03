<?php

use common\models\User;
use yii\db\Migration;

/**
 * Class m240516_125447_user
 */
class m240516_125447_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $user = new User();
        $user->username = 'administrator';
        $user->email = 'administrator@administrator.com';
        $user->setPassword('administrator');
        $user->generateAuthKey();
        $user->staff_id = 2;
        $user->save(false);

        $user = new User();
        $user->username = 'rii_3';
        $user->email = 'rii_3@rii_3.com';
        $user->setPassword('rii_3');
        $user->generateAuthKey();
        $user->staff_id = 3;
        $user->save(false);

        $user = new User();
        $user->username = 'rps_4';
        $user->email = 'rps_4@rps_4.com';
        $user->setPassword('rps_4');
        $user->generateAuthKey();
        $user->staff_id = 4;
        $user->save(false);

        $user = new User();
        $user->username = 'glu_5';
        $user->email = 'glu_5@glu_5.com';
        $user->setPassword('glu_5');
        $user->generateAuthKey();
        $user->staff_id = 5;
        $user->save(false);

        $user = new User();
        $user->username = 'gvn_6';
        $user->email = 'gvn_6@gvn_6.com';
        $user->setPassword('gvn_6');
        $user->generateAuthKey();
        $user->staff_id = 6;
        $user->save(false);

        $user = new User();
        $user->username = 'bdp_7';
        $user->email = 'bdp_7@bdp_7.com';
        $user->setPassword('bdp_7');
        $user->generateAuthKey();
        $user->staff_id = 7;
        $user->save(false);

        $user = new User();
        $user->username = 'bav_8';
        $user->email = 'bav_8@bav_8.com';
        $user->setPassword('bav_8');
        $user->generateAuthKey();
        $user->staff_id = 8;
        $user->save(false);

        $user = new User();
        $user->username = 'rak_9';
        $user->email = 'rak_9@rak_9.com';
        $user->setPassword('rak_9');
        $user->generateAuthKey();
        $user->staff_id = 9;
        $user->save(false);

        $user = new User();
        $user->username = 'lyak_10';
        $user->email = 'lyak_10@lyak_10.com';
        $user->setPassword('lyak_10');
        $user->generateAuthKey();
        $user->staff_id = 10;
        $user->save(false);

        $user = new User();
        $user->username = 'lek_11';
        $user->email = 'lek_11@lek_11.com';
        $user->setPassword('lek_11');
        $user->generateAuthKey();
        $user->staff_id = 11;
        $user->save(false);

        $user = new User();
        $user->username = 'pek_12';
        $user->email = 'pek_12@pek_12.com';
        $user->setPassword('pek_12');
        $user->generateAuthKey();
        $user->staff_id = 12;
        $user->save(false);

        $user = new User();
        $user->username = 'gvm_13';
        $user->email = 'gvm_13@gvm_13.com';
        $user->setPassword('gvm_13');
        $user->generateAuthKey();
        $user->staff_id = 13;
        $user->save(false);

        $user = new User();
        $user->username = 'udm_14';
        $user->email = 'udm_14@udm_14.com';
        $user->setPassword('udm_14');
        $user->generateAuthKey();
        $user->staff_id = 14;
        $user->save(false);

        $user = new User();
        $user->username = 'ism_15';
        $user->email = 'ism_15@ism_15.com';
        $user->setPassword('ism_15');
        $user->generateAuthKey();
        $user->staff_id = 15;
        $user->save(false);

        $user = new User();
        $user->username = 'iam_16';
        $user->email = 'iam_16@iam_16.com';
        $user->setPassword('iam_16');
        $user->generateAuthKey();
        $user->staff_id = 16;
        $user->save(false);

        $user = new User();
        $user->username = 'zvb_17';
        $user->email = 'zvb_17@zvb_17.com';
        $user->setPassword('zvb_17');
        $user->generateAuthKey();
        $user->staff_id = 17;
        $user->save(false);

        $user = new User();
        $user->username = 'bdb_18';
        $user->email = 'bdb_18@bdb_18.com';
        $user->setPassword('bdb_18');
        $user->generateAuthKey();
        $user->staff_id = 18;
        $user->save(false);

        $user = new User();
        $user->username = 'usb_19';
        $user->email = 'usb_19@usb_19.com';
        $user->setPassword('usb_19');
        $user->generateAuthKey();
        $user->staff_id = 19;
        $user->save(false);

        $user = new User();
        $user->username = 'vnd_20';
        $user->email = 'vnd_20@vnd_20.com';
        $user->setPassword('vnd_20');
        $user->generateAuthKey();
        $user->staff_id = 20;
        $user->save(false);

        $user = new User();
        $user->username = 'rat_21';
        $user->email = 'rat_21@rat_21.com';
        $user->setPassword('rat_21');
        $user->generateAuthKey();
        $user->staff_id = 21;
        $user->save(false);

        $user = new User();
        $user->username = 'cow_marusya_22';
        $user->email = 'cow_marusya_22@cow_marusya_22.com';
        $user->setPassword('cow_marusya_22');
        $user->generateAuthKey();
        $user->staff_id = 22;
        $user->save(false);

        $user = new User();
        $user->username = 'cow_mashka_23';
        $user->email = 'cow_mashka_23@cow_mashka_23.com';
        $user->setPassword('cow_mashka_23');
        $user->generateAuthKey();
        $user->staff_id = 23;
        $user->save(false);

        $user = new User();
        $user->username = 'rat_useless_24';
        $user->email = 'rat_useless_24@rat_useless_24.com';
        $user->setPassword('rat_useless_24');
        $user->generateAuthKey();
        $user->staff_id = 24;
        $user->save(false);

        $user = new User();
        $user->username = 'org_25';
        $user->email = 'org_25@org_25.com';
        $user->setPassword('org_25');
        $user->generateAuthKey();
        $user->organization_id = 1;
        $user->save(false);

        $user = new User();
        $user->username = 'org_26';
        $user->email = 'org_26@org_26.com';
        $user->setPassword('org_26');
        $user->generateAuthKey();
        $user->organization_id = 2;
        $user->save(false);

        $user = new User();
        $user->username = 'org_27';
        $user->email = 'org_27@org_27.com';
        $user->setPassword('org_27');
        $user->generateAuthKey();
        $user->organization_id = 3;
        $user->save(false);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute(<<<SQL
            DELETE FROM user WHERE username <> "***************";
        SQL
        );
    }
}
