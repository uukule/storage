<?php


namespace uukule;

/**
 * Interface StorageInterface
 * @package uukule
 *
 * @todo temporaryUrl($path, $time) 临时 URLs
 * @todo download() 下载文件
 */
interface StorageInterface
{

    /**
     * 是否存在指定的文件
     *
     * @param string $path
     * @return bool
     */
    function exists(string $path): bool;

    /**
     * 返回该文件的原始字符串内容
     *
     * @param string $path
     * @param bool $lock
     * @return string
     */
    function get(string $path, bool $lock = false): string;

    /**
     * 获取文件元信息
     *
     * @param string $path 存储空间名称
     * @return array
     */
    function meta(string $path): array;


    /**
     * 文件上传/保存文件
     *
     * @param string $path
     * @param string|resource $contents
     * @param mixed $options
     * @return bool
     */
    function put(string $path, $contents, $options = []): bool;

    /**
     * @param string $path
     * @param \think\File|\Illuminate\Http\File|\Illuminate\Http\UploadedFile|string $file
     * @param mixed $options
     * @return string|false
     */
    function putFile(string $path, $file, $options = []);

    /**
     * 开头追加上传字符串
     *
     * @param string $path 存储空间名称
     * @param string $data 文件内容
     * @return bool
     */
    function prepend(string $path, string $data): bool;

    /**
     * 结尾追加上传字符串
     *
     * @param string $path 存储空间名称
     * @param string $data 文件内容
     * @return bool
     */
    function append(string $path, string $data): bool;

    /**
     * 删除文件
     *
     * @param string|array $paths
     * @return bool
     */
    function delete($paths): bool;

    /**
     * 复制文件
     *
     * @param string $from
     * @param string $to
     * @return bool
     */
    function copy(string $from, string $to): bool;

    /**
     * 移动文件
     *
     * @param string $from
     * @param string $to
     * @return bool
     */
    function move(string $from, string $to): bool;

    /**
     * 获取文件的大小
     *
     * @param string $path
     * @return int
     */
    function size(string $path): int;

    /**
     * 文件最后一次被修改的 UNIX 时间戳
     *
     * @param string $path
     * @return int
     */
    function lastModified(string $path): int;

    /**
     * 获取目录下的所有文件
     *
     * @param string|null $directory
     * @param bool $recursive
     * @return array
     */
    function files(string $directory = null, bool $recursive = false): array;

    /**
     * 获取目录（包括子目录）下的所有文件
     *
     * @param string|null $directory
     * @param bool $recursive
     * @return array
     */
    function allFiles(string $directory = null, bool $recursive = false): array;

    /**
     * 获取目录下的所有目录
     *
     * @param string|null $directory
     * @param bool $recursive
     * @return array
     */
    function directories(string $directory = null, bool $recursive = false): array;

    /**
     * 获取目录（包括子目录）下的所有目录
     *
     * @param string|null $directory
     * @param bool $recursive
     * @return array
     */
    function allDirectories(string $directory = null, bool $recursive = false): array;

    /**
     * 创建目录
     *
     * @param string $directory
     * @return bool
     */
    function makeDirectory(string $directory): bool;

    /**
     * 删除目录
     *
     * @param string $directory
     * @return bool
     */
    function deleteDirectory(string $directory): bool;


    /**
     * 查询文件可见性
     *
     * @param string $path
     * @return string
     */
    function getVisibility(string $path): string;

    /**
     * 设置文件可见性
     *
     * @param string $path
     * @param string $visibility
     * @return bool
     */
    function setVisibility(string $path, string $visibility): bool;

    /**
     * 获取文件URL
     *
     * @param string $path
     * @return string
     */
    function url(string $path): string;
}