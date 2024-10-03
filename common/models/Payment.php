<?php

namespace common\models;

use common\extensions\traits\BasicMethodsTrait;
use Yii;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "payment".
 *
 * @property int $id
 * @property int $act_num
 * @property float $fact_sum
 * @property string $act_date
 * @property string|null $list_date
 * @property string|null $return_date
 * @property string|null $pay_date
 * @property int|null $file_act
 * @property int|null $file_act_client
 * @property int|null $file_pay
 * @property int|null $file_invoice
 * @property int $locked
 *
 * @property File $fileAct
 * @property File $fileActClient
 * @property File $filePay
 * @property File $fileInvoice
 * @property Batch $batch
 */
class Payment extends \yii\db\ActiveRecord
{
    use BasicMethodsTrait;

    const STATUS_NOTSENT = 'Заполняется';
    const STATUS_NOTPAY = 'Не оплачено';
    const STATUS_WITHOUT_ACT = 'В ожидании';
    const STATUS_COMPLETE = 'Завершено';
    const STATUS_CHECK = 'На проверке';

    public $uploadedFileAct;   // атрибут для загрузки файла с актом
    public $uploadedFileActClient;   // атрибут для загрузки файла с подписанным клиентом актом
    public $uploadedFilePay;   // атрибут для загрузки файла оплаты
    public $uploadedFileInvoice;   // атрибут для загрузки файла счёт-фактуры

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['act_num'], 'required'],
            [['act_date', 'list_date', 'return_date', 'fact_sum', 'pay_date'], 'default', 'value' => NULL],
            [['act_num', 'file_act', 'file_act_client', 'file_pay', 'file_invoice', 'locked'], 'integer'],
            [['fact_sum'], 'number'],
            [['act_date', 'return_date', 'pay_date', 'list_date'], 'date', 'format' => 'php:Y-m-d'],
            [['file_act'], 'exist', 'skipOnError' => true, 'targetClass' => File::class, 'targetAttribute' => ['file_act' => 'id']],
            [['file_act_client'], 'exist', 'skipOnError' => true, 'targetClass' => File::class, 'targetAttribute' => ['file_act_client' => 'id']],
            [['file_pay'], 'exist', 'skipOnError' => true, 'targetClass' => File::class, 'targetAttribute' => ['file_pay' => 'id']],
            [['file_invoice'], 'exist', 'skipOnError' => true, 'targetClass' => File::class, 'targetAttribute' => ['file_invoice' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'act_num' => 'Номер акта',
            'fact_sum' => 'Фактическая сумма',
            'act_date' => 'Дата заключения акта',
            'list_date' => 'Дата отправки оригиналов',
            'return_date' => 'Дата возврата оригиналов',
            'pay_date' => 'Дата оплаты',
            'file_act' => 'Файл акт',
            'file_act_client' => 'Файл с подписанным актом',
            'file_pay' => 'Файл счёт',
            'file_invoice' => 'Файл счёт-фактура',
            'locked' => 'Заблокировано',

            'uploadedFileAct' => 'Файл акта:',
            'uploadedFileActClient' => 'Файл подписанного акта:',
            'uploadedFilePay' => 'Файл счёт:',
            'uploadedFileInvoice' => 'Файл счёт-фактуры:',
            'sum' => 'Cумма',
            'status' => 'Статус',
            'organization_info' => 'Организация'
        ];
    }

    /**
     * Gets query for [[Batch]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBatch()
    {
        return $this->hasOne(Batch::class, ['payment_id' => 'id']);
    }

    /**
     * Gets query for [[FileAct]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFileAct()
    {
        return $this->hasOne(File::class, ['id' => 'file_act']);
    }

    /**
     * Gets query for [[FileActClient]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFileActClient()
    {
        return $this->hasOne(File::class, ['id' => 'file_act_client']);
    }

    /**
     * Gets query for [[FilePay]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFilePay()
    {
        return $this->hasOne(File::class, ['id' => 'file_pay']);
    }

    /**
     * Gets query for [[FileInvoice]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFileInvoice()
    {
        return $this->hasOne(File::class, ['id' => 'file_invoice']);
    }

    /**
     * Получение заголовка
     * @param int $len ограничение длины
     * @return string
     */
    public function getShortTitle($len = 50)
    {
        $result = StringHelper::truncate("Акт № {$this->act_num}", $len);
        return $result;
    }

    /**
     * Метод для проверки все ли атрибуты экземпляра заполнены
     * 
     * @return bool
     */
    public function checkSetAllValues()
    {
        if (
            isset($this->act_date)
            and isset($this->list_date)
            and isset($this->return_date)
            and isset($this->fact_sum)
            and isset($this->pay_date)
            and isset($this->file_act)
            and isset($this->file_act_client)
            and isset($this->file_pay)
            and isset($this->file_invoice)
        ) {
            return true;
        }
        return false;
    }

    /**
     * Получение статуса
     * 
     * @return string
     */
    public function getStatus()
    {
        if ($this->locked) {
            return '<span class="tooltip-custom tooltip-green" data-toggle="tooltip" title="Успешно завершено">' . self::STATUS_COMPLETE . '</span>';
        }
        if (!$this->act_date or !$this->list_date) {
            return '<span class="tooltip-custom tooltip-black" data-toggle="tooltip" title="Производится оформление акта">' . self::STATUS_NOTSENT . '</span>';
        }
        if (!$this->pay_date) {
            return '<span class="tooltip-custom tooltip-purple" data-toggle="tooltip" title="Оплата не подтверждена">' . self::STATUS_NOTPAY . '</span>';
        }
        if (!$this->file_act or !$this->file_invoice or !$this->file_pay) {
            return '<span class="tooltip-custom tooltip-black" data-toggle="tooltip" title="Заполняются документы акта">' . self::STATUS_NOTSENT . '</span>';
        }
        if (!$this->return_date) {
            return '<span class="tooltip-custom tooltip-purple" data-toggle="tooltip" title="Оригиналы акта еще не возвращены">' . self::STATUS_WITHOUT_ACT . '</span>';
        }
        if (!$this->file_act_client) {
            return '<span class="tooltip-custom tooltip-purple" data-toggle="tooltip" title="Клиент еще не прикрепил подписанный акт">' . self::STATUS_WITHOUT_ACT . '</span>';
        }
        return '<span class="tooltip-custom tooltip-red" data-toggle="tooltip" title="Требуется проверить данные для закрытия акта">' . self::STATUS_CHECK . '</span>';
    }

    /**
     * Путь к файлу
     * 
     * @param string $type_file наименование атрибута искомого файла
     * @return string|null
     */
    public function getFilePreview($type_file)
    {
        if ($this->$type_file) {
            return $this->$type_file->getUrlFile();
        }
        return null;
    }

    /**
     * Название файла
     * 
     * @param string $type_file наименование атрибута искомого файла
     * @return string|null
     */
    public function getFileName($type_file)
    {
        if ($this->$type_file) {
            return Html::encode($this->$type_file->filename);
        }
        return null;
    }

    /**
     * Иконка-заглушка для файла с актом
     * @return string
     */
    static function getStubFileAct()
    {
        return '<span class="tooltip-custom" data-toggle="tooltip" title="Акт не прикреплен"><i class="fa-solid fa-file fa-xl disabled" style="color: var(--color-disabled-button);"></i></span>';
    }

    /**
     * Возвращает кнопку с переходом на файл с актом
     * 
     * @param bool $with_stub выводить ли заглушку при отсутствии файла
     * @return null|string
     */
    public function getIconFileAct($with_stub = false)
    {
        if ($this->file_act) {
            return Html::a(
                '<i class="fa-solid fa-file fa-xl"></i>',
                $this->fileAct->getUrlFile(),
                [
                    'class' => 'custom-link-file tooltip-custom',
                    'data-toggle' => 'tooltip',
                    'target' => '_blank',
                    'data-pjax' => 0,
                    'title' => 'Открыть акт'
                ]
            );
        } else {
            if ($with_stub) {
                return self::getStubFileAct();
            }
        }
        return null;
    }

    /**
     * Иконка-заглушка для файла с подписанным актом
     * @return string
     */
    static function getStubFileActClient()
    {
        return '<span class="tooltip-custom" data-toggle="tooltip" title="Подписанный акт не прикреплен"><i class="fa-solid fa-file-contract fa-xl" style="color: var(--color-disabled-button);"></i></span>';
    }

    /**
     * Возвращает кнопку с переходом на форму с загрузкой файла (только для клиента)
     * 
     * @return null|string
     */
    public function getIconUploadFileClient()
    {
        $title = 'Прикрепить подписанный акт';
        $color = 'color:red';
        if ($this->file_act_client) {
            $title = 'Изменить подписанный акт';
            $color = 'color:mediumseagreen';
        }
        return Html::a(
            '<i class="fa-solid fa-file-contract fa-xl"></i>',
            ['/payment/upload-client-act', 'id' => $this->id],
            [
                'class' => 'custom-link-file tooltip-custom',
                'style' => $color,
                'data-toggle' => 'tooltip',
                'data-pjax' => 0,
                'title' => $title
            ]
        );
    }

    /**
     * Возвращает кнопку с переходом на файл подписанного акта
     * 
     * @param bool $with_stub выводить ли заглушку при отсутствии файла
     * @return null|string
     */
    public function getIconFileActClient($with_stub = false)
    {
        if ($with_stub) {
            if (
                $this->batch->contract->organization_id == Yii::$app->user->identity->organization_id
                and !$this->locked and $this->file_act
            ) {
                return $this->getIconUploadFileClient();
            }
            if (!$this->file_act_client) {
                return self::getStubFileActClient();
            }
        }
        if ($this->file_act_client) {
            return Html::a(
                '<i class="fa-solid fa-file-contract fa-xl"></i>',
                $this->fileActClient->getUrlFile(),
                [
                    'class' => 'custom-link-file tooltip-custom',
                    'data-toggle' => 'tooltip',
                    'target' => '_blank',
                    'data-pjax' => 0,
                    'title' => 'Открыть подписанный акт'
                ]
            );
        }
        return null;
    }

    /**
     * Иконка-заглушка для файла с счётом
     * @return string
     */
    static function getStubFilePay()
    {
        return '<span class="tooltip-custom" data-toggle="tooltip" title="Счёт не прикреплен"><i class="fa-solid fa-file-invoice-dollar fa-xl" style="color: var(--color-disabled-button);"></i></span>';
    }

    /**
     * Возвращает кнопку с переходом на файл с оплатой
     * 
     * @param bool $with_stub выводить ли заглушку при отсутствии файла
     * @return null|string
     */
    public function getIconFilePay($with_stub = false)
    {
        if ($this->file_pay) {
            return Html::a(
                '<i class="fa-solid fa-file-invoice-dollar fa-xl"></i>',
                $this->filePay->getUrlFile(),
                [
                    'class' => 'custom-link-file tooltip-custom',
                    'data-toggle' => 'tooltip',
                    'target' => '_blank',
                    'data-pjax' => 0,
                    'title' => 'Открыть счёт'
                ]
            );
        } else {
            if ($with_stub) {
                return self::getStubFilePay();
            }
        }
        return null;
    }

    /**
     * Иконка-заглушка для файла с счёт-фактурой
     * @return string
     */
    static function getStubFileInvoice()
    {
        return '<span class="tooltip-custom" data-toggle="tooltip" title="Счёт-фактура не прикреплена"><i class="fa-solid fa-file-invoice fa-xl" style="color: var(--color-disabled-button);"></i></span>';
    }

    /**
     * Возвращает кнопку с переходом на файл счёт-фактуры
     * 
     * @param bool $with_stub выводить ли заглушку при отсутствии файла
     * @return null|string
     */
    public function getIconFileInvoice($with_stub = false)
    {
        if ($this->file_invoice) {
            return Html::a(
                '<i class="fa-solid fa-file-invoice fa-xl"></i>',
                $this->fileInvoice->getUrlFile(),
                [
                    'class' => 'custom-link-file tooltip-custom',
                    'data-toggle' => 'tooltip',
                    'target' => '_blank',
                    'data-pjax' => 0,
                    'title' => 'Открыть счёт-фактуру'
                ]
            );
        } else {
            if ($with_stub) {
                return self::getStubFileInvoice();
            }
        }
        return null;
    }

    /**
     * Возвращает кнопки-иконки со всеми файлами, либо их заглушки
     * 
     * @param bool $wrapper оборачивать в html-теги, default = true 
     * @return string
     */
    public function getPaymentFiles($wrapper = true)
    {
        $content = $this->getIconFileAct(true)
            . $this->getIconFileActClient(true)
            . $this->getIconFilePay(true)
            . $this->getIconFileInvoice(true);

        if ($wrapper) {
            return "<div class=\"block-files-icons\">{$content}</div>";
        }
        return $content;
    }
}
