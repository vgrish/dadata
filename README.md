## dadata


[modx-DaData] - интеграция с сервисом <a href="https://dadata.ru/">DaData</a>.
DaData.ru <b>исправляет проблемные адреса</b>, ФИО и телефоны автоматически, по 5–10 копеек за запись.
А бесплатные Подсказки помогают клиентам за пару секунд <b>вводить правильные адреса, ФИО, email</b>, <b>реквизиты компаний</b> и банков.
в пакете реализованы методы:
- подсказок
- стандартизации
- геолокации
- актуальности справочников
- баланса
- поиск адреса по коду КЛАДР или ФИАС

пример работы посмотреть тут http://location.vgrish.ru/index.php?id=10

пример подключения подсказок для ввода ФИО (<a href="http://location.vgrish.ru/index.php?id=10">Форма 1</a>) 

```
[[!dadata.form?
        &apiToken=`15d97560c1ecb12b6728548def159eaf75adfac`
	&apiSecret=`15011a4b5c8448270ace9a999edca6241299ed1`
	&suggestions=`{
        'fullname': {
            'type': 'NAME',
            'params': {
                
            },
            'autoSelectFirst':1,
            'count':6,
            'subject': {
                'surname-name': 'SURNAME',
                'fullname-name': 'NAME',
                'fullname-patronymic': 'PATRONYMIC'
            }
        },
        'surname-name': {
            'type': 'NAME',
            'params': {
                'parts': ['SURNAME']
            },
            'master': {
                'fullname': 'SURNAME'
            }
        },
        'fullname-name': {
            'type': 'NAME',
            'params': {
                'parts': ['NAME']
            },
            'master': {
                'fullname': 'NAME'
            }
        },
        'fullname-patronymic': {
            'type': 'NAME',
            'params': {
                'parts': ['PATRONYMIC']
            },
            'master': {
                'fullname': 'PATRONYMIC'
            }
        }
	}`
]]
```

пример подключения подсказок (<a href="http://location.vgrish.ru/index.php?id=10">Форма 2</a>)

```
[[!dadata.form?
        &apiToken=`ed5d97560c1ecb12b6728548def159eaf75adfac`
	&apiSecret=`a65011a4b5c8448270ace9a999edca6241299ed1`
        &selector=`#dadata-form2`
	&suggestions=`{
        'email': {
            'type': 'EMAIL',
            'params': {}
        },
        'address': {
            'type': 'ADDRESS'
        },
        'party': {
            'type': 'PARTY'
        },
        'bank': {
            'type': 'BANK'
        }
	}`
]]
```

пример подключения подсказок (<a href="http://location.vgrish.ru/index.php?id=10">AjaxForm</a>)

```
[[!dadata.form?
        &apiToken=`ed5d97560c1ecb12b6728548def159eaf75adfac`
	&apiSecret=`a65011a4b5c8448270ace9a999edca6241299ed1`
        &selector=`.ajax_form.af_example`
	&suggestions=`{
        'name': {
            'type': 'NAME'
        },
        'email': {
            'type': 'EMAIL'
        }
	}`
]]
```
