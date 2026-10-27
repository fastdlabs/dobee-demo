# Demo Scenarios

本文档覆盖 `fastd/dobee-demo` 当前注册的场景、页面和调用方式。

## 启动

Demo 首页 `/demo/index.html` 右侧提供 CGI / Swoole 目标切换和启动说明。

### Swoole

```bash
php bin/server start
```

默认 HTTP 端口为 `9527`，以下示例均以 `http://127.0.0.1:9527` 为前缀。

### CGI / FastCGI

```bash
php -S 127.0.0.1:8099 -t web
```

CGI 示例前缀为 `http://127.0.0.1:8099`。

## 路由清单

| 方法 | 路由 | 页面 | 说明 |
|---|---|---|---|
| GET | `/session` | - | Session 访问计数、flash、CSRF token |
| GET | `/di-demo` | `di.html` | 构造器注入 Controller + Middleware |
| GET | `/middleware-demo` | `middleware.html` | RequestId / 响应耗时 |
| GET | `/sse` | `sse.html` | SSE 流式推送 |
| POST | `/ai/mock-stream` | `ai.html` | 模拟 AI SSE 输出 |
| GET | `/cache-demo` | `cache.html` | file cache 读写与命中 |
| GET | `/db-demo` | `database.html` | SQLite 写入与访问计数 |
| GET | `/db-transaction-demo` | `db-transaction.html` | 事务回滚 |
| GET | `/swoole-demo` | `swoole.html` | CGI / Swoole 运行时信息 |
| GET | `/parallel-demo` | `parallel.html` | 运行时自适应的协程并发 |
| GET/POST | `/request-demo` | `request.html` | query / header / cookie / body |
| GET | `/cookie-demo` | `cookie.html` | Cookie 写入与读取 |
| POST | `/upload-demo` | `upload.html` | multipart 文件上传 |
| GET | `/download-demo` | `download.html` | 流式 CSV 下载 |
| GET | `/event-demo` | `event.html` | 自定义 PSR-14 事件 |
| GET | `/response-demo` | `response.html` | 200 / 201 / 302 |
| GET | `/error-demo` | `error.html` | 404 / 500 异常处理 |
| GET | `/config-demo` | `config.html` | 配置与运行时信息 |
| GET/OPTIONS | `/cors-demo` | `cors.html` | CORS / OPTIONS 预检 |
| GET | `/demo/{page}` | `index.html` 等 | 静态演示页面 |

## HTTP 基础

### 请求信息

```bash
curl -X POST 'http://127.0.0.1:9527/request-demo?hello=world' \
  -H 'Content-Type: application/json' \
  -H 'X-Demo: request-demo' \
  -d '{"name":"fastd"}'
```

### Cookie

```bash
curl -i http://127.0.0.1:9527/cookie-demo
curl -i -H 'Cookie: demo_visits=1' http://127.0.0.1:9527/cookie-demo
```

### 文件上传

```bash
curl -F 'file=@README.md' http://127.0.0.1:9527/upload-demo
```

文件会保存到宿主的 `runtime/uploads`。

### 文件下载

```bash
curl -OJ http://127.0.0.1:9527/download-demo
```

### 响应状态 / 重定向

```bash
curl -i http://127.0.0.1:9527/response-demo
curl -i 'http://127.0.0.1:9527/response-demo?type=created'
curl -i 'http://127.0.0.1:9527/response-demo?type=redirect'
```

### 异常处理

```bash
curl -i 'http://127.0.0.1:9527/error-demo?type=not_found'
curl -i http://127.0.0.1:9527/error-demo
```

### CORS / OPTIONS

```bash
curl -i -X OPTIONS http://127.0.0.1:9527/cors-demo \
  -H 'Origin: https://demo.example' \
  -H 'Access-Control-Request-Method: GET'
```

## Swoole / 协程

```bash
curl http://127.0.0.1:9527/parallel-demo
```

- CGI：串行执行
- Swoole：协程并发

返回字段：

```json
{
  "concurrent": true,
  "elapsed_ms": 121.5,
  "data": {
    "user": {},
    "orders": {},
    "notifications": {}
  }
}
```

`concurrent` 由运行时自动判断，业务代码不需要写 `if (Swoole)`。

## 事件 / 配置

### 自定义事件

```bash
curl http://127.0.0.1:9527/event-demo
```

返回：

- 事件类
- 消息
- 处理该事件的 Listener 列表

### 配置 / 运行时

```bash
curl http://127.0.0.1:9527/config-demo
```

## 数据 / 缓存

```bash
curl http://127.0.0.1:9527/cache-demo
curl http://127.0.0.1:9527/db-demo
curl http://127.0.0.1:9527/db-transaction-demo
```

`/db-transaction-demo` 会在事务中主动抛异常，验证 `before === after`。

## Console / Process

```bash
php bin/console demo:hello
php bin/process
```

Demo 注册了两个 Worker：

```text
demo
demo-2
```

## 静态页面

入口：

```text
/demo/index.html
```

包括：

```text
request.html
cookie.html
upload.html
download.html
event.html
response.html
error.html
config.html
cors.html
db-transaction.html
parallel.html
process.html
http-client.html
redis-session.html
```

## 外部依赖说明

以下页面是使用说明，不会在 demo 中强制创建外部依赖：

- `http-client.html`：PSR-18 `FastD\Http\Request\Client` 用法
- `redis-session.html`：Redis Session 配置，要求 `ext-redis`、`fastd/http >= 8.2` 和可用 Redis 服务

## 测试

在 `fastd/dobee-demo` 仓库根目录：

```bash
php vendor/bin/phpunit
```

当前测试覆盖路由、页面、事件、上传、下载、CORS、事务和安装脚本。
