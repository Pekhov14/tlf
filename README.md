# Перевод language files для opencart

##### tlf - translate language files

---

### Запуск скрипта

> php app.php path1/ru-ru ua-ua/ false

***(string) path1/ru-ru***

Путь к папке в которой переводим файлы

***(string) /ua-ua***

Путь к папке в которую складываем переведенные файлы.
Создаеться папка с этим именем рядом с ru-ru

***(bool) true | false***

true - Пауза будет + от 1 до 3 сек 
false или отсутстие параетра, пауза + от 20 до 60 сек

`Лучше передать false или ничего, больше шансов что google не забанит ip`

---
#### Examples

``pwd``

/home/user

``cd папка с языковыми файлами``

``pwd``

/home/user/Документы/test_translate/ru-ru

***Копируем путь***

``cd ~/tlf``

***Тут вставляем***

``php app.php /home/user/Документы/test_translate/ru-ru ua-ua/``

**Ждем индикатора со 100% завершением.**

**По итогу выведеться время выполнения скрипта.**
