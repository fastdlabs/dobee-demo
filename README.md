# fastd/dobee-demo

[![CI](https://github.com/fastdlabs/dobee-demo/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/fastdlabs/dobee-demo/actions/workflows/ci.yml)

FastD `dobee` 的官方 Demo 包，包含：

- Session 示例（访问计数 / CSRF / 注销）
- SSE / AI Mock Stream 示例
- RequestId Middleware 示例
- 构造器注入 / Resolver 示例
- Cache（file adapter / set / get / delete）/ Database（SQLite / MedooDB）示例
- 数据库事务示例
- Swoole Server / 自定义 Listener 示例
- Swoole 协程并发示例（`/parallel-demo`）
- HTTP 请求 / Cookie / 上传（大小与类型校验）/ 下载示例
- 自定义事件 / 响应状态 / 异常处理示例
- CORS / OPTIONS 示例
- Console Command / Process / Signal 示例
- 对应静态演示页面 `/demo/*.html`

Dobee 骨架通过 `require-dev` 或手动安装引入本包；未安装时不会注册任何 demo 路由或中间件。

## 文档

- [FastD 8.3 使用手册](https://fastdlabs.github.io/docs/#/zh-cn/8.3/readme)
- [Demo 使用说明](https://fastdlabs.github.io/docs/#/zh-cn/8.3/guide/demo)
- [Demo Scenarios](https://github.com/fastdlabs/dobee-demo/blob/master/docs/scenarios.md)

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

## 使用步骤

1. 在 dobee 项目中安装：

```bash
composer require --dev fastd/dobee-demo
```

2. 通过 CGI 启动 Demo 页面：

```bash
php -S 127.0.0.1:8099 -t web
```

3. 打开首页：

```text
http://127.0.0.1:8099/demo/index.html
```

4. 如需测试 Swoole 模式，再启动：

```bash
php bin/server start
```

首页左侧点击 Demo 会在中间 iframe 展示，右侧切换目标 API 运行时；页面右下角显示页面域名、实际 API 域名和最近请求。

## 首页预览与运行时切换

建议通过 CGI 打开 Demo 首页：

```text
http://127.0.0.1:8099/demo/index.html
```

首页布局：

- 左侧：Demo 列表
- 中间：点击 Demo 后在 iframe 内展示，不跳转页面
- 右侧：当前运行环境、API 目标、CGI / Swoole 切换和启动说明

### HTML 页面

HTML Demo 页面始终从当前 URL 的 CGI 域名加载，切换运行环境不会切换页面域名。

### API 请求

Demo 页面内的 API 请求按右侧选择切换：

| 选择 | API 域名 |
|---|---|
| CGI | 当前页面 URL 域名 |
| Swoole | `config/swoole.php` 中 `servers.http.host` / `port` |

页面通过 `/demo/runtime.js` 统一处理 fetch、EventSource 和 API 链接的目标地址；跨域请求由 Demo CORS 中间件处理。

每个 Demo 页面右下角会显示运行时信息条：

- 当前 HTML 页面域名
- 实际 API 域名
- 最近一次请求 URL 和状态码

请求示例页 `/demo/request.html` 还会展示完整的实际请求 URL、method、headers、body 和响应。

### 启动 Swoole

```bash
php bin/server start
```

`config/swoole.php` 默认：

```php
'http' => [
    'host' => '127.0.0.1',
    'port' => 9527,
],
```

停止：

```bash
php bin/server stop
```

### 启动 CGI

```bash
php -S 127.0.0.1:8099 -t web
```

### 行为差异

| 项目 | CGI | Swoole |
|---|---|---|
| 执行模型 | 请求级进程 | 常驻 Worker |
| 协程上下文 | 无 | 有 |
| `/parallel-demo` | 串行 | 并发 |
| API 域名来源 | 当前 URL | `config/swoole.php` |

## 路由

| 路由 | 说明 |
|---|---|
| `/session` | Session 访问计数 |
| `/session-security-demo` | CSRF 校验 / 注销 |
| `/di-demo` | 构造器注入 Controller + Middleware |
| `/middleware-demo` | RequestIdMiddleware |
| `/sse` | SSE 流式推送 |
| `/ai/mock-stream` | 模拟 AI SSE 输出 |
| `/cache-demo` | 缓存读写与命中计数 |
| `/cache-manage-demo` | 缓存 set / get / delete / miss |
| `/db-demo` | SQLite 写入与访问计数 |
| `/swoole-demo` | Swoole/CGI 运行时信息 |
| `/parallel-demo` | Swoole 协程并发 / CGI 串行 |
| `/request-demo` | 请求 query/header/cookie/body |
| `/cookie-demo` | Cookie 读写 |
| `/upload-demo` | 文件上传（1MB 限制、类型白名单） |
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
