<?php
/**
 * test case
 */
class WURFL_TestUtils
{
    /**
     * Load Test File containing user-agent -> deviceids associations
     *
     * @param string $fileName
     * @return array
     */
    public static function loadUserAgentsWithIdFromFile($filePath)
    {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("File path $filePath does not exist!!!");
        }

        $testData = [];
        $file_handle = fopen($filePath, "r");

        while (! feof($file_handle)) {
            $line = fgets($file_handle);
            self::updateTestData($testData, $line);
        }
        fclose($file_handle);

        return $testData;
    }


    public static function loadUserAgentsAsArray($filePath)
    {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("File path $filePath does not exist!!!");
        }

        $testData = [];
        $file_handle = fopen($filePath, "r");

        while (! feof($file_handle)) {
            $line = fgets($file_handle);
            $isTestData = ((strpos($line, "#") === false) && strcmp($line, "\n") != 0);
            if ($isTestData) {
                $userAgentArray = [];
                $userAgentArray[] = $line;
                $testData[] = $userAgentArray;
            }
        }
        fclose($file_handle);

        return $testData;
    }



    public static function loadTestData($fileName)
    {
        $testData = [];
        $file_handle = fopen($fileName, "r");
        while (!feof($file_handle)) {
            $line = fgets($file_handle);
            if (strpos($line, "#") === false && strcmp($line, "\n") != 0) {
                $testData[] = explode("=", trim($line));
            }
        }
        fclose($file_handle);

        return $testData;
    }


    private static function updateTestData(&$testData, $line)
    {
        $isTestData = ((strpos($line, "#") === false) && strcmp($line, "\n") != 0);
        if ($isTestData) {
            $testData[] = explode("=", trim($line));
        }
    }
}
