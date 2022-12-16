<?php

namespace User\Services;

use User\Entities\User;

class UserService
{
    private $people = [];
    const PATH_TO_FILE_WITH_PEOPLE = '././data/people.csv';
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
        if (($handle = fopen(self::PATH_TO_FILE_WITH_PEOPLE, "r")) !== FALSE) {
            while (($line = fgetcsv($handle, 1000, $separator)) !== FALSE) {
                $user = new User();
                $user->setId((int) $line[0]);
                $user->setName($line[1]);
                $this->people[] = $user;
            }
            fclose($handle);
        }
    }

    /**
     * getPathsArrayByUserId
     *
     * @param  int $userId
     * @return array
     */
    public function getPathsArrayByUserId(int $userId): array
    {
        $pathsArray = glob(self::PATH_TO_TEXTS_FOLDER . '/' . $userId . "-00[1-9].txt");
        return $pathsArray ? $pathsArray : [];
    }

    /**
     * countAverageLineCount
     *
     * @return void
     */
    public function countAverageLineCount(): void
    {
        print 'Среднее количество строк в файлах пользователя' . PHP_EOL;
        foreach ($this->people as $user) {
            $lineQuantity = 0;
            $pathsArray = $this->getPathsArrayByUserId($user->getId());
            foreach ($pathsArray as $path) {
                $lines = file($path);
                $lineQuantity = $lineQuantity + count($lines);
            }
            print $user->getName() . ' - ' . ($pathsArray ? round($lineQuantity / count($pathsArray)) : 0) . PHP_EOL;
        }
    }

    /**
     * replaceDates
     *
     * @return void
     */
    public function replaceDates(): void
    {
        print 'Количество замен формата даты для каждого пользователя' . PHP_EOL;
        foreach ($this->people as $user) {
            $changesQuantity = 0;
            $pathsArray = $this->getPathsArrayByUserId($user->getId());
            foreach ($pathsArray as $path) {
                $fileContent = file_get_contents($path); // получаем содержимое файла
                // произведем замену по регулярному выражению
                $pattern = "/(\d{2})\/(0\d|1[0,1,2])\/(\d{2})/";
                $replacement = "\$1-\$2-20\$3";
                $count = 0;
                $fileContent = preg_replace($pattern, $replacement, $fileContent, -1,  $count); 
                $changesQuantity = $changesQuantity + $count;
                file_put_contents(self::PATH_TO_OUTPUT_TEXTS_FOLDER . '/' . basename($path), $fileContent); // сохраним изменения в итоговом файле
            }
            print $user->getName() . ' - ' . ($changesQuantity) . PHP_EOL;
        }
    }
}
