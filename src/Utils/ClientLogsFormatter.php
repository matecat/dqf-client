<?php

namespace Matecat\Dqf\Utils;

use Monolog\Formatter\FormatterInterface;

class ClientLogsFormatter implements FormatterInterface
{
    /**
     * Formats a log record.
     *
     * @param array $record A record to format
     *
     * @return mixed The formatted record
     */
    public function format(array $record)
    {
        $record['message'] = str_replace(['"[', ']"'], ['[', ']'], $record['message']); // remove surrounding " from JSON arrays
        $record['message'] = str_replace(['"{', '}"'], ['{', '}'], $record['message']); // remove surrounding " from JSON objects
        $message = json_decode(str_replace("\r\n", " ", $record['message']), true);
        unset($record['message']);
        $record['message'] = $message;

        return json_encode($record) . PHP_EOL;
    }

    /**
     * Formats a set of log records.
     *
     * @param array $records A set of records to format
     *
     * @return mixed The formatted set of records
     */
    public function formatBatch(array $records)
    {
        foreach ($records as $key => $record) {
            $records[$key] = $this->format($record);
        }

        return $records;
    }
}
