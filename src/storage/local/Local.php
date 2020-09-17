<?php


namespace uukule\storage\file;

use uukule\StorageException as Exception;
use uukule\StorageInterface;

class Local implements StorageInterface
{

    protected $config = [
        'root' => '/',
        'domain' => ''
    ];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * 是否存在指定的文件
     *
     * @param string $path
     * @return bool
     */
    function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * 返回该文件的原始字符串内容
     *
     * @param string $path
     * @param bool $lock
     * @return string
     * @throws Exception
     */
    public function get(string $path, bool $lock = false): string
    {
        if ($this->isFile($path)) {
            return $lock ? $this->sharedGet($path) : file_get_contents($path);
        }

        throw new Exception("File does not exist at path {$path}");
    }

    /**
     * Get contents of a file with shared access.
     *
     * @param string $path
     * @return string
     */
    public function sharedGet($path)
    {
        $contents = '';

        $handle = fopen($path, 'rb');

        if ($handle) {
            try {
                if (flock($handle, LOCK_SH)) {
                    clearstatcache(true, $path);

                    $contents = fread($handle, $this->size($path) ?: 1);

                    flock($handle, LOCK_UN);
                }
            } finally {
                fclose($handle);
            }
        }

        return $contents;
    }

    /**
     * 获取文件元信息
     *
     * @param string $path 存储空间名称
     * @return array
     */
    function meta(string $path): array
    {
        // TODO: Implement meta() method.
    }

    /**
     * 文件上传/保存文件
     *
     * @param string $path
     * @param string|resource $contents
     * @param mixed $options
     * @return bool
     */
    function put(string $path, $contents, $options = []): bool
    {
        $save_path = $this->config['root'] . pathinfo($path)['dirname'];
        $this->makeDirectory($save_path);
        file_put_contents($path, $contents);
        return true;
    }

    /**
     * @param string $path
     * @param \think\File|\Illuminate\Http\File|\Illuminate\Http\UploadedFile|string $file
     * @param mixed $options
     * @return string|false
     */
    function putFile(string $path, $file, $options = [])
    {
        $path = $this->config['root'] . $path;
        if (is_string($file)) {

        } //判断是否THINKPHP框架
        elseif (class_exists('\\think\\File')) {
            if ($file instanceof \think\File) {
                $save_path = pathinfo($path)['dirname'];
                $this->makeDirectory($save_path);
                copy($file->getPathname(), $path);
                unlink($file->getPathname());
                if (!$this->exists($path)) {
                    throw new Exception('文件上传失败');
                }
                return $this->url($path);
            } else {
                throw new \Exception('非法文件');
            }
        } else {

        }
    }

    /**
     * 开头追加上传字符串
     *
     * @param string $path 存储空间名称
     * @param string $data 文件内容
     * @return bool
     */
    function prepend(string $path, string $data): bool
    {
        if ($this->exists($path)) {
            return $this->put($path, $data.$this->get($path));
        }

        return $this->put($path, $data);
    }

    /**
     * 结尾追加上传字符串
     *
     * @param string $path 存储空间名称
     * @param string $data 文件内容
     * @return bool
     */
    function append(string $path, string $data): bool
    {
        return file_put_contents($path, $data, FILE_APPEND);
    }

    /**
     * 删除文件
     *
     * @param string|array $paths
     * @return bool
     */
    function delete($paths): bool
    {
        $paths = is_array($paths) ? $paths : func_get_args();

        $success = true;

        foreach ($paths as $path) {
            try {
                if (! @unlink($path)) {
                    $success = false;
                }
            } catch (Exception $e) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * 复制文件
     *
     * @param string $from
     * @param string $to
     * @return bool
     */
    function copy(string $from, string $to): bool
    {
        return copy($from, $to);
    }

    /**
     * 移动文件
     *
     * @param string $from
     * @param string $to
     * @return bool
     */
    function move(string $from, string $to): bool
    {
        return rename($from, $to);
    }

    /**
     * 获取文件的大小
     *
     * @param string $path
     * @return int
     */
    function size(string $path): int
    {
        return filesize($path);
    }

    /**
     * 文件最后一次被修改的 UNIX 时间戳
     *
     * @param string $path
     * @return int
     */
    function lastModified(string $path): int
    {
        return filemtime($path);
    }

    /**
     * 获取目录下的所有文件
     *
     * @param string|null $directory
     * @param bool $recursive
     * @return array
     */
    function files(string $directory = null, bool $recursive = false): array
    {
        if(! is_dir($directory)){
            throw new Exception("[{$directory}]不是一个标准文件夹！");
        }
        return scandir($directory);
    }

    /**
     * 获取目录（包括子目录）下的所有文件
     *
     * @param string|null $directory
     * @param bool $recursive
     * @return array
     */
    function allFiles(string $directory = null, bool $recursive = false): array
    {
        if(! is_dir($directory)){
            throw new Exception("[{$directory}]不是一个标准文件夹！");
        }
        $scandir = scandir($directory);
        foreach ($scandir as $i => $file){
            if('.' === $file || '..' == $file){
                unset($scandir[$i]);
                continue;
            }
            if(is_dir($directory . '/' . $file)){
                $scandir = array_merge($scandir, $this->allFiles($directory . '/' . $file, $recursive));
            }
        }
        return $scandir;
    }

    /**
     * 获取目录下的所有目录
     *
     * @param string|null $directory
     * @param bool $recursive
     * @return array
     */
    function directories(string $directory = null, bool $recursive = false): array
    {
        // TODO: Implement directories() method.
    }

    /**
     * 获取目录（包括子目录）下的所有目录
     *
     * @param string|null $directory
     * @param bool $recursive
     * @return array
     */
    function allDirectories(string $directory = null, bool $recursive = false): array
    {
        // TODO: Implement allDirectories() method.
    }

    /**
     * 创建目录
     *
     * @param string $directory
     * @return bool
     */
    function makeDirectory(string $directory): bool
    {
        $new_path = '';
        foreach (explode('/', $directory) as $_pathname) {
            $new_path .= "{$_pathname}/";
            if (is_dir($new_path))
                continue;
            mkdir($new_path);
        }
        //检查目录
        if (is_dir($directory) === false) {
            if (DIRECTORY_SEPARATOR === '\\') {
                $is_success = mkdir(iconv("UTF-8", "GBK", $directory), 0777, true);
            } else {
                $is_success = mkdir($directory, 0777, true);
            }
            if (!$is_success)
                throw new Exception('创建目录失败', 404);
        }

        //检查目录写权限
        if (is_writable($directory) === false)
            throw new Exception('上传目录[' . $directory . ']没有写权限', 404);
        return true;
    }

    /**
     * 删除目录
     *
     * @param string $directory
     * @return bool
     */
    function deleteDirectory(string $directory): bool
    {
        // TODO: Implement deleteDirectory() method.
    }

    /**
     * 查询文件可见性
     *
     * @param string $path
     * @return string
     */
    function getVisibility(string $path): string
    {
        // TODO: Implement getVisibility() method.
    }

    /**
     * 设置文件可见性
     *
     * @param string $path
     * @param string $visibility
     * @return bool
     */
    function setVisibility(string $path, string $visibility): bool
    {
        // TODO: Implement setVisibility() method.
    }

    /**
     * 获取文件URL
     *
     * @param string $path
     * @return string
     */
    function url(string $path): string
    {
        $url = $this->config['domain'];
        if ('/' !== $url[-1]) {
            $url .= '/';
        }
        $url .= $path;
        return $url;
    }
}