## dadata


[DaData] - интеграция с сервисом <a href="https://dadata.ru/">DaData</a>.
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
    &selector=`#dadata-form2`
	&suggestions=`{
        'email': {
            'type': 'EMAIL'
        },
        'address': {
            'type': 'ADDRESS'
        },
        'party': {
            'type': 'PARTY',
            'restrict_value': 'true'
        },
        'inn': {
            'type': 'PARTY',
            'restrict_value': 'true',
            'params': {
                'return': {
                    'keys': ['data.inn']
                }
            }
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

пример для адреса (<a href="http://location.vgrish.ru/index.php?id=10">Форма 4</a>)
```
[[!dadata.form?
    &selector=`#dadata-form4`
	&suggestions=`{
        'address-input': {
            'type': 'ADDRESS',
            'params': {
                
            },
            'subject': {
                'address-postalcode': 'postal_code',
                'address-region': 'region',
                'address-city': 'city',
                'address-street': 'street_with_type',
                'address-house': 'house',
                'address-flat': 'flat'
            }
        },
        'address-postalcode': {
            'type': 'ADDRESS',
            'bounds': 'postal-code',
            'params': {
                'return': {
                    'keys': ['data.postal_code']
                }
            },
            'master': {
                'address-input': 'postal_code'
            }
        },
        'address-region': {
            'type': 'ADDRESS',
            'bounds': 'region-area',
            'params': {
                'return': {
                    'keys': ['data.region_with_type']
                }
            },
            'master': {
                'address-input': 'region_with_type'
            }
        },
        'address-city': {
            'type': 'ADDRESS',
            'bounds': 'city-settlement',
            'params': {
                'return': {
                    'keys': ['data.city']
                }
            },
            'master': {
                'address-input': 'city'
            }
        },
        'address-street': {
            'type': 'ADDRESS',
            'bounds': 'street',
            'params': {
                'return': {
                    'keys': ['data.street']
                }
            },
            'master': {
                'address-input': 'street'
            }
        },
        'address-house': {
            'type': 'ADDRESS',
            'bounds': 'house',
            'params': {
                'return': {
                    'keys': ['data.house']
                }
            },
            'master': {
                'address-input': 'house'
            }
        },
        'address-flat': {
            'type': 'ADDRESS',
            'params': {
                'return': {
                    'keys': ['data.flat']
                }
            },
            'master': {
                'address-input': 'flat'
            }
        }
	}`
]]
```
