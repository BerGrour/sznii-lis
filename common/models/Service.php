<?php

namespace common\models;

use common\extensions\traits\BasicMethodsTrait;
use DateTime;
use Yii;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "service".
 *
 * @property int $id
 * @property int $batch_id
 * @property string $research
 * @property float $price
 * @property string $started_at
 * @property int $staff_id
 * @property string $predict_date
 * @property float $pre_sum
 * @property string|null $completed_at
 * @property int|null $file_id
 * @property bool $locked
 *
 * @property File $file
 * @property SampleService[] $sampleServices
 * @property Staff $staff
 * @property Batch $batch
 */
class Service extends \yii\db\ActiveRecord
{
    use BasicMethodsTrait;

    const SCENARIO_CREATE_EMPTY = 'create_empty'; // сценарий для формы с созданием пустого исследования
    const SCENARIO_CREATE_IDLE = 'create_idle'; // сценарий для формы с созданием заполненного пробами исследования
    const STATUS_LATE = 'Опоздание';
    const STATUS_EMPTY = 'Пусто';
    const STATUS_NOTPAY = 'Не оплачено';
    const STATUS_PROCESS = 'В процессе';
    const STATUS_WAITING = 'Проверяется';
    const STATUS_COMPLETE = 'Завершено';
    const STATUS_CLIENT_RESEARCHING = 'Исследуется';
    const STATUS_CLIENT_ACTING = 'Оформляется';
    const STATUS_CLIENT_NOTPAY = 'Не оплачено';
    const STATUS_CLIENT_WAITING = 'В ожидании';
    const STATUS_CLIENT_COMPLETED = 'Завершено';
    const STATUS_CLIENT_CHECKED = 'На проверке';

    public $uploadedFile;   // атрибут для загрузки файла
    public $research_id;    // атрибут для определения вида исследования
    public $amount;         // количество проб
    public $sample_ids;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['research', 'price', 'started_at', 'staff_id', 'batch_id'], 'required'],
            [['completed_at'], 'default', 'value' => NULL],
            [['price'], 'number'],
            [['started_at', 'predict_date', 'completed_at'], 'safe'],
            [['staff_id', 'file_id', 'locked'], 'integer'],
            [['research'], 'string', 'max' => 255],

            [['batch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Batch::class, 'targetAttribute' => ['batch_id' => 'id']],
            [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::class, 'targetAttribute' => ['file_id' => 'id']],
            [['staff_id'], 'exist', 'skipOnError' => true, 'targetClass' => Staff::class, 'targetAttribute' => ['staff_id' => 'id']],

            [['research_id', 'amount'], 'required', 'on' => self::SCENARIO_CREATE_IDLE],
            [['research_id', 'batch_id'], 'required', 'on' => self::SCENARIO_CREATE_EMPTY],

            [['sample_ids'], 'validateSampleBatchConsistency']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'batch_id' => 'Партия проб',
            'research' => 'Вид исследования',
            'price' => 'Цена',
            'started_at' => 'Дата начала',
            'staff_id' => 'Сотрудник',
            'predict_date' => 'Предварительная дата окончания',
            'pre_sum' => 'Предварительная стоимость',
            'completed_at' => 'Дата окончания',
            'file_id' => 'Файл с результатами',
            'locked' => 'Завершено',

            'uploadedFile' => 'Файл: ',
            'sample_type' => 'Вид проб',
            'laboratory' => 'Лаборатория',
            'status' => 'Статус',
            'statusClient' => 'Статус',
            'staff_name' => 'Сотрудник',
            'research_id' => 'Вид исследования',
            'amount' => 'Количество проб',
            'sum' => 'Стоимость',
            'batch_date' => 'Партия проб',
            'organization' => 'Организация'
        ];
    }

    /**
     * Gets query for [[Batch]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBatch()
    {
        return $this->hasOne(Batch::class, ['id' => 'batch_id']);
    }

    /**
     * Gets query for [[File]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFile()
    {
        return $this->hasOne(File::class, ['id' => 'file_id']);
    }

    /**
     * Gets query for [[SampleServices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSampleServices()
    {
        return $this->hasMany(SampleService::class, ['service_id' => 'id']);
    }

    /**
     * Gets query for [[Samples]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActiveSamples()
    {
        if ($this->locked) {
            return $this->hasMany(Sample::class, ['id' => 'sample_id'])
                ->where(['losted_at' => NULL])
                ->orWhere(['>', 'losted_at', $this->completed_at])
                ->via('sampleServices');
        } else {
            return $this->hasMany(Sample::class, ['id' => 'sample_id'])
                ->where(['losted_at' => NULL])
                ->via('sampleServices');
        }
    }

    /**
     * Gets query for [[Staff]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStaff()
    {
        return $this->hasOne(Staff::class, ['id' => 'staff_id']);
    }

    /**
     * Получение заголовка
     * @param int $len ограничение длины
     * @return string
     */
    public function getShortTitle($len = 50)
    {
        $result = StringHelper::truncate(Html::encode($this->research), $len);
        return $result;
    }

    /**
     * Получение статуса исследования и его цветовая визуализация
     * 
     * @param bool $for_client скрытие определенных статусов для клиента, default = false
     * 
     * @return string
     */
    public function getStatus($for_client = false)
    {
        if (!$this->sampleServices && !$for_client) {
            return '<span style="color:red">' . self::STATUS_EMPTY . '</span>';
        }
        if (!$this->locked) {
            if (empty($this->completed_at)) {
                if ($this->predict_date < date('Y-m-d') && !$for_client) {
                    return '<span style="color:red">' . self::STATUS_LATE . '</span>';
                }
                return '<span style="color:black">' . self::STATUS_PROCESS . '</span>';
            } else {
                return '<span style="color:gray">' . self::STATUS_WAITING . '</span>';
            }
        }
        if ((!$this->batch->payment or !$this->batch->payment->pay_date) && !$for_client) {
            return '<span style="color:purple">' . self::STATUS_NOTPAY . '</span>';
        }
        return '<span style="color:green">' . self::STATUS_COMPLETE . '</span>';
    }

    /**
     * Получение статуса исследования и его цветовая визуализация
     * В рамках клиента
     * 
     * @return string
     */
    public function getStatusClientValue()
    {
        if (!empty($this->batch->getServiceInProcess() or $this->batch->getIdleSamplesCount(true)) != 0) {
            return '<span class="tooltip-custom tooltip-black" data-toggle="tooltip" title="Лаборатория еще проводит исследования">' . self::STATUS_CLIENT_RESEARCHING . '</span>';
        }
        if ($payment = $this->batch->payment) {
            if ($payment->locked) {
                return '<span class="tooltip-custom tooltip-green" data-toggle="tooltip" title="Завершено">' . self::STATUS_CLIENT_COMPLETED . '</span>';
            }
            if ($payment->act_date and  $payment->file_act and $payment->file_invoice and $payment->file_pay) {
                if (!$payment->pay_date) {
                    return '<span class="tooltip-custom tooltip-purple" data-toggle="tooltip" title="Оплата не подтверждена">' . self::STATUS_CLIENT_NOTPAY . '</span>';
                }
                if (!$payment->return_date) {
                    return '<span class="tooltip-custom tooltip-purple" data-toggle="tooltip" title="Оригиналы акта еще не возвращены">' . self::STATUS_CLIENT_WAITING . '</span>';
                }
                if (!$payment->file_act_client) {
                    return '<span class="tooltip-custom tooltip-purple" data-toggle="tooltip" title="Требуется прикрепить подписанный акт">' . self::STATUS_CLIENT_WAITING . '</span>';
                } else {
                    return '<span class="tooltip-custom tooltip-gray" data-toggle="tooltip" title="Бухгалтерия проверяет данные">' . self::STATUS_CLIENT_CHECKED . '</span>';
                }
            }
        }
        return '<span class="tooltip-custom tooltip-black" data-toggle="tooltip" title="Бухгалтерия оформляет акт">' . self::STATUS_CLIENT_ACTING . '</span>';
    }

    /**
     * Путь к файлу
     * 
     * @return string|null
     */
    public function getFilePreview()
    {
        if ($this->file) {
            return $this->file->getUrlFile();
        }
        return null;
    }

    /**
     * Название файла
     * 
     * @return string|null
     */
    public function getFileName()
    {
        if ($this->file) {
            return Html::encode($this->file->filename);
        }
        return null;
    }

    /**
     * Возвращает кнопку с переходом на файл результатов
     * 
     * @param bool $with_stub выводить ли заглушку при отсутствии файла
     * @return null|string
     */
    public function getIconFileResults($with_stub = false)
    {
        if ($this->file) {
            return Html::a(
                '<i class="fa-regular fa-file fa-xl"></i>',
                $this->file->getUrlFile(),
                [
                    'class' => 'custom-link-file tooltip-custom',
                    'data-toggle' => 'tooltip',
                    'target' => '_blank',
                    'data-pjax' => 0,
                    'title' => 'Открыть результаты исследования'
                ]
            );
        }
        if ($with_stub) {
            return '<span class="tooltip-custom" data-toggle="tooltip" title="Результаты не прикреплены"><i class="fa-regular fa-file fa-xl" style="color: var(--color-disabled-button);"></i></span>';
        }
    }

    /**
     * Метод для получения всех файлов исследования и оплаты
     * 
     * @param bool $with_service Включать ли файл с результатом исследований, default = true
     * @param bool $with_act Включать ли файлы с акта, default = true
     * @return string
     */
    public function getAllFiles($with_service = true, $with_act = true)
    {
        $content = '';
        if ($with_service) {
            $content = $this->getIconFileResults(true);
        }
        if ($with_act) {
            if ($payment = $this->batch->payment) {
                $content .= $payment->getPaymentFiles(false);
            } else {
                $content .= Payment::getStubFileAct()
                    . Payment::getStubFileActClient()
                    . Payment::getStubFilePay()
                    . Payment::getStubFileInvoice();
            }
        }
        return "<div class=\"block-files-icons\">{$content}</div>";
    }

    /**
     * Выводит информацию о количестве используемых проб
     * Пример: (5 проб)
     * 
     * @return string
     */
    public function getAmountSamplesInfo()
    {
        $amount = $this->getActiveSamples()->count();
        if ($amount == 1) {
            return "<span class=\"another-info-span\">($amount проба)</span>";
        }
        if ($amount < 5) {
            return "<span class=\"another-info-span\">($amount пробы)</span>";
        }
        return "<span class=\"another-info-span\">($amount проб)</span>";
    }

    /**
     * Метод возвращающий стоимость
     * @return float|string|null
     */
    public function getSum()
    {
        if ($this->locked) {
            return number_format($this->pre_sum, 2, '.', ' ');
        }
        return '<span class="another-info-span">&asymp;' . number_format(
            $this->pre_sum,
            2,
            '.',
            ' '
        ) . '</span>';
    }

    /**
     * Метод возвращающий подробную информацию об количестве проб общих и активных
     * @return string
     */
    public function getAmountDetailed()
    {
        $active_amount = $this->getActiveSamples()->count();
        $total_amount = $this->getSampleServices()->count();
        return "Всего - {$total_amount}, из них&nbsp;<strong>{$active_amount} активны</strong>";
    }

    // /**
    //  * Метод возвращающий кликабельный заголовок, направляющий на детальную страницу услуги для клиента
    //  * 
    //  * @param string $content наименование для ссылки
    //  * @param string $target атрибут target тега <a>, дефолт = "_self"
    //  * @param string $title атрибут title html-тега
    //  * @return string hyperlink
    //  */
    // public function getLinkOnClientView($content, $target = '_self', $title = null)
    // {
    //     if (!$title) $title = $content;
    //     $result = Html::a(
    //         $content,
    //         ['client/view', 'service_id' => $this->id],
    //         [
    //             'class' => 'text-link',
    //             'target' => $target,
    //             'title' => $title,
    //             'data-pjax' => '0'
    //         ]
    //     );
    //     return $result;
    // }

    /**
     * Значение заголовка для строки с общей инфо по партии проб
     * 
     * @example client/index GridView
     * @return string
     */
    public function getSummaryTitle()
    {
        $content = "В сумме";
        if ($this->batch->payment) {
            $content .= " ({$this->batch->payment->getShortTitle()})";
        }
        return $content;
    }

    /**
     * Значение статуса для строки с общей инфо по партии проб
     * 
     * @example client/index GridView
     * @return string
     */
    public function getSummaryStatus()
    {
        return $this->getStatusClientValue();
    }

    /**
     * Значение количества для строки с общей инфо по партии проб
     * 
     * @example client/index GridView
     * @return string
     */
    public function getSummaryAmount()
    {
        $services = $this->batch->services;
        $amount = 0;
        foreach ($services as $service) {
            $amount += $service->getActiveSamples()->count();
        }
        if ($this->batch->payment) {
            return $amount;
        }
        return "<span class=\"another-info-span\">&asymp;{$amount}</span>";
    }

    /**
     * Значение суммы для строки с общей инфо по партии проб 
     * 
     * @example client/index GridView
     * @return string
     */
    public function getSummaryPrice()
    {
        if ($this->batch->payment) {
            return number_format($this->batch->payment->fact_sum, 2, '.', ' ');
        }
        $services = $this->batch->services;
        $pre_sum = 0;
        foreach ($services as $service) {
            $pre_sum += $service->pre_sum;
        }

        return '<span class="another-info-span">&asymp;' . number_format(
            $pre_sum,
            2,
            '.',
            ' '
        ) . '</span>';
    }

    /**
     * Файлы акта для строки с общей инфо по партии проб
     * 
     * @example client/index GridView
     * @return string
     */
    public function getSummaryFiles()
    {
        return $this->getAllFiles(false, true);
    }

    /**
     * Массив с выходными днями для определенного года
     * @param CalendarYear $year_model модель соответствующего года
     * @return array
     */
    static function arrayOfHolidays($year_model)
    {
        $holidayDates = [];
        if (!empty($year_model)) {
            foreach ($year_model->dates as $holiday) {
                $holidayDates[] = $holiday->date;
            }
        }
        return $holidayDates;
    }

    /**
     * Подсчет предварительной даты завершения исследования
     * @param int $period
     * @return string
     */
    public function predictTheDate($period)
    {
        $start = $this->started_at;
        $year_model = CalendarYear::find()->where(['number' => date('Y')])->one();

        if (!empty($year_model)) {
            $holidayDates = self::arrayOfHolidays($year_model);

            $newDate = new DateTime($start);
            for ($i = 1; $i < $period; $i++) {
                $newDate->modify("+1 day");

                if ($newDate->format('d.m') == '01.01') {
                    $year_model = CalendarYear::find()->where(['number' => date('Y') + 1])->one();
                    $holidayDates = self::arrayOfHolidays($year_model);
                }

                if (in_array($newDate->format('d.m'), $holidayDates)) {
                    $i--;
                }
            }
            return $newDate->format('Y-m-d');
        } else {
            Yii::$app->session->setFlash('error', 'В системе нет календаря нужного года, расчет предварительной даты завершения исследования невозможен!');
            return null;
        }
    }

    
    /**
     * Проверка валидности выбранных проб на соответствие партии и лаборатории
     * @param mixed $attribute
     * @return void
     */
    public function validateSampleBatchConsistency($attribute)
    {
        if (is_array($this->sample_ids) && !empty($this->sample_ids)) {
            $samples = Sample::find()
                ->select(['batch_id', 'departament_id'])
                ->where(['id' => $this->sample_ids])
                ->asArray()
                ->all();
    
            if (count($samples) !== count($this->sample_ids)) {
                Yii::$app->session->setFlash('error', 'Некоторые пробы не найдены.');
                $this->addError($attribute, 'Некоторые пробы не найдены.');
                return;
            }
    
            $batchIds = array_column($samples, 'batch_id');
            $departamentIds = array_column($samples, 'departament_id');
    
            if (count(array_unique($batchIds)) > 1) {
                Yii::$app->session->setFlash('error', 'Все пробы должны принадлежать одной и той же партии проб.');
                $this->addError($attribute, 'Все пробы должны принадлежать одной и той же партии проб.');
            }
    
            if (!in_array($this->batch_id, $batchIds)) {
                Yii::$app->session->setFlash('error', 'В исследовании используется другая партия проб.');
                $this->addError($attribute, 'В исследовании используется другая партия проб.');
            }
    
            if (count(array_unique($departamentIds)) > 1 && 
                $departamentIds[0] != Yii::$app->user->identity->staff->job->departament_id) {
                Yii::$app->session->setFlash('error', 'Вы не имеете доступ к добавлению проб других лабораторий.');
                $this->addError($attribute, 'Вы не имеете доступ к добавлению проб других лабораторий.');
            }
        }
    }
}
