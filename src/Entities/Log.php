<?php namespace Arcanedev\LogViewer\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

/**
 * Class     Log
 *
 * @package  Arcanedev\LogViewer\Entities
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Log implements Arrayable, Jsonable, JsonSerializable
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var string */
    public $date;

    /** @var string */
    private $path;

    /** @var LogEntryCollection */
    private $entries;

    /** @var string */
    private $raw;

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Constructor
     *
     * @param  string  $date
     * @param  string  $path
     * @param  string  $raw
     */
    public function __construct($date, $path, $raw)
    {
        $this->entries = new LogEntryCollection;
        $this->date    = $date;
        $this->path    = $path;
        $this->raw     = $raw;

        $this->entries->load($raw);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get log path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get raw log content
     *
     * @return string
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Make a log object
     *
     * @param  string  $date
     * @param  string  $path
     * @param  string  $raw
     *
     * @return self
     */
    public static function make($date, $path, $raw)
    {
        return new self($date, $path, $raw);
    }

    /**
     * Get log entries
     *
     * @param  string  $level
     *
     * @return LogEntryCollection
     */
    public function entries($level = 'all')
    {
        if ($level === 'all') {
            return $this->entries;
        }

        return $this->getByLevel($level);
    }

    /**
     * Get filtered log entries by level
     *
     * @param  string  $level
     *
     * @return LogEntryCollection
     */
    public function getByLevel($level)
    {
        return $this->entries->filterByLevel($level);
    }

    /**
     * Get log stats.
     *
     * @return array
     */
    public function stats()
    {
        return $this->entries->stats();
    }

    /**
     * Get the log navigation tree.
     *
     * @param  bool|false  $trans
     *
     * @return array
     */
    public function tree($trans = false)
    {
        return $this->entries->tree($trans);
    }

    /**
     * Get log entries menu.
     *
     * @param  bool|true  $trans
     *
     * @return array
     */
    public function menu($trans = true)
    {
        return $this->entries->menu($trans);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Convert Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get the log as a plain array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'date'    => $this->date,
            'path'    => $this->path,
            'entries' => $this->entries->toArray()
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Serialize the log object to json data
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
