<h1 align="center">
ThinkPHP 6.0 Storage 核心组件含本地存储
</h1>
<p align="center">
	<strong>storage 是一个专为 存储 打造的工具</strong>    
</p>

<p align="center">
    <a href="https://packagist.org/packages/uukule/storage">
		<img src="https://poser.pugx.org/uukule/storage/v/stable" alt="Latest Stable Version">
  	</a>
     <a href="https://packagist.org/packages/uukule/storage">
		<img src="https://poser.pugx.org/uukule/storage/downloads" alt="Total Downloads">
  	</a>
    <a href="https://packagist.org/packages/uukule/storage">
		<img src="https://poser.pugx.org/uukule/storage/license" alt="License">
  	</a>
</p>

* [Composer包](#Composer包)
* [安装](#安装)
* [配置](#配置)
* [用法](#用法)
  * [快速开始](#快速开始)
  * [使用Api](#使用Api)
    * [检索文件](#检索文件)
    * [下载文件(待更新)](#下载文件)
    * [文件URLs](#文件URLs)
    * [文件临时URLs](#文件临时URLs)
    * [保存文件](#保存文件)
    * [自动流式传输 (待更新)](#自动流式传输)
    * [文件数据写入](#文件数据写入)
    * [复制文件](#复制文件)
    * [移动文件](#移动文件)
    * [删除文件](#删除文件)
    * [目录](#目录)
      * [获取目录下的所有目录](#获取目录下的所有目录)
      * [创建目录](#创建目录)
      * [删除目录](#删除目录)
      * [](#)
      * [](#)
    
    
    
## Composer包

在使用OSS驱动之前，你需要通过Composer安装相应的软件包

* OSS: `uukule/storage-oss ~1.0`


## 安装

> 该扩展需要 PHP 7.1+ 和 ThinkPHP 6.0+. <br>
> 添加阿里云OSS存储请加载 [uukule/storage-oss](https://packagist.org/packages/uukule/storage-oss)

使用`composer`安装：

```
composer require uukule/storage
```

## 配置

生成配置文件 `config/filesystem.php`
```php
<php

```

## 用法

### 获取磁盘实例
`Storage` 门面可用于与任何已配置的磁盘进行交互。例如，你可以使用门面中的 `put` 方法将头像存储到默认磁盘。如果你使用 `Storage` 门面中的任何方法，而一开始并没有使用 `disk` 方法，那么所调用的方法会自动传递给默认的磁盘:


```php
use use uukule\facade\Storage;
Storage::put('avatars/1', $fileContents);
```

如果应用程序要与多个磁盘进行互操作，可使用 `Storage` 门面中的 `disk` 方法对特定磁盘上的文件进行操作：

```php
Storage::disk('oss')->put('avatars/1', $fileContents);
```

### 检索文件
`get` 方法可以用于检索文件的内容，此方法返回该文件的原始字符串内容。 切记，所有文件路径的指定都应该相对于为磁盘配置的「root」目录：

```php
$contents = Storage::get('file.jpg');
```
`exists` 方法可以用来判断磁盘上是否存在指定的文件：
```php
$exists = Storage::disk('oss')->exists('file.jpg');
```
### 下载文件(待更新)
`download` 方法可用于生成一个响应，强制用户的浏览器在给定路径下载文件。 `download` 方法接受一个文件名作为该方法的第二个参数，它将确定用户下载文件时看到的文件名。最后，你可以传递一个 HTTP 数组头作为该方法的第三个参数：

```php
return Storage::download('file.jpg');

return Storage::download('file.jpg', $name, $headers);
```

### 文件URLs
你可以使用 `url` 方法来获取给定文件的 URL。如果你使用的时 `local` 驱动，一般只是在给定的路径上加上 `/storage` 并返回一个相对的 URL 到那个文件。如果使用的是 `s3` 或者是 `rackspace` 驱动，会返回完整的远程 URL：

```php
$url = Storage::url('file.jpg');
```
{note} 切记，如果使用的是 `local` 驱动，则所有想被公开访问的文件都应该放在 `storage/app/public` 目录下。此外你应该在 `public/storage` 创建一个符号链接 来指向 `storage/app/public` 目录。
### 文件临时URLs
当使用 `oss`、`s3` 或 `rackspace` 驱动来存储文件，可以使用 `temporaryUrl` 方法创建给定文件的临时 URL。这个方法会接收路径和 `DateTime` 实例来指定 URL 何时过期：
```php
$url = Storage::temporaryUrl(
    'file.jpg', now()->addMinutes(5)
);
```
### 保存文件

`put` 方法可用于将原始文件内容保存到磁盘上。你也可以传递 PHP 的 `resource` 给 `put` 方法，它将使用文件系统下的底层流支持。强烈建议在处理大文件时使用流：
```php
Storage::put('file.jpg', $contents);

Storage::put('file.jpg', $resource);
```
### 自动流式传输 (待更新)

### 文件数据写入
`prepend` 和 `append` 方法允许你在文件的开头或结尾写入数据：
```php
Storage::prepend('file.log', 'Prepended Text');

Storage::append('file.log', 'Appended Text');
```
### 复制文件
`copy` 方法可以复制文件到新地址：
```php
Storage::copy('old/file.jpg', 'new/file.jpg');
```
### 移动文件
`move` 方法可以重命名文件或移动文件到新地址：
```php
Storage::move('old/file.jpg', 'new/file.jpg');
```

### 删除文件
`delete` 方法接受单个文件名或数组形式的文件名来删除磁盘上的文件：
```php
Storage::delete('file.jpg');

Storage::delete(['file.jpg', 'file2.jpg']);
```

### 目录
#### 获取目录下的所有文件
`files` 方法返回指定目录下的所有文件的数组。`allFiles` 方法返回指定目录下包含子目录的所有文件的数组：
```php
$files = Storage::files($directory);

$files = Storage::allFiles($directory);
```

#### 获取目录下的所有目录
`directories` 方法返回指定目录下的所有目录的数组。`allDirectories` 方法返回指定目录下包含子目录的所有目录的数组：
```php
$directories = Storage::directories($directory);

// 递归
$directories = Storage::allDirectories($directory);
```

#### 创建目录
`makeDirectory` 方法会递归创建目录：
```php
Storage::makeDirectory($directory);
```
#### 删除目录
`deleteDirectory` 方法会删除指定目录及其下所有文件：
```php
Storage::deleteDirectory($directory);
```















1
