<?php


namespace uukule;

/**
 * @method static bool exists(string $path)
 * @method static string get(string $path)
 * @method static resource|null readStream(string $path)
 * @method static bool put(string $path, string|resource $contents, mixed $options = [])
 * @method static string|false putFile(string $path, \think\File|\Illuminate\Http\File|\Illuminate\Http\UploadedFile|string $file, mixed $options = [])
 * @method static bool writeStream(string $path, resource $resource, array $options = [])
 * @method static string getVisibility(string $path)
 * @method static bool setVisibility(string $path, string $visibility)
 * @method static bool prepend(string $path, string $data)
 * @method static bool append(string $path, string $data)
 * @method static bool delete(string|array $paths)
 * @method static bool copy(string $from, string $to)
 * @method static bool move(string $from, string $to)
 * @method static int size(string $path)
 * @method static int lastModified(string $path)
 * @method static array files(string|null $directory = null, bool $recursive = false)
 * @method static array allFiles(string|null $directory = null)
 * @method static array directories(string|null $directory = null, bool $recursive = false)
 * @method static array allDirectories(string|null $directory = null)
 * @method static bool makeDirectory(string $path)
 * @method static bool deleteDirectory(string $directory)
 *
 * @see \uukule\StorageInterface
 */
class Storage
{

    /**
     * @var array 文件的实例
     */
    public static $instance = [];

    /**
     * @var object 操作句柄
     */
    public static $handler;

    /**
     * 自动初始化缓存
     * @access public
     * @param array|string $config 配置数组
     * @return Driver
     */
    public static function init($config = null): \uukule\StorageInterface
    {
        if (is_null($config)) {
            $config = config('filesystem.default');
            return self::connect($config);
        } elseif (is_string($config)) {
            return self::disk($config);
        } elseif (is_array($config)) {
            return self::connect($config);
        }else{
            throw new \Exception('请指定文件驱动类型');
        }
    }

    /**
     * 连接文件驱动
     * @access public
     * @param array $config 配置数组
     * @param bool|string $name 缓存连接标识 true 强制重新连接
     * @return Driver
     */
    public static function connect(array $config = [], $name = false)
    {
        $type = !empty($config['type']) ? $config['type'] : 'Local';

        if (false === $name) {
            $name = md5(serialize($config));
        }

        if (true === $name || !isset(self::$instance[$name])) {
            $class = false === strpos($type, '\\') ?
                '\\uukule\\storage\\' . lcfirst($type) . '\\' . ucwords($type) :
                $type;

            if (true === $name) {
                return new $class($config);
            }

            self::$instance[$name] = new $class($config);
        }

        return self::$instance[$name];
    }

    /**
     * Get a filesystem instance.
     *
     * @param string|null $name
     * @return Storage
     */
    public static function disk($name = null)
    {
        if (is_null($name)) {
            $name = config('filesystem.default')['type'] ?? 'local';
        }
        $sys_config = config('filesystem.' . $name);
        return self::connect($sys_config);
    }

    private function getDefaultDriver(): string
    {
        return 'local';
    }

    public function __call($method, $args)
    {
        $instance = $this->init();
        return $instance->$method(...$args);
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param string $method
     * @param array $args
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public static function __callStatic($method, $args)
    {
        $instance = self::init();
        return $instance->$method(...$args);
    }
}