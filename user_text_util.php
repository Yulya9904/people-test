<?php
// выполним загрузку классов 
spl_autoload_register(function ($class) {
	$class = str_replace('\\', '/', $class);
	$path = __DIR__."/" . $class . ".php";
	if (file_exists($path)) require_once $path;
}, true, true);

use Person\Services\PersonService;


$args = $_SERVER['argv'];
$separatorType = $args[1] ?? null;
$action        = $args[2] ?? null;

if(!in_array($separatorType, array_keys(PersonService::SEPARATORS)) || !in_array($action, PersonService::ACTIONS)){
	print 'Для корректной работы утилиты необходимо передать два параметра: разделитель (comma, semilocon) и действие, которое требуется выполнить (countAverageLineCount, replaceDates)';
}
$personService = new PersonService($separatorType);
switch ($action) {
    case 'countAverageLineCount':
        $personService->countAverageLineCount();
        break;
    case 'replaceDates':
        $personService->replaceDates();
        break;
}


