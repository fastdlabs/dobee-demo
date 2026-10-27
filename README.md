# fastd/dobee-demo

FastD `dobee` 的官方 Demo 包，包含：

- Session 示例
- SSE / AI Mock Stream 示例
- RequestId Middleware 示例
- 构造器注入 / Resolver 示例
- 对应静态演示页面 `/demo/*.html`

Dobee 骨架通过 `require-dev` 或手动安装引入本包；未安装时不会注册任何 demo 路由或中间件。

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
| `/demo/index.html` | Demo 入口页 |
