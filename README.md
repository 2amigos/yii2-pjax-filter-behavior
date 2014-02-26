PjaxFilter Behavior for Yii2
=============================

PjaxFilter is an action filter that ensures an action has been called using pjax calls and to configure what to do
in case the controller's action has been called differently.

Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require "2amigos/yii2-pjax-filter-behavior" "*"
```
or add

```json
"2amigos/yii2-pjax-filter-behavior" : "*"
```

to the require section of your application's `composer.json` file.

Usage
-----

To use PjaxFilter, declare it in `behaviors()` method of your controller class. For example, the following
declarations will define full pjax filter for all controller's actions but the index action.

```
public function behaviors()
{
    return [
        'verbs' => [
            'class' => \dosamigos\pjaxfilter\PjaxFilter::className(),
            'actions' => [
                '*'  => ['url' => ['index']],
            ],
            'exclude' => ['index']
        ],
    ];
}
```
The next example, sets pjax filtering for controller's delete method and if not called via pjax, it will fire an
error. The rest of the actions will not be affected.

```
public function behaviors()
{
    return [
        'verbs' => [
            'class' => \dosamigos\pjaxfilter\PjaxFilter::className(),
            'actions' => [
                'error' => ['code' => 404, 'msg' => 'Not found']
            ]
        ],
    ];
}
```

> [![2amigOS!](http://www.gravatar.com/avatar/55363394d72945ff7ed312556ec041e0.png)](http://www.2amigos.us)

<i>Web development has never been so fun!</i>
[www.2amigos.us](http://www.2amigos.us)