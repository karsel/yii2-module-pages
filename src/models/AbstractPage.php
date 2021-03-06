<?php
/**
 * Yii2 Pages Module.
 *
 * @link      https://github.com/hiqdev/yii2-module-pages
 * @package   yii2-module-pages
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2016-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\yii2\modules\pages\models;

use Symfony\Component\Yaml\Yaml;
use Yii;

abstract class AbstractPage extends \yii\base\Object
{
    public $layout;

    public $title;

    protected $path;

    protected $text;

    protected $data = [];

    public function setData($data)
    {
        if (!is_array($data)) {
            return;
        }
        $this->data = $data;
        foreach (['title', 'layout'] as $key) {
            if (isset($data[$key])) {
                $this->{$key} = $data[$key];
            }
        }
    }

    public function getData()
    {
        return $this->data;
    }

    public function __construct($path)
    {
        list($data, $text) = $this->extractData($path);

        $this->path = $path;
        $this->text = $text;
        $this->setData($data);
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getDate()
    {
        return $this->data['date'];
    }

    public static function getModule()
    {
        /// XXX think
        return Yii::$app->getModule('pages');
    }

    public static function createFromFile($path)
    {
        $extension = pathinfo($path)['extension'];
        $class = static::getModule()->findPageClass($extension);

        return new $class($path);
    }

    public function extractData($path)
    {
        $lines = static::getModule()->readArray($path);
        $yaml = $this->readQuotedLines($lines, '/^---$/', '/^---$/');
        if (empty($yaml)) {
            $data = [];
            $text = $lines;
        } else {
            $data = $this->readYaml($yaml);
            $text = array_slice($lines, count($yaml));
        }

        return [$data, implode("\n", $text)];
    }

    public function readFrontMatter($lines)
    {
        $yaml = $this->readQuotedLines($lines, '/^---$/', '/^---$/');
        if (empty($yaml)) {
            return [];
        }

        return empty($yaml) ? [] : $this->readYaml($yaml);
    }

    public function readYaml($lines)
    {
        $data = Yaml::parse(implode("\n", $lines));
        if (is_int($data['date'])) {
            $data['date'] = date('c', $data['date']);
        }

        return $data;
    }

    public function readQuotedLines($lines, $headMarker, $tailMarker)
    {
        $line = array_shift($lines);
        if (!preg_match($headMarker, $line, $matches)) {
            return null;
        }
        $res[] = ltrim(substr($line, strlen($matches[0])));
        while ($line) {
            $line = array_shift($lines);
            if (preg_match($tailMarker, $line, $matches)) {
                $res[] = rtrim(substr($line, 0, -strlen($matches[0])));
                break;
            }
            $res[] = $line;
        }

        return $res;
    }

    /**
     * Renders the page with given params.
     *
     * @param array $params
     * @abstract
     */
    abstract public function render(array $params = []);
}
