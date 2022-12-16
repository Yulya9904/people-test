<?php

namespace Person\Services;

use Person\Entities\Person;

class PersonService
{
    private $people = [];
    const PATH_TO_FILE_WITH_PEOPLES = '././data/people.csv';
    const PATH_TO_TEXTS_FOLDER = 'data/texts';
    const PATH_TO_OUTPUT_TEXTS_FOLDER = 'data/output_texts';

    const SEPARATORS = [
        'comma' => ',',
        'semicolon' => ';',
    ];

    const ACTIONS = ['countAverageLineCount', 'replaceDates'];
    
    /**
     * @todo Добавить использование $separatorType. На данный момент не понятно по заданию для чего получаем.
     */
    public function __construct(string $separatorType) 
    {
        $separator = self::SEPARATORS['semicolon'];
        // fetch Peoples From Csv File
        if (($handle = fopen(self::PATH_TO_FILE_WITH_PEOPLES, "r")) !== FALSE) {
            while (($line = fgetcsv($handle, 1000, $separator)) !== FALSE) {
                $person = new Person();
                $person->setId((int) $line[0]);
                $person->setName($line[1]);
                $this->people[] = $person;
            }
            fclose($handle);
        }
    }

    /**
     * getPathsArrayByPersonId
     *
     * @param  int $personId
     * @return array
     */
    public function getPathsArrayByPersonId(int $personId): array
    {
        $pathsArray = glob(self::PATH_TO_TEXTS_FOLDER . '/' . $personId . "-00[1-9].txt");
        return $pathsArray ? $pathsArray : [];
    }

    /**
     * countAverageLineCount
     *
     * @return void
     */
    public function countAverageLineCount(): void
    {
        print 'Среднее количество строк в тексте в каждом файле' . PHP_EOL;
        foreach ($this->people as $person) {
            $lineQuantity = 0;
            $pathsArray = $this->getPathsArrayByPersonId($person->getId());
            foreach ($pathsArray as $path) {
                $lines = file($path);
                $lineQuantity = $lineQuantity + count($lines);
            }
            print $person->getName() . ' - ' . ($pathsArray ? round($lineQuantity / count($pathsArray)) : 0) . PHP_EOL;
        }
    }

    /**
     * replaceDates
     *
     * @return void
     */
    public function replaceDates(): void
    {
        print 'Количество замен формата даты для каждого сотрудника' . PHP_EOL;
        foreach ($this->people as $person) {
            $changesQuantity = 0;
            $pathsArray = $this->getPathsArrayByPersonId($person->getId());
            foreach ($pathsArray as $path) {
                $fileContent = file_get_contents($path); // получаем содержимое файла
                // произведем замену по регулярному выражению
                $pattern = "/(\d{2})\/(0\d|1[0,1,2])\/(\d{2})/";
                $replacement = "\$3-\$2-20\$1";
                $count = 0;
                $fileContent = preg_replace($pattern, $replacement, $fileContent, -1,  $count); 
                $changesQuantity = $changesQuantity + $count;
                file_put_contents(self::PATH_TO_OUTPUT_TEXTS_FOLDER . '/' . basename($path), $fileContent); // сохраним изменения в итоговом файле
            }
            print $person->getName() . ' - ' . ($changesQuantity) . PHP_EOL;
        }
    }
}
