# fastd/dobee-demo

[![CI](https://github.com/fastdlabs/dobee-demo/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/fastdlabs/dobee-demo/actions/workflows/ci.yml)

FastD `dobee` 的官方 Demo 包，包含：

- Session 示例
- SSE / AI Mock Stream 示例
- RequestId Middleware 示例
- 构造器注入 / Resolver 示例
- Cache（file adapter）/ Database（SQLite / MedooDB）示例
- 数据库事务示例
- Swoole Server / 自定义 Listener 示例
- Swoole 协程并发示例（`/parallel-demo`）
- HTTP 请求 / Cookie / 上传 / 下载示例
- 自定义事件 / 响应状态 / 异常处理示例
- CORS / OPTIONS 示例
- Console Command / Process / Signal 示例
- 对应静态演示页面 `/demo/*.html`

Dobee 骨架通过 `require-dev` 或手动安装引入本包；未安装时不会注册任何 demo 路由或中间件。

## 环境要求

- PHP >= 8.4.1
- Swoole >= 6.0（`ext-swoole`）
- `ext-json`、`ext-pdo`

## 安装

```bash
composer require --dev fastd/dobee-demo
```

## 启用

在 dobee 的 `config/service.php` 中条件注册：

```php
if (class_exists(\FastD\Demo\DemoServiceProvider::class)) {
    $providers[] = \FastD\Demo\DemoServiceProvider::class;
}
```

路由由 `DemoServiceProvider` 自动注册，宿主不需要修改 `routes/web.php`。

## 路由

| 路由 | 说明 |
|---|---|
| `/session` | Session 访问计数 |
| `/di-demo` | 构造器注入 Controller + Middleware |
| `/middleware-demo` | RequestIdMiddleware |
| `/sse` | SSE 流式推送 |
| `/ai/mock-stream` | 模拟 AI SSE 输出 |
| `/cache-demo` | 缓存读写与命中计数 |
| `/db-demo` | SQLite 写入与访问计数 |
| `/swoole-demo` | Swoole/CGI 运行时信息 |
| `/parallel-demo` | Swoole 协程并发 / CGI 串行 |
| `/request-demo` | 请求 query/header/cookie/body |
| `/cookie-demo` | Cookie 读写 |
| `/upload-demo` | 文件上传 |
| `/download-demo` | 文件下载 / 流式响应 |
| `/event-demo` | 自定义事件分发 |
| `/response-demo` | 响应状态 / 重定向 |
| `/error-demo` | 404 / 500 异常处理 |
| `/config-demo` | 配置 / 运行时信息 |
| `/cors-demo` | CORS / OPTIONS 预检 |
| `/db-transaction-demo` | 数据库事务回滚 |
| `/demo/index.html` | Demo 入口页 |

命令行与进程示例：

```bash
php bin/console demo:hello
# Hello from fastd/dobee-demo command
php bin/process
```

完整的 curl、页面和运行时差异说明见：

- [Demo Scenarios](docs/scenarios.md)



## 自动注册 Service

安装本包时，dobee 的 Composer 安装脚本会调用 `scripts/install.php` 自动把：

```php
\FastD\Demo\DemoServiceProvider::class
```

写入宿主的 `config/service.php`。宿主不需要手动修改 service 配置。
