<?php

namespace EugMerkeleon\Support\AutoDoc\DataCollectors;

use EugMerkeleon\Support\AutoDoc\Exceptions\MissedProductionFilePathException;
use EugMerkeleon\Support\AutoDoc\Interfaces\DataCollectorInterface;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class LocalDataCollector implements DataCollectorInterface
{
    protected static $data;

    public $prodFilePath;

    public function __construct()
    {
        $this->prodFilePath = config('auto-doc.production_path');
        if (empty($this->prodFilePath))
        {
            throw new MissedProductionFilePathException();
        }
        if (!file_exists($this->prodFilePath))
        {
            file_put_contents($this->prodFilePath, '');
        }
    }

    public function saveTmpData($tempData)
    {
        self::$data = $tempData;
    }

    public function getTmpData()
    {
        return self::$data;
    }

    public function saveData()
    {
        $content = json_encode(self::$data);
        file_put_contents($this->prodFilePath, $content);
        self::$data = [];
    }

    public function getDocumentation()
    {
        if (!file_exists($this->prodFilePath))
        {
            throw new FileNotFoundException();
        }
        $fileContent = file_get_contents($this->prodFilePath);

        return json_decode($fileContent);
    }
}
