form-selectize
==============

Form extension for Nette framework

## More documentations

* [javascript settings] (https://github.com/Olicek/form-selectize/blob/master/docs/en/javascript.md)
* [ajax] (https://github.com/Olicek/form-selectize/blob/master/docs/en/ajax.md)

## Requirements

* Nette 3.0
* PHP 7.2+
* jQuery 1.8+
* Selectize.js - included with more features

## Installation

The best way to install evosoftcz/form-selectize is using  [Composer](http://getcomposer.org/):

```sh
    "repositories": [
		{
			"url": "https://github.com/evosoftcz/form-selectize.git",
			"type": "git"
		}
	],
    "require": {
		"evosoftcz/form-selectize": "^2.0"
	},
```

After installation server site, you have to install client site. 

Link `selectize.init.js` from **client-side** and `selectize.js` and call somewhere function `selectize()`.

Last step is enable the extension using your neon config

```
extensions:
	selectize: Selectize\Form\Control\SelectizeExtension
```

## Default configuration

```
selectize:
	mode: full # second mode is `select` for selection just 1 option
	create: on
	maxItems: null
	delimiter: #/
	plugins:
		- remove_button
	valueField: id
	labelField: name
	searchField: name
```


## Data in array for full mode

```
array (2)
	0 => array (2)
		id => 1
		name => "First item"
	1 => array (2)
		id => 2
		name => "Second item"
```
id is set as valueField and name as labelField and searchField in config.neon. You can use whatever, have to just set in config.neon or in addSelectize method. For example: 
```
valueField: slug
```

## Data in array for select mode

Data for select mode are same as for SelectBox:

```
array (2)
	1 => "First item",
	2 => "Second item"
```

## Using

#### Default settings from config.neon

```php
$form->addSelectize('tags', 'štítky', $arrayData);
```

#### custome settings in method first way (array)
```php
$form->addSelectize('tags', 'štítky', $arrayData, ['create' => false, 'maxItems' => 3]);
```

#### custome settings in method first way (method)

```php
$form->addSelectize('tags', 'štítky', $arrayData)->setValueField('slug')->delimiter('_');
```

## Output is:

#### Select mode

```
dump($form->values->tags); // return "1"
```

#### Full mode
In full mode it will return array with valueField values. If you create some new tag, it will in sub array with plain text.

```
array (3)
	0 => "1"
	1 => "2"
	new => array (1)
		0 => "Third item"
```


#### Disabled items
In full mode it support disabled items at droplist

```
array (2)
    0 => array (3)
        'id' => 1
        'name' => "foo"
        'enabled' => true 
    1 => array (3)
        'id' => 2
        'name' => "bar"
        'enabled' => false 
```
