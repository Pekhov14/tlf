# Перевод language files для opencart

##### tlf - translate language files

---

### Запуск скрипта

> php app.php path1/ru-ru ua-ua/

***(string) path1/ru-ru***

Путь к папке в которой переводим файлы

***(string) /ua-ua***

Путь к папке в которую складываем переведенные файлы.
Создаеться папка с этим именем рядом с ru-ru


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

**Ждем индекатора со 100% завершением.**

**По итогу выведеться время выполнения скрипта.**
