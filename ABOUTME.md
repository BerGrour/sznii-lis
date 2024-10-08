Роли доступа пользователей
================
Распределение ролей для пользователей осуществляется (по привязке к отделу), (либо клиент). То есть если пользователь состоит в отделе лаборатории, то роль будет "laboratory".

registration - Приём проб; ввод данных об организациях; оформление договоров; отслеживание проб
------------
* Заводит в систему новые организации и оформляет договора с ними.
* Регистрирует партии проб
* Имеет доступ к партиям и пробам
* Так же имеет доступ к актам (без удаления и подверждения)

laboratory - Исследование проб в рамках своей лаборатории; загрузка результатов исследований
------------
* Полный доступ к исследованиям в рамках своей лаборатории
* Редактирование каталога
* Возможность отметить пробу как "потеряна"
* Единственная роль, способная регистрировать исследования

booker - Выставление счетов и актов; контроль оплаты и отчетности; корректировка стоимости исследований
------------
* Полный доступ к актам
* Возможность подтверждать(закреплять) акт
* Редактирование каталога

admin - Администрирование пользователями и системой
------------
* Полный доступ к системе
* Создание отделов и должностей
* Заводить новых пользователей/сотрудников
* Доступ к админке (инфа по нераспределенным пробам, не зарегистрированным актам, просроченным исследованиям + панель быстрого доступа)
* Возможность редактировать производственный календарь

client - Доступ к информации по пробам и исследованиям в рамках своей организации; прикрепление пописанных документов
------------
* Отдельный интерфейс, где на главной странице инфо по соответствующей орге и их исследованиям
* Возможность видеть каталог
* Возможность прикрепить(загрузить) "подписанный акт" к исследованию


Небольшая сводка по бизнес-процессу проекта
================
Регистрирует пользователей администратор, для этого ему нужно указать login password и email.

1. При приходе партии проб, регистратура производит их регистрацию, распределяя пробы по лабораториям, указав их количество.

2. У пользователей лабораторий, для которых пришли новые пробы, появляется уведомление с кнопкой регистрации исследования. Для регистрации выбрать соответствующее исследование из выпадающего списка каталога и ввести количество проб. (так же есть аналоги регистрации исследований: 1. Создание пустого исследования без проб, но указать партию, в дальнейшем можно добавить пробы;    2. Регистрация сразу нескольких исследований из страницы партии, выбрав несколько проб, можно провести регистрацию нескольких исследований, выбрав их далее в Select с опцией multiply)

3. На пришедшую партию требуется регистрировать все необходимые исследования ДО того как будет регистрироваться акт.

4. При загрузке результатов, исследование считается закрытым

5. При завершении всех исследований партии проб и использовании всех проб, у пользователей с ролью booker появится уведомление с возомжностью зарегистрировать акт.

6. У акта есть определенное количество различных файлов и дат, они могут заполняться со временем. Но для полного завершения акта, требуется указать их все.

7. В акте есть файл `file_act_client`, который зависит от клиента, если он не прикреплен, то стоит подпнуть клиента на его загрузку.

8. При полном заполнении акта, он все еще на закрыт. Требуется бухгалтеру (роль booker) его ПРОВЕРИТЬ и закрепить(отдельная кнопочка). При закреплении акта, все исследования и пробы входящие в акт, закрываются для редактирования.

9. У клиента с момента регистрации исследования, исследование появляется в списке с динамическим статусом, который отображает состояние исследования. Когда по партии регистрируется акт и прикрепляется соответствующий файл, появляется возможность прикрепить свою версию акта(подписанный), но как только акт будет закреплен, эта возможность пропадает.

Нюансы
================
* Регистрация исследований доступна исключительно для лаборатории (даже админ не может), чтобы не нарушать целостность данных.

* Включать пробы с исследования можно только в рамках определенной партии, то есть в одном исследовании не может быть проб из разных партий.

* Роли определяются по отделам, в которые входит сотрудник. Для клиента отдельно назначается роль.

* Потеря пробы - Если проба учавствует в двух исследованиях и была потеряна после первого (в первом успела поучавствовать, а во втором нет). То требуется сначала закрыть первое исследование, затем пометить пробу как "Потеряна", только потом можно закрывать второе исследование.

* В проекте есть "Производственный календарь", загружается он по API, с помощью cron-задачи(1 декабря каждого года), при желании можно загрузить вручную с помощью команды `yii calendar 2024`, где 2024 - требуемый год.

* В случае каких-либо внутренних изменений в рабочих днях, или произошла ошибка со стороны API, то есть возможность вручную назначать выходные дни в календаре. Такой функционал доступен роли admin.