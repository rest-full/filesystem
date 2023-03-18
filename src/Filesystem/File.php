<?php

namespace Restfull\Filesystem;

use Restfull\Container\Instances;
use Restfull\Error\Exceptions;

/**
 *
 */
class File
{

    /**
     * @var string
     */
    public $name = '';

    /**
     * @var Folder
     */
    protected $folder;

    /**
     * @var string
     */
    protected $file = '';

    /**
     * @var string
     */
    protected $extension = '';

    /**
     * @var resource
     */
    private $handle;

    /**
     * @var array
     */
    private $datas = [];

    /**
     * @var array
     */
    private $tmp = [];

    /**
     * @param string $file
     * @param array $arq
     */
    public function __construct(string $file, array $arq = [])
    {
        $instance = new Instances();
        $path = pathinfo($file);
        $this->folder = $instance->resolveClass(
            $instance->assemblyClassOrPath(
                '%s' . DS_REVERSE . 'Filesystem' . DS_REVERSE . 'Folder',
                [ROOT_NAMESPACE]
            ),
            ['folder' => $path['dirname']]
        );
        $this->file = $path['basename'];
        if (isset($path['extension'])) {
            $this->extension = $path['extension'];
        }
        if (count($arq) > 0) {
            if (!isset($this->tmp['tmp_name'])) {
                $this->tmp = $arq;
            }
        }
        return $this;
    }

    /**
     * @param string $path
     * @param bool $deleteTmp
     *
     * @return $this
     */
    public function pathFile(string $path, bool $deleteTmp = true): File
    {
        if ($path != $this->pathinfo()) {
            $path = pathinfo($path);
            $this->folder->pathFolder($path['dirname']);
            $this->file = $path['basename'];
            $this->extension = $path['extension'];
            if ($deleteTmp) {
                if (isset($this->tmp) && count($this->tmp) > 0) {
                    unset($this->tmp);
                }
            }
        }
        return $this;
    }

    /**
     * @return string
     */
    public function pathinfo(): string
    {
        return $this->folder->path() . DS . $this->file;
    }

    /**
     * @param bool $deleteFolder
     *
     * @return $this
     */
    public function delete(bool $deleteFolder = false): bool
    {
        if ($this->exists() && is_file($this->folder->path() . DS . $this->file)
            && !$this->handle
        ) {
            $path = stripos($_SERVER['WINDIR'], 'WINDOWS') !== false
                ? str_replace(
                    DS,
                    DS_REVERSE,
                    $this->folder->path()
                ) . DS_REVERSE : $this->folder->path() . DS;
            $file = $path . $this->file;
            unlink($file);
        }
        if ($deleteFolder) {
            if (count($this->folder->read()['file']) == 0) {
                $this->folder->delete();
            }
        }
        return $this->exists();
    }

    /**
     * @return bool
     */
    public function exists(string $path = ''): bool
    {
        if (!empty($path)) {
            return file_exists($path);
        }
        return file_exists($this->folder->path() . DS . $this->file);
    }

    /**
     * @param bool $count
     * @param string $mode
     *
     * @return array[]
     * @throws Exceptions
     */
    public function read(bool $count = false, string $mode = 'r+'): array
    {
        if (!isset($this->handle)) {
            $this->create($mode);
        }
        $reading = [];
        if ($this->exists()) {
            while ($read = fgets($this->handle)) {
                $reading[] = $read;
            }
            $this->close();
        }
        $read = ['content' => $reading];
        if ($count) {
            $read = array_merge($read, ['count' => count($reading) - 1]);
        }
        return $read;
    }

    /**
     * @param string $mode
     *
     * @return $this
     * @throws Exceptions
     */
    public function create(string $mode): File
    {
        if (!$this->folder->exists()) {
            throw new Exceptions("this Folder not exist.");
        }
        if (!isset($this->handle) || $this->handle === false) {
            if (substr($mode, 0, 1) == 'r') {
                if ($this->exists()) {
                    $this->handle = fopen(
                        $this->folder->path() . DS . $this->file,
                        $mode
                    );
                }
            } else {
                $this->handle = fopen(
                    $this->folder->path() . DS . $this->file,
                    $mode
                );
            }
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function close(): File
    {
        fclose($this->handle);
        $this->handle = null;
        return $this;
    }

    /**
     * @param string|array $data
     * @param bool $close
     * @param string $mode
     *
     * @return bool
     * @throws Exceptions
     */
    public function write(
        $data,
        string $mode = 'w+',
        bool $close = true
    ): bool {
        $this->create($mode);
        $this->datas = count($this->datas) > 0 ? array_merge($this->datas, [$data]) : [$data];
        if ($close) {
            $success = fwrite($this->handle, $this->datas) !== false ? true : false;
            $this->close();
            $this->datas = [];
        }
        return $success;
    }

    /**
     * @param bool $nameAlone
     *
     * @return string
     */
    public function namePath(bool $nameAlone = false): string
    {
        if ($nameAlone) {
            return substr($this->file, strripos($this->file, DS));
        }
        return $this->file;
    }

    /**
     * @return string
     */
    public function tmp(string $key = 'tmp_name'): string
    {
        return $this->tmp[$key];
    }

    /**
     * @return Folder
     */
    public function folder(): Folder
    {
        return $this->folder;
    }

    /**
     * @return resource
     */
    public function handle()
    {
        return $this->handle;
    }

}
